import type { Dictionary } from "@/lib/i18n/dictionaries";
import { LogoMark } from "../Logo";
import Reveal from "../Reveal";
import SplitHeading from "../SplitHeading";

/** Chapter 06 — Trust. Dark scene; how BAIGR behaves. */
export default function Trust({ dict }: { dict: Dictionary }) {
  return (
    <section className="dark-scene relative overflow-hidden">
      {/* quiet signal watermark */}
      <LogoMark
        className="pointer-events-none absolute -top-16 h-[420px] w-[420px] text-ivory/[0.03] ltr:-right-24 rtl:-left-24 rtl:-scale-x-100"
      />

      <div className="relative mx-auto max-w-[1280px] px-6 py-32 md:px-10 md:py-40">
        <Reveal>
          <p className="text-sm font-medium uppercase tracking-[0.25em] text-hyper">
            {dict.trust.label}
          </p>
        </Reveal>

        <SplitHeading
          text={dict.trust.title}
          className="mt-6 max-w-3xl text-balance text-4xl font-semibold leading-[1.1] tracking-tight md:text-5xl lg:text-6xl"
        />

        <Reveal stagger={0.1} className="mt-20 grid gap-5 md:grid-cols-2">
          {dict.trust.points.map((point, i) => (
            <div
              key={i}
              data-cursor="card"
              className="group rounded-card border border-ivory/8 bg-void-2 p-8 transition-all duration-500 hover:border-hyper/30 md:p-10"
            >
              <span className="inline-flex h-10 w-10 items-center justify-center rounded-full border border-ivory/10 transition-colors duration-500 group-hover:border-hyper/40">
                <span className="h-1.5 w-1.5 rounded-full bg-hyper" />
              </span>
              <h3 className="mt-6 text-2xl font-semibold leading-snug">
                {point.title}
              </h3>
              <p className="mt-3 text-lg leading-relaxed text-mist">
                {point.text}
              </p>
            </div>
          ))}
        </Reveal>
      </div>
    </section>
  );
}
