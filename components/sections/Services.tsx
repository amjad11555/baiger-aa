import Link from "next/link";
import type { Locale } from "@/lib/i18n/config";
import type { Dictionary } from "@/lib/i18n/dictionaries";
import Reveal from "../Reveal";
import SplitHeading from "../SplitHeading";

/** Chapter 04 — The Ecosystem. Seven capabilities orbiting one core. */
export default function Services({
  locale,
  dict,
}: {
  locale: Locale;
  dict: Dictionary;
}) {
  const groups = dict.services.groups;

  return (
    <section className="dark-scene relative overflow-hidden">
      <div className="mx-auto max-w-[1280px] px-6 py-32 md:px-10 md:py-40">
        <div className="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
          <div>
            <Reveal>
              <p className="text-sm font-medium uppercase tracking-[0.25em] text-hyper">
                {dict.services.label}
              </p>
            </Reveal>
            <SplitHeading
              text={dict.services.title}
              className="mt-6 max-w-2xl text-balance text-4xl font-semibold leading-[1.1] tracking-tight md:text-5xl lg:text-6xl"
            />
          </div>
          <Reveal delay={0.1}>
            <p className="max-w-sm text-mist leading-relaxed">
              {dict.services.subtitle}
            </p>
          </Reveal>
        </div>

        <Reveal stagger={0.08} className="mt-20 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          {groups.map((group) => (
            <Link
              key={group.id}
              href={`/${locale}/services#${group.id}`}
              data-cursor="card"
              className="group relative flex flex-col rounded-card border border-ivory/8 bg-void-2 p-8 transition-all duration-500 hover:-translate-y-1.5 hover:border-hyper/40 hover:shadow-[0_24px_64px_-32px_rgba(215,255,47,0.25)]"
            >
              <div className="flex items-baseline justify-between">
                <span className="tabular text-sm font-medium text-mist/60 transition-colors duration-500 group-hover:text-hyper">
                  {group.index}
                </span>
                <svg
                  className="h-4 w-4 text-mist/40 transition-all duration-500 group-hover:text-hyper ltr:group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1"
                  viewBox="0 0 16 16"
                  fill="none"
                  aria-hidden="true"
                >
                  <path
                    d="M2 8h11M9 3.5 13.5 8 9 12.5"
                    stroke="currentColor"
                    strokeWidth="1.5"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  />
                </svg>
              </div>

              <h3 className="mt-6 text-2xl font-semibold leading-snug">
                {group.title}
              </h3>
              <p className="mt-2 text-mist leading-relaxed">{group.tagline}</p>

              <ul className="mt-6 flex flex-wrap gap-2">
                {group.items.slice(0, 4).map((item) => (
                  <li
                    key={item}
                    className="rounded-full border border-ivory/10 px-3 py-1 text-xs text-mist transition-colors duration-500 group-hover:border-ivory/20 group-hover:text-ivory/80"
                  >
                    {item}
                  </li>
                ))}
                {group.items.length > 4 ? (
                  <li className="rounded-full border border-ivory/10 px-3 py-1 text-xs text-mist">
                    +{group.items.length - 4}
                  </li>
                ) : null}
              </ul>
            </Link>
          ))}

          {/* the eighth cell invites exploration */}
          <Link
            href={`/${locale}/services`}
            data-cursor="card"
            className="group flex min-h-[220px] flex-col items-start justify-between rounded-card border border-hyper/25 bg-hyper/[0.04] p-8 transition-all duration-500 hover:-translate-y-1.5 hover:bg-hyper hover:text-void"
          >
            <span className="text-sm font-medium uppercase tracking-[0.2em] text-hyper transition-colors duration-500 group-hover:text-void">
              BAIGR
            </span>
            <div>
              <p className="text-2xl font-semibold leading-snug">
                {dict.services.explore}
              </p>
              <svg
                className="mt-4 h-6 w-6 transition-transform duration-500 ltr:group-hover:translate-x-2 rtl:-scale-x-100 rtl:group-hover:-translate-x-2"
                viewBox="0 0 16 16"
                fill="none"
                aria-hidden="true"
              >
                <path
                  d="M2 8h11M9 3.5 13.5 8 9 12.5"
                  stroke="currentColor"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
              </svg>
            </div>
          </Link>
        </Reveal>
      </div>
    </section>
  );
}
