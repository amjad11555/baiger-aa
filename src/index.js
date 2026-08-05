import express from 'express';
import crypto from 'node:crypto';
import { config } from './config.js';
import {
  markReadAndTyping,
  sendText,
  downloadImage,
  downloadMediaBuffer,
} from './whatsapp.js';
import { transcribeAudio } from './transcribe.js';
import { generateReply } from './brain.js';
import { notifyOwner } from './notify.js';
import { startFollowupLoop } from './followup.js';
import { startReminderLoop } from './reminders.js';
import * as db from './db.js';
import {
  pushToBuffer,
  drainBuffer,
  getTimerHolder,
  appendHistory,
  getHistory,
  ensureContact,
  setProfileName,
  getContact,
  setContactStatus,
  getLead,
  isDuplicate,
  markSeen,
} from './store.js';

const app = express();
// نلتقط الجسم الخام للتحقق من توقيع Meta
app.use(
  express.json({
    verify: (req, _res, buf) => {
      req.rawBody = buf;
    },
  }),
);

const OPT_OUT = [
  'stop',
  'unsubscribe',
  'الغاء',
  'إلغاء',
  'ايقاف',
  'إيقاف',
  'توقف',
  'الغاء الاشتراك',
  'إلغاء الاشتراك',
];

// ===== فحص الصحة =====
app.get('/', (_req, res) => res.send('baigr WhatsApp sales bot ✅'));

// ===== التحقق من Webhook =====
app.get('/webhook', (req, res) => {
  const mode = req.query['hub.mode'];
  const token = req.query['hub.verify_token'];
  const challenge = req.query['hub.challenge'];
  if (mode === 'subscribe' && token === config.whatsapp.verifyToken) {
    console.log('[Webhook] تم التحقق بنجاح ✅');
    return res.status(200).send(challenge);
  }
  console.warn('[Webhook] فشل التحقق — رمز غير مطابق');
  return res.sendStatus(403);
});

// ===== التحقق من توقيع الطلب (أمان) =====
function verifySignature(req) {
  if (!config.whatsapp.appSecret) return true; // غير مفعّل — يُنصح بتفعيله
  const sig = req.get('x-hub-signature-256') || '';
  const expected =
    'sha256=' +
    crypto
      .createHmac('sha256', config.whatsapp.appSecret)
      .update(req.rawBody || Buffer.from(''))
      .digest('hex');
  try {
    return crypto.timingSafeEqual(Buffer.from(sig), Buffer.from(expected));
  } catch {
    return false;
  }
}

// ===== استقبال الرسائل =====
app.post('/webhook', async (req, res) => {
  if (!verifySignature(req)) {
    console.warn('[Webhook] توقيع غير صالح — تم الرفض');
    return res.sendStatus(403);
  }
  res.sendStatus(200); // نردّ فورًا ثم نعالج
  console.log('[Webhook] وصل حدث من Meta');

  try {
    for (const entry of req.body?.entry || []) {
      for (const change of entry.changes || []) {
        const value = change.value || {};
        const contacts = value.contacts || [];
        for (const msg of value.messages || []) {
          await handleIncomingMessage(msg, contacts);
        }
      }
    }
  } catch (err) {
    console.error('[Webhook] خطأ أثناء المعالجة:', err?.message || err);
  }
});

async function handleIncomingMessage(msg, contacts) {
  const from = msg.from;
  if (!from || !msg.id) return;

  // منع تكرار المعالجة (Meta قد يعيد إرسال نفس الحدث)
  if (isDuplicate(msg.id)) return;
  markSeen(msg.id);

  // إنشاء/تحديث جهة الاتصال
  const contact = contacts.find((c) => c.wa_id === from);
  ensureContact(from, contact?.profile?.name || null);
  if (contact?.profile?.name) setProfileName(from, contact.profile.name);

  const item = extractItem(msg);
  if (!item) return; // نوع غير مدعوم (صوت/موقع/ملف) — نتجاهله بصمت حاليًا
  console.log(
    '[وارد] رسالة من',
    from,
    '-',
    item.type === 'text' ? item.text : `[${item.type}]`,
  );

  markReadAndTyping(msg.id).catch(() => {});

  // إلغاء الاشتراك
  if (item.type === 'text' && isOptOut(item.text)) {
    setContactStatus(from, 'opted_out');
    appendHistory(from, 'user', item.text);
    await sendText(from, 'تم إيقاف الرسائل. يسعدنا خدمتك في أي وقت — راسلنا فقط. 🌹');
    return;
  }

  // إن كان العميل محوّلًا لمختص بشري أو موقفًا: نحفظ الرسالة فقط دون رد آلي
  const status = getContact(from)?.status || 'active';
  if (status !== 'active') {
    appendHistory(from, 'user', itemToText(item));
    return;
  }

  // نضيف للمخزن المؤقّت ونجدول الرد بعد فترة الانتظار
  pushToBuffer(from, item);
  const holder = getTimerHolder(from);
  if (holder.timer) clearTimeout(holder.timer);
  holder.timer = setTimeout(() => {
    holder.timer = null;
    respond(from).catch((e) => console.error('[Respond] خطأ:', e?.message || e));
  }, config.replyDelayMs);
}

async function respond(from) {
  if ((getContact(from)?.status || 'active') !== 'active') return;

  const items = drainBuffer(from);
  if (items.length === 0) return;

  // نبني نص التخزين + كتل المحتوى (مع الصور والصوت) للجولة الحالية
  const texts = [];
  const imageBlocks = [];
  let audioFallback = false;
  for (const it of items) {
    if (it.type === 'text') texts.push(it.text);
    else if (it.type === 'image') {
      if (it.caption) texts.push(it.caption);
      const media = await downloadImage(it.mediaId);
      if (media) {
        imageBlocks.push({
          type: 'image',
          source: {
            type: 'base64',
            media_type: media.mime,
            data: media.base64,
          },
        });
      }
    } else if (it.type === 'audio') {
      const media = await downloadMediaBuffer(it.mediaId);
      const transcript = media
        ? await transcribeAudio(media.buffer, media.mime)
        : null;
      if (transcript) texts.push(transcript);
      else audioFallback = true;
    }
  }

  // نضمّن الوقت الحالي (لحساب مواعيد المكالمات) + ملف العميل
  let contextNote = [nowNote(), buildContextNote(from)].filter(Boolean).join(' ');
  if (audioFallback && texts.length === 0) {
    contextNote =
      (contextNote ? contextNote + ' ' : '') +
      'ملاحظة: أرسل العميل رسالة صوتية تعذّر تفريغها. اعتذر بلطف واطلب منه كتابة طلبه نصًّا باختصار.';
  }

  const userText = texts.join('\n').trim();
  const hasAudio = items.some((i) => i.type === 'audio');
  const prefix =
    (imageBlocks.length ? '[صورة] ' : '') + (hasAudio ? '[صوت] ' : '');
  const storageText = (prefix + userText).trim() || '(وسائط)';
  appendHistory(from, 'user', storageText);

  // نبني رسائل Claude من السجل، مع استبدال الجولة الأخيرة بالصور إن وُجدت
  const history = getHistory(from);
  let messages = history;
  if (imageBlocks.length) {
    const userBlocks = [
      ...imageBlocks,
      { type: 'text', text: userText || 'انظر الصورة المرفقة من العميل.' },
    ];
    messages = [...history.slice(0, -1), { role: 'user', content: userBlocks }];
  }

  const { text, effects } = await generateReply(messages, {
    waId: from,
    contextNote,
  });

  if (text) {
    appendHistory(from, 'assistant', text);
    await sendText(from, text);
    console.log('[صادر] تم إرسال رد إلى', from);
  } else {
    console.log('[respond] لم يُنتج النموذج ردًّا نصّيًا لـ', from);
  }
  for (const note of effects.notifyOwner || []) await notifyOwner(note);
}

function nowNote() {
  const f = new Intl.DateTimeFormat('en-CA', {
    timeZone: config.timezone,
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  });
  return `التاريخ والوقت الحالي: ${f.format(new Date())} (${config.timezone}). عند حجز مكالمة احسب scheduled_at_iso بصيغة ISO 8601 مع فرق التوقيت اعتمادًا على هذا.`;
}

function buildContextNote(from) {
  const lead = getLead(from);
  const contact = getContact(from);
  if (!lead && !contact?.name) return null;
  return (
    `ملاحظة سياقية — ملف العميل: الاسم: ${lead?.name || contact?.name || '—'} | ` +
    `المرحلة: ${lead?.stage || 'new'} | الاهتمام: ${lead?.interest || '—'} | ` +
    `الميزانية: ${lead?.budget_signal || '—'} | الخطوة التالية: ${lead?.next_action || '—'}. ` +
    `حدّث الملف عبر update_lead عند أي معلومة جديدة.`
  );
}

function isOptOut(text) {
  const t = (text || '').trim().toLowerCase();
  return OPT_OUT.some((k) => t === k || t === k.toLowerCase());
}

function extractItem(msg) {
  switch (msg.type) {
    case 'text':
      return msg.text?.body?.trim()
        ? { type: 'text', text: msg.text.body.trim() }
        : null;
    case 'image':
      return {
        type: 'image',
        mediaId: msg.image?.id,
        caption: msg.image?.caption?.trim() || '',
      };
    case 'audio':
      return msg.audio?.id ? { type: 'audio', mediaId: msg.audio.id } : null;
    case 'button':
      return msg.button?.text?.trim()
        ? { type: 'text', text: msg.button.text.trim() }
        : null;
    case 'interactive': {
      const i = msg.interactive;
      const t = i?.button_reply?.title || i?.list_reply?.title || '';
      return t.trim() ? { type: 'text', text: t.trim() } : null;
    }
    default:
      return null;
  }
}

const itemToText = (it) => {
  if (it.type === 'text') return it.text;
  if (it.type === 'audio') return '[صوت]';
  return '[صورة] ' + (it.caption || '');
};

// ===== لوحة الإدارة والإحصائيات =====
function checkAdmin(req, res) {
  if (!config.adminKey) {
    res.status(503).json({ error: 'ADMIN_KEY غير مضبوط' });
    return false;
  }
  if (req.query.key !== config.adminKey) {
    res.status(403).json({ error: 'مفتاح غير صحيح' });
    return false;
  }
  return true;
}

app.get('/stats', (req, res) => {
  if (!checkAdmin(req, res)) return;
  res.json(db.stats());
});

// استئناف الرد الآلي لعميل بعد التحويل البشري: /release?wa=...&key=...
app.get('/release', (req, res) => {
  if (!checkAdmin(req, res)) return;
  const wa = req.query.wa;
  if (!wa) return res.status(400).json({ error: 'wa مطلوب' });
  setContactStatus(wa, 'active');
  res.json({ ok: true, wa, status: 'active' });
});

app.listen(config.port, () => {
  console.log(`baigr WhatsApp sales bot يعمل على المنفذ ${config.port}`);
  console.log(
    `النموذج: ${config.anthropic.model} | الانتظار: ${config.replyDelayMs}ms | قاعدة البيانات: ${config.dbPath}`,
  );
  if (!config.whatsapp.appSecret)
    console.warn('[أمان] WHATSAPP_APP_SECRET غير مضبوط — يُنصح بتفعيل التحقق من التوقيع.');
  startFollowupLoop();
  startReminderLoop();
});
