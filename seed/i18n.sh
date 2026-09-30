set -e
cd /var/www/html
mkdir -p wp-content/languages/plugins
WPV=$(wp core version)
WCV=$(wp plugin get woocommerce --field=version)
curl -fsSL -o /tmp/core-id.zip "https://downloads.wordpress.org/translation/core/$WPV/id_ID.zip"
curl -fsSL -o /tmp/wc-id.zip "https://downloads.wordpress.org/translation/plugin/woocommerce/$WCV/id_ID.zip" || curl -fsSL -o /tmp/wc-id.zip "https://downloads.wordpress.org/translation/plugin/woocommerce/latest/id_ID.zip"
php -r '
foreach ([["/tmp/core-id.zip","wp-content/languages"],["/tmp/wc-id.zip","wp-content/languages/plugins"]] as [$f,$d]) {
  $z = new ZipArchive; if ($z->open($f) !== true) { fwrite(STDERR, "bad zip $f\n"); exit(1); }
  $z->extractTo($d); $z->close();
}'
wp site switch-language id_ID
wp option get WPLANG
