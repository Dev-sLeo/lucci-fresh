<?php
// includes/partials/components/_cart-sidebar-content.html.php
// Conteúdo dinâmico do sidebar (atualizado via WC fragments).
defined('ABSPATH') || exit;

if (!function_exists('WC') || !WC()->cart) return;

$cart            = WC()->cart;
$items           = $cart->get_cart();
$coupon_discount = $cart->get_discount_total();
// Desconto progressivo (Progressive Pricing) é aplicado via set_price() no preço
// do item, então get_subtotal() já vem com ele embutido e get_discount_total()
// não o contabiliza. Somamos o desconto de volta ao subtotal exibido para que
// "Subtotal - Desconto = Total" feche certinho, como um cupom.
$tier_discount   = class_exists( 'PPP_Pricing_Engine' ) ? PPP_Pricing_Engine::get_cart_discount_total( $cart ) : 0.0;
$subtotal        = $cart->get_subtotal() + $tier_discount;
$discount        = $coupon_discount + $tier_discount;
$total           = $cart->get_total('');
$has_items    = !empty($items);
$checkout_url = wc_get_checkout_url();
$nonce        = wp_create_nonce('lucci-cart-nonce');
?>
<div id="cart-sidebar-content" class="c-cart-sidebar__content">

  <!-- ── Banner de desconto (gerado a partir das regras AWDP) ─────────── -->
  <?php include PATHS_PARTIALS . '/components/_cart-discount-banner.html.php'; ?>

  <!-- ── Lista de itens ───────────────────────────────────────────────── -->
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
          // Usa o preço do item do carrinho (já modificado pelo AWDP via set_price)
          $price   = 'R$' . number_format((float) $cart_item['data']->get_price(), 2, ',', '.');
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
                    <input
                      class="c-cart-sidebar__qty-num"
                      type="number"
                      inputmode="numeric"
                      min="0"
                      step="1"
                      value="<?= esc_attr($qty) ?>"
                      aria-label="<?= esc_attr__('Quantidade', 'lucci-fresh') ?>">
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

  <!-- ── Footer: totais + CTA ─────────────────────────────────────────── -->
  <?php if ($has_items) : ?>
    <div class="c-cart-sidebar__footer">
      <div class="c-cart-sidebar__totals">

        <div class="c-cart-sidebar__total-row">
          <span><?= esc_html__('Subtotal', 'lucci-fresh') ?></span>
          <span><?= wc_price($subtotal) ?></span>
        </div>

        <?php if ($discount > 0) : ?>
          <div class="c-cart-sidebar__total-row c-cart-sidebar__total-row--discount">
            <span><?= esc_html__('Desconto', 'lucci-fresh') ?></span>
            <span>-&nbsp;<?= wc_price($discount) ?></span>
          </div>
        <?php endif; ?>

      </div>

      <div class="c-cart-sidebar__divider"></div>

      <div class="c-cart-sidebar__total-final">
        <span><?= esc_html__('Total', 'lucci-fresh') ?></span>
        <strong><?= wc_price($total) ?></strong>
      </div>

      <a href="<?= esc_url($checkout_url) ?>" class="u-button u-button__wood c-cart-sidebar__checkout">
        <?= esc_html__('Finalizar Pedido', 'lucci-fresh') ?>
      </a>
    </div>
  <?php endif; ?>

</div><!-- /#cart-sidebar-content -->
</div><!-- /#cart-sidebar-content -->