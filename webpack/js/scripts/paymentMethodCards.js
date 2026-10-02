"use strict";

/**
 * Mesma ideia do shippingMethodCards.js (card inteiro clicável, não só
 * radio/label), mas o EVENTO disparado aqui tem que ser "click", não
 * "change" - diferente do frete.
 *
 * O WooCommerce (assets/js/frontend/checkout.js, wc_checkout_form) liga a
 * troca de método de pagamento a um listener de CLICK delegado no form
 * inteiro (`this.$checkout_form.on('click', 'input[name="payment_method"]',
 * this.payment_method_selected)`), não a um listener de "change" - é essa
 * função que atualiza o bookkeeping interno (`selectedPaymentMethod`) e
 * mostra/esconde o .payment_box certo (slideUp/slideDown). Disparar só
 * "change" (como no frete) faz o radio mudar visualmente sem o WooCommerce
 * ficar sabendo - na próxima vez que `init_payment_methods()` rodar (após
 * qualquer update_checkout, ex.: editar um campo de endereço), ele força
 * o radio de volta pro `selectedPaymentMethod` antigo, revertendo a troca
 * (o bug relatado: "muda pra Pix, volta pra Cartão sozinho").
 *
 * `input[type="radio"] { pointer-events: none }` em _checkout-v2.scss
 * garante que todo clique no card (incluindo em cima do próprio radio)
 * chegue até aqui como um único evento no <li>, sem o clique nativo do
 * input correr por conta própria e brigar com este handler.
 */
export default function paymentMethodCards() {
  function bind() {
    document.querySelectorAll(".c-checkout-step__payment ul.wc_payment_methods li.wc_payment_method").forEach((li) => {
      if (li.dataset.paymentCardBound) return;
      li.dataset.paymentCardBound = "true";

      li.addEventListener("click", (event) => {
        // Cliques em campos DENTRO do payment_box (número do cartão,
        // nome de quem vai receber etc.) não devem re-selecionar o
        // radio - só o clique no card/label em si. O <select> de tipo
        // de cartão (crédito/débito, ver payment-method.php) também fica
        // de fora: quem decide qual radio marcar ali é o próprio select
        // (checkoutSteps.js, initCardTypeSelects()), já que ele pode
        // apontar pro radio ESCONDIDO do débito, não o deste <li>.
        if (event.target.closest(".payment_box, .c-payment-method__card-type")) return;

        const input = li.querySelector('input[type="radio"]');
        if (!input || input.checked) return;

        input.checked = true;

        // Um clique real num radio dispara os dois eventos, nessa ordem -
        // replicamos ambos pra não quebrar nenhum script (WooCommerce
        // core ou de gateway) que dependa de um ou de outro.
        input.dispatchEvent(new Event("click", { bubbles: true }));
        input.dispatchEvent(new Event("change", { bubbles: true }));

        // O core do WooCommerce NÃO recalcula o carrinho ao trocar de
        // método de pagamento (só em frete/endereço - ver checkout.js,
        // trigger_update_checkout só está ligado a shipping_method/
        // endereço/.update_totals_on_change, nunca a input[name=
        // payment_method]). Essa loja tem um desconto de 5% que depende do
        // método escolhido (woocommerce_cart_calculate_fees, ver
        // extension/woocommerce.php) - sem forçar esse recálculo aqui, o
        // resumo do pedido continua mostrando o desconto do Pix mesmo
        // depois de trocar para cartão, E os campos ocultos de total do
        // gateway de cartão (ex.: erede_api[total_rede]/hash_total_rede,
        // embutidos no HTML do método de pagamento, recalculados só
        // quando o fragmento é re-renderizado) ficam com o valor ERRADO
        // (o total com desconto Pix) - a gateway rejeita a cobrança por
        // isso não bater com o total real do pedido, e a compra no cartão
        // nunca finaliza.
        if (window.jQuery) {
          window.jQuery(document.body).trigger("update_checkout");
        }
      });
    });
  }

  bind();

  const target = document.querySelector("[data-checkout-wizard]") || document.body;
  const observer = new MutationObserver(bind);
  observer.observe(target, { childList: true, subtree: true });
}
