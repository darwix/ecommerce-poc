#!/usr/bin/env bash
# One-time setup on the server, run from woocommerce/deploy after `docker compose -f compose.prod.yaml up -d`.
# Installs WordPress + WooCommerce, activates the Acme theme, applies store settings and seeds the sample products.
set -euo pipefail
cd "$(dirname "$0")"
set -a; . ./.env; set +a
WPCLI="docker compose -f compose.prod.yaml exec -T -u www-data -w /var/www/html wp"
if ! command -v docker >/dev/null; then echo "docker is required"; exit 1; fi
docker compose -f compose.prod.yaml exec -T wp sh -c 'command -v wp >/dev/null || (curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x /usr/local/bin/wp)'
$WPCLI wp core install --url="https://$DOMAIN" --title="Acme" --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASSWORD" --admin_email="$WP_ADMIN_EMAIL" --skip-email
$WPCLI wp plugin install woocommerce --version=10.9.4 --activate
$WPCLI wp plugin install doku-payment --activate
$WPCLI wp theme activate acme
$WPCLI wp wc hpos disable || true            # DOKU plugin needs classic order storage
$WPCLI wp option update woocommerce_currency IDR
$WPCLI wp option update woocommerce_default_country ID:JK
$WPCLI wp option update woocommerce_price_num_decimals 0
$WPCLI wp option update woocommerce_price_thousand_sep .
$WPCLI wp option update woocommerce_coming_soon no
$WPCLI wp option update woocommerce_cod_settings '{"enabled":"yes"}' --format=json
$WPCLI wp option update woocommerce_bacs_settings '{"enabled":"yes"}' --format=json
$WPCLI wp rewrite structure '/%postname%/' --hard
docker compose -f compose.prod.yaml cp ../seed/seed.sh wp:/tmp/seed.sh
docker compose -f compose.prod.yaml cp ../seed/pages.sh wp:/tmp/pages.sh
docker compose -f compose.prod.yaml cp ../seed/menu.sh wp:/tmp/menu.sh
docker compose -f compose.prod.yaml exec -T -u www-data wp bash /tmp/seed.sh
docker compose -f compose.prod.yaml exec -T -u www-data wp bash /tmp/pages.sh
docker compose -f compose.prod.yaml exec -T -u www-data wp bash /tmp/menu.sh
echo "Done. Open https://$DOMAIN . Set DOKU sandbox keys under WooCommerce > Settings > Payments."
