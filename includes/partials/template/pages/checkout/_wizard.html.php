<?php
/**
 * Checkout custom nativo do tema (substitui o Fluid Checkout).
 * Layout baseado no Figma (arquivo elbG69dWuejODeEe5sekj7, node 2106:770).
 *
 * Sem wrapper .s-container próprio aqui de propósito: page.php já envolve
 * o_content() com <section class="singular-page"><div class="s-container">,
 * então um segundo .s-container aqui duplicaria o padding lateral.
 */
global $tpl_engine;

defined('ABSPATH') || exit;
?>

<div class="p-checkout-v2">

  <a href="<?= esc_url(wc_get_cart_url()); ?>" class="p-checkout-v2__back">
    <?= esc_html__('← Voltar', 'lucci-fresh'); ?>
  </a>

  <div class="p-checkout-v2__intro">
    <h1><?= esc_html__('Falta pouco para saborear.', 'lucci-fresh'); ?></h1>
    <p><?= esc_html__('Finalize seu pedido com praticidade e receba o sabor da Lucci em casa.', 'lucci-fresh'); ?></p>
  </div>

  <?php $tpl_engine->partial('template/pages/checkout/progress'); ?>

  <?php do_action('woocommerce_before_checkout_form', WC()->checkout()); ?>

  <?php if (WC()->cart->is_empty()) : ?>

    <p class="woocommerce-info">
      <?= esc_html__('Seu carrinho está vazio. Adicione produtos antes de finalizar a compra.', 'lucci-fresh'); ?>
      <a href="<?= esc_url(wc_get_page_permalink('shop')); ?>" class="c-btn c-btn--primary">
        <?= esc_html__('Ir para a loja', 'lucci-fresh'); ?>
      </a>
    </p>

  <?php else : ?>

    <form name="checkout" method="post" class="checkout woocommerce-checkout p-checkout-v2__form"
          action="<?= esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data"
          data-checkout-wizard>

      <div class="p-checkout-v2__cols">

        <div class="p-checkout-v2__steps" id="customer_details">

          <?php $tpl_engine->partial('template/pages/checkout/step-dados'); ?>
          <?php $tpl_engine->partial('template/pages/checkout/step-entrega'); ?>
          <?php $tpl_engine->partial('template/pages/checkout/step-pagamento'); ?>
          <?php $tpl_engine->partial('template/pages/checkout/step-revisao'); ?>

        </div>

        <?php $tpl_engine->partial('template/pages/checkout/order-summary'); ?>

      </div>

    </form>

  <?php endif; ?>

  <?php do_action('woocommerce_after_checkout_form', WC()->checkout()); ?>

</div>
