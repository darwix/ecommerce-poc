// Header search modal: popular chips + recently viewed. Search / chip click goes to /shop/?acme_q=.
(function () {
  var cfg = window.acmeSearch || {};
  var TERMS = ["dress", "jaket", "tas", "sepatu", "parfum", "kemeja", "hijau", "aksesoris"];
  var read = function (k) {
    try { return JSON.parse(localStorage.getItem(k)) || []; } catch (e) { return []; }
  };

  // remember product pages for "Baru dilihat"
  var pd = document.querySelector(".pd-wish[data-wish-id]");
  if (pd) {
    var id = +pd.dataset.wishId;
    try {
      localStorage.setItem("ac-seen", JSON.stringify([id].concat(read("ac-seen").filter(function (x) { return x !== id; })).slice(0, 8)));
    } catch (e) {}
  }

  var go = function (q) {
    q = q.trim();
    if (q) location.href = cfg.shop + (cfg.shop.indexOf("?") > -1 ? "&" : "?") + "acme_q=" + encodeURIComponent(q);
  };

  var dlg;
  var open = function () {
    if (!dlg) {
      dlg = document.createElement("dialog");
      dlg.className = "sdlg";
      dlg.setAttribute("aria-label", acmeT("Search products"));
      dlg.innerHTML =
        '<form method="dialog"><div class="sbox"><svg class="i" viewBox="0 0 24 24"><path d="M11 4a7 7 0 1 0 0 14 7 7 0 0 0 0-14zm9 16-4-4"/></svg>' +
        '<input id="sQ" type="search" placeholder="' + acmeT('Search products') + '" aria-label="' + acmeT('Search products') + '" autocomplete="off">' +
        '<button class="x" value="close" aria-label="' + acmeT('Close') + '"><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg></button></div>' +
        '<div class="sres"><p class="lbl">' + acmeT('Popular searches') + '</p><div class="chips">' +
        TERMS.map(function (t) { return '<button type="button" data-term="' + t + '">' + t + "</button>"; }).join("") +
        '</div><div id="sSeen" hidden><p class="lbl">' + acmeT('Recently viewed') + '</p><div class="grid sgrid"></div></div></div></form>';
      document.body.appendChild(dlg);
      var q = dlg.querySelector("#sQ");
      q.onkeydown = function (e) {
        if (e.key === "Enter") { e.preventDefault(); go(q.value); }
      };
      dlg.onclick = function (e) {
        if (e.target === dlg) return dlg.close();
        var t = e.target.closest("[data-term]");
        if (t) go(t.dataset.term);
      };
    }
    dlg.showModal();
    dlg.querySelector("#sQ").focus();

    var ids = read("ac-seen").slice(0, 4);
    if (!ids.length) return;
    fetch(cfg.ajax + "?action=acme_wish_cards&ids=" + ids.join(","))
      .then(function (r) { return r.text(); })
      .then(function (h) {
        if (!h) return;
        var box = dlg.querySelector("#sSeen");
        box.querySelector(".grid").innerHTML = h;
        var wish = read("ac-wish");
        box.querySelectorAll(".wish").forEach(function (b) { b.classList.toggle("on", wish.indexOf(+b.dataset.wishId) > -1); });
        box.hidden = false;
      });
  };

  document.addEventListener("click", function (e) {
    if (e.target.closest("[data-search]")) open();
  });
})();
