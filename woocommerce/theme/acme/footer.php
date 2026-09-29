<?php $logos = ['bri' => 'BRI', 'bni' => 'BNI', 'mandiri' => 'MANDIRI', 'bsi' => 'BSI', 'permata' => 'PERMATA', 'cimb-niaga' => 'CIMB NIAGA', 'ovo' => 'OVO', 'akulaku' => 'AKULAKU', 'alfamart' => 'ALFAMART', 'indomaret' => 'INDOMARET', 'visa' => 'VISA', 'mastercard' => 'MASTERCARD', 'jcb' => 'JCB']; ?>
<footer>
	<div class="wrap">
		<div class="fgrid">
			<div><h4>Bantuan</h4><a href="#">Pengiriman &amp; COD</a><a href="#">Syarat &amp; ketentuan</a><a href="#">Kebijakan privasi</a><a href="<?php echo esc_url(home_url('/faq/')); ?>">FAQ</a><a href="<?php echo esc_url(home_url('/about/')); ?>">Tentang Acme</a></div>
			<div><h4>Follow us</h4><a class="ig" href="#" aria-label="Instagram Acme Beauty"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6"/></svg>Instagram</a></div>
			<div><h4>Metode pembayaran</h4><div class="pay">
				<?php foreach ($logos as $f => $alt) printf('<img src="%s" alt="%s" loading="lazy">', esc_url(get_theme_file_uri("assets/logos/$f.svg")), esc_attr($alt)); ?>
			</div></div>
		</div>
		<p class="legal">© 2026 PT. Acme Beauty Group</p>
	</div>
</footer>
<a class="fab" href="#" aria-label="Chat admin via WhatsApp"><svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.2c-.2.6-1.3 1.2-1.8 1.3-.5.1-1 .1-1.7-.1-.4-.1-.9-.3-1.6-.6-2.8-1.2-4.6-4-4.8-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.3-.3.6-.3.7-.3h.5c.2 0 .4 0 .6.5l.9 2.1c.1.1.1.3 0 .5l-.3.5-.4.5c-.1.1-.3.3-.1.6.2.3.7 1.2 1.6 1.9 1.1 1 2 1.3 2.3 1.4.3.1.4.1.6-.1l.9-1c.2-.3.4-.2.6-.1l2 1c.3.1.4.2.5.3.1.2.1.7-.1 1.3z"/></svg><span>Chat admin</span></a>
<button id="top" aria-label="Kembali ke atas"><svg class="i" viewBox="0 0 24 24"><path d="M12 19V5m-6 6 6-6 6 6"/></svg></button>
<script>
new IntersectionObserver(([e]) => document.querySelector("header").classList.toggle("solid", !e.isIntersecting)).observe(document.getElementById("sent"));
{
	const toTop = document.getElementById("top");
	const sent = document.createElement("div");
	sent.style.cssText = "position:absolute;top:0;left:0;width:1px;height:800px;pointer-events:none";
	document.body.prepend(sent);
	new IntersectionObserver(([e]) => toTop.classList.toggle("show", !e.isIntersecting)).observe(sent);
	toTop.onclick = () => scrollTo({ top: 0 });
}
</script>
<?php
if (function_exists('WC') && WC()->cart) {
	echo get_cartbar();
	echo '<script>document.body.classList.toggle("has-bar", !document.getElementById("cartbar").hidden);</script>';
}
wp_footer();
?>
</body>
</html>
