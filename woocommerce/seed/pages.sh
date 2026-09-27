cd /var/www/html
for spec in "about:Tentang" "faq:FAQ"; do
  slug=${spec%%:*}; title=${spec#*:}
  wp post list --post_type=page --name=$slug --format=ids | grep -q . || wp post create --post_type=page --post_status=publish --post_title="$title" --post_name=$slug
done
# Ongkir is paid to the courier (COD J&T), so nothing is added to the total.
wp option update woocommerce_calc_shipping no
wp option update woocommerce_enable_coupons yes
wp rewrite flush
