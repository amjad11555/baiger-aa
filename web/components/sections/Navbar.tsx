"use client";

import { useEffect, useRef, useState } from "react";
import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { locales, type Locale } from "@/lib/i18n/dictionaries";
import { site } from "@/lib/site";
import { gsap, prefersReducedMotion } from "@/lib/gsap";
import { cn } from "@/lib/utils";
import { LogoMark } from "@/components/ui/LogoMark";

const langLabels: Record<Locale, string> = { en: "EN", ar: "ع", tr: "TR" };

export function Navbar() {
  const { dict, locale, setLocale } = useLanguage();
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);
  const overlayRef = useRef<HTMLDivElement>(null);

  const links = [
    { href: "#about", label: dict.nav.about },
    { href: "#services", label: dict.nav.services },
    { href: "#work", label: dict.nav.work },
    { href: "#process", label: dict.nav.process },
    { href: "#faq", label: dict.nav.faq },
  ];

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    if (!open || prefersReducedMotion()) return;
    const items = overlayRef.current?.querySelectorAll("[data-menu-item]");
    if (items?.length) {
      gsap.fromTo(
        items,
        { yPercent: 60, autoAlpha: 0 },
        { yPercent: 0, autoAlpha: 1, duration: 0.7, stagger: 0.06, ease: "power3.out" }
      );
    }
  }, [open]);

  useEffect(() => {
    document.body.style.overflow = open ? "hidden" : "";
    return () => {
      document.body.style.overflow = "";
    };
  }, [open]);

  const LangSwitcher = ({ className }: { className?: string }) => (
    <div
      className={cn(
        "flex items-center gap-1 rounded-full border hairline p-1 font-mono text-xs",
        className
      )}
      role="group"
      aria-label="Language"
    >
      {locales.map((l) => (
        <button
          key={l}
          onClick={() => setLocale(l)}
          aria-pressed={locale === l}
          className={cn(
            "min-h-6 min-w-9 rounded-full px-3 py-1.5 transition-colors",
            locale === l
              ? "bg-iris-deep text-white"
              : "text-mist hover:text-ink"
          )}
        >
          {langLabels[l]}
        </button>
      ))}
    </div>
  );

  return (
    <header
      className={cn(
        "fixed inset-x-0 top-0 z-70 transition-all duration-500",
        scrolled && !open ? "glass" : "border-b border-transparent"
      )}
    >
      <nav className="mx-auto flex h-[72px] w-full max-w-6xl items-center justify-between px-5 md:px-8">
        <a
          href="#top"
          className="inline-flex items-center gap-2 text-ink"
          aria-label="BAIGR — home"
        >
          <LogoMark className="h-[30px] w-auto" />
          <span className="font-display text-[1.35rem] font-black tracking-[0.08em]">
            BAIGR
          </span>
        </a>

        <ul className="hidden items-center gap-8 lg:flex">
          {links.map((l) => (
            <li key={l.href}>
              <a
                href={l.href}
                className="inline-block px-1 py-2.5 text-sm text-mist transition-colors hover:text-ink"
              >
                {l.label}
              </a>
            </li>
          ))}
          <li>
            <a
              href="https://baigr.com/blog/"
              className="inline-block rounded-full border hairline px-4 py-1.5 text-sm font-bold text-iris-deep transition-colors hover:border-iris hover:bg-iris/10"
            >
              {dict.nav.blog}
            </a>
          </li>
        </ul>

        <div className="flex items-center gap-3">
          <LangSwitcher className="hidden sm:flex" />
          <a
            href={site.whatsapp}
            target="_blank"
            rel="noopener noreferrer"
            className="hidden items-center rounded-full bg-lime px-5 py-2.5 text-sm font-semibold text-ink transition-all duration-300 hover:bg-iris-deep hover:text-white lg:inline-flex"
          >
            {dict.nav.cta}
          </a>
          <button
            onClick={() => setOpen((v) => !v)}
            aria-expanded={open}
            aria-label={open ? "Close menu" : "Open menu"}
            className="flex size-10 flex-col items-center justify-center gap-1.5 lg:hidden"
          >
            <span
              className={cn(
                "h-px w-6 bg-ink transition-transform duration-300",
                open && "translate-y-[3.5px] rotate-45"
              )}
            />
            <span
              className={cn(
                "h-px w-6 bg-ink transition-transform duration-300",
                open && "-translate-y-[3.5px] -rotate-45"
              )}
            />
          </button>
        </div>
      </nav>

      {/* Mobile overlay menu */}
      <div
        ref={overlayRef}
        className={cn(
          "fixed inset-0 top-[72px] z-60 flex-col justify-between bg-paper/95 px-6 pb-10 pt-12 backdrop-blur-xl lg:hidden",
          open ? "flex" : "hidden"
        )}
      >
        <ul className="space-y-2">
          {links.map((l, i) => (
            <li key={l.href} className="overflow-hidden">
              <a
                data-menu-item
                href={l.href}
                onClick={() => setOpen(false)}
                className="font-display block py-2 text-4xl font-bold text-ink transition-colors hover:text-iris"
              >
                <span className="me-4 font-mono text-sm text-iris">
                  {String(i + 1).padStart(2, "0")}
                </span>
                {l.label}
              </a>
            </li>
          ))}
          <li className="overflow-hidden">
            <a
              data-menu-item
              href="https://baigr.com/blog/"
              onClick={() => setOpen(false)}
              className="font-display block py-2 text-4xl font-bold text-ink transition-colors hover:text-iris"
            >
              <span className="me-4 font-mono text-sm text-iris">
                {String(links.length + 1).padStart(2, "0")}
              </span>
              {dict.nav.blog}
            </a>
          </li>
        </ul>
        <div data-menu-item className="flex items-center justify-between">
          <LangSwitcher />
          <a
            href={site.whatsapp}
            target="_blank"
            rel="noopener noreferrer"
            onClick={() => setOpen(false)}
            className="rounded-full bg-lime px-6 py-3 text-sm font-semibold text-ink"
          >
            {dict.nav.cta}
          </a>
        </div>
      </div>
    </header>
  );
}
