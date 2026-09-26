<?php
/**
 * Tela "Pedido confirmado" (Figma node 2106:1500) - aparece na página de
 * pedido recebido (thank you) para qualquer pedido que não seja Pix
 * aguardando pagamento (ver woocommerce/checkout/thankyou.php e, para o
 * caso Pix aguardando, _pix-aguardando.html.php).
 *
 * @var WC_Order $order
 */
defined('ABSPATH') || exit;

global $tpl_engine;

$delivery_date   = $order->get_meta('ywcdd_order_delivery_date');
$shipping_address = $order->get_formatted_shipping_address();
?>

<div class="p-checkout-v2">

  <a href="<?= esc_url(home_url('/')); ?>" class="p-checkout-v2__back">
    <?= esc_html__('← Voltar', 'lucci-fresh'); ?>
  </a>

  <div class="p-checkout-v2__intro">
    <h1><?= esc_html__('Pedido confirmado. Bom apetite!', 'lucci-fresh'); ?></h1>
    <p><?= esc_html__('Obrigada por escolher a Lucci Fresh. Vamos cuidar de tudo por aqui.', 'lucci-fresh'); ?></p>
  </div>

  <?php $tpl_engine->partial('template/pages/checkout/progress', ['all_done' => true]); ?>

  <div class="p-checkout-v2__cols">

    <div class="p-checkout-v2__steps">
      <section class="c-checkout-step is-active" aria-label="<?= esc_attr__('Pedido confirmado', 'lucci-fresh'); ?>">

        <span class="c-order-confirmed__icon" aria-hidden="true">✓</span>

        <h2 class="c-checkout-step__title"><?= esc_html__('Recebemos seu pedido!', 'lucci-fresh'); ?></h2>
        <p class="c-checkout-step__subtitle">
          <?= esc_html(sprintf(
            /* translators: 1: número do pedido, 2: status do pedido */
            __('Pedido #%1$s • %2$s', 'lucci-fresh'),
            $order->get_order_number(),
            wc_get_order_status_name($order->get_status())
          )); ?>
        </p>

        <p class="c-order-confirmed__text">
          <?= esc_html(sprintf(
            /* translators: %s: e-mail do cliente */
            __('Enviamos os detalhes para %s. Você também receberá atualizações pelo WhatsApp.', 'lucci-fresh'),
            $order->get_billing_email()
          )); ?>
        </p>

        <div class="c-order-confirmed__box">
          <p class="c-order-confirmed__box-title"><?= esc_html__('Previsão de entrega', 'lucci-fresh'); ?></p>
          <p class="c-order-confirmed__box-text">
            <?php if ($delivery_date) : ?>
              <?= esc_html(sprintf(
                /* translators: %s: data prevista de entrega */
                __('Prevista para %s.', 'lucci-fresh'),
                wc_format_datetime(new WC_DateTime($delivery_date, new DateTimeZone('UTC')))
              )); ?>
            <?php else : ?>
              <?= esc_html__('Prazo médio de até 2 dias úteis. Nossa equipe entrará em contato para combinar o melhor horário.', 'lucci-fresh'); ?>
            <?php endif; ?>
          </p>
        </div>

        <?php if ($shipping_address) : ?>
          <h3 class="c-order-confirmed__address-title"><?= esc_html__('Endereço de entrega', 'lucci-fresh'); ?></h3>
          <p class="c-order-confirmed__address">
            <?= wp_kses_post($shipping_address); ?>
          </p>
        <?php endif; ?>

        <a href="<?= esc_url(home_url('/')); ?>" class="c-pix-wait__copy-btn" style="display:block; text-align:center; text-decoration:none;">
          <?= esc_html__('Continuar comprando', 'lucci-fresh'); ?>
        </a>

        <p class="c-order-confirmed__help">
          <?= esc_html__('Precisa de ajuda com o pedido?', 'lucci-fresh'); ?>
          <a href="https://wa.me/5511960752237">(11) 96075-2237</a>
        </p>
      </section>
    </div>

    <?php $tpl_engine->partial('template/pages/checkout/order-summary-thankyou', ['order' => $order]); ?>

  </div>

</div>
