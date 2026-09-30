<?php
/**
 * Reset link sent.
 *
 * @version 3.9.0
 */
defined('ABSPATH') || exit;
?>
<div class="auth">
	<h1>Cek email kamu</h1>
	<p class="sub">Tautan reset password sudah dikirim ke email akunmu. Bisa butuh beberapa menit; tunggu 10 menit sebelum minta lagi.</p>
	<p class="alt"><a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><b>Kembali ke halaman masuk</b></a></p>
</div>
