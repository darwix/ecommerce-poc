# Run from the WordPress root on the host (cPanel Terminal / SSH), needs wp-cli.
# Idempotent: pages, menu and coupon are only created when missing.
for spec in "about:Tentang" "faq:FAQ" "terms-and-conditions:Syarat & Ketentuan" "privacy-policy:Kebijakan Privasi"; do
  slug=${spec%%:*}; title=${spec#*:}
  id=$(wp post list --post_type=page --post_status=any --name=$slug --format=ids)
  # WP ships a draft privacy-policy page: publish it instead of creating privacy-policy-2
  if [ -n "$id" ]; then wp post update $id --post_status=publish --post_title="$title"
  else wp post create --post_type=page --post_status=publish --post_title="$title" --post_name=$slug; fi
done

wp option update woocommerce_calc_shipping no
wp option update woocommerce_enable_coupons yes
wp option update blogname "Acme Store"

wp menu list --fields=name --format=csv | grep -qx Utama || {
  HOME_URL=$(wp option get home)
  wp menu create Utama
  wp menu item add-custom Utama Beranda "$HOME_URL/"
  wp menu item add-custom Utama "Semua produk" "$HOME_URL/shop/"
  wp menu item add-custom Utama Tentang "$HOME_URL/about/"
  wp menu location assign Utama primary
  wp menu location assign Utama handheld
}

wp post list --post_type=shop_coupon --post_status=any --name=acme10 --format=ids | grep -q . || \
  wp wc shop_coupon create --code=acme10 --discount_type=percent --amount=10 --user=1

wp rewrite structure '/%postname%/' --hard
wp rewrite flush
