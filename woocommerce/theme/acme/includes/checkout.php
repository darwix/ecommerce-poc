<?php
// Disable shipping (ongkir paid to courier)
add_filter('wc_shipping_enabled', '__return_false');

// WC's built-in Indonesia states are dropped so all four address levels
// (provinsi / kota / kecamatan / kode pos) can use custom selects
add_filter('woocommerce_states', function ($states) {
	$states['ID'] = [];
	return $states;
});

// Store is Indonesia-only; pin country since the field is removed
add_filter('woocommerce_checkout_posted_data', function ($data) {
	$data['billing_country'] = 'ID';
	return $data;
});

// Build checkout fields: labels, order, types matching mockup
add_filter('woocommerce_checkout_fields', function ($fields) {
	$b = &$fields['billing'];

	// Indonesian labels + mockup field order
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

	// Four cascading selects; options start empty, JS loads each level
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
			'input_class' => ['area-select'],
			'class' => ['form-row-first'],
			'priority' => $priority,
			'autocomplete' => $autocomplete,
			'custom_attributes' => ['data-area' => str_replace('billing_', '', $key)],
		];
	}

	// Address textarea (wraps past one line)
	$b['billing_address_1']['type'] = 'textarea';
	$b['billing_address_1']['label'] = 'Nama jalan, gedung, no. rumah';
	$b['billing_address_1']['placeholder'] = '';
	$b['billing_address_1']['class'] = ['form-row-wide'];
	$b['billing_address_1']['priority'] = 70;
	$b['billing_address_1']['custom_attributes'] = ['rows' => 2];

	// Custom field: extra address details
	$b['billing_detail'] = [
		'label' => 'Detail lain <small>(blok / unit, patokan)</small>',
		'placeholder' => 'Contoh: pagar hitam, depan masjid',
		'required' => false,
		'class' => ['form-row-wide'],
		'priority' => 80,
	];

	return $fields;
});

// Persist custom address fields to order meta
add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
	foreach (['billing_kecamatan' => 'Kecamatan', 'billing_detail' => 'Detail lain'] as $key => $label) {
		if (!empty($_POST[$key])) update_post_meta($order_id, '_' . $key, sanitize_text_field(wp_unslash($_POST[$key])));
	}
});

// Show custom fields on admin order screen
add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
	foreach (['_billing_kecamatan' => 'Kecamatan', '_billing_detail' => 'Detail lain'] as $key => $label) {
		$v = get_post_meta($order->get_id(), $key, true);
		if ($v) printf('<p><strong>%s:</strong> %s</p>', esc_html($label), esc_html($v));
	}
});

// Shipping display moved to woocommerce/checkout/form-shipping.php

// Text labels
add_filter('woocommerce_order_button_text', fn() => 'Pesan sekarang');
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);

// Translate checkout section titles
add_filter('gettext', function ($translated, $original, $domain) {
	if ($domain !== 'woocommerce' || !is_checkout()) return $translated;
	$map = ['Billing details' => 'Alamat pengiriman', 'Additional information' => 'Pesan untuk pengiriman', 'Your order' => 'Pesanan kamu'];
	return $map[$original] ?? $translated;
}, 10, 3);

// WC prepends the address type to validation notices ("Billing Provinsi is a
// required field"), which reads as noise next to our own field labels.
add_filter('gettext_with_context', function ($translated, $original, $context, $domain) {
	if ($domain === 'woocommerce' && $context === 'checkout-validation' && in_array($original, ['Billing %s', 'Shipping %s'], true)) {
		return '%s';
	}
	return $translated;
}, 10, 4);

// Checkout requires login: email pulled from account, not form
add_action('template_redirect', function () {
	if (!function_exists('is_checkout') || !is_checkout() || is_wc_endpoint_url('order-received')) return;
	if (is_user_logged_in()) return;
	wp_safe_redirect(add_query_arg('redirect_to', urlencode(wc_get_checkout_url()), wc_get_page_permalink('myaccount')));
	exit;
});

// Remove email field from form (pulled from logged-in user)
add_filter('woocommerce_checkout_fields', function ($fields) {
	unset($fields['billing']['billing_email']);
	return $fields;
}, 20);

// Inject email from current user
add_filter('woocommerce_checkout_posted_data', function ($data) {
	if (empty($data['billing_email']) && is_user_logged_in()) {
		$data['billing_email'] = wp_get_current_user()->user_email;
	}
	return $data;
});

// After login, redirect back to checkout
add_filter('woocommerce_login_redirect', function ($redirect, $user) {
	return !empty($_GET['redirect_to']) ? esc_url_raw(wp_unslash($_GET['redirect_to'])) : $redirect;
}, 10, 2);

// Terms line (mockup has one)
add_action('woocommerce_review_order_after_submit', function () {
	echo '<p class="terms">Dengan memesan, kamu setuju dengan <a href="' .
		esc_url(get_privacy_policy_url() ?: '#') . '">Syarat &amp; ketentuan</a> kami.</p>';
});

// Remove WC's privacy policy text
add_action('init', function () {
	remove_action('woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20);
});

// DOKU is the only gateway; hide method list and drop others
add_filter('woocommerce_available_payment_gateways', function ($gateways) {
	if (!is_checkout()) return $gateways;
	return isset($gateways['doku_checkout']) ? ['doku_checkout' => $gateways['doku_checkout']] : $gateways;
});

// No order notes field
add_filter('woocommerce_enable_order_notes_field', '__return_false');

// Price format: "Rp 159.000" with space
add_filter('woocommerce_price_format', function () {
	return '%1$s&nbsp;%2$s';
});
