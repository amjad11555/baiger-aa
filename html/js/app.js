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
    initWelcome();
    initAssistant();
    var year = document.getElementById("year");
    if (year) year.textContent = String(new Date().getFullYear());
  });

  /* ---------- Welcome toast (once per visit) ---------- */
  function initWelcome() {
    var toast = document.getElementById("welcome-toast");
    var close = document.getElementById("welcome-close");
    if (!toast) return;
    var dismissed = false;
    try {
      dismissed = sessionStorage.getItem("baigr-welcomed") === "1";
    } catch (e) {}
    var hide = function () {
      toast.classList.remove("is-in");
      try {
        sessionStorage.setItem("baigr-welcomed", "1");
      } catch (e) {}
    };
    if (!dismissed) {
      setTimeout(function () {
        toast.classList.add("is-in");
      }, 2200);
      setTimeout(hide, 12000); // auto-dismiss
    }
    if (close) close.addEventListener("click", hide);
    window.__baigrHideWelcome = hide;
  }

  /* ---------- AI assistant (chips + free-text + human handoff) ---------- */
  function initAssistant() {
    var panel = document.getElementById("assistant");
    var body = document.getElementById("assistant-body");
    var openBtn = document.getElementById("assistant-open");
    var closeBtn = document.getElementById("assistant-close");
    var form = document.getElementById("assistant-form");
    var input = document.getElementById("assistant-input");
    if (!panel || !body || !openBtn) return;

    function t() {
      return (window.BAIGR.dict && window.BAIGR.dict.assistant) || null;
    }

    // Fold Arabic hamza/alef/taa variants + lowercase so matching is forgiving.
    function normalize(s) {
      return String(s)
        .toLowerCase()
        .replace(/[ً-ٰٟ]/g, "")       // tashkeel
        .replace(/[أإآ]/g, "ا")
        .replace(/ة/g, "ه")
        .replace(/[ىي]/g, "ي")
        .replace(/[ؤئ]/g, "ء")
        .trim();
    }

    function bubble(text, who) {
      var el = document.createElement("div");
      el.className = "assistant__bubble" + (who === "user" ? " assistant__bubble--user" : "");
      el.textContent = text;
      body.appendChild(el);
      body.scrollTop = body.scrollHeight;
      return el;
    }

    // Pick the best reply for a free-typed message from the intent table.
    function replyFor(text) {
      var a = t();
      if (!a) return "";
      var q = normalize(text);
      var best = null, bestScore = 0;
      (a.intents || []).forEach(function (intent) {
        var score = 0;
        (intent.k || []).forEach(function (kw) {
          if (kw && q.indexOf(normalize(kw)) !== -1) score += kw.length;
        });
        if (score > bestScore) { bestScore = score; best = intent; }
      });
      return best ? best.a : a.fallback;
    }

    // Conversation history sent to the server-side Claude proxy.
    // Falls back to the local intent table when the proxy is
    // unavailable (offline preview, config missing, network error).
    var convo = [];
    var CHAT_ENDPOINT = "chat.php";

    function currentLocale() {
      return (window.BAIGR && window.BAIGR.lang) ||
        document.documentElement.getAttribute("lang") || "ar";
    }

    // Ask the smart proxy; resolves to the reply text, or null on
    // any failure so the caller can fall back gracefully.
    function askServer(text) {
      convo.push({ role: "user", content: text });
      return fetch(CHAT_ENDPOINT, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ locale: currentLocale(), messages: convo })
      })
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (data) {
          if (data && data.ok && data.reply) {
            convo.push({ role: "assistant", content: data.reply });
            return data.reply;
          }
          return null;
        })
        .catch(function () { return null; });
    }

    var typingEl = null;
    function showTyping() {
      typingEl = document.createElement("div");
      typingEl.className = "assistant__typing";
      typingEl.setAttribute("aria-label", "…");
      typingEl.innerHTML = "<span></span><span></span><span></span>";
      body.appendChild(typingEl);
      body.scrollTop = body.scrollHeight;
    }
    function hideTyping() {
      if (typingEl && typingEl.parentNode) typingEl.parentNode.removeChild(typingEl);
      typingEl = null;
    }

    // Bot answers a *static* (curated) reply with a short human beat.
    function answer(text) {
      showTyping();
      var delay = 500 + Math.min(text.length * 8, 900);
      setTimeout(function () {
        hideTyping();
        bubble(text, "bot");
      }, delay);
    }

    // Bot answers a *free-typed* message: try the smart proxy first,
    // fall back to the local intent table if it's unavailable.
    function answerLive(userText) {
      showTyping();
      var started = Date.now();
      askServer(userText).then(function (reply) {
        var text = reply || replyFor(userText);
        // keep a minimum "typing" beat so it never snaps back instantly
        var wait = Math.max(0, 450 - (Date.now() - started));
        setTimeout(function () {
          hideTyping();
          bubble(text, "bot");
        }, wait);
      });
    }

    function renderChips() {
      var a = t();
      if (!a) return;
      var hint = document.createElement("p");
      hint.className = "assistant__hint";
      hint.textContent = a.hint;
      body.appendChild(hint);
      var wrap = document.createElement("div");
      wrap.className = "assistant__chips";
      a.qa.forEach(function (item) {
        var chip = document.createElement("button");
        chip.className = "assistant__chip";
        chip.type = "button";
        chip.textContent = item.q;
        chip.addEventListener("click", function () {
          bubble(item.q, "user");
          // seed the server context with this curated exchange
          convo.push({ role: "user", content: item.q });
          convo.push({ role: "assistant", content: item.a });
          answer(item.a);
        });
        wrap.appendChild(chip);
      });
      body.appendChild(wrap);
      body.scrollTop = body.scrollHeight;
    }

    function render() {
      var a = t();
      if (!a) return;
      hideTyping();
      body.innerHTML = "";
      bubble(a.greeting, "bot");
      renderChips();
      if (input && a.placeholder) input.setAttribute("placeholder", a.placeholder);
    }

    function open() {
      panel.hidden = false;
      requestAnimationFrame(function () {
        panel.classList.add("is-open");
      });
      openBtn.setAttribute("aria-expanded", "true");
      if (window.__baigrHideWelcome) window.__baigrHideWelcome();
      if (input) setTimeout(function () { input.focus(); }, 380);
    }
    function close() {
      panel.classList.remove("is-open");
      openBtn.setAttribute("aria-expanded", "false");
      setTimeout(function () {
        panel.hidden = true;
      }, 350);
    }

    if (form && input) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        var msg = input.value.trim();
        if (!msg) return;
        bubble(msg, "user");
        input.value = "";
        answerLive(msg);
      });
    }

    render();
    openBtn.addEventListener("click", function () {
      panel.hidden ? open() : close();
    });
    if (closeBtn) closeBtn.addEventListener("click", close);
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && !panel.hidden) close();
    });
    // Rebuild in the new language on switch.
    document.addEventListener("baigr:langchange", function () {
      render();
    });
  }
})();
