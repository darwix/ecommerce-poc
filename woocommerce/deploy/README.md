# Deploy the Acme WooCommerce POC to a VPS

Needs: a VPS (Ubuntu 22.04+, 2 GB RAM, ports 80/443 open), a domain with an A record pointing at the server, Docker + the compose plugin.

1. Copy the repo's `woocommerce/` folder to the server (`git clone`, then `cd woocommerce/deploy`).
2. `cp .env.example .env` and fill it in (domain, DB passwords, admin login).
3. `docker compose -f compose.prod.yaml up -d` (Caddy gets the HTTPS certificate automatically).
4. `./install.sh` installs WordPress, WooCommerce 10.9.4, the DOKU plugin and the Acme theme, then seeds the 24 products, About/FAQ pages and the menu.
5. In wp-admin, WooCommerce > Settings > Payments > DOKU: enter the DOKU sandbox keys (Client ID + Secret Key).
6. In the DOKU dashboard set the notification URL to `https://<domain>/wp-json/doku/notification` (and `/wp-json/doku/qrisnotification` for QRIS) so orders flip to paid.

Notes
- WooCommerce 11.x needs WordPress 7.0, so it is pinned to 10.9.4 on WordPress 6.9.
- The DOKU plugin needs HPOS off (install.sh does this).
- Product photos are hotlinked from DummyJSON at seed time and downloaded into the media library.
