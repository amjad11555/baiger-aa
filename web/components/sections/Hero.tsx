"use client";

import dynamic from "next/dynamic";
import { useEffect, useRef, useState } from "react";
import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { site } from "@/lib/site";
import { gsap, prefersReducedMotion } from "@/lib/gsap";
import { Magnetic } from "@/components/ui/MagneticButton";
import { WordReveal } from "@/components/fx/Reveal";

const HeroField = dynamic(
  () => import("@/components/three/HeroField").then((m) => m.HeroField),
  { ssr: false }
);

/** Cycles through the three languages' word for "growth". */
function GrowthTicker({ words }: { words: string[] }) {
  const [index, setIndex] = useState(0);
  const ref = useRef<HTMLSpanElement>(null);

  useEffect(() => {
    if (prefersReducedMotion()) return;
    const id = setInterval(() => {
      const el = ref.current;
      if (!el) return;
      gsap.to(el, {
        yPercent: -120,
        opacity: 0,
        duration: 0.4,
        ease: "power2.in",
        onComplete: () => {
          setIndex((i) => (i + 1) % words.length);
          gsap.fromTo(
            el,
            { yPercent: 120, opacity: 0 },
            { yPercent: 0, opacity: 1, duration: 0.4, ease: "power2.out" }
          );
        },
      });
    }, 2600);
    return () => clearInterval(id);
  }, [words.length]);

  return (
    <span className="inline-flex h-5 items-center overflow-hidden">
      <span ref={ref} className="font-mono text-xs tracking-[0.2em] text-iris">
        {words[index]}
      </span>
    </span>
  );
}

export function Hero() {
  const { dict, locale } = useLanguage();
  const rootRef = useRef<HTMLElement>(null);
  const [fieldReady, setFieldReady] = useState(false);

  // Mount the WebGL scene during idle time so hydration stays snappy;
  // the field fades in, so the handoff is invisible.
  useEffect(() => {
    const w = window as Window & {
      requestIdleCallback?: (cb: () => void, opts?: { timeout: number }) => number;
      cancelIdleCallback?: (id: number) => void;
    };
    const show = () => setFieldReady(true);
    const idle = w.requestIdleCallback
      ? w.requestIdleCallback(show, { timeout: 1200 })
      : window.setTimeout(show, 350);
    return () => {
      if (w.cancelIdleCallback) w.cancelIdleCallback(idle);
      else window.clearTimeout(idle);
    };
  }, []);

  // Orchestrated entrance: one timeline, not scattered effects.
  useEffect(() => {
    const root = rootRef.current!;
    const targets = root.querySelectorAll("[data-hero-fade]");
    if (prefersReducedMotion()) {
      gsap.set(targets, { autoAlpha: 1 });
      return;
    }
    let cancelled = false;
    let tl: gsap.core.Timeline | null = null;
    // Wait for fonts so nothing visible shifts when they arrive (CLS = 0).
    document.fonts.ready.then(() => {
      if (cancelled) return;
      tl = gsap.timeline({ delay: 0.15 });
      tl.fromTo(
        targets,
        { autoAlpha: 0, y: 24 },
        { autoAlpha: 1, y: 0, duration: 1, stagger: 0.12, ease: "power3.out" },
        0.5
      );
    });
    return () => {
      cancelled = true;
      tl?.kill();
    };
  }, [locale]);

  return (
    <section
      ref={rootRef}
      id="top"
      className="relative flex min-h-svh items-center overflow-hidden"
    >
      {/* Living growth field — the signature */}
      {fieldReady && (
        <HeroField className="absolute inset-0 -z-10 animate-[field-in_1.6s_ease-out]" />
      )}
      <div aria-hidden className="aurora absolute inset-0 -z-20" />
      <div
        aria-hidden
        className="absolute inset-x-0 bottom-0 -z-10 h-40 bg-gradient-to-t from-paper to-transparent"
      />

      <div className="mx-auto w-full max-w-6xl px-5 pt-28 pb-24 md:px-8">
        <p
          data-hero-fade
          className="mb-6 flex items-center gap-3 font-mono text-xs tracking-[0.22em] uppercase text-iris opacity-0"
        >
          <span className="relative flex size-2">
            <span className="absolute inline-flex size-full animate-pulse-ring rounded-full bg-lime" />
            <span className="relative inline-flex size-2 rounded-full bg-lime" />
          </span>
          {dict.hero.eyebrow}
        </p>

        <h1 className="font-display hero-display max-w-5xl leading-[1.04] font-extrabold">
          <WordReveal
            key={locale}
            as="span"
            onScroll={false}
            delay={0.35}
            text={dict.hero.titlePre}
            className="inline"
          />{" "}
          <span data-hero-fade className="text-gradient-ember opacity-0">
            {dict.hero.titleAccent}
          </span>
          <span data-hero-fade className="opacity-0">{dict.hero.titlePost}</span>
        </h1>

        <p
          data-hero-fade
          className="mt-7 max-w-xl text-base leading-relaxed text-mist opacity-0 md:text-lg"
        >
          {dict.hero.sub}
        </p>

        <div data-hero-fade className="mt-10 flex flex-wrap items-center gap-4 opacity-0">
          <Magnetic>
            <a
              href={site.whatsapp}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex h-14 items-center rounded-full bg-lime px-9 text-base font-semibold text-ink transition-all duration-300 hover:-translate-y-0.5 hover:bg-iris-deep hover:text-white hover:shadow-[0_16px_38px_rgba(114,107,214,0.4)]"
            >
              {dict.hero.ctaPrimary}
            </a>
          </Magnetic>
          <Magnetic strength={0.25}>
            <a
              href="#work"
              className="glass inline-flex h-14 items-center rounded-full px-9 text-base font-medium text-ink transition-colors duration-300 hover:border-iris/50 hover:text-iris"
            >
              {dict.hero.ctaSecondary}
            </a>
          </Magnetic>
        </div>

        <div
          data-hero-fade
          className="mt-20 flex items-center justify-between opacity-0"
        >
          <GrowthTicker words={dict.hero.ticker} />
          <a
            href="#trusted"
            className="group flex items-center gap-3 font-mono text-xs tracking-[0.2em] uppercase text-mist transition-colors hover:text-ink"
          >
            {dict.hero.scroll}
            <span className="relative h-10 w-px overflow-hidden bg-ink/15">
              <span className="absolute inset-x-0 top-0 h-1/2 animate-[scroll-line_1.8s_ease-in-out_infinite] bg-lime" />
            </span>
          </a>
        </div>
      </div>
    </section>
  );
}
