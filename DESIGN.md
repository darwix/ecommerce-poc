# Design – Acme POC

Design system and decisions for the mockups. Pink is the primary colour; blue is a supporting accent, not 50/50.

## Colour

| Token | Value | Use |
|---|---|---|
| `--pink` | `#F4A6C1` | Primary buttons, promo bar, brand fills |
| `--pink-h` | `#D96D96` | Primary hover, logo, cart badge, low-stock text, links needing emphasis |
| `--blue` | `#A8D5E8` | Secondary accent: nav underline on hover, secondary/outline buttons hover, filter chips |
| `--blue-h` | `#6FB3D2` | Active nav underline, secondary button border, focus ring, icon accents |
| `--bg` | `#FFF8FA` | Page background |
| `--text` | `#2E2A3D` | Text; dark UI (floating cart bar, selected options) |
| `--mint` | `#B8E0D2` | Success (added-to-cart check), in-stock |
| `--soft` | `#fdeef3` | Soft panels: hero panel, footer, dialog product box |
| `--muted` | `#6f6a7d` | Secondary text (v2) |
| `--line` | `#f0dbe3` | Borders/dividers |

Rules:
- Primary CTA = pink fill. Secondary = blue outline (`.btn-sec` / `.btn-line`). Tertiary = text link with blue underline (`.link`).
- Never put blue as small text on white – too low contrast. Use it for underlines, borders, fills.
- Shadows are tinted pink/navy, never plain black.

## Type
- Body/UI: **Outfit** (300–700). Replaced Poppins during the redesign audit.
- Display/logo: **Playfair Display** – logo "LB", v2 hero headline.
- Prices and counts use `font-variant-numeric: tabular-nums`.
- Headlines use `text-wrap: balance`.

## Components (v2)
- **Product card**: 4:5 image, radius 14px, image zooms on hover. Stock tag ("Sisa N") only when stock ≤ 5. Heart (wishlist) top-right. "+ Keranjang" button slides up on hover (always visible on touch). Below: selectable colour dots, readable name, SKU in small muted text, price.
- **Add-to-cart flow** (mirrors Acme's):
  1. Variant picker dialog – product summary, colour + size chips (size required when the product has sizes), qty stepper capped at stock minus what's already in cart.
  2. "Masuk keranjang" dialog – line summary, "Lihat keranjang" + "Checkout", 4 recommendations from other categories.
  3. Floating cart bar – bottom centre, navy, "N item di keranjang · Rp total", pink cart circle with count. Hidden on the cart page.
- **Filters**: sidebar on desktop, bottom sheet on ≤1000px. Size filter only for categories with sizes. Active filters as removable chips + "Hapus semua". Empty state with reset button.
- **Trust strip**: one slim row under the nav (ready stock · COD J&T · since 2018 · reseller) instead of Acme's large block.
- **Floating WhatsApp** "Chat admin" pill (icon-only circle on mobile); moves up when the cart bar or total bar is showing.
- **Checkout**: minimal header (back arrow + logo, no nav) on white. Two columns (form / sticky summary), one column ≤860px. Payment = one row (logo · name · chevron) opening a picker modal: QRIS full-width tile, other methods in a 2-col tile grid, selected tile gets a 2px navy border, method details in a soft panel above "Konfirmasi". Message + voucher use `<details>`. Placeholder bank account BCA 123 456 7890 must be replaced.
- **Settings page**: two cards on the page background — narrow section nav (active item = soft panel fill + 600 weight), wide panel. Panels stack to one column ≤760px. Danger action = pink-outline pill (`.danger`), never filled red.
- Native `<dialog>` for modals; focus ring `2px var(--blue-h)`; skip link; reduced-motion respected.

## Decisions & why
- **v1 follows Acme's structure** because the user first required it; **v2 departs** after the user asked what I'd do differently.
- v2 changes vs Acme:
  - Products appear right after a shorter (~60vh) hero; Acme buries them under a full-height banner + trust block.
  - Removed "ESTIMASI BARANG PO" (Rp 5,000,000 fake product repeated in every row) – PO info belongs on product page/checkout.
  - Split SKU from product name ("BKK2123C READY STOCK" → readable name + small code); "LOW STOCK" only when it's true.
  - Fewer, varied homepage sections (grid, bento categories, price-band row) instead of four identical rows.
  - COD/no-refund rule shown next to checkout instead of an on-load popup.
  - Indonesian copy throughout; spelling fixed (Jewerly → Aksesoris/Jewelry, Parfumes → Parfum).
- Acme's hero photos have "New Arrival" baked into the image, so overlays/scrims can't fix legibility. v1 shows them as-is; v2 uses real HTML text on a panel and crops the photo below the baked text.
- Hero carousel in v1 uses CSS scroll-snap (no carousel library).

## Not done yet
- Product detail page, login/signup (references exist in `acme-reference/`).
- Real category circle photos in v1; links to `#` (Rules PO, Contact, help pages).
- Phase 2 WooCommerce child theme should port the v2 tokens and components above.
