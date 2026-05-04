"use strict";

export default function categoryFilter() {
    const filterEl = document.querySelector("[data-category-filter]");
    if (!filterEl) return;

    const gridEl = document.querySelector("[data-category-grid]");
    const paginEl = document.querySelector("[data-category-pagination]");
    const termId = parseInt(filterEl.dataset.termId, 10) || 0;
    const nonce = filterEl.dataset.nonce || "";
    const ajaxUrl = typeof usAjax !== "undefined" ? usAjax.ajaxurl : "";

    let currentSubcat = 0;
    let isLoading = false;

    // ── Skeleton ───────────────────────────────────────────────────────────
    function renderSkeleton(count) {
        var html = "";
        for (var i = 0; i < count; i++) {
            html +=
                '<div class="c-skeleton-product">' +
                '<div class="c-skeleton c-skeleton-product__image"></div>' +
                '<div class="c-skeleton-product__body">' +
                '<div class="c-skeleton c-skeleton-product__name"></div>' +
                '<div class="c-skeleton-product__actions">' +
                '<div class="c-skeleton c-skeleton-product__price"></div>' +
                '<div class="c-skeleton c-skeleton-product__btn"></div>' +
                "</div>" +
                "</div>" +
                "</div>";
        }
        return html;
    }

    // ── Fetch products ─────────────────────────────────────────────────────
    function fetchProducts(subcatId, page) {
        if (isLoading) return;
        isLoading = true;

        gridEl.innerHTML = renderSkeleton(9);
        paginEl.innerHTML = "";

        var formData = new FormData();
        formData.append("action", "get_category_products");
        formData.append("nonce", nonce);
        formData.append("term_id", termId);
        formData.append("subcat_id", subcatId);
        formData.append("page", page);

        fetch(ajaxUrl, { method: "POST", body: formData })
            .then(function (res) {
                if (!res.ok) throw new Error("Network error");
                return res.json();
            })
            .then(function (data) {
                if (data.success) {
                    gridEl.innerHTML =
                        data.data.html ||
                        '<p class="s-cat-products__empty">Nenhum produto encontrado.</p>';
                    paginEl.innerHTML = data.data.pagination || "";
                } else {
                    gridEl.innerHTML =
                        '<p class="s-cat-products__empty">Nenhum produto encontrado.</p>';
                    paginEl.innerHTML = "";
                }
            })
            .catch(function () {
                gridEl.innerHTML =
                    '<p class="s-cat-products__empty">Erro ao carregar produtos.</p>';
                paginEl.innerHTML = "";
            })
            .finally(function () {
                isLoading = false;
            });
    }

    // ── Filter tab clicks ──────────────────────────────────────────────────
    filterEl.addEventListener("click", function (e) {
        var btn = e.target.closest(".s-cat-products__filter-btn");
        if (!btn) return;

        if (btn.classList.contains("s-cat-products__filter-btn--active"))
            return;

        filterEl
            .querySelectorAll(".s-cat-products__filter-btn")
            .forEach(function (b) {
                b.classList.remove("s-cat-products__filter-btn--active");
            });
        btn.classList.add("s-cat-products__filter-btn--active");

        currentSubcat = parseInt(btn.dataset.subcat, 10) || 0;
        fetchProducts(currentSubcat, 1);
    });

    // ── Pagination clicks (delegated) ──────────────────────────────────────
    document.addEventListener("click", function (e) {
        var btn = e.target.closest("[data-category-pagination] [data-page]");
        if (!btn || btn.disabled) return;

        var page = parseInt(btn.dataset.page, 10);
        if (!page || page < 1) return;

        fetchProducts(currentSubcat, page);

        var section = document.querySelector("#js-cat-products");
        if (section) {
            section.scrollIntoView({ behavior: "smooth", block: "start" });
        }
    });
}
