"use client";

import { useEffect, useRef } from "react";
import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { Section, Container, Eyebrow } from "./Section";
import { Reveal, WordReveal } from "@/components/fx/Reveal";
import { gsap, prefersReducedMotion } from "@/lib/gsap";

/**
 * Generative project covers — layered light compositions in the brand
 * palette, one recipe per project. No stock images, no external requests.
 */
const coverRecipes = [
  // Radiant arc — e-commerce
  "radial-gradient(120% 90% at 85% 100%, rgba(214,224,106,0.9), transparent 55%), radial-gradient(70% 60% at 15% 10%, rgba(114,107,214,0.5), transparent 60%), linear-gradient(160deg, #fbfbf3, #eeecfa)",
  // Twin beams — brand campaign
  "conic-gradient(from 210deg at 32% 38%, rgba(114,107,214,0.55), transparent 28%, transparent 72%, rgba(214,224,106,0.7)), linear-gradient(200deg, #f3f1fb, #fdfdf6)",
  // Horizon — web build
  "linear-gradient(to top, rgba(114,107,214,0.5), transparent 48%), radial-gradient(80% 48% at 50% 100%, rgba(214,224,106,0.75), transparent 66%), linear-gradient(180deg, #fbfbf4, #efedfb)",
  // Pulse rings — leads engine
  "repeating-radial-gradient(circle at 74% 30%, rgba(114,107,214,0.16) 0 2px, transparent 2px 54px), radial-gradient(72% 70% at 74% 30%, rgba(214,224,106,0.8), transparent 62%), linear-gradient(150deg, #f4f2fb, #fbfbf4)",
];

export function Portfolio() {
  const { dict, locale } = useLanguage();
  const gridRef = useRef<HTMLDivElement>(null);

  // Image reveal: covers unmask as they enter the viewport.
  useEffect(() => {
    const covers = gridRef.current?.querySelectorAll("[data-cover]");
    if (!covers?.length) return;
    if (prefersReducedMotion()) {
      gsap.set(covers, { clipPath: "inset(0% 0% 0% 0%)" });
      return;
    }
    const tweens = Array.from(covers).map((cover) =>
      gsap.fromTo(
        cover,
        { clipPath: "inset(12% 8% 12% 8% round 16px)", scale: 1.08 },
        {
          clipPath: "inset(0% 0% 0% 0% round 16px)",
          scale: 1,
          duration: 1.3,
          ease: "power3.out",
          scrollTrigger: { trigger: cover, start: "top 85%", once: true },
        }
      )
    );
    return () => {
      tweens.forEach((t) => {
        t.scrollTrigger?.kill();
        t.kill();
      });
    };
  }, [locale]);

  return (
    <Section id="work">
      <Container>
        <div className="flex flex-wrap items-end justify-between gap-6">
          <div className="max-w-xl">
            <Reveal>
              <Eyebrow>{dict.portfolio.eyebrow}</Eyebrow>
            </Reveal>
            <WordReveal
              key={locale}
              as="h2"
              text={dict.portfolio.title}
              className="font-display text-4xl font-bold leading-[1.12] md:text-5xl"
            />
          </div>
          <Reveal>
            <p className="max-w-sm leading-relaxed text-mist">
              {dict.portfolio.lead}
            </p>
          </Reveal>
        </div>

        <div ref={gridRef} className="mt-14 grid gap-x-8 gap-y-14 md:grid-cols-2">
          {dict.portfolio.projects.map((project, i) => (
            <Reveal key={project.name} delay={(i % 2) * 0.1}>
              <article data-cursor className="group">
                <div
                  data-cover
                  className="relative aspect-[4/3] overflow-hidden rounded-2xl"
                  style={{ background: coverRecipes[i % coverRecipes.length] }}
                >
                  {/* Oversized initial as the cover's typographic mark */}
                  <span
                    aria-hidden
                    className="font-display absolute -bottom-8 -end-2 text-[10rem] font-extrabold leading-none text-ink/10 transition-transform duration-700 ease-out group-hover:-translate-y-4"
                  >
                    {project.name.charAt(0)}
                  </span>
                  <span
                    aria-hidden
                    className="glass absolute end-5 top-5 grid size-11 translate-y-2 place-items-center rounded-full opacity-0 transition-all duration-500 group-hover:translate-y-0 group-hover:opacity-100"
                  >
                    <svg
                      viewBox="0 0 16 16"
                      className="size-4 text-iris rtl:-scale-x-100"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="1.5"
                    >
                      <path d="M3 13L13 3M6 3h7v7" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  </span>
                </div>
                <div className="mt-5 flex flex-wrap items-baseline justify-between gap-2">
                  <h3 className="font-display text-2xl font-bold transition-colors duration-300 group-hover:text-iris">
                    {project.name}
                  </h3>
                  <p className="font-mono text-xs tracking-wider text-mist">
                    {project.tag}
                  </p>
                </div>
                <p className="mt-1.5 font-mono text-sm text-iris">
                  {project.result}
                </p>
              </article>
            </Reveal>
          ))}
        </div>

        <Reveal>
          <p className="mt-10 font-mono text-xs text-mist">
            {dict.portfolio.note}
          </p>
        </Reveal>
      </Container>
    </Section>
  );
}
