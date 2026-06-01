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
    </div>

    <div class="s-sp-related__track">
      <button class="s-sp-related__prev js-sp-related-prev" aria-label="<?= esc_attr__('Anterior', 'lucci-fresh') ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="23" viewBox="0 0 12 23" fill="none">
          <path d="M11.0835 22.0833L0.750163 11.4167L11.0835 0.75" stroke="#FD8426" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>

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

      <button class="s-sp-related__next js-sp-related-next" aria-label="<?= esc_attr__('Próximo', 'lucci-fresh') ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="23" viewBox="0 0 12 23" fill="none">
          <path d="M0.75 22.0833L11.0833 11.4167L0.75 0.75" stroke="#FD8426" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
    </div>

  </div>
</section><!-- /.s-sp-related -->