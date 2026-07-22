// Activa las entradas .kd-reveal cuando el elemento aparece en pantalla.
// Vanilla, sin dependencias, y respeta prefers-reduced-motion.
(function () {
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var els = document.querySelectorAll('.kd-reveal');
    if (!els.length) return;

    if (reduce || !('IntersectionObserver' in window)) {
        els.forEach(function (el) { el.classList.add('is-in'); });
        return;
    }

    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
        });
    }, { rootMargin: '0px 0px -10% 0px' });

    els.forEach(function (el) { io.observe(el); });
})();
