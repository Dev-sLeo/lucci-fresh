<?php
// includes/partials/template/global/_hero.html.php
global $tpl_engine;

$slides = get_field('hero') ?: [];

?>
<section class="s-hero">
  <div class="s-hero__pattern" aria-hidden="true">
    <?php $tpl_engine->svg('pattern/pattern-hero') ?>
  </div>

  <div class="s-hero__slider-wrap">
    <div class="s-hero__slider swiper">
      <div class="swiper-wrapper">

        <?php foreach ($slides as $slide) :
          $titulo    = $slide['title']       ?? '';
          $descricao = $slide['description'] ?? '';
          $buttons   = $slide['buttons']     ?? [];
          // O grupo de imagens tem name="" no ACF — sub-fields ficam em $slide['']
          $imagens  = isset($slide['imagens']) && is_array($slide['imagens']) ? $slide['imagens'] : $slide;
          $img_desk = $imagens['desktop'] ?? null;
          $img_mob  = $imagens['mobile']  ?? null;
        ?>
          <div class="swiper-slide">
            <div class="s-hero__inner">

              <div class="s-hero__content">
                <div class="s-hero__text">
                  <?php if ($titulo) : ?>
                    <h1 class="s-hero__title"><?= esc_html($titulo) ?></h1>
                  <?php endif; ?>
                  <?php if ($descricao) : ?>
                    <div class="s-hero__description"><?= wp_kses_post($descricao) ?></div>
                  <?php endif; ?>
                </div>

                <?php if (!empty($buttons)) : ?>
                  <div class="s-hero__cta">
                    <?php foreach ($buttons as $i => $btn_row) :
                      $btn    = $btn_row['button'] ?? [];
                      $url    = $btn['url']    ?? '#';
                      $texto  = $btn['title']  ?? '';
                      $target = $btn['target'] ?? '_self';
                      if (!$texto) continue;
                      $variant = $i === 0 ? 'u-button__white' : 'u-button__wood';
                    ?>
                      <a href="<?= esc_url($url) ?>"
                        class="u-button <?= esc_attr($variant) ?> s-hero__btn"
                        target="<?= esc_attr($target) ?>">
                        <?= esc_html($texto) ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="s-hero__media">
                <div class="s-hero__product">
                  <?php if ($img_desk) : ?>
                    <picture>
                      <?php if ($img_mob) : ?>
                        <source srcset="<?= esc_url($img_mob['url']) ?>" media="(max-width: 719px)">
                      <?php endif; ?>
                      <img
                        class="s-hero__image"
                        src="<?= esc_url($img_desk['url']) ?>"
                        alt="<?= esc_attr($img_desk['alt'] ?? $titulo) ?>"
                        width="<?= esc_attr($img_desk['width'] ?? '') ?>"
                        height="<?= esc_attr($img_desk['height'] ?? '') ?>">
                    </picture>
                  <?php endif; ?>
                </div>
              </div>

            </div>
          </div>
        <?php endforeach; ?>

      </div>
    </div>

    <?php if (count($slides) > 1) : ?>
      <button class="s-hero__nav s-hero__nav--prev" type="button" aria-label="<?= esc_attr__('Anterior', 'lucci-fresh') ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M14 16L10 12L14 8" stroke="#FD8426" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
      <button class="s-hero__nav s-hero__nav--next" type="button" aria-label="<?= esc_attr__('Próximo', 'lucci-fresh') ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M10 16L14 12L10 8" stroke="#FD8426" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
    <?php endif; ?>

  </div><!-- /.s-hero__slider-wrap -->

  <?php if (count($slides) > 1) : ?>
    <div class="s-hero__controls">
      <div class="s-hero__pagination swiper-pagination"></div>
    </div>
  <?php endif; ?>

</section>