// @ts-check
import { defineConfig, fontProviders } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import tailwindcss from '@tailwindcss/vite';
import { alternates } from './src/data/i18n.ts';

export default defineConfig({
  site: 'https://marceli.to',
  // German at /, English at /en/
  i18n: {
    locales: ['de', 'en'],
    defaultLocale: 'de',
    routing: { prefixDefaultLocale: false },
  },
  integrations: [
    sitemap({
      // hreflang links between the German and English version of each page (their paths differ)
      serialize(item) {
        const urls = alternates(new URL(item.url).pathname);
        if (urls) item.links = [{ url: new URL(urls.de, item.url).href, lang: 'de-CH' }, { url: new URL(urls.en, item.url).href, lang: 'en' }];
        return item;
      },
    }),
  ],
  // Fixed IPv4 address and port, so Herd can proxy https://marceli.to.test to it
  server: { host: '127.0.0.1', port: 4321 },
  vite: {
    plugins: [tailwindcss()],
    server: { allowedHosts: ['marceli.to.test'] },
  },
  // Self-hosted (Pangram from src/assets/fonts, DM Mono from Fontsource), with metric-matched fallbacks.
  // Latin only: German and every name on the site (ä, ü, à, –) fall within it.
  fonts: [
    {
      // The logo's typeface. There is no Regular, so Light carries the body text.
      provider: fontProviders.local(),
      name: 'PP Pangram Sans Rounded',
      cssVariable: '--font-pangram',
      fallbacks: ['ui-sans-serif', 'system-ui', 'sans-serif'],
      options: {
        variants: [
          { src: ['./src/assets/fonts/PPPangramSansRounded-Light.woff2'], weight: 300, style: 'normal' },
          { src: ['./src/assets/fonts/PPPangramSansRounded-Medium.woff2'], weight: 500, style: 'normal' },
          { src: ['./src/assets/fonts/PPPangramSansRounded-Semibold.woff2'], weight: 600, style: 'normal' },
          { src: ['./src/assets/fonts/PPPangramSansRounded-Bold.woff2'], weight: 700, style: 'normal' },
        ],
      },
    },
    {
      provider: fontProviders.local(),
      name: 'DM Mono',
      cssVariable: '--font-dm-mono',
      fallbacks: ['ui-monospace', 'monospace'],
      options: {
        variants: [
          { src: ['@fontsource/dm-mono/files/dm-mono-latin-400-normal.woff2'], weight: 400, style: 'normal' },
          { src: ['@fontsource/dm-mono/files/dm-mono-latin-500-normal.woff2'], weight: 500, style: 'normal' },
        ],
      },
    },
  ],
});
