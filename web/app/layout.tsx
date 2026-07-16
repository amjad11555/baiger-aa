import type { Metadata, Viewport } from "next";
import { Cairo } from "next/font/google";
import "./globals.css";
import { LanguageProvider } from "@/lib/i18n/LanguageProvider";
import { site } from "@/lib/site";

// One typeface across every language — Arabic, Latin and Turkish.
const cairo = Cairo({
  subsets: ["arabic", "latin", "latin-ext"],
  weight: ["400", "500", "600", "700", "800", "900"],
  variable: "--font-cairo",
  display: "swap",
});

export const metadata: Metadata = {
  metadataBase: new URL(site.url),
  title: {
    default: "BAIGR — Creative Strategy & AI Growth",
    template: "%s — BAIGR",
  },
  description:
    "BAIGR is an AI-native creative growth studio. We turn bold ideas into campaigns and campaigns into revenue — Meta, Google, TikTok & Snapchat ads, AI content, websites, e-commerce and automation.",
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
    title: "BAIGR — Creative Strategy & AI Growth",
    description:
      "Creative that sells. Growth you can measure. An AI-native studio for brands that refuse to blend in.",
    locale: "en_US",
    alternateLocale: ["ar_SA", "tr_TR"],
  },
  twitter: {
    card: "summary_large_image",
    title: "BAIGR — Creative Strategy & AI Growth",
    description: "Creative that sells. Growth you can measure.",
  },
  robots: { index: true, follow: true },
};

export const viewport: Viewport = {
  themeColor: "#fefefe",
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
      className={cairo.variable}
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
