import express from 'express';
import { config } from './config.js';
import { markReadAndTyping, sendText } from './whatsapp.js';
import { generateReply } from './brain.js';
import {
  getConversation,
  pushToBuffer,
  appendHistory,
  drainBuffer,
  setProfileName,
} from './store.js';

const app = express();
app.use(express.json());

// فحص صحة الخادم
app.get('/', (_req, res) => res.send('baigr WhatsApp sales bot ✅'));

// ===== التحقق من Webhook (يستدعيه Meta مرة واحدة عند الربط) =====
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

// ===== استقبال الرسائل =====
app.post('/webhook', async (req, res) => {
  // نردّ 200 فورًا حتى لا يعيد Meta الإرسال، ثم نعالج بهدوء
  res.sendStatus(200);

  try {
    const entries = req.body?.entry || [];
    for (const entry of entries) {
      for (const change of entry.changes || []) {
        const value = change.value || {};
        const contacts = value.contacts || [];
        const messages = value.messages || [];

        for (const msg of messages) {
          await handleIncomingMessage(msg, contacts);
        }
      }
    }
  } catch (err) {
    console.error('[Webhook] خطأ أثناء المعالجة:', err?.message || err);
  }
});

async function handleIncomingMessage(msg, contacts) {
  const from = msg.from; // رقم العميل (wa_id)
  if (!from) return;

  // اسم العميل إن توفّر (لتخصيص الردود)
  const contact = contacts.find((c) => c.wa_id === from);
  if (contact?.profile?.name) setProfileName(from, contact.profile.name);

  // استخراج نص الرسالة حسب نوعها
  const text = extractText(msg);
  if (!text) return; // نتجاهل أنواعًا غير مدعومة (صوت/موقع...) بصمت في هذه النسخة

  // تعليم كمقروءة + إظهار "يكتب الآن..." مباشرة
  markReadAndTyping(msg.id).catch(() => {});

  // نضيف الرسالة للمخزن المؤقّت ونجدول الرد بعد فترة الانتظار
  const convo = pushToBuffer(from, text);

  if (convo.timer) clearTimeout(convo.timer);
  convo.timer = setTimeout(() => {
    convo.timer = null;
    respond(from).catch((e) =>
      console.error('[Respond] خطأ:', e?.message || e),
    );
  }, config.replyDelayMs);
}

async function respond(from) {
  const convo = getConversation(from);

  // نجمع كل الرسائل المتتابعة في رسالة عميل واحدة (يبدو كأنه أنهى كلامه)
  const pending = drainBuffer(from);
  if (pending.length === 0) return;

  const userTurn = pending.join('\n');
  appendHistory(from, 'user', userTurn);

  const reply = await generateReply(convo.history);
  if (!reply) return;

  appendHistory(from, 'assistant', reply);
  await sendText(from, reply);
}

function extractText(msg) {
  switch (msg.type) {
    case 'text':
      return msg.text?.body?.trim() || '';
    case 'button':
      return msg.button?.text?.trim() || '';
    case 'interactive': {
      const i = msg.interactive;
      return (
        i?.button_reply?.title?.trim() ||
        i?.list_reply?.title?.trim() ||
        ''
      );
    }
    default:
      return '';
  }
}

app.listen(config.port, () => {
  console.log(`baigr WhatsApp sales bot يعمل على المنفذ ${config.port}`);
  console.log(`النموذج: ${config.anthropic.model} | الانتظار قبل الرد: ${config.replyDelayMs}ms`);
});
