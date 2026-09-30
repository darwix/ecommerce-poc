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
$eye  = '<button type="button" class="eye" aria-label="' . esc_attr(acme_t('Show password')) . '" data-eye><svg class="i" viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></button>';
$mo   = array_map('acme_t', ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']);
$opts = fn($ph, $vals, $labels = null) => '<option value="">' . $ph . '</option>' . implode('', array_map(fn($v, $l) => "<option value=\"$v\">$l</option>", $vals, $labels ?? $vals));
$days = range(1, 31);
$year = range(gmdate('Y') - 10, gmdate('Y') - 79);
?>
<div class="auth">
	<h1><?php acme_te($reg ? 'Sign up' : 'Log in'); ?></h1>
	<?php do_action('woocommerce_before_customer_login_form'); ?>

	<?php if (isset($_GET['redirect_to'])) : ?><p class="hint"><?php acme_te('Log in first to continue to checkout.'); ?></p><?php endif; ?>

	<?php if ($reg) : ?>
	<form method="post" class="woocommerce-form-register">
		<label><?php acme_te('Full name'); ?><input name="acme_name" autocomplete="name" required placeholder="<?php echo esc_attr(acme_t('Full name')); ?>" value="<?php echo esc_attr(wp_unslash($_POST['acme_name'] ?? '')); ?>"></label>
		<label>Email<input name="email" type="email" autocomplete="email" required placeholder="name@email.com" value="<?php echo esc_attr(wp_unslash($_POST['email'] ?? '')); ?>"></label>
		<label>Password<span class="pw"><input name="password" type="password" autocomplete="new-password" required minlength="8" placeholder="<?php echo esc_attr(acme_t('At least 8 characters')); ?>"><?php echo $eye; ?></span></label>
		<fieldset class="bday">
			<legend><?php acme_te('Date of birth'); ?> <small><?php acme_te('(optional)'); ?></small></legend>
			<select name="acme_bd" aria-label="<?php echo esc_attr(acme_t('Day')); ?>"><?php echo $opts(acme_t('Day'), $days); ?></select>
			<select name="acme_bm" aria-label="<?php echo esc_attr(acme_t('Month')); ?>"><?php echo $opts(acme_t('Month'), range(1, 12), $mo); ?></select>
			<select name="acme_by" aria-label="<?php echo esc_attr(acme_t('Year')); ?>"><?php echo $opts(acme_t('Year'), $year); ?></select>
		</fieldset>
		<?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
		<input type="hidden" name="redirect" value="<?php echo esc_url($to); ?>">
		<button class="btn wide" type="submit" name="register" value="1"><?php acme_te('Create account'); ?></button>
	</form>
	<p class="alt"><?php acme_te('Already have an account?'); ?> <a href="<?php echo esc_url(remove_query_arg('register')); ?>"><b><?php acme_te('Log in here'); ?></b></a></p>

	<?php else : ?>
	<form method="post" class="woocommerce-form-login">
		<label>Email<input name="username" autocomplete="username" required placeholder="name@email.com" value="<?php echo esc_attr(wp_unslash($_POST['username'] ?? '')); ?>"></label>
		<label>Password<span class="pw"><input name="password" type="password" autocomplete="current-password" required placeholder="Password"><?php echo $eye; ?></span></label>
		<?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
		<input type="hidden" name="redirect" value="<?php echo esc_url($to); ?>">
		<button class="btn wide" type="submit" name="login" value="1"><?php acme_te('Log in'); ?></button>
	</form>
	<p class="alt"><?php acme_te("Don't have an account?"); ?> <a href="<?php echo esc_url(add_query_arg('register', 1)); ?>"><b><?php acme_te('Sign up here'); ?></b></a></p>
	<p class="alt"><a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php acme_te('Forgot password?'); ?></a></p>
	<?php endif; ?>
</div>
<script>
document.querySelectorAll('[data-eye]').forEach(function (b) {
	b.onclick = function () {
		var i = b.previousElementSibling, show = i.type === 'password';
		i.type = show ? 'text' : 'password';
		b.setAttribute('aria-label', acmeT(show ? 'Hide password' : 'Show password'));
	};
});
</script>
