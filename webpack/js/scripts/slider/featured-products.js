// webpack/js/scripts/slider/featured-products.js
import Swiper from "swiper";
import { Navigation } from "swiper/modules";

export default function featuredProducts() {
    const slider = document.querySelector(".js-featured-products");
    if (!slider) return;

    new Swiper(slider, {
        modules: [Navigation],
        slidesPerView: 1.2,
        spaceBetween: 16,
        grabCursor: true,
        navigation: {
            prevEl: ".js-featured-prev",
            nextEl: ".js-featured-next",
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
