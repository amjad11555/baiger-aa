"use client";

import { useEffect, useRef, type ReactNode } from "react";
import { gsap, SplitText, prefersReducedMotion } from "@/lib/gsap";
import { cn } from "@/lib/utils";

/** Fade-up scroll reveal for blocks. Renders as `as` so list semantics stay valid. */
export function Reveal({
  children,
  className,
  delay = 0,
  y = 28,
  as: Tag = "div",
  ...rest
}: {
  children: ReactNode;
  className?: string;
  delay?: number;
  y?: number;
  as?: "div" | "li" | "figure" | "article";
} & Record<`data-${string}`, unknown>) {
  const ref = useRef<HTMLElement>(null);

  useEffect(() => {
    const el = ref.current!;
    if (prefersReducedMotion()) {
      gsap.set(el, { autoAlpha: 1 });
      return;
    }
    const tween = gsap.fromTo(
      el,
      { autoAlpha: 0, y },
      {
        autoAlpha: 1,
        y: 0,
        duration: 1.1,
        delay,
        ease: "power3.out",
        scrollTrigger: { trigger: el, start: "top 88%", once: true },
      }
    );
    return () => {
      tween.scrollTrigger?.kill();
      tween.kill();
    };
  }, [delay, y]);

  return (
    <Tag
      // @ts-expect-error — ref type narrows per tag; safe for all listed tags
      ref={ref}
      className={cn("opacity-0 will-change-transform", className)}
      {...rest}
    >
      {children}
    </Tag>
  );
}

/**
 * Masked word-by-word reveal for headlines.
 * - Splits by words only, so Arabic ligatures stay intact.
 * - Waits for fonts to be ready: no visible font-swap shift (CLS) and the
 *   split geometry is measured against the real typeface.
 * - The animated copy is aria-hidden; a screen-reader copy carries the text.
 * Re-runs whenever `text` changes (language switch).
 */
export function WordReveal({
  text,
  className,
  as: Tag = "h2",
  delay = 0,
  onScroll = true,
}: {
  text: string;
  className?: string;
  as?: "h1" | "h2" | "h3" | "p" | "span";
  delay?: number;
  onScroll?: boolean;
}) {
  const ref = useRef<HTMLSpanElement>(null);

  useEffect(() => {
    const el = ref.current!;
    if (prefersReducedMotion()) {
      gsap.set(el, { autoAlpha: 1 });
      return;
    }

    let cancelled = false;
    let split: SplitText | null = null;
    let tween: gsap.core.Tween | null = null;
    let io: IntersectionObserver | null = null;

    const run = () => {
      if (cancelled) return;
      gsap.set(el, { autoAlpha: 1 });
      split = new SplitText(el, {
        type: "words",
        mask: "words",
        wordsClass: "wr-word",
        aria: "none",
      });
      gsap.set(split.words, { yPercent: 115, opacity: 0 });
      tween = gsap.to(split.words, {
        yPercent: 0,
        opacity: 1,
        duration: 1.05,
        delay,
        stagger: 0.06,
        ease: "power4.out",
        scrollTrigger: onScroll
          ? { trigger: el, start: "top 88%", once: true }
          : undefined,
      });
    };

    if (onScroll) {
      // Don't pay the splitting cost at load time for headings far
      // below the fold — split just before they approach the viewport.
      io = new IntersectionObserver(
        (entries) => {
          if (entries[0].isIntersecting) {
            io?.disconnect();
            document.fonts.ready.then(run);
          }
        },
        { rootMargin: "600px 0px" }
      );
      io.observe(el);
    } else {
      document.fonts.ready.then(run);
    }

    return () => {
      cancelled = true;
      io?.disconnect();
      tween?.scrollTrigger?.kill();
      tween?.kill();
      split?.revert();
    };
  }, [text, delay, onScroll]);

  return (
    <Tag className={className}>
      <span ref={ref} aria-hidden className="wr-mask inline opacity-0">
        {text}
      </span>
      <span className="sr-only">{text}</span>
    </Tag>
  );
}
