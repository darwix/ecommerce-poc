// /my-account/ dashboard: tabs, order search + status filter, wishlist cards.
(function () {
  var $ = function (s) { return document.querySelector(s); };
  var $$ = function (s) { return document.querySelectorAll(s); };
  if (!$("#orders")) return;

  var filter = function () {
    var q = $("#q").value.trim().toLowerCase(), st = $("#status").value, n = 0;
    $$("#orders .order").forEach(function (o) {
      var show = (!st || o.dataset.status === st) && (!q || o.dataset.q.indexOf(q) > -1);
      o.hidden = !show;
      n += show;
    });
    $("#orders .none").hidden = n > 0 || !$$("#orders .order").length;
  };
  $("#q").oninput = $("#status").onchange = filter;

  var none = '<p class="none">Belum ada produk di wishlist. Tekan ♡ di produk untuk menyimpannya.</p>';
  var renderWish = function () {
    var ids = [];
    try { ids = JSON.parse(localStorage.getItem("lb-wish")) || []; } catch (e) {}
    if (!ids.length) { $("#wishGrid").innerHTML = none; return; }
    fetch(window.acmeAjax + "?action=acme_wish_cards&ids=" + ids.join(","))
      .then(function (r) { return r.text(); })
      .then(function (h) {
        $("#wishGrid").innerHTML = h || none;
        $$("#wishGrid .wish").forEach(function (b) { b.classList.add("on"); });
      });
  };
  // un-heart in the wishlist tab: drop the card (product.js updates localStorage)
  $("#wishGrid").addEventListener("click", function (e) {
    var b = e.target.closest(".wish");
    if (!b) return;
    b.closest(".card").remove();
    if (!$("#wishGrid .card")) $("#wishGrid").innerHTML = none;
  });
  $$("[role=tab]").forEach(function (t) {
    t.onclick = function () {
      $$("[role=tab]").forEach(function (x) { x.setAttribute("aria-selected", x === t); });
      $$("[role=tabpanel]").forEach(function (p) { p.hidden = p.id !== t.getAttribute("aria-controls"); });
      if (t.id === "tWish") renderWish();
    };
  });
  if (location.hash === "#wishlist") $("#tWish").click();
})();
