<?php
defined('ABSPATH') || exit;

// The address book drives checkout: the card shows the chosen address and the
// field grid lives inside the picker dialog (see template-parts/address-dialog.php).
$addr = acme_address_default() !== '' ? acme_address_find(acme_address_default()) : null;
?>
<h2>Alamat pengiriman</h2>
<div class="box addr" id="addrCard">
	<div id="addrView" aria-live="polite">
		<?php if ($addr): ?>
			<b><?php echo esc_html($addr['name']); ?></b>
			<span>+62<?php echo esc_html($addr['phone']); ?></span>
			<span><?php echo esc_html(acme_address_line($addr)); ?></span>
		<?php else: ?>
			<b>Belum ada alamat</b>
			<span>Tambah alamat pengiriman dulu.</span>
		<?php endif; ?>
	</div>
	<button type="button" class="linkbtn" id="addrChange"><?php echo $addr ? 'Ubah' : 'Tambah alamat'; ?></button>
</div>

<?php get_template_part('template-parts/address-dialog'); ?>
