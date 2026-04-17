import simpleParallax from "simple-parallax-js/vanilla";

export default function () {
    document.addEventListener("DOMContentLoaded", () => {
        // Desliga no mobile/tablet
        if (window.innerWidth <= 1024) return;

        const heroImages = document.querySelectorAll(
            ".o-hero-header .background-image"
        );

        if (heroImages.length > 0) {
            new simpleParallax(heroImages, {
                scale: 1.1, // mais zoom para não aparecer o fundo atrás
                orientation: "down",
                overflow: false, // evita mostrar o fundo atrás
            });
        }
    });
}
