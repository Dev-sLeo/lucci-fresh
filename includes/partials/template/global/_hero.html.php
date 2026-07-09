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
          $titulo   = $slide['title'] ?? '';
          $link     = $slide['link']  ?? '';
          // O grupo de imagens tem name="" no ACF — sub-fields ficam em $slide['']
          $imagens  = isset($slide['imagens']) && is_array($slide['imagens']) ? $slide['imagens'] : $slide;
          $img_desk = $imagens['desktop'] ?? null;
          $img_mob  = $imagens['mobile']  ?? null;
          $tag      = $link ? 'a' : 'div';
        ?>
          <div class="swiper-slide">
            <<?= $tag ?> class="s-hero__inner"<?php if ($link) : ?> href="<?= esc_url($link) ?>"<?php endif; ?>>
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
            </<?= $tag ?>>
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