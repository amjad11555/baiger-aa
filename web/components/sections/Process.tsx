"use client";

import { useEffect, useRef } from "react";
import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { Section, Container, Eyebrow } from "./Section";
import { Reveal, WordReveal } from "@/components/fx/Reveal";
import { gsap, prefersReducedMotion } from "@/lib/gsap";

/** Four real steps in sequence — the numbering here carries information. */
export function Process() {
  const { dict, locale } = useLanguage();
  const lineRef = useRef<HTMLDivElement>(null);

  // The connecting line draws itself as you scroll through the section.
  useEffect(() => {
    const line = lineRef.current;
    if (!line) return;
    if (prefersReducedMotion()) {
      gsap.set(line, { scaleX: 1 });
      return;
    }
    const tween = gsap.fromTo(
      line,
      { scaleX: 0 },
      {
        scaleX: 1,
        ease: "none",
        scrollTrigger: {
          trigger: line,
          start: "top 80%",
          end: "bottom 35%",
          scrub: 0.6,
        },
      }
    );
    return () => {
      tween.scrollTrigger?.kill();
      tween.kill();
    };
  }, []);

  return (
    <Section id="process">
      <Container>
        <div className="max-w-2xl">
          <Reveal>
            <Eyebrow>{dict.process.eyebrow}</Eyebrow>
          </Reveal>
          <WordReveal
            key={locale}
            as="h2"
            text={dict.process.title}
            className="font-display text-4xl font-bold leading-[1.12] md:text-5xl"
          />
          <Reveal delay={0.1}>
            <p className="mt-6 leading-relaxed text-umber md:text-lg">
              {dict.process.lead}
            </p>
          </Reveal>
        </div>

        <div className="relative mt-16">
          <div
            aria-hidden
            className="absolute inset-x-0 top-[5px] hidden h-px bg-bone/10 lg:block"
          />
          <div
            ref={lineRef}
            aria-hidden
            className="absolute inset-x-0 top-[5px] hidden h-px origin-left bg-gradient-to-r from-ember to-halo lg:block rtl:origin-right"
          />
          <ol className="grid gap-12 lg:grid-cols-4 lg:gap-8">
            {dict.process.steps.map((step, i) => (
              <Reveal as="li" key={step.title} delay={i * 0.12}>
                <div className="relative">
                  <span
                    aria-hidden
                    className="glow-ember mb-6 hidden size-[11px] rounded-full bg-ember lg:block"
                  />
                  <p className="font-mono text-xs tracking-[0.2em] text-ember">
                    {String(i + 1).padStart(2, "0")}
                  </p>
                  <h3 className="font-display mt-3 text-2xl font-bold">
                    {step.title}
                  </h3>
                  <p className="mt-3 leading-relaxed text-umber">{step.desc}</p>
                </div>
              </Reveal>
            ))}
          </ol>
        </div>
      </Container>
    </Section>
  );
}
