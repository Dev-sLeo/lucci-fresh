<?php
/**
 * Step 2 - Entrega (endereço + método de frete + data YITH + observações).
 *
 * O método de frete normalmente é renderizado dentro do resumo do pedido
 * (order review), mas o Figma pede a lista de fretes aqui, junto ao
 * endereço - por isso chamamos wc_cart_totals_shipping_html() diretamente,
 * igual o Fluid Checkout faz para montar seu próprio step de entrega.
 *
 * do_action('woocommerce_checkout_shipping') continua sendo chamado para
 * preservar o hook onde o YITH Delivery Date imprime o datepicker (ver
 * docs/woocommerce-customizations.md) e o campo "ship to different address"
 * (renderizado oculto/sem uso visual - loja não oferece endereço de entrega
 * diferente do de cobrança).
 *
 * O template nativo carregado por esse hook (woocommerce/checkout/
 * form-shipping.php, sem override no tema) também imprime os campos do
 * grupo "order" (order_comments) dentro de .woocommerce-additional-fields -
 * duplicando o campo "Observações para entrega" que este arquivo já
 * renderiza manualmente mais abaixo, no lugar certo do layout do Figma
 * (depois do método de entrega, não entre o endereço e o frete). Suprimimos
 * esse campo só durante essa chamada específica.
 */
defined('ABSPATH') || exit;

$checkout     = WC()->checkout();
$order_fields = $checkout->get_checkout_fields('order');
$billing_email = $checkout->get_value('billing_email');
?>

<section class="c-checkout-step" data-checkout-step="2" aria-label="<?= esc_attr__('Entrega', 'lucci-fresh'); ?>">
  <h2 class="c-checkout-step__title"><?= esc_html__('Onde vamos entregar?', 'lucci-fresh'); ?></h2>

  <?php if ($billing_email) : ?>
    <p class="c-checkout-step__confirmed">
      <span>✓ <?= esc_html($billing_email); ?></span>
      <button type="button" data-checkout-edit="1"><?= esc_html__('Alterar', 'lucci-fresh'); ?></button>
    </p>
  <?php endif; ?>

  <div class="c-checkout-step__fields">
    <div class="c-checkout-step__row c-checkout-step__row--address">
      <?php luccifresh_render_billing_field_group(['billing_address_1', 'billing_number']); ?>
    </div>
    <?php luccifresh_render_billing_field_group(['billing_address_2']); ?>
    <div class="c-checkout-step__row c-checkout-step__row--cep">
      <?php luccifresh_render_billing_field_group(['billing_postcode', 'billing_neighborhood']); ?>
    </div>
    <?php luccifresh_render_billing_field_group(['billing_city', 'billing_state', 'billing_country']); ?>
  </div>

  <?php
  add_filter('woocommerce_enable_order_notes_field', '__return_false', 20);
  do_action('woocommerce_checkout_shipping');
  remove_filter('woocommerce_enable_order_notes_field', '__return_false', 20);
  ?>

  <hr class="c-checkout-step__divider" />

  <h3 class="c-checkout-step__section-title"><?= esc_html__('Método de entrega', 'lucci-fresh'); ?></h3>
  <div class="c-checkout-step__shipping-methods fc-shipping-method__packages">
    <?php
    /**
     * cart/cart-shipping.php (WC core, sem override no tema) monta
     * <tr><th>Envio</th><td><ul>...</ul></td></tr>. Como este container é
     * uma <div> e não uma <table>, o parser HTML do navegador descarta as
     * tags <tr>/<th>/<td> por não serem válidas fora de uma tabela - mas
     * mantém o TEXTO "Envio" como um nó solto (por isso `display:none` no
     * seletor `th` não tem efeito nenhum: o elemento já nem existe no DOM
     * renderizado). O Figma não usa esse texto, então o nome do pacote é
     * zerado via filtro `woocommerce_shipping_package_name` (registrado em
     * extension/woocommerce.php, só ativo no checkout novo) - não dá pra
     * filtrar aqui porque WC()->cart já calcula e cacheia os pacotes bem
     * antes deste template rodar; um add_filter/remove_filter neste ponto
     * chegaria tarde demais.
     */
    wc_cart_totals_shipping_html();
    ?>
  </div>

  <hr class="c-checkout-step__divider" />

  <?php if (isset($order_fields['order_comments'])) : ?>
    <div class="c-checkout-step__fields">
      <?php woocommerce_form_field('order_comments', $order_fields['order_comments'], $checkout->get_value('order_comments')); ?>
    </div>
  <?php endif; ?>

  <button type="button" class="c-btn--checkout-next" data-checkout-next="3">
    <?= esc_html__('Continuar para pagamento', 'lucci-fresh'); ?>
  </button>
</section>
