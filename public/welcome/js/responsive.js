// ☰ button (below 1200px): open / close the left sidebar menu.
(function () {
  var button = document.getElementById('menuBtn');
  var sidebar = document.getElementById('raSidebar');
  var backdrop = document.querySelector('.ra-sidebar-backdrop');
  if (!button || !sidebar || !backdrop) return;

  var desktop = window.matchMedia('(min-width: 1200px)');
  var closeTimer = null;

  function setOpen(open) {
    clearTimeout(closeTimer);
    sidebar.classList.toggle('is-open', open);
    sidebar.setAttribute('aria-hidden', open ? 'false' : 'true');
    button.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.documentElement.classList.toggle('ra-sidebar-open', open);
    if (open) {
      backdrop.hidden = false;
      requestAnimationFrame(function () { backdrop.classList.add('is-open'); });
      var close = sidebar.querySelector('.ra-sidebar-close');
      if (close) close.focus({preventScroll: true});
    } else {
      backdrop.classList.remove('is-open');
      closeTimer = setTimeout(function () { backdrop.hidden = true; }, 300);
    }
  }

  function isOpen() { return sidebar.classList.contains('is-open'); }

  button.addEventListener('click', function () { setOpen(!isOpen()); });
  document.querySelectorAll('[data-sidebar-close]').forEach(function (el) {
    el.addEventListener('click', function () {
      setOpen(false);
      button.focus({preventScroll: true});
    });
  });
  sidebar.addEventListener('click', function (e) {
    if (e.target.closest('.ra-sidebar-nav a, .ra-sidebar-cta')) setOpen(false);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && isOpen()) {
      setOpen(false);
      button.focus({preventScroll: true});
    }
  });
  // Rotating a tablet / resizing to desktop closes the sidebar
  var onChange = function (e) { if (e.matches) setOpen(false); };
  if (desktop.addEventListener) desktop.addEventListener('change', onChange); else desktop.addListener(onChange);
})();

// Animated hero panels (Industrial / Training): on small screens keep the desktop arrangement (.ra-fit in
// responsive.css) and, when there is less room than the 500px design width, zoom the whole stage down to fit.
(function () {
  var DESIGN_WIDTH = 500;
  var panels = [
    {root: '.ra-industrial-hero', stage: '.ra-ind-stage', query: window.matchMedia('(max-width: 860px)')},
    {root: '.training-visual', stage: '.tr-stage', query: window.matchMedia('(max-width: 620px)')}
  ];

  function fit() {
    panels.forEach(function (panel) {
      var root = document.querySelector(panel.root);
      var stage = root && root.querySelector(panel.stage);
      if (!stage) return;
      var on = panel.query.matches;
      root.classList.toggle('ra-fit', on);
      stage.style.width = '';
      stage.style.zoom = '';
      if (!on) return;
      var box = stage.parentElement, cs = getComputedStyle(box);
      var room = box.clientWidth - parseFloat(cs.paddingLeft) - parseFloat(cs.paddingRight);
      if (room > 0 && room < DESIGN_WIDTH) {
        stage.style.width = DESIGN_WIDTH + 'px';
        stage.style.zoom = (room / DESIGN_WIDTH).toFixed(4);
      }
    });
  }

  var frame = null;
  function schedule() {
    if (frame) cancelAnimationFrame(frame);
    frame = requestAnimationFrame(fit);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fit); else fit();
  window.addEventListener('resize', schedule);
  window.addEventListener('orientationchange', schedule);
})();
