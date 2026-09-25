<?php
/**
 * Override do widget de QR Code/copia-e-cola do plugin woo-asaas
 * (templates/order/pix-thankyou.php), usado na tela "Aguardando Pix"
 * (Figma node 2106:1420 - ver _pix-aguardando.html.php).
 *
 * Mantém os dados reais (QR/payload vindos da API Asaas, via $order->
 * get_meta_data()) e as classes que o JS do plugin usa para o botão de
 * copiar (assets/src/store/js/components/copy-to-clipboard.js lê
 * .woocommerce-order-details__asaas-pix-button/-code por querySelector),
 * só o HTML/CSS em volta é redesenhado pra bater com o Figma.
 *
 * @var \WC_Asaas\Meta_Data\Order $order
 * @var bool $show_copy_and_paste
 */

defined('ABSPATH') || exit;

$data = $order->get_meta_data();

if (false === $data) {
    $wc_order = $order->get_wc();
    $total    = $wc_order->get_total();
    $message  = $total <= 0
        ? esc_html__('This order does not require payment at this time.', 'woo-asaas')
        : esc_html__('Unable to load payment details.', 'woo-asaas');
    wc_print_notice($message, 'notice');
    return;
}
?>

<div class="c-pix-wait__widget">

  <div class="c-pix-wait__widget-top">
    <img
      class="c-pix-wait__qr-image js-pix-qr-code"
      src="data:image/jpeg;base64,<?php echo esc_attr($data->encodedImage); ?>"
      alt="<?= esc_attr__('QR Code Pix', 'lucci-fresh'); ?>"
    >

    <div class="c-pix-wait__widget-info">
      <p class="c-pix-wait__amount-label"><?= esc_html__('Total a pagar', 'lucci-fresh'); ?></p>
      <p class="c-pix-wait__amount"><?php echo wp_kses_post(wc_price($data->value)); ?></p>
      <p class="c-pix-wait__widget-hint"><?= esc_html__('Abra o aplicativo do banco e escaneie o QR Code.', 'lucci-fresh'); ?></p>
    </div>
  </div>

  <?php if (true === $show_copy_and_paste) : ?>
    <input
      class="woocommerce-order-details__asaas-pix-code"
      type="hidden"
      value="<?php echo esc_attr($data->payload); ?>"
    >
    <button
      class="c-pix-wait__copy-btn woocommerce-order-details__asaas-pix-button"
      data-success-copy="<?= esc_attr__('Código copiado para a área de transferência', 'lucci-fresh'); ?>"
    >
      <?= esc_html__('Copiar código Pix', 'lucci-fresh'); ?>
    </button>
  <?php endif; ?>

</div>
