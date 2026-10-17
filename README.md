# marceli.to

Portfolio of Marcel Stadelmann, web developer in Zürich. A static site built with [Astro](https://astro.build) and [Tailwind CSS](https://tailwindcss.com); all content lives in this repository.

## Development

Requires Node 24 (see `.nvmrc`; Astro needs at least 22.22).

```sh
nvm use
npm install
npm run dev       # starts the dev server in the background on http://127.0.0.1:4321
npm run build     # type check, then build the static site into dist/
npm run preview   # serve dist/ locally
```

The dev server keeps running in the background: `npx astro dev status`, `npx astro dev logs`, `npx astro dev stop`.

With [Herd](https://herd.laravel.com), `https://marceli.to.test` proxies to the dev server (set up once with `herd proxy marceli.to http://127.0.0.1:4321 --secure`).

## Adding a project

Each project is one Markdown file in `src/content/projects/` with its screenshot in `src/assets/projects/`. The front matter is validated against the schema in `src/content.config.ts`, so a typo fails the build.

```yaml
---
name: SHIFT Altstetten              # optional; shown instead of the client
client: stoz Werbeagentur AG, Pfäffikon/ZH
agency: stoz Werbeagentur AG, Pfäffikon/ZH   # optional; concept/design studio
year: 2026
link: https://shift-altstetten.ch
offline: true                       # optional; listed without a link
description: One or two sentences in German.
descriptionEn: The same in English.  # optional; without it the English site shows the German text
order: 1                            # position within the year, lower first
image: ../../assets/projects/shift-altstetten-ch.jpg
draft: true                         # optional; hidden from the site
---
```

Screenshots: JPG, 3:2, ideally 2400 px wide. Astro crops them to 16:10 from the top and generates AVIF/WebP in all needed sizes at build time.

## Structure

- `src/pages/index.astro` (German, `/`) and `src/pages/en/index.astro` (English, `/en/`) – both render `src/components/Home.astro`; `llms.txt.ts` (per language) and `robots.txt.ts` generate those files
- `src/data/i18n.ts` – every text on the site in German and English, and the URL of each page in both languages
- `src/pages/impressum.astro`, `datenschutz.astro` and `en/imprint.astro`, `en/privacy.astro` – the legal pages (layout: `src/layouts/Legal.astro`)
- `src/components/` – Hero, Projects (list and grid), About, Footer, Header
- `src/lib/projects.ts` – sorting, display names, the studio list
- `src/scripts/app.ts` – night mode, cursor preview, list/grid switch, header, navigation
- `src/scripts/webmcp.ts` – [WebMCP](https://github.com/webmachinelearning/webmcp) tools for AI agents in the browser
- `src/data/site.ts` – site URL and contact details
- `scripts/generate-icons.mjs` – favicons and app icons (`npm run icons`)

## Search engines and agents

- Title, description, canonical URL, Open Graph image (`public/og.png`), sitemap and `robots.txt`
- JSON-LD: `WebSite`, `Person`, `ProfilePage` and an `ItemList` of all projects
- `hreflang` links between `/` and `/en/`, also in the sitemap
- `/llms.txt` and `/en/llms.txt`: a Markdown summary of the site for language models
- WebMCP tools (`list_projects`, `get_contact`, `show_projects`, `set_lights`), registered where the browser supports it (Chrome: `--enable-features=WebMCP`)

## Deployment

```sh
./deploy.sh --dry-run   # show what would change on the server
./deploy.sh
```

`deploy.sh` builds the committed state into `dist/` and syncs it with rsync to `/home/pumutuxu/www/marceli.to/dist` on Hostpoint (the web root), deleting files that no longer exist. `.htaccess` comes from `public/`; the server's `.well-known/` is left alone. No PHP or Node is needed on the server.
