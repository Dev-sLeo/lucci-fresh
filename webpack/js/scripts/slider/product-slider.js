import Swiper from "swiper";
import { Pagination, Navigation } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";

function initSliderInPanel(panel) {
    const root = panel.querySelector(".o-product-slider__list.swiper");
    const pagination = panel.querySelector(".o-product-slider__pagination");
    const prevEl = panel.querySelector(".o-product-slider__nav--prev");
    const nextEl = panel.querySelector(".o-product-slider__nav--next");

    if (!root) return null;

    return new Swiper(root, {
        modules: [Pagination, Navigation],
        slidesPerView: 1,
        spaceBetween: 16,
        pagination: {
            el: pagination,
            type: "progressbar",
        },
        navigation: {
            prevEl,
            nextEl,
        },
        breakpoints: {
            720: { slidesPerView: 2, spaceBetween: 20 },
            1024: { slidesPerView: 3, spaceBetween: 24 },
        },
    });
}

export default function () {
    const sections = document.querySelectorAll("[data-product-slider]");
    if (!sections.length) return;

    sections.forEach((section) => {
        const panels = section.querySelectorAll(".o-product-slider__panel");
        const tabs = section.querySelectorAll(".o-product-slider__tab");

        // Init a Swiper for every panel (including hidden ones)
        panels.forEach((panel) => initSliderInPanel(panel));

        // Tab switching
        if (!tabs.length) return;

        tabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                // Deactivate all
                tabs.forEach((t) => {
                    t.classList.remove("is-active");
                    t.setAttribute("aria-selected", "false");
                });
                panels.forEach((p) => p.setAttribute("hidden", ""));

                // Activate clicked
                tab.classList.add("is-active");
                tab.setAttribute("aria-selected", "true");

                const panelId = tab.dataset.tab;
                const target = section.querySelector(
                    `[data-panel="${panelId}"]`,
                );
                if (target) target.removeAttribute("hidden");
            });
        });
    });
}
