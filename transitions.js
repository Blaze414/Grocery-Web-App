/**
 * Page Transitions — Uber-inspired wipe effect
 * Five vertical panels sweep down (exit) then collapse up (enter)
 */
(function () {
  'use strict';

  const PANEL_COUNT   = 5;
  const EXIT_DURATION = 420;   // ms — how long the wipe-in takes before we navigate
  const ENTER_STAGGER = 40;    // ms — delay per panel

  /* ── Build the wipe overlay ── */
  const wipe = document.createElement('div');
  wipe.id = 'page-wipe';
  for (let i = 0; i < PANEL_COUNT; i++) {
    const p = document.createElement('div');
    p.className = 'wipe-panel';
    wipe.appendChild(p);
  }
  document.body.appendChild(wipe);

  /* ── Play entrance animation on every page load ── */
  document.body.classList.add('page-entering');
  wipe.classList.add('entering');

  /* Remove the entering class once the animation finishes so it doesn't
     interfere with anything else */
  const longestEnter = EXIT_DURATION + PANEL_COUNT * ENTER_STAGGER + 500;
  setTimeout(function () {
    document.body.classList.remove('page-entering');
    wipe.classList.remove('entering');
  }, longestEnter);

  /* ── Intercept navigation clicks ── */
  document.addEventListener('click', function (e) {
    // Walk up the DOM to find an <a> tag
    let target = e.target;
    while (target && target.tagName !== 'A') target = target.parentElement;

    if (!target) return;

    const href = target.getAttribute('href');
    if (!href) return;

    // Only intercept same-origin, non-hash, non-javascript links
    const isSameOrigin = (
      target.hostname === window.location.hostname ||
      !target.hostname  // relative links have no hostname
    );
    const isHash       = href.startsWith('#');
    const isJavascript = href.startsWith('javascript:');
    const isNewTab     = target.target === '_blank';
    const isModified   = e.metaKey || e.ctrlKey || e.shiftKey || e.altKey;

    if (!isSameOrigin || isHash || isJavascript || isNewTab || isModified) return;

    // Don't intercept form-action links that shouldn't navigate mid-submit
    if (target.closest('form')) return;

    e.preventDefault();

    // Trigger exit wipe
    wipe.classList.remove('entering');
    wipe.classList.add('exiting');

    // Navigate after the wipe covers the page
    const totalExit = EXIT_DURATION + PANEL_COUNT * ENTER_STAGGER;
    setTimeout(function () {
      window.location.href = href;
    }, totalExit);
  });

  /* ── Also handle form submits with a brief wipe ── */
  document.addEventListener('submit', function (e) {
    const form = e.target;
    // Only animate non-AJAX, same-page forms that will navigate away
    if (form.method && form.method.toLowerCase() === 'get') {
      // GET forms navigate — animate
      // But don't block the submit; just add the visual overlay
      wipe.classList.remove('entering');
      wipe.classList.add('exiting');
    }
    // POST forms reload/redirect — the new page load handles the enter animation
  });

})();