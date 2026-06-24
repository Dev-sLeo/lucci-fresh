<?php
// includes/partials/components/_cart-discount-banner.html.php
// Banner de progresso de desconto AWDP, reutilizado no sidebar e na página do carrinho.
defined('ABSPATH') || exit;

$disc_info = function_exists('lucci_get_awdp_discount_info') ? lucci_get_awdp_discount_info() : ['show' => false];
$show_disc = !empty($disc_info['show']);
?>
<div class="c-cart-discount" <?= !$show_disc ? 'aria-hidden="true"' : '' ?>>
  <div class="c-cart-discount__top">
    <div class="c-cart-discount__icon" aria-hidden="true">
      <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M14 25.6667C19.1546 25.6667 23.3333 21.488 23.3333 16.3333C23.3333 9.33333 14 2.33333 14 2.33333C13.5469 5.23474 13.1034 6.79184 11.6666 9.33333C10.2656 8.68572 9.91663 8.16667 9.33329 6.70833C6.99996 9.33333 4.66663 12.8333 4.66663 16.3333C4.66663 21.488 8.8453 25.6667 14 25.6667Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
        <path d="M16.9167 14.5833L11.0834 20.4167" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M11.5209 14.875H11.375M11.6667 14.875C11.6667 15.0361 11.5361 15.1667 11.375 15.1667C11.214 15.1667 11.0834 15.0361 11.0834 14.875C11.0834 14.7139 11.214 14.5833 11.375 14.5833C11.5361 14.5833 11.6667 14.7139 11.6667 14.875Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M16.7709 20.125H16.625M16.9167 20.125C16.9167 20.2861 16.7862 20.4167 16.625 20.4167C16.4639 20.4167 16.3334 20.2861 16.3334 20.125C16.3334 19.9639 16.4639 19.8333 16.625 19.8333C16.7862 19.8333 16.9167 19.9639 16.9167 20.125Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </div>
    <div class="c-cart-discount__body">
      <p class="c-cart-discount__label"><?= esc_html__('Receba seu desconto', 'lucci-fresh') ?></p>
      <?php if ($show_disc && !empty($disc_info['hint'])) : ?>
        <p class="c-cart-discount__hint"><?= wp_kses_post($disc_info['hint']) ?></p>
      <?php endif; ?>
    </div>
  </div>
  <?php if ($show_disc) : ?>
    <div class="c-cart-discount__bottom">
      <span class="c-cart-discount__fraction"><?= esc_html($disc_info['fraction']) ?></span>
      <?php
      $thresh  = max(1, (int) $disc_info['threshold']);
      $current = min($thresh, (int) $disc_info['current']);
      $percent = round(($current / $thresh) * 100);
      ?>
      <div class="c-cart-discount__bar" role="progressbar" aria-valuemin="0" aria-valuemax="<?= esc_attr($thresh) ?>" aria-valuenow="<?= esc_attr($current) ?>">
        <span class="c-cart-discount__bar-fill" style="width: <?= esc_attr($percent) ?>%;"></span>
      </div>
    </div>
  <?php endif; ?>
</div>
