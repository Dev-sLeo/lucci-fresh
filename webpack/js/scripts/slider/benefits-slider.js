import Swiper from "swiper";
import { Scrollbar } from "swiper/modules";

export default function benefitsSlider() {
    const el = document.querySelector(".js-benefits-slider");
    if (!el) return;

    let swiper = null;
    const mq = window.matchMedia("(max-width: 719px)");

    function toggle() {
        if (mq.matches) {
            if (!swiper) {
                swiper = new Swiper(el, {
                    modules: [Scrollbar],
                    slidesPerView: 1.4,
                    spaceBetween: 16,
                    grabCursor: true,
                    scrollbar: {
                        el: ".js-benefits-scrollbar",
                        draggable: true,
                    },
                });
            }
        } else {
            if (swiper) {
                swiper.destroy(true, true);
                swiper = null;
            }
        }
    }

    mq.addEventListener("change", toggle);
    toggle();
}
