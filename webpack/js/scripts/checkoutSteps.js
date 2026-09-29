"use strict";

/**
 * Controla a navegação entre os 4 steps do checkout novo
 * (data-checkout-wizard / data-checkout-step). O form é um único
 * <form name="checkout"> do WooCommerce - os steps são só visibilidade;
 * nenhum dado é perdido ao trocar de step e a validação final continua
 * sendo a do WooCommerce no submit real.
 *
 * Só roda quando o checkout novo está ativo (a marcação
 * data-checkout-wizard só existe nesse layout - ver wizard.html.php).
 */
export default function checkoutSteps() {
  const form = document.querySelector("[data-checkout-wizard]");
  if (!form) return;

  // Sem JS, todos os steps ficam visíveis por padrão (ver _checkout-v2.scss)
  // para não travar o checkout - só escondemos por step quando o JS roda.
  form.classList.add("js-stepped");

  const TOTAL_STEPS = 4;

  function getStepSection(step) {
    return form.querySelector(`[data-checkout-step="${step}"]`);
  }

  function getCurrentStep() {
    const active = form.querySelector('[data-checkout-step].is-active');
    return active ? Number(active.dataset.checkoutStep) : 1;
  }

  // O WooCommerce NUNCA imprime o atributo HTML `required` nos campos -
  // só `aria-required="true"` (ver woocommerce_form_field() em
  // wc-template-functions.php: só seta aria-required, a obrigatoriedade
  // de verdade é validada no PHP, no submit). Sem isso, checkValidity()
  // considera QUALQUER campo vazio como válido, porque pro navegador ele
  // nunca foi marcado como obrigatório - o botão nunca ficaria desabilitado
  // de verdade. Sincroniza o `required` de verdade a partir do
  // aria-required já existente, assim a validação nativa (checkValidity/
  // reportValidity) passa a refletir a obrigatoriedade real do WooCommerce.
  // Chamado de novo a cada updateAllButtonsState() porque os fragmentos de
  // frete/pagamento são substituídos via AJAX com campos novos.
  function syncRequiredAttributes() {
    form.querySelectorAll('[aria-required="true"]:not([required])').forEach((field) => {
      field.required = true;
    });
  }

  // Cada step só bloqueia o avanço com os campos que ele mesmo contém -
  // usa a validação nativa do HTML5 (o atributo `required` já vem do
  // WooCommerce/woocommerce_checkout_fields, não duplicamos regra nenhuma).
  //
  // getFirstInvalidField() é a versão "silenciosa" (sem reportValidity/
  // focus) - usada tanto pelo clique no botão (isStepValid, que aí sim
  // avisa o usuário) quanto pelo botão ficar desabilitado em tempo real
  // enquanto o cliente ainda está preenchendo (updateNextButtonState) -
  // chamar reportValidity() a cada tecla digitada seria bem irritante.
  function getFirstInvalidField(step) {
    const section = getStepSection(step);
    if (!section) return null;

    const fields = section.querySelectorAll("input, select, textarea");
    for (const field of fields) {
      if (field.offsetParent === null) continue; // campo escondido (ex: billing_state fixo)
      if (!field.checkValidity()) return field;
    }

    // O radio de forma de pagamento (step 3) não tem o atributo `required`
    // no HTML (o WooCommerce valida isso só no PHP, no submit) - sem essa
    // checagem extra, o botão "Revisar pedido" ficaria liberado mesmo no
    // instante entre o step carregar e o fragmento de pagamento (AJAX)
    // terminar de renderizar as opções.
    if (step === 3) {
      const paymentRadios = section.querySelectorAll('input[name="payment_method"]');
      if (paymentRadios.length && !section.querySelector('input[name="payment_method"]:checked')) {
        return paymentRadios[0];
      }
    }

    return null;
  }

  function isStepValid(step) {
    const firstInvalid = getFirstInvalidField(step);

    if (firstInvalid) {
      firstInvalid.reportValidity();
      firstInvalid.focus();
      return false;
    }

    return true;
  }

  // Deixa o botão "Continuar"/"Revisar pedido" de cada step desabilitado
  // até todos os campos daquele step estarem preenchidos corretamente -
  // atualizado a cada input/change no form e a cada recálculo AJAX do
  // WooCommerce (evento "updated_checkout", disparado depois que o
  // fragmento de frete/pagamento termina de re-renderizar).
  function updateNextButtonState(step) {
    const section = getStepSection(step);
    if (!section) return;

    const button = section.querySelector("[data-checkout-next], [data-checkout-place-order]");
    if (!button) return;

    button.disabled = Boolean(getFirstInvalidField(step));
  }

  function updateAllButtonsState() {
    syncRequiredAttributes();
    for (let step = 1; step <= TOTAL_STEPS; step += 1) {
      updateNextButtonState(step);
    }
  }

  // A barra de progresso (data-checkout-progress) fica FORA do <form> (ver
  // wizard.html.php - o partial é incluído antes do <form> abrir, pra não
  // interferir no POST). Por isso a busca é a partir de `document`, não de
  // `form`: um `form.querySelectorAll` aqui sempre retorna vazio, e é
  // exatamente por isso que a barra nunca atualizava o estágio atual.
  function goToStep(step) {
    step = Math.min(Math.max(step, 1), TOTAL_STEPS);

    form.querySelectorAll("[data-checkout-step]").forEach((section) => {
      section.classList.toggle("is-active", Number(section.dataset.checkoutStep) === step);
    });

    document.querySelectorAll("[data-checkout-progress-item]").forEach((item) => {
      const itemStep = Number(item.dataset.checkoutProgressItem);
      const isDone = itemStep < step;
      item.classList.toggle("is-active", itemStep === step);
      item.classList.toggle("is-done", isDone);

      // Igual ao Figma: etapas concluídas mostram "✓" no lugar do número e
      // viram um link de volta pra elas (clicável); a atual e as futuras
      // mostram o número normal e não são clicáveis (dado ainda incompleto).
      const numberEl = item.querySelector("[data-checkout-progress-number]");
      if (numberEl) {
        numberEl.textContent = isDone ? "✓" : String(itemStep);
      }
      item.setAttribute("role", isDone ? "button" : "presentation");
      item.tabIndex = isDone ? 0 : -1;
    });

    if (step === TOTAL_STEPS) {
      populateReview();
    }

    getStepSection(step)?.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  // Clique em qualquer etapa já concluída da barra de progresso volta pra
  // ela - não precisa passar pelo botão "Alterar/Editar" de cada step. Sem
  // validação: voltar não perde nenhum dado (os campos continuam no DOM).
  document.addEventListener("click", (event) => {
    const progressItem = event.target.closest("[data-checkout-progress-item].is-done");
    if (progressItem) {
      goToStep(Number(progressItem.dataset.checkoutProgressItem));
    }
  });

  form.addEventListener("click", (event) => {
    const nextBtn = event.target.closest("[data-checkout-next]");
    if (nextBtn) {
      const current = getCurrentStep();
      if (isStepValid(current)) {
        goToStep(Number(nextBtn.dataset.checkoutNext));
      }
      return;
    }

    const editBtn = event.target.closest("[data-checkout-edit]");
    if (editBtn) {
      goToStep(Number(editBtn.dataset.checkoutEdit));
      return;
    }

    const placeOrderBtn = event.target.closest("[data-checkout-place-order]");
    if (placeOrderBtn) {
      // O botão real (#place_order) é renderizado pelo WooCommerce dentro
      // do step de Pagamento (checkout/payment.php); aqui só disparamos o
      // clique nele para reaproveitar o fluxo padrão de submit/validação
      // do WooCommerce sem duplicar lógica nenhuma.
      form.querySelector("#place_order")?.click();
    }
  });

  // "input" cobre digitação em tempo real; "change" cobre radio/select/
  // checkbox e o disparo manual de change que paymentMethodCards.js/
  // shippingMethodCards.js fazem ao clicar num card inteiro.
  form.addEventListener("input", updateAllButtonsState);
  form.addEventListener("change", updateAllButtonsState);

  // Troca de step (ex.: state fixo SP ficando visível/invisível conforme
  // o campo de bairro) também pode mudar quais campos contam como
  // "visíveis" pra validação - reavalia tudo de novo.
  document.addEventListener("click", (event) => {
    if (event.target.closest("[data-checkout-progress-item].is-done, [data-checkout-edit]")) {
      updateAllButtonsState();
    }
  });

  // O fragmento de frete (.c-checkout-step__shipping-methods) e o de
  // pagamento (.woocommerce-checkout-payment) são substituídos via AJAX
  // pelo próprio WooCommerce - isso não dispara "input"/"change" nenhum
  // nos elementos novos, só o evento "updated_checkout" depois que a
  // troca termina.
  if (window.jQuery) {
    window.jQuery(document.body).on("updated_checkout", updateAllButtonsState);
  }

  function fieldValue(name) {
    const field = form.querySelector(`[name="${name}"]`);
    if (!field) return "";
    if (field.type === "checkbox") return field.checked ? field.value : "";
    return field.value?.trim() || "";
  }

  // Preenche um <div data-checkout-review-content> com uma linha <p> por
  // item da lista (não junta tudo num textContent só) - o Figma mostra
  // endereço/prazo e pagamento/parcelas como linhas separadas, não um texto
  // corrido com "•".
  function setReviewLines(section, lines) {
    const content = form.querySelector(`[data-checkout-review-section="${section}"] [data-checkout-review-content]`);
    if (!content) return;

    content.replaceChildren(
      ...lines.filter(Boolean).map((line) => {
        const p = document.createElement("p");
        p.textContent = line;
        return p;
      })
    );
  }

  function getSelectedShippingDescription() {
    const checked = form.querySelector('#customer_details input[name^="shipping_method"]:checked');
    return checked?.closest("li")?.querySelector(".shipping-method-description")?.textContent?.trim() || "";
  }

  function getPaymentReviewLines() {
    const checked = form.querySelector('input[name="payment_method"]:checked');
    if (!checked) return [];

    const total = document.querySelector("[data-checkout-review-total]")?.textContent?.trim() || "";
    const gatewayId = checked.value;

    if (gatewayId === "asaas-pix" || gatewayId === "lkn_pix_for_woocommerce") {
      return [
        `Pix${total ? " • " + total : ""}`,
        "O código será gerado ao confirmar o pedido.",
      ];
    }

    if (gatewayId === "cod") {
      const receiverName = form.querySelector("#cod_receiver_name")?.value?.trim();
      return [
        "Pagamento na entrega",
        receiverName ? `Recebe: ${receiverName}` : "",
        total ? `1× de ${total} • sem juros` : "",
      ].filter(Boolean);
    }

    // Cartão (Rede/Maxipago): mostra os últimos 4 dígitos só se o cliente já
    // digitou um número de cartão válido o suficiente - não inventamos dado
    // nenhum antes disso.
    const cardTypeLabel = form.querySelector('select[name$="_card_type"]')?.selectedOptions?.[0]?.textContent?.trim();
    const cardNumberDigits = (form.querySelector('input[name$="_number"]')?.value || "").replace(/\D/g, "");
    const last4 = cardNumberDigits.length >= 12 ? cardNumberDigits.slice(-4) : "";

    return [
      [cardTypeLabel, last4 && `final ${last4}`].filter(Boolean).join(" • ") || "Cartão de crédito/débito",
      total ? `1× de ${total}, sem juros` : "",
    ];
  }

  function populateReview() {
    setReviewLines(1, [
      [`${fieldValue("billing_first_name")} ${fieldValue("billing_last_name")}`.trim(), fieldValue("billing_email")]
        .filter(Boolean)
        .join(" • "),
    ]);

    setReviewLines(2, [
      [fieldValue("billing_address_1"), fieldValue("billing_number")].filter(Boolean).join(", "),
      [fieldValue("billing_neighborhood"), fieldValue("billing_city") && `${fieldValue("billing_city")} / SP`]
        .filter(Boolean)
        .join(", ") + (fieldValue("billing_postcode") ? ` • ${fieldValue("billing_postcode")}` : ""),
      getSelectedShippingDescription(),
    ]);

    // O total já é calculado pelo próprio WooCommerce (não recalculamos
    // nada aqui) - só lemos o valor que já está certo no resumo do pedido.
    const summaryTotal = document
      .querySelector(".p-checkout-v2__summary .luccifresh-summary-table tfoot .order-total td")
      ?.textContent?.trim();
    const totalEl = form.querySelector("[data-checkout-review-total]");
    if (totalEl && summaryTotal) {
      totalEl.textContent = summaryTotal;
    }

    setReviewLines(3, getPaymentReviewLines());

    // O texto do botão de confirmar varia por gateway (data-order_button_text
    // é o mesmo texto que o WooCommerce usa no #place_order real - ver
    // checkout/payment-method.php) - "Confirmar pedido e gerar Pix",
    // "Finalizar pedido" etc., conforme configurado em cada gateway.
    const placeOrderBtn = form.querySelector("[data-checkout-place-order]");
    const selectedGateway = form.querySelector('input[name="payment_method"]:checked');
    const buttonText = selectedGateway?.dataset.orderButtonText;
    if (placeOrderBtn && buttonText) {
      placeOrderBtn.textContent = buttonText;
    }
  }

  goToStep(getCurrentStep());
  updateAllButtonsState();
}
