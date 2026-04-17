import Swiper from "swiper";
import { Navigation } from "swiper/modules";

export default function () {
    const root = document.querySelector(".o-tour-list__slider.swiper");
    if (!root) return;

    const section = root.closest(".o-tour-list");
    const prevEl = section.querySelector(".o-tour-list__nav-btn--prev");
    const nextEl = section.querySelector(".o-tour-list__nav-btn--next");

    const slider = new Swiper(root, {
        modules: [Navigation],
        slidesPerView: "auto",
        spaceBetween: 16,
        centeredSlides: true,
        loop: false,
        navigation: {
            prevEl,
            nextEl,
        },
        breakpoints: {
            // tablet+: centred single card
            768: {
                spaceBetween: 20,
                centeredSlides: true,
            },
            // desktop: disable slider behavior
            1024: {
                slidesPerView: 1,
                spaceBetween: 0,
                centeredSlides: false,
                allowTouchMove: false,
            },
        },
    });

    return slider;
}
