<?php
/**
 * Coluna lateral "Resumo do pedido" - mesmo hook/markup padrão do
 * WooCommerce (review-order.php), só que sem o método de frete (já movido
 * para o step de Entrega - ver step-entrega.html.php).
 */
defined('ABSPATH') || exit;

$item_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>

<aside class="p-checkout-v2__summary" aria-label="<?= esc_attr__('Resumo do pedido', 'lucci-fresh'); ?>">
  <h3><?= esc_html__('Seu pedido', 'lucci-fresh'); ?></h3>
  <p class="p-checkout-v2__summary-subtitle">
    <?= esc_html(sprintf(
      /* translators: %d: número de itens no carrinho */
      _n('%d item • preparado com carinho', '%d itens • preparados com carinho', $item_count, 'lucci-fresh'),
      $item_count
    )); ?>
  </p>

  <?php do_action('woocommerce_checkout_before_order_review'); ?>
  <div id="order_review" class="woocommerce-checkout-review-order">
    <?php do_action('woocommerce_checkout_order_review'); ?>
  </div>
  <?php do_action('woocommerce_checkout_after_order_review'); ?>

  <a href="<?= esc_url(wc_get_cart_url()); ?>" class="p-checkout-v2__summary-edit-cart">
    <?= esc_html__('← Editar carrinho', 'lucci-fresh'); ?>
  </a>
</aside>
