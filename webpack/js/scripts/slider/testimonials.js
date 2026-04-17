import Swiper from "swiper";
import { Pagination, Navigation } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";

export default function () {
    const root = document.querySelector(".o-testimonials__list.swiper");
    if (!root) return;

    const paginationEl = root.querySelector(".o-testimonials__pagination");
    const prevEl = root.querySelector(".o-testimonials__nav--prev");
    const nextEl = root.querySelector(".o-testimonials__nav--next");

    const slider = new Swiper(root, {
        modules: [Pagination, Navigation],
        slidesPerView: 1,
        spaceBetween: 14,
        pagination: {
            el: paginationEl,
            type: "progressbar",
        },
        navigation: {
            prevEl,
            nextEl,
        },
        breakpoints: {
            768: {
                slidesPerView: 1,
                spaceBetween: 18,
            },
        },
    });

    return slider;
}
