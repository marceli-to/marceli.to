// @ts-check
import { defineConfig, fontProviders } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  site: 'https://marceli.to',
  integrations: [sitemap()],
  // Fixed IPv4 address and port, so Herd can proxy https://marceli.to.test to it
  server: { host: '127.0.0.1', port: 4321 },
  vite: {
    plugins: [tailwindcss()],
    server: { allowedHosts: ['marceli.to.test'] },
  },
  // Self-hosted from the installed Fontsource files, with metric-matched fallbacks.
  // Latin only: German and every name on the site (ä, ü, à, –) fall within it.
  fonts: [
    {
      provider: fontProviders.local(),
      name: 'Figtree',
      cssVariable: '--font-figtree',
      fallbacks: ['ui-sans-serif', 'system-ui', 'sans-serif'],
      options: {
        variants: [
          { src: ['@fontsource-variable/figtree/files/figtree-latin-wght-normal.woff2'], weight: '300 900', style: 'normal' },
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
