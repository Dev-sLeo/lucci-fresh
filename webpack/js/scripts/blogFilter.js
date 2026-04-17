"use strict";

export default function () {
    const filterEl = document.querySelector("[data-blog-filter]");
    if (!filterEl) return;

    const postsWrap = document.querySelector("[data-blog-posts]");
    const paginWrap = document.querySelector("[data-blog-pagination]");
    const nonce = filterEl.dataset.nonce || "";
    const ajaxUrl = typeof usAjax !== "undefined" ? usAjax.ajaxurl : "";

    let currentCategory = 0;
    let isLoading = false;

    // ── Skeleton template ───────────────────────────────────────────────────
    function renderSkeleton(count) {
        var html = "";
        for (var i = 0; i < count; i++) {
            html +=
                '<div class="c-skeleton-post">' +
                '<div class="c-skeleton c-skeleton-post__image"></div>' +
                '<div class="c-skeleton-post__content">' +
                '<div class="c-skeleton c-skeleton-post__title"></div>' +
                '<div class="c-skeleton c-skeleton-post__excerpt c-skeleton--short"></div>' +
                "</div>" +
                "</div>";
        }
        return html;
    }

    // ── Fetch posts via AJAX ────────────────────────────────────────────────
    function fetchPosts(categoryId, page) {
        if (isLoading) return;
        isLoading = true;

        // Show skeleton in grid + clear pagination
        postsWrap.innerHTML = renderSkeleton(9);
        paginWrap.innerHTML = "";

        var formData = new FormData();
        formData.append("action", "get_blog_posts");
        formData.append("nonce", nonce);
        formData.append("category_id", categoryId);
        formData.append("page", page);

        fetch(ajaxUrl, { method: "POST", body: formData })
            .then(function (res) {
                if (!res.ok) throw new Error("Network error");
                return res.json();
            })
            .then(function (data) {
                if (data.success) {
                    postsWrap.innerHTML =
                        data.data.html ||
                        '<p class="p-blog-grid__empty">Nenhum post encontrado.</p>';
                    paginWrap.innerHTML = data.data.pagination || "";
                } else {
                    postsWrap.innerHTML =
                        '<p class="p-blog-grid__empty">Nenhum post encontrado.</p>';
                    paginWrap.innerHTML = "";
                }
            })
            .catch(function () {
                postsWrap.innerHTML =
                    '<p class="p-blog-grid__empty">Erro ao carregar posts.</p>';
                paginWrap.innerHTML = "";
            })
            .finally(function () {
                isLoading = false;
            });
    }

    // ── Filter tab clicks ───────────────────────────────────────────────────
    filterEl.addEventListener("click", function (e) {
        var tab = e.target.closest(".c-blog-filter__tab");
        if (!tab) return;

        // Skip if already active
        if (tab.classList.contains("c-blog-filter__tab--active")) return;

        filterEl.querySelectorAll(".c-blog-filter__tab").forEach(function (t) {
            t.classList.remove("c-blog-filter__tab--active");
        });
        tab.classList.add("c-blog-filter__tab--active");

        currentCategory = parseInt(tab.dataset.category, 10) || 0;
        fetchPosts(currentCategory, 1);
    });

    // ── Pagination clicks (delegated — content is replaced by AJAX) ─────────
    document.addEventListener("click", function (e) {
        var btn = e.target.closest("[data-blog-pagination] [data-page]");
        if (!btn) return;

        if (btn.disabled) return;

        var page = parseInt(btn.dataset.page, 10);
        if (!page || page < 1) return;

        fetchPosts(currentCategory, page);

        // Scroll back to grid top
        var gridSection = document.querySelector(".p-blog-grid");
        if (gridSection) {
            gridSection.scrollIntoView({ behavior: "smooth", block: "start" });
        }
    });
}
