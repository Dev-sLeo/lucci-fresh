<?php
// includes/partials/template/pages/single-product/_hero.html.php
defined('ABSPATH') || exit;
global $product, $tpl_engine;

// Dados do produto
$title      = get_the_title();
$short_desc = $product->get_short_description();
$price_html = $product->get_price_html();
$cart_url   = $product->add_to_cart_url();

// Galeria de imagens
$gallery_ids  = $product->get_gallery_image_ids();
$thumbnail_id = get_post_thumbnail_id();
$image_ids    = $thumbnail_id ? array_merge([$thumbnail_id], $gallery_ids) : $gallery_ids;
$has_gallery  = count($image_ids) > 1;
?>
<section class="s-sp-hero">

  <div class="s-sp-hero__pattern" aria-hidden="true">
    <?php $tpl_engine->svg('pattern/pattern-hero') ?>
  </div>

  <div class="s-container">
    <div class="s-sp-hero__inner">

      <!-- Galeria / Imagem principal (Swiper) -->
      <div class="s-sp-hero__gallery-wrap">
        <div class="swiper s-sp-hero__gallery js-sp-gallery">
          <div class="swiper-wrapper">
            <?php foreach ($image_ids as $i => $img_id) :
              $img_src = wp_get_attachment_image_url($img_id, 'large');
              $img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: $title;
            ?>
              <div class="swiper-slide">
                <img
                  class="s-sp-hero__image"
                  src="<?= esc_url($img_src) ?>"
                  alt="<?= esc_attr($img_alt) ?>"
                  loading="<?= $i === 0 ? 'eager' : 'lazy' ?>"
                  width="600" height="500">
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if ($has_gallery) : ?>
          <div class="s-sp-hero__gallery-controls">
            <button class="s-sp-hero__prev js-sp-gallery-prev" type="button" aria-label="<?= esc_attr__('Anterior', 'lucci-fresh') ?>">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
            <div class="s-sp-hero__progress js-sp-gallery-progress"></div>
            <button class="s-sp-hero__next js-sp-gallery-next" type="button" aria-label="<?= esc_attr__('Próximo', 'lucci-fresh') ?>">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>
        <?php endif; ?>
      </div>

      <!-- Conteúdo -->
      <div class="s-sp-hero__content">
        <h1 class="s-sp-hero__title"><?= esc_html($title) ?></h1>

        <?php if ($short_desc) : ?>
          <div class="s-sp-hero__description">
            <?= wp_kses_post($short_desc) ?>
          </div>
        <?php endif; ?>

        <div class="s-sp-hero__cta-group">
          <a href="<?= esc_url($cart_url) ?>" class="u-button u-button__white s-sp-hero__price-btn">
            <span class="s-sp-hero__price-prefix"><?= esc_html__('partir de', 'lucci-fresh') ?></span>
            <?= $price_html ?>
          </a>
          <a href="<?= esc_url($cart_url) ?>" class="u-button u-button__wood s-sp-hero__cta">
            <?= esc_html__('Fazer pedido', 'lucci-fresh') ?>
          </a>
        </div>

        <p class="s-sp-hero__discount-note">
          <?= esc_html__('Confira nossa tabela de desconto', 'lucci-fresh') ?>
        </p>
      </div>

    </div>
  </div>

</section><!-- /.s-sp-hero -->