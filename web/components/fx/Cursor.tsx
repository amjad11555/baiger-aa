"use client";

import { useEffect, useRef } from "react";
import { gsap, prefersReducedMotion } from "@/lib/gsap";

/** Custom cursor: an ember dot with a trailing ring. Desktop pointers only. */
export function Cursor() {
  const dotRef = useRef<HTMLDivElement>(null);
  const ringRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const finePointer = window.matchMedia("(pointer: fine)").matches;
    if (!finePointer || prefersReducedMotion()) return;

    const dot = dotRef.current!;
    const ring = ringRef.current!;
    document.body.dataset.customCursor = "on";

    const dotX = gsap.quickTo(dot, "x", { duration: 0.12, ease: "power2.out" });
    const dotY = gsap.quickTo(dot, "y", { duration: 0.12, ease: "power2.out" });
    const ringX = gsap.quickTo(ring, "x", { duration: 0.45, ease: "power3.out" });
    const ringY = gsap.quickTo(ring, "y", { duration: 0.45, ease: "power3.out" });

    let visible = false;

    const onMove = (e: PointerEvent) => {
      if (!visible) {
        visible = true;
        gsap.to([dot, ring], { autoAlpha: 1, duration: 0.3 });
      }
      dotX(e.clientX);
      dotY(e.clientY);
      ringX(e.clientX);
      ringY(e.clientY);
    };

    const onOver = (e: PointerEvent) => {
      const interactive = (e.target as Element).closest(
        "a, button, [data-cursor]"
      );
      gsap.to(ring, {
        scale: interactive ? 2.2 : 1,
        opacity: interactive ? 0.9 : 0.5,
        duration: 0.35,
        ease: "power3.out",
      });
    };

    const onLeave = () => {
      visible = false;
      gsap.to([dot, ring], { autoAlpha: 0, duration: 0.3 });
    };

    window.addEventListener("pointermove", onMove, { passive: true });
    window.addEventListener("pointerover", onOver, { passive: true });
    document.documentElement.addEventListener("pointerleave", onLeave);

    return () => {
      delete document.body.dataset.customCursor;
      window.removeEventListener("pointermove", onMove);
      window.removeEventListener("pointerover", onOver);
      document.documentElement.removeEventListener("pointerleave", onLeave);
    };
  }, []);

  return (
    <>
      <div
        ref={dotRef}
        aria-hidden
        className="pointer-events-none fixed start-0 top-0 z-90 size-2 -translate-x-1/2 -translate-y-1/2 rounded-full bg-ember opacity-0"
        style={{ insetInlineStart: 0 }}
      />
      <div
        ref={ringRef}
        aria-hidden
        className="pointer-events-none fixed start-0 top-0 z-90 size-9 -translate-x-1/2 -translate-y-1/2 rounded-full border border-halo/60 opacity-0"
        style={{ insetInlineStart: 0 }}
      />
    </>
  );
}
