import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Magnetic from "@/components/Magnetic";
import Reveal from "@/components/Reveal";
import SplitHeading from "@/components/SplitHeading";
import FinalCta from "@/components/sections/FinalCta";
import { isLocale, whatsappHref, type Locale } from "@/lib/i18n/config";
import { getDictionary } from "@/lib/i18n/dictionaries";

export async function generateMetadata({
  params,
}: {
  params: Promise<{ locale: string }>;
}): Promise<Metadata> {
  const { locale } = await params;
  if (!isLocale(locale)) return {};
  const dict = getDictionary(locale);
  return { title: dict.nav.services };
}

export default async function ServicesPage({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const typedLocale = locale as Locale;
  const dict = getDictionary(typedLocale);

  return (
    <>
      {/* Hero */}
      <section className="dark-scene relative overflow-hidden">
        <div
          className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_-20%,rgba(114,92,255,0.12)_0%,transparent_55%)]"
          aria-hidden="true"
        />
        <div className="relative mx-auto max-w-[1280px] px-6 pb-24 pt-44 md:px-10 md:pb-32 md:pt-56">
          <Reveal>
            <p className="text-sm font-medium uppercase tracking-[0.25em] text-hyper">
              {dict.services.label}
            </p>
          </Reveal>
          <SplitHeading
            as="h1"
            text={dict.servicesPage.title}
            className="mt-6 max-w-4xl text-balance text-4xl font-semibold leading-[1.08] tracking-tight md:text-6xl lg:text-7xl"
          />
          <Reveal delay={0.15}>
            <p className="reading mt-8 max-w-2xl text-lg leading-relaxed text-mist md:text-xl">
              {dict.servicesPage.subtitle}
            </p>
          </Reveal>
        </div>
      </section>

      {/* Service chapters — alternating scenes */}
      {dict.services.groups.map((group, i) => {
        const light = i % 2 === 0;
        return (
          <section
            key={group.id}
            id={group.id}
            className={`${light ? "light-scene" : "dark-scene"} scroll-mt-24`}
          >
            <div className="mx-auto max-w-[1280px] px-6 py-24 md:px-10 md:py-32">
              <div className="grid gap-10 md:grid-cols-[180px_1fr] lg:grid-cols-[220px_1fr]">
                <Reveal>
                  <span
                    className={`tabular text-6xl font-light leading-none md:text-7xl ${
                      light ? "text-void/15" : "text-ivory/15"
                    }`}
                  >
                    {group.index}
                  </span>
                </Reveal>

                <div>
                  <SplitHeading
                    text={group.title}
                    className="text-balance text-3xl font-semibold leading-[1.12] tracking-tight md:text-5xl"
                  />
                  <Reveal delay={0.08}>
                    <p
                      className={`mt-4 text-xl font-medium ${
                        light ? "text-pulse" : "text-hyper"
                      }`}
                    >
                      {group.tagline}
                    </p>
                  </Reveal>
                  <Reveal delay={0.12}>
                    <p
                      className={`reading mt-6 max-w-2xl text-lg leading-relaxed ${
                        light ? "text-void/65" : "text-mist"
                      }`}
                    >
                      {group.description}
                    </p>
                  </Reveal>

                  <Reveal stagger={0.05} className="mt-10 flex flex-wrap gap-3">
                    {group.items.map((item) => (
                      <span
                        key={item}
                        className={`rounded-full border px-4 py-2 text-sm transition-colors duration-300 ${
                          light
                            ? "border-void/15 text-void/75 hover:border-void hover:bg-void hover:text-hyper"
                            : "border-ivory/15 text-ivory/80 hover:border-hyper hover:text-hyper"
                        }`}
                      >
                        {item}
                      </span>
                    ))}
                  </Reveal>
                </div>
              </div>
            </div>
          </section>
        );
      })}

      {/* Bridge to conversation */}
      <section className="dark-scene border-t border-ivory/5">
        <div className="mx-auto flex max-w-[1280px] flex-col items-center gap-8 px-6 py-24 text-center md:px-10">
          <Reveal>
            <p className="max-w-xl text-2xl font-medium leading-snug text-ivory md:text-3xl">
              {dict.servicesPage.ctaLine}
            </p>
          </Reveal>
          <Reveal delay={0.1}>
            <Magnetic>
              <a
                href={whatsappHref(dict.cta.whatsapp)}
                target="_blank"
                rel="noopener noreferrer"
                data-cursor="button"
                className="btn btn-primary"
              >
                {dict.cta.whatsapp}
              </a>
            </Magnetic>
          </Reveal>
        </div>
      </section>

      <FinalCta locale={typedLocale} dict={dict} />
    </>
  );
}
