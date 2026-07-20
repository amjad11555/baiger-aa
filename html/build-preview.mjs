/* ============================================================
   BAIGR — build derived deliverables from the html/ sources.

   Outputs:
   • baigr-preview.html — one fully self-contained file (CSS, JS,
     fonts, three.js and the dictionaries all inlined). Opens
     straight from disk (file://) with every language, the
     assistant and the 3D hero working. Use it to preview.
   • baigr-embed.html — a lighter single file for embedding inside
     WordPress via <iframe>: CSS/JS/dictionaries inlined, but the
     heavy libraries (GSAP, Lenis, three.js) and the Cairo font
     come from a CDN.

   Run:  node build-preview.mjs
   ============================================================ */
import { readFileSync, writeFileSync } from "node:fs";
import { fileURLToPath } from "node:url";
import { dirname, join } from "node:path";

const root = dirname(fileURLToPath(import.meta.url));
const read = (p) => readFileSync(join(root, p), "utf8");
const readB64 = (p) => readFileSync(join(root, p)).toString("base64");

const dicts = {
  en: JSON.parse(read("translations/en.json")),
  ar: JSON.parse(read("translations/ar.json")),
  tr: JSON.parse(read("translations/tr.json")),
};
const dictPreload = `window.BAIGR=window.BAIGR||{};window.BAIGR.__DICTS=${JSON.stringify(dicts)};\n`;

const CSS = {
  style: read("css/style.css"),
  anim: read("css/animations.css"),
  resp: read("css/responsive.css"),
};
const APP = ["language.js", "smooth-scroll.js", "parallax.js", "animations.js", "app.js"];
const threeScene = read("js/three-scene.js");

// split/join replace — no regex escaping, no `$` replacement-token pitfalls.
function inline(html, tag, code) {
  if (!html.includes(tag)) throw new Error("tag not found: " + tag);
  return html.split(tag).join(`<script>\n${code}\n</script>`);
}

function inlineApp(html) {
  for (const a of APP) {
    const code = a === "language.js" ? dictPreload + read("js/" + a) : read("js/" + a);
    html = inline(html, `<script defer src="js/${a}"></script>`, code);
  }
  return html;
}

/* ============================================================
   1. baigr-preview.html — everything inlined, zero network.
   ============================================================ */
function buildPreview() {
  let html = read("index.html");

  // Fonts: embed each woff2 as a data: URI, drop .woff fallbacks.
  let fontsCss = read("fonts/fonts.css")
    .replace(/,\s*url\(\.\/files\/[^)]+\.woff\)\s*format\('woff'\)/g, "")
    .replace(/url\(\.\/files\/([^)]+\.woff2)\)/g, (_, name) =>
      `url(data:font/woff2;base64,${readB64(join("fonts", "files", name))})`
    );

  const styleBlock = [fontsCss, CSS.style, CSS.anim, CSS.resp].join("\n\n");
  html = html
    .replace(/<link rel="preload" as="font"[^>]*>\s*/g, "")
    .replace(/<link rel="stylesheet" href="fonts\/fonts.css"[^>]*>\s*/g, "")
    .replace(/<link rel="stylesheet" href="css\/style.css"[^>]*>\s*/g, `<style>\n${styleBlock}\n</style>\n`)
    .replace(/<link rel="stylesheet" href="css\/animations.css"[^>]*>\s*/g, "")
    .replace(/<link rel="stylesheet" href="css\/responsive.css"[^>]*>\s*/g, "");

  // Vendor libs inlined.
  for (const v of ["gsap.min.js", "ScrollTrigger.min.js", "SplitText.min.js", "lenis.min.js"]) {
    html = inline(html, `<script defer src="assets/vendor/${v}"></script>`, read("assets/vendor/" + v));
  }
  html = inlineApp(html);

  // three.js: fold core into the wrapper (relative import won't resolve from data:).
  const coreData = `data:text/javascript;base64,${Buffer.from(read("assets/vendor/three.core.min.js")).toString("base64")}`;
  const threeMod = read("assets/vendor/three.module.js").split("./three.core.min.js").join(coreData);
  const threeData = `data:text/javascript;base64,${Buffer.from(threeMod).toString("base64")}`;
  html = html
    .replace(/\{ "imports": \{ "three": "\.\/assets\/vendor\/three\.module\.js" \} \}/,
      JSON.stringify({ imports: { three: threeData } }))
    .split('<script type="module" src="js/three-scene.js"></script>')
    .join(`<script type="module">\n${threeScene}\n</script>`);

  writeFileSync(join(root, "baigr-preview.html"), html);
  return Buffer.byteLength(html);
}

/* ============================================================
   2. baigr-embed.html — CSS/JS/dicts inlined; libs + font via CDN.
   ============================================================ */
function buildEmbed() {
  let html = read("index.html");
  const G_FONTS =
    `<link rel="preconnect" href="https://fonts.googleapis.com" />\n` +
    `  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />\n` +
    `  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />\n`;

  // Drop the local font machinery; Cairo comes from Google Fonts.
  const styleBlock = [CSS.style, CSS.anim, CSS.resp].join("\n\n");
  html = html
    .replace(/<link rel="preload" as="font"[^>]*>\s*/g, "")
    .replace(/<link rel="stylesheet" href="fonts\/fonts.css"[^>]*>\s*/g, G_FONTS)
    .replace(/<link rel="stylesheet" href="css\/style.css"[^>]*>\s*/g, `<style>\n${styleBlock}\n</style>\n`)
    .replace(/<link rel="stylesheet" href="css\/animations.css"[^>]*>\s*/g, "")
    .replace(/<link rel="stylesheet" href="css\/responsive.css"[^>]*>\s*/g, "");

  // Libraries from CDN.
  const CDN = {
    "gsap.min.js": "https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js",
    "ScrollTrigger.min.js": "https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js",
    "SplitText.min.js": "https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/SplitText.min.js",
    "lenis.min.js": "https://cdn.jsdelivr.net/npm/lenis@1.3.11/dist/lenis.min.js",
  };
  for (const [v, url] of Object.entries(CDN)) {
    html = html.split(`<script defer src="assets/vendor/${v}"></script>`)
      .join(`<script defer src="${url}"></script>`);
  }
  html = inlineApp(html);

  // three.js from CDN via the import map; three-scene inlined as a module.
  html = html
    .replace(/\{ "imports": \{ "three": "\.\/assets\/vendor\/three\.module\.js" \} \}/,
      JSON.stringify({ imports: { three: "https://cdn.jsdelivr.net/npm/three@0.185.0/build/three.module.js" } }))
    .split('<script type="module" src="js/three-scene.js"></script>')
    .join(`<script type="module">\n${threeScene}\n</script>`);

  writeFileSync(join(root, "baigr-embed.html"), html);
  return Buffer.byteLength(html);
}

const p = buildPreview();
const e = buildEmbed();
console.log(`baigr-preview.html — ${(p / 1024).toFixed(0)} KB (self-contained)`);
console.log(`baigr-embed.html   — ${(e / 1024).toFixed(0)} KB (CDN libs + Google Fonts)`);
