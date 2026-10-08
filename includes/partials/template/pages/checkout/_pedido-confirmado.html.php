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

/**
 * Data/turno de entrega reais vêm do plugin "Agendar Entregas"
 * (_ae_data_entrega/_ae_turno_id, ver includes/order/class-ae-order-hooks.php
 * do plugin) - não do YITH Delivery Date (ywcdd_order_delivery_date), cuja
 * integração com o checkout foi removida (ver "Desliga o YITH WooCommerce
 * Delivery Date no checkout" em extension/woocommerce.php). Só existem
 * quando esse plugin estiver ativo E o método de entrega escolhido exigir
 * agendamento (ex.: retirada na loja não pede data/turno) - nesse caso a
 * frase genérica "nossa equipe entrará em contato" não faz sentido, já que
 * o cliente escolheu o horário.
 */
$ae_data_entrega = $order->get_meta('_ae_data_entrega', true);
$ae_turno_id     = $order->get_meta('_ae_turno_id', true);
$ae_turno        = ($ae_turno_id && class_exists('AE_CPT_Turno')) ? AE_CPT_Turno::obter_turno($ae_turno_id) : null;

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
            <?php if ($ae_data_entrega && $ae_turno) : ?>
              <?= esc_html(sprintf(
                /* translators: 1: data prevista de entrega, 2: hora de início do turno, 3: hora de fim do turno */
                __('Prevista para %1$s, das %2$s às %3$s.', 'lucci-fresh'),
                wc_format_datetime(new WC_DateTime($ae_data_entrega)),
                $ae_turno->hora_inicio,
                $ae_turno->hora_fim
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

        <div class="c-order-confirmed__actions">
          <a href="<?= esc_url(home_url('/')); ?>" class="c-pix-wait__copy-btn c-order-confirmed__action">
            <?= esc_html__('Continuar comprando', 'lucci-fresh'); ?>
          </a>
          <a href="<?= esc_url(wc_get_page_permalink('myaccount')); ?>" class="c-pix-wait__copy-btn c-order-confirmed__action c-order-confirmed__action--secondary">
            <?= esc_html__('Minha Conta', 'lucci-fresh'); ?>
          </a>
        </div>

        <p class="c-order-confirmed__help">
          <?= esc_html__('Precisa de ajuda com o pedido?', 'lucci-fresh'); ?>
          <a href="https://wa.me/5511960752237">(11) 96075-2237</a>
        </p>
      </section>
    </div>

    <?php $tpl_engine->partial('template/pages/checkout/order-summary-thankyou', ['order' => $order]); ?>

  </div>

</div>
