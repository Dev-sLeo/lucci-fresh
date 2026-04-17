"use strict";

function initMegaTabsRoot(root) {
    if (!root || root.dataset.megaTabsInit === "1") return;
    root.dataset.megaTabsInit = "1";

    var tabBtns = Array.prototype.slice.call(
        root.querySelectorAll("[data-mega-tab]"),
    );
    var panels = Array.prototype.slice.call(
        root.querySelectorAll("[data-mega-panel]"),
    );

    if (!tabBtns.length || !panels.length) return;

    function setActive(id) {
        if (!id) return;

        tabBtns.forEach(function (b) {
            var on = b.getAttribute("data-mega-tab") === id;
            b.classList.toggle("is-active", on);
            b.setAttribute("aria-expanded", on ? "true" : "false");
        });

        panels.forEach(function (p) {
            p.style.display =
                p.getAttribute("data-mega-panel") === id ? "" : "none";
        });
    }

    var initialBtn =
        root.querySelector("[data-mega-tab].is-active") || tabBtns[0];

    if (initialBtn) setActive(initialBtn.getAttribute("data-mega-tab"));

    function getBtnFromEvent(e) {
        var el = e.target;
        if (!el) return null;
        if (el.matches && el.matches("[data-mega-tab]")) return el;
        if (el.closest) return el.closest("[data-mega-tab]");
        return null;
    }

    root.addEventListener(
        "mouseenter",
        function (e) {
            var btn = getBtnFromEvent(e);
            if (!btn) return;
            setActive(btn.getAttribute("data-mega-tab"));
        },
        true,
    );

    root.addEventListener("click", function (e) {
        var btn = getBtnFromEvent(e);
        if (!btn) return;
        setActive(btn.getAttribute("data-mega-tab"));
    });
}

export default function () {
    var roots = document.querySelectorAll("[data-mega-tabs]");
    if (!roots || !roots.length) return;

    Array.prototype.forEach.call(roots, function (root) {
        initMegaTabsRoot(root);
    });
}
