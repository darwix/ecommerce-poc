// Generates seed.sh (wp-cli) from mockup-v2/app.js product array.
const fs = require('fs');
const src = fs.readFileSync(__dirname + '/../../mockup-v2/app.js', 'utf8');
const D = 'https://cdn.dummyjson.com/product-images/';
const CATS = { pakaian: 'Pakaian', tas: 'Tas', sepatu: 'Sepatu', aksesoris: 'Aksesoris', kecantikan: 'Kecantikan' };
const P = eval(src.slice(src.indexOf('const P = [') + 10, src.indexOf('].map(') + 1));
const q = (s) => "'" + String(s).replace(/'/g, "'\\''") + "'";
let o = 'set -e\ncd /var/www/html\nmkdir -p /tmp/img\n';
for (const [c, n] of Object.entries(CATS)) o += `wp term create product_cat ${q(n)} --slug=${c}\n`;
P.forEach(([code, name, cat, price, stock, img, , , sold], i) => {
  const url = encodeURI(D + img + '/thumbnail.webp').replace(/&/g, '%26');
  o += `curl -fsSL -o /tmp/img/${i}.webp ${q(url)}\n`;
  o += `ID=$(wp post create --post_type=product --post_status=publish --post_title=${q(name)} --porcelain)\n`;
  o += `wp post term set $ID product_cat ${cat}\n`;
  o += `wp post meta update $ID _sku ${q(code)}\nwp post meta update $ID _regular_price ${price}\nwp post meta update $ID _price ${price}\n`;
  o += `wp post meta update $ID _manage_stock yes\nwp post meta update $ID _stock ${stock}\nwp post meta update $ID _stock_status instock\nwp post meta update $ID total_sales ${sold}\n`;
  o += `wp media import /tmp/img/${i}.webp --post_id=$ID --featured_image --title=${q(name)}\n`;
});
fs.writeFileSync(__dirname + '/seed.sh', o);
console.log(P.length + ' products');
