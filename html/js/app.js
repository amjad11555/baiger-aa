/* ============================================================
   BAIGR — UI behaviour: navbar, mobile menu, accordion,
   growth ticker, footer year.
   ============================================================ */
(function () {
  "use strict";

  /* ---------- Navbar scroll state ---------- */
  function initNavbar() {
    var navbar = document.getElementById("navbar");
    var onScroll = function () {
      navbar.classList.toggle("is-scrolled", window.scrollY > 24);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /* ---------- Mobile overlay menu ---------- */
  function initMenu() {
    var navbar = document.getElementById("navbar");
    var burger = document.getElementById("burger");
    var overlay = document.getElementById("menu-overlay");

    var close = function () {
      navbar.classList.remove("is-open");
      burger.setAttribute("aria-expanded", "false");
      burger.setAttribute("aria-label", "Open menu");
      overlay.hidden = true;
      document.body.style.overflow = "";
    };

    burger.addEventListener("click", function () {
      var open = overlay.hidden;
      if (!open) return close();

      navbar.classList.add("is-open");
      burger.setAttribute("aria-expanded", "true");
      burger.setAttribute("aria-label", "Close menu");
      overlay.hidden = false;
      document.body.style.overflow = "hidden";

      if (!window.BAIGR.reduced && typeof gsap !== "undefined") {
        gsap.fromTo(
          overlay.querySelectorAll("[data-menu-item]"),
          { yPercent: 60, autoAlpha: 0 },
          {
            yPercent: 0,
            autoAlpha: 1,
            duration: 0.7,
            stagger: 0.06,
            ease: "power3.out",
          }
        );
      }
    });

    overlay.querySelectorAll("a").forEach(function (a) {
      a.addEventListener("click", close);
    });
  }

  /* ---------- FAQ accordion (single open, animated height) ---------- */
  function initAccordion() {
    var items = document.querySelectorAll(".accordion__item");

    var collapse = function (item) {
      var trigger = item.querySelector(".accordion__trigger");
      var content = item.querySelector(".accordion__content");
      trigger.setAttribute("aria-expanded", "false");
      content.style.height = content.scrollHeight + "px";
      requestAnimationFrame(function () {
        content.style.transition = "height 0.26s cubic-bezier(0.22,1,0.36,1)";
        content.style.height = "0px";
      });
    };

    var expand = function (item) {
      var trigger = item.querySelector(".accordion__trigger");
      var content = item.querySelector(".accordion__content");
      trigger.setAttribute("aria-expanded", "true");
      content.style.transition = "height 0.32s cubic-bezier(0.22,1,0.36,1)";
      content.style.height = content.scrollHeight + "px";
      content.addEventListener("transitionend", function done(e) {
        if (e.propertyName !== "height") return;
        content.removeEventListener("transitionend", done);
        if (trigger.getAttribute("aria-expanded") === "true") {
          content.style.height = "auto";
        }
      });
    };

    items.forEach(function (item) {
      var trigger = item.querySelector(".accordion__trigger");
      trigger.addEventListener("click", function () {
        var isOpen = trigger.getAttribute("aria-expanded") === "true";
        items.forEach(function (other) {
          if (
            other !== item &&
            other
              .querySelector(".accordion__trigger")
              .getAttribute("aria-expanded") === "true"
          ) {
            collapse(other);
          }
        });
        if (isOpen) collapse(item);
        else expand(item);
      });
    });
  }

  /* ---------- Growth ticker (Growth · نمو · Büyüme) ---------- */
  function initTicker() {
    var el = document.getElementById("growth-ticker");
    if (!el || window.BAIGR.reduced) return;

    var index = 0;
    var words = function () {
      return (window.BAIGR.dict && window.BAIGR.dict.hero.ticker) || ["Growth"];
    };

    setInterval(function () {
      var list = words();
      if (typeof gsap === "undefined") {
        index = (index + 1) % list.length;
        el.textContent = list[index];
        return;
      }
      gsap.to(el, {
        yPercent: -120,
        opacity: 0,
        duration: 0.4,
        ease: "power2.in",
        onComplete: function () {
          index = (index + 1) % list.length;
          el.textContent = list[index];
          gsap.fromTo(
            el,
            { yPercent: 120, opacity: 0 },
            { yPercent: 0, opacity: 1, duration: 0.4, ease: "power2.out" }
          );
        },
      });
    }, 2600);
  }

  document.addEventListener("DOMContentLoaded", function () {
    initNavbar();
    initMenu();
    initAccordion();
    initTicker();
    var year = document.getElementById("year");
    if (year) year.textContent = String(new Date().getFullYear());
  });
})();
