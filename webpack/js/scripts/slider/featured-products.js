// webpack/js/scripts/slider/featured-products.js
import Swiper from "swiper";
import { Navigation, Scrollbar } from "swiper/modules";

export default function featuredProducts() {
    const slider = document.querySelector(".js-featured-products");
    if (!slider) return;

    new Swiper(slider, {
        modules: [Navigation, Scrollbar],
        slidesPerView: 1.2,
        spaceBetween: 16,
        grabCursor: true,
        navigation: {
            prevEl: ".js-featured-prev",
            nextEl: ".js-featured-next",
        },
        scrollbar: {
            el: ".js-featured-progress",
            draggable: true,
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
