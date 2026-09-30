<?php
defined('ABSPATH') || exit;

global $product;
$product = wc_get_product(get_the_ID());
if (!$product) return;

$id = $product->get_id();
$gallery_ids = array_filter(array_merge([$product->get_image_id()], $product->get_gallery_image_ids()));
$cats = wc_get_product_category_list($id);
$sold = (int) get_post_meta($id, 'total_sales', true);
$low_stock = $product->managing_stock() ? $product->get_stock_quantity() : null;
?>
<section class="pd" aria-live="polite">
	<div class="pd-gal">
		<div class="pd-main">
			<?php echo wp_get_attachment_image(reset($gallery_ids), 'large', false, ['id' => 'pdImg']); ?>
			<?php if ($low_stock !== null && $low_stock <= 5 && $low_stock > 0): ?>
				<span class="tag">Sisa <?php echo (int) $low_stock; ?></span>
			<?php endif; ?>
		</div>
		<?php if (count($gallery_ids) > 1): ?>
		<div class="pd-thumbs">
			<?php foreach (array_values($gallery_ids) as $i => $img_id): ?>
				<button type="button" data-img="<?php echo esc_attr($img_id); ?>" data-full="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'large')); ?>" class="<?php echo $i === 0 ? 'on' : ''; ?>" aria-label="Foto <?php echo $i + 1; ?>">
					<?php echo wp_get_attachment_image($img_id, 'thumbnail'); ?>
				</button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

	<div class="pd-info" data-product-id="<?php echo esc_attr($id); ?>" data-product-name="<?php echo esc_attr($product->get_name()); ?>" data-product-price="<?php echo esc_attr($product->get_price_html()); ?>" data-product-image="<?php echo esc_url(wp_get_attachment_image_url(reset($gallery_ids), 'thumbnail')); ?>">
		<?php echo $cats ? '<span class="eyebrow">' . wp_strip_all_tags($cats) . '</span>' : ''; ?>
		<h1><?php echo esc_html($product->get_name()); ?></h1>
		<div class="pd-meta">
			<?php if ($product->get_sku()): ?><span class="code"><?php echo esc_html($product->get_sku()); ?></span><?php endif; ?>
			<?php if ($sold): ?><span><?php echo $sold; ?> terjual</span><?php endif; ?>
		</div>
		<span class="price pd-price"><?php echo $product->get_price_html(); ?></span>

		<div class="pd-form">
			<?php woocommerce_template_single_add_to_cart(); ?>
		</div>

		<button class="btn-sec pd-wish" id="wishBtn" type="button" data-wish-id="<?php echo esc_attr($id); ?>" aria-label="Simpan <?php echo esc_attr($product->get_name()); ?>">
			<svg viewBox="0 0 24 24"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></svg>Simpan
		</button>

		<ul class="pd-perks">
			<li><svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg><span><b>Ready stock</b> · dikirim 1×24 jam</span></li>
			<li><svg viewBox="0 0 24 24"><path d="M2 7h12v9H2zM14 10h4l3 3v3h-7M6 19a2 2 0 1 0 0-.1M17 19a2 2 0 1 0 0-.1"/></svg><span><b>COD via J&amp;T</b> · ongkir dibayar ke kurir</span></li>
		</ul>

		<div class="qa">
			<details open><summary>Detail produk</summary>
				<?php
				$desc = $product->get_description() ?: $product->get_short_description();
				if ($desc) {
					echo wpautop(wp_kses_post($desc));
				} else {
					// mockup fallback: kode + atribut
					$bits = [];
					if ($product->get_sku()) $bits[] = 'Kode ' . $product->get_sku() . '.';
					foreach ($product->get_attributes() as $attr) {
						$terms = $product->get_attribute($attr->get_name());
						if ($terms) $bits[] = wc_attribute_label($attr->get_name()) . ': ' . $terms . '.';
					}
					$bits[] = 'Foto produk hanya contoh untuk mockup.';
					echo '<p>' . esc_html(implode(' ', $bits)) . '</p>';
				}
				?>
			</details>
			<details><summary>Pengiriman &amp; COD</summary><p>Pesanan dikirim 1×24 jam setelah dikonfirmasi lewat J&amp;T. Bayar di tempat saat paket sampai, ongkir dibayar langsung ke kurir.</p></details>
			<details><summary>Butuh bantuan?</summary><p>Chat admin lewat WhatsApp sebelum pesanan dikirim jika ingin bertanya soal ukuran atau stok.</p></details>
		</div>
	</div>
</section>

<?php
// The popup shows the line as it landed in the cart (variant + qty + line total), so read it back
// from the cart rather than the form, which the redirect has already reset.
if (isset($_GET['added-to-cart']) && (int) $_GET['added-to-cart'] === $id && WC()->cart) {
	$line = null;
	foreach (WC()->cart->get_cart() as $item) {
		if ((int) $item['product_id'] === $id) $line = $item;
	}
	if ($line) {
		$variant = [];
		foreach ($line['variation'] ?? [] as $value) {
			if ($value !== '') $variant[] = $value;
		}
		$variant[] = sprintf('%d item', $line['quantity']);
		?>
		<script>window.acmeJustAdded = <?php echo wp_json_encode([
			'name'  => $product->get_name(),
			'meta'  => implode(' · ', $variant),
			'price' => wc_price($line['line_subtotal']),
			'image' => wp_get_attachment_image_url(reset($gallery_ids), 'thumbnail'),
		]); ?>;</script>
		<?php
	}
}
?>

<section class="pd-more">
	<div class="shead"><h2>Mungkin kamu suka</h2><a class="link" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Lihat semua</a></div>
	<?php
	add_filter('woocommerce_product_related_products_heading', '__return_empty_string');
	woocommerce_output_related_products();
	?>
</section>
