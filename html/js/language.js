/* ============================================================
   BAIGR — multilingual system (ar / en / tr).
   - Browser language auto-detected before paint (inline head script).
   - Live switching without reload: every translatable node carries a
     data-i18n="path.in.dictionary" attribute.
   - Dictionaries load from translations/*.json; when the page is opened
     from file:// (no fetch allowed) English markup remains as-is.
   ============================================================ */
(function () {
  "use strict";

  var LOCALES = ["en", "ar", "tr"];
  var cache = {};

  window.BAIGR = window.BAIGR || {};
  window.BAIGR.locale = document.documentElement.lang || "ar";
  window.BAIGR.dict = null;
  window.BAIGR.reduced =
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function resolve(dict, path) {
    return path.split(".").reduce(function (acc, key) {
      return acc == null ? undefined : acc[key];
    }, dict);
  }

  function loadDict(locale) {
    if (cache[locale]) return Promise.resolve(cache[locale]);
    // Inlined dictionaries (single-file preview / file:// usage) win over fetch.
    if (window.BAIGR.__DICTS && window.BAIGR.__DICTS[locale]) {
      cache[locale] = window.BAIGR.__DICTS[locale];
      return Promise.resolve(cache[locale]);
    }
    return fetch("translations/" + locale + ".json")
      .then(function (res) {
        if (!res.ok) throw new Error("HTTP " + res.status);
        return res.json();
      })
      .then(function (json) {
        cache[locale] = json;
        return json;
      });
  }

  function applyDict(dict) {
    document.querySelectorAll("[data-i18n]").forEach(function (el) {
      var value = resolve(dict, el.getAttribute("data-i18n"));
      if (typeof value === "string") el.textContent = value;
    });

    // Composed strings (e.g. the screen-reader copy of split headlines)
    document.querySelectorAll("[data-i18n-compose]").forEach(function (el) {
      var parts = el.getAttribute("data-i18n-compose").split(/\s+/);
      el.textContent = parts
        .map(function (p) {
          return resolve(dict, p) || "";
        })
        .join("");
    });

    // Attribute translations: data-i18n-attr="aria-label:whatsapp,title:whatsapp"
    document.querySelectorAll("[data-i18n-attr]").forEach(function (el) {
      el.getAttribute("data-i18n-attr")
        .split(",")
        .forEach(function (pair) {
          var split = pair.split(":");
          var value = resolve(dict, split[1]);
          if (typeof value === "string") el.setAttribute(split[0], value);
        });
    });
  }

  function setPressedState(locale) {
    document.querySelectorAll(".lang-switch button").forEach(function (btn) {
      btn.setAttribute(
        "aria-pressed",
        btn.getAttribute("data-lang") === locale ? "true" : "false"
      );
    });
  }

  function setLocale(locale, initial) {
    if (LOCALES.indexOf(locale) < 0) locale = "ar";
    return loadDict(locale)
      .then(function (dict) {
        window.BAIGR.locale = locale;
        window.BAIGR.dict = dict;
        document.documentElement.lang = locale;
        document.documentElement.dir = locale === "ar" ? "rtl" : "ltr";
        try {
          if (!initial) localStorage.setItem("baigr-lang", locale);
        } catch (e) {}
        applyDict(dict);
        setPressedState(locale);
        document.dispatchEvent(
          new CustomEvent("baigr:langchange", {
            detail: { locale: locale, dict: dict, initial: !!initial },
          })
        );
      })
      .catch(function (err) {
        // file:// or offline: keep the static English markup working.
        console.warn(
          "[BAIGR] Could not load translations (" +
            err.message +
            "). Serve the folder with a static server for full multilingual support."
        );
        setPressedState(window.BAIGR.locale);
      });
  }

  window.BAIGR.setLocale = setLocale;
  window.BAIGR.t = function (path) {
    return window.BAIGR.dict ? resolve(window.BAIGR.dict, path) : undefined;
  };

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".lang-switch button").forEach(function (btn) {
      btn.addEventListener("click", function () {
        setLocale(btn.getAttribute("data-lang"));
      });
    });
    // Load the detected locale's dictionary (applies AR/TR text if needed).
    setLocale(window.BAIGR.locale, true);
  });
})();
