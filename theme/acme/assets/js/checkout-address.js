// provinsi → kota → kecamatan → kode pos, each level loaded after the one above
// is picked. Ported from mockup-v2/app.js areaCascade().
// ponytail: public APIs (emsifa wilayah + kodepos.vercel.app); RajaOngkir or a
// bundled wilayah table replaces them if this ever needs to work offline.
(function () {
	var WIL = 'https://www.emsifa.com/api-wilayah-indonesia/api/';
	var sels = ['state', 'city', 'kecamatan', 'postcode'].map(function (n) {
		return document.querySelector('select[data-area="' + n + '"]');
	});
	if (sels.some(function (s) { return !s; })) return;

	var ph = sels.map(function (s) { return s.dataset.ph = s.options[0] ? s.options[0].textContent : ''; });
	var txt = function (s) { return s.selectedIndex > 0 ? s.options[s.selectedIndex].textContent : ''; };
	// Selected option's emsifa id; the option value is the readable name so the
	// order stores names, not ids.
	var id = function (s) { return s.selectedIndex > 0 ? s.options[s.selectedIndex].dataset.id || '' : ''; };

	function setOpts(sel, text, opts) {
		sel.innerHTML = '<option value="">' + text + '</option>' + opts.map(function (o) {
			// o = [emsifa id, label, submitted value (defaults to the label)]
			var val = String(o.length > 2 ? o[2] : o[1]).replace(/"/g, '&quot;');
			return '<option value="' + val + '" data-id="' + o[0] + '">' + o[1] + '</option>';
		}).join('');
		sel.disabled = !opts.length;
		// WC re-styles selects with select2 on some setups; keep it in sync.
		if (window.jQuery) jQuery(sel).trigger('change.select2');
	}

	function load(sel, text, url, map) {
		// Picking a level invalidates every level below it.
		sels.slice(sels.indexOf(sel) + 1).forEach(function (s) { setOpts(s, s.dataset.ph, []); });
		sel.innerHTML = '<option value="">Memuat…</option>';
		sel.disabled = true;
		return fetch(url).then(function (r) {
			if (!r.ok) throw new Error(r.status);
			return r.json();
		}).then(function (d) {
			setOpts(sel, text, map(d));
		}).catch(function (e) {
			console.error('[acme] area load failed', url, e);
			setOpts(sel, 'Gagal memuat, pilih ulang', []);
		});
	}

	function cities() {
		return load(sels[1], ph[1], WIL + 'regencies/' + id(sels[0]) + '.json', function (d) {
			return d.map(function (r) { return [r.id, r.name]; });
		});
	}
	function kecs() {
		return load(sels[2], ph[2], WIL + 'districts/' + id(sels[1]) + '.json', function (d) {
			return d.map(function (r) { return [r.id, r.name]; });
		});
	}
	function zips() {
		var kec = txt(sels[2]).toLowerCase();
		var city = txt(sels[1]).toLowerCase().replace(/^(kabupaten|kota) /, '');
		return load(sels[3], ph[3], 'https://kodepos.vercel.app/search/?q=' + encodeURIComponent(txt(sels[2])), function (d) {
			return (d.data || []).filter(function (r) {
				return r.district.toLowerCase() === kec && r.regency.toLowerCase().indexOf(city) !== -1;
			}).sort(function (a, b) { return a.code - b.code; }).map(function (r) {
				// Label shows the village for context; only the code is submitted.
				return [r.code, r.code + ' · ' + r.village, r.code];
			});
		});
	}

	sels[0].addEventListener('change', function () { if (sels[0].value) cities(); });
	sels[1].addEventListener('change', function () { if (sels[1].value) kecs(); });
	sels[2].addEventListener('change', function () { if (sels[2].value) zips(); });

	load(sels[0], ph[0], WIL + 'provinces.json', function (d) {
		return d.map(function (r) { return [r.id, r.name]; });
	}).then(function () {
		// address.js re-applies the saved address once the real option list is in
		document.dispatchEvent(new Event('acme:cascade-ready'));
	});
})();
