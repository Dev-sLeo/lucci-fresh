"use strict";

export default function () {
    const header = document.querySelector(".o-header");
    if (!header) return;

    const main = document.querySelector("main");
    let lastScrollY = window.scrollY;
    let lastPaddingTop = 0;
    let directionLocked = false;
    let lockTimer = null;

    const lockDirection = () => {
        directionLocked = true;
        clearTimeout(lockTimer);
        lockTimer = setTimeout(() => {
            directionLocked = false;
            lastScrollY = window.scrollY;
        }, 200);
    };

    const applyMainPadding = () => {
        if (!main) return;
        const h = header.offsetHeight;
        if (h !== lastPaddingTop) {
            lastPaddingTop = h;
            lockDirection(); // trava a detecção de direção durante o reflow
            main.style.paddingTop = h + "px";
        }
    };

    if (typeof ResizeObserver !== "undefined") {
        const ro = new ResizeObserver(applyMainPadding);
        ro.observe(header);
    }
    window.addEventListener("resize", applyMainPadding, { passive: true });

    const SCROLL_THRESHOLD = 8;

    const toggleHeader = () => {
        const currentScrollY = window.scrollY;
        const delta = currentScrollY - lastScrollY;

        if (currentScrollY > 0) {
            header.classList.add("active");
        } else {
            header.classList.remove("active", "scrolled-down", "scrolled-up");
        }

        // Só detecta direção se não estiver travado e o delta for suficiente
        if (
            !directionLocked &&
            currentScrollY > 0 &&
            Math.abs(delta) >= SCROLL_THRESHOLD
        ) {
            const scrollingDown = delta > 0;
            if (!scrollingDown) {
                header.classList.remove("scrolled-down");
                header.classList.add("scrolled-up");
            } else {
                header.classList.remove("scrolled-up");
                header.classList.add("scrolled-down");
            }
        }

        lastScrollY = currentScrollY;
    };

    applyMainPadding();
    toggleHeader();

    window.addEventListener("scroll", toggleHeader, { passive: true });
}
