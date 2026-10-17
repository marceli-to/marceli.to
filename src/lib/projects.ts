import { getCollection, type CollectionEntry } from 'astro:content';
import type { Lang } from '../data/i18n';

export type Project = CollectionEntry<'projects'>;

/**
 * Shorten a client or studio name: drop the location and a trailing legal form.
 * "Bivgrafik GmbH, Zürich" becomes "Bivgrafik"; "WBG AG – Visuelle Kommunikation, Zürich"
 * keeps its tagline and becomes "WBG AG – Visuelle Kommunikation".
 */
export function shortName(name: string): string {
  return name.split(',')[0].replace(/\s+(AG|GmbH)$/, '').trim();
}

/** The name shown on the site: the project name, or else the client without its location. */
export function displayName(project: Project): string {
  return project.data.name ?? project.data.client.split(',')[0].trim();
}

/** The description in the given language, falling back to German. */
export function description(project: Project, lang: Lang): string {
  return (lang === 'en' && project.data.descriptionEn) || project.data.description;
}

export function domain(url: string): string {
  return url.replace(/^https?:\/\//, '').replace(/^www\./, '').replace(/\/$/, '');
}

const yearOf = (project: Project) => Number.parseInt(String(project.data.year), 10) || 0;

/** Published projects, newest year first, then by their position within the year. */
export async function getProjects(): Promise<Project[]> {
  const projects = await getCollection('projects', ({ data }) => !data.draft);
  return projects.sort((a, b) => yearOf(b) - yearOf(a) || a.data.order - b.data.order);
}

/** Studios credited for concept/design, most frequent first (without marceli.to itself). */
export function getAgencies(projects: Project[]): string[] {
  const counts = new Map<string, number>();

  for (const { data } of projects) {
    if (!data.agency) continue;
    const name = shortName(data.agency);
    if (name !== 'marceli.to') counts.set(name, (counts.get(name) ?? 0) + 1);
  }

  return [...counts].sort((a, b) => b[1] - a[1]).map(([name]) => name);
}
