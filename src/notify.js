import { config } from './config.js';
import { sendText } from './whatsapp.js';

/** يُرسل تنبيهًا لصاحب الوكالة (إن ضُبط رقمه) — أفضل جهد. */
export async function notifyOwner(text) {
  if (!config.ownerWaId || !text) return;
  try {
    await sendText(config.ownerWaId, text);
  } catch (err) {
    console.error('[Notify] تعذّر تنبيه المالك:', err?.message || err);
  }
}
