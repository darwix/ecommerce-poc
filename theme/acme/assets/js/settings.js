// /my-account/edit-account/: pane switching, birthday selects, delete dialog.
(function () {
  var nav = document.getElementById("setNav");
  if (!nav) return;
  nav.onclick = function (e) {
    var b = e.target.closest("button[data-p]");
    if (!b) return;
    nav.querySelectorAll("button").forEach(function (x) { x.setAttribute("aria-current", x === b); });
    document.querySelectorAll(".setpanes > section").forEach(function (s) { s.hidden = s.id !== b.dataset.p; });
  };

  var MO = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"].map(acmeT);
  var range = function (n, f) { return Array.from({ length: n }, function (_, i) { return f(i); }); };
  var fill = function (sel, ph, vals, labels, cur) {
    sel.innerHTML = '<option value="">' + ph + "</option>" + vals.map(function (v, i) {
      return '<option value="' + v + '"' + (String(v) === String(cur) ? " selected" : "") + ">" + labels[i] + "</option>";
    }).join("");
  };
  var f = document.querySelector("[data-bday]"), cur = JSON.parse(f.dataset.bday), s = f.querySelectorAll("select");
  var d = range(31, function (i) { return i + 1; }), y = range(70, function (i) { return 2008 - i; });
  fill(s[0], acmeT("Day"), d, d, cur[2]);
  fill(s[1], acmeT("Month"), d.slice(0, 12), MO, cur[1]);
  fill(s[2], acmeT("Year"), y, y, cur[0]);

  var btn = document.getElementById("delBtn");
  if (btn) btn.onclick = function () { document.getElementById("delDlg").showModal(); };
})();
