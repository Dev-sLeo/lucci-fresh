"use strict";

/**
 * O WooCommerce imprime o conteúdo específico de cada gateway (.payment_box
 * - campos de cartão, texto do Pix/COD) DENTRO do <li> de cada método,
 * junto do radio/label. No checkout novo os 3 métodos viram cards lado a
 * lado (ver .c-checkout-step__payment em _checkout-v2.scss) - isso deixa
 * o .payment_box preso na largura estreita do card (~214px) em vez de
 * ocupar a largura inteira do formulário, como no Figma.
 *
 * O toggle de mostrar/esconder o .payment_box certo ao trocar o gateway
 * (assets/js/frontend/checkout.js do WC core) usa seletor por CLASSE
 * (`div.payment_box.payment_method_xxx`), não por posição no DOM - então é
 * seguro mover esses elementos para fora do <li>, como irmãos depois da
 * <ul>, sem quebrar esse toggle nativo.
 *
 * A lista de métodos (ul.wc_payment_methods) não está garantida no DOM no
 * momento em que este script roda - o WooCommerce Blocks (script/CSS dele
 * carregam na página mesmo com o checkout clássico forçado via
 * luccifresh_new_checkout_enabled(), ver extension/woocommerce.php) mexe no
 * DOM da página de checkout de formas que não dá pra prever com certeza.
 * Por segurança, observamos o <form> do checkout (não o body inteiro -
 * reduz o raio de efeito e evita mexer com widgets fora do form, como o
 * datepicker da YITH, que também observa mutações e fica de boca aberta
 * pro que muda perto dele) em vez de assumir que os elementos já existem
 * numa chamada síncrona única.
 */
export default function paymentMethodLayout() {
  function relocate() {
    const ul = document.querySelector(".c-checkout-step__payment ul.wc_payment_methods");
    if (!ul) return;

    ul.querySelectorAll(":scope > li > .payment_box").forEach((box) => {
      ul.insertAdjacentElement("afterend", box);
    });
  }

  relocate();

  const target = document.querySelector("[data-checkout-wizard]") || document.body;
  const observer = new MutationObserver(relocate);
  observer.observe(target, { childList: true, subtree: true });
}
