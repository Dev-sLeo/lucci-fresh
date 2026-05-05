<?php
// includes/partials/template/pages/_quem-somos.html.php
global $tpl_engine;

// ── Hero ──────────────────────────────────────────────────────────────────────
$hero         = get_field('hero') ?? [];
$hero_eyebrow = $hero['eyebrow']     ?? __('Quem somos', 'lucci-fresh');
$hero_titulo  = $hero['title']       ?? __('Da nossa casa para sua, todos os dias', 'lucci-fresh');
$hero_desc    = $hero['description'] ?? '';
$hero_img     = $hero['image']       ?? null;

// ── Descrição / História ──────────────────────────────────────────────────────
$desc_block = get_field('description') ?? [];
$texto      = $desc_block['texto']   ?? '';
$citacao    = $desc_block['citacao'] ?? '';
?>

<!-- ═══════════════════════════════════════════════════════════════ HERO -->
<section class="s-qs-hero">

  <div class="s-qs-hero__pattern" aria-hidden="true">
    <?php $tpl_engine->svg('pattern/pattern-hero') ?>
  </div>

  <div class="s-container">
    <div class="s-qs-hero__inner">

      <div class="s-qs-hero__content">
        <?php if ($hero_eyebrow) : ?>
          <p class="s-qs-hero__eyebrow"><?= esc_html($hero_eyebrow) ?></p>
        <?php endif; ?>
        <h1 class="s-qs-hero__title"><?= esc_html($hero_titulo) ?></h1>
        <?php if ($hero_desc) : ?>
          <p class="s-qs-hero__description"><?= esc_html($hero_desc) ?></p>
        <?php endif; ?>
      </div>

    </div>
  </div>

</section><!-- /.s-qs-hero -->

<?php if ($hero_img) : ?>
  <div class="s-qs-media-bridge">
    <div class="s-container">
      <div class="s-qs-hero__media">
        <img
          class="s-qs-hero__image"
          src="<?= esc_url($hero_img['url']) ?>"
          alt="<?= esc_attr($hero_img['alt'] ?? $hero_titulo) ?>"
          width="<?= esc_attr($hero_img['width'] ?? '') ?>"
          height="<?= esc_attr($hero_img['height'] ?? '') ?>"
          loading="eager">
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════ HISTÓRIA -->
<?php if ($texto || $citacao) : ?>
  <section class="s-qs-about">
    <div class="s-container">

      <?php if ($texto) : ?>
        <div class="s-qs-about__texto">
          <?= wp_kses_post($texto) ?>
        </div>
      <?php endif; ?>

      <?php if ($citacao) : ?>
        <blockquote class="s-qs-about__quote">
          <span class="s-qs-about__quote-mark" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="28" viewBox="0 0 33 28" fill="none">
              <path d="M13.9057 14.1538V28H0V24.4103C0 18.8034 0.415094 14.6325 1.24528 11.8974C2.14465 9.09402 4.46226 5.1282 8.19811 0L14.1132 3.28205C11 8.54701 9.20126 12.1709 8.71698 14.1538H13.9057ZM32.7924 14.1538V28H18.8868V24.4103C18.8868 18.8034 19.3019 14.6325 20.1321 11.8974C21.0314 9.09402 23.3491 5.1282 27.0849 0L33 3.28205C29.8868 8.54701 28.0881 12.1709 27.6038 14.1538H32.7924Z" fill="#425D33" />
            </svg>
          </span>
          <p class="s-qs-about__quote-text"><?= esc_html($citacao) ?></p>
        </blockquote>
      <?php endif; ?>

    </div>
  </section><!-- /.s-qs-about -->
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════ RESTAURANTE -->
<?php $tpl_engine->partial('template/global/store') ?>

<!-- ══════════════════════════════════════════════════════════ DEPOIMENTOS -->
<?php $tpl_engine->partial('template/global/testimonials') ?>