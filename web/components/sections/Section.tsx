import { cn } from "@/lib/utils";
import type { ReactNode } from "react";

export function Container({
  children,
  className,
}: {
  children: ReactNode;
  className?: string;
}) {
  return (
    <div className={cn("mx-auto w-full max-w-6xl px-5 md:px-8", className)}>
      {children}
    </div>
  );
}

export function Section({
  id,
  children,
  className,
}: {
  id?: string;
  children: ReactNode;
  className?: string;
}) {
  return (
    <section id={id} className={cn("relative py-24 md:py-36", className)}>
      {children}
    </section>
  );
}

/** Mono label that opens every chapter of the page. */
export function Eyebrow({
  children,
  className,
}: {
  children: ReactNode;
  className?: string;
}) {
  return (
    <p
      className={cn(
        "mb-5 flex items-center gap-3 font-mono text-xs tracking-[0.22em] uppercase text-iris",
        className
      )}
    >
      <span aria-hidden className="inline-block h-px w-8 bg-lime" />
      {children}
    </p>
  );
}
