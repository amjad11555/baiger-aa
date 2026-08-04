import { upsertLead, setContactStatus, addAlert, getLead } from './store.js';

export const TOOLS = [
  {
    name: 'update_lead',
    description:
      'حدّث بيانات العميل المحتمل في نظام المبيعات. استدعِها كلما اكتشفت معلومة جديدة عن العميل ' +
      '(اسمه، ما يهتم به، مؤشّر ميزانيته) أو تغيّرت مرحلته البيعية. لا تُرسل رسالة للعميل عبر هذه الأداة.',
    input_schema: {
      type: 'object',
      properties: {
        name: { type: 'string', description: 'اسم العميل إن عُرف' },
        interest: {
          type: 'string',
          description: 'الخدمة أو الحاجة التي يهتم بها العميل',
        },
        stage: {
          type: 'string',
          enum: [
            'new',
            'qualified',
            'interested',
            'hot',
            'negotiating',
            'won',
            'lost',
          ],
          description:
            'المرحلة البيعية: new جديد، qualified مؤهّل، interested مهتم، hot ساخن جاهز للشراء، negotiating يتفاوض، won صفقة مغلقة، lost فُقد',
        },
        budget_signal: {
          type: 'string',
          description: 'أي مؤشّر عن ميزانية العميل أو حساسيته للسعر',
        },
        summary: {
          type: 'string',
          description: 'ملخّص قصير جدًا لحالة العميل واحتياجه',
        },
        next_action: {
          type: 'string',
          description: 'الخطوة التالية المقترحة لإغلاق الصفقة',
        },
      },
      required: ['stage', 'summary'],
    },
  },
  {
    name: 'book_call',
    description:
      'سجّل موعد مكالمة بعد أن يوافق العميل ويعطيك الوقت المناسب له. استدعِها بمجرد حصولك على الوقت المفضّل. ' +
      'سيصل تنبيه فوري بالموعد لفريق الوكالة ليتصل بالعميل. بعدها أرسل للعميل رسالة تأكيد لطيفة.',
    input_schema: {
      type: 'object',
      properties: {
        preferred_time: {
          type: 'string',
          description: 'اليوم/الوقت المناسب للعميل كما ذكره بالضبط',
        },
        name: { type: 'string', description: 'اسم العميل إن عُرف' },
        interest: {
          type: 'string',
          description: 'الخدمة أو الحاجة التي يهتم بها العميل',
        },
        summary: {
          type: 'string',
          description: 'ملخّص قصير جدًا لاحتياج العميل (لتجهيز الفريق للمكالمة)',
        },
      },
      required: ['preferred_time'],
    },
  },
  {
    name: 'escalate_to_human',
    description:
      'حوّل المحادثة لمختص بشري في الحالات الحرجة أو المعقّدة (شكوى، طلب استثنائي، تفاوض كبير، ' +
      'طلب صريح للتحدث مع شخص). بعد استدعائها سيتوقف الرد الآلي حتى يتدخّل الفريق. ' +
      'أرسل أيضًا رسالة لطيفة للعميل تطمئنه أن مختصًا سيتواصل معه.',
    input_schema: {
      type: 'object',
      properties: {
        reason: { type: 'string', description: 'سبب التحويل باختصار' },
      },
      required: ['reason'],
    },
  },
];

/**
 * ينفّذ أداة ويُرجع نص النتيجة (يعود للنموذج) + التأثيرات الجانبية للمعالجة في index.
 */
export function runTool(waId, name, input) {
  const effects = {};
  try {
    if (name === 'update_lead') {
      upsertLead(waId, input);
      const hot = ['hot', 'negotiating'].includes(input.stage);
      if (hot) {
        const txt = `🔥 عميل ساخن (${waId})\nالاهتمام: ${input.interest || '—'}\nالميزانية: ${input.budget_signal || '—'}\nالملخّص: ${input.summary || '—'}\nالخطوة التالية: ${input.next_action || '—'}`;
        addAlert(waId, 'hot_lead', txt);
        effects.notifyOwner = txt;
      }
      return { result: 'تم تحديث بيانات العميل بنجاح.', effects };
    }

    if (name === 'book_call') {
      upsertLead(waId, {
        name: input.name,
        interest: input.interest,
        stage: 'hot',
        summary: input.summary,
        next_action: `مكالمة مجدولة: ${input.preferred_time}`,
      });
      const txt =
        `📞 موعد مكالمة جديد (${waId})\n` +
        `العميل: ${input.name || '—'}\n` +
        `الوقت المفضّل: ${input.preferred_time}\n` +
        `الاهتمام: ${input.interest || '—'}\n` +
        `الملخّص: ${input.summary || '—'}`;
      addAlert(waId, 'call_booked', txt);
      effects.notifyOwner = txt;
      return {
        result:
          'تم تسجيل الموعد وإخطار الفريق. أكّد للعميل بلطف أن أحد مختصينا سيتواصل معه في الوقت المحدّد.',
        effects,
      };
    }

    if (name === 'escalate_to_human') {
      setContactStatus(waId, 'human');
      const lead = getLead(waId);
      const txt = `🙋 طلب تحويل بشري (${waId})\nالسبب: ${input.reason}\nالملخّص: ${lead?.summary || '—'}`;
      addAlert(waId, 'handoff', txt);
      effects.notifyOwner = txt;
      effects.escalated = true;
      return {
        result: 'تم تحويل المحادثة لمختص بشري. سيتوقف الرد الآلي الآن.',
        effects,
      };
    }

    return { result: `أداة غير معروفة: ${name}`, effects };
  } catch (err) {
    return { result: `خطأ في تنفيذ الأداة: ${err?.message || err}`, effects };
  }
}
