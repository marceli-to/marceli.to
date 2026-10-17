import type { APIRoute } from 'astro';
import { contact, site } from '../data/site';
import { displayName, getAgencies, getProjects, shortName } from '../lib/projects';

// A plain Markdown summary of the site for language models (https://llmstxt.org)
export const GET: APIRoute = async () => {
  const projects = await getProjects();
  const lines = projects.map((project) => {
    const { data } = project;
    const name = displayName(project);
    const title = data.offline ? `${name} (offline)` : `[${name}](${data.link})`;
    const studio = data.agency ? `, Design: ${shortName(data.agency)}` : '';
    return `- ${title} – ${data.year}${studio}. ${data.description.replace(/\s+/g, ' ')}`;
  });

  const body = `# ${site.name}

> ${site.description}

Marcel Stadelmann ist selbständiger Webentwickler in Zürich (seit 2016): Entwicklung, technische Leitung und Beratung an der Schnittstelle von Gestaltung, Auftraggebern und IT. Er arbeitet mit Designstudios und Agenturen sowie direkt mit Kulturinstitutionen und KMU. Werkzeuge: Laravel, Statamic, Vue.js, Tailwind CSS.

## Kontakt

- E-Mail: ${contact.email}
- Telefon: +41 ${contact.phone.replace(/^0/, '')}
- Adresse: ${contact.street}, ${contact.postalCode} ${contact.city}

## Zusammengearbeitet mit

${getAgencies(projects).join(', ')} und viele mehr.

## Arbeiten (${projects.length})

${lines.join('\n')}

## Seiten

- [Startseite](${site.url}/): Arbeiten, Über mich, Kontakt
`;

  return new Response(body, { headers: { 'Content-Type': 'text/markdown; charset=utf-8' } });
};
