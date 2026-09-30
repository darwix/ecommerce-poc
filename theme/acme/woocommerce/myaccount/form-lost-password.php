<?php
/**
 * Lost password, styled like form-login.php.
 *
 * @version 9.2.0
 */
defined('ABSPATH') || exit;
?>
<div class="auth">
	<h1><?php acme_te('Forgot password'); ?></h1>
	<p class="sub"><?php acme_te('Enter your account email. We will send a link to create a new password.'); ?></p>
	<?php do_action('woocommerce_before_lost_password_form'); ?>
	<form method="post" class="woocommerce-ResetPassword lost_reset_password">
		<label>Email<input name="user_login" autocomplete="username" required placeholder="name@email.com"></label>
		<?php do_action('woocommerce_lostpassword_form'); ?>
		<input type="hidden" name="wc_reset_password" value="true">
		<?php wp_nonce_field('lost_password', 'woocommerce-lost-password-nonce'); ?>
		<button class="btn wide" type="submit"><?php acme_te('Send reset link'); ?></button>
	</form>
	<p class="alt"><a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><b><?php acme_te('Back to the login page'); ?></b></a></p>
	<?php do_action('woocommerce_after_lost_password_form'); ?>
</div>
