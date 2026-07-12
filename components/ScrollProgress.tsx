"use client";

import { useEffect, useRef } from "react";

/** Thin lime progress line pinned to the top of the viewport. */
export default function ScrollProgress() {
  const barRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const bar = barRef.current;
    if (!bar) return;

    let target = 0;
    let current = 0;
    let raf = 0;

    const measure = () => {
      const max = document.documentElement.scrollHeight - window.innerHeight;
      target = max > 0 ? window.scrollY / max : 0;
    };

    const loop = () => {
      current += (target - current) * 0.15;
      bar.style.transform = `scaleX(${current})`;
      raf = requestAnimationFrame(loop);
    };

    measure();
    raf = requestAnimationFrame(loop);
    window.addEventListener("scroll", measure, { passive: true });
    window.addEventListener("resize", measure);

    return () => {
      cancelAnimationFrame(raf);
      window.removeEventListener("scroll", measure);
      window.removeEventListener("resize", measure);
    };
  }, []);

  return (
    <div
      className="fixed inset-x-0 top-0 z-[90] h-[3px] pointer-events-none"
      aria-hidden="true"
    >
      <div
        ref={barRef}
        className="h-full w-full origin-left rtl:origin-right bg-hyper"
        style={{ transform: "scaleX(0)" }}
      />
    </div>
  );
}
