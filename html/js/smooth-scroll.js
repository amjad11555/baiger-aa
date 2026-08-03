/* ============================================================
   BAIGR — Lenis smooth scrolling, synced with GSAP's ticker.
   ============================================================ */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    if (window.BAIGR.reduced || typeof Lenis === "undefined") return;
    // Desktop only. On touch devices native momentum scroll is smoother
    // and cheaper than running Lenis' rAF loop + ScrollTrigger.update.
    var finePointer = window.matchMedia("(pointer: fine)").matches;
    var wideEnough = window.matchMedia("(min-width: 1024px)").matches;
    if (!finePointer || !wideEnough) return;

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
