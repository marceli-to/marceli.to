import { contact } from '../data/site';

// FAQ answers are plain strings with two kinds of links: {email} for the contact address and
// Markdown-style [text](url) for anything else.
const LINK = /\{email\}|\[([^\]]+)\]\(([^)]+)\)/g;

export type AnswerPart = { text: string; href?: string };

/** The answer split into text and links, for the page */
export function answerParts(answer: string): AnswerPart[] {
  const parts: AnswerPart[] = [];
  let last = 0;
  for (const match of answer.matchAll(LINK)) {
    if (match.index > last) parts.push({ text: answer.slice(last, match.index) });
    parts.push(match[0] === '{email}' ? { text: contact.email, href: `mailto:${contact.email}` } : { text: match[1], href: match[2] });
    last = match.index + match[0].length;
  }
  if (last < answer.length) parts.push({ text: answer.slice(last) });
  return parts;
}

/** The answer as plain text, for JSON-LD */
export function answerText(answer: string): string {
  return answerParts(answer).map((part) => part.text).join('');
}

/** The answer as Markdown, for llms.txt */
export function answerMarkdown(answer: string): string {
  return answer.replace('{email}', contact.email);
}
