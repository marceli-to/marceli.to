// Every text on the site in German (the default, served at /) and English (served at /en/).
// The project descriptions live in the project files (description, descriptionEn).

export const languages = ['de', 'en'] as const;
export type Lang = (typeof languages)[number];

// Every page in both languages: drives the DE / EN switch, the hreflang links and the sitemap
export const pages = {
  home: { de: '/', en: '/en/' },
  imprint: { de: '/impressum/', en: '/en/imprint/' },
  privacy: { de: '/datenschutz/', en: '/en/privacy/' },
} satisfies Record<string, Record<Lang, string>>;

/** The German and English URL of the page at this path, or undefined if it has none (404). */
export function alternates(pathname: string): Record<Lang, string> | undefined {
  const path = pathname.endsWith('/') ? pathname : `${pathname}/`;
  return Object.values(pages).find((page) => page.de === path || page.en === path);
}

const de = {
  lang: 'de-CH',
  locale: 'de_CH',
  home: '/',
  title: 'Marcel Stadelmann – Websites und digitale Abläufe, Zürich',
  description:
    'Marcel Stadelmann entwickelt Websites und digitale Abläufe für Designstudios, Agenturen, Kulturinstitutionen, KMU und Verwaltungen. Selbständig in Zürich seit 2016.',
  ogImageAlt: 'Hoi, ich bin Marcel. Ich entwickle Websites und digitale Abläufe, die halten, was sie versprechen.',
  llmsTitle: 'Zusammenfassung für Sprachmodelle (llms.txt)',
  person: {
    jobTitle: 'Webentwickler',
    description: 'Selbständiger Webentwickler in Zürich: technische Leitung, Beratung, Umsetzung, Automatisierung und KI.',
    knowsAbout: ['Webentwicklung', 'Digitalisierung von Abläufen', 'Automatisierung', 'Künstliche Intelligenz'],
  },
  // Section anchors
  ids: { projects: 'arbeiten', about: 'about', faq: 'fragen', contact: 'kontakt' },
  nav: {
    label: 'Hauptnavigation',
    footerLabel: 'Fussnavigation',
    home: 'Startseite',
    projects: 'Arbeiten',
    about: 'Über mich',
    faq: 'Fragen',
    contact: 'Kontakt',
    language: 'Sprache',
    open: 'Menü öffnen',
    close: 'Menü schliessen',
  },
  lights: { label: 'Licht an- und ausschalten', title: 'Licht an/aus' },
  hero: {
    hi: 'Hoi, ich bin',
    // "Ich entwickle Websites und digitale Abläufe, die halten, was sie versprechen."
    build: 'Ich entwickle',
    websites: 'Websites',
    and: 'und',
    workflows: 'digitale Abläufe',
    that: ', die',
    keep: 'halten',
    pause: ',',
    promise: 'was sie versprechen',
    toProjects: 'Zu den Arbeiten',
  },
  projects: {
    title: 'Arbeiten',
    count: (count: number) => `${count} Projekte`,
    showAll: (count: number) => `Alle ${count} Projekte anzeigen`,
    view: 'Ansicht',
    list: 'Liste',
    grid: 'Raster',
    screenshot: (name: string) => `Screenshot der Website ${name}`,
  },
  about: {
    // The underlined part sits between before and after
    headline: { before: 'Zwischen Idee und Umsetzung sorge ich dafür, dass ', underlined: 'nichts verloren geht', after: '.' },
    text: [
      'Seit 2016 bin ich selbständig in Zürich, als technischer Leiter, Berater und Entwickler. Ich arbeite dort, wo Gestaltung, Auftraggeber und IT aufeinandertreffen, und sorge dafür, dass alle dasselbe meinen. Meine Kunden sind Designstudios und Agenturen, aber auch Kulturinstitutionen, KMU und Verwaltungen: bei Websites ebenso wie bei der Digitalisierung von Abläufen.',
      'Immer öfter geht es dabei um mehr als Websites. Ich baue KI dort ein, wo sie tatsächlich Arbeit abnimmt: als Assistenten, für automatisierte Abläufe oder damit KI-Agenten eine Website lesen und bedienen können.',
      'Am liebsten arbeite ich mit Leuten, denen Details genauso wichtig sind wie mir, egal ob Architekturbüro, Museum oder KMU.',
    ],
    role: 'Rolle',
    roleText: 'Technische Leitung, Beratung, Umsetzung, Automatisierung\u00a0&\u00a0KI',
    tools: 'Werkzeuge',
    also: 'Daneben',
    // Kimiyu is linked between before and after
    founder: { before: 'Gründer von ', after: ', einem Studio für kleine, ruhige Apps' },
    portraitAlt: 'Illustration: Marcel mit Oerlikon-Mütze vor einem Café',
    sticker: 'das bin ich ↘',
    workedWith: 'Zusammengearbeitet mit',
    andMore: 'und viele mehr.',
  },
  faq: {
    title: 'Häufige Fragen',
    // Answers may link: {email} for the contact address, [text](url) for anything else (src/lib/faq.ts)
    items: [
      {
        q: 'Für wen arbeitest du?',
        a: 'Für Designstudios und Agenturen, die einen technischen Partner brauchen, und direkt für Kulturinstitutionen, KMU und Verwaltungen. Oft gestaltet ein Studio, und ich setze um und halte die Technik zusammen.',
      },
      {
        q: 'Gestaltest du auch?',
        a: 'Nein, das überlasse ich Leuten, die es besser können. Wenn du noch niemanden für die Gestaltung hast, bringe ich dich mit Studios zusammen, mit denen ich gearbeitet habe und denen ich vertraue: [Jam’on digital](https://jamon.digital), [WBG](https://wbg.ch), [Bivgrafik](https://bivgrafik.ch) und [Marion Kähler](https://www.marionkaehler.com/).',
      },
      {
        q: 'Kannst du meine vibecodete Website reparieren?',
        a: 'Manchmal. Ich schaue sie mir zuerst an und sage dir ehrlich, ob sich Flicken lohnt. Oft ist es günstiger und besser, sauber neu zu bauen, als ein wackliges Fundament zu stützen.',
      },
      {
        q: 'Ist Programmieren dank KI nicht viel günstiger geworden?',
        a: 'Doch, Code zu schreiben kostet heute fast nichts mehr. Du bezahlst mich aber nicht fürs Tippen, sondern für das, was davor und danach kommt: zu wissen, was gebaut werden soll, wie es sicher und wartbar wird und wann die KI danebenliegt. Mit dieser Erfahrung und KI als Werkzeug bin ich heute schneller als je zuvor, und davon profitierst du direkt.',
      },
      {
        q: 'Arbeitest du selbst mit KI?',
        a: 'Jeden Tag, vor allem mit Claude. Die KI schreibt einen grossen Teil des Codes, ich entscheide, prüfe und trage die Verantwortung. Alle Apps meines Studios [Kimiyu](https://kimiyu.ch), darunter Joey, Wallaby und Roxy, sind vollständig mit KI entstanden.',
      },
      {
        q: 'Was heisst «KI einbauen» konkret?',
        a: 'KI dort, wo sie tatsächlich Arbeit abnimmt: als Assistent, der Fragen beantwortet oder Inhalte vorbereitet, in automatisierten Abläufen, oder damit KI-Agenten eine Website lesen und bedienen können.',
      },
      {
        q: 'Finden ChatGPT und andere KI-Assistenten meine Website?',
        a: 'Nur wenn sie sie lesen können. Ich sorge für sauber strukturierte Inhalte, ergänze strukturierte Daten und eine llms.txt und mache die Website auf Wunsch über WebMCP für KI-Agenten bedienbar. So wie diese hier.',
      },
      {
        q: 'Was passiert, wenn du länger ausfällst?',
        a: 'Dann übernimmt Karsten Zingg von [Jam’on digital](https://jamon.digital). Wir arbeiten eng zusammen, er kennt meine Projekte und kann jederzeit einspringen.',
      },
      {
        q: 'Was kostet ein Projekt?',
        a: 'Mein Stundensatz liegt bei CHF\u00a0150. Bei grösseren Vorhaben schätze ich den Aufwand vorher ab, damit du weisst, woran du bist.',
      },
      {
        q: 'Wie fangen wir an?',
        a: 'Mit einer E-Mail an {email}: ein paar Sätze zum Vorhaben, zum Zeitrahmen und dazu, wer sonst noch beteiligt ist, reichen.',
      },
    ],
  },
  legal: { imprint: 'Impressum', privacy: 'Datenschutz', label: 'Rechtliches' },
  footer: {
    question: 'Ein Projekt oder ein Ablauf, der zu viel Zeit frisst?',
    cta: 'Schreib mir',
    builtWith: 'Gebaut mit',
    love: 'Liebe',
    in: 'in Zürich',
  },
  llms: {
    intro:
      'Marcel Stadelmann ist seit 2016 selbständig in Zürich, als technischer Leiter, Berater und Entwickler. Er arbeitet dort, wo Gestaltung, Auftraggeber und IT aufeinandertreffen, und sorgt dafür, dass alle dasselbe meinen. Seine Kunden sind Designstudios und Agenturen, aber auch Kulturinstitutionen, KMU und Verwaltungen, bei Websites ebenso wie bei der Digitalisierung von Abläufen. KI baut er dort ein, wo sie tatsächlich Arbeit abnimmt: als Assistenten, für automatisierte Abläufe oder damit KI-Agenten eine Website lesen und bedienen können. Daneben hat er Kimiyu gegründet, ein Studio für kleine, ruhige Apps (https://kimiyu.ch). Werkzeuge: Laravel, Statamic, Vue.js, Tailwind CSS, Claude, MCP (Model Context Protocol).',
    contact: 'Kontakt',
    email: 'E-Mail',
    phone: 'Telefon',
    address: 'Adresse',
    pages: 'Seiten',
    homeLink: 'Startseite',
    homeText: 'Arbeiten, Über mich, Häufige Fragen, Kontakt',
    otherLanguage: 'English version',
  },
};

export type Strings = typeof de;

const en: Strings = {
  lang: 'en',
  locale: 'en_GB',
  home: '/en/',
  title: 'Marcel Stadelmann – websites and digital workflows, Zürich',
  description:
    'Marcel Stadelmann builds websites and digital workflows for design studios, agencies, cultural institutions, small businesses and public administrations. Self-employed in Zürich since 2016.',
  ogImageAlt: 'Hi, I’m Marcel. I build websites and digital workflows that deliver what they promise.',
  llmsTitle: 'Summary for language models (llms.txt)',
  person: {
    jobTitle: 'Web developer',
    description: 'Self-employed web developer in Zürich: technical lead, consulting, implementation, automation and AI.',
    knowsAbout: ['Web development', 'Workflow digitisation', 'Automation', 'Artificial intelligence'],
  },
  ids: { projects: 'work', about: 'about', faq: 'faq', contact: 'contact' },
  nav: {
    label: 'Main navigation',
    footerLabel: 'Footer navigation',
    home: 'Home',
    projects: 'Work',
    about: 'About',
    faq: 'FAQ',
    contact: 'Contact',
    language: 'Language',
    open: 'Open menu',
    close: 'Close menu',
  },
  lights: { label: 'Turn the lights on and off', title: 'Lights on/off' },
  hero: {
    hi: 'Hi, I’m',
    build: 'I build',
    websites: 'websites',
    and: 'and',
    workflows: 'digital workflows',
    that: ' that',
    keep: 'deliver',
    pause: '',
    promise: 'what they promise',
    toProjects: 'To the work',
  },
  projects: {
    title: 'Work',
    count: (count: number) => `${count} projects`,
    showAll: (count: number) => `Show all ${count} projects`,
    view: 'View',
    list: 'List',
    grid: 'Grid',
    screenshot: (name: string) => `Screenshot of the website ${name}`,
  },
  about: {
    headline: { before: 'Between idea and execution, I make sure that ', underlined: 'nothing gets lost', after: '.' },
    text: [
      'I’ve been self-employed in Zürich since 2016, as a technical lead, consultant and developer. I work where design, clients and IT meet, and make sure everyone means the same thing. My clients are design studios and agencies, but also cultural institutions, small businesses and public administrations: on websites as well as on digitising workflows.',
      'More and more, it’s about more than websites. I build in AI where it actually takes work off people’s hands: as assistants, for automated workflows, or so that AI agents can read and use a website.',
      'I most like working with people who care about details as much as I do, whether it’s an architecture practice, a museum or a small business.',
    ],
    role: 'Role',
    roleText: 'Technical lead, consulting, implementation, automation\u00a0&\u00a0AI',
    tools: 'Tools',
    also: 'Also',
    founder: { before: 'Founder of ', after: ', a studio for small, calm apps' },
    portraitAlt: 'Illustration: Marcel in an Oerlikon beanie in front of a café',
    sticker: 'that’s me ↘',
    workedWith: 'Worked with',
    andMore: 'and many more.',
  },
  faq: {
    title: 'Frequently asked questions',
    items: [
      {
        q: 'Who do you work for?',
        a: 'For design studios and agencies that need a technical partner, and directly for cultural institutions, small businesses and public administrations. Often a studio does the design, and I build it and hold the technical side together.',
      },
      {
        q: 'Do you also do design?',
        a: 'No, I leave that to people who do it better. If you don’t have anyone for the design yet, I’ll put you in touch with studios I’ve worked with and trust: [Jam’on digital](https://jamon.digital), [WBG](https://wbg.ch), [Bivgrafik](https://bivgrafik.ch) and [Marion Kähler](https://www.marionkaehler.com/).',
      },
      {
        q: 'Can you fix my vibe-coded website?',
        a: 'Sometimes. I take a look first and tell you honestly whether patching it up is worth it. Often it’s cheaper and better to rebuild it properly than to prop up a shaky foundation.',
      },
      {
        q: 'Hasn’t writing code become much cheaper with AI?',
        a: 'It has, writing code costs next to nothing today. But you don’t pay me for typing, you pay me for what comes before and after: knowing what to build, how to make it secure and maintainable, and when the AI gets it wrong. With that experience and AI as a tool, I’m faster than ever, and you benefit directly.',
      },
      {
        q: 'Do you use AI yourself?',
        a: 'Every day, mostly Claude. The AI writes a large part of the code; I decide, review and take responsibility. All the apps from my studio [Kimiyu](https://kimiyu.ch), Joey, Wallaby and Roxy among them, were built entirely with AI.',
      },
      {
        q: 'What does “building in AI” actually mean?',
        a: 'AI where it actually takes work off people’s hands: as an assistant that answers questions or prepares content, in automated workflows, or so that AI agents can read and use a website.',
      },
      {
        q: 'Will ChatGPT and other AI assistants find my website?',
        a: 'Only if they can read it. I make sure the content is cleanly structured, add structured data and an llms.txt, and, if you like, make the website usable for AI agents through WebMCP. Just like this one.',
      },
      {
        q: 'What if you’re unavailable for a longer time?',
        a: 'Then Karsten Zingg of [Jam’on digital](https://jamon.digital) takes over. We work closely together, he knows my projects and can step in at any time.',
      },
      {
        q: 'What does a project cost?',
        a: 'My hourly rate is CHF\u00a0150. For larger projects I estimate the effort up front, so you know where you stand.',
      },
      {
        q: 'How do we get started?',
        a: 'With an email to {email}: a few sentences about the project, the timeline and who else is involved are enough.',
      },
    ],
  },
  legal: { imprint: 'Imprint', privacy: 'Privacy policy', label: 'Legal' },
  footer: {
    question: 'Got a project or a workflow that takes too much time?',
    cta: 'Drop me a line',
    builtWith: 'Built with',
    love: 'love',
    in: 'in Zürich',
  },
  llms: {
    intro:
      'Marcel Stadelmann has been self-employed in Zürich since 2016, as a technical lead, consultant and developer. He works where design, clients and IT meet, and makes sure everyone means the same thing. His clients are design studios and agencies, but also cultural institutions, small businesses and public administrations, on websites as well as on digitising workflows. He builds in AI where it actually takes work off people’s hands: as assistants, for automated workflows, or so that AI agents can read and use a website. He also founded Kimiyu, a studio for small, calm apps (https://kimiyu.ch). Tools: Laravel, Statamic, Vue.js, Tailwind CSS, Claude, MCP (Model Context Protocol).',
    contact: 'Contact',
    email: 'Email',
    phone: 'Phone',
    address: 'Address',
    pages: 'Pages',
    homeLink: 'Home',
    homeText: 'Work, About, FAQ, Contact',
    otherLanguage: 'Deutsche Version',
  },
};

const strings: Record<Lang, Strings> = { de, en };

/** The language of the current page (Astro.currentLocale), German by default. */
export function getLang(locale: string | undefined): Lang {
  return locale === 'en' ? 'en' : 'de';
}

export function useStrings(lang: Lang): Strings {
  return strings[lang];
}
