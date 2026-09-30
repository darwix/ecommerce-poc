<?php
// Login / register (woocommerce/myaccount/form-login.php) and checkout gate.

// Account-only store: force the settings the auth flow depends on, so a fresh deploy needs no wp-admin setup.
foreach ([
	'woocommerce_enable_myaccount_registration'    => 'yes',
	'woocommerce_registration_generate_password'   => 'no',
	'woocommerce_enable_guest_checkout'            => 'no',
	'woocommerce_enable_signup_and_login_from_checkout' => 'no',
] as $opt => $val) add_filter("pre_option_$opt", fn() => $val);

// Post-auth destination: same-host ?redirect_to only, else the account page.
function acme_auth_redirect(): string {
	$to = wp_validate_redirect(wp_unslash($_GET['redirect_to'] ?? ''), '');
	return $to ?: wc_get_page_permalink('myaccount');
}

// Checkout needs a login; order-received / order-pay stay reachable for the payment return.
add_action('template_redirect', function () {
	if (is_user_logged_in() || !function_exists('is_checkout') || !is_checkout() || is_wc_endpoint_url()) return;
	wp_safe_redirect(add_query_arg('redirect_to', urlencode(wc_get_checkout_url()), wc_get_page_permalink('myaccount')));
	exit;
});

add_filter('woocommerce_process_registration_errors', function ($err, $user, $pass) {
	if (trim($_POST['acme_name'] ?? '') === '') $err->add('acme_name', 'Nama lengkap wajib diisi.');
	if (strlen($pass) < 6) $err->add('acme_pass', 'Password minimal 6 karakter.');
	return $err;
}, 10, 3);

add_action('woocommerce_created_customer', function ($uid) {
	$name = sanitize_text_field(wp_unslash($_POST['acme_name'] ?? ''));
	update_user_meta($uid, 'first_name', $name);
	wp_update_user(['ID' => $uid, 'display_name' => $name]);
	acme_save_birthday($uid);
});
