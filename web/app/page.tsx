import { LenisProvider } from "@/components/providers/LenisProvider";
import { Cursor } from "@/components/fx/Cursor";
import { Navbar } from "@/components/sections/Navbar";
import { Hero } from "@/components/sections/Hero";
import { TrustedBy } from "@/components/sections/TrustedBy";
import { About } from "@/components/sections/About";
import { Services } from "@/components/sections/Services";
import { Portfolio } from "@/components/sections/Portfolio";
import { CaseStudies } from "@/components/sections/CaseStudies";
import { Process } from "@/components/sections/Process";
import { WhyBaigr } from "@/components/sections/WhyBaigr";
import { Testimonials } from "@/components/sections/Testimonials";
import { Faq } from "@/components/sections/Faq";
import { ContactCta } from "@/components/sections/ContactCta";
import { Footer } from "@/components/sections/Footer";
import { WhatsAppFloat } from "@/components/sections/WhatsAppFloat";

export default function Home() {
  return (
    <LenisProvider>
      <Cursor />
      <Navbar />
      <main>
        <Hero />
        <TrustedBy />
        <About />
        <Services />
        <Portfolio />
        <CaseStudies />
        <Process />
        <WhyBaigr />
        <Testimonials />
        <Faq />
        <ContactCta />
      </main>
      <Footer />
      <WhatsAppFloat />
    </LenisProvider>
  );
}
