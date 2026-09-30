<?php
// Logged-in dashboard, after mockup-v2/account.html. Login form and the
// orders / edit-address / edit-account endpoints keep the generic page shell.
$endpoint = WC()->query->get_current_endpoint();
if (!is_user_logged_in()) { // login / register / lost-password, own shell (myaccount/form-*.php)
	get_header();
	echo '<main id="content" class="wrap">';
	while (have_posts()) { the_post(); the_content(); }
	echo '</main>';
	get_footer();
	return;
}
if (!is_user_logged_in() || ($endpoint && $endpoint !== 'edit-account')) {
	locate_template('page.php', true, false);
	return;
}
if ($endpoint === 'edit-account') {
	get_template_part('template-parts/account-settings');
	return;
}
$user   = wp_get_current_user();
$first  = $user->first_name ?: $user->display_name;
$orders = wc_get_orders(['customer_id' => $user->ID, 'limit' => 50]);
$coupon = new WC_Coupon('ACME10');
$stat   = [];
foreach ($orders as $o) $stat[acme_order_status($o->get_status())[0]] = 1;
get_header();
?>
<main id="content" class="acct">
	<div class="ahead">
		<h1><?php acme_te('Hi, %s', $first); ?></h1>
		<div>
			<a class="btn-sec" href="<?php echo esc_url(wc_get_endpoint_url('edit-account')); ?>"><?php acme_te('Settings'); ?></a>
			<a class="btn-sec" href="<?php echo esc_url(wc_logout_url()); ?>"><?php acme_te('Log out'); ?></a>
		</div>
	</div>
	<?php if ($coupon->get_id()) : ?>
	<section class="abox">
		<h2><?php acme_te('My voucher'); ?></h2>
		<div class="vouch">
			<b><?php echo esc_html(strtoupper($coupon->get_code())); ?></b>
			<span><?php acme_te('%s%% off all products', wc_format_localized_price($coupon->get_amount())); ?><small><?php acme_te('Use it on checkout'); ?></small></span>
		</div>
	</section>
	<?php endif; ?>
	<section class="abox">
		<div class="tabs" role="tablist">
			<button role="tab" id="tOrders" aria-selected="true" aria-controls="pOrders"><?php acme_te('Orders'); ?></button>
			<button role="tab" id="tWish" aria-selected="false" aria-controls="pWish">Wishlist</button>
		</div>
		<div role="tabpanel" id="pOrders" aria-labelledby="tOrders">
			<div class="otools">
				<h2><?php acme_te('My orders (%d)', count($orders)); ?></h2>
				<input id="q" type="search" placeholder="<?php echo esc_attr(acme_t('Search order no. / product')); ?>" aria-label="<?php echo esc_attr(acme_t('Search orders')); ?>">
				<select id="status" aria-label="<?php echo esc_attr(acme_t('Filter status')); ?>">
					<option value=""><?php acme_te('All statuses'); ?></option>
					<?php foreach (array_keys($stat) as $s) echo '<option>' . esc_html($s) . '</option>'; ?>
				</select>
			</div>
			<div id="orders">
				<?php foreach ($orders as $o) :
					[$label, $cls] = acme_order_status($o->get_status());
					$names = [];
					$qty   = 0;
					foreach ($o->get_items() as $it) { $names[] = $it->get_name(); $qty += $it->get_quantity(); }
					?>
				<article class="order" data-status="<?php echo esc_attr($label); ?>" data-q="<?php echo esc_attr(strtolower($o->get_order_number() . ' ' . implode(' ', $names))); ?>">
					<div class="ohead">
						<div><b><?php acme_te('Order #%s', $o->get_order_number()); ?></b><small><?php echo esc_html(date_i18n('j M Y', $o->get_date_created()->getTimestamp()) . ' · ' . $o->get_payment_method_title()); ?></small></div>
						<span class="st <?php echo $cls; ?>"><?php echo esc_html($label); ?></span>
					</div>
					<?php foreach ($o->get_items() as $it) :
						$p    = $it->get_product();
						$meta = wc_display_item_meta($it, ['echo' => false, 'before' => '', 'after' => '', 'separator' => ', ', 'label_before' => '', 'label_after' => ': ', 'autop' => false]);
						?>
					<div class="coline">
						<?php echo $p ? $p->get_image('woocommerce_thumbnail') : ''; ?>
						<span><b><?php echo esc_html($it->get_name()); ?></b><small><?php echo esc_html(implode(' · ', array_filter([$p ? $p->get_sku() : '', wp_strip_all_tags($meta)]))); ?></small></span>
						<span class="r"><b><?php echo wp_kses_post(wc_price($o->get_item_subtotal($it, false, true) , ['currency' => $o->get_currency()])); ?></b><small>×<?php echo (int) $it->get_quantity(); ?></small></span>
					</div>
					<?php endforeach; ?>
					<p class="ototal"><?php echo esc_html(acme_t('%d items', $qty)); ?>: <b><?php echo wp_kses_post($o->get_formatted_order_total()); ?></b></p>
				</article>
				<?php endforeach; ?>
				<p class="none" <?php echo $orders ? 'hidden' : ''; ?>><?php echo esc_html($orders ? acme_t('No matching orders.') : acme_t('No orders yet.')); ?></p>
			</div>
		</div>
		<div role="tabpanel" id="pWish" aria-labelledby="tWish" hidden>
			<div class="grid" id="wishGrid"></div>
		</div>
	</section>
</main>
<?php get_footer();
