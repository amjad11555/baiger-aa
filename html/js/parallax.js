/* ============================================================
   BAIGR — pointer-driven interactions: magnetic buttons.
   Fine pointers (desktop) only.
   (The hero's 3D parallax lives in three-scene.js.)

   The old JS-driven custom cursor was removed: replacing the
   native cursor with a GSAP-tweened dot+ring meant work on every
   pointermove, which showed up as lag "on mouse movement". The
   native cursor is instant and costs nothing.
   ============================================================ */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var finePointer = window.matchMedia("(pointer: fine)").matches;
    var wideEnough = window.matchMedia("(min-width: 1024px)").matches;
    if (!finePointer || !wideEnough || window.BAIGR.reduced ||
        typeof gsap === "undefined")
      return;

    /* ---------- Magnetic elements (desktop only) ---------- */
    document.querySelectorAll(".magnetic").forEach(function (el) {
      var strength = parseFloat(el.getAttribute("data-strength")) || 0.35;
      var xTo = gsap.quickTo(el, "x", { duration: 0.5, ease: "power3.out" });
      var yTo = gsap.quickTo(el, "y", { duration: 0.5, ease: "power3.out" });

      el.addEventListener("pointermove", function (e) {
        var rect = el.getBoundingClientRect();
        xTo((e.clientX - (rect.left + rect.width / 2)) * strength);
        yTo((e.clientY - (rect.top + rect.height / 2)) * strength);
      });

      el.addEventListener("pointerleave", function () {
        gsap.to(el, {
          x: 0,
          y: 0,
          duration: 0.9,
          ease: "elastic.out(1, 0.35)",
        });
      });
    });
  });
})();
