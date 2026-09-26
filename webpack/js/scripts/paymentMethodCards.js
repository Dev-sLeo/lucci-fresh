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
        // radio - só o clique no card/label em si.
        if (event.target.closest(".payment_box")) return;

        const input = li.querySelector('input[type="radio"]');
        if (!input || input.checked) return;

        input.checked = true;

        // Um clique real num radio dispara os dois eventos, nessa ordem -
        // replicamos ambos pra não quebrar nenhum script (WooCommerce
        // core ou de gateway) que dependa de um ou de outro.
        input.dispatchEvent(new Event("click", { bubbles: true }));
        input.dispatchEvent(new Event("change", { bubbles: true }));
      });
    });
  }

  bind();

  const target = document.querySelector("[data-checkout-wizard]") || document.body;
  const observer = new MutationObserver(bind);
  observer.observe(target, { childList: true, subtree: true });
}
