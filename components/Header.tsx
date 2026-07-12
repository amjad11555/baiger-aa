"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import type { Locale } from "@/lib/i18n/config";
import { LOCALE_COOKIE, locales, whatsappHref } from "@/lib/i18n/config";
import type { Dictionary } from "@/lib/i18n/dictionaries";
import { Logo } from "./Logo";
import Magnetic from "./Magnetic";

interface HeaderProps {
  locale: Locale;
  dict: Dictionary;
}

export default function Header({ locale, dict }: HeaderProps) {
  const pathname = usePathname();
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    setOpen(false);
  }, [pathname]);

  useEffect(() => {
    document.documentElement.style.overflow = open ? "hidden" : "";
    return () => {
      document.documentElement.style.overflow = "";
    };
  }, [open]);

  const links = [
    { href: `/${locale}`, label: dict.nav.home },
    { href: `/${locale}/services`, label: dict.nav.services },
    { href: `/${locale}/about`, label: dict.nav.about },
    { href: `/${locale}/contact`, label: dict.nav.contact },
  ];

  const isActive = (href: string) =>
    href === `/${locale}` ? pathname === href : pathname.startsWith(href);

  const switchLocale = (target: Locale) => {
    document.cookie = `${LOCALE_COOKIE}=${target};path=/;max-age=${60 * 60 * 24 * 365}`;
    const rest = pathname.replace(/^\/(en|ar|tr)/, "");
    window.location.href = `/${target}${rest}`;
  };

  return (
    <header className="fixed inset-x-0 top-0 z-[80]">
      <div
        className={`mx-auto flex items-center justify-between gap-4 transition-all duration-500 ease-[var(--ease-out-quart)] ${
          scrolled
            ? "mt-3 max-w-5xl rounded-[24px] glass px-5 py-3 shadow-[0_8px_40px_-16px_rgba(0,0,0,0.5)]"
            : "mt-0 max-w-[1440px] bg-transparent px-6 py-5 md:px-10"
        }`}
      >
        <Link
          href={`/${locale}`}
          aria-label="BAIGR — Home"
          data-cursor="link"
          className="shrink-0"
        >
          <Logo
            markClassName={scrolled ? "h-5 w-5" : "h-6 w-6"}
            wordClassName="text-ivory"
          />
        </Link>

        <nav
          aria-label="Main"
          className="hidden md:flex items-center gap-1"
        >
          {links.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              data-cursor="link"
              className={`rounded-full px-4 py-2 text-[15px] transition-colors duration-300 ${
                isActive(link.href)
                  ? "text-hyper"
                  : "text-mist hover:text-ivory"
              }`}
            >
              {link.label}
            </Link>
          ))}
        </nav>

        <div className="flex items-center gap-3">
          <div
            className="hidden sm:flex items-center gap-1 rounded-full border border-ivory/10 p-1"
            role="group"
            aria-label="Language"
          >
            {locales.map((l) => (
              <button
                key={l}
                onClick={() => switchLocale(l)}
                data-cursor="link"
                aria-current={l === locale ? "true" : undefined}
                className={`rounded-full px-2.5 py-1 text-xs font-medium uppercase transition-colors duration-300 ${
                  l === locale
                    ? "bg-hyper text-void"
                    : "text-mist hover:text-ivory"
                }`}
              >
                {l}
              </button>
            ))}
          </div>

          <Magnetic className="hidden md:inline-block">
            <a
              href={whatsappHref(dict.nav.startProject)}
              target="_blank"
              rel="noopener noreferrer"
              data-cursor="button"
              className="btn btn-primary !px-6 !py-3 text-[15px]"
            >
              {dict.nav.startProject}
            </a>
          </Magnetic>

          <button
            onClick={() => setOpen((v) => !v)}
            aria-expanded={open}
            aria-label={open ? dict.nav.close : dict.nav.menu}
            className="md:hidden flex h-11 w-11 flex-col items-center justify-center gap-[5px] rounded-full border border-ivory/15"
          >
            <span
              className={`block h-[1.5px] w-5 bg-ivory transition-transform duration-300 ${
                open ? "translate-y-[3.25px] rotate-45" : ""
              }`}
            />
            <span
              className={`block h-[1.5px] w-5 bg-ivory transition-transform duration-300 ${
                open ? "-translate-y-[3.25px] -rotate-45" : ""
              }`}
            />
          </button>
        </div>
      </div>

      {/* Mobile menu */}
      <div
        className={`md:hidden fixed inset-0 z-[-1] bg-void/95 backdrop-blur-xl transition-opacity duration-400 ${
          open ? "opacity-100" : "pointer-events-none opacity-0"
        }`}
      >
        <div className="flex h-full flex-col justify-center gap-2 px-8 pt-16">
          {links.map((link, i) => (
            <Link
              key={link.href}
              href={link.href}
              className={`py-3 text-3xl font-semibold transition-all duration-500 ${
                isActive(link.href) ? "text-hyper" : "text-ivory"
              } ${open ? "translate-y-0 opacity-100" : "translate-y-4 opacity-0"}`}
              style={{ transitionDelay: open ? `${80 + i * 60}ms` : "0ms" }}
            >
              {link.label}
            </Link>
          ))}

          <div className="mt-8 flex items-center gap-2">
            {locales.map((l) => (
              <button
                key={l}
                onClick={() => switchLocale(l)}
                className={`rounded-full px-4 py-2 text-sm font-medium uppercase ${
                  l === locale
                    ? "bg-hyper text-void"
                    : "border border-ivory/15 text-mist"
                }`}
              >
                {dict.langNames[l]}
              </button>
            ))}
          </div>

          <a
            href={whatsappHref(dict.nav.startProject)}
            target="_blank"
            rel="noopener noreferrer"
            className="btn btn-primary mt-8 w-full"
          >
            {dict.nav.startProject}
          </a>
        </div>
      </div>
    </header>
  );
}
