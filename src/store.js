import { config } from './config.js';

/**
 * ذاكرة محادثات بسيطة في الذاكرة (in-memory) لكل رقم عميل.
 * لكل عميل:
 *  - history: سجل المحادثة [{ role, content }]
 *  - buffer:  الرسائل الواردة التي لم يُرد عليها بعد (تُجمَّع خلال نافذة الانتظار)
 *  - timer:   مؤقّت الانتظار قبل الرد
 *  - profile: اسم العميل إن توفر
 *
 * ملاحظة: عند إعادة تشغيل الخادم تُفقد الذاكرة. للإنتاج طويل الأمد يمكن استبدال
 * هذا الملف بقاعدة بيانات (Redis / SQLite / Postgres) بنفس الواجهة.
 */
const conversations = new Map();

export function getConversation(waId) {
  let convo = conversations.get(waId);
  if (!convo) {
    convo = { history: [], buffer: [], timer: null, profile: {} };
    conversations.set(waId, convo);
  }
  return convo;
}

export function pushToBuffer(waId, text) {
  const convo = getConversation(waId);
  convo.buffer.push(text);
  return convo;
}

export function appendHistory(waId, role, content) {
  const convo = getConversation(waId);
  convo.history.push({ role, content });
  // قصّ السجل للحفاظ على حدود السياق والتكلفة
  const max = config.maxHistoryMessages;
  if (convo.history.length > max) {
    convo.history = convo.history.slice(convo.history.length - max);
  }
}

export function drainBuffer(waId) {
  const convo = getConversation(waId);
  const messages = convo.buffer;
  convo.buffer = [];
  return messages;
}

export function setProfileName(waId, name) {
  if (!name) return;
  const convo = getConversation(waId);
  if (!convo.profile.name) convo.profile.name = name;
}
