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

// Ongkir is paid to the courier (COD J&T), so no shipping cost is added; the address is still collected as billing.
add_filter('wc_shipping_enabled', '__return_false');

// Shop title + category chips above the grid.
add_filter('woocommerce_page_title', fn($t) => is_shop() ? 'Semua produk' : $t);
add_action('woocommerce_before_shop_loop', function () {
	$cur = get_queried_object();
	$cur_id = $cur instanceof WP_Term ? $cur->term_id : 0;
	echo '<nav class="chips" aria-label="Kategori"><a href="' . esc_url(wc_get_page_permalink('shop')) . '"' . (!$cur_id ? ' class="on"' : '') . '>Semua</a>';
	foreach (get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => [get_option('default_product_cat')]]) as $t) {
		echo '<a href="' . esc_url(get_term_link($t)) . '"' . ($t->term_id === $cur_id ? ' class="on"' : '') . '>' . esc_html($t->name) . '</a>';
	}
	echo '</nav>';
}, 5);
