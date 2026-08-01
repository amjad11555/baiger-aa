/* ============================================================
   BAIGR Blog — lightweight motion layer (no dependencies).
   Native scroll (fast). Reveals + progress + magnetic + menu +
   an ambient particle field that PAUSES when off-screen.
   Respects prefers-reduced-motion.
   ============================================================ */
(function () {
  "use strict";
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var doc = document.documentElement;

  /* ---- Scroll progress + navbar state (rAF-throttled) ---- */
  var bar = document.getElementById("scroll-progress");
  var nav = document.getElementById("navbar");
  var ticking = false;
  function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(function () {
      var st = window.pageYOffset || doc.scrollTop;
      var h = doc.scrollHeight - doc.clientHeight;
      if (bar) bar.style.transform = "scaleX(" + (h > 0 ? st / h : 0) + ")";
      if (nav) nav.classList.toggle("is-scrolled", st > 12);
      ticking = false;
    });
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* ---- Reveal on scroll ---- */
  function reveals() {
    var els = document.querySelectorAll(".reveal");
    if (reduce || !("IntersectionObserver" in window)) {
      els.forEach(function (el) { el.classList.add("is-in"); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add("is-in"); io.unobserve(e.target); }
      });
    }, { rootMargin: "0px 0px -6% 0px", threshold: 0.05 });
    els.forEach(function (el) { io.observe(el); });
  }

  /* ---- Magnetic buttons (fine pointer only) ---- */
  function magnetic() {
    if (reduce || !window.matchMedia("(pointer:fine)").matches) return;
    document.querySelectorAll(".magnetic").forEach(function (m) {
      m.addEventListener("mousemove", function (e) {
        var r = m.getBoundingClientRect();
        m.style.transform = "translate(" + (e.clientX - r.left - r.width / 2) * 0.22 + "px," + (e.clientY - r.top - r.height / 2) * 0.3 + "px)";
      });
      m.addEventListener("mouseleave", function () { m.style.transform = ""; });
    });
  }

  /* ---- Mobile menu ---- */
  function menu() {
    var burger = document.getElementById("burger");
    var overlay = document.getElementById("menu-overlay");
    if (!burger || !overlay) return;
    function set(open) {
      overlay.classList.toggle("is-open", open);
      if (nav) nav.classList.toggle("is-open", open);
      burger.setAttribute("aria-expanded", open);
      document.body.style.overflow = open ? "hidden" : "";
    }
    burger.addEventListener("click", function () { set(!overlay.classList.contains("is-open")); });
    overlay.querySelectorAll("a").forEach(function (a) { a.addEventListener("click", function () { set(false); }); });
  }

  /* ---- Ambient particle field — light, and paused when off-screen ---- */
  function field() {
    var c = document.getElementById("hero-field");
    if (!c || reduce) return;
    var ctx = c.getContext("2d");
    var dpr = Math.min(window.devicePixelRatio || 1, 1.5);
    var W = 0, H = 0, pts = [], raf = 0, visible = true;
    var LIME = "214,224,106", IRIS = "114,107,214", LINK = 96;

    function resize() {
      W = c.clientWidth; H = c.clientHeight;
      c.width = W * dpr; c.height = H * dpr;
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      var n = Math.min(28, Math.floor(W / 40));
      pts = [];
      for (var i = 0; i < n; i++) {
        pts.push({
          x: Math.random() * W, y: Math.random() * H,
          vx: (Math.random() - 0.5) * 0.24, vy: (Math.random() - 0.5) * 0.24,
          r: Math.random() * 1.8 + 1, iris: Math.random() > 0.5
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
          if (d < LINK) {
            ctx.strokeStyle = "rgba(" + IRIS + "," + (0.11 * (1 - d / LINK)) + ")";
            ctx.lineWidth = 1;
            ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y); ctx.stroke();
          }
        }
        ctx.fillStyle = "rgba(" + (p.iris ? IRIS : LIME) + ",.5)";
        ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, 6.283); ctx.fill();
      }
      raf = requestAnimationFrame(tick);
    }
    function start() { if (!raf) raf = requestAnimationFrame(tick); }
    function stop() { if (raf) { cancelAnimationFrame(raf); raf = 0; } }

    resize();
    window.addEventListener("resize", resize, { passive: true });
    // Pause the loop whenever the hero scrolls out of view (saves CPU/battery).
    if ("IntersectionObserver" in window) {
      new IntersectionObserver(function (e) {
        visible = e[0].isIntersecting;
        visible ? start() : stop();
      }, { threshold: 0 }).observe(c);
    }
    // Pause when the tab is hidden.
    document.addEventListener("visibilitychange", function () {
      document.hidden || !visible ? stop() : start();
    });
    start();
  }

  function init() { reveals(); magnetic(); menu(); field(); }
  if (document.readyState !== "loading") init();
  else document.addEventListener("DOMContentLoaded", init);
})();
