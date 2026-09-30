# Acme POC – WooCommerce rebuild

Proof-of-concept rebuild of acme.example (Indonesian import-fashion / beauty resale store, PT. Acme Beauty Group) with a new pink + blue palette. Target stack is WordPress + WooCommerce; later deployment on Biznet Gio. Design rules live in [DESIGN.md](DESIGN.md).

## Status (2026-09-29)

- **Phase 1 - static mockup (mockup-v2): done, deployed** to https://ecommerce-poc.acme-demo.workers.dev (Cloudflare Workers static assets).
- **Phase 2 - WooCommerce POC deployed and DOKU sandbox live on the host.** Two environments:
  - Local: `woocommerce/` = podman compose (WP + MariaDB, port 8080; admin/admin), WP 6.9.4 + WooCommerce 10.9.4 (11.x needs WP 7). Gotchas: run wp-cli inside `woocommerce_wp_1` as uid 33 (WP's own HTTP client gets 434 from wordpress.org, so install plugins from zips fetched with curl); other containers `wp-app`/`wp-db` belong to a different project.
  - Host (real deploy check): NeoHosting shared cPanel, https://shop.armstronger.web.id/wp/ - WordPress 7.1.2 + WooCommerce 11.1.2, Acme theme active, store Live, 24 products imported and visible, DOKU Payment plugin installed with sandbox keys entered, email confirmed working (DOKU's own receipt + WooCommerce order emails). Full details, gotchas (OPcache fix, anti-bot layer, wrong-IP mistake) and open items in [DEPLOYMENT.md](DEPLOYMENT.md) - **read it before continuing deploy work**.
- Custom theme `woocommerce/theme/acme/` (SCSS + PHP templates, see refactor note below), 24 products seeded from `mockup-v2/app.js` via `woocommerce/seed/gen.js` (local) / `woocommerce/seed/csv.js` -> `woocommerce/deploy/products.csv` (host import).
- Payment: DOKU (official plugin `doku-payment`, hosted DOKU Checkout page, sandbox). Client ID/Secret Key go on the **`doku_gateway`** section (`wc-settings&tab=checkout&section=doku_gateway`), not `doku_checkout` - the Payments screen hides this in newer WooCommerce, use the direct URL. Never commit credentials. Plugin needs HPOS off (`wp wc hpos disable`). No refund/cancel API - refunds and post-cancel refunds are a manual step in the DOKU Back Office; WooCommerce-side refund/cancel only updates local records. Stock is held (reserved), not decremented, until payment confirms; WooCommerce's Hold stock (minutes) setting should be >= DOKU's payment expiry window. Local dev: webhooks can't reach localhost, so orders stay pending unless a public tunnel is used (untested). Host: notification URL is `https://shop.armstronger.web.id/wp/wp-json/doku/notification` (QRIS needs separate QRIS Client-ID/Shared Key/MPAN, not yet obtained).
- Still to do: variant/added popups, bottom-sheet filters, account/settings pages on the host theme, client Shop-manager user, verify order actually flips Pending -> Processing on the host (webhook vs anti-bot layer, not yet confirmed end-to-end).
- **Theme refactor done (2026-09-29): functions.php split into `includes/`, CSS is now SCSS.**
  - `functions.php` is a 6-line loader (`require_once` of `includes/*.php`). Logic lives in `includes/core-functions.php` (setup + enqueue), `cart.php`, `product.php`, `shop.php`, `checkout.php`. Frontend markup moved to template overrides (`header.php`, `footer.php`, `front-page.php`, `woocommerce/`, `page-*.php`).
  - Styles are SCSS. `style.scss` at the theme root holds the WordPress theme header + `@import`s and **no rules**; all sources are flat partials in `assets/css/_*.scss` (`_variables`, `_mixins`, `_base`, `_layout`, `_buttons`, `_header`, `_footer`, `_product-card`, `_component-utilities`, `_home`, `_shop`, `_product`, `_cart`, `_checkout`, `_account`, `_dialogs`, `_search`, `_page`, `_wc-base`). `style.css` is build output - never edit it. Follows Storefront's pattern.
  - Build: `npm run sass` (one-shot) / `npm run sass:watch`. Dart Sass. `@import` is deprecated in favour of `@use` but warnings are ignored for now.
  - Only two stylesheets are enqueued: Google Fonts and `style.css`. The per-page `assets/css/<page>.css` files and their conditional `wp_enqueue_style` blocks are gone - all rules ship in the one compiled file.
  - `node_modules/` and `*.css.map` are gitignored. `style.css` is currently still tracked (decision pending on whether to follow Storefront's gitignore-compiled-CSS).
  - **Checkout is now two columns** (`woocommerce/checkout/form-checkout.php`): left = sticky address card, "Pengiriman", "Ringkasan" sums, place-order button; right = "Pesanan" heading + `checkout/order-lines.php` cart lines. `review-order.php` keeps only the sums.
  - **Saved-address book**: `includes/address.php` (CRUD, user meta), `page-address.php`, `template-parts/address-dialog.php` (pick / add / edit / delete-confirm dialogs), `assets/js/address.js`, styles in `assets/css/_address.scss`. Billing fields render inside the picker dialog, not inline; validation errors are per-field inline with the failing label going red (`$danger`).
  - **Account dashboard (`/my-account/`, done 2026-09-30)** after `mockup-v2/account.html`: `page-my-account.php` renders the `.acct` dashboard (greeting, Pengaturan + Keluar, voucher, Pesanan/Wishlist tabs) only when logged in on the main endpoint; otherwise it loads `page.php` (logged-in only now; logged-out gets the auth shell below), so the orders / edit-address / edit-account endpoints still use the generic shell . `includes/account.php` = WC status -> mockup label map (`acme_order_status()`), `acme_wish_cards` ajax, script enqueue; `assets/js/account.js` = tabs, order search/status filter, wishlist. Orders are real WC orders (server-rendered, filtered client-side). Wishlist is still localStorage `lb-wish` (product IDs), cards fetched via ajax. Voucher box shows only if a WC coupon `ACME10` exists (created locally via wp-cli; **create it on the host too**). Pengaturan links to WC edit-account, which is now the settings page (next bullet). Styles were already in `_account.scss`.
  - **Settings (`/my-account/edit-account/`, done 2026-09-30)** after `mockup-v2/settings.html`: `page-my-account.php` loads `template-parts/account-settings.php` for that endpoint (orders / edit-address endpoints still use the generic shell). Nav panes are switched by `assets/js/settings.js` (also fills birthday selects, opens delete dialog). Info profil = one "Nama" field (`account_first_name`) + read-only email + birthday (user meta `acme_birthday`), posted to WooCommerce's own `save_account_details` handler; hidden inputs carry email/display name and `woocommerce_save_account_details_required_fields` is filtered to name + email (`includes/account.php`). Info pengiriman embeds the address book in place (same `[data-addr-list]` + `address-dialog` partial as `/address/`; `acme_address_page()` in `includes/address.php` includes the edit-account endpoint so scripts load) - no redirect. Info akun: "Hapus akun saya" -> confirm dialog -> `admin_post_acme_delete_account` (`wp_delete_user`, orders kept); button hidden for admins, so the delete flow is **untested**. Styles were already in `_account.scss`; label selectors are `.setpanes > section label` (labels sit inside a form). Not ported: password change, email edit.
  - **Auth (done 2026-09-30)** after `mockup-v2/login.html` + `register.html`: `woocommerce/myaccount/form-login.php` (login; `?register=1` = register form), `form-lost-password.php`, `lost-password-confirmation.php`, `form-reset-password.php`, all in the `.auth` card (styles `assets/css/_auth.scss`). `page-my-account.php` gives every logged-out request (any endpoint) its own bare shell, so no generic "Detail Akun"/page title shows above the form. `includes/auth.php`: `pre_option_*` filters force registration on, guest checkout off, password field on (no wp-admin setup on a fresh deploy); **checkout gate** = `template_redirect` sends logged-out `/checkout/` to `/my-account/?redirect_to=<checkout>` (order-received / order-pay exempt for the DOKU return) and the form posts `redirect` so login/register land back on checkout; register requires name + password >= 6, saves `first_name`, display name and optional birthday (`acme_save_birthday()` in `includes/account.php`, shared with settings). Login is email only (mockup says "email atau nomor HP"; WC has no phone lookup), no reCAPTCHA. WC injects its own `.show-password-input` reveal button on every password field - hidden in `.auth`; don't put `::-ms-reveal` in the same selector list (invalid pseudo drops the whole rule). Session notices set in `template_redirect` before `exit` were lost, so the checkout hint is drawn from `?redirect_to` in the template. Reset-password email flow untested (local mail not sent).
  - **Gotcha that cost an hour:** `:root` must be emitted by SCSS. Converting `var(--x)` to `$x` throughout silently drops the custom-property definitions - keep the `:root { --x: ... }` block in `_variables.scss` alongside the `$x` SCSS variables. Also re-check `html`/`body` background after any build: WooCommerce ships its own `html`/`body` background and it wins on a stale cache.

Deployment state and hosting gotchas live in [DEPLOYMENT.md](DEPLOYMENT.md). Keep it updated after any deploy change.

## Layout

```
acme-reference/   Screenshots of the live Acme site (desktop 1440 / mobile 390): home, category, product, add-to-cart flow, cart, login/signup.
mockup/            v1 – follows Acme's layout closely (user asked for this first).
mockup-v2/         v2 – redesign (user said "don't follow Acme 100%"). Current recommendation.
```

Each mockup is plain HTML + one `style.css` + one `app.js`, no build step, no framework. Open `index.html` in a browser.

### mockup/ (v1, Acme-faithful)
- `index.html` – announcement bar, header (logo, IDR, search, account, cart badge), nav (HOME / RULES PO / Q&A PO / CONTACT / ABOUT US / ALL PRODUCT), hero carousel (Acme's own banner photos in `img/`), trust block (6 points), category circles, 4 horizontal product rows (Import Fashion, Bags, Shoes & Heels, Jewerly/Watch), "view all" banner, footer with WhatsApp admins + payment logos.
- `category.html` – category circles, left filter sidebar (type, availability, price, colour, size), sort, 3-col grid of the 12 products from acme.example/products.
- `cart.html` – copy of Acme's cart screen.
- Shipping/COD popup was removed at the user's request.

### mockup-v2/ (redesign)
- Built from `build_v2.py` in the session scratchpad (not in repo) – **edit the HTML files directly now**; header/footer are duplicated across the three pages, keep them in sync.
- `index.html` – trust strip, split hero (real text + Acme photo cropped below its baked-in text), "Baru datang" grid, bento category grid, "Di bawah Rp 200rb" row.
- `category.html` – JS filtering (category, price, contextual size, colour), removable filter chips, empty state, infinite scroll, mobile bottom-sheet filters, back-to-top.
- `cart.html` – rendered from cart state; COD notice, payment logos, recommendations, fixed total bar → Checkout.
- `checkout.html` – follows Acme's checkout screenshot (address card + Ubah with name, +62 phone, cascading selects provinsi → kota → kecamatan → kode pos (live: emsifa.com/api-wilayah-indonesia + kodepos.vercel.app, both CORS-open, need internet), street, other details; J&T shipping, payment, order summary with message/voucher). Payment: row showing the chosen method → modal like Acme's (QRIS tile, "Metode lain" grid: OVO with phone field, Virtual Account with bank pick BRI/BNI/Mandiri/Permata/CIMB, Alfamart, Akulaku, card). UI only – no gateway called; user asked for Acme's methods, which goes beyond the brief's COD/bank-transfer-only, so Phase 2 needs a decision on a gateway (e.g. Midtrans/Xendit sandbox). QRIS/VA shown as text badges (no logo found on zonalogo). Shipping: "COD J&T · ongkir ditanggung pembeli" – ongkir not in the total, paid to the courier. Dummy voucher `ACME10` = 10%. "Pesan sekarang" validates the address, clears the cart and shows a confirmation with payment instructions. Nothing is sent anywhere. No map pin — user decided against a maps service.
- `product.html` – detail page, `?id=<index in P>`: gallery (DummyJSON 1-3.webp), colour/size/qty, add to cart (same cart + "Masuk keranjang" popup), wishlist, accordions, related row. Cards on every page link here.
- `login.html` / `register.html` – mockup auth: user in `lb-user`, session in `lb-session` (localStorage only). Header account icon goes to login when signed out; account greeting uses the registered name; settings "Keluar" clears the session.
- `account.html` – after Acme's account page: greeting, reseller-status pill, Pengaturan, voucher (ACME10), tabs Pesanan (search + status filter; orders saved by checkout in `lb-orders`, one sample order seeded when empty) / Wishlist (hearts saved in `lb-wish`).
- `settings.html` – "Pengaturan", reached from the account page. Left card = section nav (Info profil / Info pengiriman / Info akun / Keluar → index.html); right card = the panel. Profil: name, email (disabled), birthday day/month/year selects. Pengiriman: saved addresses in `lb-addr` (seeded with one sample), each card with edit + delete icon buttons, "+ Tambah baru" opens name / WhatsApp / alamat form. Akun: "Hapus akun saya" opens a confirm `<dialog>`; confirming clears `lb-orders`, `lb-wish`, `lb-addr`, `lb-cart` in this browser only (no server).
- Confirmation screen: QRIS shows a fake, deliberately unscannable QR; Virtual Account shows a fake 16-digit number (`88` + order digits) with copy button. Both tagged "Contoh".
- Footer: "Follow us" column with Instagram – link is `#`, real handle not found on the live site.
- `app.js` – product data (24 items, array `P`), card renderer, add-to-cart flow (variant picker `<dialog>` → "Masuk keranjang" `<dialog>` → floating cart bar), cart in `localStorage` key `lb-cart`, category filtering.
- Copy is Indonesian. Promo bar removed (user). Placeholder copy the user must confirm: "Jakarta warehouse", "24 item baru", product names.

## Assets & sources
- Product photos: DummyJSON CDN (`https://cdn.dummyjson.com/product-images/...`), hotlinked, dummy only, needs internet. loremflickr returned 401 – don't use it.
- Hero photos: downloaded from Acme's CloudFront (`img/hero-1..3.webp`).
- Payment logos: zonalogo.com SVGs, saved in `logos/`. Its `.webp` previews have a fake transparency checkerboard baked in – use the SVGs.
- Acme data (product names/prices/SKUs) was scraped from the live site on 2026-09-26.

## Working notes
- Verify UI by screenshot with `agent-browser` (see global CLAUDE.md). Use absolute Windows paths for screenshot output; relative paths fail.
- Full-page screenshots leave lazy-loaded images blank – scroll and take viewport shots to check images.
- Bash heredocs containing backticks/quotes broke several times; for larger JS/Python edits write a file first, then splice.
- The user wants per-phase stops: show results and wait for approval before moving to the next phase.
- `woocommerce.css` is enqueued **after** the theme's `style.css`, and its form rules (`.woocommerce form .form-row .input-text`, `... select`) tie on specificity, so WooCommerce wins the cascade and theme field styling silently does nothing. Set its own variables instead of writing override rules: `--wc-form-border-color`, `--wc-form-border-width`, `--wc-form-border-radius`, `--wc-form-color-background`, `--wc-form-color-text` on `:root`. Padding has no variable, so it needs one extra specificity notch (`.woocommerce-checkout .co ...`).
- When matching mockup field metrics, copy `line-height` too - the mockup inherits `1.5` (22.5px at 15px) from `body`; `line-height: normal` gives ~19px and every input comes out 4px short.
- Don't claim a style "matches the mockup" from reading the mockup's CSS alone. Measure the live page with `agent-browser eval` and compare computed values - the theme rule may be present and still losing the cascade.
- A page that renders *most* of the layout but sits in a white bordered box is WordPress's critical-error screen (`#error-page`), not a CSS bug. Check the real error first: `podman logs --tail 40 woocommerce_wp_1 | grep -i fatal`. Styling the symptom wastes time.
- `podman exec` from Git Bash mangles absolute container paths (`/var/...` -> `C:/Program Files/Git/var/...`). Prefix with `MSYS_NO_PATHCONV=1`.
- After refactors that move PHP, `grep -rn` for every function the templates call before trusting the build - the `includes/` split silently dropped `acme_card()` and only surfaced as a runtime fatal.
- WooCommerce's checkout JS replaces **every** element carrying `.woocommerce-checkout-review-order-table` with the sidebar AJAX fragment. Any custom block in that class gets overwritten - use another class (`order-lines.php` uses `.co-lines`/`.box`).
- A sticky column inside a CSS grid needs `align-items: stretch` (the default `start` shrinks the grid item to its own content, so the sticky child has zero travel) plus an inner wrapper carrying `position: sticky` - the item itself can never travel, its containing block is its own grid area.
- `overflow-x: clip` on `html`/`body` (set in `_base.scss`) — **not `hidden`**. `hidden` makes `body` the scroll container and disables `position: sticky` for its children (the header). This replaced the older workaround where `page-checkout.php` opted out with `class="co-html"` + `overflow: visible`; both the class and that override are gone.
- `page-checkout.php` is a standalone shell (no `header.php`), so it owns its own `<!doctype html>` - without it the page renders in quirks mode.
- Local dev: `/checkout/` showing "Ada beberapa masalah dengan barang di keranjang Anda" is a stale cart error in the WC session, not a template bug. Clear it by removing an item from `/cart/` and re-adding a product - `?empty-cart=1` does not work.

## Phase 2 plan (when approved)
1. Local WordPress + WooCommerce via Docker (wordpress + mysql images).
2. Storefront or Astra (free) + child theme applying the approved mockup's palette/layout.
3. 4–6 sample products across 2–3 categories with stock levels.
4. Cart + checkout with Cash on Delivery / Bank Transfer only – no real payment gateway, domain, or hosting.
5. Give the user access instructions.

## Key files

Read these directly instead of searching the repo.

| Path | What |
|---|---|
| `DEPLOYMENT.md` | Host state, cPanel/DOKU gotchas. Read before deploy work. |
| `DESIGN.md` | Palette + design rules. |
| `mockup-v2/app.js` | Product data (array `P`, 24 items), `acme`-card renderer, cart state (`lb-cart`). Source of truth for the port. |
| `mockup-v2/style.css` | All mockup CSS; the theme's `style.css` is derived from it. |
| `mockup-v2/*.html` | Reference pages: index, category, product, cart, checkout, login, register, account, settings, about, faq. Header/footer duplicated - keep in sync. |
| `woocommerce/compose.yaml` | Local podman compose (WP + MariaDB, :8080). |
| `woocommerce/theme/acme/style.scss` | SCSS entry point: theme header + `@import`s, no rules. |
| `woocommerce/theme/acme/assets/css/_*.scss` | All theme CSS source, flat partials. |
| `woocommerce/theme/acme/style.css` | Build output from `style.scss`. Never edit. |
| `woocommerce/theme/acme/functions.php` | 6-line loader for `includes/*.php`. |
| `woocommerce/theme/acme/includes/core-functions.php` | Theme setup, stylesheet/script enqueue, `body_class`. |
| `woocommerce/theme/acme/includes/product.php` | `theme_product_card()`, add-to-cart redirect, order button. |
| `woocommerce/theme/acme/includes/cart.php` | `get_cart_badge()`, `get_cartbar()`, ajax fragments. |
| `woocommerce/theme/acme/includes/shop.php` | Filters, price buckets, `is_shop_archive()`. |
| `woocommerce/theme/acme/includes/checkout.php` | Billing/shipping fields, saved-address handlers, payment tweaks. |
| `woocommerce/theme/acme/includes/address.php` | Address book CRUD (user meta), AJAX endpoints, script enqueue. |
| `woocommerce/theme/acme/woocommerce/checkout/form-checkout.php` | Two-column checkout shell (`.co-side` sticky / `.co-main`). |
| `woocommerce/theme/acme/woocommerce/checkout/order-lines.php` | Cart line items, right column. Not `.woocommerce-checkout-review-order-table`. |
| `woocommerce/theme/acme/woocommerce/checkout/review-order.php` | Sums only (Subtotal / Ongkir / Total bayar). |
| `woocommerce/theme/acme/template-parts/address-dialog.php` | Address pick / add-edit / delete-confirm `<dialog>`s. |
| `woocommerce/theme/acme/assets/js/address.js` | Address book state + AJAX. |
| `woocommerce/theme/acme/page-address.php` | `/address/` saved-address page. |
| `woocommerce/theme/acme/page-my-account.php` | `/my-account/` dashboard (logged-in, main endpoint only). |
| `woocommerce/theme/acme/includes/account.php` | Order status labels, wishlist ajax cards, account script enqueue. |
| `woocommerce/theme/acme/assets/js/account.js` | Account tabs, order filter, wishlist. |
| `woocommerce/theme/acme/template-parts/account-settings.php` | `/my-account/edit-account/` settings page (profile / shipping / delete account). |
| `woocommerce/theme/acme/assets/js/settings.js` | Settings pane switching, birthday selects, delete dialog. |
| `woocommerce/theme/acme/front-page.php` | Home: hero, product grids, bento categories. |
| `woocommerce/theme/acme/woocommerce.php` | Shop archive (filters, grid). |
| `woocommerce/theme/acme/woocommerce/content-product.php` | Product card in loops. |
| `woocommerce/theme/acme/woocommerce/content-single-product.php` | Product detail. |
| `woocommerce/theme/acme/page-cart.php`, `page-checkout.php` | Cart / checkout templates. |
| `woocommerce/theme/acme/header.php`, `footer.php` | Shell (scroll header, nav, footer). |
| `woocommerce/theme/acme/assets/js/` | Theme JS. |
| `woocommerce/seed/gen.js` | Seeds products into local WP via wp-cli. |
| `woocommerce/seed/csv.js` | Generates `woocommerce/deploy/products.csv` for host import. |
| `woocommerce/seed/*.sh` | One-off setup: pages, menu, i18n, DOKU. |
| `woocommerce/deploy/` | Host deploy: install.sh, compose.prod.yaml, Caddyfile, products.csv. |

## Agents

Never fork or spawn subagents (no Agent tool, no forks). Do all work directly in this session.
