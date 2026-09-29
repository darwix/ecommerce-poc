# Acme POC – WooCommerce rebuild

Proof-of-concept rebuild of acme.example (Indonesian import-fashion / beauty resale store, PT. Acme Beauty Group) with a new pink + blue palette. Target stack is WordPress + WooCommerce; later deployment on Biznet Gio. Design rules live in [DESIGN.md](DESIGN.md).

## Status (2026-09-27)

- **Phase 1 - static mockup (mockup-v2): done, deployed** to https://ecommerce-poc.acme-demo.workers.dev (Cloudflare Workers static assets).
- **Phase 2 - WooCommerce POC deployed and DOKU sandbox live on the host.** Two environments:
  - Local: `woocommerce/` = podman compose (WP + MariaDB, port 8080; admin/admin), WP 6.9.4 + WooCommerce 10.9.4 (11.x needs WP 7). Gotchas: run wp-cli inside `woocommerce_wp_1` as uid 33 (WP's own HTTP client gets 434 from wordpress.org, so install plugins from zips fetched with curl); other containers `wp-app`/`wp-db` belong to a different project.
  - Host (real deploy check): NeoHosting shared cPanel, https://shop.armstronger.web.id/wp/ - WordPress 7.1.2 + WooCommerce 11.1.2, Acme theme active, store Live, 24 products imported and visible, DOKU Payment plugin installed with sandbox keys entered, email confirmed working (DOKU's own receipt + WooCommerce order emails). Full details, gotchas (OPcache fix, anti-bot layer, wrong-IP mistake) and open items in [DEPLOYMENT.md](DEPLOYMENT.md) - **read it before continuing deploy work**.
- Custom theme `woocommerce/theme/acme/` (mockup-v2 style.css + PHP templates), 24 products seeded from `mockup-v2/app.js` via `woocommerce/seed/gen.js` (local) / `woocommerce/seed/csv.js` -> `woocommerce/deploy/products.csv` (host import).
- Payment: DOKU (official plugin `doku-payment`, hosted DOKU Checkout page, sandbox). Client ID/Secret Key go on the **`doku_gateway`** section (`wc-settings&tab=checkout&section=doku_gateway`), not `doku_checkout` - the Payments screen hides this in newer WooCommerce, use the direct URL. Never commit credentials. Plugin needs HPOS off (`wp wc hpos disable`). No refund/cancel API - refunds and post-cancel refunds are a manual step in the DOKU Back Office; WooCommerce-side refund/cancel only updates local records. Stock is held (reserved), not decremented, until payment confirms; WooCommerce's Hold stock (minutes) setting should be >= DOKU's payment expiry window. Local dev: webhooks can't reach localhost, so orders stay pending unless a public tunnel is used (untested). Host: notification URL is `https://shop.armstronger.web.id/wp/wp-json/doku/notification` (QRIS needs separate QRIS Client-ID/Shared Key/MPAN, not yet obtained).
- Still to do: variant/added popups, bottom-sheet filters, account/settings pages on the host theme, client Shop-manager user, verify order actually flips Pending -> Processing on the host (webhook vs anti-bot layer, not yet confirmed end-to-end).
- Deferred: split `theme/acme/style.css` per page. WordPress requires `style.css` at the theme root (theme header), so base/tokens/header/footer stay there and page CSS moves to `assets/css/<page>.css` enqueued per template. Only worth doing once a page's CSS is big and page-specific - checkout (~160 lines) is the only candidate today, and the whole file is ~700 lines, so one cached request still wins.

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
| `woocommerce/theme/acme/style.css` | All theme CSS. |
| `woocommerce/theme/acme/functions.php` | Hooks, `acme_card()`, WooCommerce hook removals, cart/redirect filters. |
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
