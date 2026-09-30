<?php get_header(); ?>
<main id="content">
      <div class="wrap">
        <section class="hero" aria-label="<?php echo esc_attr(acme_t('About Acme')); ?>">
          <div class="hero-copy">
            <span class="eyebrow"><?php acme_te('About Acme'); ?></span>
            <h1><?php acme_te('Curated imported fashion, straight from our warehouse.'); ?></h1>
            <p>
              <?php acme_te('Acme Store is run by PT. Acme Retail Group. We select imported clothing, bags, shoes, and beauty products, then ship them from stock that is really on hand.'); ?>
            </p>
            <div class="cta"><a class="btn" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php acme_te('View products'); ?></a></div>
          </div>
          <div class="hero-img">
            <img src="<?php echo esc_url(get_theme_file_uri('assets/img/about-1.webp')); ?>" alt="<?php acme_te('Imported fashion collection at the Acme store'); ?>" />
          </div>
        </section>
        <section class="split">
          <div>
            <span class="eyebrow"><?php acme_te('How we work'); ?></span>
            <h2><?php acme_te('Shop without guesswork.'); ?></h2>
            <div class="pts">
              <div><h3>Ready stock</h3><p><?php acme_te('What you see in the store is in the warehouse and ships within 1×24 hours.'); ?></p></div>
              <div><h3>COD via J&amp;T</h3><p><?php acme_te('Pay on delivery when your package arrives. Shipping is paid to the courier.'); ?></p></div>
              <div><h3><?php acme_te('Curated selection'); ?></h3><p><?php acme_te('New collections arrive weekly, in limited numbers to keep things tidy.'); ?></p></div>
            </div>
          </div>
          <img src="<?php echo esc_url(get_theme_file_uri('assets/img/about-2.webp')); ?>" alt="<?php acme_te('Imported bags and shoes picked by Acme'); ?>" loading="lazy" />
        </section>
        <section class="band">
          <div>
            <h2><?php acme_te('Questions about an order?'); ?></h2>
            <p><?php acme_te('Message our admin on WhatsApp, or browse the latest collection.'); ?></p>
          </div>
          <div class="acts">
            <a class="btn" href="#"><?php acme_te('Chat admin'); ?></a>
            <a class="btn-sec" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php acme_te('All products'); ?></a>
          </div>
        </section>
      </div>
    </main>
<?php get_footer();
