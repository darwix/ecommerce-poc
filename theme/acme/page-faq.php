<?php get_header(); ?>
<main id="content">
      <div class="wrap faq">
        <span class="eyebrow"><?php acme_te('Help'); ?></span>
        <h1><?php acme_te('Frequently asked questions'); ?></h1>
        <div class="qa">
          <details><summary><?php acme_te('Are all products in stock?'); ?></summary><p><?php acme_te('Yes. Products shown in the store are in our warehouse and ship within 1×24 hours. Low stock carries an "Only X left" tag.'); ?></p></details>
          <details><summary><?php acme_te('How does COD work?'); ?></summary><p><?php acme_te('Choose COD J&T shipping at checkout and pay the courier when your package arrives. Shipping is not part of the order total and is paid directly to the courier.'); ?></p></details>
          <details><summary><?php acme_te('What payment methods are available?'); ?></summary><p><?php acme_te('QRIS, OVO, Virtual Account (BRI, BNI, Mandiri, Permata, CIMB), Alfamart, Akulaku, and cards.'); ?></p></details>
          <details><summary><?php acme_te('How long does delivery take?'); ?></summary><p><?php acme_te('Orders ship within 1×24 hours of confirmation. Travel time depends on the destination city and J&T.'); ?></p></details>
          <details><summary><?php acme_te('Can I cancel or change my order?'); ?></summary><p><?php acme_te("Contact the admin on WhatsApp before your order ships. Once shipped, it can't be changed."); ?></p></details>
          <details><summary><?php acme_te("What if there's a problem with an item?"); ?></summary><p><?php acme_te('Record a video while unboxing, then send it to the admin on WhatsApp so we can help.'); ?></p></details>
        </div>
        <p class="faq-more"><?php acme_te("Didn't find your answer?"); ?> <a href="#"><?php acme_te('Chat admin'); ?></a></p>
      </div>
    </main>
<?php get_footer();
