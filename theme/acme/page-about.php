<?php get_header(); ?>
<main id="content">
      <div class="wrap">
        <section class="hero" aria-label="Tentang Acme">
          <div class="hero-copy">
            <span class="eyebrow">Tentang Acme</span>
            <h1>Fashion import pilihan, langsung dari gudang kami.</h1>
            <p>
              Acme Beauty dijalankan PT. Acme Beauty Group. Kami memilih
              pakaian, tas, sepatu, dan produk kecantikan import, lalu
              mengirimnya dari stok yang benar-benar ada.
            </p>
            <div class="cta"><a class="btn" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Lihat produk</a></div>
          </div>
          <div class="hero-img">
            <img src="<?php echo esc_url(get_theme_file_uri('assets/img/about-1.webp')); ?>" alt="Koleksi fashion import di toko Acme" />
          </div>
        </section>
        <section class="split">
          <div>
            <span class="eyebrow">Cara kami bekerja</span>
            <h2>Belanja tanpa tebak-tebakan.</h2>
            <div class="pts">
              <div><h3>Ready stock</h3><p>Yang tampil di toko ada di gudang, dikirim dalam 1×24 jam.</p></div>
              <div><h3>COD via J&amp;T</h3><p>Bayar di tempat saat paket sampai. Ongkir dibayar ke kurir.</p></div>
              <div><h3>Pilihan dikurasi</h3><p>Koleksi baru datang tiap minggu, jumlahnya kami batasi supaya tetap rapi.</p></div>
            </div>
          </div>
          <img src="<?php echo esc_url(get_theme_file_uri('assets/img/about-2.webp')); ?>" alt="Tas dan sepatu import pilihan Acme" loading="lazy" />
        </section>
        <section class="band">
          <div>
            <h2>Ada pertanyaan soal pesanan?</h2>
            <p>Chat admin kami lewat WhatsApp, atau langsung lihat koleksi terbaru.</p>
          </div>
          <div class="acts">
            <a class="btn" href="#">Chat admin</a>
            <a class="btn-sec" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Semua produk</a>
          </div>
        </section>
      </div>
    </main>
<?php get_footer();
