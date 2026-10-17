// Lights: toggles night mode and remembers the choice
const root = document.documentElement;

document.querySelectorAll('[data-lights-toggle]').forEach((button) => {
  button.setAttribute('aria-pressed', root.classList.contains('night'));
  button.addEventListener('click', () => {
    const on = !root.classList.contains('night');
    const apply = () => {
      root.classList.toggle('night', on);
      button.setAttribute('aria-pressed', on);
    };
    localStorage.setItem('lights', on ? 'night' : 'day');

    // Crossfade the whole page so every colour changes at the same time
    if (document.startViewTransition && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
      document.startViewTransition(apply);
    } else {
      apply();
    }
  });
});

// Project index: screenshot preview follows the cursor
const preview = document.querySelector('[data-preview]');

if (preview && matchMedia('(hover: hover)').matches) {
  const image = preview.querySelector('img');
  let x = 0, y = 0, frame = null;

  const move = () => {
    const left = x + 24 + preview.offsetWidth > innerWidth ? x - 24 - preview.offsetWidth : x + 24;
    preview.style.translate = `${left}px ${y - preview.offsetHeight / 2}px`;
    frame = null;
  };

  // Show after a short pause so a quick pass over the list stays calm;
  // once visible, moving between rows swaps the image immediately
  let showTimer = null, hideTimer = null, scrollTimer = null, scrolling = false;

  const hide = () => {
    clearTimeout(showTimer);
    showTimer = null;
    delete preview.dataset.visible;
  };

  const scheduleShow = () => {
    if (scrolling || showTimer || 'visible' in preview.dataset) return;
    showTimer = setTimeout(() => {
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
    scrollTimer = setTimeout(() => (scrolling = false), 150);
  }, { passive: true });

  document.querySelectorAll('[data-preview-src]').forEach((row) => {
    row.addEventListener('mouseenter', () => {
      clearTimeout(hideTimer);
      image.src = row.dataset.previewSrc;
      scheduleShow();
    });
    row.addEventListener('mouseleave', () => {
      clearTimeout(showTimer);
      showTimer = null;
      hideTimer = setTimeout(hide, 80);
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
const viewButtons = document.querySelectorAll('[data-view-button]');

const showView = (view) => {
  document.querySelectorAll('[data-view]').forEach((panel) => (panel.hidden = panel.dataset.view !== view));
  viewButtons.forEach((button) => button.setAttribute('aria-pressed', button.dataset.viewButton === view));
};

viewButtons.forEach((button) => {
  button.addEventListener('click', () => {
    showView(button.dataset.viewButton);
    localStorage.setItem('view', button.dataset.viewButton);
  });
});

if (localStorage.getItem('view') === 'grid') showView('grid');

// Header: fade out when scrolling down, back in when scrolling up
const header = document.querySelector('[data-header]');

if (header) {
  let lastY = scrollY, headerFrame = null;

  const updateHeader = () => {
    const y = scrollY;
    const delta = y - lastY;
    if (y < 80) delete header.dataset.hidden;
    else if (delta > 4) header.dataset.hidden = '';
    else if (delta < -4) delete header.dataset.hidden;
    if (Math.abs(delta) > 4 || y < 80) lastY = y;
    headerFrame = null;
  };

  addEventListener('scroll', () => (headerFrame ??= requestAnimationFrame(updateHeader)), { passive: true });
  header.addEventListener('focusin', () => delete header.dataset.hidden);
}

// Navigation: underline the link of the section in view
const navLinks = [...document.querySelectorAll('[data-nav]')];
const navSections = navLinks.map((link) => document.querySelector(link.hash));
let navFrame = null;

const updateNav = () => {
  const line = innerHeight * 0.4;
  const atBottom = innerHeight + scrollY >= document.documentElement.scrollHeight - 2;
  const active = atBottom
    ? navSections.length - 1
    : navSections.findLastIndex((section) => section && section.getBoundingClientRect().top <= line);

  navLinks.forEach((link, index) => {
    if (index === active) link.setAttribute('aria-current', 'true');
    else link.removeAttribute('aria-current');
  });
  navFrame = null;
};

addEventListener('scroll', () => (navFrame ??= requestAnimationFrame(updateNav)), { passive: true });
updateNav();
