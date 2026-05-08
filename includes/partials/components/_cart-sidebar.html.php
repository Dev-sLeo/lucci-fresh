<?php
// includes/partials/components/_cart-sidebar.html.php
defined('ABSPATH') || exit;

if (!function_exists('WC') || !WC()->cart) return;
?>
<div class="c-cart-sidebar" id="cart-sidebar" role="dialog" aria-modal="true" aria-label="<?= esc_attr__('Carrinho', 'lucci-fresh') ?>" aria-hidden="true">

  <div class="c-cart-sidebar__overlay js-cart-overlay" tabindex="-1"></div>

  <div class="c-cart-sidebar__panel">

    <div class="c-cart-sidebar__header">
      <h2 class="c-cart-sidebar__title"><?= esc_html__('Carrinho', 'lucci-fresh') ?></h2>
      <button class="c-cart-sidebar__close js-cart-close" type="button" aria-label="<?= esc_attr__('Fechar carrinho', 'lucci-fresh') ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
    </div>

    <!-- ── Conteúdo dinâmico (atualizado via WC fragment) ──────────────── -->
    <?php include PATHS_PARTIALS . '/components/_cart-sidebar-content.html.php'; ?>

  </div><!-- /.c-cart-sidebar__panel -->

</div><!-- /.c-cart-sidebar -->