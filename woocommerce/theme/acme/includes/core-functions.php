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
	wp_enqueue_style('fonts', 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap', [], null);
	wp_enqueue_style('theme', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));

	if (function_exists('WC')) {
		wp_enqueue_script('wc-add-to-cart');
		$deps = ['jquery'];
		if (is_product()) $deps[] = 'wc-add-to-cart-variation';
		wp_enqueue_script('product', get_theme_file_uri('assets/js/product.js'), $deps, wp_get_theme()->get('Version'), true);
		wp_add_inline_script('product', 'window.acmeCartUrl=' . wp_json_encode(wc_get_cart_url()) . ';window.acmeCheckoutUrl=' . wp_json_encode(wc_get_checkout_url()) . ';', 'before');
	}
});

add_filter('body_class', function ($c) {
	if (is_front_page()) $c[] = 'hc';
	return $c;
});
