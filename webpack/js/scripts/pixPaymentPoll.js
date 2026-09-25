"use strict";

/**
 * Tela "Aguardando Pix" (Figma node 2106:1420 - ver _pix-aguardando.html.php
 * e woocommerce/checkout/thankyou.php): qual tela aparece (Aguardando Pix x
 * Pedido confirmado) só é decidido no carregamento da página. Se o webhook
 * do Asaas confirmar o pagamento enquanto o cliente está parado aqui, nada
 * muda sozinho sem isto - consultamos o status do pedido em intervalos
 * (endpoint luccifresh_check_pix_order_status, registrado em
 * extension/woocommerce.php) e recarregamos a página assim que o pedido
 * não precisar mais de pagamento, pra trocar pra tela "Pedido confirmado".
 */
export default function pixPaymentPoll() {
  const root = document.querySelector("[data-pix-wait]");
  if (!root) return;
  if (typeof window.phpVars === "undefined" || !window.phpVars.ajaxUrl) return;

  const orderId = root.getAttribute("data-pix-wait-order-id");
  const orderKey = root.getAttribute("data-pix-wait-order-key");
  if (!orderId || !orderKey) return;

  const POLL_INTERVAL_MS = 8000;
  const MAX_ATTEMPTS = 150; // ~20 minutos

  let attempts = 0;
  let timerId = null;

  const checkStatus = () => {
    attempts += 1;

    const body = new URLSearchParams({
      action: "luccifresh_check_pix_order_status",
      order_id: orderId,
      order_key: orderKey,
    });

    fetch(window.phpVars.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body.toString(),
    })
      .then((response) => response.json())
      .then((json) => {
        if (json && json.success && json.data && false === json.data.needs_payment) {
          clearInterval(timerId);
          window.location.reload();
          return;
        }

        if (attempts >= MAX_ATTEMPTS) {
          clearInterval(timerId);
        }
      })
      .catch(() => {
        // Falha de rede pontual não para o polling - só tenta de novo no
        // próximo intervalo.
      });
  };

  timerId = setInterval(checkStatus, POLL_INTERVAL_MS);
}
