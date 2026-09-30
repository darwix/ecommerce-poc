cd /var/www/html
HOME_URL=$(wp option get home)
wp post delete $(wp post list --post_type=page --name=sample-page --format=ids) --force
wp menu create Utama
wp menu item add-custom Utama Beranda "$HOME_URL/"
wp menu item add-custom Utama "Semua produk" "$HOME_URL/shop/"
wp menu location assign Utama primary
wp menu location assign Utama handheld
wp option update blogname Acme
wp option update blogdescription "Import fashion, ready stock"
