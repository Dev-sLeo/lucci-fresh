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

// Informações do desconto AWDP
$disc_info    = function_exists('lucci_get_awdp_discount_info') ? lucci_get_awdp_discount_info() : ['show' => false];
$show_disc    = !empty($disc_info['show']);
?>
<div id="cart-sidebar-content" class="c-cart-sidebar__content">

  <!-- ── Banner de desconto (gerado a partir das regras AWDP) ─────────── -->
  <div class="c-cart-sidebar__discount" <?= !$show_disc ? 'aria-hidden="true"' : '' ?>>
    <div class="c-cart-sidebar__discount-top">
      <div class="c-cart-sidebar__discount-icon" aria-hidden="true">
        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M14 25.6667C19.1546 25.6667 23.3333 21.488 23.3333 16.3333C23.3333 9.33333 14 2.33333 14 2.33333C13.5469 5.23474 13.1034 6.79184 11.6666 9.33333C10.2656 8.68572 9.91663 8.16667 9.33329 6.70833C6.99996 9.33333 4.66663 12.8333 4.66663 16.3333C4.66663 21.488 8.8453 25.6667 14 25.6667Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
          <path d="M16.9167 14.5833L11.0834 20.4167" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M11.5209 14.875H11.375M11.6667 14.875C11.6667 15.0361 11.5361 15.1667 11.375 15.1667C11.214 15.1667 11.0834 15.0361 11.0834 14.875C11.0834 14.7139 11.214 14.5833 11.375 14.5833C11.5361 14.5833 11.6667 14.7139 11.6667 14.875Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M16.7709 20.125H16.625M16.9167 20.125C16.9167 20.2861 16.7862 20.4167 16.625 20.4167C16.4639 20.4167 16.3334 20.2861 16.3334 20.125C16.3334 19.9639 16.4639 19.8333 16.625 19.8333C16.7862 19.8333 16.9167 19.9639 16.9167 20.125Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </div>
      <div class="c-cart-sidebar__discount-body">
        <p class="c-cart-sidebar__discount-label"><?= esc_html__('Receba seu desconto', 'lucci-fresh') ?></p>
        <?php if ($show_disc && !empty($disc_info['hint'])) : ?>
          <p class="c-cart-sidebar__discount-hint"><?= wp_kses_post($disc_info['hint']) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($show_disc) : ?>
      <div class="c-cart-sidebar__discount-bottom">
        <span class="c-cart-sidebar__discount-fraction"><?= esc_html($disc_info['fraction']) ?></span>
        <div class="c-cart-sidebar__discount-bars" aria-hidden="true">
          <?php
          $thresh   = max(1, (int) $disc_info['threshold']);
          $current  = min($thresh, (int) $disc_info['current']);
          for ($i = 1; $i <= $thresh; $i++) :
          ?>
            <span class="c-cart-sidebar__discount-bar-seg<?= $i <= $current ? ' is-filled' : '' ?>"></span>
          <?php endfor; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

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