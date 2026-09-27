cd /var/www/html
wp post delete $(wp post list --post_type=page --name=sample-page --format=ids) --force
wp menu create Utama
wp menu item add-custom Utama Beranda http://localhost:8080/
wp menu item add-custom Utama "Semua produk" http://localhost:8080/shop/
wp menu location assign Utama primary
wp menu location assign Utama handheld
wp option update blogname Acme
wp option update blogdescription "Import fashion, ready stock"
