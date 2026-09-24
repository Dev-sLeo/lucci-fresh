<?php
/**
 * Step 4 - Revisão. Resumo somente-leitura preenchido via JS a partir dos
 * campos já digitados nos steps 1-3 (ver checkoutSteps.js) - não duplica
 * nenhuma validação/dado, só lê o DOM do form real. O botão final aqui só
 * aciona o #place_order de verdade, que fica no step de Pagamento (onde o
 * template padrão do WooCommerce checkout/payment.php já o renderiza).
 */
defined('ABSPATH') || exit;
?>

<section class="c-checkout-step" data-checkout-step="4" aria-label="<?= esc_attr__('Revisão', 'lucci-fresh'); ?>">
  <h2 class="c-checkout-step__title"><?= esc_html__('Confira antes de finalizar', 'lucci-fresh'); ?></h2>
  <p class="c-checkout-step__subtitle"><?= esc_html__('Está tudo certo? Revise seus dados e confirme o pedido.', 'lucci-fresh'); ?></p>

  <div class="c-checkout-review" data-checkout-review>
    <div class="c-checkout-review__section" data-checkout-review-section="1">
      <div class="c-checkout-review__section-title">
        <p><?= esc_html__('Seus dados', 'lucci-fresh'); ?></p>
        <button type="button" data-checkout-edit="1"><?= esc_html__('Alterar', 'lucci-fresh'); ?></button>
      </div>
      <div class="c-checkout-review__section-content" data-checkout-review-content></div>
    </div>

    <div class="c-checkout-step__divider"></div>

    <div class="c-checkout-review__section" data-checkout-review-section="2">
      <div class="c-checkout-review__section-title">
        <p><?= esc_html__('Entrega', 'lucci-fresh'); ?></p>
        <button type="button" data-checkout-edit="2"><?= esc_html__('Alterar', 'lucci-fresh'); ?></button>
      </div>
      <div class="c-checkout-review__section-content" data-checkout-review-content></div>
    </div>

    <div class="c-checkout-step__divider"></div>

    <div class="c-checkout-review__section" data-checkout-review-section="3">
      <div class="c-checkout-review__section-title">
        <p><?= esc_html__('Pagamento', 'lucci-fresh'); ?></p>
        <button type="button" data-checkout-edit="3"><?= esc_html__('Alterar', 'lucci-fresh'); ?></button>
      </div>
      <div class="c-checkout-review__section-content" data-checkout-review-content></div>
    </div>

    <div class="c-checkout-step__divider"></div>

    <div class="c-checkout-review__total">
      <p><?= esc_html__('Total do pedido', 'lucci-fresh'); ?></p>
      <strong data-checkout-review-total></strong>
    </div>
  </div>

  <p class="c-checkout-step__privacy">
    <?= esc_html__('Ao confirmar, você concorda com os Termos de compra e a Política de privacidade.', 'lucci-fresh'); ?>
  </p>

  <button type="button" class="c-btn--checkout-next" data-checkout-place-order>
    <?= esc_html__('Confirmar pedido', 'lucci-fresh'); ?>
  </button>
</section>
