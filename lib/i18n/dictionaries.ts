import type { Locale } from "./config";

export interface ServiceGroup {
  id: string;
  index: string;
  title: string;
  tagline: string;
  items: string[];
  description: string;
}

export interface ProcessStep {
  index: string;
  title: string;
  description: string;
}

export interface Dictionary {
  meta: {
    title: string;
    description: string;
  };
  nav: {
    home: string;
    services: string;
    about: string;
    contact: string;
    startProject: string;
    menu: string;
    close: string;
  };
  hero: {
    badge: string;
    titleA: string;
    titleB: string;
    subtitle: string;
    ctaPrimary: string;
    ctaSecondary: string;
    scrollCue: string;
  };
  problem: {
    label: string;
    title: string;
    points: { title: string; text: string }[];
  };
  shift: {
    label: string;
    title: string;
    text: string;
    pillars: string[];
  };
  services: {
    label: string;
    title: string;
    subtitle: string;
    explore: string;
    groups: ServiceGroup[];
  };
  process: {
    label: string;
    title: string;
    subtitle: string;
    steps: ProcessStep[];
  };
  trust: {
    label: string;
    title: string;
    points: { title: string; text: string }[];
  };
  cta: {
    title: string;
    text: string;
    whatsapp: string;
    secondary: string;
  };
  footer: {
    tagline: string;
    navTitle: string;
    languagesTitle: string;
    contactTitle: string;
    rights: string;
    basedIn: string;
  };
  servicesPage: {
    title: string;
    subtitle: string;
    ctaLine: string;
  };
  aboutPage: {
    title: string;
    subtitle: string;
    missionLabel: string;
    missionTitle: string;
    missionText: string;
    beliefsLabel: string;
    beliefs: { title: string; text: string }[];
    marketsLabel: string;
    marketsTitle: string;
    markets: string[];
  };
  contactPage: {
    title: string;
    subtitle: string;
    whatsappTitle: string;
    whatsappText: string;
    whatsappCta: string;
    emailTitle: string;
    emailText: string;
    responseNote: string;
  };
  langNames: Record<Locale, string>;
}

const en: Dictionary = {
  meta: {
    title: "BAIGR — Creative Growth Agency",
    description:
      "BAIGR builds complete digital ecosystems — brand, web, marketing and automation — engineered for measurable business growth across the Middle East.",
  },
  nav: {
    home: "Home",
    services: "Services",
    about: "About",
    contact: "Contact",
    startProject: "Start Your Project",
    menu: "Menu",
    close: "Close",
  },
  hero: {
    badge: "Creative Growth Agency",
    titleA: "Growth is not luck.",
    titleB: "It is a system.",
    subtitle:
      "BAIGR designs complete digital ecosystems — identity, web, marketing and automation — engineered to grow businesses across the Middle East.",
    ctaPrimary: "Start Your Project",
    ctaSecondary: "Explore Services",
    scrollCue: "Scroll to enter",
  },
  problem: {
    label: "The Problem",
    title: "Most businesses don't have a marketing problem. They have a system problem.",
    points: [
      {
        title: "A website that looks fine",
        text: "— but doesn't convert visitors into customers.",
      },
      {
        title: "Ads that keep spending",
        text: "— but never compound into predictable growth.",
      },
      {
        title: "Content that gets posted",
        text: "— but never builds a brand people remember.",
      },
      {
        title: "Tools everywhere",
        text: "— connected to nothing, measured by no one.",
      },
    ],
  },
  shift: {
    label: "The Shift",
    title: "We don't sell services. We build growth systems.",
    text: "Every part of your digital presence — identity, website, campaigns, automation — designed as one connected ecosystem with one job: measurable growth.",
    pillars: ["Strategy", "Design", "Technology", "Automation"],
  },
  services: {
    label: "What We Do",
    title: "One partner. A complete ecosystem.",
    subtitle:
      "Seven capabilities, engineered to work as one system around your business.",
    explore: "Explore all services",
    groups: [
      {
        id: "web",
        index: "01",
        title: "Web Experiences",
        tagline: "Websites engineered to sell, not just to exist.",
        items: [
          "Corporate Websites",
          "Landing Pages",
          "WooCommerce",
          "Shopify",
          "Salla",
          "Zid",
        ],
        description:
          "High-performance websites and e-commerce stores designed around conversion — from corporate platforms to Salla and Zid storefronts built for the Gulf market.",
      },
      {
        id: "brand",
        index: "02",
        title: "Brand Identity",
        tagline: "Identities that make businesses unforgettable.",
        items: ["Brand Identity", "Visual Systems", "Social Media Design"],
        description:
          "Complete visual systems — logo, typography, color, voice — plus social media design that keeps every touchpoint premium and consistent.",
      },
      {
        id: "ai",
        index: "03",
        title: "AI Production",
        tagline: "Cinematic visuals at the speed of software.",
        items: ["AI Image Production", "AI Video Production"],
        description:
          "Product imagery, campaign visuals and video produced with AI pipelines — premium quality without the traditional production timeline.",
      },
      {
        id: "ads",
        index: "04",
        title: "Performance Marketing",
        tagline: "Campaigns accountable to revenue, not impressions.",
        items: ["Advertising Campaigns", "Meta Ads", "Google Ads"],
        description:
          "Full-funnel campaigns on Meta and Google — strategy, creative, targeting and optimization measured against real business outcomes.",
      },
      {
        id: "automation",
        index: "05",
        title: "Automation",
        tagline: "Systems that work while you sleep.",
        items: [
          "Marketing Automation",
          "CRM Automation",
          "WhatsApp Automation",
        ],
        description:
          "Lead routing, follow-ups, CRM flows and WhatsApp journeys automated end-to-end — so no opportunity is ever lost to a slow reply.",
      },
      {
        id: "consulting",
        index: "06",
        title: "Growth Consulting",
        tagline: "A strategist inside your business.",
        items: ["Marketing Consultation", "Growth Strategy"],
        description:
          "Clear, honest marketing consultation — positioning, channels, budgets and priorities — from a partner invested in your growth.",
      },
      {
        id: "infrastructure",
        index: "07",
        title: "Business Infrastructure",
        tagline: "The foundations of a global digital business.",
        items: [
          "US Company Formation Assistance",
          "UK Company Formation Assistance",
          "Payment Gateway Assistance",
          "Digital Infrastructure",
        ],
        description:
          "Assistance with US and UK company formation, payment gateways and the digital infrastructure your business needs to operate globally.",
      },
    ],
  },
  process: {
    label: "How We Work",
    title: "A precise process. No guessing.",
    subtitle: "Five stages, from first conversation to measurable growth.",
    steps: [
      {
        index: "01",
        title: "Listen",
        description:
          "We start with your business, not our services. Goals, market, customers, numbers.",
      },
      {
        index: "02",
        title: "Diagnose",
        description:
          "We map your entire digital presence and find exactly where growth is leaking.",
      },
      {
        index: "03",
        title: "Design",
        description:
          "We architect the system — brand, web, campaigns, automation — as one connected plan.",
      },
      {
        index: "04",
        title: "Build",
        description:
          "We craft every asset to a premium standard and connect every tool into one ecosystem.",
      },
      {
        index: "05",
        title: "Grow",
        description:
          "We launch, measure, optimize and scale — as a partner, not a vendor.",
      },
    ],
  },
  trust: {
    label: "Why BAIGR",
    title: "Built like a partner. Not a vendor.",
    points: [
      {
        title: "Systems, not services",
        text: "We never ship disconnected deliverables. Everything we build plugs into one growth ecosystem.",
      },
      {
        title: "Zero templates",
        text: "Every identity, website and campaign is designed from zero, around your business.",
      },
      {
        title: "Clarity, not jargon",
        text: "You'll always understand what we're doing, why we're doing it, and what it returned.",
      },
      {
        title: "Your market, our region",
        text: "We work across the Gulf, the Levant and Türkiye — in Arabic, English and Turkish.",
      },
    ],
  },
  cta: {
    title: "Growth starts with one conversation.",
    text: "Tell us about your business. We'll tell you exactly how we'd grow it — clearly, honestly, and for free.",
    whatsapp: "Chat on WhatsApp",
    secondary: "Explore Services",
  },
  footer: {
    tagline: "Creative strategy & digital marketing for businesses that want to grow.",
    navTitle: "Navigate",
    languagesTitle: "Languages",
    contactTitle: "Contact",
    rights: "All rights reserved.",
    basedIn: "Serving the Middle East — remotely, everywhere.",
  },
  servicesPage: {
    title: "Everything your growth needs. In one place.",
    subtitle:
      "Seven capabilities designed to work as one connected system — so you never have to coordinate five vendors again.",
    ctaLine: "Not sure where to start? Start with a conversation.",
  },
  aboutPage: {
    title: "We are BAIGR.",
    subtitle: "A creative growth agency for the Middle East.",
    missionLabel: "Our Mission",
    missionTitle: "We help businesses grow through creative systems.",
    missionText:
      "We don't sell websites. We don't sell ads. We don't sell design. We build complete digital ecosystems — and every project must create measurable business growth.",
    beliefsLabel: "What We Believe",
    beliefs: [
      {
        title: "Growth is engineering",
        text: "Predictable growth comes from connected systems — not from isolated tactics.",
      },
      {
        title: "Premium is restraint",
        text: "Luxury is created through precision and space, never through noise.",
      },
      {
        title: "Partners, not projects",
        text: "We succeed when your numbers grow — so we behave like part of your team.",
      },
      {
        title: "Technology serves story",
        text: "AI, automation and code exist to amplify a clear brand story — never to replace it.",
      },
    ],
    marketsLabel: "Where We Work",
    marketsTitle: "One region. Three languages. Fully remote.",
    markets: [
      "Saudi Arabia",
      "United Arab Emirates",
      "Qatar",
      "Kuwait",
      "Oman",
      "Bahrain",
      "Jordan",
      "Iraq",
      "Türkiye",
    ],
  },
  contactPage: {
    title: "Let's talk about your growth.",
    subtitle:
      "One conversation is enough to know if we're the right partner. No pressure, no scripts.",
    whatsappTitle: "WhatsApp",
    whatsappText: "The fastest way to reach us. Real people, real answers.",
    whatsappCta: "Chat on WhatsApp",
    emailTitle: "Email",
    emailText: "For briefs, documents and formal inquiries.",
    responseNote: "We usually respond within a few hours, in Arabic, English or Turkish.",
  },
  langNames: { en: "English", ar: "العربية", tr: "Türkçe" },
};

const ar: Dictionary = {
  meta: {
    title: "بيغر — وكالة نمو إبداعية",
    description:
      "بيغر تبني منظومات رقمية متكاملة — هوية، مواقع، تسويق وأتمتة — مصمّمة لتحقيق نموّ قابل للقياس للأعمال في الشرق الأوسط.",
  },
  nav: {
    home: "الرئيسية",
    services: "الخدمات",
    about: "من نحن",
    contact: "تواصل معنا",
    startProject: "ابدأ مشروعك",
    menu: "القائمة",
    close: "إغلاق",
  },
  hero: {
    badge: "وكالة نمو إبداعية",
    titleA: "النمو ليس حظًا.",
    titleB: "النمو نظام.",
    subtitle:
      "بيغر تصمّم منظومات رقمية متكاملة — هوية، مواقع، تسويق وأتمتة — مبنيّة لتنمية الأعمال في الشرق الأوسط.",
    ctaPrimary: "ابدأ مشروعك",
    ctaSecondary: "استكشف الخدمات",
    scrollCue: "مرّر للأسفل",
  },
  problem: {
    label: "المشكلة",
    title: "معظم الأعمال لا تعاني من مشكلة تسويق، بل من مشكلة نظام.",
    points: [
      {
        title: "موقع يبدو جميلًا",
        text: "— لكنه لا يحوّل الزوّار إلى عملاء.",
      },
      {
        title: "إعلانات تستهلك الميزانية",
        text: "— دون أن تتحوّل إلى نموّ ثابت ومتوقّع.",
      },
      {
        title: "محتوى يُنشر باستمرار",
        text: "— لكنه لا يبني علامة يتذكّرها الناس.",
      },
      {
        title: "أدوات في كل مكان",
        text: "— غير مترابطة، ولا أحد يقيس نتائجها.",
      },
    ],
  },
  shift: {
    label: "التحوّل",
    title: "نحن لا نبيع خدمات. نحن نبني أنظمة نمو.",
    text: "كل جزء من حضورك الرقمي — الهوية، الموقع، الحملات، الأتمتة — يُصمَّم كمنظومة واحدة مترابطة بهدف واحد: نموّ قابل للقياس.",
    pillars: ["استراتيجية", "تصميم", "تقنية", "أتمتة"],
  },
  services: {
    label: "ماذا نفعل",
    title: "شريك واحد. منظومة متكاملة.",
    subtitle: "سبع قدرات مصمّمة لتعمل كنظام واحد حول عملك.",
    explore: "استكشف جميع الخدمات",
    groups: [
      {
        id: "web",
        index: "01",
        title: "تجارب الويب",
        tagline: "مواقع مبنيّة للبيع، لا لمجرّد الوجود.",
        items: [
          "مواقع الشركات",
          "صفحات الهبوط",
          "ووكومرس",
          "شوبيفاي",
          "سلة",
          "زد",
        ],
        description:
          "مواقع ومتاجر إلكترونية عالية الأداء مصمّمة حول التحويل — من منصات الشركات إلى متاجر سلة وزد المبنيّة لسوق الخليج.",
      },
      {
        id: "brand",
        index: "02",
        title: "الهوية التجارية",
        tagline: "هويات تجعل الأعمال لا تُنسى.",
        items: ["الهوية التجارية", "الأنظمة البصرية", "تصميم السوشيال ميديا"],
        description:
          "أنظمة بصرية متكاملة — شعار، خطوط، ألوان، صوت العلامة — مع تصميم سوشيال ميديا يحافظ على فخامة كل نقطة تواصل.",
      },
      {
        id: "ai",
        index: "03",
        title: "الإنتاج بالذكاء الاصطناعي",
        tagline: "صور ومشاهد سينمائية بسرعة البرمجيات.",
        items: ["إنتاج الصور بالذكاء الاصطناعي", "إنتاج الفيديو بالذكاء الاصطناعي"],
        description:
          "صور منتجات ومرئيات حملات وفيديوهات تُنتَج عبر تقنيات الذكاء الاصطناعي — جودة فاخرة دون جداول الإنتاج التقليدية.",
      },
      {
        id: "ads",
        index: "04",
        title: "التسويق بالأداء",
        tagline: "حملات مسؤولة أمام الإيرادات، لا أمام المشاهدات.",
        items: ["الحملات الإعلانية", "إعلانات ميتا", "إعلانات جوجل"],
        description:
          "حملات متكاملة على ميتا وجوجل — استراتيجية وإبداع واستهداف وتحسين مستمر يُقاس بنتائج العمل الحقيقية.",
      },
      {
        id: "automation",
        index: "05",
        title: "الأتمتة",
        tagline: "أنظمة تعمل بينما أنت نائم.",
        items: ["أتمتة التسويق", "أتمتة إدارة العملاء (CRM)", "أتمتة واتساب"],
        description:
          "توجيه العملاء المحتملين، المتابعات، مسارات CRM ورحلات واتساب مؤتمتة بالكامل — حتى لا تضيع أي فرصة بسبب ردّ متأخّر.",
      },
      {
        id: "consulting",
        index: "06",
        title: "استشارات النمو",
        tagline: "استراتيجي داخل عملك.",
        items: ["الاستشارات التسويقية", "استراتيجية النمو"],
        description:
          "استشارات تسويقية واضحة وصادقة — تموضع، قنوات، ميزانيات وأولويات — من شريك مهتمّ فعلًا بنموّك.",
      },
      {
        id: "infrastructure",
        index: "07",
        title: "البنية التحتية للأعمال",
        tagline: "أساسات عمل رقمي عالمي.",
        items: [
          "المساعدة في تأسيس شركة في أمريكا",
          "المساعدة في تأسيس شركة في بريطانيا",
          "المساعدة في بوابات الدفع",
          "البنية الرقمية للأعمال",
        ],
        description:
          "مساعدة في تأسيس الشركات في الولايات المتحدة وبريطانيا، وبوابات الدفع، والبنية الرقمية التي يحتاجها عملك للعمل عالميًا.",
      },
    ],
  },
  process: {
    label: "كيف نعمل",
    title: "منهجية دقيقة. لا تخمين.",
    subtitle: "خمس مراحل، من أول محادثة إلى نموّ قابل للقياس.",
    steps: [
      {
        index: "01",
        title: "نستمع",
        description:
          "نبدأ من عملك لا من خدماتنا: الأهداف، السوق، العملاء، الأرقام.",
      },
      {
        index: "02",
        title: "نشخّص",
        description:
          "نرسم خريطة حضورك الرقمي بالكامل ونحدّد بدقة أين يتسرّب النمو.",
      },
      {
        index: "03",
        title: "نصمّم",
        description:
          "نبني هندسة النظام — هوية، موقع، حملات، أتمتة — كخطة واحدة مترابطة.",
      },
      {
        index: "04",
        title: "ننفّذ",
        description:
          "نصنع كل عنصر بمعيار فاخر ونربط كل الأدوات في منظومة واحدة.",
      },
      {
        index: "05",
        title: "ننمّي",
        description: "نطلق، نقيس، نحسّن ونوسّع — كشريك، لا كمورّد.",
      },
    ],
  },
  trust: {
    label: "لماذا بيغر",
    title: "نعمل كشريك. لا كمورّد.",
    points: [
      {
        title: "أنظمة، لا خدمات",
        text: "لا نسلّم مخرجات متفرّقة. كل ما نبنيه يتّصل بمنظومة نموّ واحدة.",
      },
      {
        title: "صفر قوالب",
        text: "كل هوية وموقع وحملة تُصمَّم من الصفر، حول عملك أنت.",
      },
      {
        title: "وضوح، لا مصطلحات",
        text: "ستفهم دائمًا ماذا نفعل، ولماذا، وما الذي حقّقه لك.",
      },
      {
        title: "سوقك هو منطقتنا",
        text: "نعمل في الخليج وبلاد الشام وتركيا — بالعربية والإنجليزية والتركية.",
      },
    ],
  },
  cta: {
    title: "النموّ يبدأ بمحادثة واحدة.",
    text: "حدّثنا عن عملك، وسنخبرك بالضبط كيف سننمّيه — بوضوح وصدق ومجانًا.",
    whatsapp: "تحدّث عبر واتساب",
    secondary: "استكشف الخدمات",
  },
  footer: {
    tagline: "استراتيجية إبداعية وتسويق رقمي للأعمال التي تريد أن تنمو.",
    navTitle: "تصفّح",
    languagesTitle: "اللغات",
    contactTitle: "تواصل",
    rights: "جميع الحقوق محفوظة.",
    basedIn: "نخدم الشرق الأوسط — عن بُعد، في كل مكان.",
  },
  servicesPage: {
    title: "كل ما يحتاجه نموّك. في مكان واحد.",
    subtitle:
      "سبع قدرات مصمّمة لتعمل كنظام واحد مترابط — حتى لا تضطر لتنسيق خمسة مورّدين بعد اليوم.",
    ctaLine: "لست متأكدًا من أين تبدأ؟ ابدأ بمحادثة.",
  },
  aboutPage: {
    title: "نحن بيغر.",
    subtitle: "وكالة نموّ إبداعية للشرق الأوسط.",
    missionLabel: "رسالتنا",
    missionTitle: "نساعد الأعمال على النمو عبر أنظمة إبداعية.",
    missionText:
      "نحن لا نبيع مواقع. لا نبيع إعلانات. لا نبيع تصميمًا. نحن نبني منظومات رقمية متكاملة — وكل مشروع يجب أن يحقّق نموًا قابلًا للقياس.",
    beliefsLabel: "بماذا نؤمن",
    beliefs: [
      {
        title: "النمو هندسة",
        text: "النمو المتوقَّع يأتي من أنظمة مترابطة — لا من تكتيكات متفرّقة.",
      },
      {
        title: "الفخامة انضباط",
        text: "الفخامة تُصنع بالدقة والمساحة، لا بالضجيج.",
      },
      {
        title: "شركاء، لا مشاريع",
        text: "ننجح عندما تنمو أرقامك — لذلك نتصرّف كجزء من فريقك.",
      },
      {
        title: "التقنية تخدم القصة",
        text: "الذكاء الاصطناعي والأتمتة والبرمجة تُضخّم قصة علامة واضحة — ولا تحلّ محلّها.",
      },
    ],
    marketsLabel: "أين نعمل",
    marketsTitle: "منطقة واحدة. ثلاث لغات. عن بُعد بالكامل.",
    markets: [
      "السعودية",
      "الإمارات",
      "قطر",
      "الكويت",
      "عُمان",
      "البحرين",
      "الأردن",
      "العراق",
      "تركيا",
    ],
  },
  contactPage: {
    title: "لنتحدث عن نموّ عملك.",
    subtitle:
      "محادثة واحدة تكفي لتعرف إن كنّا الشريك المناسب. بلا ضغط، وبلا نصوص مبيعات.",
    whatsappTitle: "واتساب",
    whatsappText: "أسرع طريقة للوصول إلينا. أشخاص حقيقيون، وإجابات حقيقية.",
    whatsappCta: "تحدّث عبر واتساب",
    emailTitle: "البريد الإلكتروني",
    emailText: "للملفات والمستندات والاستفسارات الرسمية.",
    responseNote: "نردّ عادة خلال ساعات قليلة — بالعربية أو الإنجليزية أو التركية.",
  },
  langNames: { en: "English", ar: "العربية", tr: "Türkçe" },
};

const tr: Dictionary = {
  meta: {
    title: "BAIGR — Yaratıcı Büyüme Ajansı",
    description:
      "BAIGR; marka, web, pazarlama ve otomasyonu tek bir dijital ekosistem olarak tasarlar — Orta Doğu'daki işletmeler için ölçülebilir büyüme üretir.",
  },
  nav: {
    home: "Ana Sayfa",
    services: "Hizmetler",
    about: "Hakkımızda",
    contact: "İletişim",
    startProject: "Projeni Başlat",
    menu: "Menü",
    close: "Kapat",
  },
  hero: {
    badge: "Yaratıcı Büyüme Ajansı",
    titleA: "Büyüme şans değildir.",
    titleB: "Büyüme bir sistemdir.",
    subtitle:
      "BAIGR; kimlik, web, pazarlama ve otomasyonu tek bir dijital ekosistem olarak tasarlar — Orta Doğu'daki işletmeleri büyütmek için.",
    ctaPrimary: "Projeni Başlat",
    ctaSecondary: "Hizmetleri Keşfet",
    scrollCue: "Keşfetmek için kaydır",
  },
  problem: {
    label: "Problem",
    title: "Çoğu işletmenin pazarlama sorunu yok. Sistem sorunu var.",
    points: [
      {
        title: "Güzel görünen bir web sitesi",
        text: "— ama ziyaretçileri müşteriye dönüştürmüyor.",
      },
      {
        title: "Sürekli harcayan reklamlar",
        text: "— ama öngörülebilir bir büyümeye dönüşmüyor.",
      },
      {
        title: "Düzenli paylaşılan içerik",
        text: "— ama akılda kalan bir marka inşa etmiyor.",
      },
      {
        title: "Her yerde araçlar",
        text: "— hiçbiri birbirine bağlı değil, kimse ölçmüyor.",
      },
    ],
  },
  shift: {
    label: "Dönüşüm",
    title: "Hizmet satmıyoruz. Büyüme sistemleri kuruyoruz.",
    text: "Dijital varlığınızın her parçası — kimlik, web sitesi, kampanyalar, otomasyon — tek bir bağlı ekosistem olarak tasarlanır. Tek görevi: ölçülebilir büyüme.",
    pillars: ["Strateji", "Tasarım", "Teknoloji", "Otomasyon"],
  },
  services: {
    label: "Ne Yapıyoruz",
    title: "Tek partner. Eksiksiz bir ekosistem.",
    subtitle:
      "İşinizin etrafında tek bir sistem gibi çalışan yedi yetkinlik.",
    explore: "Tüm hizmetleri keşfet",
    groups: [
      {
        id: "web",
        index: "01",
        title: "Web Deneyimleri",
        tagline: "Var olmak için değil, satmak için tasarlanan siteler.",
        items: [
          "Kurumsal Web Siteleri",
          "Landing Page",
          "WooCommerce",
          "Shopify",
          "Salla",
          "Zid",
        ],
        description:
          "Dönüşüm odaklı, yüksek performanslı web siteleri ve e-ticaret mağazaları — kurumsal platformlardan Körfez pazarı için Salla ve Zid mağazalarına kadar.",
      },
      {
        id: "brand",
        index: "02",
        title: "Marka Kimliği",
        tagline: "İşletmeleri unutulmaz kılan kimlikler.",
        items: ["Marka Kimliği", "Görsel Sistemler", "Sosyal Medya Tasarımı"],
        description:
          "Logo, tipografi, renk ve marka sesi dahil eksiksiz görsel sistemler — her temas noktasını premium ve tutarlı tutan sosyal medya tasarımıyla birlikte.",
      },
      {
        id: "ai",
        index: "03",
        title: "AI Prodüksiyon",
        tagline: "Yazılım hızında sinematik görseller.",
        items: ["AI Görsel Üretimi", "AI Video Üretimi"],
        description:
          "AI üretim hatlarıyla hazırlanan ürün görselleri, kampanya görselleri ve videolar — geleneksel prodüksiyon takvimi olmadan premium kalite.",
      },
      {
        id: "ads",
        index: "04",
        title: "Performans Pazarlama",
        tagline: "Gösterime değil, ciroya hesap veren kampanyalar.",
        items: ["Reklam Kampanyaları", "Meta Ads", "Google Ads"],
        description:
          "Meta ve Google'da tam huni kampanyalar — strateji, kreatif, hedefleme ve gerçek iş sonuçlarıyla ölçülen optimizasyon.",
      },
      {
        id: "automation",
        index: "05",
        title: "Otomasyon",
        tagline: "Siz uyurken çalışan sistemler.",
        items: [
          "Pazarlama Otomasyonu",
          "CRM Otomasyonu",
          "WhatsApp Otomasyonu",
        ],
        description:
          "Lead yönlendirme, takipler, CRM akışları ve WhatsApp yolculukları uçtan uca otomatik — hiçbir fırsat geç bir yanıt yüzünden kaybolmasın.",
      },
      {
        id: "consulting",
        index: "06",
        title: "Büyüme Danışmanlığı",
        tagline: "İşletmenizin içinde bir stratejist.",
        items: ["Pazarlama Danışmanlığı", "Büyüme Stratejisi"],
        description:
          "Net ve dürüst pazarlama danışmanlığı — konumlandırma, kanallar, bütçeler ve öncelikler — büyümenize gerçekten yatırım yapan bir partnerden.",
      },
      {
        id: "infrastructure",
        index: "07",
        title: "İş Altyapısı",
        tagline: "Küresel bir dijital işletmenin temelleri.",
        items: [
          "ABD Şirket Kuruluşu Desteği",
          "İngiltere Şirket Kuruluşu Desteği",
          "Ödeme Altyapısı Desteği",
          "Dijital İş Altyapısı",
        ],
        description:
          "ABD ve İngiltere'de şirket kuruluşu, ödeme altyapıları ve işletmenizin küresel çalışması için gereken dijital altyapı desteği.",
      },
    ],
  },
  process: {
    label: "Nasıl Çalışıyoruz",
    title: "Net bir süreç. Tahmin yok.",
    subtitle: "İlk görüşmeden ölçülebilir büyümeye beş aşama.",
    steps: [
      {
        index: "01",
        title: "Dinliyoruz",
        description:
          "Hizmetlerimizden değil, işinizden başlıyoruz: hedefler, pazar, müşteriler, rakamlar.",
      },
      {
        index: "02",
        title: "Teşhis Ediyoruz",
        description:
          "Tüm dijital varlığınızı haritalayıp büyümenin tam olarak nereden sızdığını buluyoruz.",
      },
      {
        index: "03",
        title: "Tasarlıyoruz",
        description:
          "Sistemi kurguluyoruz — marka, web, kampanyalar, otomasyon — tek bir bağlı plan olarak.",
      },
      {
        index: "04",
        title: "İnşa Ediyoruz",
        description:
          "Her varlığı premium standartta üretip tüm araçları tek ekosisteme bağlıyoruz.",
      },
      {
        index: "05",
        title: "Büyütüyoruz",
        description:
          "Yayınlıyor, ölçüyor, optimize ediyor ve ölçekliyoruz — tedarikçi gibi değil, partner gibi.",
      },
    ],
  },
  trust: {
    label: "Neden BAIGR",
    title: "Tedarikçi gibi değil. Partner gibi.",
    points: [
      {
        title: "Hizmet değil, sistem",
        text: "Asla kopuk işler teslim etmeyiz. Ürettiğimiz her şey tek bir büyüme ekosistemine bağlanır.",
      },
      {
        title: "Sıfır şablon",
        text: "Her kimlik, site ve kampanya sıfırdan, sizin işiniz etrafında tasarlanır.",
      },
      {
        title: "Jargon değil, netlik",
        text: "Ne yaptığımızı, neden yaptığımızı ve ne kazandırdığını her zaman bilirsiniz.",
      },
      {
        title: "Pazarınız, bölgemiz",
        text: "Körfez, Levant ve Türkiye'de çalışıyoruz — Arapça, İngilizce ve Türkçe.",
      },
    ],
  },
  cta: {
    title: "Büyüme tek bir konuşmayla başlar.",
    text: "Bize işinizi anlatın. Onu nasıl büyüteceğimizi net, dürüst ve ücretsiz şekilde söyleyelim.",
    whatsapp: "WhatsApp'tan Yaz",
    secondary: "Hizmetleri Keşfet",
  },
  footer: {
    tagline: "Büyümek isteyen işletmeler için yaratıcı strateji ve dijital pazarlama.",
    navTitle: "Keşfet",
    languagesTitle: "Diller",
    contactTitle: "İletişim",
    rights: "Tüm hakları saklıdır.",
    basedIn: "Orta Doğu'ya hizmet veriyoruz — uzaktan, her yerde.",
  },
  servicesPage: {
    title: "Büyümenizin ihtiyacı olan her şey. Tek yerde.",
    subtitle:
      "Tek bir bağlı sistem gibi çalışan yedi yetkinlik — böylece bir daha beş tedarikçiyi koordine etmek zorunda kalmazsınız.",
    ctaLine: "Nereden başlayacağınızdan emin değil misiniz? Bir konuşmayla başlayın.",
  },
  aboutPage: {
    title: "Biz BAIGR'ız.",
    subtitle: "Orta Doğu için yaratıcı bir büyüme ajansı.",
    missionLabel: "Misyonumuz",
    missionTitle: "İşletmeleri yaratıcı sistemlerle büyütüyoruz.",
    missionText:
      "Web sitesi satmıyoruz. Reklam satmıyoruz. Tasarım satmıyoruz. Eksiksiz dijital ekosistemler kuruyoruz — ve her proje ölçülebilir bir büyüme üretmek zorunda.",
    beliefsLabel: "Neye İnanıyoruz",
    beliefs: [
      {
        title: "Büyüme mühendisliktir",
        text: "Öngörülebilir büyüme bağlı sistemlerden gelir — kopuk taktiklerden değil.",
      },
      {
        title: "Premium, sadeliktir",
        text: "Lüks; hassasiyet ve boşlukla yaratılır, asla gürültüyle değil.",
      },
      {
        title: "Proje değil, partner",
        text: "Rakamlarınız büyüdüğünde kazanırız — bu yüzden ekibinizin parçası gibi çalışırız.",
      },
      {
        title: "Teknoloji hikâyeye hizmet eder",
        text: "AI, otomasyon ve kod; net bir marka hikâyesini güçlendirmek için var — onun yerine geçmek için değil.",
      },
    ],
    marketsLabel: "Nerede Çalışıyoruz",
    marketsTitle: "Tek bölge. Üç dil. Tamamen uzaktan.",
    markets: [
      "Suudi Arabistan",
      "Birleşik Arap Emirlikleri",
      "Katar",
      "Kuveyt",
      "Umman",
      "Bahreyn",
      "Ürdün",
      "Irak",
      "Türkiye",
    ],
  },
  contactPage: {
    title: "Büyümenizi konuşalım.",
    subtitle:
      "Doğru partner olup olmadığımızı anlamak için tek bir görüşme yeter. Baskı yok, satış senaryosu yok.",
    whatsappTitle: "WhatsApp",
    whatsappText: "Bize ulaşmanın en hızlı yolu. Gerçek insanlar, gerçek yanıtlar.",
    whatsappCta: "WhatsApp'tan Yaz",
    emailTitle: "E-posta",
    emailText: "Brief'ler, dokümanlar ve resmî talepler için.",
    responseNote:
      "Genellikle birkaç saat içinde yanıtlıyoruz — Arapça, İngilizce veya Türkçe.",
  },
  langNames: { en: "English", ar: "العربية", tr: "Türkçe" },
};

const dictionaries: Record<Locale, Dictionary> = { en, ar, tr };

export function getDictionary(locale: Locale): Dictionary {
  return dictionaries[locale];
}
