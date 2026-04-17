<?php
/**
 * Content Product - Card de produto no loop da loja
 */
defined('ABSPATH') || exit;

global $product;
if (!$product || !$product->is_visible()) return;
?>

<article <?php wc_product_class('c-product-card', $product); ?>>

  <a href="<?= esc_url(get_the_permalink()); ?>" class="c-product-card__image-wrap">
    <?php if (has_post_thumbnail()) : ?>
      <?= get_the_post_thumbnail(get_the_ID(), 'woocommerce_thumbnail', ['class' => 'c-product-card__image', 'loading' => 'lazy']); ?>
    <?php else : ?>
      <img src="<?= esc_url(wc_placeholder_img_src('woocommerce_thumbnail')); ?>"
           alt="<?= esc_attr__('Produto sem imagem', 'arterra'); ?>"
           class="c-product-card__image" loading="lazy">
    <?php endif; ?>

    <?php if ($product->is_on_sale()) : ?>
      <span class="c-product-card__badge c-product-card__badge--sale">
        <?= esc_html__('Oferta', 'arterra'); ?>
      </span>
    <?php endif; ?>

    <?php if (!$product->is_in_stock()) : ?>
      <span class="c-product-card__badge c-product-card__badge--out">
        <?= esc_html__('Esgotado', 'arterra'); ?>
      </span>
    <?php endif; ?>
  </a>

  <div class="c-product-card__body">
    <h2 class="c-product-card__title">
      <a href="<?= esc_url(get_the_permalink()); ?>">
        <?= esc_html(get_the_title()); ?>
      </a>
    </h2>

    <?php if ($product->get_rating_count()) : ?>
      <div class="c-product-card__rating">
        <?= wc_get_rating_html($product->get_average_rating(), $product->get_rating_count()); ?>
      </div>
    <?php endif; ?>

    <div class="c-product-card__price">
      <?= $product->get_price_html(); ?>
    </div>

    <?php if ($product->is_in_stock()) : ?>
      <?php
      echo apply_filters(
        'woocommerce_loop_add_to_cart_link',
        sprintf(
          '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
          esc_url($product->add_to_cart_url()),
          esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
          esc_attr(isset($args['class']) ? $args['class'] : 'button c-btn c-btn--primary c-product-card__add-to-cart add_to_cart_button ajax_add_to_cart'),
          isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
          esc_html($product->add_to_cart_text())
        ),
        $product,
        $args ?? []
      );
      ?>
    <?php endif; ?>
  </div>

</article>
