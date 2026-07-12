"use client";

import { useEffect, useRef } from "react";
import { EASE, ensureGsap, prefersReducedMotion } from "@/lib/motion";

interface SplitHeadingProps {
  text: string;
  className?: string;
  as?: "h1" | "h2" | "h3" | "p" | "span";
  /** highlight these exact words in Hyper Lime */
  highlight?: string[];
  delay?: number;
  /** play immediately instead of on scroll (hero) */
  immediate?: boolean;
}

/**
 * Word-mask heading reveal: each word rises out of its own
 * clipped container. Works in LTR and RTL.
 */
export default function SplitHeading({
  text,
  className,
  as: Tag = "h2",
  highlight = [],
  delay = 0,
  immediate = false,
}: SplitHeadingProps) {
  const ref = useRef<HTMLElement | null>(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    if (prefersReducedMotion()) return;

    const { gsap } = ensureGsap();
    const words = el.querySelectorAll<HTMLElement>(".split-word > span");

    const ctx = gsap.context(() => {
      gsap.fromTo(
        words,
        { yPercent: 110 },
        {
          yPercent: 0,
          duration: 0.9,
          delay,
          ease: EASE.outExpo,
          stagger: 0.04,
          scrollTrigger: immediate
            ? undefined
            : {
                trigger: el,
                start: "top 85%",
                toggleActions: "play none none none",
              },
        }
      );
    }, el);

    return () => ctx.revert();
  }, [delay, immediate, text]);

  const words = text.split(" ");
  const cleaned = (w: string) => w.replace(/[.,،!?؟]/g, "");

  return (
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    <Tag ref={ref as any} className={className}>
      {words.map((word, i) => (
        <span key={i}>
          <span className="split-word">
            <span
              className={
                highlight.includes(cleaned(word)) ? "text-hyper" : undefined
              }
            >
              {word}
            </span>
          </span>
          {i < words.length - 1 ? " " : null}
        </span>
      ))}
    </Tag>
  );
}
