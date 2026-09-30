<?php
defined('ABSPATH') || exit;

get_header('shop');

if (!is_shop_archive()) {
	woocommerce_content();
	get_footer('shop');
	return;
}

$cur = get_queried_object();
$cur_cat = $cur instanceof WP_Term ? $cur->term_id : (int) ($_GET['acme_cat'] ?? 0);
$cur_price = $_GET['price'] ?? '';
$cur_q = $_GET['acme_q'] ?? '';
$cur_sort = $_GET['orderby'] ?? '';
$shop_url = wc_get_page_permalink('shop');

// category lives in the query string too, so the URL never leaves /shop/
$title = $cur_cat ? get_term_field('name', $cur_cat) : acme_t('All products');

$count_html = acme_t('%d products', $GLOBALS['wp_query']->found_posts);

// mockup-v2 sort options (app.js SORTS) mapped to WooCommerce orderby values
$sorts = [
	'popularity' => acme_t('Most popular'),
	'date' => acme_t('Newest'),
	'price' => acme_t('Lowest price'),
	'price-desc' => acme_t('Highest price'),
];

// active-filter chips, each removable. URLs are rebuilt from $_GET (not
// remove_query_arg) so they stay on /shop/ even when landing on a term archive.
$keep = fn(array $drop) => add_query_arg(array_diff_key($_GET, array_flip($drop)), $shop_url);
$chips = [];
if ($cur_q !== '') $chips[] = [acme_t('Search: %s', $cur_q), $keep(['acme_q'])];
if ($cur_cat) $chips[] = [$title, $keep(['acme_cat'])];
if ($cur_price !== '' && array_key_exists($cur_price, get_price_buckets())) $chips[] = [get_price_buckets()[$cur_price], $keep(['price'])];
?>
<main id="content" class="wrap woo">
<form class="shop-form" method="get" action="<?php echo esc_url($shop_url); ?>">
	<div class="phead">
		<div>
			<h1><?php echo esc_html($title); ?></h1>
			<span class="n"><?php echo esc_html($count_html); ?></span>
		</div>
		<div class="tools">
			<button id="openFilters" type="button"><?php acme_te('Filter'); ?><?php echo $chips ? ' <span id="fcount">(' . count($chips) . ')</span>' : ''; ?></button>
			<select id="sort" name="orderby" aria-label="<?php echo esc_attr(acme_t('Sort by')); ?>" onchange="this.form.submit()">
				<?php foreach ($sorts as $k => $label): ?>
					<option value="<?php echo esc_attr($k); ?>" <?php selected($cur_sort, $k); ?>><?php echo esc_html($label); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="shop">
		<aside class="sheet" aria-label="<?php echo esc_attr(acme_t('Product filters')); ?>">
			<div class="sheet-head">
				<span><?php acme_te('Filter'); ?></span>
				<button id="closeFilters" class="btn-sec" type="button"><?php acme_te('View results'); ?></button>
			</div>

			<label class="fsearch">
				<svg class="i" viewBox="0 0 24 24"><path d="M11 4a7 7 0 1 0 0 14 7 7 0 0 0 0-14zm9 16-4-4" /></svg>
				<input id="fq" type="search" name="acme_q" value="<?php echo esc_attr($cur_q); ?>" placeholder="<?php echo esc_attr(acme_t('Search products')); ?>" aria-label="<?php echo esc_attr(acme_t('Search products')); ?>" />
			</label>

			<div id="filters">
				<fieldset>
					<legend><?php acme_te('Category'); ?></legend>
					<?php
					$preserve = array_filter(['acme_q' => $cur_q, 'price' => $cur_price, 'orderby' => $cur_sort]);
					$cat_url = fn($id) => add_query_arg($id ? $preserve + ['acme_cat' => $id] : $preserve, $shop_url);
					?>
					<label>
						<input type="radio" name="acme_cat" value="" <?php checked($cur_cat, 0); ?> onchange="location='<?php echo esc_url($cat_url(0)); ?>'">
						<span><?php acme_te('All products'); ?></span><em><?php echo (int) wp_count_posts('product')->publish; ?></em>
					</label>
					<?php foreach (get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => [get_option('default_product_cat')]]) as $t): ?>
						<label>
							<input type="radio" name="acme_cat" value="<?php echo (int) $t->term_id; ?>" <?php checked($cur_cat, $t->term_id); ?> onchange="location='<?php echo esc_url($cat_url($t->term_id)); ?>'">
							<span><?php echo esc_html($t->name); ?></span><em><?php echo (int) $t->count; ?></em>
						</label>
					<?php endforeach; ?>
				</fieldset>

				<fieldset>
					<legend><?php acme_te('Price'); ?></legend>
					<?php foreach (get_price_buckets() as $k => $label): ?>
						<label>
							<input type="radio" name="price" value="<?php echo esc_attr($k); ?>" <?php checked($cur_price, $k); ?> onchange="this.form.submit()">
							<span><?php echo esc_html($label); ?></span>
						</label>
					<?php endforeach; ?>
				</fieldset>
			</div>
		</aside>

		<div class="shop-col">
			<div id="chips"><?php
				foreach ($chips as [$label, $href]) {
					echo '<a href="' . esc_url($href) . '">' . esc_html($label) . ' <span aria-hidden="true">&times;</span></a>';
				}
				if (count($chips) > 1) echo '<a class="clear" href="' . esc_url($shop_url) . '">' . esc_html(acme_t('Clear all')) . '</a>';
			?></div>
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
				?>
				<div class="empty">
					<b><?php acme_te('No matching products yet.'); ?></b>
					<p><?php acme_te('Try removing one of the filters.'); ?></p>
					<a class="btn-sec" href="<?php echo esc_url($shop_url); ?>"><?php acme_te('Clear all filters'); ?></a>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</form>
</main>

<?php
get_footer('shop');
