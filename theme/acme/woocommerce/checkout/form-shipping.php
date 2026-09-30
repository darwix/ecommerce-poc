<?php
defined('ABSPATH') || exit;
?>
<div class="woocommerce-shipping-fields">
	<h2>Pengiriman</h2>
	<div class="box row-opt">
		<span class="ico">J&amp;T</span>
		<span>
			<b>COD J&amp;T · ongkir ditanggung pembeli</b>
			<small>Ongkir dibayar ke kurir saat paket sampai</small>
		</span>
	</div>
	<?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>
</div>
