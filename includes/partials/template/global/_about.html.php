<?php
// includes/partials/template/global/_about.html.php
global $tpl_engine;

$block     = get_field('nossa_historia');
$eyebrow   = $block['eyebrow']     ?? __('Nossa História', 'lucci-fresh');
$titulo    = $block['title']       ?? __('Da nossa casa para a sua, todos os dias!', 'lucci-fresh');
$descricao = $block['description'] ?? '';
$btn       = $block['button']      ?? [];
$imagem    = $block['imagem']      ?? null;

$btn_url    = $btn['url']    ?? '#';
$btn_texto  = $btn['title']  ?? __('Saiba mais', 'lucci-fresh');
$btn_target = $btn['target'] ?? '_self';
?>
<section class="s-about">
  <div class="s-container">
    <div class="s-about__container">
      <div class="s-about__content">
        <div class="s-about__text-block">
          <?php if ($eyebrow) : ?>
            <p class="s-about__eyebrow"><?= esc_html($eyebrow) ?></p>
          <?php endif; ?>
          <h2 class="s-about__title"><?= esc_html($titulo) ?></h2>
          <?php if ($descricao) : ?>
            <p class="s-about__description"><?= esc_html($descricao) ?></p>
          <?php endif; ?>
          <a href="<?= esc_url($btn_url) ?>" class="u-button u-button__wood s-about__btn" target="<?= esc_attr($btn_target) ?>">
            <?= esc_html($btn_texto) ?>
          </a>
        </div>
      </div>

      <?php if ($imagem) : ?>
        <div class="s-about__media">
          <img
            class="s-about__image"
            src="<?= esc_url($imagem['url']) ?>"
            alt="<?= esc_attr($imagem['alt'] ?? $titulo) ?>"
            width="<?= esc_attr($imagem['width'] ?? '') ?>"
            height="<?= esc_attr($imagem['height'] ?? '') ?>"
            loading="lazy">
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>