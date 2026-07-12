"use client";

import { useEffect, useRef } from "react";
import Link from "next/link";
import type { Locale } from "@/lib/i18n/config";
import { whatsappHref } from "@/lib/i18n/config";
import type { Dictionary } from "@/lib/i18n/dictionaries";
import { EASE, ensureGsap, prefersReducedMotion } from "@/lib/motion";
import DigitalCore from "../DigitalCore";
import Magnetic from "../Magnetic";
import SplitHeading from "../SplitHeading";

interface HeroProps {
  locale: Locale;
  dict: Dictionary;
}

/** Chapter 01 — Arrival. The visitor enters the BAIGR universe. */
export default function Hero({ locale, dict }: HeroProps) {
  const sceneRef = useRef<HTMLElement>(null);
  const contentRef = useRef<HTMLDivElement>(null);
  const coreRef = useRef<HTMLDivElement>(null);
  const cueRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (prefersReducedMotion()) return;
    const scene = sceneRef.current;
    if (!scene) return;

    const { gsap } = ensureGsap();

    const ctx = gsap.context(() => {
      // entrance: badge → subtitle → CTAs (headline handled by SplitHeading)
      gsap.fromTo(
        "[data-hero-fade]",
        { opacity: 0.15, y: 24 },
        {
          opacity: 1,
          y: 0,
          duration: 1,
          ease: EASE.outExpo,
          stagger: 0.12,
          delay: 1.15,
        }
      );

      // scroll: content drifts up and softens, core recedes — camera moves on
      gsap.to(contentRef.current, {
        y: -90,
        opacity: 0.25,
        ease: "none",
        scrollTrigger: {
          trigger: scene,
          start: "top top",
          end: "bottom 20%",
          scrub: true,
        },
      });

      gsap.to(coreRef.current, {
        scale: 1.08,
        opacity: 0.35,
        ease: "none",
        scrollTrigger: {
          trigger: scene,
          start: "top top",
          end: "bottom top",
          scrub: true,
        },
      });

      gsap.to(cueRef.current, {
        opacity: 0,
        ease: "none",
        scrollTrigger: {
          trigger: scene,
          start: "top top",
          end: "12% top",
          scrub: true,
        },
      });
    }, scene);

    return () => ctx.revert();
  }, []);

  return (
    <section
      ref={sceneRef}
      className="dark-scene relative flex min-h-[100svh] items-center overflow-hidden"
    >
      {/* Digital Core environment */}
      <div ref={coreRef} className="absolute inset-0 will-change-transform">
        <DigitalCore className="h-full w-full" />
        {/* readability veil */}
        <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(11,13,16,0.25)_0%,rgba(11,13,16,0.7)_78%)]" />
      </div>

      <div
        ref={contentRef}
        className="relative z-10 mx-auto w-full max-w-[1280px] px-6 md:px-10"
      >
        <div className="mx-auto flex max-w-4xl flex-col items-center text-center">
          <p
            data-hero-fade
            className="mb-8 inline-flex items-center gap-2 rounded-full border border-ivory/12 px-4 py-2 text-sm text-mist"
          >
            <span className="inline-block h-1.5 w-1.5 rounded-full bg-hyper" />
            {dict.hero.badge}
          </p>

          <h1 className="text-balance text-[44px] font-semibold leading-[1.05] tracking-tight sm:text-6xl md:text-7xl lg:text-[88px]">
            <SplitHeading
              as="span"
              text={dict.hero.titleA}
              immediate
              delay={0.85}
              className="block"
            />
            <SplitHeading
              as="span"
              text={dict.hero.titleB}
              immediate
              delay={1.05}
              highlight={dict.hero.titleB.split(" ").map((w) =>
                w.replace(/[.,،!?؟]/g, "")
              )}
              className="block"
            />
          </h1>

          <p
            data-hero-fade
            className="reading mt-8 max-w-2xl text-lg leading-relaxed text-mist md:text-xl"
          >
            {dict.hero.subtitle}
          </p>

          <div
            data-hero-fade
            className="mt-12 flex flex-col items-center gap-4 sm:flex-row"
          >
            <Magnetic>
              <a
                href={whatsappHref(dict.hero.ctaPrimary)}
                target="_blank"
                rel="noopener noreferrer"
                data-cursor="button"
                className="btn btn-primary"
              >
                {dict.hero.ctaPrimary}
                <svg
                  className="btn-arrow h-4 w-4"
                  viewBox="0 0 16 16"
                  fill="none"
                  aria-hidden="true"
                >
                  <path
                    d="M2 8h11M9 3.5 13.5 8 9 12.5"
                    stroke="currentColor"
                    strokeWidth="1.6"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  />
                </svg>
              </a>
            </Magnetic>
            <Link
              href={`/${locale}/services`}
              data-cursor="button"
              className="btn btn-secondary"
            >
              {dict.hero.ctaSecondary}
            </Link>
          </div>
        </div>
      </div>

      {/* scroll cue */}
      <div
        ref={cueRef}
        className="absolute bottom-8 left-1/2 z-10 -translate-x-1/2"
        aria-hidden="true"
      >
        <div className="flex flex-col items-center gap-3 text-mist/70">
          <span className="text-xs tracking-[0.25em] uppercase">
            {dict.hero.scrollCue}
          </span>
          <span className="flex h-10 w-6 items-start justify-center rounded-full border border-ivory/15 p-1.5">
            <span className="h-1.5 w-1 rounded-full bg-hyper animate-scroll-cue" />
          </span>
        </div>
      </div>
    </section>
  );
}
