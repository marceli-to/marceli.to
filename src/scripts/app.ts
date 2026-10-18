const root = document.documentElement;
const reducedMotion = () => matchMedia('(prefers-reduced-motion: reduce)').matches;

// Lights: toggles night mode and remembers the choice
export function setLights(on: boolean) {
  const apply = () => {
    root.classList.toggle('night', on);
    document.querySelectorAll('[data-lights-toggle]').forEach((button) => button.setAttribute('aria-pressed', String(on)));
  };
  localStorage.setItem('lights', on ? 'night' : 'day');

  // Crossfade the whole page so every colour changes at the same time
  if (document.startViewTransition && !reducedMotion()) document.startViewTransition(apply);
  else apply();
}

document.querySelectorAll<HTMLButtonElement>('[data-lights-toggle]').forEach((button) => {
  button.setAttribute('aria-pressed', String(root.classList.contains('night')));
  button.addEventListener('click', () => setLights(!root.classList.contains('night')));
});

// Project index: screenshot preview follows the cursor
const preview = document.querySelector<HTMLElement>('[data-preview]');
const previewImage = preview?.querySelector('img');

if (preview && previewImage && matchMedia('(hover: hover)').matches) {
  let x = 0;
  let y = 0;
  let frame: number | null = null;

  const move = () => {
    const left = x + 24 + preview.offsetWidth > innerWidth ? x - 24 - preview.offsetWidth : x + 24;
    preview.style.translate = `${left}px ${y - preview.offsetHeight / 2}px`;
    frame = null;
  };

  // Show after a short pause so a quick pass over the list stays calm;
  // once visible, moving between rows swaps the image immediately
  let showTimer: number | null = null;
  let hideTimer: number | undefined;
  let scrollTimer: number | undefined;
  let scrolling = false;

  const hide = () => {
    if (showTimer) clearTimeout(showTimer);
    showTimer = null;
    delete preview.dataset.visible;
  };

  const scheduleShow = () => {
    if (scrolling || showTimer || 'visible' in preview.dataset) return;
    showTimer = window.setTimeout(() => {
      showTimer = null;
      move();
      preview.dataset.visible = '';
    }, 400);
  };

  // Hide while the page scrolls; it comes back once the mouse moves again
  addEventListener('scroll', () => {
    scrolling = true;
    hide();
    clearTimeout(scrollTimer);
    scrollTimer = window.setTimeout(() => (scrolling = false), 150);
  }, { passive: true });

  document.querySelectorAll<HTMLElement>('[data-preview-src]').forEach((row) => {
    row.addEventListener('mouseenter', () => {
      clearTimeout(hideTimer);
      previewImage.src = row.dataset.previewSrc ?? '';
      scheduleShow();
    });
    row.addEventListener('mouseleave', () => {
      if (showTimer) clearTimeout(showTimer);
      showTimer = null;
      hideTimer = window.setTimeout(hide, 80);
    });
    row.addEventListener('mousemove', (event) => {
      x = event.clientX;
      y = event.clientY;
      frame ??= requestAnimationFrame(move);
      scheduleShow();
    });
  });
}

// Projects: switch between list and grid, remember the choice
export type View = 'list' | 'grid';

export function setView(view: View) {
  document.querySelectorAll<HTMLElement>('[data-view]').forEach((panel) => (panel.hidden = panel.dataset.view !== view));
  document.querySelectorAll<HTMLElement>('[data-view-button]').forEach((button) =>
    button.setAttribute('aria-pressed', String(button.dataset.viewButton === view)),
  );
  localStorage.setItem('view', view);
}

document.querySelectorAll<HTMLElement>('[data-view-button]').forEach((button) => {
  button.addEventListener('click', () => setView(button.dataset.viewButton as View));
});

if (localStorage.getItem('view') === 'grid') setView('grid');

// Projects: older years are revealed on demand; focus moves to the first of them
document.querySelector<HTMLButtonElement>('[data-show-older]')?.addEventListener('click', (event) => {
  const older = [...document.querySelectorAll<HTMLElement>('[data-older]')];
  older.forEach((item) => (item.hidden = false));
  (event.currentTarget as HTMLElement).parentElement?.remove();
  const first = older.find((item) => item.offsetParent !== null);
  (first?.matches('a') ? first : first?.querySelector<HTMLElement>('a'))?.focus({ preventScroll: true });
});

// Header: the logo fades out when scrolling down, back in when scrolling up
const header = document.querySelector<HTMLElement>('[data-header]');

if (header) {
  let lastY = scrollY;
  let headerFrame: number | null = null;

  const updateHeader = () => {
    headerFrame = null;
    if ('menu' in root.dataset) return;
    const delta = scrollY - lastY;
    if (scrollY < 80) delete header.dataset.hidden;
    else if (delta > 4) header.dataset.hidden = '';
    else if (delta < -4) delete header.dataset.hidden;
    if (Math.abs(delta) > 4 || scrollY < 80) lastY = scrollY;
  };

  addEventListener('scroll', () => (headerFrame ??= requestAnimationFrame(updateHeader)), { passive: true });
  header.addEventListener('focusin', () => delete header.dataset.hidden);
}

// Hero: the arrow to the projects fades out once the page is scrolled
const scrollHint = document.querySelector<HTMLElement>('[data-scroll-hint]');

if (scrollHint) {
  const updateHint = () => (scrollHint.toggleAttribute('data-hidden', scrollY > 40));
  addEventListener('scroll', updateHint, { passive: true });
  updateHint();
}

// Navigation: underline the link of the section in view (header, menu overlay and footer share the hashes)
const navLinks = [...document.querySelectorAll<HTMLAnchorElement>('[data-nav]')].filter((link) => link.pathname === location.pathname);
const navHashes = [...new Set(navLinks.map((link) => link.hash))];
const navSections = navHashes.map((hash) => document.querySelector(hash));
let navFrame: number | null = null;

const updateNav = () => {
  const line = innerHeight * 0.4;
  const atBottom = innerHeight + scrollY >= document.documentElement.scrollHeight - 2;
  const active = atBottom
    ? navSections.length - 1
    : navSections.findLastIndex((section) => section !== null && section.getBoundingClientRect().top <= line);

  navLinks.forEach((link) => {
    if (link.hash === navHashes[active]) link.setAttribute('aria-current', 'true');
    else link.removeAttribute('aria-current');
  });
  navFrame = null;
};

addEventListener('scroll', () => (navFrame ??= requestAnimationFrame(updateNav)), { passive: true });
updateNav();

// Menu: the burger opens a full-screen overlay (src/components/Menu.astro). The header swaps colours
// halfway through the fade.
const INK_SWAP = 150;
const menuButton = document.querySelector<HTMLButtonElement>('[data-menu-toggle]');
const menu = document.getElementById(menuButton?.getAttribute('aria-controls') ?? '');

if (menuButton && menu) {
  const button = menuButton;
  const timers: number[] = [];
  const later = (ms: number, fn: () => void) => timers.push(window.setTimeout(fn, reducedMotion() ? 0 : ms));
  const isOpen = () => button.getAttribute('aria-expanded') === 'true';

  const setOpen = (open: boolean, returnFocus = true) => {
    timers.splice(0).forEach(clearTimeout);
    button.setAttribute('aria-expanded', String(open));
    button.setAttribute('aria-label', (open ? button.dataset.labelClose : button.dataset.labelOpen) ?? '');
    document.querySelectorAll<HTMLElement>('body > :not([data-header], script)').forEach((element) => (element.inert = open));
    root.style.overflow = open ? 'hidden' : '';

    if (open) {
      root.dataset.menu = '';
      header?.removeAttribute('data-hidden');
      menu.dataset.open = '';
      later(INK_SWAP, () => (root.dataset.menuInk = ''));
    } else {
      delete root.dataset.menu;
      delete menu.dataset.open;
      later(INK_SWAP, () => delete root.dataset.menuInk);
      if (returnFocus) button.focus();
    }
  };

  button.addEventListener('click', () => setOpen(!isOpen()));
  menu.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false, false)));
  addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && isOpen()) setOpen(false);
  });
}
