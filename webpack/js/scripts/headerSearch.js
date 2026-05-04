"use strict";

export default function headerSearch() {
    const btns = document.querySelectorAll(".o-header__search");
    const bar = document.getElementById("header-search-bar");
    const input = document.getElementById("header-search-input");
    const closeBtn = document.querySelector(".js-search-close");

    if (!btns.length || !bar) return;

    const open = () => {
        bar.classList.add("is-open");
        bar.setAttribute("aria-hidden", "false");
        btns.forEach((b) => b.setAttribute("aria-expanded", "true"));
        if (input) input.focus();
    };

    const close = () => {
        bar.classList.remove("is-open");
        bar.setAttribute("aria-hidden", "true");
        btns.forEach((b) => b.setAttribute("aria-expanded", "false"));
    };

    btns.forEach((btn) => {
        btn.addEventListener("click", () => {
            bar.classList.contains("is-open") ? close() : open();
        });
    });

    if (closeBtn) closeBtn.addEventListener("click", close);

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && bar.classList.contains("is-open")) close();
    });
}
