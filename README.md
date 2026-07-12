# BAIGR Growth Agency — Website

The digital home of **BAIGR**, a creative growth agency for the Middle East.
Built as a cinematic, chapter-based experience rather than a traditional
agency site.

## Stack

- **Next.js 16** (App Router, TypeScript)
- **Tailwind CSS v4** — BAIGR design tokens (Hyper Lime `#D7FF2F`, Void Black
  `#0B0D10`, Soft Ivory `#F6F4EE`, Graphite Mist `#A7ADB5`, Digital Purple
  `#725CFF`)
- **GSAP + ScrollTrigger** — scroll storytelling engine
- **Lenis** — single smooth-scroll controller, synced to the GSAP ticker
- **Canvas 2D "Digital Core"** — BAIGR's signature signal visual
  (orbits, pulses, particle field) with adaptive density and
  reduced-motion / offscreen fallbacks

## Languages

Arabic (RTL), English and Turkish. The proxy detects the browser language on
first visit, redirects to `/ar`, `/en` or `/tr`, and remembers manual
switches in a cookie.

## Structure

```
app/[locale]/          # localized routes: home, services, about, contact
components/            # header, footer, cursor, loader, reveal system
components/sections/   # homepage chapters (Arrival → Action)
components/DigitalCore.tsx  # canvas signal engine
lib/i18n/              # locale config + AR/EN/TR dictionaries
proxy.ts               # locale detection & redirect
public/baigr-profile-logo.svg  # 1:1 Instagram profile logo
```

## Development

```bash
npm install
npm run dev
```

## Homepage chapters

01 Arrival · 02 Problem · 03 Transformation · 04 Services · 05 Process ·
06 Trust · 07 Action — every CTA leads to WhatsApp (+90 537 857 31 81).
