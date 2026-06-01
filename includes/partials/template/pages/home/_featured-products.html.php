<?php
// includes/partials/components/_featured-products.html.php
global $tpl_engine;

$block  = get_field('mais_pedidos');
$titulo = $block['title'] ?? __('Os mais pedidos', 'lucci-fresh');

$args = [
  'post_type'      => 'product',
  'posts_per_page' => 8,
  'post_status'    => 'publish',
  'meta_query'     => [
    [
      'key'   => '_featured',
      'value' => 'yes',
    ],
  ],
];

$products = new WP_Query($args);

// Fallback: se não houver produtos em destaque, busca os mais recentes
if (!$products->have_posts()) {
  $args = [
    'post_type'      => 'product',
    'posts_per_page' => 8,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
  ];
  $products = new WP_Query($args);
}
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