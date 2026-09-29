<?php
/**
 * The cart's line items, shown in the wide column next to the address card.
 * Split out of review-order.php so the totals block (which stays in the sticky
 * sidebar) no longer has to share a box with it.
 *
 * NOT .woocommerce-checkout-review-order-table: WooCommerce's checkout JS
 * replaces every element with that class with the sidebar fragment.
 *
 * @see woocommerce/templates/checkout/review-order.php (5.2.0)
 */

defined('ABSPATH') || exit;
?>
<div class="box">
	<?php
	do_action('woocommerce_review_order_before_cart_contents');

	foreach (WC()->cart->get_cart() as $key => $item) {
		$product = apply_filters('woocommerce_cart_item_product', $item['data'], $item, $key);
		if (!$product || !$product->exists() || $item['quantity'] <= 0
			|| !apply_filters('woocommerce_checkout_cart_item_visible', true, $item, $key)) {
			continue;
		}
		$meta  = wc_get_formatted_cart_item_data($item, true); // "Warna: X, Ukuran: Y"
		$parts = array_filter([$product->get_sku(), $meta, $item['quantity'] . '×']);
		?>
		<div class="coline">
			<?php echo $product->get_image('woocommerce_thumbnail'); ?>
			<span>
				<b><?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $product->get_name(), $item, $key)); ?></b>
				<small><?php echo esc_html(implode(' · ', $parts)); ?></small>
			</span>
			<b><?php echo wp_kses_post(apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($product, $item['quantity']), $item, $key)); ?></b>
		</div>
		<?php
	}

	do_action('woocommerce_review_order_after_cart_contents');
	?>
</div>
