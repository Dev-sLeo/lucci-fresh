import Swiper from "swiper";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";

export default function () {
    const root = document.querySelector(".s-hero__slider.swiper");
    if (!root) return;

    const section  = root.closest(".s-hero");
    const wrap     = root.closest(".s-hero__slider-wrap");
    const prevEl   = wrap.querySelector(".s-hero__nav--prev");
    const nextEl   = wrap.querySelector(".s-hero__nav--next");
    const pageEl   = section.querySelector(".s-hero__pagination");

    if (!prevEl || !pageEl) return;

    const slider = new Swiper(root, {
        modules: [Navigation, Pagination, Autoplay],
        slidesPerView: 1,
        loop: true,
        autoplay: {
            delay: 6000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        navigation: {
            prevEl,
            nextEl,
        },
        pagination: {
            el: pageEl,
            type: "bullets",
            clickable: true,
        },
    });

    return slider;
}
