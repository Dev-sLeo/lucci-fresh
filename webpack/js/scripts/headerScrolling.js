"use strict";

export default function () {
    const header = document.querySelector(".o-header");
    if (!header) return;

    const toggleHeader = () => {
        if (window.scrollY > 0) {
            header.classList.add("is-scrolled");
        } else {
            header.classList.remove("is-scrolled");
        }
    };

    // Verifica no carregamento inicial
    toggleHeader();

    // Atualiza ao rolar a página
    window.addEventListener("scroll", toggleHeader);
}
