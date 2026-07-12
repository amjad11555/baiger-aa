import { notFound } from "next/navigation";
import FinalCta from "@/components/sections/FinalCta";
import Hero from "@/components/sections/Hero";
import Problem from "@/components/sections/Problem";
import Process from "@/components/sections/Process";
import Services from "@/components/sections/Services";
import Shift from "@/components/sections/Shift";
import Trust from "@/components/sections/Trust";
import { isLocale, type Locale } from "@/lib/i18n/config";
import { getDictionary } from "@/lib/i18n/dictionaries";

/**
 * The homepage is not sections — it is chapters.
 * 01 Arrival → 02 Problem → 03 Transformation →
 * 04 Services → 05 Process → 06 Trust → 07 Action.
 */
export default async function HomePage({
  params,
}: {
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const typedLocale = locale as Locale;
  const dict = getDictionary(typedLocale);

  return (
    <>
      <Hero locale={typedLocale} dict={dict} />
      <Problem dict={dict} />
      <Shift dict={dict} />
      <Services locale={typedLocale} dict={dict} />
      <Process dict={dict} />
      <Trust dict={dict} />
      <FinalCta locale={typedLocale} dict={dict} />
    </>
  );
}
