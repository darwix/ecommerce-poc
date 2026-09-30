<?php
// Saved delivery addresses, kept on the logged-in user so checkout and the
// "Alamat" page share one book. Records use short keys; each maps to a
// WooCommerce billing field, so a record drops straight into the checkout form.

const ACME_ADDR_META = 'acme_addresses';
const ACME_ADDR_DEFAULT = 'acme_address_default';

function acme_address_field_map(): array {
	return [
		'name'      => 'billing_first_name',
		'phone'     => 'billing_phone',
		'state'     => 'billing_state',
		'city'      => 'billing_city',
		'kecamatan' => 'billing_kecamatan',
		'postcode'  => 'billing_postcode',
		'address_1' => 'billing_address_1',
		'detail'    => 'billing_detail',
	];
}

// Labels for messages; the form itself uses WooCommerce's own field definitions.
function acme_address_labels(): array {
	return [
		'name'      => acme_t('Full name'),
		'phone'     => acme_t('WhatsApp number'),
		'state'     => acme_t('Province'),
		'city'      => acme_t('City / regency'),
		'kecamatan' => acme_t('District (kecamatan)'),
		'postcode'  => acme_t('Postal code'),
		'address_1' => acme_t('Street, building, house number'),
		'detail'    => acme_t('Other details'),
	];
}

function acme_addresses(): array {
	if (!is_user_logged_in()) return [];
	$list = get_user_meta(get_current_user_id(), ACME_ADDR_META, true);
	return is_array($list) ? array_values($list) : [];
}

function acme_address_default(): string {
	return is_user_logged_in() ? (string) get_user_meta(get_current_user_id(), ACME_ADDR_DEFAULT, true) : '';
}

function acme_address_find(string $id): ?array {
	foreach (acme_addresses() as $a) {
		if ($a['id'] === $id) return $a;
	}
	return null;
}

function acme_address_save_book(array $list, string $default = ''): void {
	$uid = get_current_user_id();
	update_user_meta($uid, ACME_ADDR_META, array_values($list));
	if ($default !== '') update_user_meta($uid, ACME_ADDR_DEFAULT, $default);
	// The selected address may have been deleted; fall back to the first one.
	$ids = array_column($list, 'id');
	$cur = (string) get_user_meta($uid, ACME_ADDR_DEFAULT, true);
	if (!in_array($cur, $ids, true)) update_user_meta($uid, ACME_ADDR_DEFAULT, $ids[0] ?? '');
}

// "Kota, Kecamatan, Jalan" - the one-line address shown under the name.
function acme_address_line(array $a): string {
	return implode(', ', array_filter([$a['city'] ?? '', $a['kecamatan'] ?? '', $a['address_1'] ?? '']));
}

// Fields the book insists on; `detail` is the only optional one.
function acme_address_required(): array {
	return ['name', 'phone', 'state', 'city', 'kecamatan', 'postcode', 'address_1'];
}

// One endpoint for save / delete / default: the three ops share the nonce, the
// login check and the re-read of the book.
add_action('wp_ajax_acme_address', function () {
	check_ajax_referer('acme_address', 'nonce');
	if (!is_user_logged_in()) wp_send_json_error(['message' => acme_t('Log in to save addresses.')], 401);

	$op   = sanitize_key($_POST['op'] ?? '');
	$id   = sanitize_key($_POST['id'] ?? '');
	$list = acme_addresses();

	if ($op === 'delete') {
		$list = array_values(array_filter($list, fn($a) => $a['id'] !== $id));
		acme_address_save_book($list);
		wp_send_json_success(['list' => $list, 'default' => acme_address_default()]);
	}

	if ($op === 'default') {
		if (!acme_address_find($id)) wp_send_json_error(['message' => acme_t('Address not found.')]);
		acme_address_save_book($list, $id);
		wp_send_json_success(['list' => $list, 'default' => $id]);
	}

	if ($op !== 'save') wp_send_json_error(['message' => acme_t('Unknown request.')]);

	$rec = [];
	foreach (array_keys(acme_address_field_map()) as $key) {
		$rec[$key] = sanitize_text_field(wp_unslash($_POST[$key] ?? ''));
	}
	// Report every missing field at once; one-at-a-time means the next error only
	// appears after the previous field is filled.
	$missing = [];
	foreach (acme_address_required() as $key) {
		if ($rec[$key] === '') $missing[$key] = acme_t('%s is required.', acme_address_labels()[$key]);
	}
	if ($missing) {
		wp_send_json_error(['message' => reset($missing), 'fields' => $missing]);
	}

	if ($id !== '' && acme_address_find($id)) {
		foreach ($list as $i => $a) {
			if ($a['id'] === $id) $list[$i] = ['id' => $id] + $rec;
		}
	} else {
		$id = substr(md5(uniqid('', true)), 0, 10);
		$list[] = ['id' => $id] + $rec;
	}

	acme_address_save_book($list, $id);
	wp_send_json_success(['list' => $list, 'default' => $id, 'id' => $id]);
});

// Hand the book to the two pages that use it.
add_action('wp_enqueue_scripts', function () {
	if (!acme_address_page()) return;
	wp_enqueue_script('address', get_theme_file_uri('assets/js/address.js'), [], wp_get_theme()->get('Version'), true);
	wp_add_inline_script('address', 'window.acmeAddr=' . wp_json_encode([
		'ajax'      => admin_url('admin-ajax.php'),
		'nonce'     => wp_create_nonce('acme_address'),
		'list'      => acme_addresses(),
		'default'   => acme_address_default(),
		'fields'    => acme_address_field_map(),
		'loggedIn'  => is_user_logged_in(),
		'manageUrl' => get_permalink(get_page_by_path('address')) ?: '',
	]) . ';', 'before');
});

// Pages where the address book is in play: checkout, the "Alamat" page and account settings.
function acme_address_page(): bool {
	return is_page('address') || (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('edit-account')) || (function_exists('is_checkout') && is_checkout() && !is_wc_endpoint_url());
}

// The cascading wilaya selects are needed by the address form too.
add_action('wp_enqueue_scripts', function () {
	if (acme_address_page()) {
		wp_enqueue_script('checkout-address', get_theme_file_uri('assets/js/checkout-address.js'), [], wp_get_theme()->get('Version'), true);
	}
}, 20);
