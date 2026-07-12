"use client";

import { useEffect, useRef } from "react";
import type { Dictionary } from "@/lib/i18n/dictionaries";
import { ensureGsap, prefersReducedMotion } from "@/lib/motion";
import Reveal from "../Reveal";
import SplitHeading from "../SplitHeading";

/** Chapter 05 — The Method. Light scene; a spine of five stages. */
export default function Process({ dict }: { dict: Dictionary }) {
  const sceneRef = useRef<HTMLElement>(null);
  const spineRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (prefersReducedMotion()) return;
    const scene = sceneRef.current;
    if (!scene) return;

    const { gsap } = ensureGsap();
    const ctx = gsap.context(() => {
      gsap.fromTo(
        spineRef.current,
        { scaleY: 0 },
        {
          scaleY: 1,
          ease: "none",
          scrollTrigger: {
            trigger: scene,
            start: "top 60%",
            end: "bottom 75%",
            scrub: true,
          },
        }
      );
    }, scene);

    return () => ctx.revert();
  }, []);

  return (
    <section ref={sceneRef} className="light-scene relative">
      <div className="mx-auto max-w-[1280px] px-6 py-32 md:px-10 md:py-40">
        <Reveal>
          <p className="text-sm font-medium uppercase tracking-[0.25em] text-pulse">
            {dict.process.label}
          </p>
        </Reveal>

        <SplitHeading
          text={dict.process.title}
          className="mt-6 max-w-3xl text-balance text-4xl font-semibold leading-[1.1] tracking-tight md:text-5xl lg:text-6xl"
        />

        <Reveal delay={0.1}>
          <p className="mt-6 max-w-xl text-lg text-void/60">
            {dict.process.subtitle}
          </p>
        </Reveal>

        <div className="relative mt-20">
          {/* growing spine */}
          <div
            className="absolute top-0 bottom-0 hidden w-px bg-void/10 md:block ltr:left-[27px] rtl:right-[27px]"
            aria-hidden="true"
          >
            <div
              ref={spineRef}
              className="h-full w-full origin-top bg-gradient-to-b from-pulse to-hyper"
            />
          </div>

          <ol className="space-y-4 md:space-y-0">
            {dict.process.steps.map((step, i) => (
              <Reveal
                as="li"
                key={step.index}
                delay={i * 0.04}
                className="group relative md:grid md:grid-cols-[56px_180px_1fr] md:items-baseline md:gap-10 md:py-10 md:border-b md:border-void/8 last:md:border-0"
              >
                <span className="relative z-10 flex h-14 w-14 items-center justify-center rounded-full border border-void/12 bg-ivory text-sm font-semibold tabular transition-colors duration-500 group-hover:border-void group-hover:bg-void group-hover:text-hyper">
                  {step.index}
                </span>
                <h3 className="mt-4 text-2xl font-semibold md:mt-0 md:text-[26px]">
                  {step.title}
                </h3>
                <p className="mt-2 max-w-xl text-lg leading-relaxed text-void/60 md:mt-0 pb-10 md:pb-0">
                  {step.description}
                </p>
              </Reveal>
            ))}
          </ol>
        </div>
      </div>
    </section>
  );
}
