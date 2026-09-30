<?php
// Standalone address book (page slug: address). Same dialog as checkout, without
// the order form, so the fields here only feed the saved list.
get_header();
?>
<main id="content">
	<div class="wrap addr-page">
		<h1><?php acme_te('Shipping addresses'); ?></h1>
		<p class="addr-lead"><?php acme_te('Saved addresses are used at checkout. Your main address fills the order form automatically.'); ?></p>
		<div class="addr-list" data-addr-list></div>
		<button type="button" class="btn" id="addrAdd"><?php acme_te('+ Add new'); ?></button>
		<?php get_template_part('template-parts/address-dialog'); ?>
	</div>
</main>
<?php get_footer();
