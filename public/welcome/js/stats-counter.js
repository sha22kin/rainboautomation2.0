// Home page stats: count each number up from 0 when the stats bar scrolls into view.
// Values such as "4+", "19" and "360°" are read from the markup; non-numeric ones ("End-to-End") stay as they are.
(function () {
  var items = Array.prototype.slice.call(document.querySelectorAll('.stats .stat b'))
    .map(function (el) {
      var match = el.textContent.trim().match(/^(\d+)(.*)$/);
      return match ? { el: el, target: parseInt(match[1], 10), suffix: match[2] } : null;
    })
    .filter(Boolean);

  if (!items.length) return;
  if (!('IntersectionObserver' in window)) return;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var duration = 1800;

  function easeOut(t) {
    return 1 - Math.pow(1 - t, 3);
  }

  function count(item) {
    var start = Date.now();
    var timer = setInterval(function () {
      var progress = Math.min((Date.now() - start) / duration, 1);
      item.el.textContent = Math.round(item.target * easeOut(progress)) + item.suffix;
      if (progress >= 1) clearInterval(timer);
    }, 16);
  }

  items.forEach(function (item) {
    item.el.textContent = '0' + item.suffix;
  });

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      observer.disconnect();
      items.forEach(count);
    });
  }, { threshold: 0.4 });

  observer.observe(document.querySelector('.stats'));
})();
