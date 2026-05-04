// webpack/js/scripts/slider/product-grid.js
import Swiper from "swiper";

export default function productGrid() {
    const slider = document.querySelector(".js-product-grid");
    if (!slider) return;

    new Swiper(slider, {
        slidesPerView: 1.2,
        spaceBetween: 16,
        grabCursor: true,
        breakpoints: {
            720: {
                slidesPerView: 2.2,
                spaceBetween: 24,
            },
            1024: {
                enabled: false,
            },
        },
    });
}
