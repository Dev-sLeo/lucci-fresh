import Swiper from "swiper";
import { Navigation, Scrollbar, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/scrollbar";

export default function categorySlider() {
    const el = document.querySelector(".js-category-slider");
    if (!el) return;

    new Swiper(el, {
        modules: [Navigation, Scrollbar, Autoplay],
        slidesPerView: "auto",
        spaceBetween: 16,
        grabCursor: true,
        navigation: {
            prevEl: ".js-category-prev",
            nextEl: ".js-category-next",
        },
        scrollbar: {
            el: ".js-category-progress",
            draggable: true,
        },
        breakpoints: {
            720: {
                spaceBetween: 20,
            },
            1024: {
                spaceBetween: 32,
            },
        },
    });
}
