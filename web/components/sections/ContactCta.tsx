"use client";

import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { site } from "@/lib/site";
import { Section, Container, Eyebrow } from "./Section";
import { Reveal, WordReveal } from "@/components/fx/Reveal";
import { Magnetic } from "@/components/ui/MagneticButton";

export function ContactCta() {
  const { dict, locale } = useLanguage();

  return (
    <Section id="contact" className="overflow-hidden">
      {/* Ember horizon behind the closing statement */}
      <div
        aria-hidden
        className="absolute inset-x-0 bottom-0 -z-10 h-[420px]"
        style={{
          background:
            "radial-gradient(60% 90% at 50% 100%, rgba(255,122,41,0.22), transparent 70%)",
        }}
      />
      <Container className="flex flex-col items-center py-10 text-center md:py-20">
        <Reveal>
          <Eyebrow className="justify-center">{dict.contact.eyebrow}</Eyebrow>
        </Reveal>
        <h2 className="font-display max-w-4xl text-[clamp(2.4rem,6.5vw,5.5rem)] font-extrabold leading-[1.06]">
          <WordReveal
            key={locale}
            as="span"
            text={dict.contact.titlePre}
            className="inline"
          />{" "}
          <span className="text-gradient-ember">{dict.contact.titleAccent}</span>
        </h2>
        <Reveal delay={0.15}>
          <p className="mt-7 max-w-xl leading-relaxed text-umber md:text-lg">
            {dict.contact.sub}
          </p>
        </Reveal>
        <Reveal delay={0.25}>
          <div className="mt-11 flex flex-col items-center gap-5">
            <Magnetic strength={0.4}>
              <a
                href={site.whatsapp}
                target="_blank"
                rel="noopener noreferrer"
                className="glow-ember inline-flex h-16 items-center gap-3 rounded-full bg-ember px-11 text-lg font-semibold text-obsidian transition-all duration-300 hover:bg-halo"
              >
                <WhatsAppIcon className="size-5" />
                {dict.contact.cta}
              </a>
            </Magnetic>
            <p className="text-sm text-umber">
              {dict.contact.or}{" "}
              <a
                href={`mailto:${site.email}`}
                className="text-bone underline decoration-ember/60 underline-offset-4 transition-colors hover:text-halo"
              >
                {dict.contact.emailLabel}
              </a>
            </p>
          </div>
        </Reveal>
      </Container>
    </Section>
  );
}

export function WhatsAppIcon({ className }: { className?: string }) {
  return (
    <svg viewBox="0 0 24 24" fill="currentColor" className={className} aria-hidden>
      <path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.14-1.26-.46-2.4-1.47-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.44-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.66-1.6-.91-2.19-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.5 0 1.47 1.07 2.89 1.22 3.09.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.7.63.71.22 1.36.19 1.87.11.57-.08 1.76-.72 2-1.41.25-.7.25-1.29.18-1.41-.07-.13-.27-.2-.57-.35zM12.05 21.79h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 0 1-1.51-5.26c0-5.44 4.43-9.87 9.89-9.87a9.82 9.82 0 0 1 6.98 2.9 9.82 9.82 0 0 1 2.89 6.99c0 5.44-4.43 9.87-9.87 9.87zm8.4-18.28A11.8 11.8 0 0 0 12.04 0C5.46 0 .1 5.35.1 11.93c0 2.1.55 4.15 1.6 5.96L0 24l6.25-1.64a11.93 11.93 0 0 0 5.8 1.48c6.58 0 11.94-5.36 11.94-11.94 0-3.19-1.24-6.19-3.54-8.39z" />
    </svg>
  );
}
