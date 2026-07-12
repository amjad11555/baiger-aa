"use client";

import { useEffect, useState } from "react";
import { LogoMark } from "./Logo";

/**
 * Entry scene: void black, the signal mark pulses once,
 * then the site fades in. Hard capped at 1.4s.
 */
export default function Loader() {
  const [phase, setPhase] = useState<"active" | "leaving" | "done">("active");

  useEffect(() => {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      setPhase("done");
      return;
    }
    const leave = setTimeout(() => setPhase("leaving"), 950);
    const done = setTimeout(() => setPhase("done"), 1400);
    return () => {
      clearTimeout(leave);
      clearTimeout(done);
    };
  }, []);

  if (phase === "done") return null;

  return (
    <div
      aria-hidden="true"
      className={`fixed inset-0 z-[100] flex items-center justify-center bg-void transition-opacity duration-500 ease-out ${
        phase === "leaving" ? "opacity-0 pointer-events-none" : "opacity-100"
      }`}
    >
      <div className="relative flex flex-col items-center gap-5">
        <span className="absolute -inset-10 rounded-full border border-hyper/20 animate-[pulse-ring_1.4s_ease-out_infinite]" />
        <LogoMark className="h-12 w-12 text-hyper animate-[float-slow_2.4s_ease-in-out_infinite]" />
        <span
          dir="ltr"
          className="text-ivory font-semibold tracking-[0.45em] text-sm"
        >
          BAIGR
        </span>
      </div>
    </div>
  );
}
