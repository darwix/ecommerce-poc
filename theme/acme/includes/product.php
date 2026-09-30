<?php
// Product card markup, same as the mockup's card().
function theme_product_card($product) {
	$id = $product->get_id();
	$stock = $product->managing_stock() ? $product->get_stock_quantity() : null;
	$link = get_permalink($id);
	echo '<article class="card"><div class="img"><a href="' . esc_url($link) . '">' . $product->get_image('woocommerce_thumbnail') . '</a>';
	if ($stock !== null && $stock <= 5 && $stock > 0) echo '<span class="tag">' . esc_html(acme_t('Only %d left', (int) $stock)) . '</span>';
	printf('<button type="button" class="wish" data-wish-id="%d" aria-label="%s"><svg viewBox="0 0 24 24"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></svg></button>', $id, esc_attr(acme_t('Save %s', $product->get_name())));
	// opens the quick-add dialog (product.js); it also handles sold-out products
	if ($product->is_purchasable() && ($product->is_type('simple') || $product->is_type('variable'))) {
		printf('<a href="%s" data-quick-add="%d" class="add button" rel="nofollow" aria-label="%s">%s</a>',
			esc_url($link), $id, esc_attr(acme_t('Add %s to cart', $product->get_name())), esc_html(acme_t('+ Cart')));
	}
	echo '</div><div class="meta"><h3><a href="' . esc_url($link) . '">' . esc_html($product->get_name()) . '</a></h3>';
	echo '<span class="code">' . esc_html($product->get_sku()) . '</span><span class="price">' . $product->get_price_html() . '</span></div></article>';
}

// Redirect single-product add-to-cart back to same page with ?added-to-cart=ID
// so product.js can show mockup-styled confirmation popup
add_filter('woocommerce_add_to_cart_redirect', function ($url) {
	$id = isset($_REQUEST['add-to-cart']) ? absint($_REQUEST['add-to-cart']) : 0;
	$product = $id ? wc_get_product($id) : null;
	if (!$product instanceof WC_Product) return $url;
	return add_query_arg('added-to-cart', $id, get_permalink($id));
});

// Style "Place order" button like mockup
add_filter('woocommerce_order_button_html', function ($html) {
	return str_replace('class="button', 'class="button btn wide', $html);
});
