"use client";

import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { Section, Container, Eyebrow } from "./Section";
import { Reveal, WordReveal } from "@/components/fx/Reveal";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";

export function Faq() {
  const { dict, locale } = useLanguage();

  return (
    <Section id="faq">
      <Container className="max-w-3xl">
        <Reveal>
          <Eyebrow>{dict.faq.eyebrow}</Eyebrow>
        </Reveal>
        <WordReveal
          key={locale}
          as="h2"
          text={dict.faq.title}
          className="font-display text-4xl font-bold leading-[1.12] md:text-5xl"
        />

        <Reveal delay={0.15}>
          <Accordion type="single" collapsible className="mt-12">
            {dict.faq.items.map((item, i) => (
              <AccordionItem key={i} value={`item-${i}`}>
                <AccordionTrigger>
                  <span className="font-display text-lg font-bold md:text-xl">
                    {item.q}
                  </span>
                </AccordionTrigger>
                <AccordionContent>{item.a}</AccordionContent>
              </AccordionItem>
            ))}
          </Accordion>
        </Reveal>
      </Container>
    </Section>
  );
}
