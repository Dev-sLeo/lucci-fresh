"use strict";

export default function () {
    document.addEventListener("click", function (e) {
        const head = e.target.closest(".c-accordion__head");
        if (!head) return;

        const item = head.closest(".c-accordion__item");
        const body = item.querySelector(".c-accordion__body");

        item.classList.toggle("is-open");

        if (item.classList.contains("is-open")) {
            body.style.height = body.scrollHeight + "px";
        } else {
            body.style.height = 0;
        }
    });
    const modules = document.querySelectorAll(".js-faq");

    modules.forEach((module) => {
        if (module.dataset.initialized === "true") return;
        module.dataset.initialized = "true";

        const chunks = Array.from(module.querySelectorAll(".js-faq-chunk"));
        const btn = module.querySelector(".js-faq-load-more");
        if (!btn || chunks.length <= 1) return;

        btn.addEventListener("click", () => {
            const next = chunks.find((c) => c.hasAttribute("hidden"));
            if (next) next.removeAttribute("hidden");

            const stillHidden = chunks.some((c) => c.hasAttribute("hidden"));
            if (!stillHidden) btn.remove();
        });
    });
}
