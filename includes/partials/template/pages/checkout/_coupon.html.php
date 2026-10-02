<?php
/**
 * Campo de cupom - faltava no checkout novo (o tema removeu
 * woocommerce_checkout_coupon_form do hook padrão, ver extension/woocommerce.php,
 * seção "Layout & estrutura": o link "Tem um cupom?" + form escondido/toggle
 * do core não existe no Figma). Esta é uma versão própria, sempre visível
 * (sem toggle), com a mesma cor do card "Seu pedido" logo abaixo.
 *
 * <div>, NÃO <form>: este bloco é renderizado dentro do grid do checkout
 * (.p-checkout-v2__cols), que já está dentro do <form name="checkout">
 * principal do wizard inteiro - um <form> aninhado dentro de outro é HTML
 * inválido, e o navegador descarta silenciosamente a tag de dentro ao
 * parsear (confirmado: o elemento simplesmente não existe no DOM
 * renderizado). Por isso o AJAX de aplicar cupom é feito via JS próprio do
 * tema (webpack/js/scripts/checkoutCoupon.js), não pelo listener de
 * `submit` nativo do WooCommerce (wc_checkout_coupons, assets/js/frontend/
 * checkout.js), que só funciona com um <form> de verdade.
 */
defined('ABSPATH') || exit;

if (!wc_coupons_enabled()) {
    return;
}
?>

<div class="p-checkout-v2__coupon">
  <div class="p-checkout-v2__coupon-form">
    <label for="coupon_code" class="screen-reader-text"><?= esc_html__('Cupom:', 'lucci-fresh'); ?></label>
    <input type="text" name="coupon_code" id="coupon_code" class="input-text" placeholder="<?= esc_attr__('Código do cupom', 'lucci-fresh'); ?>" />
    <button type="button" class="p-checkout-v2__coupon-apply">
      <?= esc_html__('Aplicar', 'lucci-fresh'); ?>
    </button>
  </div>
</div>
