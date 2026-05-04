<?php
// includes/partials/components/_benefits.html.php
global $tpl_engine;

$block  = get_field('resposabilidade');
$titulo = $block['title'] ?? __('Responsabilidade & qualidade', 'lucci-fresh');
$cards  = $block['cards'] ?? [];
?>
<section class="s-benefits">
  <div class="s-container">
    <h2 class="s-benefits__title"><?= esc_html($titulo) ?></h2>

    <?php if (!empty($cards)) : ?>
      <div class="s-benefits__slider swiper js-benefits-slider">
        <div class="s-benefits__list swiper-wrapper">
          <?php foreach ($cards as $card) :
            $icon  = $card['icon']  ?? null;
            $texto = $card['title'] ?? '';
          ?>
            <div class="swiper-slide">
              <div class="c-benefit-card">
                <div class="c-benefit-card__icon-wrap">
                  <?php if ($icon) : ?>
                    <?= render_media_image($icon, 'full', ['class' => 'c-benefit-card__icon']) ?>
                  <?php endif; ?>
                </div>
                <p class="c-benefit-card__text"><?= esc_html($texto) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="s-benefits__scrollbar js-benefits-scrollbar"></div>
      </div>
    <?php endif; ?>
  </div>
</section>