import { config } from './config.js';

const { token, phoneNumberId, graphVersion } = config.whatsapp;

const GRAPH_URL = `https://graph.facebook.com/${graphVersion}/${phoneNumberId}/messages`;

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
 * يبقى المؤشّر ظاهرًا حتى ~25 ثانية أو حتى نرسل الرد — وهو ما يجعل انتظار العميل طبيعيًا.
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
