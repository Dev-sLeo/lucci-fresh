"use strict";

/**
 * Cada <li> de método de frete (ver step-entrega.html.php /
 * wc_cart_totals_shipping_html()) só tem o radio + <label> realmente
 * clicáveis nativamente - a descrição do método ("Após a confirmação...",
 * o endereço da retirada) vem como um <p>/<span> IRMÃO do <label>, fora
 * dele, então clicar ali não marca o radio. Visualmente o card inteiro
 * parece clicável (mesmo fundo/borda do CSS), então o clique precisa
 * funcionar em qualquer parte do <li>, não só em cima do texto do título.
 *
 * `input { pointer-events: none }` em _checkout-v2.scss garante que todo
 * clique no card (incluindo em cima do próprio radio) chegue até aqui como
 * um único evento no <li>, sem o clique nativo do input correr por conta
 * própria e brigar com este handler.
 */
export default function shippingMethodCards() {
  function bind() {
    document.querySelectorAll(".c-checkout-step__shipping-methods li").forEach((li) => {
      if (li.dataset.shippingCardBound) return;
      li.dataset.shippingCardBound = "true";

      li.addEventListener("click", () => {
        const input = li.querySelector('input[type="radio"]');
        if (!input || input.checked) return;

        input.checked = true;
        input.dispatchEvent(new Event("change", { bubbles: true }));
      });
    });
  }

  bind();

  const target = document.querySelector("[data-checkout-wizard]") || document.body;
  const observer = new MutationObserver(bind);
  observer.observe(target, { childList: true, subtree: true });
}
