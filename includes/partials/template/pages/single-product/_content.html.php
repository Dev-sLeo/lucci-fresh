<?php
// includes/partials/template/pages/single-product/_content.html.php
defined('ABSPATH') || exit;

$descricao   = get_the_content()   ?: '';
$ingredientes = get_field('ingredientes') ?: '';
$tabela      = get_field('tabela_nutricional') ?: [];

// Verifica se há conteúdo para renderizar
if (!$descricao && !$ingredientes && empty($tabela)) return;
?>
<section class="s-sp-content">
  <div class="s-container">
    <div class="s-sp-content__inner">

      <!-- Coluna Esquerda: Descrição + Ingredientes -->
      <?php if ($descricao || $ingredientes) : ?>
        <div class="s-sp-content__text">

          <?php if ($descricao) : ?>
            <div class="s-sp-content__block">
              <h2 class="s-sp-content__subtitle"><?= esc_html__('Descrição', 'lucci-fresh') ?></h2>
              <div class="s-sp-content__body">
                <?= wp_kses_post($descricao) ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($ingredientes) : ?>
            <div class="s-sp-content__block">
              <h2 class="s-sp-content__subtitle"><?= esc_html__('Ingredientes', 'lucci-fresh') ?></h2>
              <div class="s-sp-content__body">
                <?= wp_kses_post($ingredientes) ?>
              </div>
            </div>
          <?php endif; ?>

        </div>
      <?php endif; ?>

      <!-- Coluna Direita: Tabela Nutricional -->
      <?php if (!empty($tabela)) : ?>
        <div class="s-sp-content__nutrition">
          <h2 class="s-sp-content__nutrition-title"><?= esc_html__('Tabela nutricional', 'lucci-fresh') ?></h2>
          <?php if (get_field('description_table')) : ?>
            <p class="s-sp-content__nutrition-description"><?= get_field('description_table') ?></p>
          <?php endif; ?>
          <div class="s-sp-content__table">
            <?php
            $row_count = 0;
            foreach ($tabela as $row) :
              $nutriente = $row['nutriente'] ?? '';
              $valor     = $row['valor']     ?? '';
              if (!$nutriente) continue;
            ?>
              <?php if ($row_count > 0) : ?>
                <hr class="s-sp-content__divider" aria-hidden="true">
              <?php endif; ?>
              <div class="s-sp-content__row">
                <span class="s-sp-content__nutriente"><?= esc_html($nutriente) ?></span>
                <span class="s-sp-content__valor"><?= esc_html($valor) ?></span>
              </div>
            <?php
              $row_count++;
            endforeach; ?>
          </div>
          <?php if (get_field('description_after_table')) : ?>
            <p class="s-sp-content__nutrition-description"><?= get_field('description_after_table') ?></p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section><!-- /.s-sp-content -->