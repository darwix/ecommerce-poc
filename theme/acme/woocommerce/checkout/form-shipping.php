<?php
defined('ABSPATH') || exit;
?>
<div class="woocommerce-shipping-fields">
	<h2><?php acme_te('Shipping'); ?></h2>
	<div class="box row-opt">
		<span class="ico">J&amp;T</span>
		<span>
			<b><?php acme_te('COD J&amp;T · shipping paid by the buyer'); ?></b>
			<small><?php acme_te('Shipping is paid to the courier when the package arrives.'); ?></small>
		</span>
	</div>
	<?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>
</div>
