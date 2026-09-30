<?php get_header(); ?>
<main id="content" class="cart">
	<h1><?php acme_te('Cart'); ?> <span style="color:var(--muted);font-weight:400">(<?php echo (int) WC()->cart->get_cart_contents_count(); ?>)</span></h1>

	<?php if (WC()->cart->is_empty()): ?>
		<div class="empty"><b><?php acme_te('Your cart is still empty.'); ?></b><p><?php acme_te("Check out this week's new arrivals."); ?></p><a class="btn" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php acme_te('Start shopping'); ?></a></div>
	<?php else: ?>
		<form class="lines" method="post" action="<?php echo esc_url(wc_get_cart_url()); ?>">
		<?php foreach (WC()->cart->get_cart() as $key => $item):
			$product = $item['data'];
			if (!$product || !$product->exists() || $item['quantity'] <= 0) continue;
			$stock = $product->managing_stock() ? $product->get_stock_quantity() : null;
			$max = $product->get_max_purchase_quantity();
		?>
			<div class="line">
				<img src="<?php echo esc_url(wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src()); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
				<div class="info">
					<b><a href="<?php echo esc_url($product->get_permalink()); ?>" style="color:inherit;text-decoration:none"><?php echo esc_html($product->get_name()); ?></a></b>
					<span class="var"><?php echo esc_html($product->get_sku()); ?><?php echo wc_get_formatted_cart_item_data($item) ? ' · ' . wp_strip_all_tags(wc_get_formatted_cart_item_data($item)) : ''; ?></span>
					<b><?php echo wp_kses_post(WC()->cart->get_product_subtotal($product, 1)); ?></b>
					<?php if ($stock !== null && $stock <= 5 && $stock > 0): ?><span class="left"><?php acme_te('Only %d left — checkout soon', (int) $stock); ?></span><?php endif; ?>
					<div class="acts">
						<a class="linkbtn" href="<?php echo esc_url(wc_get_cart_remove_url($key)); ?>"><?php acme_te('Remove'); ?></a>
						<div class="qty">
							<button type="button" data-dq="-1" aria-label="<?php echo esc_attr(acme_t('Decrease')); ?>" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>&minus;</button>
							<span><input type="number" name="cart[<?php echo esc_attr($key); ?>][qty]" value="<?php echo (int) $item['quantity']; ?>" min="1" <?php echo $max > 0 ? 'max="' . (int) $max . '"' : ''; ?> style="width:32px;border:0;text-align:center;font:inherit;background:transparent"></span>
							<button type="button" data-dq="1" aria-label="<?php echo esc_attr(acme_t('Increase')); ?>" <?php echo ($max > 0 && $item['quantity'] >= $max) ? 'disabled title="' . esc_attr(acme_t('Out of stock')) . '"' : ''; ?>>+</button>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		<button type="submit" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>" hidden></button>
		<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
		</form>
	<?php endif; ?>

	<div class="note"><svg viewBox="0 0 24 24"><path d="M12 8v5m0 3h.01M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18z"/></svg><span><?php acme_te('COD payment via J&T. Packages refused over unpaid COD shipping cannot be returned or refunded.'); ?></span></div>

	<h2><?php acme_te('Payment methods'); ?></h2>
	<?php $logos = ['bri' => 'BRI', 'bni' => 'BNI', 'mandiri' => 'MANDIRI', 'bsi' => 'BSI', 'permata' => 'PERMATA', 'cimb-niaga' => 'CIMB NIAGA', 'ovo' => 'OVO', 'akulaku' => 'AKULAKU', 'alfamart' => 'ALFAMART', 'indomaret' => 'INDOMARET', 'visa' => 'VISA', 'mastercard' => 'MASTERCARD', 'jcb' => 'JCB']; ?>
	<div class="pay"><?php foreach ($logos as $f => $alt) printf('<img src="%s" alt="%s" loading="lazy">', esc_url(get_theme_file_uri("assets/logos/$f.svg")), esc_attr($alt)); ?></div>

	<?php
	$cross_ids = array_diff(WC()->cart->get_cross_sells(), array_keys(WC()->cart->get_cart()));
	if (!$cross_ids) {
		$cross = wc_get_products(['limit' => 8, 'orderby' => 'popularity', 'status' => 'publish', 'exclude' => array_map(fn($i) => $i['product_id'], WC()->cart->get_cart())]);
	} else {
		$cross = array_map('wc_get_product', array_slice($cross_ids, 0, 8));
	}
	if ($cross):
	?>
		<h2><?php acme_te('Frequently bought together'); ?></h2>
		<div class="row"><?php foreach ($cross as $p) if ($p) theme_product_card($p); ?></div>
	<?php endif; ?>
</main>

<div class="totalbar"<?php echo WC()->cart->is_empty() ? ' hidden' : ''; ?>><div class="inner">
	<div class="sum"><small><?php acme_te('Total (%d items)', (int) WC()->cart->get_cart_contents_count()); ?></small><b><?php echo wp_kses_post(WC()->cart->get_cart_total()); ?></b></div>
	<a class="btn" href="<?php echo esc_url(wc_get_checkout_url()); ?>">Checkout</a>
</div></div>

<script>
document.querySelectorAll('.qty [data-dq]').forEach(function (btn) {
	btn.addEventListener('click', function () {
		var input = btn.parentElement.querySelector('input[type=number]');
		var v = Math.max(1, (+input.value || 1) + (+btn.dataset.dq));
		if (input.max) v = Math.min(v, +input.max);
		input.value = v;
		btn.closest('form').querySelector('[name=update_cart]').click();
	});
});
</script>

<?php get_footer();
