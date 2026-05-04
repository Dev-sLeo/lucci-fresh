<?php
// includes/partials/template/pages/single-product/_benefits.html.php
defined('ABSPATH') || exit;

$items = get_field('beneficios') ?: [];
if (empty($items)) return;
?>
<section class="s-sp-benefits">
  <div class="s-container">
    <ul class="s-sp-benefits__list">
      <?php foreach ($items as $item) :
        $icone = $item['icone'] ?? null;
        $label = $item['label'] ?? '';
        if (!$label) continue;
      ?>
        <li class="s-sp-benefits__card">
          <?php if ($icone) : ?>
            <div class="s-sp-benefits__icon" aria-hidden="true">
              <img
                src="<?= esc_url($icone['url']) ?>"
                alt=""
                width="48" height="48"
                loading="lazy">
            </div>
          <?php endif; ?>
          <p class="s-sp-benefits__label"><?= esc_html($label) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section><!-- /.s-sp-benefits -->