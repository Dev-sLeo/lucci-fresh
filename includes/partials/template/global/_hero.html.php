<?php
// includes/partials/template/global/_hero.html.php
global $tpl_engine;

$block     = get_field('hero');
$titulo    = $block['title']       ?? __('A melhor mini pizza de São Paulo', 'lucci-fresh');
$descricao = $block['description'] ?? __('Sabor caseiro, ingredientes selecionados e praticidade para o seu dia a dia. Peça agora e receba qualidade', 'lucci-fresh');
$btn1      = $block['buttons']['button']   ?? [];
$btn2      = $block['buttons']['button_2'] ?? [];
$imagens   = $block['imagens'] ?? [];

$img_produto = $imagens['desktop'] ?? null;
$img_mobile  = $imagens['mobile']  ?? null;
$foto_1      = $imagens['foto_1']  ?? null;
$foto_2      = $imagens['foto_2']  ?? null;

$btn1_url    = $btn1['url']    ?? '#';
$btn1_texto  = $btn1['title']  ?? __('R$ 15,80', 'lucci-fresh');
$btn1_target = $btn1['target'] ?? '_self';

$btn2_url    = $btn2['url']    ?? wc_get_page_permalink('shop');
$btn2_texto  = $btn2['title']  ?? __('Fazer pedido', 'lucci-fresh');
$btn2_target = $btn2['target'] ?? '_self';
?>
<section class="s-hero">
  <div class="s-hero__pattern" aria-hidden="true">
    <?php $tpl_engine->svg('pattern/pattern-hero') ?>
  </div>
  <div class="s-hero__inner">

      <div class="s-hero__content">
        <div class="s-hero__text">
          <h1 class="s-hero__title"><?= esc_html($titulo) ?></h1>
          <p class="s-hero__description"><?= esc_html($descricao) ?></p>
        </div>
        <div class="s-hero__cta">
          <a href="<?= esc_url($btn1_url) ?>"
            class="u-button u-button__white s-hero__btn"
            target="<?= esc_attr($btn1_target) ?>">
            <?= esc_html($btn1_texto) ?>
          </a>
          <a href="<?= esc_url($btn2_url) ?>"
            class="u-button u-button__wood s-hero__btn"
            target="<?= esc_attr($btn2_target) ?>">
            <?= esc_html($btn2_texto) ?>
          </a>
        </div>
      </div>

      <div class="s-hero__media">
        <div class="s-hero__product">
          <?php if ($img_produto) : ?>
            <picture>
              <?php if ($img_mobile) : ?>
                <source srcset="<?= esc_url($img_mobile['url']) ?>" media="(max-width: 719px)">
              <?php endif; ?>
              <img
                class="s-hero__image"
                src="<?= esc_url($img_produto['url']) ?>"
                alt="<?= esc_attr($img_produto['alt'] ?? $titulo) ?>"
                width="<?= esc_attr($img_produto['width'] ?? '') ?>"
                height="<?= esc_attr($img_produto['height'] ?? '') ?>">
            </picture>
          <?php endif; ?>
        </div>

        <?php if ($foto_1 || $foto_2) : ?>
          <div class="s-hero__photos">
            <?php if ($foto_1) : ?>
              <div class="s-hero__photo">
                <?= wp_get_attachment_image($foto_1['ID'], 'large', false, ['loading' => 'lazy', 'class' => 's-hero__photo-img', 'alt' => esc_attr($foto_1['alt'] ?? '')]) ?>
              </div>
            <?php endif; ?>
            <?php if ($foto_2) : ?>
              <div class="s-hero__photo">
                <?= wp_get_attachment_image($foto_2['ID'], 'large', false, ['loading' => 'lazy', 'class' => 's-hero__photo-img', 'alt' => esc_attr($foto_2['alt'] ?? '')]) ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

  </div>
</section>