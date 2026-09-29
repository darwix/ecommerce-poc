<?php
defined('ABSPATH') || exit;

get_header('shop');

function is_shop_archive() {
	return is_shop() || is_product_category() || is_product_tag();
}

if (!is_shop_archive()) {
	woocommerce_content();
	get_footer('shop');
	return;
}

$cur = get_queried_object();
$cur_cat = $cur instanceof WP_Term ? $cur->term_id : 0;
$cur_price = $_GET['price'] ?? '';

ob_start();
woocommerce_result_count();
$count_html = wp_strip_all_tags(ob_get_clean());

ob_start();
woocommerce_catalog_ordering();
$order_html = ob_get_clean();

$action = $cur instanceof WP_Term ? get_term_link($cur) : wc_get_page_permalink('shop');
?>

<div class="phead">
	<div>
		<h1><?php echo esc_html(woocommerce_page_title(false)); ?></h1>
		<span class="n"><?php echo esc_html($count_html); ?></span>
	</div>
	<div class="tools">
		<button id="openFilters" type="button">Filter</button>
		<?php echo $order_html; ?>
	</div>
</div>

<div class="shop">
	<aside class="sheet" aria-label="Filter produk">
		<div class="sheet-head">
			<span>Filter</span>
			<button id="closeFilters" class="btn-sec" type="button">Lihat hasil</button>
		</div>
		<form id="filters" method="get" action="<?php echo esc_url($action); ?>">
			<fieldset>
				<legend>Kategori</legend>
				<label>
					<input type="radio" name="acme_cat" value="" <?php echo checked($cur_cat, 0, false); ?> onchange="location='<?php echo esc_url(wc_get_page_permalink('shop')); ?>'">
					Semua produk
				</label>
				<?php foreach (get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => [get_option('default_product_cat')]]) as $t): ?>
					<label>
						<input type="radio" name="acme_cat" value="<?php echo (int) $t->term_id; ?>" <?php echo checked($cur_cat, $t->term_id, false); ?> onchange="location='<?php echo esc_url(get_term_link($t)); ?>'">
						<?php echo esc_html($t->name); ?><em><?php echo (int) $t->count; ?></em>
					</label>
				<?php endforeach; ?>
			</fieldset>

			<fieldset>
				<legend>Harga</legend>
				<?php foreach (get_price_buckets() as $k => $label): ?>
					<label>
						<input type="radio" name="price" value="<?php echo esc_attr($k); ?>" <?php echo checked($cur_price, $k, false); ?> onchange="this.form.submit()">
						<?php echo esc_html($label); ?>
					</label>
				<?php endforeach; ?>
			</fieldset>
		</form>
	</aside>

	<div class="shop-col">
		<?php
		if (woocommerce_product_loop()) {
			woocommerce_product_loop_start();
			while (have_posts()) {
				the_post();
				wc_get_template_part('content', 'product');
			}
			woocommerce_product_loop_end();
			woocommerce_pagination();
		} else {
			wc_no_products_found();
		}
		?>
	</div>
</div>

<?php
get_footer('shop');
