// "+ Keranjang" on a product card: pick colour / size / qty in a dialog, then add via ajax
// (includes/quick-add.php). Sold-out values are disabled; a sold-out product can't be added.
(function () {
  var ajax = function () {
    return window.acmeAjax;
  };
  var X =
    '<button class="x" value="close" aria-label="' + acmeT('Close') + '"><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg></button>';
  var esc = function (s) {
    var d = document.createElement("div");
    d.textContent = s;
    return d.innerHTML;
  };
  // a variation matches when each of its attributes is "any" ("") or equals the wanted value
  var matches = function (v, want) {
    return Object.keys(want).every(function (k) {
      return v.attrs[k] === "" || v.attrs[k] === want[k];
    });
  };

  function open(card, p) {
    var d = document.getElementById("qadd");
    if (!d) {
      d = document.createElement("dialog");
      d.id = "qadd";
      document.body.appendChild(d);
    }
    var sel = {};
    var qty = 1;
    var busy = false;
    var inStock = function (want) {
      return p.vars.some(function (v) {
        return v.left > 0 && matches(v, want);
      });
    };
    var pickable = function (key, val) {
      var want = Object.assign({}, sel);
      want[key] = val;
      return inStock(want);
    };
    // first in-stock value of the first attribute, like the mockup preselects a colour
    if (p.attrs[0]) {
      var first = p.attrs[0].options.filter(function (o) {
        return pickable(p.attrs[0].key, o);
      })[0];
      if (first) sel[p.attrs[0].key] = first;
    }

    d.innerHTML =
      '<form method="dialog"><div class="dhead"><h2>' + acmeT('Add to cart') + '</h2>' + X + "</div>" +
      '<div class="dprod">' + (p.image ? '<img src="' + esc(p.image) + '" alt="">' : "") +
      "<div><b>" + esc(p.name) + "</b><small>" + esc(p.sku) + '</small><span class="price">' + p.price + "</span></div></div>" +
      p.attrs.map(function (a) {
        return "<fieldset><legend>" + esc(a.label) + '</legend><div class="opts">' +
          a.options.map(function (o) {
            return '<button type="button" data-k="' + esc(a.key) + '" data-v="' + esc(o) + '">' + esc(o) + "</button>";
          }).join("") + "</div></fieldset>";
      }).join("") +
      '<div class="dfoot"><div class="qty"><button type="button" data-q="-1" aria-label="' + acmeT('Decrease') + '">−</button><span data-qn>1</span>' +
      '<button type="button" data-q="1" aria-label="' + acmeT('Increase') + '">+</button></div><small class="stockline"></small></div>' +
      '<p class="hint" role="alert" hidden></p><button type="button" class="btn wide" data-go>' + acmeT('Add to cart') + '</button></form>';

    var q = function (s) {
      return d.querySelector(s);
    };
    var hint = function (msg) {
      q(".hint").textContent = msg || "";
      q(".hint").hidden = !msg;
    };
    var refresh = function () {
      d.querySelectorAll("[data-k]").forEach(function (b) {
        var ok = pickable(b.dataset.k, b.dataset.v);
        b.disabled = !ok;
        b.classList.toggle("on", sel[b.dataset.k] === b.dataset.v);
      });
      var chosen = p.attrs.every(function (a) { return sel[a.key]; });
      var v = p.vars.filter(function (x) { return matches(x, sel); })[0];
      var soldOut = p.attrs.length ? !inStock({}) || (chosen && (!v || v.left <= 0)) : p.left <= 0;
      var left = p.attrs.length ? (chosen && v ? v.left : 99) : p.left;
      qty = Math.max(1, Math.min(qty, left || 1));
      q("[data-qn]").textContent = qty;
      q('[data-q="-1"]').disabled = soldOut || qty <= 1;
      q('[data-q="1"]').disabled = soldOut || qty >= left;
      q(".stockline").textContent = soldOut ? acmeT("Sold out") : left <= 5 ? acmeT("Only %d left", left) : acmeT("In stock");
      var go = q("[data-go]");
      go.disabled = soldOut || busy;
      go.textContent = soldOut ? acmeT("Sold out") : acmeT("Add to cart");
    };

    d.onclick = function (e) {
      if (e.target === d) return d.close();
      var b = e.target.closest("button");
      if (!b || b.disabled) return;
      if (b.dataset.k) {
        sel[b.dataset.k] = b.dataset.v;
        // the other choice no longer exists in stock with this one: keep only the click
        if (!inStock(sel)) {
          sel = {};
          sel[b.dataset.k] = b.dataset.v;
        }
        hint("");
      }
      if (b.dataset.q) qty += +b.dataset.q;
      if (b.hasAttribute("data-go")) return add();
      refresh();
    };

    function add() {
      var missing = p.attrs.filter(function (a) { return !sel[a.key]; })[0];
      if (missing) return hint(acmeT("Select %s first", missing.label.toLowerCase()));
      var v = p.vars.filter(function (x) { return matches(x, sel); })[0];
      var body = new URLSearchParams({ action: "acme_quick_add", id: p.id, qty: qty, variation_id: v ? v.id : 0 });
      Object.keys(sel).forEach(function (k) {
        body.set("attrs[" + k + "]", sel[k]);
      });
      busy = true;
      refresh();
      fetch(ajax(), { method: "POST", body: body })
        .then(function (r) { return r.json(); })
        .then(function (r) {
          busy = false;
          if (!r.success) {
            refresh();
            return hint(r.data || acmeT("Failed to add to cart."));
          }
          Object.keys(r.data.fragments).forEach(function (s) {
            document.querySelectorAll(s).forEach(function (el) { el.outerHTML = r.data.fragments[s]; });
          });
          d.close();
          window.acmeOpenAdded(Object.assign({ card: card }, r.data));
        })
        .catch(function () {
          busy = false;
          refresh();
          hint(acmeT("Failed to add to cart."));
        });
    }

    refresh();
    d.showModal();
  }

  document.addEventListener("click", function (e) {
    var a = e.target.closest("[data-quick-add]");
    if (!a) return;
    e.preventDefault(); // href is the product page, for no-JS
    fetch(ajax() + "?action=acme_quick_add_info&id=" + encodeURIComponent(a.dataset.quickAdd))
      .then(function (r) { return r.json(); })
      .then(function (r) {
        if (r.success) open(a.closest(".card"), r.data);
        else location.href = a.href;
      });
  });
})();
