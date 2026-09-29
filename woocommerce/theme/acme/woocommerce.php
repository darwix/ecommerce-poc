<?php get_header(); ?>
<main id="content" class="wrap woo">
<?php if (is_singular('product')): woocommerce_content(); else:
	do_action('woocommerce_before_main_content');
	do_action('woocommerce_archive_description');
	if (woocommerce_product_loop()):
		do_action('woocommerce_before_shop_loop');
		woocommerce_product_loop_start();
		if (wc_get_loop_prop('total')):
			while (have_posts()): the_post();
				do_action('woocommerce_shop_loop');
				wc_get_template_part('content', 'product');
			endwhile;
		endif;
		woocommerce_product_loop_end();
		do_action('woocommerce_after_shop_loop');
	else:
		do_action('woocommerce_no_products_found');
	endif;
	do_action('woocommerce_after_main_content');
endif; ?>
</main>
<?php get_footer();
