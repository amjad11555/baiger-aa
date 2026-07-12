import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Reveal from "@/components/Reveal";
import SplitHeading from "@/components/SplitHeading";
import FinalCta from "@/components/sections/FinalCta";
import { isLocale, type Locale } from "@/lib/i18n/config";
import { getDictionary } from "@/lib/i18n/dictionaries";

export async function generateMetadata({
  params,
}: {
  params: Promise<{ locale: string }>;
}): Promise<Metadata> {
  const { locale } = await params;
  if (!isLocale(locale)) return {};
  const dict = getDictionary(locale);
  return { title: dict.nav.about };
}

export default async function AboutPage({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const typedLocale = locale as Locale;
  const dict = getDictionary(typedLocale);
  const about = dict.aboutPage;

  return (
    <>
      {/* Hero */}
      <section className="dark-scene relative overflow-hidden">
        <div
          className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_-20%,rgba(215,255,47,0.08)_0%,transparent_55%)]"
          aria-hidden="true"
        />
        <div className="relative mx-auto max-w-[1280px] px-6 pb-24 pt-44 md:px-10 md:pb-32 md:pt-56">
          <SplitHeading
            as="h1"
            text={about.title}
            className="max-w-4xl text-balance text-5xl font-semibold leading-[1.05] tracking-tight md:text-7xl"
          />
          <Reveal delay={0.15}>
            <p className="mt-6 text-xl text-mist md:text-2xl">{about.subtitle}</p>
          </Reveal>
        </div>
      </section>

      {/* Mission */}
      <section className="light-scene">
        <div className="mx-auto max-w-[1280px] px-6 py-32 md:px-10 md:py-40">
          <Reveal>
            <p className="text-sm font-medium uppercase tracking-[0.25em] text-pulse">
              {about.missionLabel}
            </p>
          </Reveal>
          <SplitHeading
            text={about.missionTitle}
            className="mt-6 max-w-3xl text-balance text-4xl font-semibold leading-[1.1] tracking-tight md:text-5xl"
          />
          <Reveal delay={0.1}>
            <p className="reading mt-8 max-w-2xl text-lg leading-relaxed text-void/65 md:text-xl">
              {about.missionText}
            </p>
          </Reveal>
        </div>
      </section>

      {/* Beliefs */}
      <section className="dark-scene">
        <div className="mx-auto max-w-[1280px] px-6 py-32 md:px-10 md:py-40">
          <Reveal>
            <p className="text-sm font-medium uppercase tracking-[0.25em] text-hyper">
              {about.beliefsLabel}
            </p>
          </Reveal>
          <Reveal stagger={0.1} className="mt-14 grid gap-5 md:grid-cols-2">
            {about.beliefs.map((belief, i) => (
              <div
                key={i}
                data-cursor="card"
                className="group rounded-card border border-ivory/8 bg-void-2 p-8 transition-all duration-500 hover:border-hyper/30 md:p-10"
              >
                <span className="tabular text-sm font-medium text-mist/60 transition-colors duration-500 group-hover:text-hyper">
                  0{i + 1}
                </span>
                <h3 className="mt-4 text-2xl font-semibold leading-snug">
                  {belief.title}
                </h3>
                <p className="mt-3 text-lg leading-relaxed text-mist">
                  {belief.text}
                </p>
              </div>
            ))}
          </Reveal>
        </div>
      </section>

      {/* Markets */}
      <section className="light-scene">
        <div className="mx-auto max-w-[1280px] px-6 py-32 md:px-10 md:py-40">
          <Reveal>
            <p className="text-sm font-medium uppercase tracking-[0.25em] text-pulse">
              {about.marketsLabel}
            </p>
          </Reveal>
          <SplitHeading
            text={about.marketsTitle}
            className="mt-6 max-w-3xl text-balance text-4xl font-semibold leading-[1.1] tracking-tight md:text-5xl"
          />
          <Reveal stagger={0.05} className="mt-14 flex flex-wrap gap-3">
            {about.markets.map((market) => (
              <span
                key={market}
                className="rounded-full border border-void/15 px-5 py-2.5 text-void/75 transition-colors duration-300 hover:border-void hover:bg-void hover:text-hyper"
              >
                {market}
              </span>
            ))}
          </Reveal>
        </div>
      </section>

      <FinalCta locale={typedLocale} dict={dict} />
    </>
  );
}
