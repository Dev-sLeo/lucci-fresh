"use strict";

/**
 * Botão "Copiar código Pix" (tela "Aguardando Pix", Figma node 2106:1420 -
 * ver _pix-aguardando.html.php e o override de tema woocommerce/asaas/
 * order/pix-thankyou.php).
 *
 * O plugin woo-asaas já tem seu próprio listener pra esse botão
 * (assets/src/store/js/components/copy-to-clipboard.js), mas ele usa só
 * `navigator.clipboard.writeText` - que não existe fora de um contexto
 * seguro (https ou localhost). Em ambientes http (ex.: *.local no dev),
 * `navigator.clipboard` é `undefined` e o clique não faz nada, sem erro
 * visível pro usuário. Aqui a gente registra o próprio listener com
 * fallback (textarea temporário + document.execCommand('copy')) e feedback
 * visual (classe is-copied, some 2s depois).
 */
export default function pixCopyCode() {
  const button = document.querySelector(".woocommerce-order-details__asaas-pix-button");
  if (!button) return;

  const input = document.querySelector(".woocommerce-order-details__asaas-pix-code");
  if (!input) return;

  const defaultText = button.textContent;
  const successText = button.getAttribute("data-success-copy") || defaultText;

  const copyFallback = (text) => {
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.setAttribute("readonly", "");
    textarea.style.position = "fixed";
    textarea.style.opacity = "0";
    document.body.appendChild(textarea);
    textarea.select();
    textarea.setSelectionRange(0, textarea.value.length);

    let copied = false;
    try {
      copied = document.execCommand("copy");
    } catch (e) {
      copied = false;
    }

    document.body.removeChild(textarea);
    return copied;
  };

  const showSuccess = () => {
    button.textContent = successText;
    button.classList.add("is-copied");
    setTimeout(() => {
      button.textContent = defaultText;
      button.classList.remove("is-copied");
    }, 3000);
  };

  button.addEventListener("click", (event) => {
    event.preventDefault();

    const value = input.value;

    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard
        .writeText(value)
        .then(showSuccess)
        .catch(() => {
          if (copyFallback(value)) showSuccess();
        });
      return;
    }

    if (copyFallback(value)) showSuccess();
  });
}
