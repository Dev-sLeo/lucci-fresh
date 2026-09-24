<?php
/**
 * Step 3 - Pagamento.
 *
 * Usa o hook nativo woocommerce_checkout_payment (mesma lista de gateways,
 * mesmos payment_fields()/process_payment() de cada gateway - Pix, Rede
 * crédito/débito, COD). O layout de "cards" é só CSS por cima do
 * ul.wc_payment_methods padrão; o JS de toggle (mostrar/esconder os campos
 * de cada gateway ao trocar o radio) já vem do core do WooCommerce
 * (assets/js/frontend/checkout.js), não precisa ser reimplementado.
 */
defined('ABSPATH') || exit;
?>

<section class="c-checkout-step" data-checkout-step="3" aria-label="<?= esc_attr__('Pagamento', 'lucci-fresh'); ?>">
  <h2 class="c-checkout-step__title"><?= esc_html__('Como prefere pagar?', 'lucci-fresh'); ?></h2>
  <p class="c-checkout-step__payment-subtitle"><?= esc_html__('Escolha a forma de pagamento do seu pedido.', 'lucci-fresh'); ?></p>

  <?php
  /**
   * A classe de estilo (c-checkout-step__payment) fica num wrapper por
   * fora do #payment, não nele - o core do WooCommerce (checkout.js,
   * update_checkout via AJAX) substitui o <div id="payment"> inteiro pelo
   * HTML que vem do servidor (checkout/payment.php, que só tem a classe
   * "woocommerce-checkout-payment") sempre que recalcula o carrinho. Uma
   * classe extra direto nele some no primeiro recálculo automático (dispara
   * já no carregamento da página) - só sobrevive num elemento que o AJAX
   * não toca.
   */
  ?>
  <div class="c-checkout-step__payment">
    <div id="payment" class="woocommerce-checkout-payment">
      <?php do_action('woocommerce_checkout_payment'); ?>
    </div>
  </div>

  <button type="button" class="c-btn--checkout-next" data-checkout-next="4">
    <?= esc_html__('Revisar pedido', 'lucci-fresh'); ?>
  </button>

  <p class="c-checkout-step__privacy">
    <?= esc_html__('Seus dados de pagamento são protegidos.', 'lucci-fresh'); ?>
  </p>
</section>
