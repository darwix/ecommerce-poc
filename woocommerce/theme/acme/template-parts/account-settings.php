<?php
// /my-account/edit-account/, after mockup-v2/settings.html. Profile posts to
// WooCommerce's own save_account_details handler; shipping links to /address/.
defined('ABSPATH') || exit;
$user  = wp_get_current_user();
$name  = trim($user->first_name . ' ' . $user->last_name) ?: $user->display_name;
$bday  = array_pad(explode('-', (string) get_user_meta($user->ID, 'acme_birthday', true)), 3, '');
$icons = [
	'pProfile' => 'M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM4 21c0-4 4-6 8-6s8 2 8 6',
	'pShip'    => 'M2 7h12v9H2zM14 10h4l3 3v3h-7M6 19a2 2 0 1 0 0-.1M17 19a2 2 0 1 0 0-.1',
	'pAcct'    => 'M12 3l8 3v6c0 5-4 8-8 9-4-1-8-4-8-9V6zM12 13v3',
];
get_header();
?>
<main id="content" class="acct set">
	<h1 class="settitle">Pengaturan</h1>
	<?php wc_print_notices(); ?>
	<div class="setwrap">
		<aside class="abox setnav">
			<h2>Pengaturan saya</h2>
			<nav id="setNav">
				<?php foreach (['pProfile' => 'Info profil', 'pShip' => 'Info pengiriman', 'pAcct' => 'Info akun'] as $id => $label) : ?>
				<button type="button" data-p="<?php echo $id; ?>"<?php echo $id === 'pProfile' ? ' aria-current="true"' : ''; ?>>
					<svg class="i" viewBox="0 0 24 24"><path d="<?php echo $icons[$id]; ?>"/></svg><?php echo $label; ?>
				</button>
				<?php endforeach; ?>
				<a href="<?php echo esc_url(wc_logout_url()); ?>">
					<svg class="i" viewBox="0 0 24 24"><path d="M15 4h4v16h-4M11 8l-4 4 4 4M7 12h9"/></svg>Keluar
				</a>
			</nav>
		</aside>
		<div class="setpanes">
			<section class="abox" id="pProfile">
				<h2>Info profil</h2>
				<form method="post" style="display:grid;gap:14px">
					<label>Nama<input name="account_first_name" value="<?php echo esc_attr($name); ?>" autocomplete="name" required></label>
					<label>Email<input value="<?php echo esc_attr($user->user_email); ?>" disabled></label>
					<input type="hidden" name="account_email" value="<?php echo esc_attr($user->user_email); ?>">
					<input type="hidden" name="account_display_name" value="<?php echo esc_attr($user->display_name); ?>">
					<fieldset class="bday" data-bday="<?php echo esc_attr(wp_json_encode($bday)); ?>">
						<legend>Tanggal lahir</legend>
						<select name="acme_bd" aria-label="Tanggal"></select>
						<select name="acme_bm" aria-label="Bulan"></select>
						<select name="acme_by" aria-label="Tahun"></select>
					</fieldset>
					<div class="fbtns"><button type="submit" class="btn">Simpan</button></div>
					<?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
					<input type="hidden" name="action" value="save_account_details">
				</form>
			</section>
			<section class="abox" id="pShip" hidden>
				<h2>Info pengiriman</h2>
				<div class="addr-list" data-addr-list></div>
				<button type="button" class="linkbtn" id="addrAdd">+ Tambah baru</button>
			</section>
			<section class="abox" id="pAcct" hidden>
				<h2>Info akun</h2>
				<?php if (!user_can($user, 'manage_options')) : ?>
				<button type="button" class="danger" id="delBtn">Hapus akun saya</button>
				<?php endif; ?>
				<p class="note">Semua data kamu di toko ini akan hilang dan tidak bisa diambil lagi, termasuk data pribadi dan alamat tersimpan. Riwayat pesanan tetap disimpan toko.</p>
				<p class="warn">Tindakan ini tidak bisa dibatalkan.</p>
			</section>
		</div>
	</div>
	<?php get_template_part('template-parts/address-dialog'); ?>
	<dialog id="delDlg" aria-labelledby="delTitle">
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<div class="dhead"><h2 id="delTitle">Hapus akun saya?</h2></div>
			<p class="note">Akun dan data pribadi kamu akan dihapus permanen.</p>
			<input type="hidden" name="action" value="acme_delete_account">
			<?php wp_nonce_field('acme_delete_account'); ?>
			<div class="fbtns"><button class="danger" type="submit">Ya, hapus akun</button><button class="btn-sec" type="submit" formmethod="dialog">Batal</button></div>
		</form>
	</dialog>
</main>
<?php get_footer();
