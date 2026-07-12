"use client";

import { useEffect, useRef } from "react";
import type { Dictionary } from "@/lib/i18n/dictionaries";
import { ensureGsap, prefersReducedMotion } from "@/lib/motion";
import Reveal from "../Reveal";
import SplitHeading from "../SplitHeading";

/** Chapter 03 — The Transformation. Dark scene; the thesis. */
export default function Shift({ dict }: { dict: Dictionary }) {
  const sceneRef = useRef<HTMLElement>(null);
  const lineRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (prefersReducedMotion()) return;
    const scene = sceneRef.current;
    if (!scene) return;

    const { gsap } = ensureGsap();
    const ctx = gsap.context(() => {
      // a signal line draws itself across the scene while scrolling
      gsap.fromTo(
        lineRef.current,
        { scaleX: 0 },
        {
          scaleX: 1,
          ease: "none",
          scrollTrigger: {
            trigger: scene,
            start: "top 70%",
            end: "center center",
            scrub: true,
          },
        }
      );
    }, scene);

    return () => ctx.revert();
  }, []);

  return (
    <section ref={sceneRef} className="dark-scene relative overflow-hidden">
      {/* faint radial atmosphere */}
      <div
        className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_0%,rgba(215,255,47,0.05)_0%,transparent_55%)]"
        aria-hidden="true"
      />

      <div className="relative mx-auto max-w-[1280px] px-6 py-32 md:px-10 md:py-44">
        <Reveal>
          <p className="text-sm font-medium uppercase tracking-[0.25em] text-hyper">
            {dict.shift.label}
          </p>
        </Reveal>

        <SplitHeading
          text={dict.shift.title}
          className="mt-6 max-w-4xl text-balance text-4xl font-semibold leading-[1.1] tracking-tight md:text-6xl lg:text-7xl"
        />

        <Reveal delay={0.15}>
          <p className="reading mt-10 max-w-2xl text-lg leading-relaxed text-mist md:text-xl">
            {dict.shift.text}
          </p>
        </Reveal>

        <div
          ref={lineRef}
          className="mt-20 h-px origin-left rtl:origin-right bg-gradient-to-r from-hyper via-hyper/40 to-transparent"
          aria-hidden="true"
        />

        <Reveal stagger={0.08} className="mt-12 flex flex-wrap gap-x-12 gap-y-6">
          {dict.shift.pillars.map((pillar, i) => (
            <div key={i} className="flex items-center gap-3">
              <span className="h-1.5 w-1.5 rounded-full bg-hyper" />
              <span className="text-lg font-medium text-ivory/90 md:text-xl">
                {pillar}
              </span>
            </div>
          ))}
        </Reveal>
      </div>
    </section>
  );
}
