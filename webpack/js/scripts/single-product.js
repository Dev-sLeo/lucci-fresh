// webpack/js/scripts/single-product.js
// Galeria de imagens (Swiper) + slider de produtos relacionados
import Swiper from "swiper";
import { Navigation, Scrollbar } from "swiper/modules";

export default function singleProduct() {
    // ── Gallery slider ─────────────────────────────────────────────────────────
    const galleryEl = document.querySelector(".js-sp-gallery");
    if (galleryEl) {
        new Swiper(galleryEl, {
            modules: [Navigation, Scrollbar],
            slidesPerView: 1,
            spaceBetween: 0,
            grabCursor: true,
            navigation: {
                prevEl: ".js-sp-gallery-prev",
                nextEl: ".js-sp-gallery-next",
            },
            scrollbar: {
                el: ".js-sp-gallery-progress",
                draggable: true,
            },
        });
    }

    // ── Related products slider ────────────────────────────────────────────────
    const relatedEl = document.querySelector(".js-sp-related");
    if (!relatedEl) return;

    new Swiper(relatedEl, {
        modules: [Navigation],
        slidesPerView: 1.2,
        spaceBetween: 16,
        grabCursor: true,
        navigation: {
            prevEl: ".js-sp-related-prev",
            nextEl: ".js-sp-related-next",
        },
        breakpoints: {
            720: {
                slidesPerView: 2.2,
                spaceBetween: 24,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 32,
            },
        },
    });
}
