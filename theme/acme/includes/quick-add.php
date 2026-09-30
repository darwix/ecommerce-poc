<?php
// "+ Keranjang" on a product card opens the quick-add dialog (assets/js/product.js, #qadd).
// acme_quick_add_info returns what the dialog needs; acme_quick_add puts the choice in the cart.

// How many more units can go in the cart (0 = sold out or all already in the cart).
function acme_units_left($p) {
	if (!$p->is_in_stock()) return 0;
	if (!$p->managing_stock() || $p->backorders_allowed()) return 99;
	$in_cart = WC()->cart->get_cart_item_quantities()[$p->get_stock_managed_by_id()] ?? 0;
	return max(0, (int) $p->get_stock_quantity() - $in_cart);
}

function acme_quick_add_product() {
	$p = wc_get_product(absint($_REQUEST['id'] ?? 0));
	if (!$p || !$p->is_purchasable() || !($p->is_type('simple') || $p->is_type('variable'))) wp_send_json_error();
	return $p;
}

add_action('wp_ajax_acme_quick_add_info', 'acme_quick_add_info');
add_action('wp_ajax_nopriv_acme_quick_add_info', 'acme_quick_add_info');
function acme_quick_add_info() {
	$p = acme_quick_add_product();
	$out = [
		'id'    => $p->get_id(),
		'name'  => $p->get_name(),
		'sku'   => $p->get_sku(),
		'price' => $p->get_price_html(),
		'image' => wp_get_attachment_image_url($p->get_image_id(), 'thumbnail'),
		'left'  => 0,
		'attrs' => [],
		'vars'  => [],
	];
	if ($p->is_type('variable')) {
		foreach ($p->get_variation_attributes() as $name => $opts) {
			$out['attrs'][] = ['key' => 'attribute_' . sanitize_title($name), 'label' => wc_attribute_label($name, $p), 'options' => array_values($opts)];
		}
		foreach ($p->get_children() as $vid) {
			$v = wc_get_product($vid);
			if ($v && $v->variation_is_visible()) $out['vars'][] = ['id' => $vid, 'attrs' => $v->get_variation_attributes(), 'left' => acme_units_left($v)];
		}
	} else {
		$out['left'] = acme_units_left($p);
	}
	wp_send_json_success($out);
}

add_action('wp_ajax_acme_quick_add', 'acme_quick_add');
add_action('wp_ajax_nopriv_acme_quick_add', 'acme_quick_add');
function acme_quick_add() {
	$p = acme_quick_add_product();
	$qty = max(1, absint($_POST['qty'] ?? 1));
	$vid = absint($_POST['variation_id'] ?? 0);
	$attrs = array_map('wc_clean', (array) wp_unslash($_POST['attrs'] ?? []));
	wc_clear_notices();
	if (!WC()->cart->add_to_cart($p->get_id(), $qty, $vid, $vid ? $attrs : [])) {
		$msg = html_entity_decode(wp_strip_all_tags(implode(' ', array_column(wc_get_notices('error'), 'notice'))));
		wc_clear_notices();
		wp_send_json_error($msg ?: 'Gagal menambah ke keranjang.');
	}
	wc_clear_notices(); // WC's "added" notice would otherwise show on the next page
	$line = $vid ? wc_get_product($vid) : $p;
	wp_send_json_success([
		'name'      => $p->get_name(),
		'meta'      => implode(' · ', array_filter(array_merge(array_values($attrs), [$qty . ' item']))),
		'price'     => wc_price((float) $line->get_price() * $qty),
		'image'     => wp_get_attachment_image_url($p->get_image_id(), 'thumbnail'),
		'fragments' => apply_filters('woocommerce_add_to_cart_fragments', []),
	]);
}
