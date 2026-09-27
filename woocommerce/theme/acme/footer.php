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
<script>
new IntersectionObserver(([e]) => document.querySelector("header").classList.toggle("solid", !e.isIntersecting)).observe(document.getElementById("sent"));
</script>
<?php wp_footer(); ?>
</body>
</html>
