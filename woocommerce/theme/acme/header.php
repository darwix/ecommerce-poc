<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="sent" aria-hidden="true"></div>
<a class="skip" href="#content">Langsung ke konten</a>
<header>
	<div class="wrap bar">
		<button class="menu-btn" aria-label="Menu" onclick="document.querySelector('nav').classList.toggle('open')"><svg class="i" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
		<a class="logo" href="<?php echo esc_url(home_url('/')); ?>"AB<small>ACME BEAUTY</small></a>
		<nav>
			<a href="<?php echo esc_url(home_url('/')); ?>"<?php echo is_front_page() ? ' class="active"' : ''; ?>>Beranda</a><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"<?php echo is_shop() ? ' class="active"' : ''; ?>>Semua produk</a><a href="<?php echo esc_url(home_url('/about/')); ?>"<?php echo is_page('about') ? ' class="active"' : ''; ?>>Tentang</a>
		</nav>
		<div class="icons">
			<a href="<?php echo esc_url(home_url('/?post_type=product&s=')); ?>" aria-label="Cari produk"><svg class="i" viewBox="0 0 24 24"><path d="M11 4a7 7 0 1 0 0 14 7 7 0 0 0 0-14zm9 16-4-4"/></svg></a>
			<a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" aria-label="Akun"><svg class="i" viewBox="0 0 24 24"><path d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM4 21c0-4 4-6 8-6s8 2 8 6"/></svg></a>
			<a href="<?php echo esc_url(wc_get_cart_url()); ?>" aria-label="Keranjang"><svg class="i" viewBox="0 0 24 24"><path d="M5 7h14l-1.5 13h-11zM9 7a3 3 0 0 1 6 0"/></svg><?php echo get_cart_badge(); ?></a>
		</div>
	</div>
	<div class="trust"><ul class="wrap">
		<li><svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg><span><b>Ready stock</b> · dikirim 1×24 jam</span></li>
		<li><svg viewBox="0 0 24 24"><path d="M2 7h12v9H2zM14 10h4l3 3v3h-7M6 19a2 2 0 1 0 0-.1M17 19a2 2 0 1 0 0-.1"/></svg><span><b>COD</b> via J&amp;T</span></li>
		<li><svg viewBox="0 0 24 24"><path d="M12 3l8 3v6c0 5-4 8-8 9-4-1-8-4-8-9V6z"/></svg><span>Terpercaya <b>sejak 2018</b></span></li>
	</ul></div>
</header>
