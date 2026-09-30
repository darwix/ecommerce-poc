// Single product page: pill UI over WC's own variation <select> (WC's wc-add-to-cart-variation.js
// still does the real matching/stock/price logic — this only mirrors clicks onto the selects it reads),
// wishlist heart (localStorage; no WC backend yet), gallery thumbs, and the "added to cart" popup.
(function () {
  var $ = function (s, ctx) {
    return (ctx || document).querySelector(s);
  };
  var $$ = function (s, ctx) {
    return Array.prototype.slice.call((ctx || document).querySelectorAll(s));
  };

  // gallery thumbs
  document.addEventListener("click", function (e) {
    var b = e.target.closest(".pd-thumbs button[data-img]");
    if (!b) return;
    var img = $("#pdImg");
    var src = b.querySelector("img");
    if (img) img.src = b.dataset.full || (src && src.src);
    if (img) img.removeAttribute("srcset");
    $$(".pd-thumbs button").forEach(function (t) {
      t.classList.toggle("on", t === b);
    });
  });

  // pill UI for each attribute <select> in the WC variation form
  function buildPills(select) {
    if (select.dataset.pillified) return;
    select.dataset.pillified = "1";
    var wrap = document.createElement("div");
    wrap.className = "pd-attr-opts";
    $$("option", select).forEach(function (opt) {
      if (!opt.value) return;
      var btn = document.createElement("button");
      btn.type = "button";
      btn.textContent = opt.textContent;
      btn.dataset.value = opt.value;
      btn.disabled = opt.disabled;
      btn.addEventListener("click", function () {
        select.value = opt.value;
        select.dispatchEvent(new Event("change", { bubbles: true }));
        $$("button", wrap).forEach(function (b) {
          b.classList.toggle("on", b === btn);
        });
      });
      wrap.appendChild(btn);
    });
    select.insertAdjacentElement("afterend", wrap);
    // keep pills in sync if WC disables/resets options (e.g. out-of-stock combo)
    new MutationObserver(function () {
      $$("option", select).forEach(function (opt, i) {
        var btn = wrap.children[i - 1]; // option[0] is the placeholder, has no button
        if (btn) btn.disabled = opt.disabled || btn.classList.contains("out");
      });
    }).observe(select, {
      attributes: true,
      subtree: true,
      attributeFilter: ["disabled"],
    });
  }
  $$('.variations select[name^="attribute_"]').forEach(buildPills);

  // WC leaves an out-of-stock variation selectable and explains itself in a notice;
  // the mockup greys the value out and labels it "Habis" instead. A value is sold out
  // when every variation carrying it is out of stock.
  (function markSoldOut() {
    var form = $("form.variations_form");
    if (!form || !form.dataset.product_variations) return;
    var vars;
    try {
      vars = JSON.parse(form.dataset.product_variations);
    } catch (e) {
      return;
    }
    if (!vars) return; // ajax variation form: the data isn't inlined
    $$('.variations select[name^="attribute_"]').forEach(function (select) {
      var name = select.name;
      var wrap = select.nextElementSibling;
      if (!wrap || !wrap.classList.contains("pd-attr-opts")) return;
      $$("button", wrap).forEach(function (btn) {
        var v = btn.dataset.value;
        var matching = vars.filter(function (x) {
          var a = x.attributes[name];
          return !a || a === v; // "any" matches every value
        });
        if (!matching.length || matching.some(function (x) { return x.is_in_stock; })) return;
        btn.disabled = true;
        btn.classList.add("out");
        btn.appendChild(document.createElement("small")).textContent = "Habis";
      });
      // don't leave a sold-out value preselected
      var on = $("button.on.out", wrap);
      if (on) {
        on.classList.remove("on");
        select.value = "";
        select.dispatchEvent(new Event("change", { bubbles: true }));
      }
    });
  })();

  // wishlist heart (client-only for now)
  var wishKey = "lb-wish";
  var getWish = function () {
    try {
      return JSON.parse(localStorage.getItem(wishKey)) || [];
    } catch (e) {
      return [];
    }
  };
  var setWish = function (v) {
    try {
      localStorage.setItem(wishKey, JSON.stringify(v));
    } catch (e) {}
  };
  $$("[data-wish-id]").forEach(function (btn) {
    btn.classList.toggle("on", getWish().indexOf(+btn.dataset.wishId) > -1);
  });
  // delegated so cards injected later (wishlist tab ajax) work too
  document.addEventListener("click", function (e) {
    var btn = e.target.closest("[data-wish-id]");
    if (!btn) return;
    var id = +btn.dataset.wishId,
      w = getWish(),
      i = w.indexOf(id);
    i < 0 ? w.push(id) : w.splice(i, 1);
    setWish(w);
    btn.classList.toggle("on", i < 0);
  });

  // "added to cart" popup — real WC add-to-cart happened server-side (added-to-cart query var,
  // or jQuery's added_to_cart event for the ajax case in product loops/cards elsewhere)
  function openAdded(line) {
    var d = document.getElementById("added");
    if (!d) {
      d = document.createElement("dialog");
      d.id = "added";
      document.body.appendChild(d);
    }
    // recommendations: reuse cards already rendered on the page (related row on a product page,
    // otherwise whatever grid the visitor is looking at)
    var pool = $$(".pd-more .card");
    if (!pool.length)
      pool = $$(".grid .card").filter(function (c) {
        return c !== line.card;
      });
    var recs = pool
      .slice(0, 4)
      .map(function (c) {
        return c.outerHTML;
      })
      .join("");
    d.innerHTML =
      '<form method="dialog"><div class="dhead"><h2><svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>Masuk keranjang</h2>' +
      '<button class="x" value="close" aria-label="Tutup"><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg></button></div>' +
      '<div class="dprod wide-prod">' +
      (line.image ? '<img src="' + line.image + '" alt="">' : "") +
      "<div><b>" +
      line.name +
      "</b>" +
      (line.meta ? "<small>" + line.meta + "</small>" : "") +
      '<span class="price">' +
      (line.price || "") +
      "</span></div>" +
      '<div class="dacts"><a class="btn-sec" href="' +
      (window.acmeCartUrl || "/cart/") +
      '">Lihat keranjang</a>' +
      '<a class="btn" href="' +
      (window.acmeCheckoutUrl || "/checkout/") +
      '">Checkout</a></div></div>' +
      (recs
        ? '<h3 class="dsub">Mungkin kamu suka</h3><div class="recs">' +
          recs +
          "</div>"
        : "") +
      "</form>";
    d.addEventListener("click", function (e) {
      if (e.target === d) d.close();
    });
    d.showModal();
  }

  if (window.acmeJustAdded) openAdded(window.acmeJustAdded);
  window.acmeOpenAdded = openAdded; // used by quick-add.js after a card's "+ Keranjang"
})();
