<?php
/**
 * Address picker + add/edit form. Shared by checkout and the "Alamat" page.
 *
 * On checkout this partial is rendered inside <form class="checkout">, so the
 * field grid stays in the DOM (hidden while the dialog is closed) and WooCommerce
 * still receives the billing fields on submit. The row list is rendered by
 * assets/js/address.js from the book handed over in window.acmeAddr.
 */
defined('ABSPATH') || exit;

$fields = WC()->checkout()->get_checkout_fields('billing');
uasort($fields, function ($a, $b) { // uasort keeps the field keys, which become the input names
	return ($a['priority'] ?? 10) - ($b['priority'] ?? 10);
});
?>
<dialog id="addrDlg" aria-labelledby="addrDlgTitle">
	<div class="dhead">
		<h2 id="addrDlgTitle">Pilih alamat pengiriman</h2>
		<button type="button" class="x" data-close aria-label="Tutup">
			<svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>
	</div>

	<div id="addrPick">
		<div class="addr-list" data-addr-list></div>
		<button type="button" class="btn wide" id="addrAdd">+ Tambah baru</button>
	</div>

	<div id="addrFormPane" hidden>
		<div class="woocommerce-billing-fields">
			<?php foreach ($fields as $key => $field): ?>
				<?php woocommerce_form_field($key, $field, WC()->checkout()->get_value($key)); ?>
			<?php endforeach; ?>
		</div>
		<p class="addr-err" id="addrErr" role="alert" hidden></p>
		<div class="addr-acts">
			<button type="button" class="btn-sec" id="addrCancel">Batal</button>
			<button type="button" class="btn" id="addrSave">Simpan alamat</button>
		</div>
	</div>
</dialog>

<dialog id="addrDelDlg" aria-labelledby="addrDelMsg">
	<p id="addrDelMsg">Hapus alamat ini?</p>
	<div class="addr-acts">
		<button type="button" class="btn-sec" id="addrDelCancel">Batal</button>
		<button type="button" class="btn" id="addrDelOk">Hapus</button>
	</div>
</dialog>
