"use client";

import { useEffect, useRef } from "react";
import { EASE, ensureGsap, prefersReducedMotion } from "@/lib/motion";

interface RevealProps {
  children: React.ReactNode;
  className?: string;
  /** seconds */
  delay?: number;
  /** stagger direct children instead of animating the wrapper */
  stagger?: number;
  y?: number;
  as?: "div" | "section" | "ul" | "li" | "span" | "p";
  once?: boolean;
}

/**
 * Scroll-triggered entrance. Elements rise from 0.15 opacity —
 * nothing teleports, nothing pops from zero.
 */
export default function Reveal({
  children,
  className,
  delay = 0,
  stagger,
  y = 28,
  as: Tag = "div",
  once = true,
}: RevealProps) {
  const ref = useRef<HTMLElement | null>(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    if (prefersReducedMotion()) return;

    const { gsap } = ensureGsap();
    const targets = stagger ? Array.from(el.children) : el;

    const ctx = gsap.context(() => {
      gsap.fromTo(
        targets,
        { opacity: 0.15, y },
        {
          opacity: 1,
          y: 0,
          duration: 0.9,
          delay,
          ease: EASE.outExpo,
          stagger: stagger ?? 0,
          scrollTrigger: {
            trigger: el,
            start: "top 82%",
            toggleActions: once
              ? "play none none none"
              : "play none none reverse",
          },
        }
      );
    }, el);

    return () => ctx.revert();
  }, [delay, stagger, y, once]);

  return (
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    <Tag ref={ref as any} className={className}>
      {children}
    </Tag>
  );
}
