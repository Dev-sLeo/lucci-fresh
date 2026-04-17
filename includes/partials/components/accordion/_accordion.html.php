<?php global $tpl_engine; ?>
<?php

/**
 * Componente Accordion Reutilizável
 * Espera receber:
 * $items = [
 *   [
 *     'icone' => image array ID,
 *     'titulo' => string,
 *     'texto' => html,
 *   ]
 * ]
 */
?>

<div class="c-accordion">

  <?php foreach ($items as $index => $item): ?>
    <?php
    $icon = $item['icone'];
    $title = $item['titulo'];
    $text = $item['texto'];
    ?>

    <div class="c-accordion__item" data-acc="<?= $index ?>">

      <button class="c-accordion__head" type="button">
        <div class="c-accordion__head--left">
          <?php if ($icon): ?>
            <div class="c-accordion__icon">
              <?= $icon ? render_media_image($icon['ID'], 'full') : '' ?>
            </div>
          <?php endif ?>
          <span class="c-accordion__title"><?= esc_html($title) ?></span>
        </div>
        <span class="c-accordion__arrow">
          <?= $tpl_engine->svg('filters/arrow'); ?>
        </span>
      </button>

      <div class="c-accordion__body">
        <div class="c-accordion__content">
          <?= wp_kses_post($text) ?>
        </div>
      </div>

    </div>

  <?php endforeach; ?>

</div>