// WebMCP: lets AI agents in the browser use the page through tools instead of clicking around.
// The API is still a proposal (https://github.com/webmachinelearning/webmcp); browsers without it skip this.
import { setLights, setView, type View } from './app';

interface ModelContextTool {
  name: string;
  description: string;
  inputSchema: object;
  annotations?: { readOnlyHint?: boolean };
  execute: (input: Record<string, unknown>) => Promise<unknown> | unknown;
}

interface ModelContext {
  registerTool?: (tool: ModelContextTool) => void;
  provideContext?: (context: { tools: ModelContextTool[] }) => void;
}

// Chrome exposes it on document (behind the WebMCP flag); earlier drafts used navigator
const modelContext =
  (document as Document & { modelContext?: ModelContext }).modelContext ??
  (navigator as Navigator & { modelContext?: ModelContext }).modelContext;
const data = JSON.parse(document.getElementById('site-data')?.textContent ?? '{}');

const tools: ModelContextTool[] = [
  {
    name: 'list_projects',
    description: 'List the websites Marcel Stadelmann has built: name, client, design studio, year, URL, offline state and a short description (in the language of the page). Optionally filter by year or studio.',
    inputSchema: {
      type: 'object',
      properties: {
        year: { type: 'number', description: 'Only projects from this year, e.g. 2025.' },
        studio: { type: 'string', description: 'Only projects designed by this studio (case-insensitive substring), e.g. "Bivgrafik".' },
      },
    },
    annotations: { readOnlyHint: true },
    execute: ({ year, studio }) =>
      (data.projects ?? []).filter(
        (project: { year: number | string; studio?: string }) =>
          (!year || Number.parseInt(String(project.year), 10) === year) &&
          (!studio || project.studio?.toLowerCase().includes(String(studio).toLowerCase())),
      ),
  },
  {
    name: 'get_contact',
    description: 'Get Marcel Stadelmann’s contact details (email, phone, postal address in Zürich).',
    inputSchema: { type: 'object', properties: {} },
    annotations: { readOnlyHint: true },
    execute: () => data.contact,
  },
  {
    name: 'show_projects',
    description: 'Scroll to the project section and show it as a compact list or as a grid of screenshots.',
    inputSchema: {
      type: 'object',
      properties: { view: { type: 'string', enum: ['list', 'grid'] } },
      required: ['view'],
    },
    execute: ({ view }) => {
      setView(view as View);
      document.querySelector('[data-view-button]')?.closest('section')?.scrollIntoView();
      return { view };
    },
  },
  {
    name: 'set_lights',
    description: 'Switch the page between day (light) and night (dark) mode.',
    inputSchema: {
      type: 'object',
      properties: { night: { type: 'boolean', description: 'true for night mode, false for day mode.' } },
      required: ['night'],
    },
    execute: ({ night }) => {
      setLights(Boolean(night));
      return { night: Boolean(night) };
    },
  },
];

if (modelContext?.registerTool) tools.forEach((tool) => modelContext.registerTool?.(tool));
else modelContext?.provideContext?.({ tools });
