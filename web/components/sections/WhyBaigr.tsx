"use client";

import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { Section, Container, Eyebrow } from "./Section";
import { Reveal, WordReveal } from "@/components/fx/Reveal";

export function WhyBaigr() {
  const { dict, locale } = useLanguage();

  return (
    <Section id="why">
      <Container>
        <div className="max-w-2xl">
          <Reveal>
            <Eyebrow>{dict.why.eyebrow}</Eyebrow>
          </Reveal>
          <WordReveal
            key={locale}
            as="h2"
            text={dict.why.title}
            className="font-display text-4xl font-bold leading-[1.12] md:text-5xl"
          />
          <Reveal delay={0.1}>
            <p className="mt-6 leading-relaxed text-mist md:text-lg">
              {dict.why.lead}
            </p>
          </Reveal>
        </div>

        <div className="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {dict.why.reasons.map((reason, i) => (
            <Reveal key={reason.title} delay={(i % 3) * 0.08}>
              <article className="glass group h-full rounded-2xl p-7 transition-all duration-500 hover:-translate-y-1.5 hover:border-iris/40">
                <span
                  aria-hidden
                  className="mb-6 block size-2.5 rounded-full bg-gradient-to-br from-lime to-iris transition-shadow duration-500 group-hover:shadow-[0_0_18px_rgba(114,107,214,0.6)]"
                />
                <h3 className="font-display text-lg font-bold">{reason.title}</h3>
                <p className="mt-2.5 text-sm leading-relaxed text-mist">
                  {reason.desc}
                </p>
              </article>
            </Reveal>
          ))}
        </div>
      </Container>
    </Section>
  );
}
