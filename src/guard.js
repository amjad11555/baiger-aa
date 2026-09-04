/**
 * حارس الرد — فحص حتمي (بلا نموذج) لكل رد قبل إرساله للعميل.
 *
 * التعليمات وحدها لا تكفي: النموذج ينساها أحياناً. هذا الحارس يمسك المخالفات
 * التي تكلّف صفقة أو سمعة: أكثر من سؤال، رسالة طويلة، قوائم، أرقام ممنوعة،
 * عبارات ممنوعة. عند المخالفة يُطلب من النموذج إعادة الصياغة مرة واحدة،
 * وإن استمرت تُطبَّق تصحيحات ميكانيكية أو رد آمن.
 */

export const LIMITS = {
  maxQuestions: 1,
  maxWords: 45, // التعليمات تقول ~25؛ هذا سقف صلب يترك هامشاً لرسائل التأكيد
  maxLines: 4,
};

// الأرقام الوحيدة المسموح اقترانها بعملة: نطاق خدمة المواقع المعلن.
const ALLOWED_AMOUNTS = new Set(['400', '5000', '5,000', '٤٠٠', '٥٠٠٠']);

// عبارات تكشف مخالفة سياسة واضحة. تُطابَق بلا حساسية للتشكيل.
const BANNED_PHRASES = [
  // تسعير
  { re: /حتى\s+أحدد\s+لك\s+السعر|أحدد\s+لك\s+السعر|أعطيك\s+السعر\s+والمدة|لأحدد\s+السعر/, why: 'الوكيل لا يحدد سعراً' },
  { re: /\bخصم\b|خصومات|عرض\s+خاص|العرض\s+ينتهي|ينتهي\s+اليوم|لفترة\s+محدودة/, why: 'خصم أو استعجال كاذب' },
  { re: /\bباقة\b|باقات|حزمة\s+الخدمات|خارج\s+الباقة/, why: 'لا باقات' },
  { re: /تقسيط|دفعتين|دفعات|الدفعة\s+الأولى|مقدم\b/, why: 'شروط الدفع للفريق' },
  // ضمانات ونتائج
  { re: /أضمن\s+(لك|إلك)\s+(نتائج|زيادة|مبيعات|ترتيب)|ضمان\s+(نتائج|مبيعات|استرجاع)|استرجاع\s+(المبلغ|الفلوس|المصاري)/, why: 'ضمان نتائج أو استرجاع' },
  { re: /تعديلات\s+غير\s+محدودة|لين\s+يعجبك|لحد\s+ما\s+يعجبك|ما\s+في\s+التزام/, why: 'وعد تعاقدي' },
  // إنكار كونه روبوتاً
  { re: /أنا\s+إنسان|مو\s+روبوت|مش\s+روبوت|لست\s+روبوت|لست\s+ذكاء/, why: 'إنكار كونه روبوتاً' },
  // نفي المعرفة بالخدمات
  { re: /ما\s+عندي\s+معلومات|لا\s+أملك\s+معلومات|ما\s+بعرف\s+إذا\s+(منعمل|بنعمل|منقدم)|مش\s+متأكد\s+إذا\s+(منعمل|بنعمل)/, why: 'نفي معرفة بخدمة' },
  { re: /ما\s+(منعمل|بنعمل|منقدم|نقدم)\s+(هذا|هيك|هاد|هالشي)|هذا\s+غير\s+متاح|مستحيل/, why: 'نفي قدرة' },
  // لغة مندوب مبيعات لا مندوب مواعيد
  { re: /كيف\s+(أستطيع|يمكنني)\s+مساعدتك/, why: 'صيغة رسمية جامدة' },
];

// مقاطع تدل على سرد/قائمة
const LIST_LINE = /^\s*(?:[-•*▪●]|\d+[.)]|[أ-ي][.)])\s+/m;
const MARKDOWN_STRUCT = /^\s*#|^\s*\|.*\|\s*$/m;

function countQuestions(text) {
  return (text.match(/[؟?]/g) || []).length;
}

function countWords(text) {
  return text
    .replace(/[^\p{L}\p{N}\s]/gu, ' ')
    .split(/\s+/)
    .filter(Boolean).length;
}

function currencyViolations(text) {
  const found = [];
  const re = /([0-9٠-٩][0-9٠-٩,.]*)\s*(?:دولار|\$|USD|ريال|ليرة|درهم|جنيه|يورو|€)/g;
  let m;
  while ((m = re.exec(text))) {
    const n = m[1].replace(/[.,]$/, '');
    if (!ALLOWED_AMOUNTS.has(n)) found.push(m[0]);
  }
  if (/[0-9٠-٩]+\s*%/.test(text)) found.push('نسبة مئوية');
  return found;
}

/**
 * يفحص الرد ويرجع قائمة مخالفات (فارغة = سليم).
 * @param {string} text
 * @returns {string[]}
 */
export function checkReply(text) {
  const v = [];
  if (!text) return v;

  const q = countQuestions(text);
  if (q > LIMITS.maxQuestions) v.push(`فيه ${q} أسئلة — المسموح سؤال واحد`);

  const w = countWords(text);
  if (w > LIMITS.maxWords) v.push(`طويل (${w} كلمة) — الحد ${LIMITS.maxWords}، والمطلوب ~25`);

  const lines = text.split('\n').filter((l) => l.trim()).length;
  if (lines > LIMITS.maxLines) v.push(`${lines} أسطر — لا قوائم ولا فقرات`);
  if (LIST_LINE.test(text)) v.push('يحتوي قائمة نقاط أو ترقيم — ممنوع السرد');
  if (MARKDOWN_STRUCT.test(text)) v.push('يحتوي عناوين أو جداول');

  for (const c of currencyViolations(text)) v.push(`رقم ممنوع: «${c}»`);

  for (const { re, why } of BANNED_PHRASES) {
    if (re.test(text)) v.push(`عبارة ممنوعة (${why})`);
  }
  return v;
}

/**
 * تصحيح ميكانيكي أخير عندما يفشل النموذج في إعادة الصياغة.
 * يزيل القوائم، يقتطع للجملة الأولى التي تنتهي بسؤال إن وُجد، ويقصّ الطول.
 * إن بقيت مخالفة سياسة (رقم/عبارة ممنوعة) يُرجع رداً آمناً.
 */
export function sanitizeReply(text) {
  // سرد طويل أو قائمة: قصّه يُبقيه سرداً. الرد الآمن أفضل.
  if (countWords(text) > LIMITS.maxWords * 1.6 || LIST_LINE.test(text)) return SAFE_REPLY;

  let t = text
    .split('\n')
    .filter((l) => !LIST_LINE.test(l) && !MARKDOWN_STRUCT.test(l))
    .join(' ')
    .replace(/\s+/g, ' ')
    .trim();

  // أبقِ حتى أول سؤال (ضمناً) إن وُجد أكثر من سؤال
  const firstQ = t.search(/[؟?]/);
  if (firstQ !== -1 && countQuestions(t) > 1) t = t.slice(0, firstQ + 1).trim();

  // قصّ الطول على حدود الجمل
  if (countWords(t) > LIMITS.maxWords) {
    const sentences = t.split(/(?<=[.!؟?])\s+/);
    let out = '';
    for (const s of sentences) {
      if (countWords(out + ' ' + s) > LIMITS.maxWords) break;
      out = (out + ' ' + s).trim();
    }
    t = out || t.split(/\s+/).slice(0, LIMITS.maxWords).join(' ');
  }

  const remaining = checkReply(t).filter(
    (m) => m.startsWith('رقم ممنوع') || m.startsWith('عبارة ممنوعة'),
  );
  if (remaining.length) return SAFE_REPLY;
  return t;
}

export const SAFE_REPLY =
  'خليني أرتبلك مكالمة مع الفريق يوضحولك كل شي بالتفصيل — إيمتى بتكون فاضي؟';

/**
 * نص التصحيح الذي يُرسل للنموذج عند طلب إعادة الصياغة.
 */
export function correctionNote(violations) {
  return (
    '⚠️ مراجعة داخلية قبل الإرسال — ردّك السابق خالف القواعد التالية:\n' +
    violations.map((x) => `- ${x}`).join('\n') +
    '\n\nأعد كتابة الرد نفسه ملتزماً: سؤال واحد كحد أقصى، جملتان ونحو 25 كلمة، ' +
    'بلا قوائم ولا أرقام ولا عبارات ممنوعة. أرسل الرد المُعاد فقط بلا شرح ولا اعتذار.'
  );
}
