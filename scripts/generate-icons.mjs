// Generates the favicons and app icons in public/ from the avatar (src/assets/marcel.jpg).
// Run with `npm run icons` after changing the avatar.
import fs from 'node:fs';
import sharp from 'sharp';

const avatar = 'src/assets/marcel.jpg';
const lamp = '#f4b942';

// Zoomed in on the face so it still reads at 16 px (the hero zooms less, its avatar is bigger)
const { width = 1024 } = await sharp(avatar).metadata();
const zoom = 1.5;
const side = Math.round(width / zoom);
const face = await sharp(avatar)
  .extract({ left: Math.round(0.56 * (width - side)), top: Math.round(0.42 * (width - side)), width: side, height: side })
  .toBuffer();

// Round avatar with the lamp-yellow ring, like in the hero, on a transparent background
const round = async (size) => {
  const ring = Math.max(1, Math.round(size / 16));
  const inner = size - 2 * ring;
  const mask = Buffer.from(`<svg width="${inner}" height="${inner}"><circle cx="${inner / 2}" cy="${inner / 2}" r="${inner / 2}"/></svg>`);
  const photo = await sharp(face).resize(inner, inner).composite([{ input: mask, blend: 'dest-in' }]).png().toBuffer();
  const disc = Buffer.from(`<svg width="${size}" height="${size}"><circle cx="${size / 2}" cy="${size / 2}" r="${size / 2}" fill="${lamp}"/></svg>`);
  return sharp(disc).composite([{ input: photo, left: ring, top: ring }]).png().toBuffer();
};

// Apple and maskable icons are full-bleed squares; the system rounds them
const square = (size) => sharp(face).resize(size, size).png().toBuffer();

fs.writeFileSync('public/favicon.png', await round(96));
fs.writeFileSync('public/apple-touch-icon.png', await square(180));
fs.writeFileSync('public/icon-192.png', await square(192));
fs.writeFileSync('public/icon-512.png', await square(512));

// favicon.ico with 16, 32 and 48 px PNG images inside
const sizes = [16, 32, 48];
const images = await Promise.all(sizes.map(round));
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
