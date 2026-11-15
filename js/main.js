/* Nexabie — site behaviour */

(function () {
  "use strict";

  // Sticky header border on scroll
  var header = document.querySelector(".site-header");
  if (header) {
    var onScroll = function () {
      header.classList.toggle("is-scrolled", window.scrollY > 8);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  // Mobile menu
  var toggle = document.querySelector(".menu-toggle");
  var nav = document.querySelector(".nav");
  if (toggle && nav) {
    var setMenu = function (open) {
      nav.classList.toggle("is-open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      toggle.querySelector(".menu-label").textContent = open ? "Close" : "Menu";
    };
    toggle.addEventListener("click", function () {
      setMenu(!nav.classList.contains("is-open"));
    });
    nav.addEventListener("click", function (e) {
      if (e.target.tagName === "A") setMenu(false);
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && nav.classList.contains("is-open")) setMenu(false);
    });
    document.addEventListener("click", function (e) {
      if (
        nav.classList.contains("is-open") &&
        !nav.contains(e.target) &&
        !toggle.contains(e.target)
      ) {
        setMenu(false);
      }
    });
  }

  // Hero word rotator
  var rotator = document.querySelector(".rotator");
  if (rotator && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    var words = rotator.querySelectorAll("span");
    var i = 0;
    if (words.length > 1) {
      setInterval(function () {
        words[i].classList.remove("is-active");
        i = (i + 1) % words.length;
        words[i].classList.add("is-active");
      }, 2600);
    }
  }

  // Work filters
  var filters = document.querySelector(".filters");
  if (filters) {
    var cards = document.querySelectorAll("[data-category]");
    filters.addEventListener("click", function (e) {
      var btn = e.target.closest(".filter-btn");
      if (!btn) return;
      filters.querySelectorAll(".filter-btn").forEach(function (b) {
        b.classList.remove("is-active");
        b.setAttribute("aria-pressed", "false");
      });
      btn.classList.add("is-active");
      btn.setAttribute("aria-pressed", "true");
      var cat = btn.dataset.filter;
      cards.forEach(function (card) {
        var show = cat === "all" || card.dataset.category.split(" ").indexOf(cat) !== -1;
        card.classList.toggle("is-hidden", !show);
      });
    });
  }

  // Reveal on scroll
  var revealEls = document.querySelectorAll(".reveal");
  if (revealEls.length && "IntersectionObserver" in window) {
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12 }
    );
    revealEls.forEach(function (el) {
      // Stagger: siblings revealed together cascade left-to-right / top-to-bottom
      var siblings = el.parentElement
        ? el.parentElement.querySelectorAll(":scope > .reveal")
        : [];
      var i = Array.prototype.indexOf.call(siblings, el);
      if (i > 0) {
        el.style.setProperty("--rd", Math.min(i * 0.08, 0.4) + "s");
      }
      io.observe(el);
    });
  } else {
    revealEls.forEach(function (el) {
      el.classList.add("is-visible");
    });
  }

  // Contact form is handled by PHP (contact.php) — no JS interception.

  // Prefill service from ?service= query param
  var params = new URLSearchParams(window.location.search);
  var service = params.get("service");
  if (service) {
    var select = document.getElementById("field-service");
    if (select) {
      var match = Array.prototype.find.call(select.options, function (o) {
        return o.value === service;
      });
      if (match) select.value = service;
    }
  }

  // Year in footer
  var year = document.getElementById("year");
  if (year) year.textContent = String(new Date().getFullYear());
})();
