<?php

/**
 * Cart Page - Página do carrinho
 */
defined('ABSPATH') || exit;
?>

<div class="p-cart">
  <div class="s-container">
    <?php do_action('woocommerce_before_cart'); ?>

    <?php include PATHS_PARTIALS . '/components/_cart-discount-banner.html.php'; ?>

    <form class="p-cart__form woocommerce-cart-form" action="<?= esc_url(wc_get_cart_url()); ?>" method="post">

      <?php do_action('woocommerce_before_cart_table'); ?>

      <div class="p-cart__table-wrap">
        <table class="p-cart__table shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
          <thead>
            <tr>
              <th class="product-remove">&nbsp;</th>
              <th class="product-thumbnail">&nbsp;</th>
              <th class="product-name"><?= esc_html__('Produto', 'arterra'); ?></th>
              <th class="product-price"><?= esc_html__('Preço', 'arterra'); ?></th>
              <th class="product-quantity"><?= esc_html__('Quantidade', 'arterra'); ?></th>
              <th class="product-subtotal"><?= esc_html__('Subtotal', 'arterra'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php do_action('woocommerce_before_cart_contents'); ?>

            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
              $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
              $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

              if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
            ?>
                <tr class="woocommerce-cart-form__cart-item <?= esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                  <td class="product-remove">
                    <?= apply_filters(
                      'woocommerce_cart_item_remove_link',
                      sprintf(
                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                        esc_html__('Remover item', 'arterra'),
                        esc_attr($product_id),
                        esc_attr($_product->get_sku())
                      ),
                      $cart_item_key
                    ); ?>
                  </td>

                  <td class="product-thumbnail">
                    <?php $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key); ?>
                    <?php if ($product_permalink) : ?>
                      <a href="<?= esc_url($product_permalink); ?>"><?= $thumbnail; ?></a>
                    <?php else : ?>
                      <?= $thumbnail; ?>
                    <?php endif; ?>
                  </td>

                  <td class="product-name" data-title="<?= esc_attr__('Produto', 'arterra'); ?>">
                    <?php if ($product_permalink) : ?>
                      <a href="<?= esc_url($product_permalink); ?>">
                        <?= apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key); ?>
                      </a>
                    <?php else : ?>
                      <?= apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key); ?>
                    <?php endif; ?>
                    <?php do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key); ?>
                    <?= wc_get_formatted_cart_item_data($cart_item); ?>
                    <?php if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) : ?>
                      <p class="backorder_notification"><?= esc_html__('Disponível sob encomenda', 'arterra'); ?></p>
                    <?php endif; ?>
                  </td>

                  <td class="product-price" data-title="<?= esc_attr__('Preço', 'arterra'); ?>">
                    <?= apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                  </td>

                  <td class="product-quantity" data-title="<?= esc_attr__('Quantidade', 'arterra'); ?>">
                    <?php if ($_product->is_sold_individually()) : ?>
                      <span>1 <input type="hidden" name="cart[<?= esc_attr($cart_item_key); ?>][qty]" value="1"></span>
                    <?php else : ?>
                      <?php
                      woocommerce_quantity_input([
                        'input_name'   => "cart[{$cart_item_key}][qty]",
                        'input_value'  => $cart_item['quantity'],
                        'max_value'    => $_product->get_max_purchase_quantity(),
                        'min_value'    => '0',
                        'product_name' => $_product->get_name(),
                      ], $_product);
                      ?>
                    <?php endif; ?>
                  </td>

                  <td class="product-subtotal" data-title="<?= esc_attr__('Subtotal', 'arterra'); ?>">
                    <?= apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                  </td>

                </tr>
            <?php endif;
            endforeach; ?>

            <?php do_action('woocommerce_cart_contents'); ?>

            <tr>
              <td colspan="6" class="actions">
                <?php if (wc_coupons_enabled()) : ?>
                  <div class="coupon">
                    <label for="coupon_code"><?= esc_html__('Cupom', 'arterra'); ?></label>
                    <input type="text" name="coupon_code" class="input-text" id="coupon_code"
                      placeholder="<?= esc_attr__('Código do cupom', 'arterra'); ?>">
                    <button type="submit" class="u-button u-button__wood" name="apply_coupon"
                      value="<?= esc_attr__('Aplicar cupom', 'arterra'); ?>">
                      <?= esc_html__('Aplicar cupom', 'arterra'); ?>
                    </button>
                    <?php do_action('woocommerce_cart_coupon'); ?>
                  </div>
                <?php endif; ?>

                <button type="submit" class="u-button u-button__green" name="update_cart"
                  value="<?= esc_attr__('Atualizar carrinho', 'arterra'); ?>">
                  <?= esc_html__('Atualizar carrinho', 'arterra'); ?>
                </button>

                <?php do_action('woocommerce_cart_actions'); ?>
                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
              </td>
            </tr>

            <?php do_action('woocommerce_after_cart_contents'); ?>
          </tbody>
        </table>
      </div>

      <?php do_action('woocommerce_after_cart_table'); ?>
    </form>

    <div class="p-cart__collaterals cart-collaterals">
      <?php do_action('woocommerce_cart_collaterals'); ?>
    </div>

    <?php do_action('woocommerce_after_cart'); ?>

  </div>
</div>