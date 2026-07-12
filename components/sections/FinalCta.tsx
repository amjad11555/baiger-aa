import Link from "next/link";
import type { Locale } from "@/lib/i18n/config";
import { whatsappHref } from "@/lib/i18n/config";
import type { Dictionary } from "@/lib/i18n/dictionaries";
import Magnetic from "../Magnetic";
import Reveal from "../Reveal";
import SplitHeading from "../SplitHeading";

/**
 * Chapter 07 — Action. The environment simplifies:
 * one signal, one message, one button. Growth starts
 * with one conversation.
 */
export default function FinalCta({
  locale,
  dict,
}: {
  locale: Locale;
  dict: Dictionary;
}) {
  return (
    <section className="dark-scene relative overflow-hidden">
      {/* single remaining signal */}
      <div
        className="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
        aria-hidden="true"
      >
        <span className="block h-[520px] w-[520px] rounded-full border border-hyper/8" />
        <span className="absolute inset-0 m-auto block h-[340px] w-[340px] rounded-full border border-hyper/12" />
        <span className="absolute inset-0 m-auto block h-[180px] w-[180px] rounded-full border border-hyper/16 animate-[pulse-ring_4s_ease-out_infinite]" />
      </div>

      <div className="relative mx-auto max-w-[1280px] px-6 py-40 text-center md:px-10 md:py-56">
        <SplitHeading
          text={dict.cta.title}
          className="mx-auto max-w-3xl text-balance text-4xl font-semibold leading-[1.1] tracking-tight md:text-6xl"
        />

        <Reveal delay={0.1}>
          <p className="reading mx-auto mt-8 max-w-xl text-lg leading-relaxed text-mist md:text-xl">
            {dict.cta.text}
          </p>
        </Reveal>

        <Reveal delay={0.2}>
          <div className="mt-12 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <Magnetic>
              <a
                href={whatsappHref(dict.cta.whatsapp)}
                target="_blank"
                rel="noopener noreferrer"
                data-cursor="button"
                className="btn btn-primary !px-10 !py-5 !text-lg"
              >
                <svg
                  className="h-5 w-5"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path d="M12.04 2a9.9 9.9 0 0 0-8.5 14.9L2 22l5.25-1.5A9.9 9.9 0 1 0 12.04 2Zm5.8 14.1c-.25.7-1.45 1.35-2 1.4-.55.1-1.2.15-1.95-.1-.45-.15-1.03-.34-1.77-.66-3.12-1.35-5.15-4.5-5.3-4.7-.15-.2-1.27-1.69-1.27-3.22 0-1.53.8-2.28 1.1-2.6.28-.3.62-.37.83-.37h.6c.2 0 .45-.04.7.53.25.62.85 2.08.93 2.23.07.15.12.33.02.53-.1.2-.15.32-.3.5l-.45.53c-.15.15-.3.31-.13.61.17.3.76 1.26 1.63 2.04 1.13 1 2.07 1.32 2.37 1.47.3.15.47.12.65-.08.17-.2.75-.87.95-1.17.2-.3.4-.25.67-.15.28.1 1.75.83 2.05.98.3.15.5.22.57.34.08.13.08.72-.18 1.42Z" />
                </svg>
                {dict.cta.whatsapp}
              </a>
            </Magnetic>
            <Link
              href={`/${locale}/services`}
              data-cursor="button"
              className="btn btn-secondary"
            >
              {dict.cta.secondary}
            </Link>
          </div>
        </Reveal>

        <Reveal delay={0.3}>
          <p dir="ltr" className="mt-10 text-sm tracking-wide text-mist/60 tabular">
            WhatsApp&nbsp;&nbsp;+90 537 857 31 81
          </p>
        </Reveal>
      </div>
    </section>
  );
}
