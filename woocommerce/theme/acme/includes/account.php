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

// Wishlist lives in localStorage (lb-wish); the browser posts the ids, we return cards.
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
	wp_add_inline_script('account', 'window.acmeAjax=' . wp_json_encode(admin_url('admin-ajax.php')) . ';', 'before');
});
