import { config } from './config.js';
import * as db from './db.js';
import { sendText, sendTemplate } from './whatsapp.js';
import { appendHistory } from './store.js';

const MIN = 60 * 1000;
const HOUR = 60 * MIN;
const DAY = 24 * HOUR;

// ===== 1) تذكير العميل قبل موعد المكالمة =====
async function runCallReminders() {
  const now = Date.now();
  let due = [];
  try {
    due = db.dueCallReminders(now, config.reminder.beforeMinutes * MIN);
  } catch (err) {
    console.error('[Reminder] فشل الجلب:', err?.message || err);
    return;
  }

  for (const r of due) {
    try {
      const timeText = r.call_time_text || 'موعدنا القادم';
      const within24h = r.last_inbound_at && r.last_inbound_at > now - 24 * HOUR;

      if (within24h) {
        // ضمن نافذة 24 ساعة → رسالة نصية عادية
        const msg = `تذكير ودّي 🌟 موعد مكالمتنا (${timeText}). جاهزين نسمع منك ونجهّز لك أفضل عرض. لو تحتاج تغيّر الوقت خبّرني 👍`;
        await sendText(r.wa_id, msg);
        appendHistory(r.wa_id, 'assistant', msg);
      } else if (config.reminder.template) {
        // خارج 24 ساعة → قالب معتمد
        await sendTemplate(r.wa_id, config.reminder.template, config.templateLang, [
          timeText,
        ]);
      } else {
        // لا سبيل للإرسال خارج النافذة بدون قالب — نعلّمه لتجنّب التكرار
        db.markCallReminded(r.wa_id);
        console.warn(
          `[Reminder] تخطّي ${r.wa_id}: خارج نافذة 24 ساعة وبدون REMINDER_TEMPLATE`,
        );
        continue;
      }

      db.markCallReminded(r.wa_id);
      console.log(`[Reminder] تم إرسال تذكير المكالمة إلى ${r.wa_id}`);
    } catch (err) {
      console.error(`[Reminder] خطأ مع ${r.wa_id}:`, err?.message || err);
    }
  }
}

// ===== 2) متابعة من لم يُغلق الصفقة بعد يومين+ (قالب معتمد) =====
async function runReengage() {
  const now = Date.now();
  const idleBefore = now - config.reengage.afterDays * DAY;
  const reengageBefore = now - config.reengage.everyDays * DAY;

  let cands = [];
  try {
    cands = db.reengageCandidates({
      idleBefore,
      maxCount: config.reengage.max,
      reengageBefore,
      now,
    });
  } catch (err) {
    console.error('[Reengage] فشل الجلب:', err?.message || err);
    return;
  }

  if (cands.length && !config.reengage.template) {
    console.warn(
      `[Reengage] ${cands.length} عميل بحاجة متابعة لكن REENGAGE_TEMPLATE غير مضبوط (مطلوب لأنها خارج 24 ساعة).`,
    );
    return;
  }

  for (const c of cands) {
    try {
      await sendTemplate(c.wa_id, config.reengage.template, config.templateLang, [
        c.name || 'عميلنا العزيز',
      ]);
      db.incReengage(c.wa_id, now);
      console.log(`[Reengage] تم إرسال متابعة (قالب) إلى ${c.wa_id}`);
    } catch (err) {
      console.error(`[Reengage] خطأ مع ${c.wa_id}:`, err?.message || err);
    }
  }
}

export function startReminderLoop() {
  if (!config.reminder.enabled) {
    console.log('[Reminder] التذكيرات معطّلة.');
    return;
  }
  console.log(
    `[Reminder] مفعّل — فحص كل ${Math.round(config.reminder.checkMs / 60000)} دقيقة | ` +
      `تذكير قبل ${config.reminder.beforeMinutes} دقيقة | ` +
      `متابعة بعد ${config.reengage.afterDays} يوم` +
      (config.reengage.template ? '' : ' (تحتاج REENGAGE_TEMPLATE)'),
  );
  setInterval(() => {
    runCallReminders().catch((e) => console.error('[Reminder]', e?.message || e));
    runReengage().catch((e) => console.error('[Reengage]', e?.message || e));
  }, config.reminder.checkMs);
}
