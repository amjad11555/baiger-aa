/* ============================================================
   BAIGR — GSAP animation layer.
   - .reveal          fade-up blocks on scroll
   - .wr              masked word-by-word headline reveals (SplitText)
   - [data-hero-fade] orchestrated hero entrance (waits for fonts → CLS 0)
   - portfolio covers clip-path unmask
   - process line     draws itself on scroll (scrubbed)
   All word reveals re-run on language switch.
   ============================================================ */
(function () {
  "use strict";

  var wrState = []; // { el, split, tween, io }

  function killWordReveals() {
    wrState.forEach(function (s) {
      if (s.io) s.io.disconnect();
      if (s.tween) {
        if (s.tween.scrollTrigger) s.tween.scrollTrigger.kill();
        s.tween.kill();
      }
      if (s.split) s.split.revert();
    });
    wrState = [];
  }

  function initWordReveal(el) {
    var onScroll = el.getAttribute("data-wr") !== "load";
    var state = { el: el, split: null, tween: null, io: null };
    wrState.push(state);

    var run = function () {
      if (state.done) return;
      state.done = true;
      gsap.set(el, { autoAlpha: 1 });
      state.split = new SplitText(el, {
        type: "words",
        mask: "words",
        wordsClass: "wr-word",
        aria: "none",
      });
      gsap.set(state.split.words, { yPercent: 115, opacity: 0 });
      state.tween = gsap.to(state.split.words, {
        yPercent: 0,
        opacity: 1,
        duration: 1.05,
        delay: onScroll ? 0 : 0.35,
        stagger: 0.06,
        ease: "power4.out",
        scrollTrigger: onScroll
          ? { trigger: el, start: "top 88%", once: true }
          : undefined,
      });
    };

    if (onScroll) {
      // Don't pay the splitting cost at load for headings below the fold.
      state.io = new IntersectionObserver(
        function (entries) {
          if (entries[0].isIntersecting) {
            state.io.disconnect();
            document.fonts.ready.then(run);
          }
        },
        { rootMargin: "600px 0px" }
      );
      state.io.observe(el);
    } else {
      document.fonts.ready.then(run);
    }
  }

  function initWordReveals() {
    document.querySelectorAll(".wr").forEach(initWordReveal);
  }

  function initHeroEntrance() {
    var targets = document.querySelectorAll("[data-hero-fade]");
    if (!targets.length) return;
    document.fonts.ready.then(function () {
      gsap.timeline({ delay: 0.15 }).fromTo(
        targets,
        { autoAlpha: 0, y: 24 },
        { autoAlpha: 1, y: 0, duration: 1, stagger: 0.12, ease: "power3.out" },
        0.5
      );
    });
  }

  function initReveals(done) {
    // Chunked creation: building every ScrollTrigger in one task causes a
    // forced-reflow storm; slices keep each main-thread task short.
    var els = Array.prototype.slice.call(document.querySelectorAll(".reveal"));
    var SLICE = 12;

    var work = function () {
      els.splice(0, SLICE).forEach(function (el) {
        var delay = parseFloat(el.getAttribute("data-delay")) || 0;
        gsap.fromTo(
          el,
          { autoAlpha: 0, y: 28 },
          {
            autoAlpha: 1,
            y: 0,
            duration: 1.1,
            delay: delay,
            ease: "power3.out",
            scrollTrigger: { trigger: el, start: "top 88%", once: true },
          }
        );
      });
      if (els.length) schedule(work);
      else if (done) done();
    };
    schedule(work);
  }

  function schedule(fn) {
    if ("requestIdleCallback" in window) {
      requestIdleCallback(fn, { timeout: 400 });
    } else {
      setTimeout(fn, 40);
    }
  }

  function initCovers() {
    document.querySelectorAll("[data-cover]").forEach(function (cover) {
      gsap.fromTo(
        cover,
        { clipPath: "inset(12% 8% 12% 8% round 16px)", scale: 1.08 },
        {
          clipPath: "inset(0% 0% 0% 0% round 16px)",
          scale: 1,
          duration: 1.3,
          ease: "power3.out",
          scrollTrigger: { trigger: cover, start: "top 85%", once: true },
        }
      );
    });
  }

  function initProcessLine() {
    var line = document.getElementById("process-line");
    if (!line) return;
    gsap.fromTo(
      line,
      { scaleX: 0 },
      {
        scaleX: 1,
        ease: "none",
        scrollTrigger: {
          trigger: line,
          start: "top 80%",
          end: "bottom 35%",
          scrub: 0.6,
        },
      }
    );
  }

  // Top scroll-progress bar — a thin lime→iris line that fills as you descend.
  function initScrollProgress() {
    var bar = document.getElementById("scroll-progress");
    if (!bar) return;
    gsap.fromTo(
      bar,
      { scaleX: 0 },
      {
        scaleX: 1,
        ease: "none",
        scrollTrigger: { start: 0, end: "max", scrub: 0.3 },
      }
    );
  }

  // Numbers that count up as they enter view — the "prosperity" beat.
  var counterState = [];
  function killCounters() {
    counterState.forEach(function (s) {
      if (s.st) s.st.kill();
      if (s.tween) s.tween.kill();
    });
    counterState = [];
  }
  function initCounters() {
    document.querySelectorAll("[data-count]").forEach(function (el) {
      var raw = el.getAttribute("data-count-target") || el.textContent.trim();
      el.setAttribute("data-count-target", raw); // remember across language switches
      var m = raw.match(/^(\D*)(\d[\d,]*(?:\.\d+)?)(.*)$/s);
      if (!m) return;
      var prefix = m[1],
        numStr = m[2].replace(/,/g, ""),
        suffix = m[3],
        decimals = (numStr.split(".")[1] || "").length,
        target = parseFloat(numStr),
        obj = { v: 0 };
      el.textContent = prefix + (0).toFixed(decimals) + suffix;
      var tween = gsap.to(obj, {
        v: target,
        duration: 1.6,
        ease: "power2.out",
        onUpdate: function () {
          el.textContent =
            prefix +
            obj.v.toLocaleString("en-US", {
              minimumFractionDigits: decimals,
              maximumFractionDigits: decimals,
            }) +
            suffix;
        },
        paused: true,
      });
      var st = ScrollTrigger.create({
        trigger: el,
        start: "top 85%",
        once: true,
        onEnter: function () {
          tween.play();
        },
      });
      counterState.push({ tween: tween, st: st });
    });
  }

  // Cinematic hero: content drifts up and softens as you scroll away.
  function initHeroParallax() {
    var content = document.querySelector(".hero__content");
    if (!content) return;
    gsap.to(content, {
      yPercent: -16,
      autoAlpha: 0.25,
      ease: "none",
      scrollTrigger: {
        trigger: ".hero",
        start: "top top",
        end: "bottom top",
        scrub: 0.4,
      },
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    if (typeof gsap === "undefined") return;
    gsap.registerPlugin(ScrollTrigger, SplitText);

    if (window.BAIGR.reduced) {
      // animations.css already forces everything visible.
      var line = document.getElementById("process-line");
      if (line) line.style.transform = "scaleX(1)";
      var bar = document.getElementById("scroll-progress");
      if (bar) bar.style.transform = "scaleX(1)";
      return;
    }

    initScrollProgress();
    initHeroEntrance();
    initHeroParallax();
    initWordReveals();

    // Below-the-fold triggers build lazily in idle slices — first paint
    // and the hero entrance stay off the critical path.
    initReveals(function () {
      initCovers();
      initProcessLine();
      initCounters();
    });

    // Language switch: SplitText.revert() restores the pre-switch words,
    // so re-apply the new dictionary text after reverting, then re-split.
    document.addEventListener("baigr:langchange", function (e) {
      if (e.detail.initial) return;
      killWordReveals();
      document.querySelectorAll(".wr[data-i18n]").forEach(function (el) {
        var value = window.BAIGR.t(el.getAttribute("data-i18n"));
        if (typeof value === "string") el.textContent = value;
      });
      initWordReveals();
      initHeroEntrance();
      killCounters();
      initCounters();
      ScrollTrigger.refresh();
    });
  });
})();
