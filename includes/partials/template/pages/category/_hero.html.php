<?php
// includes/partials/template/pages/category/_hero.html.php
defined('ABSPATH') || exit;
global $tpl_engine;

$queried_object = get_queried_object();
$term_id        = $queried_object->term_id ?? 0;
$term_slug      = $queried_object->slug ?? '';
$hero           = get_field('hero', 'product_cat_' . $term_id) ?: [];

// ACF fields from taxonomy term
$titulo    = $hero['title']    ?? __('Desconto progressivo', 'lucci-fresh');
$descricao = $hero['description'] ?? __('Quanto mais você leva, maior o desconto', 'lucci-fresh');
$tabela    = $hero['tabela']    ?? [];

// Imagens hero
$imagens     = $hero['imagens'] ?? [];
$img_desktop = $imagens['desktop'] ?? null;
$img_mobile  = $imagens['mobile']  ?? null;
$is_marmita  = $term_slug === 'marmita';

// Background (somente categorias que não são marmita e possuem imagem)
$has_bg          = !$is_marmita && ($img_desktop || $img_mobile);
$bg_desktop_url  = '';
$bg_mobile_url   = '';
if ($has_bg) {
  $resolve_url = function ($img) {
    if (!$img) return '';
    $id = is_array($img) ? ($img['id'] ?? null) : intval($img);
    return $id ? wp_get_attachment_image_url($id, 'full') : ($img['url'] ?? '');
  };
  $bg_desktop_url = $resolve_url($img_desktop);
  $bg_mobile_url  = $resolve_url($img_mobile) ?: $bg_desktop_url;
}

// Monta inline style com custom properties CSS
$bg_style = '';
if ($has_bg) {
  $parts = [];
  if ($bg_desktop_url) $parts[] = "--bg-desktop: url('" . esc_url($bg_desktop_url) . "')";
  if ($bg_mobile_url)  $parts[] = "--bg-mobile: url('"  . esc_url($bg_mobile_url)  . "')";
  $bg_style = implode('; ', $parts);
}
?>
<section class="s-cat-hero<?= $has_bg ? ' s-cat-hero--has-bg' : '' ?>" <?= $bg_style ? ' style="' . esc_attr($bg_style) . '"' : '' ?>>

  <?php if (!$has_bg) : ?>
    <div class="s-cat-hero__pattern" aria-hidden="true">
      <?php $tpl_engine->svg('pattern/pattern-hero') ?>
    </div>
  <?php endif; ?>

  <div class="s-container">
    <div class="s-cat-hero__inner">

      <h1 class="s-cat-hero__title"><?= esc_html($titulo) ?></h1>
      <p class="s-cat-hero__description"><?= esc_html($descricao) ?></p>

      <?php if ($is_marmita && ($img_desktop || $img_mobile)) : ?>
        <div class="s-cat-hero__image-wrap">
          <?php if ($img_desktop) : ?>
            <div class="s-cat-hero__image s-cat-hero__image--desktop">
              <?= render_media_image($img_desktop, 'full', ['class' => 's-cat-hero__img', 'alt' => esc_attr($titulo)]) ?>
            </div>
          <?php endif; ?>
          <?php if ($img_mobile) : ?>
            <div class="s-cat-hero__image s-cat-hero__image--mobile">
              <?= render_media_image($img_mobile, 'full', ['class' => 's-cat-hero__img', 'alt' => esc_attr($titulo)]) ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($tabela)) : ?>
        <div class="s-cat-hero__table">
          <?php foreach ($tabela as $faixa) :
            $rotulo   = $faixa['rotulo']   ?? '';
            $unidade  = $faixa['unidade']  ?? '';
            $preco    = $faixa['preco']    ?? '';
            $destaque = !empty($faixa['destaque']);
          ?>
            <div class="s-cat-hero__tier<?= $destaque ? ' s-cat-hero__tier--featured' : '' ?>">
              <span class="s-cat-hero__tier-range"><?= esc_html($rotulo) ?></span>
              <span class="s-cat-hero__tier-unit"><?= esc_html($unidade) ?></span>
              <div class="s-cat-hero__tier-price">
                <span class="s-cat-hero__tier-prefix"><?= esc_html__('a partir de', 'lucci-fresh') ?></span>
                <strong class="s-cat-hero__tier-value"><?= esc_html($preco) ?></strong>
                <span class="s-cat-hero__tier-suffix"><?= esc_html__('/cada', 'lucci-fresh') ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section><!-- /.s-cat-hero -->