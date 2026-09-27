set -e
cd /var/www/html
mkdir -p /tmp/img
wp term create product_cat 'Pakaian' --slug=pakaian
wp term create product_cat 'Tas' --slug=tas
wp term create product_cat 'Sepatu' --slug=sepatu
wp term create product_cat 'Aksesoris' --slug=aksesoris
wp term create product_cat 'Kecantikan' --slug=kecantikan
curl -fsSL -o /tmp/img/0.webp 'https://cdn.dummyjson.com/product-images/tops/girl-summer-dress/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Maxi dress motif daun' --porcelain)
wp post term set $ID product_cat pakaian
wp post meta update $ID _sku 'BKB-005'
wp post meta update $ID _regular_price 159000
wp post meta update $ID _price 159000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 12
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 84
wp media import /tmp/img/0.webp --post_id=$ID --featured_image --title='Maxi dress motif daun'
curl -fsSL -o /tmp/img/1.webp 'https://cdn.dummyjson.com/product-images/womens-bags/prada-women-bag/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Tote bag kulit biru muda' --porcelain)
wp post term set $ID product_cat tas
wp post meta update $ID _sku 'YQA-024'
wp post meta update $ID _regular_price 319000
wp post meta update $ID _price 319000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 3
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 57
wp media import /tmp/img/1.webp --post_id=$ID --featured_image --title='Tote bag kulit biru muda'
curl -fsSL -o /tmp/img/2.webp 'https://cdn.dummyjson.com/product-images/womens-shoes/golden-shoes-woman/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Pump rose gold' --porcelain)
wp post term set $ID product_cat sepatu
wp post meta update $ID _sku 'BRK-003'
wp post meta update $ID _regular_price 389000
wp post meta update $ID _price 389000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 7
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 41
wp media import /tmp/img/2.webp --post_id=$ID --featured_image --title='Pump rose gold'
curl -fsSL -o /tmp/img/3.webp 'https://cdn.dummyjson.com/product-images/tops/tartan-dress/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Dress tartan pita hitam' --porcelain)
wp post term set $ID product_cat pakaian
wp post meta update $ID _sku 'BKC-001'
wp post meta update $ID _regular_price 215000
wp post meta update $ID _price 215000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 2
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 96
wp media import /tmp/img/3.webp --post_id=$ID --featured_image --title='Dress tartan pita hitam'
curl -fsSL -o /tmp/img/4.webp 'https://cdn.dummyjson.com/product-images/fragrances/dior-j'\''adore/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Body serum wangi bouquet' --porcelain)
wp post term set $ID product_cat kecantikan
wp post meta update $ID _sku 'SRM-001'
wp post meta update $ID _regular_price 129000
wp post meta update $ID _price 129000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 18
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 212
wp media import /tmp/img/4.webp --post_id=$ID --featured_image --title='Body serum wangi bouquet'
curl -fsSL -o /tmp/img/5.webp 'https://cdn.dummyjson.com/product-images/womens-jewellery/green-crystal-earring/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Anting kristal hijau' --porcelain)
wp post term set $ID product_cat aksesoris
wp post meta update $ID _sku 'CST-009'
wp post meta update $ID _regular_price 159000
wp post meta update $ID _price 159000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 9
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 33
wp media import /tmp/img/5.webp --post_id=$ID --featured_image --title='Anting kristal hijau'
curl -fsSL -o /tmp/img/6.webp 'https://cdn.dummyjson.com/product-images/tops/gray-dress/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Dress abu kancing depan' --porcelain)
wp post term set $ID product_cat pakaian
wp post meta update $ID _sku 'BKK-2513B'
wp post meta update $ID _regular_price 210000
wp post meta update $ID _price 210000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 6
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 62
wp media import /tmp/img/6.webp --post_id=$ID --featured_image --title='Dress abu kancing depan'
curl -fsSL -o /tmp/img/7.webp 'https://cdn.dummyjson.com/product-images/womens-bags/heshe-women'\''s-leather-bag/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Hobo bag kulit coklat' --porcelain)
wp post term set $ID product_cat tas
wp post meta update $ID _sku 'YQA-021'
wp post meta update $ID _regular_price 315000
wp post meta update $ID _price 315000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 11
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 48
wp media import /tmp/img/7.webp --post_id=$ID --featured_image --title='Hobo bag kulit coklat'
curl -fsSL -o /tmp/img/8.webp 'https://cdn.dummyjson.com/product-images/tops/short-frock/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Mini dress tiered hijau' --porcelain)
wp post term set $ID product_cat pakaian
wp post meta update $ID _sku 'BKC-010'
wp post meta update $ID _regular_price 259000
wp post meta update $ID _price 259000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 5
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 71
wp media import /tmp/img/8.webp --post_id=$ID --featured_image --title='Mini dress tiered hijau'
curl -fsSL -o /tmp/img/9.webp 'https://cdn.dummyjson.com/product-images/womens-shoes/red-shoes/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Stiletto merah' --porcelain)
wp post term set $ID product_cat sepatu
wp post meta update $ID _sku 'BDS-001'
wp post meta update $ID _regular_price 389000
wp post meta update $ID _price 389000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 4
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 29
wp media import /tmp/img/9.webp --post_id=$ID --featured_image --title='Stiletto merah'
curl -fsSL -o /tmp/img/10.webp 'https://cdn.dummyjson.com/product-images/mobile-accessories/iphone-12-silicone-case-with-magsafe-plum/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Case iPhone ungu plum' --porcelain)
wp post term set $ID product_cat aksesoris
wp post meta update $ID _sku 'RPL-002'
wp post meta update $ID _regular_price 169000
wp post meta update $ID _price 169000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 14
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 118
wp media import /tmp/img/10.webp --post_id=$ID --featured_image --title='Case iPhone ungu plum'
curl -fsSL -o /tmp/img/11.webp 'https://cdn.dummyjson.com/product-images/tops/blue-frock/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Frock biru motif bulat' --porcelain)
wp post term set $ID product_cat pakaian
wp post meta update $ID _sku 'BKB-006'
wp post meta update $ID _regular_price 165000
wp post meta update $ID _price 165000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 10
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 55
wp media import /tmp/img/11.webp --post_id=$ID --featured_image --title='Frock biru motif bulat'
curl -fsSL -o /tmp/img/12.webp 'https://cdn.dummyjson.com/product-images/womens-bags/women-handbag-black/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Handbag hitam struktur' --porcelain)
wp post term set $ID product_cat tas
wp post meta update $ID _sku 'YQA-019'
wp post meta update $ID _regular_price 305000
wp post meta update $ID _price 305000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 8
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 39
wp media import /tmp/img/12.webp --post_id=$ID --featured_image --title='Handbag hitam struktur'
curl -fsSL -o /tmp/img/13.webp 'https://cdn.dummyjson.com/product-images/womens-shoes/calvin-klein-heel-shoes/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Pump hitam klasik' --porcelain)
wp post term set $ID product_cat sepatu
wp post meta update $ID _sku 'BRK-002'
wp post meta update $ID _regular_price 389000
wp post meta update $ID _price 389000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 1
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 88
wp media import /tmp/img/13.webp --post_id=$ID --featured_image --title='Pump hitam klasik'
curl -fsSL -o /tmp/img/14.webp 'https://cdn.dummyjson.com/product-images/fragrances/gucci-bloom-eau-de/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Parfum floral bloom' --porcelain)
wp post term set $ID product_cat kecantikan
wp post meta update $ID _sku 'SPR-002'
wp post meta update $ID _regular_price 245000
wp post meta update $ID _price 245000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 6
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 64
wp media import /tmp/img/14.webp --post_id=$ID --featured_image --title='Parfum floral bloom'
curl -fsSL -o /tmp/img/15.webp 'https://cdn.dummyjson.com/product-images/womens-dresses/black-women'\''s-gown/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Gaun hitam panjang' --porcelain)
wp post term set $ID product_cat pakaian
wp post meta update $ID _sku 'BKC-008'
wp post meta update $ID _regular_price 259000
wp post meta update $ID _price 259000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 7
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 37
wp media import /tmp/img/15.webp --post_id=$ID --featured_image --title='Gaun hitam panjang'
curl -fsSL -o /tmp/img/16.webp 'https://cdn.dummyjson.com/product-images/womens-jewellery/tropical-earring/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Anting daun tropis' --porcelain)
wp post term set $ID product_cat aksesoris
wp post meta update $ID _sku 'CST-007'
wp post meta update $ID _regular_price 149000
wp post meta update $ID _price 149000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 12
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 45
wp media import /tmp/img/16.webp --post_id=$ID --featured_image --title='Anting daun tropis'
curl -fsSL -o /tmp/img/17.webp 'https://cdn.dummyjson.com/product-images/womens-bags/white-faux-leather-backpack/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Ransel kulit putih' --porcelain)
wp post term set $ID product_cat tas
wp post meta update $ID _sku 'YQA-004'
wp post meta update $ID _regular_price 219000
wp post meta update $ID _price 219000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 15
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 52
wp media import /tmp/img/17.webp --post_id=$ID --featured_image --title='Ransel kulit putih'
curl -fsSL -o /tmp/img/18.webp 'https://cdn.dummyjson.com/product-images/womens-dresses/dress-pea/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Dress polkadot strapless' --porcelain)
wp post term set $ID product_cat pakaian
wp post meta update $ID _sku 'BKC-005'
wp post meta update $ID _regular_price 215000
wp post meta update $ID _price 215000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 9
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 76
wp media import /tmp/img/18.webp --post_id=$ID --featured_image --title='Dress polkadot strapless'
curl -fsSL -o /tmp/img/19.webp 'https://cdn.dummyjson.com/product-images/womens-bags/blue-women'\''s-handbag/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Tote bag navy' --porcelain)
wp post term set $ID product_cat tas
wp post meta update $ID _sku 'SPUA-017'
wp post meta update $ID _regular_price 255000
wp post meta update $ID _price 255000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 4
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 43
wp media import /tmp/img/19.webp --post_id=$ID --featured_image --title='Tote bag navy'
curl -fsSL -o /tmp/img/20.webp 'https://cdn.dummyjson.com/product-images/womens-shoes/black-%26-brown-slipper/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Sandal tali hitam' --porcelain)
wp post term set $ID product_cat sepatu
wp post meta update $ID _sku 'BRK-004'
wp post meta update $ID _regular_price 199000
wp post meta update $ID _price 199000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 13
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 58
wp media import /tmp/img/20.webp --post_id=$ID --featured_image --title='Sandal tali hitam'
curl -fsSL -o /tmp/img/21.webp 'https://cdn.dummyjson.com/product-images/womens-jewellery/green-oval-earring/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Anting oval kotak-kotak' --porcelain)
wp post term set $ID product_cat aksesoris
wp post meta update $ID _sku 'CST-010'
wp post meta update $ID _regular_price 95000
wp post meta update $ID _price 95000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 20
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 27
wp media import /tmp/img/21.webp --post_id=$ID --featured_image --title='Anting oval kotak-kotak'
curl -fsSL -o /tmp/img/22.webp 'https://cdn.dummyjson.com/product-images/fragrances/calvin-klein-ck-one/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Parfum unisex fresh' --porcelain)
wp post term set $ID product_cat kecantikan
wp post meta update $ID _sku 'SPR-003'
wp post meta update $ID _regular_price 189000
wp post meta update $ID _price 189000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 16
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 93
wp media import /tmp/img/22.webp --post_id=$ID --featured_image --title='Parfum unisex fresh'
curl -fsSL -o /tmp/img/23.webp 'https://cdn.dummyjson.com/product-images/womens-dresses/corset-leather-with-skirt/thumbnail.webp'
ID=$(wp post create --post_type=product --post_status=publish --post_title='Setelan korset + rok' --porcelain)
wp post term set $ID product_cat pakaian
wp post meta update $ID _sku 'BKK-2123C'
wp post meta update $ID _regular_price 219000
wp post meta update $ID _price 219000
wp post meta update $ID _manage_stock yes
wp post meta update $ID _stock 3
wp post meta update $ID _stock_status instock
wp post meta update $ID total_sales 34
wp media import /tmp/img/23.webp --post_id=$ID --featured_image --title='Setelan korset + rok'
