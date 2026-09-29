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
		<?php if ($product->is_purchasable() && $product->is_in_stock() && $product->is_type('simple')): ?>
			<a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
				data-product_id="<?php echo (int) $id; ?>"
				data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
				class="add button add_to_cart_button ajax_add_to_cart"
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
