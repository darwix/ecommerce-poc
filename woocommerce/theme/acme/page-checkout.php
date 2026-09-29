<!doctype html>
<html <?php language_attributes(); ?> class="co-html">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="co-head">
	<a href="<?php echo esc_url(wc_get_cart_url()); ?>" aria-label="Kembali ke keranjang"><svg class="i" viewBox="0 0 24 24"><path d="M19 12H5m6-6-6 6 6 6"/></svg></a>
	<a class="logo" href="<?php echo esc_url(home_url('/')); ?>"AB<small>ACME BEAUTY</small></a>
	<span></span>
</header>
<main id="content" class="co">
<?php the_content(); ?>
</main>
<?php wp_footer(); ?>
</body>
</html>
