"use client";

import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { Reveal } from "@/components/fx/Reveal";

export function TrustedBy() {
  const { dict } = useLanguage();
  const brands = [...dict.trusted.brands, ...dict.trusted.brands];

  return (
    <section id="trusted" className="relative border-y hairline py-12">
      <Reveal>
        <p className="mb-8 text-center font-mono text-xs tracking-[0.22em] uppercase text-umber">
          {dict.trusted.label}
        </p>
        <div
          aria-hidden
          className="overflow-hidden"
          style={{
            maskImage:
              "linear-gradient(to right, transparent, black 12%, black 88%, transparent)",
          }}
        >
          <div className="marquee-row flex w-max items-center gap-16 pe-16">
            {brands.map((brand, i) => (
              <span
                key={`${brand}-${i}`}
                aria-hidden={i >= dict.trusted.brands.length}
                className="font-display text-2xl font-bold tracking-widest text-bone/25 transition-colors duration-300 hover:text-bone/60"
              >
                {brand}
              </span>
            ))}
          </div>
        </div>
      </Reveal>
    </section>
  );
}
