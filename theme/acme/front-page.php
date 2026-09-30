<?php get_header(); ?>
<main id="content">
	<section class="hp hp-c"><img src="<?php echo esc_url(get_theme_file_uri('assets/img/about-2.webp')); ?>" alt="<?php acme_te('Imported bags and shoes arranged on Acme store shelves'); ?>"><div class="hp-copy"><span class="eyebrow">Import fashion · ready stock</span><h1><?php acme_te('New collection this week, ready to ship today.'); ?></h1><p><?php acme_te('Hand-picked imported clothing, bags, and shoes. All stock sits in our Jakarta warehouse, so your order ships right away.'); ?></p><div class="cta"><a class="btn" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php acme_te('Shop now'); ?></a></div></div></section>

	<section class="wrap">
		<div class="shead"><div><h2><?php acme_te('New arrivals'); ?></h2><p><?php acme_te('Limited stock, updated weekly'); ?></p></div><a class="link" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php acme_te('View all'); ?></a></div>
		<div class="grid"><?php foreach (wc_get_products(['limit' => 8, 'orderby' => 'date', 'order' => 'DESC']) as $p) theme_product_card($p); ?></div>
	</section>

	<section class="wrap">
		<div class="shead"><h2><?php acme_te('Shop by category'); ?></h2></div>
		<div class="bento">
		<?php
		$tints = ['pakaian' => '#fbe1ea', 'tas' => '#e3f1f7', 'sepatu' => '#e6f3ee', 'aksesoris' => '#efeaf6', 'kecantikan' => '#fdf0e6'];
		foreach ($tints as $slug => $tint) {
			$t = get_term_by('slug', $slug, 'product_cat');
			if (!$t) continue;
			$ids = wc_get_products(['category' => [$slug], 'limit' => 1, 'return' => 'ids']);
			$img = $ids ? get_the_post_thumbnail_url($ids[0], 'woocommerce_single') : '';
			printf('<a class="tile" href="%s" style="--c: %s">%s<span class="label"><b>%s</b><small>%s</small></span></a>',
				esc_url(get_term_link($t)), $tint, $img ? '<img src="' . esc_url($img) . '" alt="" loading="lazy">' : '', esc_html($t->name), esc_html(acme_t('%d products', $t->count)));
		}
		?>
		</div>
	</section>

	<section class="wrap">
		<div class="shead"><h2><?php acme_te('Best sellers this month'); ?></h2><a class="link" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php acme_te('View all'); ?></a></div>
		<div class="row"><?php foreach (wc_get_products(['limit' => 8, 'orderby' => 'meta_value_num', 'meta_key' => 'total_sales', 'order' => 'DESC']) as $p) theme_product_card($p); ?></div>
	</section>
</main>
<?php get_footer();
