<?php
// includes/partials/template/global/_store.html.php
global $tpl_engine;

$block          = get_field('restaurante');
$eyebrow        = $block['eyebrow']      ?? '';
$titulo         = $block['title']        ?? __('Conheça nossa loja física', 'lucci-fresh');
$descricao      = $block['description']  ?? '';
$galeria        = $block['galeria']      ?? [];
$mapas          = $block['mapa']         ?? [];
?>
<section class="s-store">

  <!-- Top: fundo branco com cabeçalho -->
  <div class="s-store__top">
    <div class="s-container">
      <div class="s-store__header">
        <?php if ($eyebrow) : ?>
          <p class="s-store__eyebrow"><?= esc_html($eyebrow) ?></p>
        <?php endif; ?>
        <h2 class="s-store__title"><?= esc_html($titulo) ?></h2>
      </div>
    </div>
  </div>

  <!-- Galeria: ponte visual entre branco e laranja -->
  <?php if (!empty($galeria)) : ?>
    <div class="s-store__gallery-wrap">
      <div class="s-container">
        <div class="s-store__gallery">
          <?php foreach ($galeria as $foto) : ?>
            <div class="s-store__gallery-item">
              <img
                class="s-store__gallery-img"
                src="<?= esc_url($foto['url']) ?>"
                alt="<?= esc_attr($foto['alt'] ?? $titulo) ?>"
                loading="lazy">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Body: fundo laranja com pattern + conteúdo -->
  <div class="s-store__body">
    <span class="s-store__pattern" aria-hidden="true">
      <img src="<?= get_stylesheet_directory_uri() ?>/public/image/pattern-store.webp" alt="">
    </span>

    <div class="s-container">

      <?php if ($descricao) : ?>
        <div class="s-store__info">
          <p class="s-store__description"><?= esc_html($descricao) ?></p>
        </div>
      <?php endif; ?>

      <?php if (!empty($mapas)) : ?>
        <div class="s-store__maps">
          <?php foreach ($mapas as $mapa) :
            $mapa_img     = $mapa['image']   ?? null;
            $mapa_link    = $mapa['link']    ?? '';
            $mapa_address = $mapa['address'] ?? '';
          ?>
            <div class="c-map-card">
              <?php if ($mapa_img) : ?>
                <div class="c-map-card__image-wrap">
                  <?php if ($mapa_link) : ?>
                    <a href="<?= esc_url($mapa_link) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= esc_attr__('Ver no mapa', 'lucci-fresh') ?>">
                    <?php endif; ?>
                    <img
                      class="c-map-card__image"
                      src="<?= esc_url($mapa_img['url']) ?>"
                      alt="<?= esc_attr($mapa_img['alt'] ?? $mapa_address) ?>"
                      loading="lazy">
                    <?php if ($mapa_link) : ?>
                    </a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
              <?php if ($mapa_address) : ?>
                <div class="c-map-card__address">
                  <div class="c-map-card__pin">
                    <?php $tpl_engine->svg('icons/pin-location'); ?>
                  </div>
                  <p class="c-map-card__address-text"><?= esc_html($mapa_address) ?></p>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>

</section>