<?php

/**
 * Component: Product Card
 *
 * @var WC_Product|null $product  WooCommerce product object
 * @var string          $modifier BEM modifier adicional (opcional)
 */
global $tpl_engine;

$modifier = $modifier ?? '';

if (empty($product)) {
  global $product;
}

if (! $product || ! ($product instanceof WC_Product)) {
  return;
}

$name       = $product->get_name();
$permalink  = get_permalink($product->get_id());
$price_raw    = $product->get_price();
$price_value  = !empty($price_raw)
  ? wc_price($price_raw)
  : '<span class="woocommerce-Price-amount amount"><bdi>R$&nbsp;0</bdi></span>';
$in_stock   = $product->is_in_stock();

if (has_post_thumbnail($product->get_id())) {
  $thumbnail = get_the_post_thumbnail(
    $product->get_id(),
    'full',
    ['class' => 'c-product-card__image', 'loading' => 'lazy']
  );
} else {
  $thumbnail = '<img src="' . esc_url(wc_placeholder_img_src()) . '" class="c-product-card__image" alt="' . esc_attr__('Produto sem imagem', 'lucci-fresh') . '" loading="lazy">';
}
?>
<article class="c-product-card <?= esc_attr($modifier) ?>">

  <a href="<?= esc_url($permalink) ?>" class="c-product-card__image-link" tabindex="-1" aria-hidden="true">
    <div class="c-product-card__image-wrap">
      <?= $thumbnail ?>
    </div>
  </a>

  <div class="c-product-card__body">

    <div class="c-product-card__info">
      <a href="<?= esc_url($permalink) ?>" class="c-product-card__name">
        <?= esc_html($name) ?>
      </a>
      <p class="c-product-card__description"><?= esc_html($product->get_short_description()) ?></p>
    </div>

    <div class="c-product-card__actions">
      <span class="c-product-card__price">
        <?= $price_value ?>
      </span>

      <?php if ($in_stock) : ?>
        <a href="<?= esc_url($product->add_to_cart_url()) ?>"
          data-product_id="<?= esc_attr($product->get_id()) ?>"
          data-product_sku="<?= esc_attr($product->get_sku()) ?>"
          class="c-product-card__cta add_to_cart_button ajax_add_to_cart"
          rel="nofollow">
          <?= esc_html__('Adicionar', 'lucci-fresh') ?>
        </a>
      <?php else : ?>
        <a href="<?= esc_url($permalink) ?>" class="c-product-card__cta">
          <?= esc_html__('Ver produto', 'lucci-fresh') ?>
        </a>
      <?php endif; ?>
    </div>

  </div>

</article>