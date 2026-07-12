"use client";

import { useEffect, useRef } from "react";

/**
 * Magnetic hover: the child gently follows the mouse
 * (max ~12px) and eases back on leave. Desktop only.
 */
export default function Magnetic({
  children,
  strength = 0.25,
  className,
}: {
  children: React.ReactNode;
  strength?: number;
  className?: string;
}) {
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    if (!window.matchMedia("(pointer: fine)").matches) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    let raf = 0;
    let tx = 0;
    let ty = 0;
    let cx = 0;
    let cy = 0;
    let running = false;

    const loop = () => {
      cx += (tx - cx) * 0.16;
      cy += (ty - cy) * 0.16;
      el.style.transform = `translate(${cx}px, ${cy}px)`;
      if (Math.abs(tx - cx) > 0.1 || Math.abs(ty - cy) > 0.1 || tx || ty) {
        raf = requestAnimationFrame(loop);
      } else {
        el.style.transform = "";
        running = false;
      }
    };

    const start = () => {
      if (!running) {
        running = true;
        raf = requestAnimationFrame(loop);
      }
    };

    const onMove = (e: MouseEvent) => {
      const rect = el.getBoundingClientRect();
      const dx = e.clientX - (rect.left + rect.width / 2);
      const dy = e.clientY - (rect.top + rect.height / 2);
      tx = Math.max(-12, Math.min(12, dx * strength));
      ty = Math.max(-12, Math.min(12, dy * strength));
      start();
    };

    const onLeave = () => {
      tx = 0;
      ty = 0;
      start();
    };

    el.addEventListener("mousemove", onMove);
    el.addEventListener("mouseleave", onLeave);

    return () => {
      cancelAnimationFrame(raf);
      el.removeEventListener("mousemove", onMove);
      el.removeEventListener("mouseleave", onLeave);
    };
  }, [strength]);

  return (
    <div ref={ref} className={`inline-block will-change-transform ${className ?? ""}`}>
      {children}
    </div>
  );
}
