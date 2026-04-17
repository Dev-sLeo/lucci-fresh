import Swiper from "swiper";
import { Pagination, Navigation } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";

export default function () {
    const root = document.querySelector(".p-blog-hero__swiper.swiper");
    if (!root) return;

    const paginationEl = root.querySelector(".p-blog-hero__pagination");
    const prevEl = root.querySelector(".p-blog-hero__nav--prev");
    const nextEl = root.querySelector(".p-blog-hero__nav--next");

    new Swiper(root, {
        modules: [Pagination, Navigation],
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        pagination: {
            el: paginationEl,
            type: "progressbar",
        },
        navigation: {
            prevEl,
            nextEl,
        },
    });
}
