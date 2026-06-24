"use strict";

/**
 * Toggle "Enviar" / "Retirar na loja" do Checkout Block: o WooCommerce sempre
 * marca uma opção como selecionada por padrão (geralmente "Enviar"). Para não
 * induzir o cliente, removemos visualmente essa seleção até que ele clique
 * em uma das opções.
 */
export default function shippingMethodToggle() {
  let userChosen = false;

  function getGroup() {
    return document.querySelector('#shipping-method[role="radiogroup"]');
  }

  function clearSelection() {
    if (userChosen) return;
    const group = getGroup();
    if (!group) return;

    group.querySelectorAll('[role="radio"]').forEach((option) => {
      option.setAttribute("aria-checked", "false");
      option.classList.remove(
        "wc-block-checkout__shipping-method-option--selected",
      );
    });
  }

  function bind() {
    const group = getGroup();
    if (!group || group.dataset.toggleBound) return;
    group.dataset.toggleBound = "true";

    const markChosen = () => {
      userChosen = true;
    };

    // Captura antes do React processar o clique/tecla, evitando race condition
    group.addEventListener("click", markChosen, true);
    group.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === " ") markChosen();
    }, true);
  }

  bind();
  clearSelection();

  const observer = new MutationObserver(() => {
    bind();
    clearSelection();
  });
  observer.observe(document.body, { childList: true, subtree: true });
}
