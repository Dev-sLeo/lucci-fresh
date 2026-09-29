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

  <?php
  /**
   * Abre o mesmo sidebar de carrinho do header normal (#cart-sidebar,
   * ver webpack/js/scripts/cartSidebar.js) em vez de navegar para
   * /carrinho/ - o markup do sidebar é incluído no cabeçalho do checkout
   * (_header-checkout.html.php) só por causa deste botão.
   *
   * <button>, não <a href>: o open() do cartSidebar.js não dá
   * preventDefault() (mesmo padrão do botão de carrinho do header normal,
   * _header.html.php) - com href, o clique navegaria pra /carrinho/ antes
   * (ou ao mesmo tempo) do sidebar abrir.
   */
  ?>
  <button type="button" class="p-checkout-v2__summary-edit-cart js-cart-open" aria-controls="cart-sidebar" aria-expanded="false">
    <?= esc_html__('← Editar carrinho', 'lucci-fresh'); ?>
  </button>
</aside>
