"use client";

import { useLanguage } from "@/lib/i18n/LanguageProvider";
import { site } from "@/lib/site";
import { Container } from "./Section";
import { LogoMark } from "@/components/ui/LogoMark";

export function Footer() {
  const { dict } = useLanguage();
  const year = new Date().getFullYear();

  const links = [
    { href: "#about", label: dict.nav.about },
    { href: "#services", label: dict.nav.services },
    { href: "#work", label: dict.nav.work },
    { href: "#process", label: dict.nav.process },
    { href: "#faq", label: dict.nav.faq },
  ];

  return (
    <footer className="hairline border-t py-16">
      <Container>
        <div className="grid gap-12 md:grid-cols-[2fr_1fr_1fr]">
          <div>
            <div className="flex items-center gap-2.5">
              <LogoMark className="h-10 w-auto" />
              <span className="font-display text-3xl font-black tracking-[0.08em]">
                BAIGR
              </span>
            </div>
            <p className="mt-3 max-w-xs leading-relaxed text-mist">
              {dict.footer.tagline}
            </p>
          </div>
          <nav aria-label={dict.footer.nav}>
            <h3 className="mb-4 font-mono text-xs tracking-[0.2em] uppercase text-mist">
              {dict.footer.nav}
            </h3>
            <ul className="space-y-2.5">
              {links.map((l) => (
                <li key={l.href}>
                  <a
                    href={l.href}
                    className="text-sm text-mist transition-colors hover:text-iris"
                  >
                    {l.label}
                  </a>
                </li>
              ))}
            </ul>
          </nav>
          <div>
            <h3 className="mb-4 font-mono text-xs tracking-[0.2em] uppercase text-mist">
              {dict.footer.talk}
            </h3>
            <ul className="space-y-2.5 text-sm">
              <li>
                <a
                  href={site.whatsapp}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="text-mist transition-colors hover:text-iris"
                >
                  WhatsApp
                </a>
              </li>
              <li>
                <a
                  href={`mailto:${site.email}`}
                  className="text-mist transition-colors hover:text-iris"
                >
                  {site.email}
                </a>
              </li>
              <li>
                <a
                  href={site.instagram}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="text-mist transition-colors hover:text-iris"
                >
                  Instagram
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div className="hairline mt-14 flex flex-wrap items-center justify-between gap-3 border-t pt-7">
          <p className="font-mono text-xs text-mist">
            © {year} BAIGR — {dict.footer.rights}
          </p>
          <p className="font-mono text-xs text-mist">AR · EN · TR</p>
        </div>
      </Container>
    </footer>
  );
}
