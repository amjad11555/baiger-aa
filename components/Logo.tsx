interface LogoMarkProps {
  className?: string;
  color?: string;
}

/** BAIGR signal mark — four rounded arcs radiating like a broadcast. */
export function LogoMark({ className, color = "currentColor" }: LogoMarkProps) {
  return (
    <svg
      viewBox="0 0 128 128"
      className={className}
      aria-hidden="true"
      fill="none"
    >
      <g stroke={color} strokeWidth="10" strokeLinecap="round">
        <path d="M 0.9 72.1 A 70 70 0 0 1 103.1 36.6" />
        <path d="M 17.1 78.2 A 52 52 0 0 1 93.6 51.9" />
        <path d="M 34.1 84.4 A 34 34 0 0 1 84.0 67.2" />
        <path d="M 51.0 90.5 A 16 16 0 0 1 74.5 82.4" />
      </g>
    </svg>
  );
}

interface LogoProps {
  className?: string;
  markClassName?: string;
  wordClassName?: string;
}

export function Logo({ className, markClassName, wordClassName }: LogoProps) {
  return (
    <span
      className={`inline-flex items-center gap-2.5 select-none ${className ?? ""}`}
    >
      <LogoMark className={`h-6 w-6 text-hyper ${markClassName ?? ""}`} />
      <span
        dir="ltr"
        className={`font-semibold tracking-[0.35em] text-[17px] leading-none translate-y-[1px] ${wordClassName ?? ""}`}
      >
        BAIGR
      </span>
    </span>
  );
}
