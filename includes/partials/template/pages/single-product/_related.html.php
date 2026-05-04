<?php
// includes/partials/template/pages/single-product/_related.html.php
defined('ABSPATH') || exit;

global $tpl_engine;

$related_ids = wc_get_related_products(get_the_ID(), 8);
if (empty($related_ids)) return;

$related_products = array_filter(array_map('wc_get_product', $related_ids));
if (empty($related_products)) return;
?>
<section class="s-sp-related">
  <div class="s-container">

    <div class="s-sp-related__header">
      <h2 class="s-sp-related__title"><?= esc_html__('Outras opções', 'lucci-fresh') ?></h2>
      <div class="s-sp-related__nav">
        <button class="s-sp-related__prev js-sp-related-prev" aria-label="<?= esc_attr__('Anterior', 'lucci-fresh') ?>">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
        <div class="s-sp-related__progress js-sp-related-progress"></div>
        <button class="s-sp-related__next js-sp-related-next" aria-label="<?= esc_attr__('Próximo', 'lucci-fresh') ?>">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
    </div>

    <div class="swiper js-sp-related">
      <div class="swiper-wrapper">
        <?php foreach ($related_products as $rel) :
          $product = $rel;
        ?>
          <div class="swiper-slide">
            <?php $tpl_engine->partial('components/product-card', ['product' => $product]) ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section><!-- /.s-sp-related -->