<?php
add_action('after_setup_theme', function () {
	add_theme_support('title-tag');
	add_theme_support('woocommerce');
	add_theme_support('wc-product-gallery-zoom');
	add_theme_support('wc-product-gallery-lightbox');
	add_theme_support('wc-product-gallery-slider');
	add_theme_support('post-thumbnails');
});

add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('acme-fonts', 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap', [], null);
	wp_enqueue_style('acme', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
	if (function_exists('is_product') && is_product()) {
		wp_enqueue_script('acme-product', get_theme_file_uri('assets/js/product.js'), ['jquery', 'wc-add-to-cart-variation'], wp_get_theme()->get('Version'), true);
		wp_add_inline_script('acme-product', 'window.acmeCartUrl=' . wp_json_encode(wc_get_cart_url()) . ';window.acmeCheckoutUrl=' . wp_json_encode(wc_get_checkout_url()) . ';', 'before');
	}
});

// Product grid uses the mockup's .grid instead of <ul class="products">.
add_filter('woocommerce_product_loop_start', fn() => '<div class="grid">');
add_filter('woocommerce_product_loop_end', fn() => '</div>');
add_filter('loop_shop_columns', fn() => 4);
add_filter('body_class', function ($c) {
	if (is_front_page()) $c[] = 'hc';
	return $c;
});

// Cart badge in the header, refreshed after ajax add-to-cart.
function acme_cart_badge() {
	$n = function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return '<span class="badge"' . ($n ? '' : ' hidden') . '>' . (int) $n . '</span>';
}
add_filter('woocommerce_add_to_cart_fragments', function ($f) {
	$f['span.badge'] = acme_cart_badge();
	return $f;
});

// Product card, same markup as the mockup's card().
function acme_card($product) {
	$id = $product->get_id();
	$stock = $product->managing_stock() ? $product->get_stock_quantity() : null;
	$link = get_permalink($id);
	echo '<article class="card"><div class="img"><a href="' . esc_url($link) . '">' . $product->get_image('woocommerce_thumbnail') . '</a>';
	if ($stock !== null && $stock <= 5 && $stock > 0) echo '<span class="tag">Sisa ' . (int) $stock . '</span>';
	if ($product->is_purchasable() && $product->is_in_stock() && $product->is_type('simple')) {
		printf('<a href="%s" data-product_id="%d" data-product_sku="%s" class="add button add_to_cart_button ajax_add_to_cart" rel="nofollow" aria-label="Tambah %s ke keranjang">+ Keranjang</a>',
			esc_url($product->add_to_cart_url()), $id, esc_attr($product->get_sku()), esc_attr($product->get_name()));
	}
	echo '</div><div class="meta"><h3><a href="' . esc_url($link) . '">' . esc_html($product->get_name()) . '</a></h3>';
	echo '<span class="code">' . esc_html($product->get_sku()) . '</span><span class="price">' . $product->get_price_html() . '</span></div></article>';
}

// Non-ajax single-product add-to-cart: redirect back to the same page with ?added-to-cart=ID
// so product.js can show the mockup-styled confirmation popup with real product data.
add_filter('woocommerce_add_to_cart_redirect', function ($url) {
	if (!is_product()) return $url;
	global $product;
	if (!$product instanceof WC_Product) return $url;
	return add_query_arg('added-to-cart', $product->get_id(), get_permalink());
});

// Ongkir is paid to the courier (COD J&T), so no shipping cost is added; the address is still collected as billing.
add_filter('wc_shipping_enabled', '__return_false');

// Style the default WC checkout "Place order" button like the mockup's .btn.
add_filter('woocommerce_order_button_html', function ($html) {
	return str_replace('class="button', 'class="button btn wide', $html);
});

// Shop title.
add_filter('woocommerce_page_title', fn($t) => is_shop() ? 'Semua produk' : $t);

// Price bucket filter (mockup-v2's "Harga" radio: all / <200rb / 200-300rb / >300rb), real query filtering.
function acme_price_buckets() {
	return [
		'' => 'Semua harga',
		'lt200' => 'Di bawah Rp 200rb',
		'200-300' => 'Rp 200rb - 300rb',
		'gt300' => 'Di atas Rp 300rb',
	];
}
add_action('woocommerce_product_query', function ($q) {
	if (is_admin() || empty($_GET['price']) || !array_key_exists($_GET['price'], acme_price_buckets())) return;
	$ranges = ['lt200' => [0, 200000], '200-300' => [200000, 300000], 'gt300' => [300000, PHP_INT_MAX]];
	[$min, $max] = $ranges[$_GET['price']];
	$mq = $q->get('meta_query') ?: [];
	$mq[] = ['key' => '_price', 'value' => [$min, $max], 'type' => 'NUMERIC', 'compare' => 'BETWEEN'];
	$q->set('meta_query', $mq);
});

// Sidebar filter layout: left "aside.sheet" (category + price) + right column (title/tools/grid),
// matching mockup-v2/category.html's .shop grid + mobile bottom-sheet.
function acme_is_shop_archive() {
	return is_shop() || is_product_category() || is_product_tag();
}
// Default WC hooks would print result-count/ordering inline in the loop; we render them
// ourselves in .phead/.tools below (matching mockup-v2's layout), so drop the defaults.
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
add_action('woocommerce_before_main_content', function () {
	if (!acme_is_shop_archive()) return;
	$cur = get_queried_object();
	$cur_cat = $cur instanceof WP_Term ? $cur->term_id : 0;
	$cur_price = $_GET['price'] ?? '';

	ob_start(); woocommerce_result_count(); $count_html = wp_strip_all_tags(ob_get_clean());
	ob_start(); woocommerce_catalog_ordering(); $order_html = ob_get_clean();

	echo '<div class="phead"><div><h1>' . esc_html(woocommerce_page_title(false)) . '</h1><span class="n">' . esc_html($count_html) . '</span></div>';
	echo '<div class="tools"><button id="openFilters" type="button">Filter</button>' . $order_html . '</div></div>';

	$action = $cur instanceof WP_Term ? get_term_link($cur) : wc_get_page_permalink('shop');
	echo '<div class="shop"><aside class="sheet" aria-label="Filter produk"><div class="sheet-head"><span>Filter</span><button id="closeFilters" class="btn-sec" type="button">Lihat hasil</button></div><form id="filters" method="get" action="' . esc_url($action) . '">';

	echo '<fieldset><legend>Kategori</legend>';
	echo '<label><input type="radio" name="acme_cat" value="" ' . checked($cur_cat, 0, false) . ' onchange="location=\'' . esc_url(wc_get_page_permalink('shop')) . '\'">Semua produk</label>';
	foreach (get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => [get_option('default_product_cat')]]) as $t) {
		printf('<label><input type="radio" name="acme_cat" value="%d" %s onchange="location=\'%s\'">%s<em>%d</em></label>',
			$t->term_id, checked($cur_cat, $t->term_id, false), esc_url(get_term_link($t)), esc_html($t->name), $t->count);
	}
	echo '</fieldset>';

	echo '<fieldset><legend>Harga</legend>';
	foreach (acme_price_buckets() as $k => $label) {
		printf('<label><input type="radio" name="price" value="%s" %s onchange="this.form.submit()">%s</label>', esc_attr($k), checked($cur_price, $k, false), esc_html($label));
	}
	echo '</fieldset>';

	echo '</form></aside><div class="shop-col">';
}, 25);
add_action('woocommerce_after_main_content', function () {
	if (!acme_is_shop_archive()) return;
	echo '</div></div>';
}, 5);
add_action('wp_enqueue_scripts', function () {
	if (acme_is_shop_archive()) wp_enqueue_script('acme-shop', get_stylesheet_directory_uri() . '/assets/js/shop.js', [], wp_get_theme()->get('Version'), true);
});

// --- Checkout: mockup-v2/checkout.html parity ---------------------------------

// WC's built-in Indonesia state list is dropped so the four address levels
// (provinsi / kota / kecamatan / kode pos) can all be our own selects, filled by
// assets/js/checkout-address.js. Values are the human-readable names, so the
// order and admin screens stay readable without an id->name lookup.
add_filter('woocommerce_states', function ($states) {
	$states['ID'] = [];
	return $states;
});

add_filter('woocommerce_checkout_fields', function ($fields) {
	$b = &$fields['billing'];

	// Indonesian labels + the mockup's field order.
	$b['billing_first_name']['label'] = 'Nama lengkap';
	$b['billing_first_name']['class'] = ['form-row-first'];
	$b['billing_first_name']['priority'] = 10;
	unset($b['billing_last_name'], $b['billing_company'], $b['billing_country'], $b['billing_address_2']);

	$b['billing_phone']['label'] = 'No. WhatsApp';
	$b['billing_phone']['required'] = true;
	$b['billing_phone']['placeholder'] = '8123456789';
	$b['billing_phone']['priority'] = 20;
	$b['billing_phone']['class'] = ['form-row-last'];

	$b['billing_email']['priority'] = 25;

	// The four cascading selects. Options start empty; the JS loads each level
	// once the level above it is picked, so the markup carries only placeholders.
	$cascade = [
		'billing_state' => [30, 'Provinsi', 'Pilih provinsi', 'address-level1'],
		'billing_city' => [40, 'Kota / kabupaten', 'Pilih provinsi dulu', 'address-level2'],
		'billing_kecamatan' => [50, 'Kecamatan', 'Pilih kota dulu', 'address-level3'],
		'billing_postcode' => [60, 'Kode pos', 'Pilih kecamatan dulu', 'postal-code'],
	];
	foreach ($cascade as $key => [$priority, $label, $placeholder, $autocomplete]) {
		$b[$key] = [
			'type' => 'select',
			'label' => $label,
			'required' => true,
			'options' => ['' => $placeholder],
			'input_class' => ['acme-area'],
			'class' => ['form-row-first'],
			'priority' => $priority,
			'autocomplete' => $autocomplete,
			'custom_attributes' => ['data-area' => str_replace('billing_', '', $key)],
		];
	}

	// The mockup uses a two-row textarea here; addresses wrap past one line.
	$b['billing_address_1']['type'] = 'textarea';
	$b['billing_address_1']['label'] = 'Nama jalan, gedung, no. rumah';
	$b['billing_address_1']['placeholder'] = '';
	$b['billing_address_1']['class'] = ['form-row-wide'];
	$b['billing_address_1']['priority'] = 70;
	$b['billing_address_1']['custom_attributes'] = ['rows' => 2];

	$b['billing_detail'] = [
		'label' => 'Detail lain <small>(blok / unit, patokan)</small>',
		'placeholder' => 'Contoh: pagar hitam, depan masjid',
		'required' => false,
		'class' => ['form-row-wide'],
		'priority' => 80,
	];

	return $fields;
});

// billing_kecamatan / billing_detail are not WC address keys, so persist them
// and show them on the admin order screen ourselves.
add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
	foreach (['billing_kecamatan' => 'Kecamatan', 'billing_detail' => 'Detail lain'] as $key => $label) {
		if (!empty($_POST[$key])) update_post_meta($order_id, '_' . $key, sanitize_text_field(wp_unslash($_POST[$key])));
	}
});
add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
	foreach (['_billing_kecamatan' => 'Kecamatan', '_billing_detail' => 'Detail lain'] as $key => $label) {
		$v = get_post_meta($order->get_id(), $key, true);
		if ($v) printf('<p><strong>%s:</strong> %s</p>', esc_html($label), esc_html($v));
	}
});

// Shipping is disabled (ongkir is paid to the courier), so the mockup's
// "Pengiriman" row is static markup rather than a WC shipping method.
add_action('woocommerce_checkout_after_customer_details', function () {
	echo '<h2>Pengiriman</h2><div class="box row-opt"><span class="ico">J&amp;T</span><span><b>COD J&amp;T · ongkir ditanggung pembeli</b><small>Ongkir dibayar ke kurir saat paket sampai</small></span></div>';
});

add_filter('woocommerce_order_button_text', fn() => 'Pesan sekarang');

// No voucher field on checkout (user's call); coupons stay enabled for admin use.
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);

add_action('wp_enqueue_scripts', function () {
	if (function_exists('is_checkout') && is_checkout() && !is_wc_endpoint_url()) {
		wp_enqueue_script('acme-checkout-address', get_theme_file_uri('assets/js/checkout-address.js'), [], wp_get_theme()->get('Version'), true);
	}
});

// WC has no filter for the checkout column headings, so swap the strings.
add_filter('gettext', function ($translated, $original, $domain) {
	if ($domain !== 'woocommerce' || !is_checkout()) return $translated;
	$map = ['Billing details' => 'Alamat pengiriman', 'Additional information' => 'Pesan untuk pengiriman', 'Your order' => 'Pesanan kamu'];
	return $map[$original] ?? $translated;
}, 10, 3);

// Checkout requires an account: the email comes from the logged-in user, so the
// mockup's address form has no email field. Guests are sent to log in first.
add_action('template_redirect', function () {
	if (!function_exists('is_checkout') || !is_checkout() || is_wc_endpoint_url('order-received')) return;
	if (is_user_logged_in()) return;
	wp_safe_redirect(add_query_arg('redirect_to', urlencode(wc_get_checkout_url()), wc_get_page_permalink('myaccount')));
	exit;
});

add_filter('woocommerce_checkout_fields', function ($fields) {
	unset($fields['billing']['billing_email']);
	return $fields;
}, 20);

// WC still needs a billing email on the order; take the account's.
add_filter('woocommerce_checkout_posted_data', function ($data) {
	if (empty($data['billing_email']) && is_user_logged_in()) {
		$data['billing_email'] = wp_get_current_user()->user_email;
	}
	return $data;
});

// After logging in from the checkout redirect, go back to checkout.
add_filter('woocommerce_login_redirect', function ($redirect, $user) {
	return !empty($_GET['redirect_to']) ? esc_url_raw(wp_unslash($_GET['redirect_to'])) : $redirect;
}, 10, 2);

add_action('woocommerce_review_order_after_submit', function () {
	echo '<p class="terms">Dengan memesan, kamu setuju dengan <a href="' .
		esc_url(get_privacy_policy_url() ?: '#') . '">Syarat &amp; ketentuan</a> kami.</p>';
});

// The checkout already carries a short terms line; WooCommerce's untranslated
// privacy paragraph sits above the button and is not in the mockup.
add_action('init', function () {
	remove_action('woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20);
});

// DOKU is the only way to pay, so there is nothing to choose: hide the method list
// (style.css) and drop every other gateway so DOKU is always the selected one.
// "Pesan sekarang" then goes straight to DOKU's hosted checkout page.
add_filter('woocommerce_available_payment_gateways', function ($gateways) {
	if (!is_checkout()) {
		return $gateways;
	}
	return isset($gateways['doku_checkout']) ? ['doku_checkout' => $gateways['doku_checkout']] : $gateways;
});

// No order note on this checkout.
add_filter('woocommerce_enable_order_notes_field', '__return_false');

// The mockup writes prices as "Rp 159.000"; WooCommerce's default has no gap.
add_filter('woocommerce_price_format', function () {
	return '%1$s&nbsp;%2$s';
});
