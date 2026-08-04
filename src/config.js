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
