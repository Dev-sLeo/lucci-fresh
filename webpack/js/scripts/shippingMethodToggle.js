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

  shippingRateToggle();
}

/**
 * Lista de taxas de entrega (ex.: "Taxa de Entrega") do Checkout Block: quando
 * existe apenas uma opção, o WooCommerce já marca o radio nativo como
 * "checked" (estado real do input, usado no cálculo do frete). Como não
 * podemos desmarcar o input sem afetar o frete já calculado, escondemos
 * apenas a aparência marcada via CSS até o cliente clicar manualmente.
 */
function shippingRateToggle() {
  const chosen = new WeakSet();

  function getControls() {
    return document.querySelectorAll(
      ".wc-block-components-shipping-rates-control",
    );
  }

  function clearSelection() {
    getControls().forEach((control) => {
      if (chosen.has(control)) return;
      control.classList.add("shipping-rate-not-chosen");
    });
  }

  function bind(control) {
    if (control.dataset.toggleBound) return;
    control.dataset.toggleBound = "true";

    const markChosen = () => {
      chosen.add(control);
      control.classList.remove("shipping-rate-not-chosen");
    };

    control.addEventListener("click", markChosen, true);
    control.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === " ") markChosen();
    }, true);
  }

  function run() {
    getControls().forEach((control) => {
      bind(control);
    });
    clearSelection();
  }

  run();

  const observer = new MutationObserver(run);
  observer.observe(document.body, { childList: true, subtree: true });
}
