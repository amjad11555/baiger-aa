import { config } from './config.js';

const { token, phoneNumberId, graphVersion } = config.whatsapp;

const BASE = `https://graph.facebook.com/${graphVersion}`;
const GRAPH_URL = `${BASE}/${phoneNumberId}/messages`;

async function callGraph(payload) {
  const res = await fetch(GRAPH_URL, {
    method: 'POST',
    headers: {
      Authorization: `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(payload),
  });

  if (!res.ok) {
    const errText = await res.text().catch(() => '');
    console.error(`[WhatsApp] فشل الطلب (${res.status}): ${errText}`);
    return null;
  }
  return res.json().catch(() => ({}));
}

/**
 * تعليم الرسالة كمقروءة وإظهار مؤشّر "يكتب الآن..." للعميل.
 */
export async function markReadAndTyping(messageId) {
  if (!messageId) return;
  await callGraph({
    messaging_product: 'whatsapp',
    status: 'read',
    message_id: messageId,
    typing_indicator: { type: 'text' },
  });
}

/**
 * إرسال رسالة نصية للعميل.
 */
export async function sendText(to, body) {
  if (!body || !body.trim()) return;
  return callGraph({
    messaging_product: 'whatsapp',
    recipient_type: 'individual',
    to,
    type: 'text',
    text: { preview_url: false, body: body.slice(0, 4096) },
  });
}

const SUPPORTED_IMAGE = new Set([
  'image/jpeg',
  'image/png',
  'image/webp',
  'image/gif',
]);

/**
 * تنزيل وسائط (صورة) من واتساب وإرجاعها base64 لتحليلها عبر رؤية Claude.
 * يُرجع null إذا لم تكن صورة مدعومة أو فشل التنزيل أو تجاوز الحجم.
 */
export async function downloadMedia(mediaId) {
  try {
    const metaRes = await fetch(`${BASE}/${mediaId}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (!metaRes.ok) return null;
    const meta = await metaRes.json();
    const mime = (meta.mime_type || '').split(';')[0].trim();
    if (!SUPPORTED_IMAGE.has(mime)) return null;
    if (meta.file_size && meta.file_size > 5 * 1024 * 1024) return null; // 5MB

    const binRes = await fetch(meta.url, {
      headers: { Authorization: `Bearer ${token}` },
    });
    if (!binRes.ok) return null;
    const buf = Buffer.from(await binRes.arrayBuffer());
    if (buf.length > 5 * 1024 * 1024) return null;

    return { base64: buf.toString('base64'), mime };
  } catch (err) {
    console.error('[WhatsApp] فشل تنزيل الوسائط:', err?.message || err);
    return null;
  }
}
