import type { Dictionary } from "@/lib/i18n/dictionaries";
import Reveal from "../Reveal";
import SplitHeading from "../SplitHeading";

/** Chapter 02 — The Problem. Light scene; the diagnosis. */
export default function Problem({ dict }: { dict: Dictionary }) {
  return (
    <section className="light-scene relative">
      <div className="mx-auto max-w-[1280px] px-6 py-32 md:px-10 md:py-40">
        <Reveal>
          <p className="text-sm font-medium uppercase tracking-[0.25em] text-pulse">
            {dict.problem.label}
          </p>
        </Reveal>

        <SplitHeading
          text={dict.problem.title}
          className="mt-6 max-w-3xl text-balance text-4xl font-semibold leading-[1.12] tracking-tight md:text-5xl lg:text-6xl"
        />

        <Reveal stagger={0.12} className="mt-20 grid gap-px overflow-hidden rounded-card bg-void/8 md:grid-cols-2">
          {dict.problem.points.map((point, i) => (
            <div
              key={i}
              data-cursor="card"
              className="group bg-ivory p-8 transition-colors duration-500 hover:bg-void md:p-12"
            >
              <span className="tabular text-sm font-medium text-mist transition-colors duration-500 group-hover:text-hyper">
                0{i + 1}
              </span>
              <h3 className="mt-4 text-2xl font-semibold leading-snug transition-colors duration-500 group-hover:text-ivory md:text-[26px]">
                {point.title}
              </h3>
              <p className="mt-3 text-lg leading-relaxed text-void/60 transition-colors duration-500 group-hover:text-mist">
                {point.text}
              </p>
            </div>
          ))}
        </Reveal>
      </div>
    </section>
  );
}
