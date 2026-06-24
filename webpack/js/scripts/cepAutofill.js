"use strict";

/**
 * Preenche endereço e cidade automaticamente a partir do CEP (ViaCEP).
 *
 * O Checkout Block do WooCommerce usa ids com hífen (ex: "billing-postcode"),
 * mas isso pode variar entre versões/integrações — por isso cada campo é
 * resolvido tentando uma lista de seletores possíveis, não um id fixo.
 */
export default function cepAutofill() {
  const PREFIXES = ["billing", "shipping"];

  const SELECTORS = {
    cep: (p) => [
      `#${p}-postcode`,
      `#${p}_postcode`,
      `input[name="${p}-postcode"]`,
      `input[name="${p}_postcode"]`,
    ],
    address: (p) => [
      `#${p}-address_1`,
      `#${p}_address_1`,
      `input[name="${p}-address_1"]`,
      `input[name="${p}_address_1"]`,
    ],
    city: (p) => [
      `#${p}-city`,
      `#${p}_city`,
      `input[name="${p}-city"]`,
      `input[name="${p}_city"]`,
    ],
    state: (p) => [
      `#${p}-state`,
      `#${p}_state`,
      `select[name="${p}-state"]`,
      `select[name="${p}_state"]`,
    ],
    number: (p) => [
      `[id^="${p}-arterra"]`,
      `#${p}_number`,
      `input[name="${p}-number"]`,
      `input[name="${p}_number"]`,
    ],
    neighborhood: (p) => [
      `#${p}-neighborhood`,
      `#${p}_neighborhood`,
      `input[name="${p}-neighborhood"]`,
      `input[name="${p}_neighborhood"]`,
    ],
  };

  function findField(prefix, key) {
    const selectors = SELECTORS[key](prefix);
    for (const selector of selectors) {
      const el = document.querySelector(selector);
      if (el) return el;
    }
    return null;
  }

  // Inputs do Checkout Block são React-controlled: setar .value direto não
  // atualiza a UI. É necessário usar o setter nativo para "burlar" o tracker
  // de valor do React antes de disparar o evento "input".
  function setValue(input, value) {
    if (!input || !value) return;

    const proto =
      input.tagName === "SELECT"
        ? window.HTMLSelectElement.prototype
        : window.HTMLInputElement.prototype;
    const nativeSetter = Object.getOwnPropertyDescriptor(proto, "value")?.set;

    if (nativeSetter) {
      nativeSetter.call(input, value);
    } else {
      input.value = value;
    }

    input.dispatchEvent(new Event("input", { bubbles: true }));
    input.dispatchEvent(new Event("change", { bubbles: true }));
  }

  function setLoading(cepInput, state) {
    cepInput
      .closest(".form-row, .wc-block-components-text-input")
      ?.classList.toggle("is-cep-loading", state);
  }

  function getRow(cepInput) {
    return cepInput.closest(".form-row, .wc-block-components-text-input");
  }

  function clearError(cepInput) {
    const row = getRow(cepInput);
    row?.classList.remove("woocommerce-invalid", "woocommerce-invalid-required-field");
    row?.querySelector(".c-cep-error")?.remove();
  }

  function showError(cepInput, message) {
    const row = getRow(cepInput);
    if (!row) return;
    clearError(cepInput);
    row.classList.add("woocommerce-invalid");
    const span = document.createElement("span");
    span.className = "c-cep-error";
    span.textContent = message;
    row.appendChild(span);
  }

  async function fetchAddress(prefix, cepInput) {
    setLoading(cepInput, true);
    clearError(cepInput);

    const cep = cepInput.value.replace(/\D/g, "");

    try {
      const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
      const data = await res.json();

      if (data.erro) {
        showError(cepInput, "CEP não encontrado.");
        return;
      }

      setValue(findField(prefix, "address"), data.logradouro);
      setValue(findField(prefix, "city"), data.localidade);
      setValue(findField(prefix, "neighborhood"), data.bairro);

      const stateInput = findField(prefix, "state");
      if (stateInput && !stateInput.disabled) {
        setValue(stateInput, data.uf);
      }

      // Entrega restrita a SP: avisa quando o CEP for de outro estado
      if (data.uf && data.uf !== "SP") {
        showError(cepInput, "No momento só entregamos no estado de São Paulo.");
      }

      findField(prefix, "number")?.focus();
    } catch (err) {
      showError(cepInput, "Não foi possível buscar o CEP. Tente novamente.");
    } finally {
      setLoading(cepInput, false);
    }
  }

  function bind(prefix) {
    const cepInput = findField(prefix, "cep");
    if (!cepInput || cepInput.dataset.cepBound) return;
    cepInput.dataset.cepBound = "true";

    let lastFetchedCep = "";

    const maybeFetch = () => {
      const cep = cepInput.value.replace(/\D/g, "");
      if (cep.length === 8 && cep !== lastFetchedCep) {
        lastFetchedCep = cep;
        fetchAddress(prefix, cepInput);
      }
    };

    cepInput.addEventListener("input", maybeFetch);
    cepInput.addEventListener("blur", maybeFetch);
  }

  function bindAll() {
    PREFIXES.forEach(bind);
  }

  bindAll();

  // Campos de envio só existem após o checkout renderizar/atualizar via AJAX
  const observer = new MutationObserver(bindAll);
  observer.observe(document.body, { childList: true, subtree: true });
}
