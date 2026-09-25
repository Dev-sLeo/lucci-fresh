<?php
/**
 * Tela "Aguardando Pix" (Figma node 2106:1420) - aparece na página de
 * pedido recebido (thank you) quando o método de pagamento é Pix e o
 * pedido ainda não foi pago (ver woocommerce/checkout/thankyou.php).
 *
 * O gateway Pix desta loja é o "Asaas Pix" (plugin woo-asaas, id de
 * pagamento "asaas-pix"). O QR Code, o payload "copia e cola" e o botão de
 * copiar usam os dados REAIS do plugin (WC_Asaas\Meta_Data\Order::
 * get_meta_data(), que espelha a resposta da API Asaas salva no pedido) -
 * o HTML deles vem de um override de tema do template do próprio plugin
 * (woocommerce/asaas/order/pix-thankyou.php) redesenhado pro Figma, mas
 * mantendo as classes que o JS de copiar (copy-to-clipboard.js) usa.
 *
 * O método público do gateway (WC_Asaas\Gateway\Pix::
 * append_html_to_thankyou_page(), hook woocommerce_thankyou_asaas-pix) não
 * é usado diretamente aqui: em teste ele não produz nenhuma saída nesta
 * página (mesmo com a condição de pagamento batendo), então chamamos o
 * template do plugin diretamente com os mesmos dados, replicando o que
 * esse método faz.
 *
 * Nota sobre o texto do Figma: o design original tem "Pague em até 15:00"
 * (contagem regressiva) e "assim que o pagamento for aprovado, esta página
 * será atualizada automaticamente". A confirmação de pagamento do Asaas
 * depende de webhook, então não é instantânea - mas a página FICA se
 * atualizando sozinha: webpack/js/scripts/pixPaymentPoll.js consulta o
 * status do pedido em intervalos (data-pix-wait abaixo) e recarrega assim
 * que o webhook marcar o pagamento como recebido.
 *
 * @var WC_Order $order
 */
defined('ABSPATH') || exit;

global $tpl_engine;
?>

<div
  class="p-checkout-v2"
  data-pix-wait
  data-pix-wait-order-id="<?= (int) $order->get_id(); ?>"
  data-pix-wait-order-key="<?= esc_attr($order->get_order_key()); ?>"
>

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
          <?php
          if (class_exists('\WC_Asaas\Meta_Data\Order') && class_exists('\WC_Asaas\WC_Asaas')) {
              $luccifresh_asaas_order = new \WC_Asaas\Meta_Data\Order($order->get_id());

              if (false !== $luccifresh_asaas_order->get_meta_data()) {
                  $luccifresh_show_copy = true;

                  $luccifresh_gateways = WC()->payment_gateways()->payment_gateways();
                  $luccifresh_gw = $luccifresh_gateways['asaas-pix'] ?? null;

                  if ($luccifresh_gw instanceof \WC_Asaas\Gateway\Pix) {
                      $luccifresh_show_copy_method = new \ReflectionMethod($luccifresh_gw, 'show_copy_and_paste');
                      $luccifresh_show_copy_method->setAccessible(true);
                      $luccifresh_show_copy = $luccifresh_show_copy_method->invoke($luccifresh_gw);
                  }

                  echo \WC_Asaas\WC_Asaas::get_instance()->get_template_file(
                      'order/pix-thankyou.php',
                      array(
                          'order' => $luccifresh_asaas_order,
                          'show_copy_and_paste' => $luccifresh_show_copy,
                      ),
                      true
                  );
              }
          }
          ?>
        </div>

        <div class="c-pix-wait__notice">
          <p class="c-pix-wait__notice-title"><?= esc_html__('Aguardando confirmação', 'lucci-fresh'); ?></p>
          <p class="c-pix-wait__notice-text">
            <?= esc_html__('Assim que o pagamento for identificado, esta página será atualizada automaticamente.', 'lucci-fresh'); ?>
          </p>
        </div>

        <ol class="c-pix-wait__steps">
          <li><?= esc_html__('Escolha Pix no aplicativo do seu banco.', 'lucci-fresh'); ?></li>
          <li><?= esc_html__('Escaneie o QR Code ou use Pix Copia e Cola.', 'lucci-fresh'); ?></li>
          <li><?= esc_html__('Confira o valor e confirme o pagamento.', 'lucci-fresh'); ?></li>
        </ol>

        <a href="<?= esc_url($order->get_checkout_payment_url()); ?>" class="c-checkout-step__helper" style="display:inline-block;">
          <?= esc_html__('Escolher outra forma de pagamento', 'lucci-fresh'); ?>
        </a>
      </section>
    </div>

    <?php $tpl_engine->partial('template/pages/checkout/order-summary-thankyou', ['order' => $order]); ?>

  </div>

</div>
