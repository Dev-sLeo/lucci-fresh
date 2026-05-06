// webpack/js/scripts/cartSidebar.js

export default function cartSidebar() {
    const sidebar = document.getElementById("cart-sidebar");
    if (!sidebar) return;

    const overlay = sidebar.querySelector(".js-cart-overlay");
    const closeBtn = sidebar.querySelector(".js-cart-close");
    const openBtns = document.querySelectorAll(".js-cart-open");

    // ── Open / Close ─────────────────────────────────────────────────────────

    function open() {
        sidebar.setAttribute("aria-hidden", "false");
        sidebar.classList.add("is-open");
        document.body.style.overflow = "hidden";
        closeBtn && closeBtn.focus();
    }

    function close() {
        sidebar.setAttribute("aria-hidden", "true");
        sidebar.classList.remove("is-open");
        document.body.style.overflow = "";
    }

    openBtns.forEach((btn) => btn.addEventListener("click", open));
    overlay && overlay.addEventListener("click", close);
    closeBtn && closeBtn.addEventListener("click", close);

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && sidebar.classList.contains("is-open"))
            close();
    });

    // ── Helpers ───────────────────────────────────────────────────────────────

    function getNonce() {
        const items = sidebar.querySelector(".c-cart-sidebar__items");
        return items ? items.dataset.nonce : "";
    }

    function setLoading(state) {
        const body = sidebar.querySelector(".c-cart-sidebar__body");
        if (body) body.classList.toggle("is-loading", state);
    }

    function applyFragments(fragments) {
        if (!fragments || typeof jQuery === "undefined") return;
        jQuery.each(fragments, (selector, html) => {
            jQuery(selector).replaceWith(html);
        });
    }

    // ── AJAX: remover item ────────────────────────────────────────────────────

    function removeItem(cartItemKey) {
        setLoading(true);
        const formData = new FormData();
        formData.append("action", "lucci_remove_cart_item");
        formData.append("nonce", getNonce());
        formData.append("cart_item_key", cartItemKey);

        fetch(window.wc_cart_params?.ajax_url || window.ajaxurl, {
            method: "POST",
            credentials: "same-origin",
            body: formData,
        })
            .then((r) => r.json())
            .then((res) => {
                if (res.success) applyFragments(res.data);
            })
            .catch(console.error)
            .finally(() => setLoading(false));
    }

    // ── AJAX: atualizar quantidade ────────────────────────────────────────────

    function updateQty(cartItemKey, qty) {
        setLoading(true);
        const formData = new FormData();
        formData.append("action", "lucci_update_cart_qty");
        formData.append("nonce", getNonce());
        formData.append("cart_item_key", cartItemKey);
        formData.append("qty", qty);

        fetch(window.wc_cart_params?.ajax_url || window.ajaxurl, {
            method: "POST",
            credentials: "same-origin",
            body: formData,
        })
            .then((r) => r.json())
            .then((res) => {
                if (res.success) applyFragments(res.data);
            })
            .catch(console.error)
            .finally(() => setLoading(false));
    }

    // ── Delegação de eventos (items são re-renderizados via fragments) ─────────

    sidebar.addEventListener("click", (e) => {
        // Remover item
        const removeBtn = e.target.closest(".js-cart-remove");
        if (removeBtn) {
            removeItem(removeBtn.dataset.key);
            return;
        }

        // Quantidade +/-
        const qtyBtn = e.target.closest(".js-cart-qty");
        if (qtyBtn) {
            const qtyWrap = qtyBtn.closest(".c-cart-sidebar__qty");
            if (!qtyWrap) return;
            const numEl = qtyWrap.querySelector(".c-cart-sidebar__qty-num");
            const current = parseInt(numEl?.textContent || "1", 10);
            const action = qtyBtn.dataset.action;
            const newQty =
                action === "plus" ? current + 1 : Math.max(0, current - 1);

            // Otimista: atualiza UI antes da resposta
            if (numEl) numEl.textContent = newQty;
            updateQty(qtyWrap.dataset.key, newQty);
        }
    });

    // ── Abre o sidebar quando um produto é adicionado via AJAX (WooCommerce) ──

    document.body.addEventListener("added_to_cart", () => {
        open();
    });
}
