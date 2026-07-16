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
    eyebrow: "BAIGR · Creative Strategy & AI Growth",
    titlePre: "Creative that sells. Growth you can ",
    titleAccent: "measure",
    titlePost: ".",
    sub: "We're BAIGR — an AI-native studio that turns bold ideas into campaigns, and campaigns into revenue you can see in the bank, not just the dashboard.",
    ctaPrimary: "Let's grow your brand",
    ctaSecondary: "See what we've built",
    scroll: "Scroll",
    ticker: ["Growth", "نمو", "Büyüme"],
  },
  trusted: {
    label: "Brands that trusted us with their growth",
    brands: ["NORA", "ATLAS", "VELA", "KAYRA", "ORBIT", "MIRA", "LUNE", "ASYA"],
  },
  about: {
    eyebrow: "Who we are",
    title: "The team behind brands that refuse to blend in.",
    lead: "BAIGR is a creative growth studio built for the AI era — strategy, content and media working as one engine, all under a single roof.",
    body: "We're a compact team of senior creatives and marketers who treat your budget like it's our own money on the line. Every idea we ship has a number attached to it. AI gives us the speed and the volume; human taste makes sure it actually moves people. That mix — machine scale, human craft — is why the brands we take on tend to stay for years.",
    points: [
      {
        title: "Ideas with a receipt",
        desc: "Every creative decision ties back to a business outcome. Beautiful work that doesn't sell is just decoration.",
      },
      {
        title: "AI speed, human soul",
        desc: "We produce ten times faster with AI — then a real creative director makes the final call on every frame.",
      },
      {
        title: "Fluent in three markets",
        desc: "Arabic, English and Turkish as native tongues — copy that feels born in each culture, never translated into it.",
      },
    ],
  },
  services: {
    eyebrow: "What we do",
    title: "One team for the whole growth journey.",
    lead: "Five connected disciplines that feed one another: creative sparks the ads, ads generate the data, data sharpens the automation — and the whole loop compounds month after month.",
    groups: [
      {
        name: "Create",
        items: [
          {
            title: "AI Image Creation",
            desc: "Scroll-stopping product shots and brand visuals — studio quality without the studio, delivered in days instead of weeks.",
          },
          {
            title: "AI Video Production",
            desc: "Ads, reels and brand films built on AI pipelines: a cinematic feel your audience remembers, at a fraction of the usual cost.",
          },
        ],
      },
      {
        name: "Advertise",
        items: [
          {
            title: "Meta Ads",
            desc: "Facebook and Instagram funnels engineered around return on ad spend — we chase sales, not vanity reach.",
          },
          {
            title: "Google Ads",
            desc: "Search, Shopping and YouTube that meet demand at the exact second it's ready to buy.",
          },
          {
            title: "TikTok Ads",
            desc: "Native creative that rides the algorithm instead of fighting it — the content people forget is even an ad.",
          },
          {
            title: "Snapchat Ads",
            desc: "The most under-priced attention in the Gulf. We turn it into a quiet, dependable growth engine.",
          },
        ],
      },
      {
        name: "Build",
        items: [
          {
            title: "Websites that convert",
            desc: "Fast, striking sites that earn a visitor's trust before they've finished the first line.",
          },
          {
            title: "E-commerce that sells",
            desc: "Stores engineered around the buy button — checkout measured in seconds, not clicks.",
          },
        ],
      },
      {
        name: "Automate",
        items: [
          {
            title: "Marketing Automation",
            desc: "Follow-ups, funnels and reporting that keep working the nights and weekends you don't.",
          },
          {
            title: "AI Chatbots",
            desc: "Assistants that reply in your brand's voice, qualify every lead and close the easy sales around the clock.",
          },
        ],
      },
      {
        name: "Advise",
        items: [
          {
            title: "Growth Consulting",
            desc: "A senior marketing brain on your side of the table — sharp audits, honest strategy, no jargon.",
          },
        ],
      },
    ],
  },
  portfolio: {
    eyebrow: "Selected work",
    title: "We'd rather show you than tell you.",
    lead: "A few of the brands we've helped scale. Full case decks are one message away.",
    projects: [
      { name: "Ayla Beauty", tag: "E-commerce · Skincare", result: "+212% revenue in 6 months" },
      { name: "Nomad Coffee", tag: "Brand · Meta Ads", result: "3.4× return on ad spend" },
      { name: "Kavra Home", tag: "Website · Google Ads", result: "2.1× conversion rate" },
      { name: "Pulse Fitness", tag: "TikTok · Automation", result: "18,000 leads in one quarter" },
    ],
    note: "Some names changed at the client's request. The numbers are real.",
  },
  cases: {
    eyebrow: "Case studies",
    title: "The moment the numbers turned.",
    lead: "Three brands, three markets, one method: find the thing holding growth back, remove it, then pour fuel on whatever works.",
    items: [
      {
        sector: "Skincare e-commerce · Saudi Arabia",
        challenge: "A product people loved, sales stuck flat — the ads were buying clicks that never became customers.",
        play: "We rebuilt the store around one hero product, generated 40 AI creative variants overnight, and let the data crown the winners.",
        metric: "+212%",
        metricLabel: "revenue in 6 months",
      },
      {
        sector: "Real estate developer · Türkiye",
        challenge: "Leads were expensive, and the sales team burned hours on calls that were never going to close.",
        play: "An AI chatbot qualified every lead in Arabic, Turkish and English — so a human only ever spoke to people ready to buy.",
        metric: "−38%",
        metricLabel: "cost per qualified lead",
      },
      {
        sector: "Restaurant group · UAE",
        challenge: "Weekends were packed, midweek tables sat empty, and discounts were eating the margin.",
        play: "Geo-targeted TikTok and Snapchat campaigns with offers that switched on automatically during the quiet hours.",
        metric: "5.1×",
        metricLabel: "return on ad spend",
      },
    ],
    labels: { challenge: "The problem", play: "What we did", result: "The result" },
  },
  process: {
    eyebrow: "How we work",
    title: "Four steps. Zero mystery.",
    lead: "You'll always know exactly what we're doing, why we're doing it, and what it brought back.",
    steps: [
      {
        title: "Listen",
        desc: "We start with your numbers, your market and your margins — not a slide deck. One week in, you get a clear, honest diagnosis.",
      },
      {
        title: "Map the plan",
        desc: "A growth plan with targets you can hold us to: channels, creative, budget and timeline, all on one page.",
      },
      {
        title: "Launch",
        desc: "Creative, campaigns and builds go live fast — then we watch the data like it's our own money on the table.",
      },
      {
        title: "Scale",
        desc: "Double down on what works, cut what doesn't, and report back every week in language you'd use with a friend.",
      },
    ],
  },
  why: {
    eyebrow: "Why BAIGR",
    title: "The reasons brands stay.",
    lead: "Not perks. The things that actually change your results.",
    reasons: [
      {
        title: "AI-native, not AI-curious",
        desc: "Our whole production line is built around AI — so you get ten times the creative for the same budget, without the drop in quality.",
      },
      {
        title: "Only senior hands",
        desc: "No juniors practising on your account. The people who pitch you are the people who do the work.",
      },
      {
        title: "Speed as a habit",
        desc: "Your first campaigns go live within days, not months. Momentum is a strategy, and we treat it like one.",
      },
      {
        title: "Nothing to hide",
        desc: "Live dashboards, weekly summaries and the truth even when it's the uncomfortable version.",
      },
      {
        title: "One roof, no finger-pointing",
        desc: "Creative, media, web and automation in a single team — no agencies blaming each other while your budget burns.",
      },
      {
        title: "We win when you win",
        desc: "Performance-linked pricing is on the table. Our upside is literally tied to your growth.",
      },
    ],
  },
  testimonials: {
    eyebrow: "In their words",
    title: "Trust is spelled in numbers.",
    quotes: [
      {
        quote:
          "They rebuilt our store and doubled revenue in a single quarter. First agency that talked to me about margins instead of impressions.",
        name: "Sara A.",
        role: "Founder, skincare brand",
      },
      {
        quote:
          "The chatbot alone gave my sales team fifteen hours back every week. Everything they promised showed up in the dashboard.",
        name: "Mehmet K.",
        role: "Sales director, real estate",
      },
      {
        quote:
          "Fast, honest and genuinely creative. Our TikTok went from a channel we ignored to the best-performing one we have.",
        name: "Omar R.",
        role: "Marketing lead, F&B group",
      },
    ],
  },
  faq: {
    eyebrow: "Good questions",
    title: "The things everyone asks first.",
    items: [
      {
        q: "How soon will I actually see results?",
        a: "Ad campaigns start giving clear signals within 2–4 weeks, and meaningful revenue usually moves inside the first quarter. Websites and automation pay off from day one. In our first call we'll give you a realistic timeline — and then we hold ourselves to it.",
      },
      {
        q: "What kind of budget do I need?",
        a: "Most of our clients invest between $2,000 and $50,000 a month across ads and services. Honestly, intent matters more than size: if you're serious about growing and ready to move, we'll shape a plan that fits where you are.",
      },
      {
        q: "Which markets do you cover?",
        a: "We work natively in Arabic, English and Turkish — across the Gulf, the Levant, Türkiye, Europe and North America. Same team, three cultures, no translation layer in between.",
      },
      {
        q: "What actually makes BAIGR different?",
        a: "Three things: AI-native production (ten times the creative for the same spend), a senior-only team, and reporting tied to revenue instead of reach. We're glad to prove all three in a single pilot month.",
      },
      {
        q: "How do we get started?",
        a: "One message on WhatsApp. We'll set up a free 30-minute call, look at what you already have, and send back a plan with real numbers within a week. No commitment until you've seen it.",
      },
      {
        q: "How will we stay in touch?",
        a: "A dedicated WhatsApp group, a live results dashboard, and a weekly summary written in plain language. You'll never have to chase us for an update.",
      },
    ],
  },
  contact: {
    eyebrow: "Your move",
    titlePre: "Let's build something worth ",
    titleAccent: "talking about",
    sub: "Tell us where your brand is today and where you want it to be. We'll bring the map, the team and the first ideas — and the first call is completely free.",
    cta: "Message us on WhatsApp",
    or: "or",
    emailLabel: "send an email",
  },
  footer: {
    tagline: "Creative strategy and digital growth, powered by AI.",
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
    faq: "أسئلتكم",
    cta: "ابدأ مشروعك",
  },
  hero: {
    eyebrow: "BAIGR · إستراتيجية إبداعية ونمو بالذكاء الاصطناعي",
    titlePre: "إبداعٌ يبيع، ونموٌّ ",
    titleAccent: "تقيسه",
    titlePost: ".",
    sub: "نحن BAIGR — استوديو يعمل بالذكاء الاصطناعي، نحوّل الأفكار الجريئة إلى حملات، والحملات إلى أرباح تراها في حسابك البنكي، لا في لوحة التقارير فقط.",
    ctaPrimary: "لِنُنمِّ علامتك",
    ctaSecondary: "شاهد ما صنعناه",
    scroll: "مرّر للأسفل",
    ticker: ["نمو", "Growth", "Büyüme"],
  },
  trusted: {
    label: "علاماتٌ ائتمنتنا على نموّها",
    brands: ["NORA", "ATLAS", "VELA", "KAYRA", "ORBIT", "MIRA", "LUNE", "ASYA"],
  },
  about: {
    eyebrow: "من نحن",
    title: "الفريق الذي يقف خلف العلامات التي ترفض أن تكون كالبقية.",
    lead: "BAIGR استوديو نموٍّ إبداعي وُلد لعصر الذكاء الاصطناعي — الإستراتيجية والمحتوى والإعلانات تعمل كمحرّك واحد، تحت سقفٍ واحد.",
    body: "نحن فريقٌ صغير من كبار المبدعين والمسوّقين، نتعامل مع ميزانيتك وكأنها أموالنا نحن على المحك. كل فكرة نطلقها يرافقها رقم. الذكاء الاصطناعي يمنحنا السرعة والكمّ، والذوق البشري يضمن أنها تحرّك الناس فعلاً. هذا المزيج — قوة الآلة وحِرفة الإنسان — هو سبب بقاء عملائنا معنا سنوات.",
    points: [
      {
        title: "أفكارٌ لها مردود",
        desc: "كل قرار إبداعي مرتبطٌ بنتيجة تجارية. العمل الجميل الذي لا يبيع مجرّد زينة.",
      },
      {
        title: "سرعة الآلة، وروح الإنسان",
        desc: "ننتج أسرع عشر مرات بالذكاء الاصطناعي، ثم يتخذ مدير إبداعي حقيقي القرار الأخير في كل لقطة.",
      },
      {
        title: "نتقن ثلاثة أسواق",
        desc: "العربية والإنجليزية والتركية بلسان أهلها — نصوصٌ تُولد في كل ثقافة، لا تُترجَم إليها.",
      },
    ],
  },
  services: {
    eyebrow: "ماذا نقدّم",
    title: "فريقٌ واحد لرحلة النمو كاملة.",
    lead: "خمسة تخصصات مترابطة يغذّي بعضها بعضاً: الإبداع يشعل الإعلان، والإعلان يولّد البيانات، والبيانات تصقل الأتمتة — والدورة كلها تتضاعف شهراً بعد شهر.",
    groups: [
      {
        name: "نُبدع",
        items: [
          {
            title: "صناعة الصور بالذكاء الاصطناعي",
            desc: "صور منتجاتٍ ومرئيات علامة توقف التمرير — جودة استوديو بلا استوديو، تُسلَّم في أيام لا أسابيع.",
          },
          {
            title: "إنتاج الفيديو بالذكاء الاصطناعي",
            desc: "إعلانات وريلز وأفلام علامة بخطوط إنتاج ذكية: إحساسٌ سينمائي يبقى في الذاكرة، بجزءٍ يسير من التكلفة.",
          },
        ],
      },
      {
        name: "نُعلن",
        items: [
          {
            title: "إعلانات ميتا",
            desc: "قمعُ فيسبوك وإنستغرام مبنيٌّ على العائد من الإنفاق الإعلاني — نلاحق المبيعات، لا الوصول الفارغ.",
          },
          {
            title: "إعلانات جوجل",
            desc: "بحثٌ وتسوّق ويوتيوب يلتقي بالطلب في اللحظة التي يصبح فيها العميل جاهزاً للشراء.",
          },
          {
            title: "إعلانات تيك توك",
            desc: "محتوى أصيل يركب الخوارزمية بدل أن يصارعها — إعلانٌ ينساه المشاهد أنه إعلان.",
          },
          {
            title: "إعلانات سناب شات",
            desc: "أرخص انتباهٍ في الخليج قيمةً. نحوّله إلى محرك نموٍّ هادئ يُعتمَد عليه.",
          },
        ],
      },
      {
        name: "نبني",
        items: [
          {
            title: "مواقع تُحوّل الزائر إلى عميل",
            desc: "مواقع سريعة لافتة تكسب ثقة الزائر قبل أن يُنهي السطر الأول.",
          },
          {
            title: "متاجر إلكترونية تبيع",
            desc: "متاجر مهندَسة حول زرّ الشراء — إتمامُ طلبٍ يُقاس بالثواني لا بالنقرات.",
          },
        ],
      },
      {
        name: "نُؤتمت",
        items: [
          {
            title: "أتمتة التسويق",
            desc: "متابعاتٌ وقنوات بيع وتقارير تواصل العمل في الليالي والعطل التي ترتاح فيها أنت.",
          },
          {
            title: "روبوتات محادثة ذكية",
            desc: "مساعدٌ يردّ بصوت علامتك، يفرز كل عميل محتمل، ويُغلق المبيعات السهلة على مدار الساعة.",
          },
        ],
      },
      {
        name: "نستشير",
        items: [
          {
            title: "استشارات النمو",
            desc: "عقلٌ تسويقي خبير في صفّك — تدقيقٌ حاد، إستراتيجية صادقة، بلا مصطلحات معقّدة.",
          },
        ],
      },
    ],
  },
  portfolio: {
    eyebrow: "أعمالٌ مختارة",
    title: "أن نُريك خيرٌ من أن نُخبرك.",
    lead: "بعضٌ من العلامات التي ساعدناها على التوسّع. ملفات الحالة الكاملة على بُعد رسالة واحدة.",
    projects: [
      { name: "آيلا بيوتي", tag: "متجر إلكتروني · عناية بالبشرة", result: "+212% نمو الإيرادات في 6 أشهر" },
      { name: "نوماد كوفي", tag: "علامة تجارية · إعلانات ميتا", result: "عائد إنفاق إعلاني 3.4×" },
      { name: "كافرا هوم", tag: "موقع · إعلانات جوجل", result: "مضاعفة معدل التحويل 2.1×" },
      { name: "بَلس فِتنس", tag: "تيك توك · أتمتة", result: "18,000 عميل محتمل في ربعٍ واحد" },
    ],
    note: "بعض الأسماء مُغيَّرة بطلب العميل. أما الأرقام فحقيقية.",
  },
  cases: {
    eyebrow: "دراسات حالة",
    title: "اللحظة التي انقلبت فيها الأرقام.",
    lead: "ثلاث علامات، ثلاثة أسواق، منهجٌ واحد: نجد ما يكبح النمو، نُزيله، ثم نصبّ الوقود على ما ينجح.",
    items: [
      {
        sector: "متجر عناية بالبشرة · السعودية",
        challenge: "منتجٌ يعشقه الناس ومبيعاتٌ راكدة — الإعلانات تشتري نقراتٍ لا تتحوّل إلى عملاء.",
        play: "أعدنا بناء المتجر حول منتجٍ بطل واحد، وولّدنا 40 نسخة إعلانية بالذكاء الاصطناعي بين عشيةٍ وضحاها، وتركنا البيانات تُتوّج الرابح.",
        metric: "+212%",
        metricLabel: "نمو الإيرادات في 6 أشهر",
      },
      {
        sector: "مطوّر عقاري · تركيا",
        challenge: "العملاء المحتملون باهظون، وفريق المبيعات يحرق ساعاته في مكالماتٍ لن تُغلق أبداً.",
        play: "روبوت محادثة ذكي يفرز كل عميل بالعربية والتركية والإنجليزية — فلا يتحدث الإنسان إلا مع من هو جاهزٌ للشراء.",
        metric: "−38%",
        metricLabel: "تكلفة العميل المؤهَّل",
      },
      {
        sector: "مجموعة مطاعم · الإمارات",
        challenge: "نهايات الأسبوع مزدحمة، وطاولات منتصف الأسبوع فارغة، والخصومات تلتهم الهامش.",
        play: "حملات جغرافية على تيك توك وسناب شات بعروضٍ تشتغل تلقائياً في ساعات الهدوء.",
        metric: "5.1×",
        metricLabel: "عائد الإنفاق الإعلاني",
      },
    ],
    labels: { challenge: "المشكلة", play: "ما فعلناه", result: "النتيجة" },
  },
  process: {
    eyebrow: "كيف نعمل",
    title: "أربع خطوات. بلا غموض.",
    lead: "ستعرف دائماً ماذا نفعل بالضبط، ولماذا، وماذا أعاد عليك.",
    steps: [
      {
        title: "نُنصت",
        desc: "نبدأ من أرقامك وسوقك وهوامشك — لا من عرضٍ تقديمي. وخلال أسبوع تحصل على تشخيصٍ واضح وصادق.",
      },
      {
        title: "نرسم الخطة",
        desc: "خطة نموٍّ بأهدافٍ تحاسبنا عليها: القنوات والإبداع والميزانية والجدول الزمني، في صفحةٍ واحدة.",
      },
      {
        title: "نُطلق",
        desc: "الإبداع والحملات والمواقع تنطلق بسرعة — ثم نراقب البيانات وكأنها أموالنا على الطاولة.",
      },
      {
        title: "نُضاعف",
        desc: "نضاعف ما ينجح، ونقطع ما لا ينجح، ونرسل تقريراً أسبوعياً بلغةٍ تتحدّث بها مع صديق.",
      },
    ],
  },
  why: {
    eyebrow: "لماذا BAIGR",
    title: "الأسباب التي تُبقي العلامات معنا.",
    lead: "ليست امتيازات، بل الأشياء التي تُغيّر نتائجك فعلاً.",
    reasons: [
      {
        title: "ذكاءٌ اصطناعي في الصميم، لا للتجربة",
        desc: "خط إنتاجنا كله مبنيٌّ على الذكاء الاصطناعي — فتحصل على إبداعٍ مضاعف عشر مرات بنفس الميزانية، دون أن تهبط الجودة.",
      },
      {
        title: "أيادٍ خبيرة فقط",
        desc: "لا مبتدئين يتمرّنون على حسابك. من يعرض عليك الفكرة هو من ينفّذها.",
      },
      {
        title: "السرعة عادة",
        desc: "أول حملاتك تنطلق خلال أيام، لا أشهر. الزخم إستراتيجية، ونعامله على هذا الأساس.",
      },
      {
        title: "لا شيء نخفيه",
        desc: "لوحات نتائج مباشرة، ملخصاتٌ أسبوعية، والحقيقة حتى في نسختها غير المريحة.",
      },
      {
        title: "سقفٌ واحد، بلا تبادل لوم",
        desc: "الإبداع والإعلان والويب والأتمتة في فريقٍ واحد — لا وكالات تتقاذف المسؤولية بينما تحترق ميزانيتك.",
      },
      {
        title: "نربح حين تربح",
        desc: "التسعير المرتبط بالأداء مطروحٌ على الطاولة. مكسبنا مرتبطٌ حرفياً بنموّك.",
      },
    ],
  },
  testimonials: {
    eyebrow: "بكلماتهم",
    title: "الثقة تُكتب بالأرقام.",
    quotes: [
      {
        quote:
          "أعادوا بناء متجرنا وضاعفوا الإيرادات في ربعٍ واحد. أول وكالة تحدّثني عن الهوامش بدل مرات الظهور.",
        name: "سارة أ.",
        role: "مؤسِّسة علامة عناية بالبشرة",
      },
      {
        quote:
          "روبوت المحادثة وحده أعاد لفريق مبيعاتي خمس عشرة ساعة كل أسبوع. وكل ما وعدوا به ظهر في لوحة النتائج.",
        name: "محمد ك.",
        role: "مدير مبيعات، قطاع عقاري",
      },
      {
        quote:
          "سرعةٌ وصدقٌ وإبداعٌ حقيقي. تحوّل تيك توك من قناةٍ نتجاهلها إلى أفضل قنواتنا أداءً.",
        name: "عمر ر.",
        role: "مسؤول تسويق، مجموعة مطاعم",
      },
    ],
  },
  faq: {
    eyebrow: "أسئلةٌ في محلّها",
    title: "ما يسأل عنه الجميع أولاً.",
    items: [
      {
        q: "متى سأرى النتائج فعلاً؟",
        a: "الحملات الإعلانية تعطي إشاراتٍ واضحة خلال 2–4 أسابيع، وتحرّك الإيرادات الملموس يأتي عادةً خلال الربع الأول. أما المواقع والأتمتة فتؤتي ثمارها من اليوم الأول. في مكالمتنا الأولى نعطيك جدولاً واقعياً — ثم نلتزم به.",
      },
      {
        q: "ما الميزانية التي أحتاجها؟",
        a: "معظم عملائنا يستثمرون بين 2,000 و50,000 دولار شهرياً بين الإعلانات والخدمات. وبصراحة، الجدية أهم من الحجم: إن كنت جاداً في النمو ومستعداً للتحرك، سنُفصّل خطةً تناسب موقعك.",
      },
      {
        q: "ما الأسواق التي تغطّونها؟",
        a: "نعمل بلسان أهل العربية والإنجليزية والتركية — في الخليج والشام وتركيا وأوروبا وأمريكا الشمالية. فريقٌ واحد، ثلاث ثقافات، بلا طبقة ترجمةٍ بينها.",
      },
      {
        q: "ما الذي يميّز BAIGR فعلاً؟",
        a: "ثلاثة أشياء: إنتاجٌ مبنيٌّ على الذكاء الاصطناعي (إبداعٌ مضاعف عشر مرات بنفس الإنفاق)، فريقٌ من الخبراء فقط، وتقاريرٌ مرتبطة بالإيرادات لا بالوصول. ويسعدنا إثبات الثلاثة في شهرٍ تجريبي واحد.",
      },
      {
        q: "كيف نبدأ؟",
        a: "رسالة واحدة على واتساب. نحجز مكالمة مجانية لثلاثين دقيقة، نطّلع على ما لديك، ونعيد إليك خطةً بأرقامٍ حقيقية خلال أسبوع. ولا التزام حتى تراها.",
      },
      {
        q: "كيف سيكون تواصلنا؟",
        a: "مجموعة واتساب مخصّصة، لوحة نتائج مباشرة، وملخصٌ أسبوعي بلغةٍ مفهومة. لن تُضطر يوماً لملاحقتنا كي تعرف المستجدات.",
      },
    ],
  },
  contact: {
    eyebrow: "دورك الآن",
    titlePre: "لنصنع شيئاً ",
    titleAccent: "يستحقّ الحديث عنه",
    sub: "أخبرنا أين علامتك اليوم وأين تريدها أن تصل. نحضر لك الخريطة والفريق وأول الأفكار — والمكالمة الأولى مجانية تماماً.",
    cta: "راسلنا على واتساب",
    or: "أو",
    emailLabel: "أرسل بريداً",
  },
  footer: {
    tagline: "إستراتيجية إبداعية ونموٌّ رقمي، مدعومٌ بالذكاء الاصطناعي.",
    nav: "استكشف",
    talk: "تواصل معنا",
    rights: "جميع الحقوق محفوظة.",
  },
  whatsapp: "تحدّث مع BAIGR على واتساب",
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
    eyebrow: "BAIGR · Kreatif Strateji ve Yapay Zekâ Büyümesi",
    titlePre: "Satan kreatif, ",
    titleAccent: "ölçülebilir",
    titlePost: " büyüme.",
    sub: "Biz BAIGR'ız — yapay zekâ temelli bir stüdyo. Cesur fikirleri kampanyalara, kampanyaları da yalnızca panoda değil banka hesabında görünen gelire dönüştürüyoruz.",
    ctaPrimary: "Markanı büyütelim",
    ctaSecondary: "Neler yaptığımıza bak",
    scroll: "Kaydır",
    ticker: ["Büyüme", "Growth", "نمو"],
  },
  trusted: {
    label: "Büyümesini bize emanet eden markalar",
    brands: ["NORA", "ATLAS", "VELA", "KAYRA", "ORBIT", "MIRA", "LUNE", "ASYA"],
  },
  about: {
    eyebrow: "Biz kimiz",
    title: "Kalabalığa karışmayı reddeden markaların arkasındaki ekip.",
    lead: "BAIGR, yapay zekâ çağı için kurulmuş bir kreatif büyüme stüdyosu — strateji, içerik ve medya tek bir motor gibi, tek çatı altında çalışıyor.",
    body: "Bütçenize sanki kendi paramız masadaymış gibi davranan, kıdemli kreatif ve pazarlamacılardan oluşan küçük bir ekibiz. Yayınladığımız her fikrin yanında bir rakam vardır. Yapay zekâ bize hız ve hacim verir; insan zevki ise onun gerçekten insanları harekete geçirmesini sağlar. Bu birleşim — makine ölçeği, insan zanaati — çalıştığımız markaların yıllarca bizimle kalmasının sebebidir.",
    points: [
      {
        title: "Karşılığı olan fikirler",
        desc: "Her kreatif karar bir iş sonucuna bağlıdır. Satmayan güzel iş, yalnızca süstür.",
      },
      {
        title: "Makine hızı, insan ruhu",
        desc: "Yapay zekâyla on kat hızlı üretiriz — sonra gerçek bir kreatif direktör her karede son sözü söyler.",
      },
      {
        title: "Üç pazara ana dilde hâkim",
        desc: "Arapça, İngilizce ve Türkçe ana dil gibi — her kültürde çevrilmiş değil, o kültürde doğmuş metinler.",
      },
    ],
  },
  services: {
    eyebrow: "Ne yapıyoruz",
    title: "Tüm büyüme yolculuğu için tek ekip.",
    lead: "Birbirini besleyen beş disiplin: kreatif reklamı ateşler, reklam veriyi üretir, veri otomasyonu keskinleştirir — ve tüm döngü her ay katlanarak büyür.",
    groups: [
      {
        name: "Üret",
        items: [
          {
            title: "Yapay Zekâ ile Görsel Üretimi",
            desc: "Kaydırmayı durduran ürün çekimleri ve marka görselleri — stüdyosuz stüdyo kalitesi, haftalarca değil günler içinde.",
          },
          {
            title: "Yapay Zekâ ile Video Prodüksiyonu",
            desc: "Yapay zekâ hatlarıyla kurulan reklamlar, reels ve marka filmleri: hatırlanan sinematik bir his, alışılmış maliyetin çok altında.",
          },
        ],
      },
      {
        name: "Reklam ver",
        items: [
          {
            title: "Meta Reklamları",
            desc: "Reklam harcamasının getirisi etrafında kurulan Facebook ve Instagram hunileri — boş erişimi değil, satışı kovalarız.",
          },
          {
            title: "Google Reklamları",
            desc: "Arama, Alışveriş ve YouTube — talebi tam satın almaya hazır olduğu saniyede yakalar.",
          },
          {
            title: "TikTok Reklamları",
            desc: "Algoritmayla savaşmak yerine onunla akan doğal kreatif — izleyicinin reklam olduğunu unuttuğu içerik.",
          },
          {
            title: "Snapchat Reklamları",
            desc: "Körfez'in en ucuz değerlenen dikkati. Onu sessiz, güvenilir bir büyüme motoruna çeviririz.",
          },
        ],
      },
      {
        name: "İnşa et",
        items: [
          {
            title: "Dönüştüren web siteleri",
            desc: "Ziyaretçi ilk satırı bitirmeden güvenini kazanan, hızlı ve etkileyici siteler.",
          },
          {
            title: "Satan e-ticaret",
            desc: "Satın alma butonu etrafında mühendisliği yapılmış mağazalar — tıklamayla değil, saniyeyle ölçülen ödeme.",
          },
        ],
      },
      {
        name: "Otomatikleştir",
        items: [
          {
            title: "Pazarlama Otomasyonu",
            desc: "Senin dinlendiğin gecelerde ve tatillerde çalışmaya devam eden takipler, huniler ve raporlar.",
          },
          {
            title: "Yapay Zekâ Sohbet Botları",
            desc: "Markanın sesiyle yanıtlayan, her adayı eleyen ve kolay satışları 7/24 kapatan asistanlar.",
          },
        ],
      },
      {
        name: "Danış",
        items: [
          {
            title: "Büyüme Danışmanlığı",
            desc: "Masanın senin tarafında kıdemli bir pazarlama aklı — keskin denetim, dürüst strateji, jargon yok.",
          },
        ],
      },
    ],
  },
  portfolio: {
    eyebrow: "Seçilmiş işler",
    title: "Anlatmaktansa göstermeyi tercih ederiz.",
    lead: "Büyümesine katkı verdiğimiz markalardan birkaçı. Ayrıntılı vaka sunumları tek mesaj uzaklıkta.",
    projects: [
      { name: "Ayla Beauty", tag: "E-ticaret · Cilt bakımı", result: "6 ayda +212% gelir" },
      { name: "Nomad Coffee", tag: "Marka · Meta Reklam", result: "3.4× reklam harcaması getirisi" },
      { name: "Kavra Home", tag: "Web sitesi · Google Ads", result: "2.1× dönüşüm oranı" },
      { name: "Pulse Fitness", tag: "TikTok · Otomasyon", result: "Bir çeyrekte 18.000 potansiyel müşteri" },
    ],
    note: "Bazı isimler müşteri talebiyle değiştirildi. Rakamlar gerçek.",
  },
  cases: {
    eyebrow: "Vaka çalışmaları",
    title: "Rakamların döndüğü an.",
    lead: "Üç marka, üç pazar, tek yöntem: büyümeyi tutan şeyi bul, kaldır, sonra işe yarayana yakıt dök.",
    items: [
      {
        sector: "Cilt bakımı e-ticareti · Suudi Arabistan",
        challenge: "İnsanların sevdiği bir ürün, duran satışlar — reklamlar müşteriye dönüşmeyen tıklamalar satın alıyordu.",
        play: "Mağazayı tek bir kahraman ürün etrafında yeniden kurduk, bir gecede 40 yapay zekâ kreatif varyantı ürettik ve kazananı verinin taçlandırmasına bıraktık.",
        metric: "+212%",
        metricLabel: "6 ayda gelir artışı",
      },
      {
        sector: "Gayrimenkul geliştiricisi · Türkiye",
        challenge: "Potansiyel müşteriler pahalıydı ve satış ekibi asla kapanmayacak aramalarda saatlerini yakıyordu.",
        play: "Bir yapay zekâ botu her adayı Arapça, Türkçe ve İngilizce eledi — böylece insan yalnızca satın almaya hazır kişilerle konuştu.",
        metric: "−38%",
        metricLabel: "nitelikli müşteri başına maliyet",
      },
      {
        sector: "Restoran grubu · BAE",
        challenge: "Hafta sonları doluydu, hafta içi masalar boştu ve indirimler kârı yiyordu.",
        play: "Sakin saatlerde otomatik devreye giren tekliflerle konum hedefli TikTok ve Snapchat kampanyaları.",
        metric: "5.1×",
        metricLabel: "reklam harcaması getirisi",
      },
    ],
    labels: { challenge: "Sorun", play: "Ne yaptık", result: "Sonuç" },
  },
  process: {
    eyebrow: "Nasıl çalışıyoruz",
    title: "Dört adım. Sıfır muamma.",
    lead: "Ne yaptığımızı, neden yaptığımızı ve ne getirdiğini her zaman tam olarak bileceksin.",
    steps: [
      {
        title: "Dinle",
        desc: "Bir sunumla değil; rakamların, pazarın ve marjlarınla başlarız. Bir hafta içinde net ve dürüst bir teşhis alırsın.",
      },
      {
        title: "Planı çiz",
        desc: "Bizi hesap sorabileceğin hedeflerle bir büyüme planı: kanallar, kreatif, bütçe ve takvim, tek sayfada.",
      },
      {
        title: "Yayına al",
        desc: "Kreatif, kampanyalar ve siteler hızla canlıya çıkar — sonra veriyi masadaki kendi paramızmış gibi izleriz.",
      },
      {
        title: "Ölçekle",
        desc: "İşe yarayana yüklen, yaramayanı kes ve her hafta bir dostuna anlatır gibi rapor ver.",
      },
    ],
  },
  why: {
    eyebrow: "Neden BAIGR",
    title: "Markaların kalma sebepleri.",
    lead: "Ayrıcalık değil. Sonuçlarını gerçekten değiştiren şeyler.",
    reasons: [
      {
        title: "Meraklı değil, yapay zekâ temelli",
        desc: "Tüm üretim hattımız yapay zekâ üzerine kuruludur — böylece aynı bütçeyle, kaliteden ödün vermeden on kat kreatif alırsın.",
      },
      {
        title: "Yalnızca kıdemli eller",
        desc: "Hesabında pratik yapan stajyer yok. Sana sunum yapanlar, işi yapan kişilerdir.",
      },
      {
        title: "Alışkanlık hâline gelmiş hız",
        desc: "İlk kampanyaların aylarca değil günler içinde yayında. İvme bir stratejidir ve biz onu öyle ele alırız.",
      },
      {
        title: "Saklayacak bir şey yok",
        desc: "Canlı panolar, haftalık özetler ve rahatsız edici hâliyle bile gerçek.",
      },
      {
        title: "Tek çatı, parmakla gösterme yok",
        desc: "Kreatif, medya, web ve otomasyon tek ekipte — bütçen yanarken birbirini suçlayan ajanslar yok.",
      },
      {
        title: "Sen kazanınca biz kazanırız",
        desc: "Performansa bağlı fiyatlandırma masada. Kazancımız tam anlamıyla senin büyümene bağlı.",
      },
    ],
  },
  testimonials: {
    eyebrow: "Kendi sözleriyle",
    title: "Güven, rakamlarla yazılır.",
    quotes: [
      {
        quote:
          "Mağazamızı yeniden kurdular ve geliri tek çeyrekte ikiye katladılar. Benimle gösterim yerine marj konuşan ilk ajans.",
        name: "Sara A.",
        role: "Kurucu, cilt bakım markası",
      },
      {
        quote:
          "Yalnızca sohbet botu bile satış ekibime her hafta on beş saat kazandırdı. Söz verdikleri her şey panoda göründü.",
        name: "Mehmet K.",
        role: "Satış direktörü, gayrimenkul",
      },
      {
        quote:
          "Hızlı, dürüst ve gerçekten yaratıcı. TikTok'umuz görmezden geldiğimiz bir kanaldan en iyi performans gösteren kanalımıza dönüştü.",
        name: "Omar R.",
        role: "Pazarlama lideri, yeme-içme grubu",
      },
    ],
  },
  faq: {
    eyebrow: "İyi sorular",
    title: "Herkesin ilk sorduğu şeyler.",
    items: [
      {
        q: "Sonuçları gerçekten ne zaman görürüm?",
        a: "Reklam kampanyaları 2–4 hafta içinde net sinyaller vermeye başlar ve anlamlı gelir genellikle ilk çeyrekte hareket eder. Web siteleri ve otomasyon ise ilk günden kazandırır. İlk görüşmemizde gerçekçi bir takvim veririz — ve sonra ona sadık kalırız.",
      },
      {
        q: "Ne kadar bütçeye ihtiyacım var?",
        a: "Müşterilerimizin çoğu reklam ve hizmetler genelinde aylık 2.000–50.000 dolar yatırım yapar. Açıkçası niyet, büyüklükten önemlidir: büyümek konusunda ciddiysen ve harekete geçmeye hazırsan, bulunduğun yere uyan bir plan kurarız.",
      },
      {
        q: "Hangi pazarları kapsıyorsunuz?",
        a: "Arapça, İngilizce ve Türkçede ana dil seviyesinde çalışıyoruz — Körfez, Levant, Türkiye, Avrupa ve Kuzey Amerika'da. Tek ekip, üç kültür, aralarında çeviri katmanı yok.",
      },
      {
        q: "BAIGR'ı gerçekten farklı kılan ne?",
        a: "Üç şey: yapay zekâ temelli üretim (aynı harcamayla on kat kreatif), yalnızca kıdemli bir ekip ve erişime değil gelire bağlı raporlama. Üçünü de tek bir pilot ayda kanıtlamaktan mutluluk duyarız.",
      },
      {
        q: "Nasıl başlıyoruz?",
        a: "WhatsApp'tan tek bir mesaj. Ücretsiz 30 dakikalık bir görüşme ayarlar, elindekine bakar ve bir hafta içinde gerçek rakamlarla bir plan göndeririz. Görene kadar hiçbir taahhüt yok.",
      },
      {
        q: "İletişimi nasıl sürdüreceğiz?",
        a: "Sana özel bir WhatsApp grubu, canlı bir sonuç panosu ve sade bir dille yazılmış haftalık özet. Güncelleme için asla peşimizden koşmak zorunda kalmazsın.",
      },
    ],
  },
  contact: {
    eyebrow: "Sıra sende",
    titlePre: "Konuşulmaya değer bir şey ",
    titleAccent: "inşa edelim",
    sub: "Markanın bugün nerede olduğunu ve nereye varmak istediğini anlat. Haritayı, ekibi ve ilk fikirleri biz getirelim — üstelik ilk görüşme tamamen ücretsiz.",
    cta: "WhatsApp'tan yaz",
    or: "veya",
    emailLabel: "e-posta gönder",
  },
  footer: {
    tagline: "Yapay zekâ destekli kreatif strateji ve dijital büyüme.",
    nav: "Keşfet",
    talk: "Bize ulaş",
    rights: "Tüm hakları saklıdır.",
  },
  whatsapp: "BAIGR ile WhatsApp'ta konuş",
};

export const dictionaries: Record<Locale, Dictionary> = { en, ar, tr };
