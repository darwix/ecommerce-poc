<?php
// Grid wrapper (replaces <ul class="products">)
add_filter('woocommerce_product_loop_start', fn() => '<div class="grid">');
add_filter('woocommerce_product_loop_end', fn() => '</div>');
add_filter('loop_shop_columns', fn() => 4);

// Shop page title
add_filter('woocommerce_page_title', fn($t) => is_shop() ? 'Semua produk' : $t);

// Price bucket filter: all / <200rb / 200-300rb / >300rb
function get_price_buckets() {
	return [
		'' => 'Semua harga',
		'lt200' => 'Di bawah Rp 200rb',
		'200-300' => 'Rp 200rb - 300rb',
		'gt300' => 'Di atas Rp 300rb',
	];
}

add_action('woocommerce_product_query', function ($q) {
	if (is_admin() || empty($_GET['price']) || !array_key_exists($_GET['price'], get_price_buckets())) return;
	$ranges = ['lt200' => [0, 200000], '200-300' => [200000, 300000], 'gt300' => [300000, PHP_INT_MAX]];
	[$min, $max] = $ranges[$_GET['price']];
	$mq = $q->get('meta_query') ?: [];
	$mq[] = ['key' => '_price', 'value' => [$min, $max], 'type' => 'NUMERIC', 'compare' => 'BETWEEN'];
	$q->set('meta_query', $mq);
});

function is_shop_archive() {
	return is_shop() || is_product_category() || is_product_tag();
}

// Enqueue shop JS on archive pages
add_action('wp_enqueue_scripts', function () {
	if (is_shop_archive()) wp_enqueue_script('shop', get_stylesheet_directory_uri() . '/assets/js/shop.js', [], wp_get_theme()->get('Version'), true);
});

// Remove default WC hooks; archive-product.php renders them
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
