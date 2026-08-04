import { config } from './config.js';
import * as db from './db.js';
import { getHistory, appendHistory, getLead } from './store.js';
import { generateReply } from './brain.js';
import { sendText } from './whatsapp.js';
import { notifyOwner } from './notify.js';

const HOUR = 60 * 60 * 1000;

async function runOnce() {
  const now = Date.now();
  // نبقى بأمان داخل نافذة 24 ساعة (نستخدم 23 ساعة) حتى لا نحتاج قوالب رسائل معتمدة
  const windowStart = now - 23 * HOUR;
  const silentBefore = now - config.followup.hours * HOUR;

  let candidates = [];
  try {
    candidates = db.followupCandidates({
      windowStart,
      silentBefore,
      maxFollowups: config.followup.max,
    });
  } catch (err) {
    console.error('[Followup] فشل جلب المرشّحين:', err?.message || err);
    return;
  }

  for (const c of candidates) {
    const waId = c.wa_id;
    try {
      const history = getHistory(waId);
      if (!history.length) continue;

      const lead = getLead(waId);
      const contextNote =
        `ملاحظة سياقية (متابعة تلقائية): هذا العميل تفاعل سابقًا ثم صمت. ` +
        `أرسل رسالة متابعة واحدة قصيرة وطبيعية تعيد إشعال اهتمامه وتقترح الخطوة التالية بلطف دون إلحاح. ` +
        `حالته: ${lead?.summary || '—'} | المرحلة: ${lead?.stage || 'new'} | ` +
        `الخطوة المقترحة: ${lead?.next_action || '—'}. لا تكرّر رسالة سابقة حرفيًا.`;

      const { text, effects } = await generateReply(history, {
        waId,
        contextNote,
      });
      if (!text) continue;

      await sendText(waId, text);
      appendHistory(waId, 'assistant', text);
      db.incFollowup(waId);

      for (const note of effects.notifyOwner || []) await notifyOwner(note);
      console.log(`[Followup] تم إرسال متابعة إلى ${waId}`);
    } catch (err) {
      console.error(`[Followup] خطأ مع ${waId}:`, err?.message || err);
    }
  }
}

export function startFollowupLoop() {
  if (!config.followup.enabled) {
    console.log('[Followup] المتابعة التلقائية معطّلة.');
    return;
  }
  console.log(
    `[Followup] مفعّلة — متابعة بعد ${config.followup.hours} ساعة صمت، حتى ${config.followup.max} رسائل، فحص كل ${Math.round(config.followup.checkMs / 60000)} دقيقة.`,
  );
  setInterval(() => {
    runOnce().catch((e) => console.error('[Followup] خطأ:', e?.message || e));
  }, config.followup.checkMs);
}
