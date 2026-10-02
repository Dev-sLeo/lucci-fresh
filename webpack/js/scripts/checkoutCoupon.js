// webpack/js/scripts/checkoutCoupon.js
//
// Campo de cupom do checkout novo (_coupon.html.php) - é uma <div>, não um
// <form> (não dá pra aninhar <form> dentro do <form class="checkout">
// principal do wizard, o navegador descarta a tag de dentro ao parsear),
// então o listener de `submit` nativo do WooCommerce
// (wc_checkout_coupons, assets/js/frontend/checkout.js) nunca dispara aqui.
// Reimplementa só o essencial do que esse objeto faz: a mesma chamada
// wc-ajax=apply_coupon, o mesmo payload, e o mesmo pós-processamento
// (erro aparece sob o campo; sucesso dispara update_checkout pra recalcular
// os totais/linha de desconto no resumo do pedido).

export default function checkoutCoupon() {
    const wrapper = document.querySelector(".p-checkout-v2__coupon-form");
    if (!wrapper) return;

    const input = wrapper.querySelector("#coupon_code");
    const button = wrapper.querySelector(".p-checkout-v2__coupon-apply");
    if (!input || !button) return;

    function clearError() {
        input.classList.remove("has-error");
        const notice = wrapper.querySelector(".coupon-error-notice");
        if (notice) notice.remove();
    }

    function showError(message) {
        clearError();
        input.classList.add("has-error");
        const span = document.createElement("span");
        span.className = "coupon-error-notice";
        span.textContent = message;
        wrapper.appendChild(span);
    }

    function extractErrorText(html) {
        const tmp = document.createElement("div");
        tmp.innerHTML = html;
        const text = tmp.textContent.trim();
        return text || "Não foi possível aplicar o cupom.";
    }

    function applyCoupon() {
        const code = input.value.trim();
        if (!code) {
            showError("Informe um código de cupom.");
            return;
        }

        if (typeof jQuery === "undefined" || !window.wc_checkout_params) {
            return;
        }

        const $ = jQuery;
        clearError();
        button.disabled = true;

        $.ajax({
            type: "POST",
            url: wc_checkout_params.wc_ajax_url
                .toString()
                .replace("%%endpoint%%", "apply_coupon"),
            data: {
                security: wc_checkout_params.apply_coupon_nonce,
                coupon_code: code,
                billing_email:
                    document.getElementById("billing_email")?.value || "",
            },
            dataType: "html",
        })
            .done((response) => {
                if (!response) return;

                const hasError =
                    response.indexOf("woocommerce-error") !== -1 ||
                    response.indexOf("is-error") !== -1;

                if (hasError) {
                    showError(extractErrorText(response));
                    return;
                }

                input.value = "";
                $(document.body).trigger("applied_coupon_in_checkout", [
                    code,
                ]);
                $(document.body).trigger("update_checkout", {
                    update_shipping_method: false,
                });
            })
            .fail(() => {
                showError("Não foi possível aplicar o cupom. Tente novamente.");
            })
            .always(() => {
                button.disabled = false;
            });
    }

    button.addEventListener("click", applyCoupon);

    input.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            event.preventDefault();
            applyCoupon();
        }
    });

    input.addEventListener("input", clearError);
}
