<?php
/* ============================================================
   BAIGR — smart sales assistant proxy (server-side).

   The website's chat widget POSTs the conversation here; this
   file adds the BAIGR sales persona and calls the Claude API,
   then returns the reply as JSON. The API key never leaves the
   server, and prices are never disclosed to visitors.

   Hostinger: pure PHP + cURL, no Composer needed. Just place
   this file next to index.html and create chat.config.php
   (see the SETUP block below) with your API key.
   ============================================================ */

/* ---------- 0. Setup / config -------------------------------
   Create a file named  chat.config.php  in the SAME folder,
   NOT tracked by git, containing only:

       <?php
       putenv('ANTHROPIC_API_KEY=sk-ant-xxxxxxxx');

   That keeps the key out of the codebase and out of the repo.
   ------------------------------------------------------------ */
if (is_file(__DIR__ . '/chat.config.php')) {
    require __DIR__ . '/chat.config.php';
}

// The model. Sonnet 5 is the sweet spot for a public, high-volume
// widget: very capable, sensible cost. Swap this one line for
// 'claude-opus-5' (max intelligence) or 'claude-haiku-4-5' (lowest
// cost) whenever you like.
const BAIGR_MODEL   = 'claude-sonnet-5';
const BAIGR_MAX_TOK = 512;   // replies stay short & human
const API_ENDPOINT  = 'https://api.anthropic.com/v1/messages';
const API_VERSION   = '2023-06-01';

// Basic guardrails so a public endpoint can't run up the bill.
const MAX_MSG_LEN     = 1200;  // chars per user message
const MAX_HISTORY     = 16;    // turns kept from the client
const RATE_MAX        = 30;    // requests …
const RATE_WINDOW_SEC = 600;   // … per this many seconds, per IP

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function fail($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail('method_not_allowed', 405);
}

$apiKey = getenv('ANTHROPIC_API_KEY');
if (!$apiKey) {
    fail('server_not_configured', 500);
}

/* ---------- 1. Simple per-IP rate limit --------------------- */
function rate_limited() {
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = sys_get_temp_dir() . '/baigr_rl_' . md5($ip);
    $now = time();
    $hits = [];
    if (is_file($key)) {
        $hits = json_decode((string) @file_get_contents($key), true) ?: [];
    }
    $hits = array_values(array_filter($hits, function ($t) use ($now) {
        return $t > $now - RATE_WINDOW_SEC;
    }));
    if (count($hits) >= RATE_MAX) return true;
    $hits[] = $now;
    @file_put_contents($key, json_encode($hits), LOCK_EX);
    return false;
}
if (rate_limited()) {
    fail('rate_limited', 429);
}

/* ---------- 2. Read + validate the request ------------------ */
$raw = file_get_contents('php://input');
$req = json_decode($raw, true);
if (!is_array($req)) {
    fail('bad_request');
}

$locale = in_array(($req['locale'] ?? 'ar'), ['ar', 'en', 'tr'], true)
    ? $req['locale'] : 'ar';

$history = is_array($req['messages'] ?? null) ? $req['messages'] : [];
$messages = [];
foreach ($history as $m) {
    $role = ($m['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
    $text = trim((string) ($m['content'] ?? ''));
    if ($text === '') continue;
    if (mb_strlen($text) > MAX_MSG_LEN) $text = mb_substr($text, 0, MAX_MSG_LEN);
    $messages[] = ['role' => $role, 'content' => $text];
}
// Keep only the tail, and make sure it starts with a user turn.
if (count($messages) > MAX_HISTORY) {
    $messages = array_slice($messages, -MAX_HISTORY);
}
while ($messages && $messages[0]['role'] !== 'user') {
    array_shift($messages);
}
if (!$messages) {
    fail('empty_conversation');
}

/* ---------- 3. The BAIGR sales persona ---------------------- */
$langLine = [
    'ar' => 'الزائر يتصفّح النسخة العربية. ردّ بالعربية الفصحى المبسّطة بلمسة خليجية دافئة، ما لم يكتب الزائر بلغة أخرى فبادله بلغته.',
    'en' => 'The visitor is on the English site. Reply in warm, natural English unless they write in another language.',
    'tr' => 'Ziyaretçi Türkçe sitede. Doğal ve sıcak bir Türkçe ile yanıtla; başka bir dilde yazarsa o dile geç.',
][$locale];

$system = <<<SYS
أنت "مساعد BAIGR" — خبير نمو ومبيعات يمثّل وكالة BAIGR، وكالة تسويق ونمو مبنية على الذكاء الاصطناعي تخدم الخليج والشرق الأوسط وتركيا.

$langLine

شخصيتك:
- إنسان حقيقي في ردّه: واثق، ذكي، ودود، مباشر، بلا مبالغة ولا كلام آلي. جُمل قصيرة (٢–٤ أسطر غالباً).
- بائع محترف يبني القيمة ويقود الحوار بأسئلة ذكية، لا موظف استقبال يكرّر "تواصل معنا".

مهمتك في كل محادثة:
1) افهم الزائر: اسأل عن نشاطه، السوق المستهدف، والهدف (مبيعات؟ عملاء محتملون؟ وعي بالعلامة؟). سؤال واحد في المرة.
2) اربط خدمات BAIGR بمشكلته تحديداً: إعلانات الأداء (Meta/Google/TikTok)، بناء وهوية العلامة، تصميم وتطوير المواقع، وأتمتة التسويق والردود بالذكاء الاصطناعي.
3) اصنع الثقة بلغة النتائج (نموّ في المبيعات، خفض تكلفة العميل، أتمتة تعمل ٢٤/٧) دون اختلاق أرقام محددة عن عملاء بعينهم.
4) عالج الاعتراضات بهدوء وذكاء (الوقت، الميزانية، "جرّبت وكالات قبل").
5) قُد نحو خطوة تالية طبيعية: محادثة قصيرة عبر واتساب أو ترك بريد للتواصل — دون إلحاح ودون تكرارها في كل رسالة.

قواعد صارمة:
- لا تذكر الأسعار أو الأرقام أو الباقات إطلاقاً. إن سُئلت عن السعر، اشرح أن التسعير يُبنى على الهدف ونطاق العمل، واعرض ترتيب مكالمة قصيرة لتقديره بدقة.
- لا تختلق دراسات حالة أو أسماء عملاء أو أرقام دقيقة.
- لا تَعِد بما لا تعرفه؛ كن صادقاً وواثقاً.
- اذكر واتساب/البريد فقط عند نضج المحادثة، لا في كل رد.

معلومات التواصل عند الحاجة:
- واتساب: +90 537 857 31 81
- البريد: team@baigr.com
SYS;

/* ---------- 4. Call the Claude API via cURL ----------------- */
$payload = [
    'model'      => BAIGR_MODEL,
    'max_tokens' => BAIGR_MAX_TOK,
    'system'     => $system,
    'messages'   => $messages,
];

$ch = curl_init(API_ENDPOINT);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 45,
    CURLOPT_HTTPHEADER     => [
        'content-type: application/json',
        'x-api-key: ' . $apiKey,
        'anthropic-version: ' . API_VERSION,
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
]);

$resp   = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$cerr   = curl_error($ch);
curl_close($ch);

if ($resp === false) {
    fail('upstream_unreachable: ' . $cerr, 502);
}
if ($status < 200 || $status >= 300) {
    // Don't leak upstream detail to the browser; log it server-side.
    error_log('BAIGR chat upstream ' . $status . ': ' . $resp);
    fail('upstream_error', 502);
}

$data = json_decode($resp, true);
$reply = '';
if (isset($data['content']) && is_array($data['content'])) {
    foreach ($data['content'] as $block) {
        if (($block['type'] ?? '') === 'text') {
            $reply .= $block['text'];
        }
    }
}
$reply = trim($reply);
if ($reply === '') {
    fail('empty_reply', 502);
}

echo json_encode(['ok' => true, 'reply' => $reply], JSON_UNESCAPED_UNICODE);
