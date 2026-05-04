<?php

/**
 * AJAX: Produtos por categoria + subcategoria com paginação
 */

// ── Pagination HTML ───────────────────────────────────────────────────────────
if (!function_exists('get_category_products_pagination_html')) {
  function get_category_products_pagination_html(int $current, int $max_pages): string
  {
    if ($max_pages <= 1) return '';

    $svg_prev = '<svg width="54" height="54" viewBox="0 0 54 54" fill="none"><path d="M30 18L21 27L30 36" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $svg_next = '<svg width="54" height="54" viewBox="0 0 54 54" fill="none"><path d="M24 18L33 27L24 36" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    $prev_page     = max(1, $current - 1);
    $next_page     = min($max_pages, $current + 1);
    $prev_disabled = $current <= 1 ? ' disabled' : '';
    $next_disabled = $current >= $max_pages ? ' disabled' : '';

    $html  = '<nav class="c-pagination" aria-label="' . esc_attr__('Paginação de produtos', 'lucci-fresh') . '">';
    $html .= '<button class="c-pagination__arrow c-pagination__arrow--prev" data-page="' . esc_attr($prev_page) . '" aria-label="' . esc_attr__('Página anterior', 'lucci-fresh') . '"' . $prev_disabled . '>' . $svg_prev . '</button>';

    $start = max(1, $current - 2);
    $end   = min($max_pages, $start + 4);
    $start = max(1, $end - 4);

    $html .= '<ul class="c-pagination__pages">';
    for ($i = $start; $i <= $end; $i++) {
      $active = $i === $current ? ' c-pagination__page--active' : '';
      $label  = str_pad($i, 2, '0', STR_PAD_LEFT);
      $html  .= '<li class="c-pagination__page' . $active . '">';
      $html  .= '<button data-page="' . esc_attr($i) . '">' . esc_html($label) . '</button>';
      $html  .= '</li>';
    }
    $html .= '</ul>';

    $html .= '<button class="c-pagination__arrow c-pagination__arrow--next" data-page="' . esc_attr($next_page) . '" aria-label="' . esc_attr__('Próxima página', 'lucci-fresh') . '"' . $next_disabled . '>' . $svg_next . '</button>';
    $html .= '</nav>';

    return $html;
  }
}

// ── AJAX handler ──────────────────────────────────────────────────────────────
function get_category_products_ajax(): void
{
  check_ajax_referer('category_products_nonce', 'nonce');

  global $tpl_engine;

  $term_id        = absint($_POST['term_id']   ?? 0);
  $subcat_id      = absint($_POST['subcat_id'] ?? 0);
  $page           = max(1, absint($_POST['page'] ?? 1));
  $posts_per_page = 9;

  if (!$term_id) {
    wp_send_json_error(['message' => 'Invalid term']);
  }

  // Use subcat if provided, otherwise use parent term (include children)
  $tax_query = [[
    'taxonomy'         => 'product_cat',
    'field'            => 'term_id',
    'terms'            => $subcat_id > 0 ? $subcat_id : $term_id,
    'include_children' => $subcat_id === 0,
  ]];

  $args = [
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'paged'          => $page,
    'tax_query'      => $tax_query,
  ];

  $query = new WP_Query($args);

  ob_start();
  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      global $product;
      $product = wc_get_product(get_the_ID());
      if (!$product) continue;
      $tpl_engine->partial('components/product-card', ['product' => $product]);
    }
    wp_reset_postdata();
  } else {
    echo '<p class="s-cat-products__empty">' . esc_html__('Nenhum produto encontrado.', 'lucci-fresh') . '</p>';
  }
  $html = ob_get_clean();

  $pagination = get_category_products_pagination_html($page, $query->max_num_pages);

  wp_send_json_success([
    'html'       => $html,
    'pagination' => $pagination,
  ]);
}

add_action('wp_ajax_get_category_products',        'get_category_products_ajax');
add_action('wp_ajax_nopriv_get_category_products', 'get_category_products_ajax');
