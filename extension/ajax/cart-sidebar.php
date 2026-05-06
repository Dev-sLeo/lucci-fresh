<?php

/**
 * AJAX: Cart Sidebar – remover item e atualizar quantidade
 */
defined('ABSPATH') || exit;

if (!class_exists('WooCommerce')) return;

// ── Remove item ───────────────────────────────────────────────────────────────
function lucci_ajax_remove_cart_item(): void
{
  check_ajax_referer('lucci-cart-nonce', 'nonce');

  $cart_item_key = sanitize_text_field(wp_unslash($_POST['cart_item_key'] ?? ''));

  if ($cart_item_key && WC()->cart) {
    WC()->cart->remove_cart_item($cart_item_key);
    WC()->cart->calculate_totals();
  }

  wp_send_json_success(_lucci_cart_sidebar_fragments());
}
add_action('wp_ajax_lucci_remove_cart_item',        'lucci_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_lucci_remove_cart_item', 'lucci_ajax_remove_cart_item');

// ── Atualizar quantidade ──────────────────────────────────────────────────────
function lucci_ajax_update_cart_qty(): void
{
  check_ajax_referer('lucci-cart-nonce', 'nonce');

  $cart_item_key = sanitize_text_field(wp_unslash($_POST['cart_item_key'] ?? ''));
  $qty           = max(0, absint($_POST['qty'] ?? 0));

  if ($cart_item_key && WC()->cart) {
    if ($qty === 0) {
      WC()->cart->remove_cart_item($cart_item_key);
    } else {
      WC()->cart->set_quantity($cart_item_key, $qty, true);
    }
    WC()->cart->calculate_totals();
  }

  wp_send_json_success(_lucci_cart_sidebar_fragments());
}
add_action('wp_ajax_lucci_update_cart_qty',        'lucci_ajax_update_cart_qty');
add_action('wp_ajax_nopriv_lucci_update_cart_qty', 'lucci_ajax_update_cart_qty');

// ── Helper: gera os fragments ─────────────────────────────────────────────────
function _lucci_cart_sidebar_fragments(): array
{
  return apply_filters('woocommerce_add_to_cart_fragments', []);
}
