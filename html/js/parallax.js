/* ============================================================
   BAIGR — pointer-driven interactions:
   custom cursor + magnetic buttons. Fine pointers only.
   (The hero's 3D growth scene is pure CSS/SVG.)
   ============================================================ */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var finePointer = window.matchMedia("(pointer: fine)").matches;
    if (!finePointer || window.BAIGR.reduced || typeof gsap === "undefined")
      return;

    /* ---------- Custom cursor ---------- */
    var dot = document.getElementById("cursor-dot");
    var ring = document.getElementById("cursor-ring");
    document.body.dataset.customCursor = "on";

    var dotX = gsap.quickTo(dot, "x", { duration: 0.12, ease: "power2.out" });
    var dotY = gsap.quickTo(dot, "y", { duration: 0.12, ease: "power2.out" });
    var ringX = gsap.quickTo(ring, "x", { duration: 0.45, ease: "power3.out" });
    var ringY = gsap.quickTo(ring, "y", { duration: 0.45, ease: "power3.out" });

    var visible = false;

    window.addEventListener(
      "pointermove",
      function (e) {
        if (!visible) {
          visible = true;
          gsap.to([dot, ring], { autoAlpha: 1, duration: 0.3 });
        }
        dotX(e.clientX);
        dotY(e.clientY);
        ringX(e.clientX);
        ringY(e.clientY);
      },
      { passive: true }
    );

    window.addEventListener(
      "pointerover",
      function (e) {
        var interactive =
          e.target.closest && e.target.closest("a, button, [data-cursor]");
        gsap.to(ring, {
          scale: interactive ? 2.2 : 1,
          opacity: interactive ? 0.9 : 0.5,
          duration: 0.35,
          ease: "power3.out",
        });
      },
      { passive: true }
    );

    document.documentElement.addEventListener("pointerleave", function () {
      visible = false;
      gsap.to([dot, ring], { autoAlpha: 0, duration: 0.3 });
    });

    /* ---------- Magnetic elements ---------- */
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
