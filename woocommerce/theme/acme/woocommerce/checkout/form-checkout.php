<?php
/**
 * Checkout form – Acme two-column shell (mockup-v2/checkout.html .co-main / .co-side).
 * Same structure as WooCommerce's, with the left and right columns wrapped so the
 * grid has exactly two items instead of relying on per-child grid-column rules.
 *
 * @see woocommerce/templates/checkout/form-checkout.php (9.4.0)
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
	echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__('Checkout', 'woocommerce'); ?>">

	<div class="co-side">
	<div class="co-side-in">
		<?php if ($checkout->get_checkout_fields()) : ?>
			<?php do_action('woocommerce_checkout_before_customer_details'); ?>

			<div class="col2-set" id="customer_details">
				<div class="col-1">
					<?php do_action('woocommerce_checkout_billing'); ?>
				</div>
				<div class="col-2">
					<?php do_action('woocommerce_checkout_shipping'); ?>
				</div>
			</div>

			<?php do_action('woocommerce_checkout_after_customer_details'); ?>
		<?php endif; ?>

		<?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

		<h3 id="order_review_heading">Ringkasan</h3>

		<?php do_action('woocommerce_checkout_before_order_review'); ?>

		<div id="order_review" class="woocommerce-checkout-review-order">
			<?php do_action('woocommerce_checkout_order_review'); ?>
		</div>

		<?php do_action('woocommerce_checkout_after_order_review'); ?>
	</div>
	</div>

	<div class="co-main">
		<h3 class="co-lines-title">Pesanan</h3>

		<?php get_template_part('woocommerce/checkout/order-lines'); ?>
	</div>

</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
