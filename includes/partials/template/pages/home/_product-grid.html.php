<?php
// includes/partials/components/_product-grid.html.php
// Seção "Pizzas" — grid 3×2 de produtos de uma categoria com cabeçalho
global $tpl_engine;

// Padrão: categoria "Santa Pizzinha" (slug: santa-pizzinha)
// Pode ser customizado com ACF se necessário no futuro
$term = get_term_by('slug', 'santa-pizzinha', 'product_cat');

if (!$term) return;

$categoria_titulo   = $term->name;
$categoria_subtitulo = __('Massa tradicional e integral', 'lucci-fresh');
$categoria_url       = get_term_link($term);

$args = [
  'post_type'      => 'product',
  'posts_per_page' => 6,
  'post_status'    => 'publish',
  'tax_query'      => [
    [
      'taxonomy' => 'product_cat',
      'field'    => 'term_id',
      'terms'    => $term->term_id,
    ],
  ],
];

$products = new WP_Query($args);
if (!$products->have_posts()) return;
?>
<section class="s-product-grid">
  <div class="s-container">
    <div class="s-product-grid__header">
      <div class="s-product-grid__heading">
        <h2 class="s-product-grid__title"><?= esc_html($categoria_titulo) ?></h2>
        <div class="s-product-grid__divider" aria-hidden="true"></div>
        <p class="s-product-grid__subtitle"><?= esc_html($categoria_subtitulo) ?></p>
      </div>
      <?php if (!is_wp_error($categoria_url)) : ?>
        <a href="<?= esc_url($categoria_url) ?>" class="u-button u-button__wood s-product-grid__btn-desktop">
          <?= esc_html__('Ver todos sabores', 'lucci-fresh') ?>
        </a>
      <?php endif; ?>
    </div>

    <div class="swiper js-product-grid">
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

    <?php if (!is_wp_error($categoria_url)) : ?>
      <div class="s-product-grid__footer">
        <a href="<?= esc_url($categoria_url) ?>" class="u-button u-button__wood">
          <?= esc_html__('Ver todos sabores', 'lucci-fresh') ?>
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>