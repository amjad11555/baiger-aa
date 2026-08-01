/* ============================================================
   BAIGR Blog — motion layer (vanilla, dependency-light).
   • Lenis smooth scroll (progressive; native scroll if absent)
   • scroll-progress bar + navbar scrolled state
   • .reveal fade-ups + .wr word reveals (IntersectionObserver)
   • magnetic buttons, mobile menu, ambient particle field
   Respects prefers-reduced-motion.
   ============================================================ */
(function () {
  "use strict";
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var doc = document.documentElement;

  /* ---- Smooth scroll (Lenis) ---- */
  var lenis = null;
  if (!reduce && window.Lenis) {
    lenis = new window.Lenis({ duration: 1.05, smoothWheel: true });
    var raf = function (t) { lenis.raf(t); requestAnimationFrame(raf); };
    requestAnimationFrame(raf);
  }

  /* ---- Scroll progress + navbar ---- */
  var bar = document.getElementById("scroll-progress");
  var nav = document.getElementById("navbar");
  function onScroll() {
    var st = window.pageYOffset || doc.scrollTop;
    var h = doc.scrollHeight - doc.clientHeight;
    if (bar) bar.style.transform = "scaleX(" + (h > 0 ? st / h : 0) + ")";
    if (nav) nav.classList.toggle("is-scrolled", st > 12);
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* ---- Reveals ---- */
  function revealIO() {
    var els = document.querySelectorAll(".reveal, .wr");
    if (reduce || !("IntersectionObserver" in window)) {
      els.forEach(function (el) { el.classList.add("is-in"); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add("is-in"); io.unobserve(e.target); }
      });
    }, { rootMargin: "0px 0px -8% 0px", threshold: 0.06 });
    els.forEach(function (el) {
      if (el.classList.contains("wr")) splitWords(el);
      io.observe(el);
    });
  }

  // Wrap each word so it can rise from a mask — echoes the homepage.
  function splitWords(el) {
    if (reduce || el.dataset.split) return;
    el.dataset.split = "1";
    var words = el.textContent.split(/(\s+)/);
    el.textContent = "";
    words.forEach(function (w) {
      if (/^\s+$/.test(w)) { el.appendChild(document.createTextNode(w)); return; }
      var wrap = document.createElement("span");
      wrap.className = "wr-word";
      var inner = document.createElement("span");
      inner.textContent = w;
      wrap.appendChild(inner);
      el.appendChild(wrap);
    });
    // staggered rise via transition-delay
    var spans = el.querySelectorAll(".wr-word > span");
    spans.forEach(function (s, i) { s.style.transitionDelay = (i * 0.05) + "s"; });
  }

  /* ---- Magnetic buttons ---- */
  if (!reduce && window.matchMedia("(pointer:fine)").matches) {
    document.querySelectorAll(".magnetic").forEach(function (m) {
      m.addEventListener("mousemove", function (e) {
        var r = m.getBoundingClientRect();
        var x = e.clientX - r.left - r.width / 2;
        var y = e.clientY - r.top - r.height / 2;
        m.style.transform = "translate(" + x * 0.25 + "px," + y * 0.35 + "px)";
      });
      m.addEventListener("mouseleave", function () { m.style.transform = ""; });
    });
  }

  /* ---- Mobile menu ---- */
  var burger = document.getElementById("burger");
  var overlay = document.getElementById("menu-overlay");
  if (burger && overlay) {
    burger.addEventListener("click", function () {
      var open = overlay.classList.toggle("is-open");
      if (nav) nav.classList.toggle("is-open", open);
      burger.setAttribute("aria-expanded", open);
      document.body.style.overflow = open ? "hidden" : "";
    });
    overlay.querySelectorAll("a").forEach(function (a) {
      a.addEventListener("click", function () {
        overlay.classList.remove("is-open");
        if (nav) nav.classList.remove("is-open");
        burger.setAttribute("aria-expanded", "false");
        document.body.style.overflow = "";
      });
    });
  }

  /* ---- Ambient particle field (echoes the homepage 3D field) ---- */
  function field() {
    var c = document.getElementById("hero-field");
    if (!c || reduce) return;
    var ctx = c.getContext("2d");
    var dpr = Math.min(window.devicePixelRatio || 1, 2);
    var W, H, pts;
    var LIME = "214,224,106", IRIS = "114,107,214";

    function resize() {
      W = c.clientWidth; H = c.clientHeight;
      c.width = W * dpr; c.height = H * dpr;
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      var n = Math.min(46, Math.floor(W / 26));
      pts = [];
      for (var i = 0; i < n; i++) {
        pts.push({
          x: Math.random() * W, y: Math.random() * H,
          vx: (Math.random() - 0.5) * 0.28, vy: (Math.random() - 0.5) * 0.28,
          r: Math.random() * 2 + 1, iris: Math.random() > 0.5
        });
      }
    }
    function tick() {
      ctx.clearRect(0, 0, W, H);
      for (var i = 0; i < pts.length; i++) {
        var p = pts[i];
        p.x += p.vx; p.y += p.vy;
        if (p.x < 0 || p.x > W) p.vx *= -1;
        if (p.y < 0 || p.y > H) p.vy *= -1;
        for (var j = i + 1; j < pts.length; j++) {
          var q = pts[j], dx = p.x - q.x, dy = p.y - q.y, d = Math.hypot(dx, dy);
          if (d < 120) {
            ctx.strokeStyle = "rgba(" + IRIS + "," + (0.12 * (1 - d / 120)) + ")";
            ctx.lineWidth = 1;
            ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y); ctx.stroke();
          }
        }
        ctx.fillStyle = "rgba(" + (p.iris ? IRIS : LIME) + ",.55)";
        ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2); ctx.fill();
      }
      requestAnimationFrame(tick);
    }
    resize();
    window.addEventListener("resize", resize);
    requestAnimationFrame(tick);
  }

  function init() { revealIO(); field(); }
  if (document.readyState !== "loading") init();
  else document.addEventListener("DOMContentLoaded", init);
})();
