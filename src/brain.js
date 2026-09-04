import Anthropic from '@anthropic-ai/sdk';
import { config } from './config.js';
import { buildSystemPrompt } from './prompt.js';
import { TOOLS, runTool } from './tools.js';
import { checkReply, sanitizeReply, correctionNote } from './guard.js';

// تهيئة كسولة لعميل Claude — حتى يُقلع الخادم حتى لو لم يُضبط المفتاح بعد
let _client;
function client() {
  if (!_client) _client = new Anthropic({ apiKey: config.anthropic.apiKey });
  return _client;
}

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
      const response = await client().messages.create({
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

    let text = textParts.join('\n').trim() || null;
    if (!text) return { text, effects };

    // ===== حارس الرد: فحص حتمي قبل الإرسال =====
    let violations = checkReply(text);
    if (violations.length) {
      console.warn('[guard] مخالفات في الرد الأول:', violations.join(' | '));
      // إعادة صياغة واحدة مع تسمية المخالفات
      const fixed = await regenerate(system, convo, text, violations);
      if (fixed) {
        const v2 = checkReply(fixed);
        if (!v2.length) {
          text = fixed;
          violations = [];
        } else {
          console.warn('[guard] المخالفات بعد الإعادة:', v2.join(' | '));
          text = fixed;
          violations = v2;
        }
      }
    }
    if (violations.length) {
      // تصحيح ميكانيكي أخير — لا نرسل رداً مخالفاً أبداً
      const before = text;
      text = sanitizeReply(text);
      console.warn('[guard] طُبّق تصحيح آلي.', { before: before.slice(0, 120), after: text.slice(0, 120) });
    }
    return { text, effects };
  } catch (err) {
    console.error('[Claude] خطأ في توليد الرد:', err?.message || err);
    return { text: null, effects };
  }
}

/**
 * يطلب من النموذج إعادة صياغة رد مخالف، بلا أدوات، مرة واحدة.
 * @returns {Promise<string|null>}
 */
async function regenerate(system, convo, badText, violations) {
  try {
    const response = await client().messages.create({
      model: config.anthropic.model,
      max_tokens: 400,
      system,
      messages: [
        ...convo,
        { role: 'assistant', content: badText },
        { role: 'user', content: correctionNote(violations) },
      ],
    });
    const out = response.content
      .filter((b) => b.type === 'text')
      .map((b) => b.text.trim())
      .join(' ')
      .trim();
    return out || null;
  } catch (err) {
    console.error('[guard] فشلت إعادة الصياغة:', err?.message || err);
    return null;
  }
}
