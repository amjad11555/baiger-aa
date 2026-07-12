import type { Metadata } from "next";
import { IBM_Plex_Sans_Arabic, Plus_Jakarta_Sans } from "next/font/google";
import { notFound } from "next/navigation";
import Cursor from "@/components/Cursor";
import Footer from "@/components/Footer";
import Header from "@/components/Header";
import Loader from "@/components/Loader";
import MotionProvider from "@/components/providers/MotionProvider";
import ScrollProgress from "@/components/ScrollProgress";
import { dirOf, isLocale, locales, type Locale } from "@/lib/i18n/config";
import { getDictionary } from "@/lib/i18n/dictionaries";
import "../globals.css";

const jakarta = Plus_Jakarta_Sans({
  variable: "--font-jakarta",
  subsets: ["latin", "latin-ext"],
  weight: ["300", "400", "500", "600", "700"],
  display: "swap",
});

const plexArabic = IBM_Plex_Sans_Arabic({
  variable: "--font-arabic",
  subsets: ["arabic", "latin"],
  weight: ["300", "400", "500", "600", "700"],
  display: "swap",
});

export function generateStaticParams() {
  return locales.map((locale) => ({ locale }));
}

export const dynamicParams = false;

export async function generateMetadata({
  params,
}: {
  params: Promise<{ locale: string }>;
}): Promise<Metadata> {
  const { locale } = await params;
  if (!isLocale(locale)) return {};
  const dict = getDictionary(locale);
  return {
    title: {
      default: dict.meta.title,
      template: "%s — BAIGR",
    },
    description: dict.meta.description,
    icons: { icon: "/favicon.svg" },
    openGraph: {
      title: dict.meta.title,
      description: dict.meta.description,
      siteName: "BAIGR Growth Agency",
      locale,
      type: "website",
    },
    alternates: {
      languages: Object.fromEntries(locales.map((l) => [l, `/${l}`])),
    },
  };
}

export default async function LocaleLayout({
  children,
  params,
}: {
  children: React.ReactNode;
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();

  const typedLocale = locale as Locale;
  const dict = getDictionary(typedLocale);

  return (
    <html
      lang={typedLocale}
      dir={dirOf(typedLocale)}
      className={`${jakarta.variable} ${plexArabic.variable} antialiased`}
    >
      <body>
        <MotionProvider>
          <Loader />
          <Cursor />
          <ScrollProgress />
          <Header locale={typedLocale} dict={dict} />
          <main>{children}</main>
          <Footer locale={typedLocale} dict={dict} />
        </MotionProvider>
      </body>
    </html>
  );
}
