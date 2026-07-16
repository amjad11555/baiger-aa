"use client";

import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { Section, Container, Eyebrow } from "./Section";
import { Reveal, WordReveal } from "@/components/fx/Reveal";

/** The service index: five disciplines, eleven crafts — an editorial list, not a card grid. */
export function Services() {
  const { dict, locale } = useLanguage();
  let counter = 0;

  return (
    <Section id="services">
      <Container>
        <div className="max-w-2xl">
          <Reveal>
            <Eyebrow>{dict.services.eyebrow}</Eyebrow>
          </Reveal>
          <WordReveal
            key={locale}
            as="h2"
            text={dict.services.title}
            className="font-display text-4xl font-bold leading-[1.12] md:text-5xl"
          />
          <Reveal delay={0.1}>
            <p className="mt-6 leading-relaxed text-mist md:text-lg">
              {dict.services.lead}
            </p>
          </Reveal>
        </div>

        <div className="mt-16 space-y-14 md:mt-20">
          {dict.services.groups.map((group) => (
            <div key={group.name} className="grid gap-4 md:grid-cols-[180px_1fr] md:gap-10">
              <Reveal>
                <h3 className="font-mono text-xs tracking-[0.22em] uppercase text-iris md:sticky md:top-28">
                  {group.name}
                </h3>
              </Reveal>
              <ul>
                {group.items.map((item) => {
                  counter += 1;
                  const index = String(counter).padStart(2, "0");
                  return (
                    <Reveal
                      as="li"
                      key={item.title}
                      data-cursor
                      className="group hairline relative border-t last:border-b"
                    >
                        <div className="flex flex-col gap-2 py-6 transition-transform duration-500 ease-out group-hover:translate-x-2 md:flex-row md:items-baseline md:gap-10 rtl:group-hover:-translate-x-2">
                          <span className="font-mono text-xs text-mist">
                            {index}
                          </span>
                          <h4 className="font-display min-w-[240px] text-xl font-bold transition-colors duration-300 group-hover:text-iris md:text-2xl">
                            {item.title}
                          </h4>
                          <p className="max-w-md text-sm leading-relaxed text-mist md:ms-auto md:text-end rtl:md:text-start">
                            {item.desc}
                          </p>
                        </div>
                        <span
                          aria-hidden
                          className="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-gradient-to-r from-lime to-iris transition-transform duration-500 group-hover:scale-x-100 rtl:origin-right"
                        />
                    </Reveal>
                  );
                })}
              </ul>
            </div>
          ))}
        </div>
      </Container>
    </Section>
  );
}
