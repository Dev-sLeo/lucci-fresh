"use strict";

export default function () {
    const body = document.body;
    const menu = document.querySelector(".menu-mobile");
    const hamburguer = document.querySelector(
        ".js-mobile-menu-open, .hamburguer-menu",
    );

    if (!menu || !hamburguer) return;

    const close = menu.querySelector(".js-mobile-menu-close, .close-icon");
    const mobileButton = null;

    const closeAllPanels = () => {
        const opened = menu.querySelectorAll(".is-open");
        opened.forEach((el) => el.classList.remove("is-open"));

        const toggles = menu.querySelectorAll(
            ".c-main-menu__toggle[aria-expanded='true']",
        );
        toggles.forEach((t) => t.setAttribute("aria-expanded", "false"));

        const subpanels = menu.querySelectorAll("[data-subpanel-body]");
        subpanels.forEach((p) => {
            p.style.display = "none";
        });
    };

    const setMenuOpen = (open) => {
        menu.classList.toggle("active", open);
        menu.setAttribute("aria-hidden", open ? "false" : "true");
        hamburguer.setAttribute("aria-expanded", open ? "true" : "false");
        body.style.overflow = open ? "hidden" : "auto";
        if (!open) closeAllPanels();
    };

    const toggleMenu = () => setMenuOpen(!menu.classList.contains("active"));

    hamburguer.addEventListener("click", toggleMenu);
    if (close) close.addEventListener("click", () => setMenuOpen(false));
    if (mobileButton)
        mobileButton.addEventListener("click", () => setMenuOpen(false));

    const getPanelFromToggle = (toggle) => {
        const subpanelId = toggle.getAttribute("data-subpanel");
        if (subpanelId) {
            return menu.querySelector(
                `[data-subpanel-body="${CSS.escape(subpanelId)}"]`,
            );
        }

        const li = toggle.closest("li");
        if (!li) return null;

        return li.querySelector(
            ".c-main-menu__dropdown, .c-submenu-trigger-wrapper, .c-main-menu__sub-sub-menu, .sub-menu",
        );
    };

    const closeSiblings = (toggle) => {
        const li = toggle.closest("li");
        if (!li) return;

        const parentList = li.parentElement;
        if (!parentList) return;

        const siblingToggles = parentList.querySelectorAll(
            ":scope > li > .c-main-menu__item-head > .c-main-menu__toggle, :scope > li > .c-main-menu__toggle",
        );
        siblingToggles.forEach((t) => {
            if (t === toggle) return;

            const sibLi = t.closest("li");
            if (sibLi) sibLi.classList.remove("is-open");
            t.setAttribute("aria-expanded", "false");

            const panel = getPanelFromToggle(t);
            if (panel) {
                panel.classList.remove("is-open");
                if (panel.hasAttribute("data-subpanel-body"))
                    panel.style.display = "none";
            }
        });
    };

    menu.addEventListener("click", (e) => {
        const toggle = e.target.closest(".c-main-menu__toggle");
        if (!toggle) return;

        e.preventDefault();
        e.stopPropagation();

        const panel = getPanelFromToggle(toggle);
        if (!panel) return;

        const li = toggle.closest("li");
        if (!li) return;

        const isOpen = li.classList.contains("is-open");

        closeSiblings(toggle);

        if (isOpen) {
            li.classList.remove("is-open");
            toggle.setAttribute("aria-expanded", "false");
            panel.classList.remove("is-open");
            if (panel.hasAttribute("data-subpanel-body"))
                panel.style.display = "none";
            return;
        }

        li.classList.add("is-open");
        toggle.setAttribute("aria-expanded", "true");
        panel.classList.add("is-open");
        if (panel.hasAttribute("data-subpanel-body"))
            panel.style.display = "block";
    });
}
