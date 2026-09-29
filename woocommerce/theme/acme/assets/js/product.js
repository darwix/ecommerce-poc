// Single product page: pill UI over WC's own variation <select> (WC's wc-add-to-cart-variation.js
// still does the real matching/stock/price logic — this only mirrors clicks onto the selects it reads),
// wishlist heart (localStorage; no WC backend yet), gallery thumbs, and the "added to cart" popup.
(function () {
	var $ = function (s, ctx) { return (ctx || document).querySelector(s); };
	var $$ = function (s, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(s)); };

	// gallery thumbs
	document.addEventListener('click', function (e) {
		var b = e.target.closest('.pd-thumbs button[data-img]');
		if (!b) return;
		var img = $('#pdImg');
		var src = b.querySelector('img');
		if (img && src) img.src = src.src.replace('-100x100', '').replace('thumbnail', 'large');
		$$('.pd-thumbs button').forEach(function (t) { t.classList.toggle('on', t === b); });
	});

	// pill UI for each attribute <select> in the WC variation form
	function buildPills(select) {
		if (select.dataset.pillified) return;
		select.dataset.pillified = '1';
		var wrap = document.createElement('div');
		wrap.className = 'pd-attr-opts';
		$$('option', select).forEach(function (opt) {
			if (!opt.value) return;
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.textContent = opt.textContent;
			btn.dataset.value = opt.value;
			btn.disabled = opt.disabled;
			btn.addEventListener('click', function () {
				select.value = opt.value;
				select.dispatchEvent(new Event('change', { bubbles: true }));
				$$('button', wrap).forEach(function (b) { b.classList.toggle('on', b === btn); });
			});
			wrap.appendChild(btn);
		});
		select.insertAdjacentElement('afterend', wrap);
		// keep pills in sync if WC disables/resets options (e.g. out-of-stock combo)
		new MutationObserver(function () {
			$$('option', select).forEach(function (opt, i) {
				var btn = wrap.children[i - 1]; // option[0] is the placeholder, has no button
				if (btn) btn.disabled = opt.disabled;
			});
		}).observe(select, { attributes: true, subtree: true, attributeFilter: ['disabled'] });
	}
	$$('.variations select[name^="attribute_"]').forEach(buildPills);

	// wishlist heart (client-only for now)
	var wishKey = 'lb-wish';
	var getWish = function () { try { return JSON.parse(localStorage.getItem(wishKey)) || []; } catch (e) { return []; } };
	var setWish = function (v) { try { localStorage.setItem(wishKey, JSON.stringify(v)); } catch (e) {} };
	$$('[data-wish-id]').forEach(function (btn) {
		var id = +btn.dataset.wishId;
		btn.classList.toggle('on', getWish().indexOf(id) > -1);
		btn.addEventListener('click', function () {
			var w = getWish(), i = w.indexOf(id);
			i < 0 ? w.push(id) : w.splice(i, 1);
			setWish(w);
			btn.classList.toggle('on', i < 0);
		});
	});

	// "added to cart" popup — real WC add-to-cart happened server-side (added-to-cart query var,
	// or jQuery's added_to_cart event for the ajax case in product loops/cards elsewhere)
	function openAdded(name, price, image) {
		var d = document.getElementById('added');
		if (!d) {
			d = document.createElement('dialog');
			d.id = 'added';
			document.body.appendChild(d);
		}
		d.innerHTML =
			'<form method="dialog"><div class="dhead"><h2><svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>Masuk keranjang</h2>' +
			'<button class="x" value="close" aria-label="Tutup"><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg></button></div>' +
			'<div class="dprod wide-prod">' + (image ? '<img src="' + image + '" alt="">' : '') +
			'<div><b>' + name + '</b><span class="price">' + price + '</span></div>' +
			'<div class="dacts"><a class="btn-sec" href="' + (window.acmeCartUrl || '/cart/') + '">Lihat keranjang</a>' +
			'<a class="btn" href="' + (window.acmeCheckoutUrl || '/checkout/') + '">Checkout</a></div></div></form>';
		d.addEventListener('click', function (e) { if (e.target === d) d.close(); });
		d.showModal();
	}

	if (window.acmeJustAdded) {
		var info = $('.pd-info');
		if (info) openAdded(info.dataset.productName, info.dataset.productPrice, info.dataset.productImage);
	}

	// ajax add-to-cart elsewhere on the page (cards in related row) still uses WC's own fragment/badge refresh
	if (window.jQuery) {
		jQuery(document.body).on('added_to_cart', function (e, fragments, hash, button) {
			if (!button || !button.closest('.card')) return;
			var name = button.closest('.card') && button.closest('.card').querySelector('h3')?.textContent || '';
			openAdded(name, '', '');
		});
	}
})();
