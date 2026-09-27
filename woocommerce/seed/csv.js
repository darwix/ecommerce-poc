// Generates deploy/products.csv (WooCommerce > Products > Import) from mockup-v2/app.js.
const fs = require('fs');
const src = fs.readFileSync(__dirname + '/../../mockup-v2/app.js', 'utf8');
const D = 'https://cdn.dummyjson.com/product-images/';
const CATS = { pakaian: 'Pakaian', tas: 'Tas', sepatu: 'Sepatu', aksesoris: 'Aksesoris', kecantikan: 'Kecantikan' };
const P = eval(src.slice(src.indexOf('const P = [') + 10, src.indexOf('].map(') + 1));
const q = (s) => '"' + String(s).replace(/"/g, '""') + '"';
const head = ['SKU', 'Name', 'Published', 'In stock?', 'Stock', 'Regular price', 'Categories', 'Images'];
const rows = P.map(([code, name, cat, price, stock, img]) => [code, name, 1, 1, stock, price, CATS[cat], encodeURI(D + img + '/thumbnail.webp')]);
fs.writeFileSync(__dirname + '/../deploy/products.csv', [head, ...rows].map((r) => r.map(q).join(',')).join('\n') + '\n');
console.log(rows.length + ' rows');
