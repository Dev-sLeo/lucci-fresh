<?php
/**
 * Pay for order form.
 *
 * Override do template nativo do WooCommerce (checkout/form-pay.php,
 * v8.2.0) - usado quando o cliente troca de forma de pagamento a partir da
 * tela "Aguardando Pix" (link "Escolher outra forma de pagamento", ver
 * _pix-aguardando.html.php → $order->get_checkout_payment_url()). Sem esse
 * override, essa página usa o template padrão do WooCommerce (tabela de
 * itens + lista de gateways sem nenhum dos estilos do checkout novo) - o
 * pedido some visualmente do fluxo "Como prefere pagar?" do resto do site.
 *
 * A lista de gateways continua usando exatamente o mesmo template
 * checkout/payment-method.php (já com override do tema) que o step 3 do
 * checkout novo usa - por isso os cards ficam idênticos, só o wrapper em
 * volta (.c-checkout-step__payment) muda pra reaproveitar o mesmo CSS.
 *
 * @var WC_Order $order
 * @var WC_Payment_Gateway[] $available_gateways
 * @var string $order_button_text
 */

defined('ABSPATH') || exit;
?>

<div class="p-checkout-v2">

  <a href="<?= esc_url(home_url('/')); ?>" class="p-checkout-v2__back">
    <?= esc_html__('← Voltar', 'lucci-fresh'); ?>
  </a>

  <div class="p-checkout-v2__intro">
    <h1><?= esc_html__('Escolha outra forma de pagamento', 'lucci-fresh'); ?></h1>
    <p>
      <?= esc_html(sprintf(
        /* translators: %s: número do pedido */
        __('Pedido #%s', 'lucci-fresh'),
        $order->get_order_number()
      )); ?>
    </p>
  </div>

  <form id="order_review" method="post">

    <section class="c-checkout-step is-active" aria-label="<?= esc_attr__('Pagamento', 'lucci-fresh'); ?>">
      <h2 class="c-checkout-step__title"><?= esc_html__('Como prefere pagar?', 'lucci-fresh'); ?></h2>
      <p class="c-checkout-step__payment-subtitle"><?= esc_html__('Escolha a forma de pagamento do seu pedido.', 'lucci-fresh'); ?></p>

      <?php if ($order->needs_payment()) : ?>
        <div class="c-checkout-step__payment">
          <div id="payment" class="woocommerce-checkout-payment">
            <ul class="wc_payment_methods payment_methods methods">
              <?php
              if (!empty($available_gateways)) {
                  foreach ($available_gateways as $gateway) {
                      wc_get_template('checkout/payment-method.php', ['gateway' => $gateway]);
                  }
              } else {
                  echo '<li>';
                  wc_print_notice(
                      apply_filters(
                          'woocommerce_no_available_payment_methods_message',
                          esc_html__('Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce')
                      ),
                      'notice'
                  );
                  echo '</li>';
              }
              ?>
            </ul>
          </div>
        </div>
      <?php endif; ?>

      <input type="hidden" name="woocommerce_pay" value="1" />

      <?php wc_get_template('checkout/terms.php'); ?>

      <?php do_action('woocommerce_pay_order_before_submit'); ?>

      <button type="submit" class="c-btn--checkout-next" id="place_order" value="<?= esc_attr($order_button_text); ?>" data-value="<?= esc_attr($order_button_text); ?>">
        <?= esc_html($order_button_text); ?>
      </button>

      <?php do_action('woocommerce_pay_order_after_submit'); ?>

      <?php wp_nonce_field('woocommerce-pay', 'woocommerce-pay-nonce'); ?>

      <p class="c-checkout-step__privacy">
        <?= esc_html__('Seus dados de pagamento são protegidos.', 'lucci-fresh'); ?>
      </p>
    </section>

  </form>

</div>
