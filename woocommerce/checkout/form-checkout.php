<?php
/**
 * Checkout Page - Página de finalização de compra
 */
defined('ABSPATH') || exit;

global $tpl_engine;

if (!is_user_logged_in()) {
  $checkout = WC()->checkout();
  if ('yes' === get_option('woocommerce_enable_guest_checkout') || $checkout->is_registration_required()) {
    // proceed
  }
}
?>

<?php if (function_exists('luccifresh_new_checkout_enabled') && luccifresh_new_checkout_enabled()) : ?>

  <?php $tpl_engine->partial('template/pages/checkout/wizard'); ?>

<?php else : ?>

  <div class="p-checkout">
    <div class="s-container">

      <?php do_action('woocommerce_before_checkout_form', WC()->checkout()); ?>

      <?php if (!WC()->cart->is_empty()) : ?>

        <form name="checkout" method="post" class="checkout woocommerce-checkout p-checkout__form"
              action="<?= esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

          <div class="p-checkout__cols">

            <div class="p-checkout__billing">
              <?php do_action('woocommerce_checkout_before_customer_details'); ?>
              <div id="customer_details">
                <?php do_action('woocommerce_checkout_billing'); ?>
                <?php do_action('woocommerce_checkout_shipping'); ?>
              </div>
              <?php do_action('woocommerce_checkout_after_customer_details'); ?>
            </div>

            <div class="p-checkout__order-review">
              <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
              <h3 id="order_review_heading"><?= esc_html__('Seu pedido', 'lucci-fresh'); ?></h3>
              <?php do_action('woocommerce_checkout_before_order_review'); ?>
              <div id="order_review" class="woocommerce-checkout-review-order">
                <?php do_action('woocommerce_checkout_order_review'); ?>
              </div>
              <?php do_action('woocommerce_checkout_after_order_review'); ?>
            </div>

          </div>

        </form>

      <?php else : ?>
        <p class="woocommerce-info">
          <?= esc_html__('Seu carrinho está vazio. Adicione produtos antes de finalizar a compra.', 'lucci-fresh'); ?>
          <a href="<?= esc_url(wc_get_page_permalink('shop')); ?>" class="c-btn c-btn--primary">
            <?= esc_html__('Ir para a loja', 'lucci-fresh'); ?>
          </a>
        </p>
      <?php endif; ?>

      <?php do_action('woocommerce_after_checkout_form', WC()->checkout()); ?>

    </div>
  </div>

<?php endif; ?>
