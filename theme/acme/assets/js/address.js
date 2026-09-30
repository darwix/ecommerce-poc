// Address book for checkout and the "Alamat" page.
// Server state arrives in window.acmeAddr (see includes/address.php); the row
// list is rendered from it, so save/delete only need to re-render.
// ponytail: innerHTML re-render of the whole list on every change - the list is a
// handful of rows, a diffing layer would cost more than it saves.
(function () {
	var cfg = window.acmeAddr;
	if (!cfg) return;

	var lists = document.querySelectorAll('[data-addr-list]');
	var dlg = document.getElementById('addrDlg');
	var pick = document.getElementById('addrPick');
	var pane = document.getElementById('addrFormPane');
	var errBox = document.getElementById('addrErr');
	var card = document.getElementById('addrCard');
	var delDlg = document.getElementById('addrDelDlg');
	var state = { list: cfg.list || [], id: cfg.default || '', pending: '' };

	function esc(s) {
		return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c];
		});
	}
	function line(a) {
		return [a.city, a.kecamatan, a.address_1].filter(Boolean).join(', ');
	}
	function find(id) {
		return state.list.filter(function (a) { return a.id === id; })[0];
	}
	function field(key) {
		return dlg.querySelector('[name="' + cfg.fields[key] + '"]');
	}

	function render() {
		var rows = state.list.map(function (a) {
			return '<div class="addr-row' + (a.id === state.id ? ' on' : '') + '" data-id="' + esc(a.id) + '">' +
				'<button type="button" class="addr-pick" data-pick="' + esc(a.id) + '">' +
					'<b>' + esc(a.name) + '</b>' +
					'<span>+62' + esc(a.phone) + '</span>' +
					'<small>' + esc(line(a)) + '</small>' +
				'</button>' +
				'<svg class="tick" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>' +
				'<div class="addr-rowacts">' +
					'<button type="button" data-del="' + esc(a.id) + '">' + acmeT('Remove') + '</button>' +
					'<button type="button" data-edit="' + esc(a.id) + '">' + acmeT('Edit') + '</button>' +
				'</div>' +
			'</div>';
		}).join('') || '<p class="addr-empty">' + acmeT('No saved addresses yet.') + '</p>';
		if (cfg.manageUrl && card) {
			rows += '<a class="linkbtn" href="' + esc(cfg.manageUrl) + '">' + acmeT('Manage saved addresses') + '</a>';
		}
		Array.prototype.forEach.call(lists, function (el) { el.innerHTML = rows; });

		var view = document.getElementById('addrView');
		var btn = document.getElementById('addrChange');
		if (!view || !btn) return;
		var a = find(state.id);
		view.innerHTML = a
			? '<b>' + esc(a.name) + '</b><span>+62' + esc(a.phone) + '</span><span>' + esc(line(a)) + '</span>'
			: '<b>' + acmeT('No address yet') + '</b><span>' + acmeT('Add a shipping address first.') + '</span>';
		btn.textContent = a ? acmeT('Edit') : acmeT('Add address');
	}

	// The saved value is a readable name, so the option may not exist until the
	// level above it has loaded. Add it; a later load replaces the list and the
	// refill on 'acme:cascade-ready' puts it back.
	function setSel(sel, val) {
		if (!sel || !val) return;
		var hit = Array.prototype.some.call(sel.options, function (o) { return o.value === val; });
		if (!hit) {
			var o = document.createElement('option');
			o.value = val;
			o.textContent = val;
			sel.insertBefore(o, sel.options[1] || null);
		}
		sel.value = val;
		sel.disabled = false; // a disabled select is not submitted
	}

	function fill(rec) {
		if (!rec) return;
		Object.keys(cfg.fields).forEach(function (key) {
			var el = field(key);
			if (!el) return;
			if (el.tagName === 'SELECT') setSel(el, rec[key]);
			else el.value = rec[key] || '';
		});
	}

	function collect() {
		var rec = {};
		Object.keys(cfg.fields).forEach(function (key) {
			var el = field(key);
			rec[key] = el ? el.value : '';
		});
		return rec;
	}

	function post(op, data) {
		var body = new FormData();
		body.append('action', 'acme_address');
		body.append('nonce', cfg.nonce);
		body.append('op', op);
		Object.keys(data || {}).forEach(function (k) { body.append(k, data[k]); });
		return fetch(cfg.ajax, { method: 'POST', body: body, credentials: 'same-origin' })
			.then(function (r) { return r.json(); });
	}

	function apply(res) {
		state.list = res.data.list;
		state.id = res.data.default || '';
		render();
	}

	// Field errors go under their own input; anything else falls back to the pane.
	function clearErr() {
		errBox.hidden = true;
		Array.prototype.forEach.call(dlg.querySelectorAll('.addr-fielderr'), function (n) { n.remove(); });
		Array.prototype.forEach.call(dlg.querySelectorAll('.row-err'), function (n) { n.classList.remove('row-err'); });
	}

	function fail(res) {
		var d = (res && res.data) || {};
		var fields = d.fields;
		if (!fields && d.field) {
			fields = {};
			fields[d.field] = d.message || acmeT('Required.');
		}
		clearErr();
		if (fields) {
			var first = null;
			Object.keys(fields).forEach(function (key) {
				var el = field(key);
				if (!el) return;
				var p = document.createElement('p');
				p.className = 'addr-fielderr';
				p.setAttribute('role', 'alert');
				p.textContent = fields[key];
				var row = el.closest('.form-row') || el.parentNode;
				row.appendChild(p);
				row.classList.add('row-err');
				if (!first) first = el;
			});
			if (first) first.focus();
			return;
		}
		errBox.textContent = d.message || acmeT('Failed to save address.');
		errBox.hidden = false;
	}

	function open(paneVisible, rec) {
		pane.dataset.id = rec ? rec.id : ''; // what the form pane is editing
		clearErr();
		pick.hidden = paneVisible;
		pane.hidden = !paneVisible;
		if (paneVisible) {
			Object.keys(cfg.fields).forEach(function (key) {
				var el = field(key);
				if (el && el.tagName !== 'SELECT') el.value = rec ? rec[key] || '' : '';
			});
			if (rec) fill(rec);
			document.getElementById('addrDlgTitle').textContent = rec ? acmeT('Edit address') : acmeT('New address');
			var first = field('name');
			if (first) first.focus();
		} else {
			document.getElementById('addrDlgTitle').textContent = acmeT('Select a shipping address');
		}
		if (!dlg.open) dlg.showModal();
	}

	// Pick on checkout fills the order fields; on the "Alamat" page it only marks
	// the default, since there is no order form to fill.
	document.addEventListener('click', function (e) {
		var pickBtn = e.target.closest('[data-pick]');
		var delBtn = e.target.closest('[data-del]');
		var editBtn = e.target.closest('[data-edit]');

		if (pickBtn) {
			post('default', { id: pickBtn.dataset.pick }).then(function (res) {
				if (!res.success) return fail(res);
				apply(res);
				fill(find(res.data.default));
				dlg.close();
			});
		} else if (editBtn) {
			open(true, find(editBtn.dataset.edit));
		} else if (delBtn) {
			state.pending = delBtn.dataset.del;
			delDlg.showModal();
		}
	});

	if (card) {
		// Straight to the form when there is nothing to pick yet.
		document.getElementById('addrChange').addEventListener('click', function () {
			open(!find(state.id), null);
		});
	}
	document.getElementById('addrDelCancel').addEventListener('click', function () { delDlg.close(); });
	document.getElementById('addrDelOk').addEventListener('click', function () {
		var id = state.pending;
		state.pending = '';
		delDlg.close();
		if (!id) return;
		post('delete', { id: id }).then(function (res) {
			if (!res.success) return fail(res);
			apply(res);
			// The deleted row may have been the selected one; the server falls
			// back to another, so the order fields have to follow.
			fill(find(res.data.default));
		});
	});
	document.getElementById('addrAdd').addEventListener('click', function () { open(true, null); });
	document.getElementById('addrCancel').addEventListener('click', function () { open(false); });
	dlg.addEventListener('click', function (e) {
		if (e.target.closest('[data-close]')) dlg.close();
	});
	document.getElementById('addrSave').addEventListener('click', function () {
		var rec = collect();
		var id = pane.dataset.id || '';
		post('save', Object.assign({ id: id }, rec)).then(function (res) {
			if (!res.success) return fail(res);
			apply(res);
			fill(find(res.data.id));
			dlg.close();
		});
	});
	// the message is stale the moment the field is touched
	dlg.addEventListener('input', clearErr);

	// WC's validation list links each message at its field, which now lives inside
	// the dialog - open it instead of doing nothing.
	document.addEventListener('click', function (e) {
		var a = e.target.closest('.woocommerce-error a[href^="#billing_"]');
		if (!a) return;
		e.preventDefault();
		open(true, find(state.id) || null);
		var el = document.getElementById(a.getAttribute('href').slice(1));
		if (el) el.focus();
	});

	render();
	fill(find(state.id)); // check the order posts the saved address even untouched
	document.addEventListener('acme:cascade-ready', function () { fill(find(state.id)); });
})();
