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
        if (!fragments) return;
        if (typeof jQuery !== "undefined") {
            jQuery.each(fragments, (selector, html) => {
                jQuery(selector).replaceWith(html);
            });
        } else {
            Object.entries(fragments).forEach(([selector, html]) => {
                const el = document.querySelector(selector);
                if (el) {
                    const tmp = document.createElement("div");
                    tmp.innerHTML = html;
                    el.replaceWith(tmp.firstElementChild || tmp);
                }
            });
        }
    }

    function getAjaxUrl() {
        return (
            window.wc_cart_params?.ajax_url ||
            window.usAjax?.ajaxurl ||
            window.phpVars?.ajaxUrl ||
            "/wp-admin/admin-ajax.php"
        );
    }

    // ── AJAX: remover item ────────────────────────────────────────────────────

    function removeItem(cartItemKey) {
        setLoading(true);
        const formData = new FormData();
        formData.append("action", "lucci_remove_cart_item");
        formData.append("nonce", getNonce());
        formData.append("cart_item_key", cartItemKey);

        fetch(getAjaxUrl(), {
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

        fetch(getAjaxUrl(), {
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

    let lastAddBtn = null;

    // Captura qual botão foi clicado antes do AJAX do WC
    document.body.addEventListener(
        "click",
        (e) => {
            const btn = e.target.closest(".ajax_add_to_cart");
            if (btn) lastAddBtn = btn;
        },
        true,
    );

    function showAddedFeedback(btn) {
        if (!btn) return;
        const original = btn.textContent.trim();
        btn.textContent = "Adicionado!";
        btn.classList.add("is-added");
        btn.setAttribute("disabled", "true");
        setTimeout(() => {
            btn.textContent = original;
            btn.classList.remove("is-added");
            btn.removeAttribute("disabled");
            lastAddBtn = null;
        }, 2000);
    }

    // added_to_cart é disparado via jQuery pelo WC — precisa de jQuery.on()
    function bindAddedToCart() {
        if (typeof jQuery === "undefined") {
            setTimeout(bindAddedToCart, 200);
            return;
        }
        jQuery(document.body).on(
            "added_to_cart",
            function (_e, _fragments, _cartHash, $btn) {
                const btn = ($btn && $btn[0]) || lastAddBtn;
                showAddedFeedback(btn);
            },
        );
    }
    bindAddedToCart();
}
