<?php
/**
 * Tela "Aguardando Pix" (Figma node 2106:1420) - aparece na página de
 * pedido recebido (thank you) quando o método de pagamento é Pix e o
 * pedido ainda não foi pago (ver woocommerce/checkout/thankyou.php).
 *
 * O QR Code, o código "copia e cola" e o botão de copiar são o widget REAL
 * do plugin Payment Gateway Pix for WooCommerce (classe
 * LknPaymentPixForWoocommercePix::showPix(), hook
 * woocommerce_order_details_after_order_table) - não são recriados aqui,
 * só chamados no lugar certo do layout. Isso preserva a geração real do
 * payload Pix (chave, valor, cidade etc. configurados no plugin).
 *
 * Nota sobre o texto do Figma: o design original tem "Pague em até 15:00"
 * (contagem regressiva) e "assim que o pagamento for aprovado, esta página
 * será atualizada automaticamente". O gateway Pix configurado nesta loja
 * (chave estática, sem webhook bancário) não tem prazo de expiração nem
 * confirmação automática - o próprio plugin avisa que a confirmação é
 * manual ("contate a loja e envie o comprovante"). Por isso o texto aqui
 * foi ajustado para não prometer algo que o sistema não cumpre.
 *
 * @var WC_Order $order
 */
defined('ABSPATH') || exit;

global $tpl_engine;
?>

<div class="p-checkout-v2">

  <a href="<?= esc_url(home_url('/')); ?>" class="p-checkout-v2__back">
    <?= esc_html__('← Voltar', 'lucci-fresh'); ?>
  </a>

  <div class="p-checkout-v2__intro">
    <h1><?= esc_html__('Seu pedido está quase pronto.', 'lucci-fresh'); ?></h1>
    <p><?= esc_html__('Falta apenas o pagamento para confirmar sua compra.', 'lucci-fresh'); ?></p>
  </div>

  <?php $tpl_engine->partial('template/pages/checkout/progress', ['all_done' => true]); ?>

  <div class="p-checkout-v2__cols">

    <div class="p-checkout-v2__steps">
      <section class="c-checkout-step is-active" aria-label="<?= esc_attr__('Pague com Pix', 'lucci-fresh'); ?>">
        <h2 class="c-checkout-step__title"><?= esc_html__('Pague com Pix', 'lucci-fresh'); ?></h2>
        <p class="c-checkout-step__subtitle">
          <?= esc_html(sprintf(
            /* translators: %s: número do pedido */
            __('Pedido #%s • aguardando pagamento', 'lucci-fresh'),
            $order->get_order_number()
          )); ?>
        </p>

        <div class="c-pix-wait__qr">
          <?php do_action('woocommerce_order_details_after_order_table', $order); ?>
        </div>

        <div class="c-pix-wait__notice">
          <p class="c-pix-wait__notice-title"><?= esc_html__('Aguardando confirmação', 'lucci-fresh'); ?></p>
          <p class="c-pix-wait__notice-text">
            <?= esc_html__('Depois de pagar, envie o comprovante para a gente confirmar seu pedido o quanto antes.', 'lucci-fresh'); ?>
          </p>
        </div>

        <a href="<?= esc_url($order->get_checkout_payment_url()); ?>" class="c-checkout-step__helper" style="display:inline-block;">
          <?= esc_html__('Escolher outra forma de pagamento', 'lucci-fresh'); ?>
        </a>
      </section>
    </div>

    <?php $tpl_engine->partial('template/pages/checkout/order-summary-thankyou', ['order' => $order]); ?>

  </div>

</div>
