<?php

// ── Pagination HTML helper (used by template + AJAX) ─────────────────────────
if (!function_exists('get_blog_pagination_html')) {
  function get_blog_pagination_html(int $current, int $max_pages, int $category_id): string
  {
    if ($max_pages <= 1) {
      return '';
    }

    $prev_page = max(1, $current - 1);
    $next_page = min($max_pages, $current + 1);

    $svg_prev = '<svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M30 18L21 27L30 36" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $svg_next = '<svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M24 18L33 27L24 36" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    $html  = '<nav class="c-pagination" aria-label="Navegação de posts">';

    // Prev arrow
    $prev_disabled = $current <= 1 ? ' disabled' : '';
    $html .= '<button class="c-pagination__arrow c-pagination__arrow--prev" data-page="' . esc_attr($prev_page) . '" aria-label="Página anterior"' . $prev_disabled . '>' . $svg_prev . '</button>';

    // Page numbers (show up to 5 pages around current)
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

    // Next arrow
    $next_disabled = $current >= $max_pages ? ' disabled' : '';
    $html .= '<button class="c-pagination__arrow c-pagination__arrow--next" data-page="' . esc_attr($next_page) . '" aria-label="Próxima página"' . $next_disabled . '>' . $svg_next . '</button>';

    $html .= '</nav>';

    return $html;
  }
}

// ── AJAX handler ──────────────────────────────────────────────────────────────
function get_blog_posts_ajax()
{
  check_ajax_referer('blog_posts_nonce', 'nonce');

  global $tpl_engine;

  $category_id    = absint($_POST['category_id'] ?? 0);
  $page           = max(1, absint($_POST['page'] ?? 1));
  $posts_per_page = 12;

  $args = [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'paged'          => $page,
    'orderby'        => 'date',
    'order'          => 'DESC',
  ];

  if ($category_id > 0) {
    $args['cat'] = $category_id;
  }

  $query = new WP_Query($args);

  // ── Render cards ──────────────────────────────────────────────────────────
  ob_start();
  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $cats = get_the_category();
      $tpl_engine->partial('components/cards/blog', [
        'data' => [
          'permalink'    => get_permalink(),
          'thumbnail_id' => get_post_thumbnail_id(),
          'category'     => !empty($cats) ? $cats[0]->name : '',
          'title'        => get_the_title(),
          'excerpt'      => get_the_excerpt(),
        ],
      ]);
    }
  }
  $cards_html = ob_get_clean();
  wp_reset_postdata();

  // ── Pagination ────────────────────────────────────────────────────────────
  $pagination_html = get_blog_pagination_html($page, (int) $query->max_num_pages, $category_id);

  wp_send_json_success([
    'html'       => $cards_html,
    'pagination' => $pagination_html,
    'found'      => (int) $query->found_posts,
    'max_pages'  => (int) $query->max_num_pages,
  ]);
}
add_action('wp_ajax_nopriv_get_blog_posts', 'get_blog_posts_ajax');
add_action('wp_ajax_get_blog_posts', 'get_blog_posts_ajax');
