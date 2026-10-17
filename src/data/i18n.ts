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
  title: 'marceli.to – Marcel Stadelmann, Webentwicklung in Zürich',
  description:
    'Marcel Stadelmann entwickelt Websites für Architektur, Kunst und Design – meist zusammen mit Designstudios und Agenturen. Selbständig in Zürich seit 2016.',
  ogImageAlt: 'Hoi, ich bin Marcel und baue Websites für Architektur, Kunst und Design.',
  llmsTitle: 'Zusammenfassung für Sprachmodelle (llms.txt)',
  person: {
    jobTitle: 'Webentwickler',
    description: 'Selbständiger Webentwickler in Zürich: Entwicklung, KI-Integration, technische Leitung und Beratung für Architektur, Kunst und Design.',
    knowsAbout: ['Webentwicklung', 'Künstliche Intelligenz'],
  },
  // Section anchors
  ids: { projects: 'arbeiten', about: 'about', contact: 'kontakt' },
  nav: {
    label: 'Hauptnavigation',
    footerLabel: 'Fussnavigation',
    home: 'Startseite',
    projects: 'Arbeiten',
    about: 'Über mich',
    contact: 'Kontakt',
    language: 'Sprache',
  },
  lights: { label: 'Licht an- und ausschalten', title: 'Licht an/aus' },
  hero: {
    hi: 'Hoi, ich bin',
    build: 'und baue Websites für',
    architecture: 'Architektur',
    art: 'Kunst',
    and: 'und',
    design: 'Design',
    toProjects: 'Zu den Arbeiten',
  },
  projects: {
    title: 'Arbeiten',
    count: (count: number) => `${count} Projekte`,
    view: 'Ansicht',
    list: 'Liste',
    grid: 'Raster',
    screenshot: (name: string) => `Screenshot der Website ${name}`,
  },
  about: {
    // The underlined part sits between before and after
    headline: { before: 'Ich übersetze Gestaltung in Code, ', underlined: 'so sorgfältig', after: ', wie sie entworfen wurde.' },
    text: [
      'Seit 2016 bin ich selbständig in Zürich, als Entwickler, technischer Leiter und Berater an der Schnittstelle von Gestaltung, Auftraggebern und IT. Ich arbeite mit Designstudios und Agenturen, aber auch direkt mit Kulturinstitutionen und KMU: bei Websites ebenso wie bei der Digitalisierung von Abläufen.',
      'Dazu kommt KI: Ich arbeite täglich damit und baue sie in Projekte ein, als Assistenten, für automatisierte Abläufe oder damit KI-Agenten eine Website lesen und bedienen können. Das eröffnet vieles, was über klassische Webentwicklung hinausgeht.',
      'Am liebsten arbeite ich für Architektur, Kunst und Kultur, für Leute, denen Details genauso wichtig sind wie mir.',
    ],
    role: 'Rolle',
    roleText: 'Entwicklung, KI-Integration, technische Leitung, Beratung',
    tools: 'Werkzeuge',
    also: 'Daneben',
    // Kimiyu is linked between before and after
    founder: { before: 'Gründer von ', after: ', einem Studio für kleine, ruhige Apps' },
    portraitAlt: 'Illustration: Marcel mit Oerlikon-Mütze vor einem Café',
    sticker: 'das bin ich ↘',
    workedWith: 'Zusammengearbeitet mit',
    andMore: 'und viele mehr.',
  },
  legal: { imprint: 'Impressum', privacy: 'Datenschutz', label: 'Rechtliches' },
  footer: {
    question: 'Ein Projekt im Kopf?',
    cta: 'Schreib mir',
    builtWith: 'Gebaut mit',
    love: 'Liebe',
    in: 'in Zürich',
  },
  llms: {
    intro:
      'Marcel Stadelmann ist selbständiger Webentwickler in Zürich (seit 2016): Entwicklung, technische Leitung und Beratung an der Schnittstelle von Gestaltung, Auftraggebern und IT. Er arbeitet mit Designstudios und Agenturen sowie direkt mit Kulturinstitutionen und KMU. Er baut KI in Projekte ein: Assistenten, automatisierte Abläufe und Websites, die KI-Agenten lesen und bedienen können. Daneben hat er Kimiyu gegründet, ein Studio für kleine, ruhige Apps (https://kimiyu.ch). Werkzeuge: Laravel, Statamic, Vue.js, Tailwind CSS.',
    contact: 'Kontakt',
    email: 'E-Mail',
    phone: 'Telefon',
    address: 'Adresse',
    pages: 'Seiten',
    homeLink: 'Startseite',
    homeText: 'Arbeiten, Über mich, Kontakt',
    otherLanguage: 'English version',
  },
};

export type Strings = typeof de;

const en: Strings = {
  lang: 'en',
  locale: 'en_GB',
  home: '/en/',
  title: 'marceli.to – Marcel Stadelmann, web development in Zürich',
  description:
    'Marcel Stadelmann builds websites for architecture, art and design, mostly together with design studios and agencies. Self-employed in Zürich since 2016.',
  ogImageAlt: 'Hi, I’m Marcel and I build websites for architecture, art and design.',
  llmsTitle: 'Summary for language models (llms.txt)',
  person: {
    jobTitle: 'Web developer',
    description: 'Self-employed web developer in Zürich: development, AI integration, technical lead and consulting for architecture, art and design.',
    knowsAbout: ['Web development', 'Artificial intelligence'],
  },
  ids: { projects: 'work', about: 'about', contact: 'contact' },
  nav: {
    label: 'Main navigation',
    footerLabel: 'Footer navigation',
    home: 'Home',
    projects: 'Work',
    about: 'About',
    contact: 'Contact',
    language: 'Language',
  },
  lights: { label: 'Turn the lights on and off', title: 'Lights on/off' },
  hero: {
    hi: 'Hi, I’m',
    build: 'and I build websites for',
    architecture: 'architecture',
    art: 'art',
    and: 'and',
    design: 'design',
    toProjects: 'To the work',
  },
  projects: {
    title: 'Work',
    count: (count: number) => `${count} projects`,
    view: 'View',
    list: 'List',
    grid: 'Grid',
    screenshot: (name: string) => `Screenshot of the website ${name}`,
  },
  about: {
    headline: { before: 'I translate design \ninto code, ', underlined: 'as carefully', after: ' \nas it was designed.' },
    text: [
      'I’ve been self-employed in Zürich since 2016, working as a developer, technical lead and consultant where design, clients and IT meet. I work with design studios and agencies, and directly with cultural institutions and small businesses: on websites as well as on digitising workflows.',
      'And then there’s AI: I work with it every day and build it into projects, as assistants, for automated workflows, or so that AI agents can read and use a website. It opens up a lot that goes beyond classic web development.',
      'I most like working for architecture, art and culture, for people who care about details as much as I do.',
    ],
    role: 'Role',
    roleText: 'Development, AI integration, technical lead, consulting',
    tools: 'Tools',
    also: 'Also',
    founder: { before: 'Founder of ', after: ', a studio for small, calm apps' },
    portraitAlt: 'Illustration: Marcel in an Oerlikon beanie in front of a café',
    sticker: 'that’s me ↘',
    workedWith: 'Worked with',
    andMore: 'and many more.',
  },
  legal: { imprint: 'Imprint', privacy: 'Privacy policy', label: 'Legal' },
  footer: {
    question: 'Got a project in mind?',
    cta: 'Drop me a line',
    builtWith: 'Built with',
    love: 'love',
    in: 'in Zürich',
  },
  llms: {
    intro:
      'Marcel Stadelmann is a self-employed web developer in Zürich (since 2016): development, technical lead and consulting where design, clients and IT meet. He works with design studios and agencies, and directly with cultural institutions and small businesses. He builds AI into projects: assistants, automated workflows and websites that AI agents can read and use. He also founded Kimiyu, a studio for small, calm apps (https://kimiyu.ch). Tools: Laravel, Statamic, Vue.js, Tailwind CSS.',
    contact: 'Contact',
    email: 'Email',
    phone: 'Phone',
    address: 'Address',
    pages: 'Pages',
    homeLink: 'Home',
    homeText: 'Work, About, Contact',
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
