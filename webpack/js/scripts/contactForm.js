"use strict";

/**
 * Contact form — submits via WP AJAX (send_contact_form action)
 */
export default function contactForm() {
    const form = document.querySelector(".js-contact-form");
    if (!form) return;

    const feedback = form.querySelector(".js-contact-feedback");
    const submit = form.querySelector('[type="submit"]');
    const ajaxUrl =
        typeof usAjax !== "undefined"
            ? usAjax.ajaxurl
            : "/wp-admin/admin-ajax.php";

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        submit.disabled = true;
        hideFeedback();

        const data = new FormData(form);
        data.append("action", "send_contact_form");

        try {
            const res = await fetch(ajaxUrl, { method: "POST", body: data });
            const json = await res.json();

            if (json.success) {
                showFeedback(
                    json.message || "Mensagem enviada com sucesso!",
                    false,
                );
                form.reset();
            } else {
                showFeedback(
                    json.errors_html ||
                        json.message ||
                        "Ocorreu um erro. Tente novamente.",
                    true,
                );
            }
        } catch (_) {
            showFeedback("Erro de conexão. Tente novamente.", true);
        } finally {
            submit.disabled = false;
        }
    });

    function showFeedback(message, isError) {
        if (!feedback) return;
        feedback.innerHTML = message;
        feedback.classList.toggle("is-error", isError);
        feedback.hidden = false;
    }

    function hideFeedback() {
        if (!feedback) return;
        feedback.hidden = true;
        feedback.innerHTML = "";
    }
}
