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
          data-nonce="<?= esc_attr($nonce) ?>">

          <button class="s-cat-products__filter-btn s-cat-products__filter-btn--active" data-subcat="0">
            <?= esc_html__('Todos', 'lucci-fresh') ?>
          </button>

          <?php foreach ($subcats as $subcat) : ?>
            <button class="s-cat-products__filter-btn" data-subcat="<?= esc_attr($subcat->term_id) ?>">
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
        'paged'          => 1,
        'tax_query'      => [[
          'taxonomy'         => 'product_cat',
          'field'            => 'term_id',
          'terms'            => $term_id,
          'include_children' => true,
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
      if ($max_pages > 1) :
        $tpl_engine->partial('components/pagination/blog-pagination');
      endif;
      ?>
    </div>

  </div>
</section><!-- /.s-cat-products -->