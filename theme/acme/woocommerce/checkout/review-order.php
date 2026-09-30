<?php
/**
 * Review order – Acme sticky sidebar: the sums block and, via the hooks below
 * it, the payment method and the place-order button. The line items live in
 * checkout/order-lines.php, in the wide column.
 *
 * @see woocommerce/templates/checkout/review-order.php (5.2.0)
 */

defined('ABSPATH') || exit;

$cart = WC()->cart;
$items = $cart->get_cart_contents_count();
?>
<div class="woocommerce-checkout-review-order-table">
<dl class="sums">
	<div>
		<dt><?php printf(esc_html(acme_t('Subtotal · %d items')), $items); ?></dt>
		<dd><?php wc_cart_totals_subtotal_html(); ?></dd>
	</div>
	<div>
		<dt><?php acme_te('J&T shipping'); ?></dt>
		<dd><?php acme_te('Paid by the buyer'); ?></dd>
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
		<dt><?php acme_te('Total'); ?></dt>
		<dd><?php wc_cart_totals_order_total_html(); ?></dd>
	</div>
	<?php do_action('woocommerce_review_order_after_order_total'); ?>
</dl>
</div>
