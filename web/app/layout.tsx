import type { Metadata, Viewport } from "next";
import { Syne, Manrope, Alexandria, IBM_Plex_Mono } from "next/font/google";
import "./globals.css";
import { LanguageProvider } from "@/lib/i18n/LanguageProvider";
import { site } from "@/lib/site";

const syne = Syne({
  subsets: ["latin"],
  weight: ["600", "700", "800"],
  variable: "--font-syne",
  display: "swap",
});

const manrope = Manrope({
  subsets: ["latin"],
  weight: ["400", "500", "600", "700"],
  variable: "--font-manrope",
  display: "swap",
});

const alexandria = Alexandria({
  subsets: ["arabic", "latin"],
  weight: ["300", "400", "500", "700", "800"],
  variable: "--font-alexandria",
  display: "swap",
});

const plexMono = IBM_Plex_Mono({
  subsets: ["latin"],
  weight: ["400", "500"],
  variable: "--font-plex-mono",
  display: "swap",
});

export const metadata: Metadata = {
  metadataBase: new URL(site.url),
  title: {
    default: "BAIGR — AI Growth Agency",
    template: "%s — BAIGR",
  },
  description:
    "BAIGR blends AI creative, precision advertising and automation to turn attention into revenue. Meta, Google, TikTok & Snapchat ads, AI content, websites, e-commerce and chatbots.",
  keywords: [
    "AI growth agency",
    "digital marketing",
    "Meta ads",
    "Google ads",
    "TikTok ads",
    "AI video",
    "e-commerce",
    "marketing automation",
    "وكالة تسويق",
    "dijital pazarlama ajansı",
  ],
  openGraph: {
    type: "website",
    url: site.url,
    siteName: "BAIGR",
    title: "BAIGR — AI Growth Agency",
    description:
      "We turn attention into revenue. AI creative, precision ads and automation for brands that refuse to be ordinary.",
    locale: "en_US",
    alternateLocale: ["ar_SA", "tr_TR"],
  },
  twitter: {
    card: "summary_large_image",
    title: "BAIGR — AI Growth Agency",
    description: "We turn attention into revenue.",
  },
  robots: { index: true, follow: true },
};

export const viewport: Viewport = {
  themeColor: "#0c0a08",
  width: "device-width",
  initialScale: 1,
};

// Runs before paint: restores the saved language (or detects the browser one)
// so RTL layouts never flash LTR.
const langBoot = `(function(){try{var s=localStorage.getItem("baigr-lang");var l=s||((navigator.languages&&navigator.languages[0])||navigator.language||"en").slice(0,2);if(["ar","en","tr"].indexOf(l)<0)l="en";var d=document.documentElement;d.lang=l;d.dir=l==="ar"?"rtl":"ltr";}catch(e){}})();`;

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html
      lang="en"
      dir="ltr"
      suppressHydrationWarning
      className={`${syne.variable} ${manrope.variable} ${alexandria.variable} ${plexMono.variable}`}
    >
      <head>
        <script dangerouslySetInnerHTML={{ __html: langBoot }} />
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify({
              "@context": "https://schema.org",
              "@type": "Organization",
              name: "BAIGR",
              url: site.url,
              email: site.email,
              description:
                "AI growth agency: AI creative, precision advertising and automation.",
              knowsLanguage: ["ar", "en", "tr"],
            }),
          }}
        />
      </head>
      <body className="grain antialiased">
        <LanguageProvider>{children}</LanguageProvider>
      </body>
    </html>
  );
}
