<?php
/**
 * Review order – Acme sidebar (mockup-v2/checkout.html .co-side).
 * Replaces WooCommerce's shop_table with the mockup's line list + sums block.
 *
 * @see woocommerce/templates/checkout/review-order.php (5.2.0)
 */

defined('ABSPATH') || exit;

$cart  = WC()->cart;
$items = 0;
?>
<div class="woocommerce-checkout-review-order-table">
<div class="box">
	<?php
	do_action('woocommerce_review_order_before_cart_contents');

	foreach ($cart->get_cart() as $key => $item) {
		$product = apply_filters('woocommerce_cart_item_product', $item['data'], $item, $key);
		if (!$product || !$product->exists() || $item['quantity'] <= 0
			|| !apply_filters('woocommerce_checkout_cart_item_visible', true, $item, $key)) {
			continue;
		}
		$items += $item['quantity'];
		$meta   = wc_get_formatted_cart_item_data($item, true); // "Warna: X, Ukuran: Y"
		$parts  = array_filter([$product->get_sku(), $meta, $item['quantity'] . '×']);
		?>
		<div class="coline">
			<?php echo $product->get_image('woocommerce_thumbnail'); ?>
			<span>
				<b><?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $product->get_name(), $item, $key)); ?></b>
				<small><?php echo esc_html(implode(' · ', $parts)); ?></small>
			</span>
			<b><?php echo wp_kses_post(apply_filters('woocommerce_cart_item_subtotal', $cart->get_product_subtotal($product, $item['quantity']), $item, $key)); ?></b>
		</div>
		<?php
	}

	do_action('woocommerce_review_order_after_cart_contents');
	?>
</div>

<dl class="sums">
	<div>
		<dt><?php printf(esc_html__('Subtotal · %d item', 'acme'), $items); ?></dt>
		<dd><?php wc_cart_totals_subtotal_html(); ?></dd>
	</div>
	<div>
		<dt><?php esc_html_e('Ongkir J&T', 'acme'); ?></dt>
		<dd><?php esc_html_e('Ditanggung pembeli', 'acme'); ?></dd>
	</div>
	<?php foreach ($cart->get_coupons() as $code => $coupon) : ?>
		<div>
			<dt><?php wc_cart_totals_coupon_label($coupon); ?></dt>
			<dd><?php wc_cart_totals_coupon_html($coupon); ?></dd>
		</div>
	<?php endforeach; ?>
	<?php foreach ($cart->get_fees() as $fee) : ?>
		<div>
			<dt><?php echo esc_html($fee->name); ?></dt>
			<dd><?php wc_cart_totals_fee_html($fee); ?></dd>
		</div>
	<?php endforeach; ?>
	<?php do_action('woocommerce_review_order_before_order_total'); ?>
	<div class="tot">
		<dt><?php esc_html_e('Total bayar', 'acme'); ?></dt>
		<dd><?php wc_cart_totals_order_total_html(); ?></dd>
	</div>
	<?php do_action('woocommerce_review_order_after_order_total'); ?>
</dl>
</div>
