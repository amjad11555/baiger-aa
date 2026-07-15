"use client";

import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { Section, Container, Eyebrow } from "./Section";
import { Reveal, WordReveal } from "@/components/fx/Reveal";

export function CaseStudies() {
  const { dict, locale } = useLanguage();

  return (
    <Section id="cases" className="aurora">
      <Container>
        <div className="max-w-2xl">
          <Reveal>
            <Eyebrow>{dict.cases.eyebrow}</Eyebrow>
          </Reveal>
          <WordReveal
            key={locale}
            as="h2"
            text={dict.cases.title}
            className="font-display text-4xl font-bold leading-[1.12] md:text-5xl"
          />
          <Reveal delay={0.1}>
            <p className="mt-6 leading-relaxed text-umber md:text-lg">
              {dict.cases.lead}
            </p>
          </Reveal>
        </div>

        <div className="mt-16 space-y-8">
          {dict.cases.items.map((item, i) => (
            <Reveal key={item.sector} delay={i * 0.05}>
              <article className="glass grid gap-8 rounded-3xl p-8 md:grid-cols-[minmax(220px,1fr)_2fr] md:p-12">
                <div>
                  <p className="font-mono text-xs tracking-[0.18em] uppercase text-umber">
                    {item.sector}
                  </p>
                  <p className="text-gradient-ember font-display mt-4 text-6xl font-extrabold md:text-7xl">
                    {item.metric}
                  </p>
                  <p className="mt-2 font-mono text-sm text-halo/80">
                    {item.metricLabel}
                  </p>
                </div>
                <dl className="space-y-6 self-center">
                  <div>
                    <dt className="mb-1.5 font-mono text-xs tracking-[0.18em] uppercase text-ember">
                      {dict.cases.labels.challenge}
                    </dt>
                    <dd className="leading-relaxed text-umber">{item.challenge}</dd>
                  </div>
                  <div>
                    <dt className="mb-1.5 font-mono text-xs tracking-[0.18em] uppercase text-ember">
                      {dict.cases.labels.play}
                    </dt>
                    <dd className="leading-relaxed text-bone/90">{item.play}</dd>
                  </div>
                </dl>
              </article>
            </Reveal>
          ))}
        </div>
      </Container>
    </Section>
  );
}
