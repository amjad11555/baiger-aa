"use client";

import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { site } from "@/lib/site";
import { WhatsAppIcon } from "./ContactCta";

/** Floating glass WhatsApp button — bottom corner, pulsing, always reachable. */
export function WhatsAppFloat() {
  const { dict } = useLanguage();

  return (
    <a
      href={site.whatsapp}
      target="_blank"
      rel="noopener noreferrer"
      aria-label={dict.whatsapp}
      title={dict.whatsapp}
      className="glass group fixed bottom-5 end-5 z-70 grid size-14 place-items-center rounded-full transition-all duration-300 hover:scale-110 hover:border-[#25D366]/60 hover:shadow-[0_0_32px_rgba(37,211,102,0.35)]"
    >
      <span
        aria-hidden
        className="absolute inset-0 animate-pulse-ring rounded-full border border-[#25D366]/50"
      />
      <WhatsAppIcon className="size-6 text-[#25D366] transition-transform duration-300 group-hover:scale-110" />
    </a>
  );
}
