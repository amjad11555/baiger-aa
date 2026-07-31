# Generating article visuals with Higgsfield

Every BAIGR article ships with **original** imagery — never stock. Visuals earn
dwell time, backlinks, and image/AI-search visibility, and they make a 3000–5000
word piece readable.

## What to generate per article
- **1 hero image** — sets the topic, wide/landscape, works as the featured image.
- **2–4 section illustrations** — one per major concept/H2 that benefits visually.
- **≥1 infographic** — turns a process, comparison, or set of stats into a clean
  visual. This is the highest-value asset (most linkable, most citable).

## Tool
Use the Higgsfield MCP `generate_image` tool. Prefer the **nano_banana_pro** model
(Gemini) — strongest at legible text, UI mockups, and infographics. If unsure which
model fits, call `models_explore(action:'recommend')` first. Higgsfield has a daily
generation cap; if it's hit or the server is disconnected, don't block the article —
insert a labeled placeholder with the intended alt text and note it for the user.

## Brand look (keep visuals coherent with baigr.com)
- Clean, modern, premium, minimal. Light backgrounds; generous whitespace.
- Accent = the brand's warm **glossy yellow/gold**; deep near-black for contrast.
- Flat/editorial illustration or crisp 3D; **not** cheesy clip-art or literal stock
  photos of handshakes. Gulf/MENA context where people or places appear.
- Infographics: bold headline, 3–6 labeled steps/segments, iconography, restrained
  palette (yellow + dark + one neutral), readable at blog width (~800px).

## Prompt pattern (detailed = better)
Write rich, specific prompts. Weak prompts give generic output. Include: subject,
composition/angle, style, palette, mood, and — for infographics — the exact text
labels and layout. Specify aspect ratio (hero ≈ 16:9, infographic ≈ 4:5 or 1:1 for
vertical readability). Always generate in a language-neutral or Arabic-labeled way
so text matches the article's language.

**Hero example:**
"A premium, minimal editorial illustration for a marketing blog: a stylized growth
funnel over a Gulf city skyline at dusk, glossy golden-yellow accents on a near-black
and warm-neutral palette, clean vector style, lots of negative space, 16:9, no text."

**Infographic example (Arabic labels):**
"A clean vertical infographic titled ‘مراحل قمع إعلانات ميتا’, 4 numbered stages
(الوعي، الاهتمام، التحويل، الولاء) each with a simple line icon and one-line label,
glossy yellow + charcoal + off-white palette, generous spacing, modern Arabic-friendly
sans, readable at 800px width, 4:5 aspect."

## After generating
- **Filename**: descriptive + keyworded + `.webp` when possible
  (e.g. `meta-ads-funnel-gulf.webp`), never `image1.png`.
- **Alt text**: 5–15 words, includes a relevant keyword, describes the image
  honestly, < 125 chars. For infographics, summarize what it shows.
- **Caption** (optional but good): one line that adds context, not a repeat of alt.
- **Embed** by the returned CDN URL in the article HTML (same approach as the main
  site's images), and list each in the "Images generated" section of the deliverable
  so the user can re-upload to the WordPress media library if they prefer local files.

## Accessibility + performance
Alt text serves screen readers first, SEO second — write it to be genuinely useful.
Keep images reasonably sized; heavy media hurts INP/LCP, and Arabic pages already
run heavy on fonts.
