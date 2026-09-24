<?php
/**
 * Step 1 - Seus dados (contato). Mesmos campos/validações do WooCommerce
 * (ver luccifresh_render_billing_field_group() em extension/woocommerce.php).
 *
 * O Figma pede um único campo "Nome completo", mas o WooCommerce grava
 * nome/sobrenome em campos separados (usados em pedidos, notas fiscais,
 * etiquetas de entrega e na listagem de pedidos do admin) - por isso aqui
 * ficam como dois campos lado a lado (mesmo padrão visual do par
 * Celular/CPF) em vez de forçar um único campo que precisaria ser dividido
 * via JS antes do envio, arriscando perder dados em nomes compostos.
 */
defined('ABSPATH') || exit;

$checkout = WC()->checkout();
// O Figma não tem campo de senha em "Seus dados" (assume guest checkout),
// mas esta loja está com "Permitir checkout como visitante" desligado nas
// configurações do WooCommerce - is_registration_required() vem true, e
// sem esse campo em algum lugar do formulário o pedido nunca passa da
// validação (erro "Criar uma senha para sua conta é um campo obrigatório",
// campo #account_password inexistente no DOM - checkout/form-billing.php,
// de onde ele normalmente viria, nunca é chamado neste wizard custom).
// Some sozinho assim que "Permitir checkout como visitante" for reativado.
$luccifresh_account_fields = (!is_user_logged_in() && $checkout->is_registration_enabled())
    ? $checkout->get_checkout_fields('account')
    : [];
?>

<section class="c-checkout-step is-active" data-checkout-step="1" aria-label="<?= esc_attr__('Seus dados', 'lucci-fresh'); ?>">
  <h2 class="c-checkout-step__title"><?= esc_html__('Seus dados', 'lucci-fresh'); ?></h2>
  <p class="c-checkout-step__subtitle"><?= esc_html__('Informe seus dados para receber as atualizações do pedido.', 'lucci-fresh'); ?></p>

  <div class="c-checkout-step__fields">
    <div class="c-checkout-step__row">
      <?php luccifresh_render_billing_field_group(['billing_first_name', 'billing_last_name']); ?>
    </div>
    <?php luccifresh_render_billing_field_group(['billing_email']); ?>
    <div class="c-checkout-step__row">
      <?php luccifresh_render_billing_field_group(['billing_phone', 'billing_cpf']); ?>
    </div>
    <?php foreach ($luccifresh_account_fields as $key => $field) : ?>
      <?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
    <?php endforeach; ?>
  </div>

  <p class="c-checkout-step__helper">
    <?= esc_html__('Usaremos seus dados para processar a compra e informar sobre a entrega.', 'lucci-fresh'); ?>
  </p>

  <button type="button" class="c-btn--checkout-next" data-checkout-next="2">
    <?= esc_html__('Continuar para entrega', 'lucci-fresh'); ?>
  </button>

  <p class="c-checkout-step__privacy">
    <?= esc_html__('Ao continuar, você concorda com nossa Política de privacidade.', 'lucci-fresh'); ?>
  </p>
</section>
