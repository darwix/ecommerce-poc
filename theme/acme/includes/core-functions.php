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
		wp_enqueue_script('search', get_theme_file_uri('assets/js/search.js'), [], wp_get_theme()->get('Version'), true);
		wp_add_inline_script('search', 'window.acmeSearch=' . wp_json_encode(['ajax' => admin_url('admin-ajax.php'), 'shop' => wc_get_page_permalink('shop')]) . ';', 'before');
		wp_enqueue_script('wc-add-to-cart');
		$deps = ['jquery'];
		if (is_product()) $deps[] = 'wc-add-to-cart-variation';
		wp_enqueue_script('product', get_theme_file_uri('assets/js/product.js'), $deps, wp_get_theme()->get('Version'), true);
		wp_enqueue_script('quick-add', get_theme_file_uri('assets/js/quick-add.js'), ['product'], wp_get_theme()->get('Version'), true);
		wp_add_inline_script('product', 'window.acmeCartUrl=' . wp_json_encode(wc_get_cart_url()) . ';window.acmeCheckoutUrl=' . wp_json_encode(wc_get_checkout_url()) . ';window.acmeAjax=' . wp_json_encode(admin_url('admin-ajax.php')) . ';window.acmeWishUrl=' . wp_json_encode(wc_get_page_permalink('myaccount') . '#wishlist') . ';', 'before');
	}
});

// The theme does its own layout. woocommerce.css stays on checkout/account: its --wc-form-* variables style those fields.
add_filter('woocommerce_enqueue_styles', function ($styles) {
	unset($styles['woocommerce-layout'], $styles['woocommerce-smallscreen']);
	if (!is_checkout() && !is_account_page()) unset($styles['woocommerce-general']);
	return $styles;
});

// jQuery Migrate is a compat shim for old plugins; nothing here needs it
add_action('wp_default_scripts', function ($scripts) {
	if (!is_admin() && isset($scripts->registered['jquery'])) {
		$scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
	}
});

add_action('wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n" . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1);

add_filter('body_class', function ($c) {
	if (is_front_page()) $c[] = 'hc';
	return $c;
});
