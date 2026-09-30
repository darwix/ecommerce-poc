# Test plan: Acme WooCommerce POC

**Env:** local podman (WP 6.9.4 / WC 10.9.4, `:8080`) is the main target. Host (`shop.armstronger.web.id/wp/`) gets a smoke pass only.
**Devices:** desktop 1440 and mobile 390.
**Data:** 24 products. Product 10 is variable (Hitam sold out in all sizes). Coupon `ACME10`. One admin user, one customer user.
**Method:** manual plus `agent-browser` screenshots. UI comparisons use `mockup-v2` as the reference. Priority: P1 blocks release, P2 should fix, P3 polish.

## 1. Catalog and browse
| ID | Case | Expected | P |
|---|---|---|---|
| CAT-1 | Home loads | Hero, grids and bento categories render, no PHP errors | P1 |
| CAT-2 | Shop filters (category, price bucket, size, colour) | Grid narrows, chips removable, empty state shows | P2 |
| CAT-3 | Sort and pagination | Order correct, no duplicates | P2 |
| CAT-4 | Search modal | Opens, chip or Enter goes to `/shop/?acme_q=`, results match | P2 |
| CAT-5 | "Baru dilihat" | Fills after viewing 4 products, empty with no history | P3 |

## 2. Product page and quick-add
| ID | Case | Expected | P |
|---|---|---|---|
| PDP-1 | Simple product: qty +/- | Respects min and max | P1 |
| PDP-2 | Qty over stock, or already in cart | Notice shown above form, cart unchanged | P1 |
| PDP-3 | Variable product with no option picked | "Pilih <attr> dulu", add blocked | P1 |
| PDP-4 | Simpan button | Beside add-to-cart, toggles heart, persists in `ac-wish` | P2 |
| QA-1 | Card "+ Keranjang" | Dialog opens (does not add directly), then "Masuk keranjang" popup | P1 |
| QA-2 | Product 10, Hitam | Sold-out pills disabled, invalid combo resets selection | P1 |
| QA-3 | Sold-out product | "Stok habis", button and qty disabled | P1 |
| QA-4 | Stock already fully in cart | Button disabled, hint correct | P2 |
| QA-5 | Quick-add on shop, home, wishlist and search cards | Same behaviour on all four | P2 |

## 3. Cart
| ID | Case | Expected | P |
|---|---|---|---|
| CRT-1 | Change qty, remove item | Totals and badge update | P1 |
| CRT-2 | Cart badge and cartbar fragments | Update without reload | P2 |
| CRT-3 | Empty cart | Empty state, checkout unreachable | P2 |

## 4. Auth
| ID | Case | Expected | P |
|---|---|---|---|
| AUT-1 | Register with valid data | Account created, logged in, name saved | P1 |
| AUT-2 | Register with password under 8 characters | Rejected with a per-field error | P1 |
| AUT-3 | Register with missing name or duplicate email | Rejected | P1 |
| AUT-4 | Login with wrong password | Error shown, no user enumeration | P1 |
| AUT-5 | Logged-out `/checkout/` | Redirect to `/my-account/?redirect_to=...`, returns to checkout after login | P1 |
| AUT-6 | Order-received and order-pay while logged out | Not redirected (DOKU return) | P1 |
| AUT-7 | Lost password and reset | Email flow works (needs mail on host, untested locally) | P2 |

## 5. Checkout and address book
| ID | Case | Expected | P |
|---|---|---|---|
| CO-1 | Two-column layout, sticky left column | Sticky travels, no quirks-mode render | P2 |
| CO-2 | Address picker: add, edit, delete, select | Persists in user meta, per-field inline errors go red | P1 |
| CO-3 | Place order with empty or invalid address | Blocked, failing label goes red | P1 |
| CO-4 | Sums (subtotal, ongkir, total) | Correct, not overwritten by AJAX fragment | P1 |
| CO-5 | Stale cart error banner | Clears after remove and re-add | P3 |
| CO-6 | T&C link under button | Opens `/terms-and-conditions/` | P3 |

## 6. Payment (DOKU sandbox, host only)
| ID | Case | Expected | P |
|---|---|---|---|
| PAY-1 | Place order | Redirects to DOKU hosted checkout | P1 |
| PAY-2 | Pay in sandbox | Webhook fires, order Pending -> Processing (**not yet confirmed**) | P1 |
| PAY-3 | Abandon or expire | Stock hold released, order cancelled | P1 |
| PAY-4 | Hold-stock minutes vs DOKU expiry | Hold >= expiry | P2 |
| PAY-5 | Order and DOKU emails | Both arrive | P2 |
| PAY-6 | Credentials in repo | None committed | P1 |

## 7. Account
| ID | Case | Expected | P |
|---|---|---|---|
| ACC-1 | Dashboard (logged in) | Greeting, tabs, real orders with correct status labels | P1 |
| ACC-2 | Order search and status filter | Filters client-side | P2 |
| ACC-3 | Wishlist tab | Cards load, un-heart removes card, empty state spans grid | P2 |
| ACC-4 | Voucher box | Shows only when coupon `ACME10` exists (create on host) | P3 |
| ACC-5 | Settings: edit name and birthday | Saved, email read-only | P1 |
| ACC-6 | Settings: address book embedded | Same behaviour as `/address/` | P2 |
| ACC-7 | Delete account (customer) | Confirm dialog, user removed, orders kept (**untested**) | P1 |
| ACC-8 | Admin sees no delete button | Hidden | P2 |

## 8. Security and permissions
| ID | Case | Expected | P |
|---|---|---|---|
| SEC-1 | XSS in product name, order notes, address fields | Escaped in quick-add, added popup, wishlist and address cards | P1 |
| SEC-2 | AJAX endpoints (`acme_quick_add`, address CRUD, `acme_delete_account`) without login or nonce | Rejected | P1 |
| SEC-3 | Address CRUD on another user's ID | Rejected | P1 |
| SEC-4 | Registration forced on, guest checkout off | Holds on a fresh DB | P2 |

## 9. Cross-cutting
| ID | Case | Expected | P |
|---|---|---|---|
| X-1 | Mobile 390 on every page | No horizontal scroll, header sticky (`overflow-x: clip`) | P1 |
| X-2 | Legal pages | Both render (need WP pages with those slugs, create on host) | P2 |
| X-3 | Stale JS cache after deploy | Hard refresh shows new `?ver=` behaviour | P3 |
| X-4 | Console and PHP log | No errors: `podman logs woocommerce_wp_1 \| grep -i fatal` | P1 |
| X-5 | Visual match vs `mockup-v2` (home, shop, PDP, cart, checkout, account, settings, auth) | Screenshot diff within tolerance | P2 |

## Known gaps
- Untested: mobile layouts, variable-product PDP layout, the host quick-add (the host has no variable product yet), and reset-password email.
- Local webhooks can't reach localhost, so PAY-2 is host-only.
- QRIS needs its own credentials, which are not obtained yet.

## Exit criteria
All P1 cases pass on local. PAY-1 to PAY-3 pass on the host. No console or PHP errors.
