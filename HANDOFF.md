# Handoff: marceli.to

State as of 2026-10-17. Read this together with `README.md` (setup, adding projects, deployment).

## Where things stand

- The portfolio was redesigned and moved from Statamic/Laravel to a **static Astro site**. All content lives in this repo.
- **Live** at https://marceli.to, deployed with `./deploy.sh`. `master` is pushed to github.com/marceli-to/marceli.to. No other branches.
- Lighthouse (mobile and desktop): 100 in performance, accessibility, best practices, SEO and agentic browsing.

## Stack

- Astro 7.4, Vite 8.3, Tailwind 4.3 (all latest at the time), TypeScript 6.0.3 (TS 7 is out, but `@astrojs/check` only supports up to 6).
- Node 24 via `.nvmrc`. The nvm default on this Mac is still Node 22, so run `nvm use` first.
- `npm audit`: 0 vulnerabilities.

## Design rules (decided with Marcel, keep them)

- One background only: cream `#f5eedf` by day, navy `#141b2e` at night. No coloured section bands, no pills, no background on the header.
- Fonts: Figtree (variable) and DM Mono for small labels, years and domains.
- Colour coding through underlines only, shown on hover and on the active item: **Arbeiten red** (`brick`, stays red at night), **Über mich yellow** (`lamp`), **Kontakt green** (`leaf`, lighter `#7fae6a` at night).
- Hero: "Hoi, ich bin [avatar] Marcel. / Ich entwickle Websites und digitale Abläufe, die halten, was sie versprechen." "Websites" red, "digitale Abläufe" green, "halten" with a yellow underline; from md up "was sie versprechen." stays on one line. The avatar sits before "Marcel". `public/og.png` shows the same headline (German only, used for both languages); it was rendered from a one-off HTML page with headless Chrome at 1200×630. A subtle arrow at the bottom center scrolls to the projects and fades out once the page is scrolled more than 40 px.
- Projects: the list is the default, with a "Liste / Raster" text switch (red underline). In the list, a screenshot follows the cursor after a 400 ms delay and hides while scrolling, with no row background on hover. The grid shows one project per row with the text beside it from md to xl, and two columns from xl, without an ↗ arrow after the domain.
- Only the latest year with projects is shown (list and grid); "Alle 67 Projekte anzeigen" (grey underline, red on hover) reveals the rest in place and moves focus to the first older project. The cut-off follows the newest project year, not the calendar, so the section is never empty in January.
- Offline projects stay listed with their screenshot but no link, marked with a small mono "OFFLINE" badge (thin border, small red dot; `src/components/OfflineBadge.astro`).
- About: the headline "Zwischen Idee und Umsetzung sorge ich dafür, dass nichts verloren geht." (yellow underline on "nichts verloren geht", the promise). Text by Marcel, then Rolle, Werkzeuge and Daneben (founder of Kimiyu, Marcel's app studio, linked with an always-visible yellow underline; also in llms.txt and the JSON-LD) below it, the portrait as a tilted postcard with a "das bin ich ↘" sticker, and "Zusammengearbeitet mit" (studios from the project credits, no counts, "und viele mehr.").
- Footer: "Ein Projekt oder ein Ablauf, der zu viel Zeit frisst? Schreib mir." (EN: "Got a project or a workflow that takes too much time? Drop me a line.") ("Schreib mir" is a green-underlined mailto link). Contact block on the left, menu as a stack with DE / EN on the right, then "© 2026 – Gebaut mit [heart] in Zürich" centered. The heart is red by day and yellow at night.
- Favicon and app icons: the avatar, zoomed in on the face. Favicons are round with the lamp-yellow ring like in the hero; apple-touch and manifest icons are full-bleed squares. Regenerate with `npm run icons` (`scripts/generate-icons.mjs`).
- Header: logo and a burger only. The burger (three lines, 1.6 stroke like the lamp) turns into an X and opens a full-screen menu overlay that simply fades in (a gooey drip/blob effect was tried and rejected). The overlay holds the three links large with their colour-coded underlines, the email, DE / EN and the lamp. It's navy by day and the page colour at night; logo and burger turn cream halfway through the fade. Esc and the links close it.
- Night mode: the lamp toggle in the menu overlay. The whole page crossfades with a View Transition, and the café lamps in the portrait light up.
- Only the logo fades out when scrolling down (and back in when scrolling up); the burger stays.
- Marcel rejected these (don't propose them again): a CSS anchor-positioned preview, a yellow or green section background, rounded corners or a glow on the footer, a header background.

## Working with Marcel

- He iterates visually in small steps. Make precise changes, check them with screenshots at 390 / 768 / 1024 / 1440 px, and show the result.
- For bigger design choices, offer 2–4 options with the ui.sh picker (`/ui`, `ideas`).
- Commit only when asked. Copy is in German (Swiss spelling, "ss" instead of "ß").

## Running it

- `npm run dev` starts Astro's dev server **in the background** (Astro 7) on `127.0.0.1:4321`. Use `npx astro dev status`, `npx astro dev logs` or `npx astro dev stop`.
- Herd proxies **https://marceli.to.test** to it (`herd proxy marceli.to http://127.0.0.1:4321 --secure`). That's why `astro.config.mjs` pins `server.host` to `127.0.0.1` (IPv6-only caused 502s) and allows `marceli.to.test` in `vite.server.allowedHosts`. A 404 for `/herd-studio/loader.js` is Herd's own badge script, not the site.
- `npm run build` runs `astro check` (types and the content schema) and then the build.

## Deployment

- `./deploy.sh --dry-run`, then `./deploy.sh`. It only deploys committed code: it builds a clean copy and rsyncs `dist/` to `pumutuxu@marceli.to:/home/pumutuxu/www/marceli.to/dist` (the Hostpoint web root), keeping `.well-known/`.
- macOS ships `openrsync` (no `--chmod`), so the script sets permissions with `chmod` before uploading.
- `.htaccess` is deployed from `public/.htaccess` (the server had none). It sets `AddCharset utf-8 .txt`, otherwise Apache serves `llms.txt` without a charset and browsers show umlauts garbled.
- SSH: the server does **not** accept `~/.ssh/id_rsa`; Marcel logs in himself (password). `~/.ssh/config` is owned by `root` and unreadable (fix: `sudo chown marceli.to ~/.ssh/config`). The old host `sl287.web.hostpoint.ch` reports a changed host key; it was left alone.
- The old Statamic install on the server was deleted or replaced by Marcel.

## Pushing to GitHub

The active `gh` account is **kimiyuch**, which can't push to `marceli-to/marceli.to`. `marceli-to` is logged in too, so push without switching accounts:

```sh
TOKEN=$(gh auth token --user marceli-to)
git -c credential.helper= -c http.extraHeader="Authorization: Basic $(printf 'x-access-token:%s' "$TOKEN" | base64)" push origin master
```

## Content notes

- 65 projects, converted from the production Statamic content (downloaded 2026-10-17), including Marcel's edits: names for agency projects, three new descriptions, offline flags, the Baumberger Stegmeier link fix, the stoz credit for Skilift Oberwangen, and Gute Bauten 2025 pointing to the archive at `gutebauten.stadt-zuerich.ch.marceli.to`.
- Offline projects: Bündner Kunstverein, Intercable, Pistenverlängerung, Pappelstrasse, Quality1, Gute Bauten 2021, mobilecharge (2020 and 2021).
- `shortName()` (in `src/lib/projects.ts`) drops the location and a trailing AG/GmbH, but keeps taglines: "WBG AG – Visuelle Kommunikation".
- The old Statamic data (content, assets, `.env`, users) is backed up at `../marceli.to-statamic-backup`. The Statamic code is in git history before `a270eee`.

## Gotchas found along the way

- Astro strips whitespace around tags: a line break hidden on some screens needs a space before it (`für <br class="hidden xl:inline" />`), otherwise words run together ("fürArchitektur").
- WebMCP: Chrome (155, behind `--enable-features=WebMCP`) exposes `document.modelContext`, not `navigator.modelContext`. `src/scripts/webmcp.ts` supports both. Tools return plain data, because Chrome serialises results itself.
- Lighthouse's `ai-catalog.json` (ARD) audit doesn't apply: the site offers no API or MCP server.
- The `muted` grey was darkened to `#646a7c` for WCAG contrast (4.67:1 on cream).

## Legal pages

- Impressum / Datenschutz (`/impressum/`, `/datenschutz/`, `/en/imprint/`, `/en/privacy/`), linked below the © line in the footer. The privacy text says: no cookies, no tracking, no third parties, Hostpoint server logs, `localStorage` for lights and view. Keep it true if that changes.
- Not reviewed by a lawyer. If Marcel has a UID/VAT number, add it to the Impressum.

## Open items

1. **English version** at `/en/` (Astro i18n routing, German stays at `/`). Texts are in `src/data/i18n.ts`, project texts in `descriptionEn`. Still German only: `og.png` (the image text), `site.webmanifest`. The German descriptions of Day and Intercable are actually English.
2. **Gute Bauten 2021** is offline. If Marcel has an archive of it, link it like 2025.
3. **Redirects worth checking:** Win a Gärtner now goes to spross.com/garten, mobilecharge.ch went offline, sipt.ch goes to psychotraumatologie-sipt.ch.
4. Update TypeScript to 7 once `@astrojs/check` supports it.
