<?php
// includes/partials/template/pages/qm-somos/_hero.html.php
global $tpl_engine;

$hero         = get_field('hero') ?? [];
$hero_eyebrow = $hero['eyebrow']  ?? __('Quem somos', 'lucci-fresh');
$hero_titulo  = $hero['title']    ?? __('Da nossa casa para sua, todos os dias', 'lucci-fresh');
$hero_desc    = $hero['description'] ?? '';
$hero_img     = $hero['image']    ?? null;
$hero_simbolo = $hero['simbolo']  ?? null;
?>
<section class="s-qs-hero">
  <span class="s-qs-hero__pattern" aria-hidden="true">
    <img src="<?= get_stylesheet_directory_uri() ?>/public/image/pattern-hero-qm-somos.webp" alt="">
  </span>
  <div class="s-container">

    <div class="s-qs-hero__content">
      <?php if ($hero_eyebrow) : ?>
        <p class="s-qs-hero__eyebrow"><?= esc_html($hero_eyebrow) ?></p>
      <?php endif; ?>
      <h1 class="s-qs-hero__title"><?= esc_html($hero_titulo) ?></h1>
      <?php if ($hero_desc) : ?>
        <p class="s-qs-hero__description"><?= esc_html($hero_desc) ?></p>
      <?php endif; ?>
    </div>

    <?php if ($hero_img) : ?>
      <div class="s-qs-hero__media">
        <img
          class="s-qs-hero__image"
          src="<?= esc_url($hero_img['url']) ?>"
          alt="<?= esc_attr($hero_img['alt'] ?? $hero_titulo) ?>"
          loading="eager">
        <?php if ($hero_simbolo) : ?>
          <div class="s-qs-hero__symbol" aria-hidden="true">
            <img
              src="<?= esc_url($hero_simbolo['url']) ?>"
              alt=""
              loading="lazy">
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>

</section><!-- /.s-qs-hero -->