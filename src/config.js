import 'dotenv/config';

function required(name) {
  const v = process.env[name];
  if (!v) {
    console.warn(`[تحذير] المتغير البيئي ${name} غير مضبوط — راجع ملف .env`);
  }
  return v;
}

export const config = {
  port: Number(process.env.PORT || 3000),

  whatsapp: {
    token: required('WHATSAPP_TOKEN'),
    phoneNumberId: required('WHATSAPP_PHONE_NUMBER_ID'),
    verifyToken: required('WHATSAPP_VERIFY_TOKEN'),
    graphVersion: process.env.GRAPH_API_VERSION || 'v21.0',
    // سر التطبيق للتحقق من توقيع الويبهوك (اختياري لكن يُنصح به بشدة)
    appSecret: process.env.WHATSAPP_APP_SECRET || '',
  },

  anthropic: {
    apiKey: required('ANTHROPIC_API_KEY'),
    model: process.env.MODEL || 'claude-opus-5',
  },

  // مدة الانتظار قبل الرد — تجمع الرسائل المتتابعة وتجعل الرد يبدو بشريًا
  replyDelayMs: Number(process.env.REPLY_DELAY_MS || 10000),

  humanHandoffNote:
    process.env.HUMAN_HANDOFF_NOTE || 'سيتواصل معك أحد مختصينا خلال دقائق',

  // عدد الرسائل المحفوظة في سياق كل محادثة
  maxHistoryMessages: Number(process.env.MAX_HISTORY_MESSAGES || 40),

  // قاعدة البيانات (حفظ دائم)
  dbPath: process.env.DB_PATH || 'data/baigr.db',

  // رقم صاحب الوكالة لاستقبال تنبيهات الصفقات الساخنة والتحويل البشري
  ownerWaId: process.env.OWNER_WA_ID || '',

  // مفتاح حماية لوحة الإحصائيات والإدارة
  adminKey: process.env.ADMIN_KEY || '',

  // المتابعة التلقائية للعملاء الصامتين
  followup: {
    enabled: (process.env.FOLLOWUP_ENABLED ?? 'true') !== 'false',
    hours: Number(process.env.FOLLOWUP_HOURS || 6), // بعد كم ساعة صمت نتابع
    max: Number(process.env.FOLLOWUP_MAX || 2), // أقصى عدد رسائل متابعة
    checkMs: Number(process.env.FOLLOWUP_CHECK_MS || 15 * 60 * 1000),
  },

  // المنطقة الزمنية للوكالة (لحساب مواعيد المكالمات)
  timezone: process.env.TIMEZONE || 'Asia/Riyadh',

  // لغة قوالب واتساب المعتمدة
  templateLang: process.env.TEMPLATE_LANG || 'ar',

  // تذكير العميل قبل موعد المكالمة
  reminder: {
    enabled: (process.env.REMINDER_ENABLED ?? 'true') !== 'false',
    beforeMinutes: Number(process.env.REMINDER_BEFORE_MINUTES || 60),
    checkMs: Number(process.env.REMINDER_CHECK_MS || 5 * 60 * 1000),
    template: process.env.REMINDER_TEMPLATE || '', // قالب معتمد للتذكير خارج 24 ساعة
  },

  // متابعة العميل الذي لم يُغلق الصفقة بعد يومين+ (تحتاج قالبًا معتمدًا)
  reengage: {
    afterDays: Number(process.env.REENGAGE_AFTER_DAYS || 2),
    everyDays: Number(process.env.REENGAGE_EVERY_DAYS || 2),
    max: Number(process.env.REENGAGE_MAX || 3),
    template: process.env.REENGAGE_TEMPLATE || '',
  },

  // تفريغ الرسائل الصوتية (خدمة متوافقة مع OpenAI Whisper — OpenAI / Groq / ...)
  // يُفعَّل تلقائيًا بمجرد وضع VOICE_API_KEY (ما لم يُضبط VOICE_TRANSCRIBE_ENABLED=false)
  voice: {
    enabled:
      !!process.env.VOICE_API_KEY &&
      process.env.VOICE_TRANSCRIBE_ENABLED !== 'false',
    apiUrl:
      process.env.VOICE_API_URL ||
      'https://api.openai.com/v1/audio/transcriptions',
    apiKey: process.env.VOICE_API_KEY || '',
    model: process.env.VOICE_MODEL || 'whisper-1',
  },
};
