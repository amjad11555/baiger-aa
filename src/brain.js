import Anthropic from '@anthropic-ai/sdk';
import { config } from './config.js';
import { buildSystemPrompt } from './prompt.js';
import { TOOLS, runTool } from './tools.js';

const client = new Anthropic({ apiKey: config.anthropic.apiKey });

// نبني نظام التعليمات مرة واحدة ونثبّته للاستفادة من التخزين المؤقت (prompt caching).
const SYSTEM_PROMPT = buildSystemPrompt();

const MAX_TOOL_ITERATIONS = 5;

/**
 * يولّد ردًّا بيعيًا ذكيًا مع تشغيل أدوات المبيعات (تحديث العميل / التحويل البشري).
 * @param {Array} messages - سجل المحادثة (المحتوى قد يكون نصًا أو كتل blocks لدعم الصور)
 * @param {Object} opts - { waId, contextNote }
 * @returns {Promise<{ text: string|null, effects: object }>}
 */
export async function generateReply(messages, { waId, contextNote } = {}) {
  const system = [
    { type: 'text', text: SYSTEM_PROMPT, cache_control: { type: 'ephemeral' } },
  ];
  if (contextNote) system.push({ type: 'text', text: contextNote });

  const convo = messages.map((m) => ({ role: m.role, content: m.content }));
  const textParts = [];
  const effects = { notifyOwner: [], escalated: false };

  try {
    for (let i = 0; i < MAX_TOOL_ITERATIONS; i++) {
      const response = await client.messages.create({
        model: config.anthropic.model,
        max_tokens: 2048,
        thinking: { type: 'adaptive' },
        output_config: { effort: 'medium' },
        system,
        tools: TOOLS,
        messages: convo,
      });

      if (response.stop_reason === 'refusal') {
        return { text: null, effects };
      }

      for (const block of response.content) {
        if (block.type === 'text' && block.text.trim()) {
          textParts.push(block.text.trim());
        }
      }

      const toolUses = response.content.filter((b) => b.type === 'tool_use');
      if (response.stop_reason !== 'tool_use' || toolUses.length === 0) {
        break;
      }

      // نُعيد رسالة المساعد كما هي (تحافظ على كتل التفكير والأدوات) ثم نُرجع نتائج الأدوات
      convo.push({ role: 'assistant', content: response.content });
      const toolResults = [];
      for (const tu of toolUses) {
        const { result, effects: e } = runTool(waId, tu.name, tu.input || {});
        if (e.notifyOwner) effects.notifyOwner.push(e.notifyOwner);
        if (e.escalated) effects.escalated = true;
        toolResults.push({
          type: 'tool_result',
          tool_use_id: tu.id,
          content: result,
        });
      }
      convo.push({ role: 'user', content: toolResults });
    }

    const text = textParts.join('\n').trim() || null;
    return { text, effects };
  } catch (err) {
    console.error('[Claude] خطأ في توليد الرد:', err?.message || err);
    return { text: null, effects };
  }
}
