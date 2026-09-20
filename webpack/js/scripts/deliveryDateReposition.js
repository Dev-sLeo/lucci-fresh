"use strict";

/**
 * O plugin YITH WooCommerce Delivery Date (checkout clássico, usado pelo
 * Fluid Checkout) imprime o campo de data de entrega dentro do hook
 * `woocommerce_checkout_shipping`, ou seja, junto ao formulário de endereço
 * (`.woocommerce-shipping-fields`) — ANTES da lista "Método de entrega", que
 * o Fluid Checkout renderiza em uma seção própria e separada.
 *
 * Isso faz o campo de data aparecer fora de ordem (antes do cliente escolher
 * o tipo de frete) e, como o AJAX do plugin recria esse bloco a cada troca
 * de método (`.replaceWith`), ele volta para o lugar errado toda vez.
 * Movemos o bloco para logo abaixo da lista de métodos de entrega (full
 * width) sempre que o DOM mudar; a data e o horário ficam lado a lado
 * dentro desse bloco via CSS (ver _checkout.scss).
 *
 * O Fluid Checkout também re-renderiza a seção de endereço inteira vinda do
 * servidor de tempos em tempos (recálculo de frete, fragments, etc.), o que
 * imprime um novo `.ywcdd_select_delivery_date_content` na posição original
 * (dentro de `.woocommerce-shipping-fields`) sem remover a cópia que já
 * tínhamos movido antes — por isso sempre reduzimos para uma única cópia
 * (mantendo a mais recente) antes de reposicionar.
 *
 * Quando existe só uma opção de frete, o WooCommerce já marca o único radio
 * como selecionado (igual ao Checkout Block), então o campo de data aparece
 * pronto antes de qualquer clique do cliente. Escondemos o campo até o
 * cliente interagir manualmente com a lista de métodos de entrega.
 */
export default function deliveryDateReposition() {
  const HIDDEN_CLASS = "ywcdd-not-chosen";
  const SKELETON_CLASS = "ywcdd-loading-skeleton";
  let userChosen = false;

  function getDateFields() {
    return document.querySelectorAll(".ywcdd_select_delivery_date_content");
  }

  function getShippingMethodsContainer() {
    return document.querySelector(".fc-shipping-method__packages");
  }

  // O YITH Delivery Date leva 2-3 requisições AJAX em sequência (recalcular
  // frete → listar transportadoras → buscar datas) até o campo aparecer de
  // fato. Sem isso, o cliente vê a tela "parada" por até 1s depois de
  // escolher o frete. Mostramos um placeholder imediatamente ao clicar, e
  // trocamos pelo conteúdo real assim que ele chegar.
  function getOrCreateSkeleton(shippingContainer) {
    let skeleton = shippingContainer.parentNode.querySelector(`.${SKELETON_CLASS}`);
    if (skeleton) return skeleton;

    skeleton = document.createElement("div");
    skeleton.className = SKELETON_CLASS;
    skeleton.innerHTML =
      '<span class="ywcdd-loading-skeleton__bar"></span><span class="ywcdd-loading-skeleton__bar"></span>';
    shippingContainer.insertAdjacentElement("afterend", skeleton);
    return skeleton;
  }

  function removeSkeleton() {
    document.querySelectorAll(`.${SKELETON_CLASS}`).forEach((el) => el.remove());
  }

  function hasRealContent(dateField) {
    return !!dateField.querySelector(
      ".ywcdd_datepicker_content, .ywcdd_carrier_content, .ywcdd_timeslot_content",
    );
  }

  // Bug de corrida do próprio plugin: o YITH só chama `ajax_find_date_available()`
  // (o que de fato busca os dias disponíveis e liga o calendário) quando o
  // `update_carrier_list` retorna `update_delivery_form: true` — ou seja, só
  // quando o método de processamento MUDOU em relação ao anterior. No Fluid
  // Checkout, o evento `updated_checkout` dispara várias vezes durante o
  // preenchimento do endereço (antes do cliente sequer escolher o frete), o
  // que já deixa `current_shipping_method_id` apontando pro método certo. Na
  // hora que o cliente realmente clica, a YITH acha que "nada mudou", pula o
  // recálculo, e o HTML da data/transportadora aparece (de uma resposta
  // anterior) mas o `.datepicker()` do jQuery UI nunca é inicializado — o
  // campo fica visível, mas clicar nele não abre calendário nenhum.
  //
  // Como não dá pra corrigir a lógica de comparação dentro do plugin,
  // detectamos esse estado "meio-renderizado" (conteúdo presente, mas sem a
  // classe `hasDatepicker` que o jQuery UI adiciona ao inicializar) e forçamos
  // o mesmo evento que o próprio plugin dispara depois de um recálculo bem
  // sucedido (`init-delivery-fields`), uma única vez por render.
  function ensureDatepickerInitialized(dateField) {
    const input = dateField.querySelector("#ywcdd_datepicker");
    if (!input || input.classList.contains("hasDatepicker") || input.dataset.ywcddRetried) {
      return;
    }

    input.dataset.ywcddRetried = "true";
    if (window.jQuery) {
      window.jQuery(document).trigger("init-delivery-fields");
    }
  }

  function dedupe(fields) {
    if (fields.length <= 1) return fields[0] || null;

    // A cópia ainda dentro do wrapper de endereço é a que acabou de ser
    // renderizada pelo servidor/AJAX; as demais são sobras órfãs de uma
    // reposição anterior.
    let fresh = null;
    fields.forEach((el) => {
      if (el.closest(".woocommerce-shipping-fields")) fresh = el;
    });
    if (!fresh) fresh = fields[fields.length - 1];

    fields.forEach((el) => {
      if (el !== fresh) el.remove();
    });

    return fresh;
  }

  function bindInteraction(shippingContainer) {
    if (shippingContainer.dataset.ywcddBound) return;
    shippingContainer.dataset.ywcddBound = "true";

    const markChosen = () => {
      userChosen = true;
      applyVisibility();
      getOrCreateSkeleton(shippingContainer);

      // Nem todo método de entrega tem data configurada no YITH (ex.: retirada
      // grátis) — se depois de um tempo nada chegou, o placeholder não faz
      // mais sentido e seria enganoso deixá-lo girando para sempre.
      //
      // O timeout é alto de propósito: a chamada AJAX `update_datepicker` do
      // próprio plugin (que busca os dias disponíveis) foi medida em ~18-19s
      // em teste real neste ambiente — bem mais lenta do que parece à primeira
      // vista. Um timeout curto (ex.: 4s) esconde o skeleton bem antes da
      // resposta chegar, e o cliente fica olhando pra uma caixa vazia sem
      // nenhum indicador de carregamento pelo resto da espera.
      window.setTimeout(removeSkeleton, 30000);
    };

    // Captura antes do WooCommerce processar o clique, e cobre o caso de já
    // vir marcado (única opção de frete) sem ação do cliente.
    shippingContainer.addEventListener("click", markChosen, true);
    shippingContainer.addEventListener(
      "keydown",
      (e) => {
        if (e.key === "Enter" || e.key === " ") markChosen();
      },
      true,
    );
  }

  function applyVisibility() {
    const dateField = document.querySelector(".ywcdd_select_delivery_date_content");
    if (!dateField) return;
    dateField.classList.toggle(HIDDEN_CLASS, !userChosen);
  }

  function reposition() {
    const shippingContainer = getShippingMethodsContainer();
    const dateField = dedupe(getDateFields());
    if (!dateField || !shippingContainer) return;

    bindInteraction(shippingContainer);

    if (dateField.previousElementSibling !== shippingContainer) {
      shippingContainer.insertAdjacentElement("afterend", dateField);
    }

    if (hasRealContent(dateField)) {
      removeSkeleton();
      ensureDatepickerInitialized(dateField);
    }

    applyVisibility();
  }

  reposition();

  const observer = new MutationObserver(reposition);
  observer.observe(document.body, { childList: true, subtree: true });
}
