import Database from 'better-sqlite3';
import { mkdirSync } from 'node:fs';
import { dirname } from 'node:path';
import { config } from './config.js';

mkdirSync(dirname(config.dbPath), { recursive: true });

const db = new Database(config.dbPath);
db.pragma('journal_mode = WAL');
db.pragma('foreign_keys = ON');

db.exec(`
CREATE TABLE IF NOT EXISTS contacts (
  wa_id TEXT PRIMARY KEY,
  name TEXT,
  status TEXT NOT NULL DEFAULT 'active',   -- active | human | paused | opted_out
  followup_count INTEGER NOT NULL DEFAULT 0,
  last_inbound_at INTEGER,
  last_outbound_at INTEGER,
  created_at INTEGER NOT NULL,
  updated_at INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS messages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  wa_id TEXT NOT NULL,
  role TEXT NOT NULL,                       -- user | assistant
  content TEXT NOT NULL,
  created_at INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_messages_wa ON messages(wa_id, id);

CREATE TABLE IF NOT EXISTS processed_events (
  msg_id TEXT PRIMARY KEY,
  created_at INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS leads (
  wa_id TEXT PRIMARY KEY,
  name TEXT,
  interest TEXT,
  stage TEXT NOT NULL DEFAULT 'new',        -- new|qualified|interested|hot|negotiating|won|lost
  budget_signal TEXT,
  summary TEXT,
  next_action TEXT,
  updated_at INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS alerts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  wa_id TEXT,
  type TEXT NOT NULL,                        -- hot_lead | handoff | error
  text TEXT NOT NULL,
  seen INTEGER NOT NULL DEFAULT 0,
  created_at INTEGER NOT NULL
);
`);

// ترقية آمنة: إضافة أعمدة المواعيد والمتابعة إن لم تكن موجودة (لقواعد بيانات قديمة)
function ensureColumn(table, col, ddl) {
  const cols = db.prepare(`PRAGMA table_info(${table})`).all().map((c) => c.name);
  if (!cols.includes(col)) db.exec(`ALTER TABLE ${table} ADD COLUMN ${ddl}`);
}
ensureColumn('leads', 'call_at', 'call_at INTEGER');
ensureColumn('leads', 'call_time_text', 'call_time_text TEXT');
ensureColumn('leads', 'call_reminded', 'call_reminded INTEGER NOT NULL DEFAULT 0');
ensureColumn('leads', 'reengage_count', 'reengage_count INTEGER NOT NULL DEFAULT 0');
ensureColumn('leads', 'last_reengage_at', 'last_reengage_at INTEGER');

const now = () => Date.now();

// ===== جهات الاتصال =====
const _upsertContact = db.prepare(`
  INSERT INTO contacts (wa_id, name, created_at, updated_at)
  VALUES (@wa_id, @name, @ts, @ts)
  ON CONFLICT(wa_id) DO UPDATE SET
    name = COALESCE(contacts.name, excluded.name),
    updated_at = excluded.updated_at
`);
export function upsertContact(waId, name = null) {
  _upsertContact.run({ wa_id: waId, name, ts: now() });
}

const _getContact = db.prepare('SELECT * FROM contacts WHERE wa_id = ?');
export const getContact = (waId) => _getContact.get(waId);

const _setStatus = db.prepare(
  'UPDATE contacts SET status = ?, updated_at = ? WHERE wa_id = ?',
);
export const setContactStatus = (waId, status) =>
  _setStatus.run(status, now(), waId);

const _setName = db.prepare(
  "UPDATE contacts SET name = ?, updated_at = ? WHERE wa_id = ? AND (name IS NULL OR name = '')",
);
export const setContactName = (waId, name) => {
  if (name) _setName.run(name, now(), waId);
};

const _touchInbound = db.prepare(
  'UPDATE contacts SET last_inbound_at = ?, followup_count = 0, updated_at = ? WHERE wa_id = ?',
);
export const touchInbound = (waId, ts = now()) => _touchInbound.run(ts, ts, waId);

const _touchOutbound = db.prepare(
  'UPDATE contacts SET last_outbound_at = ?, updated_at = ? WHERE wa_id = ?',
);
export const touchOutbound = (waId, ts = now()) =>
  _touchOutbound.run(ts, ts, waId);

const _incFollowup = db.prepare(
  'UPDATE contacts SET followup_count = followup_count + 1, updated_at = ? WHERE wa_id = ?',
);
export const incFollowup = (waId) => _incFollowup.run(now(), waId);

// ===== الرسائل =====
const _addMessage = db.prepare(
  'INSERT INTO messages (wa_id, role, content, created_at) VALUES (?, ?, ?, ?)',
);
export const addMessage = (waId, role, content) =>
  _addMessage.run(waId, role, content, now());

const _recent = db.prepare(
  'SELECT role, content FROM messages WHERE wa_id = ? ORDER BY id DESC LIMIT ?',
);
export function getRecentMessages(waId, limit) {
  return _recent.all(waId, limit).reverse();
}

// ===== منع تكرار أحداث الويبهوك =====
const _isProcessed = db.prepare('SELECT 1 FROM processed_events WHERE msg_id = ?');
const _markProcessed = db.prepare(
  'INSERT OR IGNORE INTO processed_events (msg_id, created_at) VALUES (?, ?)',
);
export const isProcessed = (msgId) => !!_isProcessed.get(msgId);
export const markProcessed = (msgId) => _markProcessed.run(msgId, now());

// ===== العملاء المحتملون (CRM) =====
const _upsertLead = db.prepare(`
  INSERT INTO leads (wa_id, name, interest, stage, budget_signal, summary, next_action, updated_at)
  VALUES (@wa_id, @name, @interest, @stage, @budget_signal, @summary, @next_action, @ts)
  ON CONFLICT(wa_id) DO UPDATE SET
    name          = COALESCE(excluded.name, leads.name),
    interest      = COALESCE(excluded.interest, leads.interest),
    stage         = COALESCE(excluded.stage, leads.stage),
    budget_signal = COALESCE(excluded.budget_signal, leads.budget_signal),
    summary       = COALESCE(excluded.summary, leads.summary),
    next_action   = COALESCE(excluded.next_action, leads.next_action),
    updated_at    = excluded.updated_at
`);
export function upsertLead(waId, f = {}) {
  _upsertLead.run({
    wa_id: waId,
    name: f.name ?? null,
    interest: f.interest ?? null,
    stage: f.stage ?? null,
    budget_signal: f.budget_signal ?? null,
    summary: f.summary ?? null,
    next_action: f.next_action ?? null,
    ts: now(),
  });
}
const _getLead = db.prepare('SELECT * FROM leads WHERE wa_id = ?');
export const getLead = (waId) => _getLead.get(waId);

// ===== مواعيد المكالمات =====
const _setAppointment = db.prepare(
  'UPDATE leads SET call_at = ?, call_time_text = ?, call_reminded = 0, updated_at = ? WHERE wa_id = ?',
);
export const setAppointment = (waId, callAt, timeText) =>
  _setAppointment.run(callAt ?? null, timeText ?? null, now(), waId);

const _markReminded = db.prepare(
  'UPDATE leads SET call_reminded = 1, updated_at = ? WHERE wa_id = ?',
);
export const markCallReminded = (waId) => _markReminded.run(now(), waId);

// مكالمات قادمة خلال الفترة القريبة لم يُرسل لها تذكير بعد
const _dueReminders = db.prepare(`
  SELECT l.wa_id, l.call_at, l.call_time_text, c.last_inbound_at
  FROM leads l JOIN contacts c ON c.wa_id = l.wa_id
  WHERE l.call_at IS NOT NULL AND l.call_reminded = 0
    AND c.status = 'active'
    AND l.call_at > @now AND l.call_at <= @horizon
`);
export const dueCallReminders = (nowTs, beforeMs) =>
  _dueReminders.all({ now: nowTs, horizon: nowTs + beforeMs });

// ===== متابعة العملاء الذين لم يُغلقوا الصفقة (بعد يومين+) =====
const _incReengage = db.prepare(
  'UPDATE leads SET reengage_count = reengage_count + 1, last_reengage_at = ?, updated_at = ? WHERE wa_id = ?',
);
export const incReengage = (waId, ts = now()) =>
  _incReengage.run(ts, ts, waId);

const _reengage = db.prepare(`
  SELECT l.wa_id, l.name, l.stage, c.last_inbound_at, c.last_outbound_at
  FROM leads l JOIN contacts c ON c.wa_id = l.wa_id
  WHERE c.status = 'active'
    AND l.stage NOT IN ('won', 'lost')
    AND COALESCE(c.last_outbound_at, c.last_inbound_at, 0) < @idleBefore
    AND l.reengage_count < @maxCount
    AND (l.last_reengage_at IS NULL OR l.last_reengage_at < @reengageBefore)
    AND (l.call_at IS NULL OR l.call_at < @now)
`);
export const reengageCandidates = ({ idleBefore, maxCount, reengageBefore, now: nowTs }) =>
  _reengage.all({ idleBefore, maxCount, reengageBefore, now: nowTs ?? Date.now() });

// ===== التنبيهات =====
const _addAlert = db.prepare(
  'INSERT INTO alerts (wa_id, type, text, created_at) VALUES (?, ?, ?, ?)',
);
export const addAlert = (waId, type, text) =>
  _addAlert.run(waId, type, text, now());

// ===== المتابعة التلقائية =====
// عملاء نشطون، ضمن نافذة 24 ساعة، صمتوا فترة، ولم يُستنفد عدد المتابعات.
const _followupCandidates = db.prepare(`
  SELECT c.wa_id, c.followup_count, l.stage
  FROM contacts c
  LEFT JOIN leads l ON l.wa_id = c.wa_id
  WHERE c.status = 'active'
    AND c.last_inbound_at IS NOT NULL
    AND c.last_inbound_at > @windowStart
    AND COALESCE(c.last_outbound_at, 0) >= COALESCE(c.last_inbound_at, 0)
    AND (COALESCE(c.last_outbound_at, c.last_inbound_at)) < @silentBefore
    AND c.followup_count < @maxFollowups
    AND COALESCE(l.stage, 'new') NOT IN ('won', 'lost')
`);
export function followupCandidates({ windowStart, silentBefore, maxFollowups }) {
  return _followupCandidates.all({ windowStart, silentBefore, maxFollowups });
}

// ===== إحصائيات =====
export function stats() {
  const total = db.prepare('SELECT COUNT(*) n FROM contacts').get().n;
  const byStage = db
    .prepare('SELECT stage, COUNT(*) n FROM leads GROUP BY stage')
    .all();
  const byStatus = db
    .prepare('SELECT status, COUNT(*) n FROM contacts GROUP BY status')
    .all();
  const msgs = db.prepare('SELECT COUNT(*) n FROM messages').get().n;
  const openAlerts = db
    .prepare('SELECT COUNT(*) n FROM alerts WHERE seen = 0')
    .get().n;
  return { contacts: total, messages: msgs, byStage, byStatus, openAlerts };
}

export default db;
