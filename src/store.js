import { config } from './config.js';
import * as db from './db.js';

/**
 * الحالة اللحظية لكل عميل (buffers + timers) تبقى في الذاكرة،
 * بينما سجل المحادثة والعملاء المحتملون يُحفظون بشكل دائم في SQLite (db.js).
 */
const runtime = new Map();

function getRuntime(waId) {
  let r = runtime.get(waId);
  if (!r) {
    r = { buffer: [], timer: null };
    runtime.set(waId, r);
  }
  return r;
}

// ===== المخزن المؤقّت للرسائل الواردة (قبل الرد) =====
// عناصر: { type: 'text', text } أو { type: 'image', mediaId, caption }
export function pushToBuffer(waId, item) {
  const r = getRuntime(waId);
  r.buffer.push(item);
  return r;
}
export function drainBuffer(waId) {
  const r = getRuntime(waId);
  const items = r.buffer;
  r.buffer = [];
  return items;
}
export function getTimerHolder(waId) {
  return getRuntime(waId);
}

// ===== غلاف حول قاعدة البيانات =====
export const ensureContact = (waId, name) => db.upsertContact(waId, name);
export const setProfileName = (waId, name) => db.setContactName(waId, name);
export const getContact = (waId) => db.getContact(waId);
export const setContactStatus = (waId, status) =>
  db.setContactStatus(waId, status);

export function appendHistory(waId, role, content) {
  db.addMessage(waId, role, content);
  if (role === 'user') db.touchInbound(waId);
  else if (role === 'assistant') db.touchOutbound(waId);
}

export const getHistory = (waId) =>
  db.getRecentMessages(waId, config.maxHistoryMessages);

export const isDuplicate = (msgId) => db.isProcessed(msgId);
export const markSeen = (msgId) => db.markProcessed(msgId);

export const getLead = (waId) => db.getLead(waId);
export const upsertLead = (waId, fields) => db.upsertLead(waId, fields);
export const setAppointment = (waId, callAt, timeText) =>
  db.setAppointment(waId, callAt, timeText);

// إعدادات عامة + لوحة التحكم
export const getSetting = (key, def) => db.getSetting(key, def);
export const setSetting = (key, value) => db.setSetting(key, value);
export const conversations = () => db.conversations();
export const messagesFor = (waId, limit) => db.messagesFor(waId, limit);
export const autoReplyEnabled = () => db.getSetting('auto_reply_enabled', '1') === '1';
export const addAlert = (waId, type, text) => db.addAlert(waId, type, text);
