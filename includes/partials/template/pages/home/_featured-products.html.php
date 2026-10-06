<?php
// includes/partials/components/_featured-products.html.php
global $tpl_engine;

$block  = get_field('mais_pedidos');
$titulo = $block['title'] ?? __('Os mais pedidos', 'lucci-fresh');

$limit = 8;

// Mais pedidos: produtos com vendas, ordenados pela quantidade vendida
$bestsellers_query = new WP_Query([
  'post_type'      => 'product',
  'posts_per_page' => $limit,
  'post_status'    => 'publish',
  'orderby'        => 'meta_value_num',
  'meta_key'       => 'total_sales',
  'order'          => 'DESC',
  'meta_query'     => [
    [
      'key'     => 'total_sales',
      'value'   => 0,
      'compare' => '>',
      'type'    => 'NUMERIC',
    ],
  ],
]);

$product_ids = wp_list_pluck($bestsellers_query->posts, 'ID');

// Completa com os mais recentes até atingir o total, sem repetir produtos
if (count($product_ids) < $limit) {
  $recent_query = new WP_Query([
    'post_type'      => 'product',
    'posts_per_page' => $limit - count($product_ids),
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post__not_in'   => $product_ids,
  ]);

  $product_ids = array_merge($product_ids, wp_list_pluck($recent_query->posts, 'ID'));
}

$products = $product_ids ? new WP_Query([
  'post_type'      => 'product',
  'post_status'    => 'publish',
  'post__in'       => $product_ids,
  'orderby'        => 'post__in',
  'posts_per_page' => $limit,
]) : new WP_Query(['post__in' => [0]]);
?>
<?php if ($products->have_posts()) : ?>
  <section class="s-featured-products">
    <div class="s-container">
      <div class="s-featured-products__header">
        <h2 class="s-featured-products__title"><?= esc_html($titulo) ?></h2>
      </div>

      <div class="s-featured-products__track">
        <button class="s-featured-products__prev js-featured-prev" aria-label="<?= esc_attr__('Anterior', 'lucci-fresh') ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="23" viewBox="0 0 12 23" fill="none">
            <path d="M11.0835 22.0833L0.750163 11.4167L11.0835 0.75" stroke="#FD8426" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>

        <div class="swiper js-featured-products">
          <div class="swiper-wrapper">
            <?php while ($products->have_posts()) : $products->the_post();
              global $product;
              $product = wc_get_product(get_the_ID());
              if (!$product) continue;
            ?>
              <div class="swiper-slide">
                <?php $tpl_engine->partial('components/product-card', ['product' => $product]) ?>
              </div>
            <?php endwhile;
            wp_reset_postdata(); ?>
          </div>
        </div>

        <button class="s-featured-products__next js-featured-next" aria-label="<?= esc_attr__('Próximo', 'lucci-fresh') ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="23" viewBox="0 0 12 23" fill="none">
            <path d="M0.75 22.0833L11.0833 11.4167L0.75 0.75" stroke="#FD8426" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
    </div>
  </section>
<?php endif; ?>