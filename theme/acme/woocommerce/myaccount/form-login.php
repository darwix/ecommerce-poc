<?php
/**
 * Login / register, after mockup-v2/login.html + register.html.
 * ?register=1 shows the register form.
 *
 * @version 9.9.0
 */
defined('ABSPATH') || exit;

$reg  = isset($_GET['register']);
$to   = acme_auth_redirect();
$eye  = '<button type="button" class="eye" aria-label="Tampilkan password" data-eye><svg class="i" viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></button>';
$mo   = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$opts = fn($ph, $vals, $labels = null) => '<option value="">' . $ph . '</option>' . implode('', array_map(fn($v, $l) => "<option value=\"$v\">$l</option>", $vals, $labels ?? $vals));
$days = range(1, 31);
$year = range(gmdate('Y') - 10, gmdate('Y') - 79);
?>
<div class="auth">
	<h1><?php echo $reg ? 'Daftar' : 'Masuk'; ?></h1>
	<?php do_action('woocommerce_before_customer_login_form'); ?>

	<?php if (isset($_GET['redirect_to'])) : ?><p class="hint">Masuk dulu untuk lanjut ke checkout.</p><?php endif; ?>

	<?php if ($reg) : ?>
	<form method="post" class="woocommerce-form-register">
		<label>Nama lengkap<input name="acme_name" autocomplete="name" required placeholder="Nama lengkap" value="<?php echo esc_attr(wp_unslash($_POST['acme_name'] ?? '')); ?>"></label>
		<label>Email<input name="email" type="email" autocomplete="email" required placeholder="nama@email.com" value="<?php echo esc_attr(wp_unslash($_POST['email'] ?? '')); ?>"></label>
		<label>Password<span class="pw"><input name="password" type="password" autocomplete="new-password" required minlength="6" placeholder="Minimal 6 karakter"><?php echo $eye; ?></span></label>
		<fieldset class="bday">
			<legend>Tanggal lahir <small>(opsional)</small></legend>
			<select name="acme_bd" aria-label="Tanggal"><?php echo $opts('Tgl', $days); ?></select>
			<select name="acme_bm" aria-label="Bulan"><?php echo $opts('Bulan', range(1, 12), $mo); ?></select>
			<select name="acme_by" aria-label="Tahun"><?php echo $opts('Tahun', $year); ?></select>
		</fieldset>
		<?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
		<input type="hidden" name="redirect" value="<?php echo esc_url($to); ?>">
		<button class="btn wide" type="submit" name="register" value="Daftar">Buat akun baru</button>
	</form>
	<p class="alt">Sudah punya akun? <a href="<?php echo esc_url(remove_query_arg('register')); ?>"><b>Masuk di sini</b></a></p>

	<?php else : ?>
	<form method="post" class="woocommerce-form-login">
		<label>Email<input name="username" autocomplete="username" required placeholder="nama@email.com" value="<?php echo esc_attr(wp_unslash($_POST['username'] ?? '')); ?>"></label>
		<label>Password<span class="pw"><input name="password" type="password" autocomplete="current-password" required placeholder="Password"><?php echo $eye; ?></span></label>
		<?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
		<input type="hidden" name="redirect" value="<?php echo esc_url($to); ?>">
		<button class="btn wide" type="submit" name="login" value="Masuk">Masuk</button>
	</form>
	<p class="alt">Belum punya akun? <a href="<?php echo esc_url(add_query_arg('register', 1)); ?>"><b>Daftar di sini</b></a></p>
	<p class="alt"><a href="<?php echo esc_url(wp_lostpassword_url()); ?>">Lupa password?</a></p>
	<?php endif; ?>
</div>
<script>
document.querySelectorAll('[data-eye]').forEach(function (b) {
	b.onclick = function () {
		var i = b.previousElementSibling, show = i.type === 'password';
		i.type = show ? 'text' : 'password';
		b.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
	};
});
</script>
