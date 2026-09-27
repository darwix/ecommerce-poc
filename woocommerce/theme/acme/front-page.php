<?php get_header(); ?>
<main id="content">
	<section class="hp hp-c"><img src="<?php echo esc_url(get_theme_file_uri('assets/img/about-2.webp')); ?>" alt="Tas dan sepatu import tertata di rak toko Acme"><div class="hp-copy"><span class="eyebrow">Import fashion · ready stock</span><h1>Koleksi baru minggu ini, siap kirim hari ini.</h1><p>Pakaian, tas, dan sepatu import pilihan. Semua stok ada di gudang kami di Jakarta, jadi pesananmu langsung dikirim.</p><div class="cta"><a class="btn" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Belanja sekarang</a></div></div></section>

	<section class="wrap">
		<div class="shead"><div><h2>Baru datang</h2><p>Stok terbatas, diperbarui tiap minggu</p></div><a class="link" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Lihat semua</a></div>
		<div class="grid"><?php foreach (wc_get_products(['limit' => 8, 'orderby' => 'date', 'order' => 'DESC']) as $p) acme_card($p); ?></div>
	</section>

	<section class="wrap">
		<div class="shead"><h2>Belanja per kategori</h2></div>
		<div class="bento">
		<?php
		$tints = ['pakaian' => '#fbe1ea', 'tas' => '#e3f1f7', 'sepatu' => '#e6f3ee', 'aksesoris' => '#efeaf6', 'kecantikan' => '#fdf0e6'];
		foreach ($tints as $slug => $tint) {
			$t = get_term_by('slug', $slug, 'product_cat');
			if (!$t) continue;
			$ids = wc_get_products(['category' => [$slug], 'limit' => 1, 'return' => 'ids']);
			$img = $ids ? get_the_post_thumbnail_url($ids[0], 'woocommerce_single') : '';
			printf('<a class="tile" href="%s" style="--c: %s">%s<span class="label"><b>%s</b><small>%d produk</small></span></a>',
				esc_url(get_term_link($t)), $tint, $img ? '<img src="' . esc_url($img) . '" alt="" loading="lazy">' : '', esc_html($t->name), $t->count);
		}
		?>
		</div>
	</section>

	<section class="wrap">
		<div class="shead"><h2>Paling laris bulan ini</h2><a class="link" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Lihat semua</a></div>
		<div class="row"><?php foreach (wc_get_products(['limit' => 8, 'orderby' => 'meta_value_num', 'meta_key' => 'total_sales', 'order' => 'DESC']) as $p) acme_card($p); ?></div>
	</section>
</main>
<?php get_footer();
