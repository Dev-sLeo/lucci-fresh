"use strict";

/**
 * O Fluid Checkout (js-src/checkout.js, função update_checkout_action) lê a
 * flag global `window.can_update_payment_methods` (default: true) pra
 * decidir se o bloco `.woocommerce-checkout-payment` entra na próxima
 * chamada AJAX de `update_checkout` - e ele dispara isso a CADA campo de
 * endereço editado (debounce de 1s, ver queue_update_checkout/
 * maybe_update_checkout no mesmo arquivo), não só quando o método de frete
 * muda de verdade.
 *
 * Recarregar o bloco de pagamento é caro aqui: os gateways reinicializam
 * JS pesado a cada vez (animação do cartão da Rede, reconexão do Pix...),
 * e como qualquer letra digitada no CEP/endereço já dispara isso, o
 * checkout fica lento em produção. O próprio Fluid Checkout já usa esse
 * mesmo padrão de ligar/desligar a flag pro compat do gateway Rvvup
 * (js-src/compat/plugins/rvvup-for-woocommerce/rvvup-checkout.js) -
 * replicamos aqui: fica desligada por padrão e só liga de novo quando o
 * cliente troca o método de frete/retirada (o único campo que realmente
 * pode mudar quais formas de pagamento são exibidas) ou aplica/remove um
 * cupom (pode liberar desconto Pix, frete grátis etc.).
 *
 * Não mexe em nada do lado do checkout novo (data-checkout-wizard) - esse
 * layout já reposiciona o payment_box via paymentMethodLayout.js e não
 * depende do fragment-refresh do Fluid Checkout pra nada relacionado a
 * pagamento.
 */
export default function fluidCheckoutPaymentThrottle() {
  if (typeof jQuery === "undefined") return;
  if (!document.body.classList.contains("woocommerce-checkout")) return;
  if (document.querySelector("[data-checkout-wizard]")) return;

  const $ = jQuery;

  window.can_update_payment_methods = false;

  const allowNextPaymentRefresh = () => {
    window.can_update_payment_methods = true;
  };

  $(document.body).on(
    "change",
    'input[name^="shipping_method"], select.shipping_method',
    allowNextPaymentRefresh
  );
  $(document.body).on("applied_coupon removed_coupon", allowNextPaymentRefresh);

  // Depois que a resposta do update_checkout chega, volta a bloquear -
  // sem isso, qualquer update_checkout seguinte (ex.: próxima letra digitada
  // no CEP) herdaria a flag ligada e voltaria a recarregar o pagamento.
  $(document.body).on("updated_checkout", () => {
    window.can_update_payment_methods = false;
  });
}
