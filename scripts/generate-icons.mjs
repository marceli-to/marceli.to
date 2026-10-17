// Generates the favicons and app icons in public/ from the "M_" mark of the logo.
// Run with `npm run icons` after changing the mark or the colours.
import fs from 'node:fs';
import sharp from 'sharp';

// The "M" of the wordmark (copied from src/components/icons/Logo.astro into m-path.txt), drawn at 2/3 scale,
// followed by the logo's underscore as a cursor
const M = fs.readFileSync(new URL('./m-path.txt', import.meta.url), 'utf8').trim();
const mark = (ink, cursor) => `
  <path transform="translate(9.15 15.35) scale(0.667)" fill="${ink}" d="${M}"/>
  <rect x="40.85" y="44.15" width="14" height="4.4" rx="2.2" fill="${cursor}"/>`;

const day = { bg: '#f5eedf', ink: '#1f2b47', cursor: '#d9563f' };
const night = { bg: '#141b2e', ink: '#f3ead6', cursor: '#f4b942' };

const icon = ({ bg, ink, cursor }, radius = 14) =>
  `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" rx="${radius}" fill="${bg}"/>${mark(ink, cursor)}</svg>`;

// SVG favicon follows the system colour scheme
const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <style>
    .bg { fill: ${day.bg} } .ink { fill: ${day.ink} } .cursor { fill: ${day.cursor} }
    @media (prefers-color-scheme: dark) { .bg { fill: ${night.bg} } .ink { fill: ${night.ink} } .cursor { fill: ${night.cursor} } }
  </style>
  <rect class="bg" width="64" height="64" rx="14"/>
  <path class="ink" transform="translate(9.15 15.35) scale(0.667)" d="${M}"/>
  <rect class="cursor" x="40.85" y="44.15" width="14" height="4.4" rx="2.2"/>
</svg>
`;
fs.writeFileSync('public/favicon.svg', svg);

const png = (markup, size) => sharp(Buffer.from(markup), { density: 72 * (size / 64) * 4 }).resize(size, size).png().toBuffer();

// Apple and maskable icons are full-bleed squares; the system rounds them
fs.writeFileSync('public/apple-touch-icon.png', await png(icon(day, 0), 180));
fs.writeFileSync('public/icon-192.png', await png(icon(day, 0), 192));
fs.writeFileSync('public/icon-512.png', await png(icon(day, 0), 512));

// favicon.ico with 16, 32 and 48 px PNG images inside
const sizes = [16, 32, 48];
const images = await Promise.all(sizes.map((size) => png(icon(day), size)));
const header = Buffer.alloc(6 + 16 * sizes.length);
header.writeUInt16LE(0, 0);
header.writeUInt16LE(1, 2);
header.writeUInt16LE(sizes.length, 4);
let offset = header.length;
images.forEach((image, index) => {
  const entry = 6 + 16 * index;
  header.writeUInt8(sizes[index], entry);
  header.writeUInt8(sizes[index], entry + 1);
  header.writeUInt16LE(1, entry + 4);
  header.writeUInt16LE(32, entry + 6);
  header.writeUInt32LE(image.length, entry + 8);
  header.writeUInt32LE(offset, entry + 12);
  offset += image.length;
});
fs.writeFileSync('public/favicon.ico', Buffer.concat([header, ...images]));

console.log('Icons written to public/');
