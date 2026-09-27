cd /var/www/html
curl -fsSL -o /tmp/doku.zip https://downloads.wordpress.org/plugin/doku-payment.latest-stable.zip
wp plugin install /tmp/doku.zip --activate
wp plugin list --fields=name,status,version
wp eval 'foreach (WC()->payment_gateways()->payment_gateways() as $id => $g) echo $id . " | " . $g->get_method_title() . "\n";'
