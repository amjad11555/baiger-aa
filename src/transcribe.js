import { config } from './config.js';

/**
 * يفرّغ رسالة صوتية إلى نص عربي عبر خدمة متوافقة مع OpenAI Whisper
 * (تعمل مع OpenAI أو Groq أو أي واجهة /audio/transcriptions متوافقة).
 * يُرجع النص، أو null عند التعطيل أو الفشل.
 */
export async function transcribeAudio(buffer, mime = 'audio/ogg') {
  if (!config.voice.enabled) return null;
  try {
    const form = new FormData();
    form.append('model', config.voice.model);
    form.append('language', 'ar');
    form.append(
      'file',
      new Blob([buffer], { type: mime || 'audio/ogg' }),
      'audio.ogg',
    );

    const res = await fetch(config.voice.apiUrl, {
      method: 'POST',
      headers: { Authorization: `Bearer ${config.voice.apiKey}` },
      body: form,
    });

    if (!res.ok) {
      const t = await res.text().catch(() => '');
      console.error(`[Transcribe] فشل التفريغ (${res.status}): ${t}`);
      return null;
    }
    const data = await res.json().catch(() => ({}));
    return (data.text || '').trim() || null;
  } catch (err) {
    console.error('[Transcribe] خطأ:', err?.message || err);
    return null;
  }
}
