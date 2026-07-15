export type Locale = "en" | "ar" | "tr";

export const locales: Locale[] = ["en", "ar", "tr"];

export interface ServiceItem {
  title: string;
  desc: string;
}

export interface Dictionary {
  nav: {
    about: string;
    services: string;
    work: string;
    process: string;
    faq: string;
    cta: string;
  };
  hero: {
    eyebrow: string;
    titlePre: string;
    titleAccent: string;
    titlePost: string;
    sub: string;
    ctaPrimary: string;
    ctaSecondary: string;
    scroll: string;
    ticker: string[];
  };
  trusted: {
    label: string;
    brands: string[];
  };
  about: {
    eyebrow: string;
    title: string;
    lead: string;
    body: string;
    points: { title: string; desc: string }[];
  };
  services: {
    eyebrow: string;
    title: string;
    lead: string;
    groups: { name: string; items: ServiceItem[] }[];
  };
  portfolio: {
    eyebrow: string;
    title: string;
    lead: string;
    projects: { name: string; tag: string; result: string }[];
    note: string;
  };
  cases: {
    eyebrow: string;
    title: string;
    lead: string;
    items: {
      sector: string;
      challenge: string;
      play: string;
      metric: string;
      metricLabel: string;
    }[];
    labels: { challenge: string; play: string; result: string };
  };
  process: {
    eyebrow: string;
    title: string;
    lead: string;
    steps: { title: string; desc: string }[];
  };
  why: {
    eyebrow: string;
    title: string;
    lead: string;
    reasons: { title: string; desc: string }[];
  };
  testimonials: {
    eyebrow: string;
    title: string;
    quotes: { quote: string; name: string; role: string }[];
  };
  faq: {
    eyebrow: string;
    title: string;
    items: { q: string; a: string }[];
  };
  contact: {
    eyebrow: string;
    titlePre: string;
    titleAccent: string;
    sub: string;
    cta: string;
    or: string;
    emailLabel: string;
  };
  footer: {
    tagline: string;
    nav: string;
    talk: string;
    rights: string;
  };
  whatsapp: string;
}

const en: Dictionary = {
  nav: {
    about: "About",
    services: "Services",
    work: "Work",
    process: "Process",
    faq: "FAQ",
    cta: "Start a project",
  },
  hero: {
    eyebrow: "BAIGR · AI Growth Agency",
    titlePre: "We turn attention into ",
    titleAccent: "revenue",
    titlePost: ".",
    sub: "AI creative, precision advertising and automation — one senior team building growth for brands that refuse to be ordinary.",
    ctaPrimary: "Start your growth",
    ctaSecondary: "See the work",
    scroll: "Scroll",
    ticker: ["Growth", "نمو", "Büyüme"],
  },
  trusted: {
    label: "Brands growing with us",
    brands: ["NORA", "ATLAS", "VELA", "KAYRA", "ORBIT", "MIRA", "LUNE", "ASYA"],
  },
  about: {
    eyebrow: "Who we are",
    title: "A growth partner, not a vendor.",
    lead: "BAIGR is a growth agency built for the AI era — strategy, creative and media under one roof.",
    body: "We are a small senior team that treats your budget like our own. Every campaign starts with your numbers, not our portfolio. AI does the heavy lifting; human taste makes the call. That mix is why our clients stay.",
    points: [
      {
        title: "AI-native, human-led",
        desc: "We use AI where it multiplies output — and people where judgement matters.",
      },
      {
        title: "Revenue over vanity",
        desc: "Likes don't pay salaries. We optimise for sales, leads and lifetime value.",
      },
      {
        title: "Three markets, one team",
        desc: "Arabic, English and Turkish natively — copy that sells in each culture, not translations.",
      },
    ],
  },
  services: {
    eyebrow: "What we do",
    title: "Everything growth needs, under one roof.",
    lead: "Five disciplines that feed each other. Creative fuels ads, ads feed data, data trains automation — the loop compounds.",
    groups: [
      {
        name: "Create",
        items: [
          {
            title: "AI Image Creation",
            desc: "Scroll-stopping product shots and brand visuals, generated and art-directed in days, not weeks.",
          },
          {
            title: "AI Video Production",
            desc: "Ads, reels and brand films powered by AI pipelines — cinema feel at a fraction of the cost.",
          },
        ],
      },
      {
        name: "Advertise",
        items: [
          {
            title: "Meta Advertising",
            desc: "Full-funnel Facebook & Instagram campaigns engineered around ROAS, not reach.",
          },
          {
            title: "Google Advertising",
            desc: "Search, Shopping and YouTube — capturing demand at the exact moment it exists.",
          },
          {
            title: "TikTok Advertising",
            desc: "Native-feeling creative and spark ads that ride the algorithm instead of fighting it.",
          },
          {
            title: "Snapchat Advertising",
            desc: "The under-priced channel in MENA — we make it a quiet growth engine.",
          },
        ],
      },
      {
        name: "Build",
        items: [
          {
            title: "Professional Websites",
            desc: "Fast, beautiful sites that make visitors trust you before they've read a word.",
          },
          {
            title: "E-commerce Development",
            desc: "Stores engineered for conversion — checkout flows measured in seconds, not clicks.",
          },
        ],
      },
      {
        name: "Automate",
        items: [
          {
            title: "Marketing Automation",
            desc: "Follow-ups, funnels and reporting that run while you sleep.",
          },
          {
            title: "AI Chatbots",
            desc: "Assistants that answer in your voice, qualify leads and close simple sales 24/7.",
          },
        ],
      },
      {
        name: "Advise",
        items: [
          {
            title: "Marketing Consulting",
            desc: "A senior brain on your side of the table — audits, strategy and honest answers.",
          },
        ],
      },
    ],
  },
  portfolio: {
    eyebrow: "Selected work",
    title: "Proof, not promises.",
    lead: "A few of the brands we've helped grow. Full case decks available on request.",
    projects: [
      { name: "Ayla Beauty", tag: "E-commerce · Skincare", result: "+212% revenue in 6 months" },
      { name: "Nomad Coffee", tag: "Brand · Meta Ads", result: "3.4× return on ad spend" },
      { name: "Kavra Home", tag: "Website · Google Ads", result: "2.1× conversion rate" },
      { name: "Pulse Fitness", tag: "TikTok · Automation", result: "18,000 leads in one quarter" },
    ],
    note: "Client names anonymised where required.",
  },
  cases: {
    eyebrow: "Case studies",
    title: "How the numbers moved.",
    lead: "Three plays, three markets, one method: find the constraint, remove it, scale what works.",
    items: [
      {
        sector: "Skincare e-commerce · KSA",
        challenge: "Great product, flat sales — ads were paying for traffic that never converted.",
        play: "Rebuilt the store around one hero product, AI-generated 40 creative variants, let Meta find the winners.",
        metric: "+212%",
        metricLabel: "revenue in 6 months",
      },
      {
        sector: "Real estate developer · Türkiye",
        challenge: "Leads cost a fortune and sales wasted hours on unqualified calls.",
        play: "AI chatbot qualified every lead in Arabic, Turkish and English before a human ever picked up the phone.",
        metric: "−38%",
        metricLabel: "cost per qualified lead",
      },
      {
        sector: "Restaurant group · UAE",
        challenge: "Full dining rooms on weekends, empty ones midweek.",
        play: "TikTok + Snapchat geo-campaigns with offer automation tied to the quiet hours.",
        metric: "5.1×",
        metricLabel: "return on ad spend",
      },
    ],
    labels: { challenge: "The problem", play: "The play", result: "The result" },
  },
  process: {
    eyebrow: "How we work",
    title: "Four steps. No mystery.",
    lead: "You'll always know what we're doing, why, and what it returned.",
    steps: [
      {
        title: "Listen",
        desc: "We start with your numbers, market and margins — not a pitch deck. One week, one clear diagnosis.",
      },
      {
        title: "Design the play",
        desc: "A growth plan with targets you can hold us to: channels, creative, budget, timeline.",
      },
      {
        title: "Launch",
        desc: "Creative, campaigns and builds go live fast — then we watch the data like hawks.",
      },
      {
        title: "Scale",
        desc: "Double down on what works, kill what doesn't, report weekly in plain language.",
      },
    ],
  },
  why: {
    eyebrow: "Why BAIGR",
    title: "The unfair advantages.",
    lead: "What you actually get when you work with us.",
    reasons: [
      {
        title: "AI-native from day one",
        desc: "We don't bolt AI on — our whole pipeline is built around it. You get 10× the creative output at the same budget.",
      },
      {
        title: "Senior hands only",
        desc: "No juniors learning on your money. The people in the pitch are the people doing the work.",
      },
      {
        title: "Speed as a habit",
        desc: "First campaigns live within days. Momentum is a strategy.",
      },
      {
        title: "Radical transparency",
        desc: "Live dashboards, weekly summaries, and the truth even when it's uncomfortable.",
      },
      {
        title: "One roof, zero friction",
        desc: "Creative, media, web and automation in one team — no agencies blaming each other.",
      },
      {
        title: "We win when you win",
        desc: "Performance-linked pricing available. Our incentive is your growth, literally.",
      },
    ],
  },
  testimonials: {
    eyebrow: "What clients say",
    title: "Trust is earned in the numbers.",
    quotes: [
      {
        quote:
          "They rebuilt our store and doubled revenue in a quarter. First agency that talked margins with me instead of impressions.",
        name: "Sara A.",
        role: "Founder, skincare brand",
      },
      {
        quote:
          "The chatbot alone saved my sales team fifteen hours a week. Everything they promised showed up in the dashboard.",
        name: "Mehmet K.",
        role: "Sales director, real estate",
      },
      {
        quote:
          "Fast, honest, and genuinely creative. Our TikTok went from dead weight to our best channel.",
        name: "Omar R.",
        role: "Marketing lead, F&B group",
      },
    ],
  },
  faq: {
    eyebrow: "Questions",
    title: "Asked often, answered honestly.",
    items: [
      {
        q: "How fast will we see results?",
        a: "Ad campaigns produce signal within 2–4 weeks; meaningful revenue movement typically lands within the first quarter. Websites and automation deliver value from day one. We'll give you a realistic timeline in the first call — and hold ourselves to it.",
      },
      {
        q: "What budgets do you work with?",
        a: "Most of our clients invest between $2,000 and $50,000 monthly across ads and services. What matters more than size is intent: if you want growth and can act on it, we can build a plan that fits.",
      },
      {
        q: "Which markets do you cover?",
        a: "We operate natively in Arabic, English and Turkish — Gulf, Levant, Türkiye, Europe and North America. Same team, three cultures, no translation layer.",
      },
      {
        q: "What makes BAIGR different from other agencies?",
        a: "Three things: AI-native production (10× the creative for the same budget), senior-only staffing, and reporting tied to revenue rather than reach. We're happy to prove all three in a pilot month.",
      },
      {
        q: "How do we start?",
        a: "One WhatsApp message. We'll book a free 30-minute call, audit what you have, and send a plan with numbers within a week. No commitment until you see it.",
      },
      {
        q: "How will we communicate?",
        a: "A dedicated WhatsApp group, a live results dashboard, and a weekly summary in plain language. You'll never chase us for an update.",
      },
    ],
  },
  contact: {
    eyebrow: "Next step",
    titlePre: "Ready to grow ",
    titleAccent: "on purpose?",
    sub: "Tell us where you are and where you want to be. We'll bring the map — first call is free, no strings.",
    cta: "Chat on WhatsApp",
    or: "or",
    emailLabel: "Email us",
  },
  footer: {
    tagline: "Growth, engineered with AI.",
    nav: "Explore",
    talk: "Talk to us",
    rights: "All rights reserved.",
  },
  whatsapp: "Chat with BAIGR on WhatsApp",
};

const ar: Dictionary = {
  nav: {
    about: "من نحن",
    services: "خدماتنا",
    work: "أعمالنا",
    process: "منهجيتنا",
    faq: "الأسئلة",
    cta: "ابدأ مشروعك",
  },
  hero: {
    eyebrow: "بايقر · وكالة نمو بالذكاء الاصطناعي",
    titlePre: "نحوّل الانتباه إلى ",
    titleAccent: "إيرادات",
    titlePost: ".",
    sub: "إبداع بالذكاء الاصطناعي، إعلانات دقيقة، وأتمتة ذكية — فريق واحد من الخبراء يبني النمو لعلامات ترفض أن تكون عادية.",
    ctaPrimary: "ابدأ نموّك الآن",
    ctaSecondary: "شاهد أعمالنا",
    scroll: "مرّر للأسفل",
    ticker: ["نمو", "Growth", "Büyüme"],
  },
  trusted: {
    label: "علامات تنمو معنا",
    brands: ["NORA", "ATLAS", "VELA", "KAYRA", "ORBIT", "MIRA", "LUNE", "ASYA"],
  },
  about: {
    eyebrow: "من نحن",
    title: "شريك نمو، لا مجرّد مورّد.",
    lead: "بايقر وكالة نمو مبنية لعصر الذكاء الاصطناعي — الاستراتيجية والإبداع والإعلانات تحت سقف واحد.",
    body: "نحن فريق صغير من الخبراء نتعامل مع ميزانيتك كأنها ميزانيتنا. كل حملة تبدأ من أرقامك أنت، لا من ملف أعمالنا. الذكاء الاصطناعي يقوم بالعمل الثقيل، والذوق البشري يتخذ القرار — وهذا المزيج هو سبب بقاء عملائنا معنا.",
    points: [
      {
        title: "ذكاء اصطناعي بقيادة بشرية",
        desc: "نستخدم الذكاء الاصطناعي حيث يضاعف الإنتاج، والبشر حيث يُحسم القرار.",
      },
      {
        title: "الإيرادات قبل الإعجابات",
        desc: "اللايكات لا تدفع الرواتب. نحسّن للمبيعات والعملاء المحتملين والقيمة طويلة الأمد.",
      },
      {
        title: "ثلاثة أسواق، فريق واحد",
        desc: "العربية والإنجليزية والتركية بلسان أهلها — نصوص تبيع في كل ثقافة، لا ترجمات.",
      },
    ],
  },
  services: {
    eyebrow: "ماذا نفعل",
    title: "كل ما يحتاجه النمو، تحت سقف واحد.",
    lead: "خمسة تخصصات يغذّي بعضها بعضاً: الإبداع يغذّي الإعلانات، والإعلانات تولّد البيانات، والبيانات تدرّب الأتمتة — والدورة تتضاعف.",
    groups: [
      {
        name: "الإبداع",
        items: [
          {
            title: "صناعة الصور بالذكاء الاصطناعي",
            desc: "صور منتجات ومرئيات علامة توقف التمرير — تُنتج وتُخرَج فنياً في أيام لا أسابيع.",
          },
          {
            title: "إنتاج الفيديو بالذكاء الاصطناعي",
            desc: "إعلانات وريلز وأفلام علامة بخطوط إنتاج ذكية — إحساس سينمائي بجزء من التكلفة.",
          },
        ],
      },
      {
        name: "الإعلانات",
        items: [
          {
            title: "إعلانات ميتا",
            desc: "حملات فيسبوك وإنستقرام بكامل القمع البيعي، مهندَسة حول العائد لا الوصول.",
          },
          {
            title: "إعلانات قوقل",
            desc: "بحث وتسوّق ويوتيوب — نلتقط الطلب في اللحظة التي يولد فيها.",
          },
          {
            title: "إعلانات تيك توك",
            desc: "محتوى يبدو أصلياً وإعلانات سبارك تركب الخوارزمية بدل أن تصارعها.",
          },
          {
            title: "إعلانات سناب شات",
            desc: "القناة الأقل سعراً في الخليج — نحوّلها إلى محرك نمو هادئ.",
          },
        ],
      },
      {
        name: "البناء",
        items: [
          {
            title: "مواقع احترافية",
            desc: "مواقع سريعة وجميلة تجعل الزائر يثق بك قبل أن يقرأ كلمة واحدة.",
          },
          {
            title: "تطوير المتاجر الإلكترونية",
            desc: "متاجر مهندسة للتحويل — إتمام شراء يُقاس بالثواني لا بالنقرات.",
          },
        ],
      },
      {
        name: "الأتمتة",
        items: [
          {
            title: "أتمتة التسويق",
            desc: "متابعات وقنوات بيع وتقارير تعمل وأنت نائم.",
          },
          {
            title: "روبوتات محادثة ذكية",
            desc: "مساعد يجيب بصوت علامتك، يفرز العملاء ويغلق المبيعات البسيطة على مدار الساعة.",
          },
        ],
      },
      {
        name: "الاستشارات",
        items: [
          {
            title: "استشارات تسويقية",
            desc: "عقل خبير يجلس في صفّك من الطاولة — تدقيق واستراتيجية وإجابات صريحة.",
          },
        ],
      },
    ],
  },
  portfolio: {
    eyebrow: "أعمال مختارة",
    title: "أدلة، لا وعود.",
    lead: "بعض العلامات التي ساعدناها على النمو. ملفات الحالة الكاملة متاحة عند الطلب.",
    projects: [
      { name: "آيلا بيوتي", tag: "متجر إلكتروني · عناية بالبشرة", result: "+212% إيرادات في 6 أشهر" },
      { name: "نوماد كوفي", tag: "علامة تجارية · إعلانات ميتا", result: "عائد إنفاق إعلاني 3.4×" },
      { name: "كافرا هوم", tag: "موقع · إعلانات قوقل", result: "معدل تحويل 2.1×" },
      { name: "بَلس فيتنس", tag: "تيك توك · أتمتة", result: "18,000 عميل محتمل في ربع واحد" },
    ],
    note: "أسماء العملاء مموّهة حيث يلزم.",
  },
  cases: {
    eyebrow: "دراسات حالة",
    title: "كيف تحركت الأرقام.",
    lead: "ثلاث خطط، ثلاثة أسواق، منهج واحد: جِد العائق، أزِله، وضاعف ما ينجح.",
    items: [
      {
        sector: "متجر عناية بالبشرة · السعودية",
        challenge: "منتج ممتاز ومبيعات راكدة — الإعلانات تدفع مقابل زيارات لا تتحول إلى شراء.",
        play: "أعدنا بناء المتجر حول منتج بطل واحد، ولّدنا 40 نسخة إعلانية بالذكاء الاصطناعي، وتركنا ميتا تختار الرابح.",
        metric: "+212%",
        metricLabel: "نمو الإيرادات في 6 أشهر",
      },
      {
        sector: "مطوّر عقاري · تركيا",
        challenge: "تكلفة العميل المحتمل باهظة، وفريق المبيعات يهدر ساعات على مكالمات غير جادة.",
        play: "روبوت محادثة ذكي يفرز كل عميل بالعربية والتركية والإنجليزية قبل أن يرفع الإنسان السماعة.",
        metric: "−38%",
        metricLabel: "تكلفة العميل المؤهَّل",
      },
      {
        sector: "مجموعة مطاعم · الإمارات",
        challenge: "قاعات ممتلئة في نهاية الأسبوع، فارغة في منتصفه.",
        play: "حملات جغرافية على تيك توك وسناب شات مع عروض مؤتمتة مرتبطة بساعات الهدوء.",
        metric: "5.1×",
        metricLabel: "عائد الإنفاق الإعلاني",
      },
    ],
    labels: { challenge: "المشكلة", play: "الخطة", result: "النتيجة" },
  },
  process: {
    eyebrow: "كيف نعمل",
    title: "أربع خطوات. بلا غموض.",
    lead: "ستعرف دائماً ماذا نفعل، ولماذا، وماذا أعاد عليك.",
    steps: [
      {
        title: "نستمع",
        desc: "نبدأ من أرقامك وسوقك وهوامشك — لا من عرض تقديمي. أسبوع واحد، تشخيص واضح.",
      },
      {
        title: "نرسم الخطة",
        desc: "خطة نمو بأهداف تحاسبنا عليها: القنوات، الإبداع، الميزانية، والجدول الزمني.",
      },
      {
        title: "نطلق",
        desc: "الإبداع والحملات والمواقع تنطلق بسرعة — ثم نراقب البيانات بعين الصقر.",
      },
      {
        title: "نضاعف",
        desc: "نضاعف ما ينجح، نوقف ما لا ينجح، ونرسل تقريراً أسبوعياً بلغة مفهومة.",
      },
    ],
  },
  why: {
    eyebrow: "لماذا بايقر",
    title: "أفضليات غير عادلة.",
    lead: "ما الذي تحصل عليه فعلاً حين تعمل معنا.",
    reasons: [
      {
        title: "ذكاء اصطناعي من اليوم الأول",
        desc: "لا نضيف الذكاء الاصطناعي كإكسسوار — خط إنتاجنا كله مبني عليه. إنتاج إبداعي مضاعف عشر مرات بنفس الميزانية.",
      },
      {
        title: "خبراء فقط",
        desc: "لا مبتدئين يتعلمون على حسابك. من تقابلهم في العرض هم من ينفذون العمل.",
      },
      {
        title: "السرعة عادة",
        desc: "أول حملاتك تنطلق خلال أيام. الزخم استراتيجية.",
      },
      {
        title: "شفافية جذرية",
        desc: "لوحات نتائج مباشرة، ملخصات أسبوعية، والحقيقة حتى عندما تكون غير مريحة.",
      },
      {
        title: "سقف واحد، صفر احتكاك",
        desc: "الإبداع والإعلانات والويب والأتمتة في فريق واحد — لا وكالات تتبادل اللوم.",
      },
      {
        title: "نربح حين تربح",
        desc: "تسعير مرتبط بالأداء متاح. مصلحتنا في نموّك، حرفياً.",
      },
    ],
  },
  testimonials: {
    eyebrow: "ماذا يقول عملاؤنا",
    title: "الثقة تُكتسب بالأرقام.",
    quotes: [
      {
        quote:
          "أعادوا بناء متجرنا وضاعفوا الإيرادات في ربع واحد. أول وكالة تتحدث معي عن الهوامش لا عن مرات الظهور.",
        name: "سارة أ.",
        role: "مؤسِّسة علامة عناية بالبشرة",
      },
      {
        quote:
          "روبوت المحادثة وحده وفّر على فريق مبيعاتي خمس عشرة ساعة أسبوعياً. كل ما وعدوا به ظهر في لوحة النتائج.",
        name: "محمد ك.",
        role: "مدير مبيعات، قطاع عقاري",
      },
      {
        quote:
          "سرعة وصراحة وإبداع حقيقي. تيك توك تحوّل من عبء إلى أفضل قنواتنا.",
        name: "عمر ر.",
        role: "مسؤول تسويق، مجموعة مطاعم",
      },
    ],
  },
  faq: {
    eyebrow: "أسئلة",
    title: "تُسأل كثيراً، نجيب بصدق.",
    items: [
      {
        q: "متى سنرى النتائج؟",
        a: "الحملات الإعلانية تعطي إشارات خلال 2–4 أسابيع، وتحرّك الإيرادات الملموس يأتي عادة خلال الربع الأول. المواقع والأتمتة تضيف قيمة من اليوم الأول. سنعطيك جدولاً واقعياً في أول مكالمة — ونلتزم به.",
      },
      {
        q: "ما الميزانيات التي تعملون معها؟",
        a: "معظم عملائنا يستثمرون بين 2,000 و50,000 دولار شهرياً بين الإعلانات والخدمات. الأهم من الحجم هو الجدية: إن كنت تريد النمو وقادراً على التحرك، سنبني خطة تناسبك.",
      },
      {
        q: "ما الأسواق التي تغطونها؟",
        a: "نعمل بلسان أهل العربية والإنجليزية والتركية — الخليج والشام وتركيا وأوروبا وأمريكا الشمالية. فريق واحد، ثلاث ثقافات، بلا طبقة ترجمة.",
      },
      {
        q: "ما الذي يميز بايقر عن غيرها؟",
        a: "ثلاثة أشياء: إنتاج مبني على الذكاء الاصطناعي (إبداع مضاعف عشر مرات بنفس الميزانية)، فريق خبراء فقط، وتقارير مرتبطة بالإيرادات لا بالوصول. ويسعدنا إثبات الثلاثة في شهر تجريبي.",
      },
      {
        q: "كيف نبدأ؟",
        a: "رسالة واتساب واحدة. نحجز مكالمة مجانية لثلاثين دقيقة، نراجع ما لديك، ونرسل خطة بالأرقام خلال أسبوع. لا التزام حتى تراها.",
      },
      {
        q: "كيف سيكون التواصل؟",
        a: "مجموعة واتساب مخصصة، لوحة نتائج مباشرة، وملخص أسبوعي بلغة مفهومة. لن تلاحقنا يوماً لتعرف المستجدات.",
      },
    ],
  },
  contact: {
    eyebrow: "الخطوة التالية",
    titlePre: "جاهز تنمو ",
    titleAccent: "عن قصد؟",
    sub: "أخبرنا أين أنت وأين تريد أن تصل، ونحن نحضر الخريطة — المكالمة الأولى مجانية وبلا شروط.",
    cta: "راسلنا على واتساب",
    or: "أو",
    emailLabel: "راسلنا بالبريد",
  },
  footer: {
    tagline: "نموّ مهندَس بالذكاء الاصطناعي.",
    nav: "استكشف",
    talk: "تواصل معنا",
    rights: "جميع الحقوق محفوظة.",
  },
  whatsapp: "تحدث مع بايقر على واتساب",
};

const tr: Dictionary = {
  nav: {
    about: "Hakkımızda",
    services: "Hizmetler",
    work: "İşlerimiz",
    process: "Süreç",
    faq: "SSS",
    cta: "Proje başlat",
  },
  hero: {
    eyebrow: "BAIGR · Yapay Zekâ Büyüme Ajansı",
    titlePre: "İlgiyi ",
    titleAccent: "gelire",
    titlePost: " dönüştürüyoruz.",
    sub: "Yapay zekâ destekli kreatif, hassas reklamcılık ve otomasyon — sıradan olmayı reddeden markalar için büyüme inşa eden tek bir uzman ekip.",
    ctaPrimary: "Büyümeye başla",
    ctaSecondary: "İşlerimizi gör",
    scroll: "Kaydır",
    ticker: ["Büyüme", "Growth", "نمو"],
  },
  trusted: {
    label: "Bizimle büyüyen markalar",
    brands: ["NORA", "ATLAS", "VELA", "KAYRA", "ORBIT", "MIRA", "LUNE", "ASYA"],
  },
  about: {
    eyebrow: "Biz kimiz",
    title: "Tedarikçi değil, büyüme ortağı.",
    lead: "BAIGR, yapay zekâ çağı için kurulmuş bir büyüme ajansı — strateji, kreatif ve medya tek çatı altında.",
    body: "Bütçenize kendi bütçemiz gibi davranan küçük ve kıdemli bir ekibiz. Her kampanya bizim portfolyomuzdan değil, sizin rakamlarınızdan başlar. Ağır işi yapay zekâ yapar, kararı insan zevki verir — müşterilerimizin bizimle kalmasının sebebi bu.",
    points: [
      {
        title: "Yapay zekâ altyapılı, insan liderliğinde",
        desc: "Üretimi katlayan yerde yapay zekâ, kararın önemli olduğu yerde insan.",
      },
      {
        title: "Gösteriş değil, gelir",
        desc: "Beğeniler maaş ödemez. Satış, potansiyel müşteri ve yaşam boyu değer için optimize ederiz.",
      },
      {
        title: "Üç pazar, tek ekip",
        desc: "Arapça, İngilizce ve Türkçe ana dilde — çeviri değil, her kültürde satan metin.",
      },
    ],
  },
  services: {
    eyebrow: "Ne yapıyoruz",
    title: "Büyümenin ihtiyacı olan her şey, tek çatı altında.",
    lead: "Birbirini besleyen beş disiplin: kreatif reklamı besler, reklam veri üretir, veri otomasyonu eğitir — döngü katlanarak büyür.",
    groups: [
      {
        name: "Üret",
        items: [
          {
            title: "Yapay Zekâ ile Görsel Üretimi",
            desc: "Kaydırmayı durduran ürün çekimleri ve marka görselleri — haftalar değil, günler içinde.",
          },
          {
            title: "Yapay Zekâ ile Video Prodüksiyon",
            desc: "Yapay zekâ destekli reklamlar, reels ve marka filmleri — maliyetin çok altında sinema hissi.",
          },
        ],
      },
      {
        name: "Reklam",
        items: [
          {
            title: "Meta Reklamcılığı",
            desc: "Erişime değil ROAS'a göre kurgulanmış, tam huni Facebook ve Instagram kampanyaları.",
          },
          {
            title: "Google Reklamcılığı",
            desc: "Arama, Alışveriş ve YouTube — talebi tam oluştuğu anda yakalarız.",
          },
          {
            title: "TikTok Reklamcılığı",
            desc: "Algoritmayla savaşmak yerine onunla yükselen doğal görünümlü kreatifler ve spark reklamlar.",
          },
          {
            title: "Snapchat Reklamcılığı",
            desc: "Bölgenin en düşük maliyetli kanalı — onu sessiz bir büyüme motoruna çeviririz.",
          },
        ],
      },
      {
        name: "İnşa et",
        items: [
          {
            title: "Profesyonel Web Siteleri",
            desc: "Ziyaretçi tek kelime okumadan size güvensin — hızlı ve etkileyici siteler.",
          },
          {
            title: "E-ticaret Geliştirme",
            desc: "Dönüşüm için mühendislik — tıklamayla değil, saniyeyle ölçülen ödeme akışları.",
          },
        ],
      },
      {
        name: "Otomatikleştir",
        items: [
          {
            title: "Pazarlama Otomasyonu",
            desc: "Siz uyurken çalışan takipler, huniler ve raporlar.",
          },
          {
            title: "Yapay Zekâ Sohbet Botları",
            desc: "Markanızın sesiyle yanıtlayan, müşteri eleyip basit satışları 7/24 kapatan asistanlar.",
          },
        ],
      },
      {
        name: "Danış",
        items: [
          {
            title: "Pazarlama Danışmanlığı",
            desc: "Masanın sizin tarafınızda kıdemli bir akıl — denetim, strateji ve dürüst cevaplar.",
          },
        ],
      },
    ],
  },
  portfolio: {
    eyebrow: "Seçilmiş işler",
    title: "Vaat değil, kanıt.",
    lead: "Büyümesine katkı verdiğimiz markalardan birkaçı. Detaylı vaka dosyaları talep üzerine paylaşılır.",
    projects: [
      { name: "Ayla Beauty", tag: "E-ticaret · Cilt bakımı", result: "6 ayda +212% gelir" },
      { name: "Nomad Coffee", tag: "Marka · Meta Reklam", result: "3.4× reklam harcaması getirisi" },
      { name: "Kavra Home", tag: "Web sitesi · Google Ads", result: "2.1× dönüşüm oranı" },
      { name: "Pulse Fitness", tag: "TikTok · Otomasyon", result: "Bir çeyrekte 18.000 potansiyel müşteri" },
    ],
    note: "Gerekli yerlerde müşteri adları gizlenmiştir.",
  },
  cases: {
    eyebrow: "Vaka çalışmaları",
    title: "Rakamlar nasıl hareket etti.",
    lead: "Üç oyun, üç pazar, tek yöntem: darboğazı bul, kaldır, işe yarayanı ölçekle.",
    items: [
      {
        sector: "Cilt bakımı e-ticareti · Suudi Arabistan",
        challenge: "Harika ürün, duran satış — reklamlar dönüşmeyen trafiğe para ödüyordu.",
        play: "Mağazayı tek kahraman ürün etrafında yeniden kurduk, yapay zekâyla 40 kreatif varyant ürettik, kazananı Meta buldu.",
        metric: "+212%",
        metricLabel: "6 ayda gelir artışı",
      },
      {
        sector: "Gayrimenkul geliştirici · Türkiye",
        challenge: "Potansiyel müşteri maliyeti çok yüksekti; satış ekibi niteliksiz aramalarla saat kaybediyordu.",
        play: "Yapay zekâ botu her adayı Arapça, Türkçe ve İngilizce eledi — telefon insana ancak ondan sonra düştü.",
        metric: "−38%",
        metricLabel: "nitelikli müşteri başına maliyet",
      },
      {
        sector: "Restoran grubu · BAE",
        challenge: "Hafta sonu dolu salonlar, hafta içi boş masalar.",
        play: "Sakin saatlere bağlı otomatik tekliflerle TikTok + Snapchat konum kampanyaları.",
        metric: "5.1×",
        metricLabel: "reklam harcaması getirisi",
      },
    ],
    labels: { challenge: "Sorun", play: "Oyun planı", result: "Sonuç" },
  },
  process: {
    eyebrow: "Nasıl çalışıyoruz",
    title: "Dört adım. Sıfır muamma.",
    lead: "Ne yaptığımızı, neden yaptığımızı ve ne getirdiğini her zaman bileceksiniz.",
    steps: [
      {
        title: "Dinle",
        desc: "Sunumla değil; rakamlarınız, pazarınız ve marjlarınızla başlarız. Bir hafta, net bir teşhis.",
      },
      {
        title: "Oyunu kur",
        desc: "Hesap sorabileceğiniz hedeflerle bir büyüme planı: kanallar, kreatif, bütçe, takvim.",
      },
      {
        title: "Yayına al",
        desc: "Kreatif, kampanya ve siteler hızla canlıya çıkar — sonra veriyi şahin gibi izleriz.",
      },
      {
        title: "Ölçekle",
        desc: "İşe yarayana yüklen, yaramayanı kapat, her hafta sade bir dille raporla.",
      },
    ],
  },
  why: {
    eyebrow: "Neden BAIGR",
    title: "Haksız avantajlar.",
    lead: "Bizimle çalıştığınızda gerçekte ne alırsınız.",
    reasons: [
      {
        title: "İlk günden yapay zekâ altyapısı",
        desc: "Yapay zekâyı sonradan eklemiyoruz — tüm üretim hattımız onun üzerine kurulu. Aynı bütçeyle 10 kat kreatif.",
      },
      {
        title: "Sadece kıdemli eller",
        desc: "Sizin paranızla öğrenen stajyer yok. Sunumdaki insanlar, işi yapan insanlardır.",
      },
      {
        title: "Alışkanlık hâline gelmiş hız",
        desc: "İlk kampanyalar günler içinde yayında. İvme bir stratejidir.",
      },
      {
        title: "Radikal şeffaflık",
        desc: "Canlı panolar, haftalık özetler ve rahatsız edici olsa bile gerçek.",
      },
      {
        title: "Tek çatı, sıfır sürtünme",
        desc: "Kreatif, medya, web ve otomasyon tek ekipte — birbirini suçlayan ajanslar yok.",
      },
      {
        title: "Siz kazanınca biz kazanırız",
        desc: "Performansa bağlı fiyatlandırma mümkün. Motivasyonumuz kelimenin tam anlamıyla sizin büyümeniz.",
      },
    ],
  },
  testimonials: {
    eyebrow: "Müşteriler ne diyor",
    title: "Güven rakamlarla kazanılır.",
    quotes: [
      {
        quote:
          "Mağazamızı yeniden kurdular ve geliri bir çeyrekte ikiye katladılar. Benimle gösterim yerine marj konuşan ilk ajans.",
        name: "Sara A.",
        role: "Kurucu, cilt bakım markası",
      },
      {
        quote:
          "Sadece sohbet botu bile satış ekibime haftada on beş saat kazandırdı. Söz verdikleri her şey panoda göründü.",
        name: "Mehmet K.",
        role: "Satış direktörü, gayrimenkul",
      },
      {
        quote:
          "Hızlı, dürüst ve gerçekten yaratıcı. TikTok'umuz yükten en iyi kanalımıza dönüştü.",
        name: "Omar R.",
        role: "Pazarlama lideri, yeme-içme grubu",
      },
    ],
  },
  faq: {
    eyebrow: "Sorular",
    title: "Sık soruluyor, dürüstçe yanıtlıyoruz.",
    items: [
      {
        q: "Sonuçları ne kadar sürede görürüz?",
        a: "Reklam kampanyaları 2–4 hafta içinde sinyal üretir; gelirde anlamlı hareket genellikle ilk çeyrekte gelir. Web siteleri ve otomasyon ilk günden değer katar. İlk görüşmede gerçekçi bir takvim veririz — ve ona uyarız.",
      },
      {
        q: "Hangi bütçelerle çalışıyorsunuz?",
        a: "Müşterilerimizin çoğu reklam ve hizmetler genelinde aylık 2.000–50.000 dolar arası yatırım yapar. Büyüklükten önemlisi niyettir: büyümek istiyor ve harekete geçebiliyorsanız, size uyan bir plan kurarız.",
      },
      {
        q: "Hangi pazarları kapsıyorsunuz?",
        a: "Arapça, İngilizce ve Türkçede ana dil seviyesinde çalışıyoruz — Körfez, Levant, Türkiye, Avrupa ve Kuzey Amerika. Tek ekip, üç kültür, çeviri katmanı yok.",
      },
      {
        q: "BAIGR'ı diğer ajanslardan ayıran ne?",
        a: "Üç şey: yapay zekâ tabanlı üretim (aynı bütçeyle 10 kat kreatif), yalnızca kıdemli kadro ve erişime değil gelire bağlı raporlama. Üçünü de bir pilot ayda kanıtlamaktan mutluluk duyarız.",
      },
      {
        q: "Nasıl başlıyoruz?",
        a: "Tek bir WhatsApp mesajı. Ücretsiz 30 dakikalık bir görüşme ayarlarız, elinizdekini inceleriz ve bir hafta içinde rakamlı bir plan göndeririz. Onu görmeden hiçbir taahhüt yok.",
      },
      {
        q: "İletişim nasıl ilerleyecek?",
        a: "Size özel bir WhatsApp grubu, canlı sonuç panosu ve her hafta sade bir dille özet. Güncelleme için asla peşimizden koşmazsınız.",
      },
    ],
  },
  contact: {
    eyebrow: "Sonraki adım",
    titlePre: "Bilinçli büyümeye ",
    titleAccent: "hazır mısınız?",
    sub: "Nerede olduğunuzu ve nereye varmak istediğinizi anlatın; haritayı biz getirelim. İlk görüşme ücretsiz, koşulsuz.",
    cta: "WhatsApp'tan yazın",
    or: "veya",
    emailLabel: "E-posta gönderin",
  },
  footer: {
    tagline: "Yapay zekâ ile mühendisliği yapılmış büyüme.",
    nav: "Keşfet",
    talk: "Bize ulaşın",
    rights: "Tüm hakları saklıdır.",
  },
  whatsapp: "BAIGR ile WhatsApp'ta konuşun",
};

export const dictionaries: Record<Locale, Dictionary> = { en, ar, tr };
