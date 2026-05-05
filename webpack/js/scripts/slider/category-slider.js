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
        slidesPerView: 1.2,
        spaceBetween: 16,
        grabCursor: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
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
                slidesPerView: 2.2,
                spaceBetween: 20,
            },
            1024: {
                slidesPerView: 3.1,
                spaceBetween: 24,
            },
            1280: {
                slidesPerView: 3.5,
                spaceBetween: 24,
            },
        },
    });
}
