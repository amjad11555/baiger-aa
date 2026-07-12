import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Magnetic from "@/components/Magnetic";
import Reveal from "@/components/Reveal";
import SplitHeading from "@/components/SplitHeading";
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
  return { title: dict.nav.contact };
}

export default async function ContactPage({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const typedLocale = locale as Locale;
  const dict = getDictionary(typedLocale);
  const contact = dict.contactPage;

  return (
    <>
      <section className="dark-scene relative flex min-h-[100svh] items-center overflow-hidden">
        <div
          className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_100%,rgba(215,255,47,0.07)_0%,transparent_55%)]"
          aria-hidden="true"
        />

        <div className="relative mx-auto w-full max-w-[1280px] px-6 pb-24 pt-40 md:px-10 md:pt-48">
          <SplitHeading
            as="h1"
            text={contact.title}
            className="max-w-4xl text-balance text-4xl font-semibold leading-[1.08] tracking-tight md:text-6xl lg:text-7xl"
          />
          <Reveal delay={0.15}>
            <p className="reading mt-8 max-w-2xl text-lg leading-relaxed text-mist md:text-xl">
              {contact.subtitle}
            </p>
          </Reveal>

          <Reveal stagger={0.12} className="mt-16 grid gap-5 md:grid-cols-2">
            {/* WhatsApp panel */}
            <div className="group rounded-panel border border-hyper/25 bg-hyper/[0.05] p-8 transition-colors duration-500 hover:bg-hyper/[0.09] md:p-12">
              <h2 className="text-2xl font-semibold text-ivory">
                {contact.whatsappTitle}
              </h2>
              <p className="mt-3 text-lg leading-relaxed text-mist">
                {contact.whatsappText}
              </p>
              <p dir="ltr" className="tabular mt-6 text-xl font-medium text-hyper">
                +90 537 857 31 81
              </p>
              <Magnetic className="mt-8">
                <a
                  href={whatsappHref(contact.whatsappCta)}
                  target="_blank"
                  rel="noopener noreferrer"
                  data-cursor="button"
                  className="btn btn-primary"
                >
                  {contact.whatsappCta}
                </a>
              </Magnetic>
            </div>

            {/* Email panel */}
            <div className="rounded-panel border border-ivory/10 bg-void-2 p-8 md:p-12">
              <h2 className="text-2xl font-semibold text-ivory">
                {contact.emailTitle}
              </h2>
              <p className="mt-3 text-lg leading-relaxed text-mist">
                {contact.emailText}
              </p>
              <a
                href="mailto:baigr.agency@gmail.com"
                data-cursor="link"
                className="mt-6 inline-block text-xl font-medium text-ivory transition-colors duration-300 hover:text-hyper"
              >
                baigr.agency@gmail.com
              </a>
              <p className="mt-8 text-sm leading-relaxed text-mist/70">
                {contact.responseNote}
              </p>
            </div>
          </Reveal>
        </div>
      </section>
    </>
  );
}
