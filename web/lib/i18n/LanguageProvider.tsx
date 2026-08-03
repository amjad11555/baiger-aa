"use client";

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useState,
  type ReactNode,
} from "react";
import { dictionaries, locales, type Dictionary, type Locale } from "./dictionaries";

interface LanguageContextValue {
  locale: Locale;
  dict: Dictionary;
  dir: "ltr" | "rtl";
  setLocale: (locale: Locale) => void;
}

const LanguageContext = createContext<LanguageContextValue | null>(null);

function isLocale(value: string): value is Locale {
  return (locales as string[]).includes(value);
}

export function LanguageProvider({ children }: { children: ReactNode }) {
  // SSR renders English; the boot script in layout.tsx has already set
  // <html lang/dir> before paint, and we sync to it on mount — no refresh needed.
  const [locale, setLocaleState] = useState<Locale>("en");

  useEffect(() => {
    const docLang = document.documentElement.lang;
    if (isLocale(docLang) && docLang !== "en") {
      setLocaleState(docLang);
    }
  }, []);

  const setLocale = useCallback((next: Locale) => {
    setLocaleState(next);
    try {
      localStorage.setItem("baigr-lang", next);
    } catch {
      // storage unavailable (private mode) — language still switches for this visit
    }
  }, []);

  useEffect(() => {
    const dir = locale === "ar" ? "rtl" : "ltr";
    document.documentElement.lang = locale;
    document.documentElement.dir = dir;
  }, [locale]);

  const dir = locale === "ar" ? "rtl" : "ltr";

  return (
    <LanguageContext.Provider
      value={{ locale, dict: dictionaries[locale], dir, setLocale }}
    >
      {children}
    </LanguageContext.Provider>
  );
}

export function useLanguage() {
  const ctx = useContext(LanguageContext);
  if (!ctx) throw new Error("useLanguage must be used inside <LanguageProvider>");
  return ctx;
}
