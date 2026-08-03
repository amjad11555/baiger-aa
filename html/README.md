# BAIGR — Standalone HTML version

Pure HTML5 + CSS3 + vanilla JavaScript port of the BAIGR homepage.
No React, no build step, no CDN dependency: GSAP, Lenis and Three.js are
vendored in `assets/vendor/`, and all four typefaces are self-hosted in
`fonts/` — the site works fully offline.

## Run

Serve the folder with any static server, e.g.:

```bash
npx serve html
# or
python3 -m http.server 8080 --directory html
```

Opening `index.html` directly from disk (file://) also works, with one
limitation: browsers block `fetch` on file://, so live language switching
falls back to the built-in English content. Any static server enables full
Arabic / English / Turkish switching.

## Structure

```
index.html            All markup (English baseline, data-i18n attributes)
css/style.css         Design tokens, base, layout, components
css/animations.css    Keyframes, reveal states, reduced-motion rules
css/responsive.css    Breakpoint upgrades (mobile-first)
js/language.js        Auto-detect + live trilingual switching (RTL for Arabic)
js/smooth-scroll.js   Lenis + GSAP ticker sync
js/parallax.js        Custom cursor + magnetic buttons
js/animations.js      Scroll reveals, SplitText headlines, hero entrance
js/three-scene.js     The particle "growth field" (ES module)
js/app.js             Navbar, mobile menu, accordion, ticker
translations/*.json   Dictionaries (ar / en / tr)
assets/vendor/        GSAP, ScrollTrigger, SplitText, Lenis, Three.js
fonts/                Self-hosted Syne, Manrope, Alexandria, IBM Plex Mono
images/, videos/      Reserved for future media
```

## Notes

- Update the WhatsApp number by searching `wa.me/905000000000` in
  `index.html`.
- Reduced-motion users get a fully static, complete page.
- The 3D scene detects software rasterizers and lowers particle count and
  frame rate automatically.
