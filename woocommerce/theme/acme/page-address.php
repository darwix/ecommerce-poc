<?php
// Standalone address book (page slug: address). Same dialog as checkout, without
// the order form, so the fields here only feed the saved list.
get_header();
?>
<main id="content">
	<div class="wrap addr-page">
		<h1>Alamat pengiriman</h1>
		<p class="addr-lead">Alamat tersimpan dipakai saat checkout. Alamat utama otomatis terisi di form pesanan.</p>
		<div class="addr-list" data-addr-list></div>
		<button type="button" class="btn" id="addrAdd">+ Tambah baru</button>
		<?php get_template_part('template-parts/address-dialog'); ?>
	</div>
</main>
<?php get_footer();
