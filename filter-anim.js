/**
 * filter-anim.js — Animated category filtering
 *
 * Reads data-category on .product-card elements, intercepts filter pill
 * clicks, and runs a staggered exit → enter animation without a page reload.
 * URL is updated via history.pushState so back/forward still works.
 */
(function () {
  'use strict';

  // ── Timing constants ──────────────────────────────────────
  var EXIT_DURATION  = 180;   // ms — matches cardExit CSS
  var ENTER_STAGGER  = 55;    // ms — delay between each card entering
  var ENTER_DURATION = 380;   // ms — matches cardEnter CSS

  // ── Grab key elements ─────────────────────────────────────
  var grid        = document.querySelector('.products-grid');
  var filterBar   = document.querySelector('.filter-bar, .filter-tabs');
  var metaEl      = document.querySelector('.results-meta');
  var pageTitle   = document.querySelector('.page-header h1');
  var pageSub     = document.querySelector('.page-header .sub');
  var emptyState  = document.querySelector('.empty-state');

  // Nothing to do if there's no filter bar or grid
  if (!filterBar || !grid) return;

  var pills = filterBar.querySelectorAll('[data-filter]');
  if (!pills.length) return;

  // ── Collect all cards once, preserving their data ─────────
  var allCards = Array.prototype.slice.call(grid.querySelectorAll('.product-card'));

  // ── Category label map (for page header on category.php) ──
  var categoryMeta = {
    'all':        { title: 'All Products',  sub: allCards.length + ' items available', label: '' },
    'fruits':     { title: 'Fruits',        sub: null, label: 'Seasonal'   },
    'vegetables': { title: 'Vegetables',    sub: null, label: 'Farm Fresh' },
    'dairy':      { title: 'Dairy',         sub: null, label: 'Farm Direct'},
    'snacks':     { title: 'Snacks',        sub: null, label: 'Indulge'    },
  };

  // ── Main filter function ───────────────────────────────────
  function filterTo(cat, pushState) {

    // 1. Figure out which cards show / hide
    var toShow = allCards.filter(function (c) {
      return cat === 'all' || c.dataset.category === cat;
    });
    var toHide = allCards.filter(function (c) {
      return cat !== 'all' && c.dataset.category !== cat;
    });

    // Already filtered to this state? Skip.
    var currentActive = filterBar.querySelector('[data-filter].active');
    if (currentActive && currentActive.dataset.filter === cat) return;

    // 2. Update pill active states immediately (feels responsive)
    pills.forEach(function (p) {
      p.classList.toggle('active', p.dataset.filter === cat);
    });

    // 3. Update URL without reloading
    if (pushState) {
      var url = new URL(window.location.href);
      if (cat === 'all') {
        url.searchParams.delete('cat');
        url.searchParams.delete('category');
      } else {
        // shop.php uses ?cat=, category.php uses ?category=
        if (url.pathname.indexOf('category.php') !== -1) {
          url.searchParams.set('category', cat);
        } else {
          url.searchParams.set('cat', cat);
        }
      }
      history.pushState({ filter: cat }, '', url.toString());
    }

    // 4. Animate out visible cards that are leaving
    var visibleToHide = toHide.filter(function (c) {
      return !c.classList.contains('card-hidden');
    });

    if (visibleToHide.length === 0) {
      // Nothing to exit — go straight to enter
      enterCards(toShow, cat);
      return;
    }

    // Stagger the exits slightly so they don't all vanish at once
    visibleToHide.forEach(function (card, i) {
      card.classList.remove('card-entering');
      card.style.animationDelay = (i * 20) + 'ms';
      card.classList.add('card-exiting');
    });

    // After exit animation finishes, hide them and bring in new ones
    setTimeout(function () {
      visibleToHide.forEach(function (card) {
        card.classList.remove('card-exiting');
        card.classList.add('card-hidden');
        card.style.animationDelay = '';
      });
      enterCards(toShow, cat);
    }, EXIT_DURATION + visibleToHide.length * 20 + 20);
  }

  function enterCards(toShow, cat) {
    // Un-hide cards that should appear (but not yet animate them)
    toShow.forEach(function (card) {
      card.classList.remove('card-hidden', 'card-exiting', 'card-entering');
      card.style.animationDelay = '';
    });

    // Force a reflow so the class removal registers before we add entering
    grid.offsetHeight; // eslint-disable-line no-unused-expressions

    // Stagger them in
    toShow.forEach(function (card, i) {
      card.style.animationDelay = (i * ENTER_STAGGER) + 'ms';
      card.classList.add('card-entering');
    });

    // Clean up animation classes after they finish
    var cleanupDelay = toShow.length * ENTER_STAGGER + ENTER_DURATION + 50;
    setTimeout(function () {
      toShow.forEach(function (card) {
        card.classList.remove('card-entering');
        card.style.animationDelay = '';
      });
    }, cleanupDelay);

    // Update results meta count
    updateMeta(toShow.length, cat);

    // Show/hide empty state
    if (emptyState) {
      emptyState.style.display = toShow.length === 0 ? '' : 'none';
    }
    grid.style.display = toShow.length === 0 ? 'none' : '';
  }

  function updateMeta(count, cat) {
    // Update results counter
    if (metaEl) {
      metaEl.classList.remove('updating');
      void metaEl.offsetWidth; // reflow
      metaEl.textContent = count + ' product' + (count !== 1 ? 's' : '') +
                           (cat !== 'all' ? ' in ' + cap(cat) : ' available');
      metaEl.classList.add('updating');
    }

    // Update page header title (for category.php)
    if (pageTitle) {
      var meta = categoryMeta[cat];
      if (meta) {
        pageTitle.textContent = meta.title;
        pageTitle.classList.remove('title-animating');
        void pageTitle.offsetWidth;
        pageTitle.classList.add('title-animating');
      }
    }
    if (pageSub) {
      var sub = cat === 'all'
        ? allCards.length + ' items available'
        : count + ' product' + (count !== 1 ? 's' : '');
      pageSub.textContent = sub;
    }

    // Update the small label above the title on category.php
    var catLabelEl = document.getElementById('catLabel');
    if (catLabelEl && cat !== 'all') {
      var labels = {
        fruits: 'Seasonal', vegetables: 'Farm Fresh',
        dairy: 'Farm Direct', snacks: 'Indulge'
      };
      catLabelEl.textContent = labels[cat] || '';
    }
  }

  function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

  // ── Wire up pill clicks ────────────────────────────────────
  pills.forEach(function (pill) {
    pill.addEventListener('click', function (e) {
      e.preventDefault();
      filterTo(pill.dataset.filter, true);
    });
  });

  // ── Handle browser back/forward ────────────────────────────
  window.addEventListener('popstate', function (e) {
    var cat = (e.state && e.state.filter) ? e.state.filter : 'all';
    filterTo(cat, false);
  });

  // ── Init: make sure current state matches URL ──────────────
  // (The PHP already rendered the right cards visible, so we just
  //  make sure the JS state matches without triggering animation)
  var url     = new URL(window.location.href);
  var initCat = url.searchParams.get('cat') ||
                url.searchParams.get('category') ||
                'all';

  // Ensure all cards have card-hidden if they shouldn't be visible
  if (initCat !== 'all') {
    allCards.forEach(function (c) {
      if (c.dataset.category !== initCat) {
        c.classList.add('card-hidden');
      }
    });
  }

  // Set initial results meta without animation
  if (metaEl) {
    var visCount = allCards.filter(function (c) {
      return initCat === 'all' || c.dataset.category === initCat;
    }).length;
    metaEl.textContent = visCount + ' product' + (visCount !== 1 ? 's' : '') +
                         (initCat !== 'all' ? ' in ' + cap(initCat) : ' available');
  }

})();