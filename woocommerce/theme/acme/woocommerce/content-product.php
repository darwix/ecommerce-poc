<?php
defined('ABSPATH') || exit;

global $product;

if (!$product) return;

$id = $product->get_id();
$stock = $product->managing_stock() ? $product->get_stock_quantity() : null;
$link = get_permalink($id);
?>
<article class="card">
	<div class="img">
		<a href="<?php echo esc_url($link); ?>">
			<?php echo $product->get_image('woocommerce_thumbnail'); ?>
		</a>
		<?php if ($stock !== null && $stock <= 5 && $stock > 0): ?>
			<span class="tag">Sisa <?php echo (int) $stock; ?></span>
		<?php endif; ?>
		<button type="button" class="wish" data-wish-id="<?php echo (int) $id; ?>" aria-label="Simpan <?php echo esc_attr($product->get_name()); ?>"><svg viewBox="0 0 24 24"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></svg></button>
			<?php if ($product->is_purchasable() && ($product->is_type('simple') || $product->is_type('variable'))): ?>
			<a href="<?php echo esc_url($link); ?>"
				data-quick-add="<?php echo (int) $id; ?>"
				class="add button"
				rel="nofollow"
				aria-label="<?php echo esc_attr(sprintf(__('Add %s to cart', 'woocommerce'), $product->get_name())); ?>">
				+ Keranjang
			</a>
		<?php endif; ?>
	</div>
	<div class="meta">
		<h3><a href="<?php echo esc_url($link); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
		<span class="code"><?php echo esc_html($product->get_sku()); ?></span>
		<span class="price"><?php echo $product->get_price_html(); ?></span>
	</div>
</article>
