<?php
/**
 * Reset link sent.
 *
 * @version 3.9.0
 */
defined('ABSPATH') || exit;
?>
<div class="auth">
	<h1><?php acme_te('Check your email'); ?></h1>
	<p class="sub"><?php acme_te('The password reset link has been sent to your account email. It may take a few minutes; wait 10 minutes before requesting again.'); ?></p>
	<p class="alt"><a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><b><?php acme_te('Back to the login page'); ?></b></a></p>
</div>
