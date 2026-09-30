<?php
// /my-account/ dashboard (page-my-account.php): order status labels, wishlist cards, script.

// WooCommerce status => [label, badge class] (mockup-v2 account.html)
function acme_order_status(string $status): array {
	$map = [
		'pending'    => ['Menunggu pembayaran', 'wait'],
		'on-hold'    => ['Menunggu pembayaran', 'wait'],
		'processing' => ['Diproses', 'wait'],
		'completed'  => ['Selesai', 'ok'],
	];
	return $map[$status] ?? ['Dibatalkan', 'bad']; // cancelled, failed, refunded
}

// Wishlist lives in localStorage (ac-wish); the browser posts the ids, we return cards.
add_action('wp_ajax_acme_wish_cards', 'acme_wish_cards');
add_action('wp_ajax_nopriv_acme_wish_cards', 'acme_wish_cards');
function acme_wish_cards() {
	$ids = array_filter(array_map('absint', explode(',', $_GET['ids'] ?? '')));
	ob_start();
	foreach (array_slice($ids, 0, 48) as $id) {
		$p = wc_get_product($id);
		if ($p && $p->is_visible()) theme_product_card($p);
	}
	wp_die(ob_get_clean());
}

add_action('wp_enqueue_scripts', function () {
	if (!function_exists('is_account_page') || !is_account_page()) return;
	wp_enqueue_script('account', get_theme_file_uri('assets/js/account.js'), [], wp_get_theme()->get('Version'), true);
	wp_enqueue_script('settings', get_theme_file_uri('assets/js/settings.js'), [], wp_get_theme()->get('Version'), true);
	wp_add_inline_script('account', 'window.acmeAjax=' . wp_json_encode(admin_url('admin-ajax.php')) . ';', 'before');
});

// /my-account/edit-account/: single "Nama" field, no last name / display name inputs.
add_filter('woocommerce_save_account_details_required_fields', function () {
	return ['account_first_name' => 'Nama', 'account_email' => 'Email'];
});

function acme_save_birthday($uid) {
	$d = absint($_POST['acme_bd'] ?? 0);
	$m = absint($_POST['acme_bm'] ?? 0);
	$y = absint($_POST['acme_by'] ?? 0);
	if ($d && $m && $y && checkdate($m, $d, $y)) update_user_meta($uid, 'acme_birthday', "$y-$m-$d");
	else delete_user_meta($uid, 'acme_birthday');
}
add_action('woocommerce_save_account_details', 'acme_save_birthday');

// Delete own account (not admins); orders stay with the shop.
add_action('admin_post_acme_delete_account', function () {
	check_admin_referer('acme_delete_account');
	$u = wp_get_current_user();
	if (!$u->ID || user_can($u, 'manage_options')) wp_die('Tidak diizinkan.', '', 403);
	require_once ABSPATH . 'wp-admin/includes/user.php';
	wp_logout();
	wp_delete_user($u->ID);
	wp_safe_redirect(home_url('/'));
	exit;
});
