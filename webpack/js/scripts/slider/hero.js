import Swiper from "swiper";
import { Navigation, Autoplay } from "swiper/modules";

export default function () {
    const root = document.querySelector(".o-hero__slider.swiper");
    if (!root) return;

    const prevEl = root.parentElement.querySelector(".o-hero__nav--prev");
    const nextEl = root.parentElement.querySelector(".o-hero__nav--next");

    const slider = new Swiper(root, {
        modules: [Navigation, Autoplay],
        slidesPerView: 1,
        loop: true,
        autoplay: {
            delay: 6000,
            disableOnInteraction: false,
        },
        navigation: {
            prevEl,
            nextEl,
        },
    });

    return slider;
}
