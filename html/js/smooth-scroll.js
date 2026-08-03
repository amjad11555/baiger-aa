/* ============================================================
   BAIGR — Lenis smooth scrolling, synced with GSAP's ticker.
   ============================================================ */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    if (window.BAIGR.reduced || typeof Lenis === "undefined") return;

    var lenis = new Lenis({
      lerp: 0.11,
      anchors: { offset: -72 },
      autoRaf: false,
    });

    gsap.ticker.add(function (time) {
      lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);

    if (window.ScrollTrigger) {
      lenis.on("scroll", ScrollTrigger.update);
    }

    window.BAIGR.lenis = lenis;
  });
})();
