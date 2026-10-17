import { defineCollection } from 'astro:content';
import { glob } from 'astro/loaders';
import { z } from 'astro/zod';

// One Markdown file per project; everything lives in the front matter
const projects = defineCollection({
  loader: glob({ pattern: '*.md', base: './src/content/projects' }),
  schema: ({ image }) =>
    z.strictObject({
      // Shown on the site; without it the client is shown
      name: z.string().optional(),
      client: z.string(),
      // Concept/design studio
      agency: z.string().optional(),
      year: z.union([z.number().int(), z.string()]),
      link: z.url(),
      // The site is no longer online: listed with its screenshot, but without a link
      offline: z.boolean().default(false),
      description: z.string(),
      // Position within a year (lower comes first)
      order: z.number().int().default(999),
      image: image(),
      draft: z.boolean().default(false),
    }),
});

export const collections = { projects };
