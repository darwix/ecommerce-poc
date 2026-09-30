<?php
// One-off host setup, no wp-cli. Upload to the WordPress root (next to wp-config.php),
// open /host-setup.php?key=<KEY> while logged in as admin. Deletes itself when done.
const KEY = 'CHANGE-ME';

require __DIR__ . '/wp-load.php';
if (KEY === 'CHANGE-ME' || ($_GET['key'] ?? '') !== KEY || !current_user_can('manage_options')) {
	status_header(403);
	exit('forbidden');
}
header('Content-Type: text/plain; charset=utf-8');

foreach (['about' => 'Tentang', 'faq' => 'FAQ', 'terms-and-conditions' => 'Syarat & Ketentuan', 'privacy-policy' => 'Kebijakan Privasi'] as $slug => $title) {
	// any status: WP ships a draft privacy-policy page, publish it instead of creating privacy-policy-2
	$found = get_posts(['post_type' => 'page', 'name' => $slug, 'post_status' => 'any', 'numberposts' => 1]);
	$args = ['post_title' => $title, 'post_status' => 'publish'];
	$found ? wp_update_post($args + ['ID' => $found[0]->ID]) : wp_insert_post($args + ['post_type' => 'page', 'post_name' => $slug]);
	echo "page $slug ok\n";
}

update_option('woocommerce_calc_shipping', 'no');
update_option('woocommerce_enable_coupons', 'yes');
update_option('blogname', 'Acme Store');

if (!wp_get_nav_menu_object('Utama')) {
	$menu = wp_create_nav_menu('Utama');
	foreach (['Beranda' => '/', 'Semua produk' => '/shop/', 'Tentang' => '/about/'] as $label => $path) {
		wp_update_nav_menu_item($menu, 0, ['menu-item-title' => $label, 'menu-item-url' => home_url($path), 'menu-item-status' => 'publish']);
	}
	set_theme_mod('nav_menu_locations', ['primary' => $menu, 'handheld' => $menu]);
	echo "menu ok\n";
}

if (class_exists('WC_Coupon') && !wc_get_coupon_id_by_code('acme10')) {
	$c = new WC_Coupon();
	$c->set_code('acme10');
	$c->set_discount_type('percent');
	$c->set_amount(10);
	$c->save();
	echo "coupon ok\n";
}

global $wp_rewrite;
$wp_rewrite->set_permalink_structure('/%postname%/');
flush_rewrite_rules();

unlink(__FILE__);
echo "done, script deleted\n";
