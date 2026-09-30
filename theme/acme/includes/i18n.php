<?php
// EN/ID language switcher. English is the base: templates hold English and
// acme_t() looks up the Indonesian dictionary (lang-id.php) when the cookie
// says id. The header toggle links to ?lang=en|id; the locale filter makes
// WooCommerce core strings follow (en_US default, id_ID via WC's language pack).

function acme_lang(): string {
	return ($_COOKIE['acme_lang'] ?? '') === 'id' ? 'id' : 'en';
}

// Indonesian lookup for a hardcoded English string. Unknown keys fall back to
// the English text, so a missed pair degrades to the base output.
function acme_t(string $text, ...$args): string {
	static $id = null;
	if ($id === null) $id = require __DIR__ . '/lang-id.php';
	if (acme_lang() === 'id') $text = $id[$text] ?? $text;
	return $args ? vsprintf($text, $args) : $text;
}

function acme_te(string $text, ...$args): void {
	echo esc_html(acme_t($text, ...$args));
}

// Same dictionary for theme JS: acmeT('English', ...args) in any script.
add_action('wp_enqueue_scripts', function () {
	wp_register_script('acme-l10n', false, [], null, false);
	wp_enqueue_script('acme-l10n');
	$dict = acme_lang() === 'id' ? require __DIR__ . '/lang-id.php' : [];
	wp_add_inline_script('acme-l10n', 'window.acmeLang=' . wp_json_encode(acme_lang())
		. ';window.acmeL10n=' . wp_json_encode($dict)
		. ';window.acmeT=function(s){var a=[].slice.call(arguments,1),t=window.acmeL10n[s]||s,i=0;'
		. 'return a.length?t.replace(/%[sd]/g,function(){return i<a.length?a[i++]:""}):t};');
});

// Toggle link carries ?lang=en|id; store it and strip the param from the URL.
add_action('init', function () {
	if (!isset($_GET['lang']) || is_admin()) return;
	$lang = $_GET['lang'] === 'id' ? 'id' : 'en';
	setcookie('acme_lang', $lang, ['expires' => time() + YEAR_IN_SECONDS, 'path' => COOKIEPATH ?: '/', 'domain' => COOKIE_DOMAIN]);
	$_COOKIE['acme_lang'] = $lang; // same-request reads see the new choice
	wp_safe_redirect(home_url(remove_query_arg('lang')));
	exit;
});

// Frontend only: wp-admin has no toggle and keeps the site language.
add_filter('locale', function ($locale) {
	if (is_admin()) return $locale;
	return acme_lang() === 'id' ? 'id_ID' : 'en_US';
});
