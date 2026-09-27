# Acme POC – WooCommerce rebuild

Proof-of-concept rebuild of acme.example (Indonesian import-fashion / beauty resale store, PT. Acme Beauty Group) with a new pink + blue palette. Target stack is WordPress + WooCommerce; later deployment on Biznet Gio. Design rules live in [DESIGN.md](DESIGN.md).

## Status (2026-09-27)

- **Phase 1 - static mockup (mockup-v2): done, deployed** to https://ecommerce-poc.acme-demo.workers.dev (Cloudflare Workers static assets).
- **Phase 2 - local WooCommerce POC, Stage 1 done, awaiting review.** `woocommerce/` = podman compose (WP + MariaDB, port 8080; admin/admin) with custom theme `woocommerce/theme/acme/` (mockup-v2 style.css + PHP templates). WP 6.9.4 + WooCommerce 10.9.4 (11.x needs WP 7). 24 products seeded from `mockup-v2/app.js` via `woocommerce/seed/gen.js`. Gotchas: run wp-cli inside `woocommerce_wp_1` as uid 33 (WP's own HTTP client gets 434 from wordpress.org, so install plugins from zips fetched with curl); other containers `wp-app`/`wp-db` belong to a different project.
- Payment: DOKU (official plugin doku-payment, hosted DOKU Checkout page, sandbox). Credentials live in the DB option `woocommerce_doku_gateway_settings` (`sandbox_client_id`, `sandbox_shared_key`, `environment_payment_jokul=false`) - never commit them. Plugin needs HPOS off (`wp wc hpos disable`). Webhooks can't reach localhost, so orders stay pending unless a public tunnel is used. Installed with a manual curl + `wp plugin install /tmp/doku.zip` (see `seed/doku.sh`).
- Stage 2 done so far: Indonesian locale, About/FAQ pages, category chips, ongkir disabled, DOKU checkout redirect. Still to do: variant/added popups, bottom-sheet filters, About/FAQ/account pages, Indonesian strings, payment gateway sandbox (Midtrans/Xendit, needs keys).

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
