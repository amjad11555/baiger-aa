"use client";

import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { Section, Container, Eyebrow } from "./Section";
import { Reveal, WordReveal } from "@/components/fx/Reveal";

export function Testimonials() {
  const { dict, locale } = useLanguage();

  return (
    <Section id="testimonials" className="aurora">
      <Container>
        <div className="max-w-2xl">
          <Reveal>
            <Eyebrow>{dict.testimonials.eyebrow}</Eyebrow>
          </Reveal>
          <WordReveal
            key={locale}
            as="h2"
            text={dict.testimonials.title}
            className="font-display text-4xl font-bold leading-[1.12] md:text-5xl"
          />
        </div>

        <div className="mt-14 grid gap-5 lg:grid-cols-3">
          {dict.testimonials.quotes.map((item, i) => (
            <Reveal key={item.name} delay={i * 0.1}>
              <figure className="glass flex h-full flex-col rounded-2xl p-8 transition-all duration-500 hover:-translate-y-1.5 hover:border-ember/40">
                <span
                  aria-hidden
                  className="font-display text-5xl leading-none text-ember"
                >
                  &ldquo;
                </span>
                <blockquote className="mt-4 flex-1 leading-relaxed text-bone/90">
                  {item.quote}
                </blockquote>
                <figcaption className="mt-8 border-t hairline pt-5">
                  <p className="font-display font-bold">{item.name}</p>
                  <p className="mt-0.5 text-sm text-umber">{item.role}</p>
                </figcaption>
              </figure>
            </Reveal>
          ))}
        </div>
      </Container>
    </Section>
  );
}
