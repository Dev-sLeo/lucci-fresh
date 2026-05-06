<?php
// includes/partials/components/_cart-sidebar-content.html.php
// Conteúdo dinâmico do sidebar (atualizado via WC fragments).
defined('ABSPATH') || exit;

if (!function_exists('WC') || !WC()->cart) return;

$cart         = WC()->cart;
$items        = $cart->get_cart();
$subtotal     = $cart->get_subtotal();
$discount     = $cart->get_discount_total();
$total        = $cart->get_total('');
$has_items    = !empty($items);
$checkout_url = wc_get_checkout_url();
$nonce        = wp_create_nonce('lucci-cart-nonce');
?>
<div id="cart-sidebar-content" class="c-cart-sidebar__content">

  <!-- Banner de desconto (oculto inicialmente) -->
  <div class="c-cart-sidebar__discount js-cart-discount" aria-hidden="true">
    <div class="c-cart-sidebar__discount-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </div>
    <div class="c-cart-sidebar__discount-body">
      <p class="c-cart-sidebar__discount-label"><?= esc_html__('Receba seu desconto', 'lucci-fresh') ?></p>
      <p class="c-cart-sidebar__discount-hint js-cart-discount-hint"></p>
    </div>
    <div class="c-cart-sidebar__discount-progress">
      <span class="c-cart-sidebar__discount-fraction js-cart-discount-fraction"></span>
      <div class="c-cart-sidebar__discount-bar">
        <div class="c-cart-sidebar__discount-fill js-cart-discount-fill" style="width: 0%"></div>
      </div>
    </div>
  </div>

  <!-- Lista de itens -->
  <div class="c-cart-sidebar__body">
    <?php if ($has_items) : ?>
      <ul class="c-cart-sidebar__items" data-nonce="<?= esc_attr($nonce) ?>">
        <?php foreach ($items as $cart_item_key => $cart_item) :
          $product = $cart_item['data'];
          $qty     = (int) $cart_item['quantity'];
          $img_id  = $product->get_image_id();
          $img_url = $img_id
            ? wp_get_attachment_image_url($img_id, 'thumbnail')
            : wc_placeholder_img_src('thumbnail');
          $name    = $product->get_name();
          $price   = 'R$' . number_format((float) $product->get_price(), 2, ',', '.');
        ?>
          <li class="c-cart-sidebar__item" data-key="<?= esc_attr($cart_item_key) ?>">
            <div class="c-cart-sidebar__item-image">
              <img src="<?= esc_url($img_url) ?>" alt="<?= esc_attr($name) ?>" width="64" height="64" loading="lazy">
            </div>

            <div class="c-cart-sidebar__item-info">
              <span class="c-cart-sidebar__item-name"><?= esc_html($name) ?></span>

              <div class="c-cart-sidebar__item-footer">
                <span class="c-cart-sidebar__item-price"><?= esc_html($price) ?></span>

                <div class="c-cart-sidebar__item-actions">
                  <button
                    class="c-cart-sidebar__remove js-cart-remove"
                    type="button"
                    aria-label="<?= esc_attr__('Remover item', 'lucci-fresh') ?>"
                    data-key="<?= esc_attr($cart_item_key) ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                      <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </button>

                  <div class="c-cart-sidebar__qty" data-key="<?= esc_attr($cart_item_key) ?>">
                    <button class="c-cart-sidebar__qty-btn js-cart-qty" type="button" data-action="minus" aria-label="<?= esc_attr__('Diminuir quantidade', 'lucci-fresh') ?>">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                      </svg>
                    </button>
                    <span class="c-cart-sidebar__qty-num"><?= esc_html($qty) ?></span>
                    <button class="c-cart-sidebar__qty-btn js-cart-qty" type="button" data-action="plus" aria-label="<?= esc_attr__('Aumentar quantidade', 'lucci-fresh') ?>">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>

    <?php else : ?>
      <div class="c-cart-sidebar__empty">
        <p><?= esc_html__('Seu carrinho está vazio.', 'lucci-fresh') ?></p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Footer: totais + CTA -->
  <?php if ($has_items) : ?>
    <div class="c-cart-sidebar__footer">
      <div class="c-cart-sidebar__totals">

        <div class="c-cart-sidebar__total-row">
          <span><?= esc_html__('Subtotal', 'lucci-fresh') ?></span>
          <span><?= wc_price($subtotal) ?></span>
        </div>

        <!-- Linha de desconto (oculta inicialmente) -->
        <div class="c-cart-sidebar__total-row c-cart-sidebar__total-row--discount js-cart-discount-row" aria-hidden="true">
          <span><?= esc_html__('Desconto', 'lucci-fresh') ?></span>
          <span><?= $discount > 0 ? '- ' . wc_price($discount) : wc_price(0) ?></span>
        </div>

      </div>

      <div class="c-cart-sidebar__divider"></div>

      <div class="c-cart-sidebar__total-final">
        <span><?= esc_html__('Total', 'lucci-fresh') ?></span>
        <strong><?= wc_price($total) ?></strong>
      </div>

      <a href="<?= esc_url($checkout_url) ?>" class="u-button u-button__wood c-cart-sidebar__checkout">
        <?= esc_html__('Continuar pedido', 'lucci-fresh') ?>
      </a>
    </div>
  <?php endif; ?>

</div><!-- /#cart-sidebar-content -->