"use client";

import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { Section, Container, Eyebrow } from "./Section";
import { Reveal, WordReveal } from "@/components/fx/Reveal";

export function About() {
  const { dict, locale } = useLanguage();

  return (
    <Section id="about" className="aurora">
      <Container>
        <div className="grid gap-14 lg:grid-cols-2 lg:gap-20">
          <div className="lg:sticky lg:top-28 lg:self-start">
            <Reveal>
              <Eyebrow>{dict.about.eyebrow}</Eyebrow>
            </Reveal>
            <WordReveal
              key={locale}
              as="h2"
              text={dict.about.title}
              className="font-display text-4xl font-bold leading-[1.12] md:text-5xl"
            />
            <Reveal delay={0.15}>
              <p className="mt-6 text-lg leading-relaxed text-halo/90">
                {dict.about.lead}
              </p>
            </Reveal>
          </div>

          <div>
            <Reveal>
              <p className="text-base leading-loose text-umber md:text-lg">
                {dict.about.body}
              </p>
            </Reveal>
            <ul className="mt-10">
              {dict.about.points.map((point, i) => (
                <Reveal as="li" key={point.title} delay={i * 0.08}>
                  <div className="hairline flex gap-5 border-t py-6">
                    <span
                      aria-hidden
                      className="mt-2 size-2 shrink-0 rounded-full bg-ember"
                    />
                    <div>
                      <h3 className="font-display text-lg font-bold">
                        {point.title}
                      </h3>
                      <p className="mt-1.5 leading-relaxed text-umber">
                        {point.desc}
                      </p>
                    </div>
                  </div>
                </Reveal>
              ))}
            </ul>
          </div>
        </div>
      </Container>
    </Section>
  );
}
