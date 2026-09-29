<?php
// Shop / category / tag archives render through the template that owns the
// filter sidebar; single product keeps the plain WooCommerce shell.
if (!is_singular('product')) {
	get_template_part('woocommerce/archive-product');
	return;
}
get_header(); ?>
<main id="content" class="wrap woo">
<?php woocommerce_content(); ?>
</main>
<?php get_footer();
