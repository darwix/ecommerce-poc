<?php
/**
 * New password after the emailed reset link.
 *
 * @version 9.2.0
 */
defined('ABSPATH') || exit;
$eye = '<button type="button" class="eye" aria-label="' . esc_attr(acme_t('Show password')) . '" data-eye><svg class="i" viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></button>';
?>
<div class="auth">
	<h1><?php acme_te('New password'); ?></h1>
	<p class="sub"><?php acme_te('Enter a new password for your account.'); ?></p>
	<?php do_action('woocommerce_before_reset_password_form'); ?>
	<form method="post" class="woocommerce-ResetPassword lost_reset_password">
		<label><?php acme_te('New password'); ?><span class="pw"><input name="password_1" type="password" autocomplete="new-password" required placeholder="<?php echo esc_attr(acme_t('New password')); ?>"><?php echo $eye; ?></span></label>
		<label><?php acme_te('Repeat new password'); ?><span class="pw"><input name="password_2" type="password" autocomplete="new-password" required placeholder="<?php echo esc_attr(acme_t('Repeat password')); ?>"><?php echo $eye; ?></span></label>
		<input type="hidden" name="reset_key" value="<?php echo esc_attr($args['key']); ?>">
		<input type="hidden" name="reset_login" value="<?php echo esc_attr($args['login']); ?>">
		<?php do_action('woocommerce_resetpassword_form'); ?>
		<input type="hidden" name="wc_reset_password" value="true">
		<?php wp_nonce_field('reset_password', 'woocommerce-reset-password-nonce'); ?>
		<button class="btn wide" type="submit"><?php acme_te('Save password'); ?></button>
	</form>
	<?php do_action('woocommerce_after_reset_password_form'); ?>
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
