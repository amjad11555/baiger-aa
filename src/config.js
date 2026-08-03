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
  },

  anthropic: {
    apiKey: required('ANTHROPIC_API_KEY'),
    model: process.env.MODEL || 'claude-opus-5',
  },

  // مدة الانتظار قبل الرد — تجمع الرسائل المتتابعة وتجعل الرد يبدو بشريًا
  replyDelayMs: Number(process.env.REPLY_DELAY_MS || 10000),

  humanHandoffNote:
    process.env.HUMAN_HANDOFF_NOTE || 'سيتواصل معك أحد مختصينا خلال دقائق',

  // عدد الرسائل المحفوظة في ذاكرة كل محادثة
  maxHistoryMessages: Number(process.env.MAX_HISTORY_MESSAGES || 40),
};
