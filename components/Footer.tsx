import Link from "next/link";
import type { Locale } from "@/lib/i18n/config";
import { whatsappHref } from "@/lib/i18n/config";
import type { Dictionary } from "@/lib/i18n/dictionaries";
import { Logo } from "./Logo";

interface FooterProps {
  locale: Locale;
  dict: Dictionary;
}

export default function Footer({ locale, dict }: FooterProps) {
  const year = new Date().getFullYear();

  const links = [
    { href: `/${locale}`, label: dict.nav.home },
    { href: `/${locale}/services`, label: dict.nav.services },
    { href: `/${locale}/about`, label: dict.nav.about },
    { href: `/${locale}/contact`, label: dict.nav.contact },
  ];

  return (
    <footer className="dark-scene border-t border-ivory/5">
      <div className="mx-auto max-w-[1280px] px-6 py-24 md:px-10">
        <div className="grid gap-16 md:grid-cols-[1.6fr_1fr_1fr]">
          <div className="max-w-sm">
            <Logo wordClassName="text-ivory" />
            <p className="mt-6 text-mist leading-relaxed">{dict.footer.tagline}</p>
            <p className="mt-4 text-sm text-mist/70">{dict.footer.basedIn}</p>
          </div>

          <nav aria-label="Footer">
            <h3 className="text-sm font-medium uppercase tracking-[0.2em] text-mist/70">
              {dict.footer.navTitle}
            </h3>
            <ul className="mt-6 space-y-3">
              {links.map((link) => (
                <li key={link.href}>
                  <Link
                    href={link.href}
                    data-cursor="link"
                    className="text-ivory/85 transition-colors duration-300 hover:text-hyper"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </nav>

          <div>
            <h3 className="text-sm font-medium uppercase tracking-[0.2em] text-mist/70">
              {dict.footer.contactTitle}
            </h3>
            <ul className="mt-6 space-y-3">
              <li>
                <a
                  href={whatsappHref()}
                  target="_blank"
                  rel="noopener noreferrer"
                  data-cursor="link"
                  className="text-ivory/85 transition-colors duration-300 hover:text-hyper"
                  dir="ltr"
                >
                  +90 537 857 31 81
                </a>
              </li>
              <li>
                <a
                  href="mailto:baigr.agency@gmail.com"
                  data-cursor="link"
                  className="text-ivory/85 transition-colors duration-300 hover:text-hyper"
                >
                  baigr.agency@gmail.com
                </a>
              </li>
            </ul>

            <h3 className="mt-10 text-sm font-medium uppercase tracking-[0.2em] text-mist/70">
              {dict.footer.languagesTitle}
            </h3>
            <ul className="mt-4 flex gap-4 text-sm text-mist">
              <li>العربية</li>
              <li>English</li>
              <li>Türkçe</li>
            </ul>
          </div>
        </div>

        <div className="mt-20 flex flex-col items-start justify-between gap-4 border-t border-ivory/5 pt-8 text-sm text-mist/60 sm:flex-row sm:items-center">
          <p>
            © <span className="tabular">{year}</span> BAIGR Growth Agency —{" "}
            {dict.footer.rights}
          </p>
          <p dir="ltr" className="tracking-[0.3em] text-xs">
            BAIGR
          </p>
        </div>
      </div>
    </footer>
  );
}
