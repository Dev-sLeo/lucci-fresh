<?php
// includes/partials/template/pages/category/_product-filter.html.php
defined('ABSPATH') || exit;
global $tpl_engine;

$queried_object = get_queried_object();
if (!$queried_object || !($queried_object instanceof WP_Term)) return;

$current_term = $queried_object;
$term_id      = $current_term->term_id;
$category_name = $current_term->name;

// Buscar subcategorias da categoria atual
$subcats = get_terms([
  'taxonomy'   => 'product_cat',
  'parent'     => $term_id,
  'hide_empty' => true,
  'orderby'    => 'menu_order',
  'order'      => 'ASC',
]);

// Contar total de produtos nesta categoria (para exibir)
$total_args = [
  'post_type'      => 'product',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'fields'         => 'ids',
  'tax_query'      => [[
    'taxonomy' => 'product_cat',
    'field'    => 'term_id',
    'terms'    => $term_id,
    'include_children' => true,
  ]],
];
$total_products = count(get_posts($total_args));

$nonce = wp_create_nonce('category_products_nonce');

// Restaurar estado do filtro/paginação a partir da URL (GET)
$current_subcat = isset($_GET['subcat']) ? absint($_GET['subcat']) : 0;
$current_page   = isset($_GET['pagina']) ? max(1, absint($_GET['pagina'])) : 1;

// Se o subcat informado não pertence a esta categoria, ignora
if ($current_subcat > 0 && (empty($subcats) || is_wp_error($subcats) || !in_array($current_subcat, wp_list_pluck($subcats, 'term_id'), true))) {
  $current_subcat = 0;
}
?>
<section class="s-cat-products" id="js-cat-products">
  <div class="s-container">

    <!-- Cabeçalho: título + contador + filtros -->
    <div class="s-cat-products__header">
      <div class="s-cat-products__heading">
        <h2 class="s-cat-products__title"><?= esc_html($category_name) ?></h2>
        <span class="s-cat-products__bar"></span>
        <span class="s-cat-products__count">
          <?= esc_html(sprintf(_n('%d opção', '%d opções', $total_products, 'lucci-fresh'), $total_products)) ?>
        </span>
      </div>

      <?php if (!empty($subcats) && !is_wp_error($subcats)) : ?>
        <div class="s-cat-products__filters"
          data-category-filter
          data-term-id="<?= esc_attr($term_id) ?>"
          data-nonce="<?= esc_attr($nonce) ?>"
          data-current-subcat="<?= esc_attr($current_subcat) ?>"
          data-current-page="<?= esc_attr($current_page) ?>">

          <button class="s-cat-products__filter-btn<?= $current_subcat === 0 ? ' s-cat-products__filter-btn--active' : '' ?>" data-subcat="0">
            <?= esc_html__('Todos', 'lucci-fresh') ?>
          </button>

          <?php foreach ($subcats as $subcat) : ?>
            <button class="s-cat-products__filter-btn<?= $current_subcat === (int) $subcat->term_id ? ' s-cat-products__filter-btn--active' : '' ?>" data-subcat="<?= esc_attr($subcat->term_id) ?>">
              <?= esc_html($subcat->name) ?>
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Grid de produtos -->
    <div class="s-cat-products__grid" data-category-grid>
      <?php
      $args = [
        'post_type'      => 'product',
        'posts_per_page' => 9,
        'post_status'    => 'publish',
        'paged'          => $current_page,
        'tax_query'      => [[
          'taxonomy'         => 'product_cat',
          'field'            => 'term_id',
          'terms'            => $current_subcat > 0 ? $current_subcat : $term_id,
          'include_children' => $current_subcat === 0,
        ]],
      ];
      $query = new WP_Query($args);

      if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
          global $product;
          $product = wc_get_product(get_the_ID());
          if (!$product) continue;
          $tpl_engine->partial('components/product-card', ['product' => $product]);
        endwhile;
        wp_reset_postdata();
      else :
        echo '<p class="s-cat-products__empty">' . esc_html__('Nenhum produto encontrado.', 'lucci-fresh') . '</p>';
      endif;
      ?>
    </div>

    <!-- Paginação -->
    <div class="s-cat-products__pagination" data-category-pagination>
      <?php
      $max_pages = $query->max_num_pages;
      if ($max_pages > 1 && function_exists('get_category_products_pagination_html')) :
        echo get_category_products_pagination_html($current_page, $max_pages);
      endif;
      ?>
    </div>

  </div>
</section><!-- /.s-cat-products -->