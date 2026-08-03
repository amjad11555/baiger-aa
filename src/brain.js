import Anthropic from '@anthropic-ai/sdk';
import { config } from './config.js';
import { buildSystemPrompt } from './prompt.js';

const client = new Anthropic({ apiKey: config.anthropic.apiKey });

// نبني نظام التعليمات مرة واحدة ونثبّته للاستفادة من التخزين المؤقت (prompt caching).
const SYSTEM_PROMPT = buildSystemPrompt();

/**
 * يولّد ردًّا بيعيًا ذكيًا بناءً على سجل المحادثة الكامل.
 * history: مصفوفة [{ role: 'user' | 'assistant', content: string }]
 */
export async function generateReply(history) {
  try {
    const response = await client.messages.create({
      model: config.anthropic.model,
      max_tokens: 1024,
      thinking: { type: 'adaptive' },
      output_config: { effort: 'medium' },
      system: [
        {
          type: 'text',
          text: SYSTEM_PROMPT,
          cache_control: { type: 'ephemeral' },
        },
      ],
      messages: history.map((m) => ({ role: m.role, content: m.content })),
    });

    if (response.stop_reason === 'refusal') {
      return null; // حالة نادرة — نتركها بلا رد بدل إرسال شيء غير مناسب
    }

    const text = response.content
      .filter((b) => b.type === 'text')
      .map((b) => b.text)
      .join('\n')
      .trim();

    return text || null;
  } catch (err) {
    console.error('[Claude] خطأ في توليد الرد:', err?.message || err);
    return null;
  }
}
