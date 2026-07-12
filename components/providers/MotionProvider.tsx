"use client";

import { useEffect } from "react";
import Lenis from "lenis";
import { ensureGsap, prefersReducedMotion } from "@/lib/motion";

/**
 * Single scroll controller for the whole site:
 * Lenis drives scrolling, GSAP's ticker drives Lenis,
 * ScrollTrigger listens to Lenis. Never more than one engine.
 */
export default function MotionProvider({
  children,
}: {
  children: React.ReactNode;
}) {
  useEffect(() => {
    if (prefersReducedMotion()) {
      document.documentElement.classList.add("no-motion");
      return;
    }

    const { gsap, ScrollTrigger } = ensureGsap();

    const lenis = new Lenis({
      lerp: 0.1,
      wheelMultiplier: 1,
      touchMultiplier: 1.4,
      smoothWheel: true,
    });

    lenis.on("scroll", ScrollTrigger.update);

    const tick = (time: number) => {
      lenis.raf(time * 1000);
    };
    gsap.ticker.add(tick);
    gsap.ticker.lagSmoothing(0);

    // Re-measure triggers after fonts/images settle.
    const refresh = () => ScrollTrigger.refresh();
    window.addEventListener("load", refresh);

    return () => {
      window.removeEventListener("load", refresh);
      gsap.ticker.remove(tick);
      lenis.destroy();
    };
  }, []);

  return <>{children}</>;
}
