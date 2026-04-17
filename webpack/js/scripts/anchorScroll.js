"use strict";

const EXTRA_OFFSET = 32;

export default function () {
    const header = document.querySelector(".o-header");

    const getOffset = () => {
        const headerHeight = header ? header.offsetHeight : 0;
        return headerHeight + EXTRA_OFFSET;
    };

    const scrollToAnchor = (id) => {
        const target = document.getElementById(id);
        if (!target) return;

        const top =
            target.getBoundingClientRect().top + window.scrollY - getOffset();

        window.scrollTo({ top, behavior: "smooth" });
    };

    document.addEventListener("click", (e) => {
        const link = e.target.closest('a[href^="#"]');
        if (!link) return;

        const id = link.getAttribute("href").slice(1);
        if (!id) return;

        const target = document.getElementById(id);
        if (!target) return;

        e.preventDefault();
        scrollToAnchor(id);
    });

    // Trata âncora presente na URL ao carregar a página
    if (window.location.hash) {
        const id = window.location.hash.slice(1);
        // Aguarda o layout estabilizar antes de rolar
        window.addEventListener("load", () => {
            setTimeout(() => scrollToAnchor(id), 50);
        });
    }
}
