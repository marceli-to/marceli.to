import { contact, site } from '../data/site';
import { useStrings, type Lang } from '../data/i18n';
import { description, displayName, getAgencies, getProjects, shortName } from './projects';

/** A plain Markdown summary of the site for language models (https://llmstxt.org) */
export async function llmsTxt(lang: Lang): Promise<Response> {
  const t = useStrings(lang);
  const home = new URL(t.home, site.url).href;
  const other = new URL(useStrings(lang === 'de' ? 'en' : 'de').home, site.url).href;
  const projects = await getProjects();
  const lines = projects.map((project) => {
    const { data } = project;
    const name = displayName(project);
    const title = data.offline ? `${name} (offline)` : `[${name}](${data.link})`;
    const studio = data.agency ? `, Design: ${shortName(data.agency)}` : '';
    return `- ${title} – ${data.year}${studio}. ${description(project, lang).replace(/\s+/g, ' ')}`;
  });

  const body = `# ${site.name}

> ${t.description}

${t.llms.intro}

## ${t.llms.contact}

- ${t.llms.email}: ${contact.email}
- ${t.llms.phone}: +41 ${contact.phone.replace(/^0/, '')}
- ${t.llms.address}: ${contact.street}, ${contact.postalCode} ${contact.city}

## ${t.about.workedWith}

${getAgencies(projects).join(', ')} ${t.about.andMore}

## ${t.projects.title} (${projects.length})

${lines.join('\n')}

## ${t.llms.pages}

- [${t.llms.homeLink}](${home}): ${t.llms.homeText}
- [${t.llms.otherLanguage}](${other}llms.txt)
`;

  return new Response(body, { headers: { 'Content-Type': 'text/markdown; charset=utf-8' } });
}
