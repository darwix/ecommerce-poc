<?php
function get_cart_badge() {
	$n = function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return '<span class="badge"' . ($n ? '' : ' hidden') . '>' . (int) $n . '</span>';
}

function get_cartbar() {
	$cart = function_exists('WC') ? WC()->cart : null;
	$n = $cart ? $cart->get_cart_contents_count() : 0;
	$hide = !$n || is_cart() || is_checkout();
	return '<a id="cartbar" href="' . esc_url(wc_get_cart_url()) . '"' . ($hide ? ' hidden' : '') . '>'
		. '<span><b>' . acme_t('%d items in cart', $n) . '</b>'
		. '<small>' . ($cart ? wp_kses_post($cart->get_cart_subtotal()) : '') . '</small></span>'
		. '<i><svg viewBox="0 0 24 24"><path d="M5 7h14l-1.5 13h-11zM9 7a3 3 0 0 1 6 0"/></svg><em>' . (int) $n . '</em></i></a>';
}

add_filter('woocommerce_add_to_cart_fragments', function ($f) {
	$f['span.badge'] = get_cart_badge();
	$f['a#cartbar'] = get_cartbar();
	return $f;
});
