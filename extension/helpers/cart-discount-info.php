<?php
// extension/helpers/cart-discount-info.php
// Retorna o progresso do desconto AWDP (Dynamic Pricing) para o cart sidebar.
defined('ABSPATH') || exit;

/**
 * Calcula o progresso em direção ao desconto de quantidade do AWDP.
 *
 * Retorna um array com:
 *  - show      (bool)   — se deve exibir o banner
 *  - current   (int)    — quantidade atual no carrinho
 *  - threshold (int)    — quantidade necessária para o próximo desconto
 *  - needed    (int)    — quantos itens faltam
 *  - fraction  (string) — ex: "3/5"
 *  - pct       (float)  — percentual de preenchimento da barra (0–100)
 *  - hint      (string) — mensagem de dica (HTML seguro)
 *  - hit       (bool)   — true se o desconto já foi atingido
 *
 * @return array
 */
// Categoria de produto à qual o desconto por quantidade se aplica
const LUCCI_AWDP_DISCOUNT_CATEGORY = 'marmitas';

/**
 * Retorna apenas os itens do carrinho cujo produto pertence à categoria informada.
 *
 * @return array<string, array> cart_item_key => cart_item
 */
function lucci_get_cart_items_in_category(WC_Cart $cart, string $category_slug): array
{
  $items = [];

  foreach ($cart->get_cart() as $key => $item) {
    $product_id = $item['variation_id'] ?: $item['product_id'];
    if (has_term($category_slug, 'product_cat', $product_id)) {
      $items[$key] = $item;
    }
  }

  return $items;
}

function lucci_get_awdp_discount_info(): array
{
  if (!defined('AWDP_POST_TYPE') || !function_exists('WC') || !WC()->cart) {
    return ['show' => false];
  }

  $cart = WC()->cart;
  if ($cart->is_empty()) {
    return ['show' => false];
  }

  // Considera somente itens da categoria "Marmitas" para o cálculo do desconto
  $discount_items = lucci_get_cart_items_in_category($cart, LUCCI_AWDP_DISCOUNT_CATEGORY);
  if (empty($discount_items)) {
    return ['show' => false];
  }

  $rules_posts = get_posts([
    'post_type'      => AWDP_POST_TYPE,
    'post_status'    => 'publish',
    'numberposts'    => -1,
    'no_found_rows'  => true,
    'update_post_term_cache' => false,
  ]);

  if (empty($rules_posts)) {
    return ['show' => false];
  }

  foreach ($rules_posts as $rule_post) {
    $type = get_post_meta($rule_post->ID, 'discount_type', true);
    if ($type !== 'cart_quantity') {
      continue;
    }

    $qty_type           = get_post_meta($rule_post->ID, 'discount_quantity_type', true);
    $quantity_rules_raw = get_post_meta($rule_post->ID, 'discount_quantityranges', true);

    if (empty($quantity_rules_raw)) {
      continue;
    }

    $quantity_rules = maybe_unserialize($quantity_rules_raw);
    if (empty($quantity_rules) || !is_array($quantity_rules)) {
      continue;
    }

    // Ordena por start_range crescente
    usort($quantity_rules, fn($a, $b) => (int) $a['start_range'] - (int) $b['start_range']);

    // Contagem atual de acordo com o tipo da regra (somente itens de "Marmitas")
    if ($qty_type === 'type_cart') {
      // Número de tipos de produto distintos no carrinho
      $current_count = count($discount_items);
    } else {
      // Quantidade total de itens no carrinho (padrão)
      $current_count = (int) array_sum(wp_list_pluck($discount_items, 'quantity'));
    }

    $last_rule    = end($quantity_rules);
    $max_threshold = (int) $last_rule['start_range'];

    $next_tier    = null;
    $current_tier = null;

    foreach ($quantity_rules as $qr) {
      $start = (int) $qr['start_range'];
      $end   = isset($qr['end_range']) && $qr['end_range'] !== '' ? (int) $qr['end_range'] : 0;

      if ($current_count < $start) {
        if ($next_tier === null) {
          $next_tier = $qr;
        }
      } elseif ($end === 0 || $current_count <= $end) {
        $current_tier = $qr;
      }
    }

    // Próximo tier não atingido ainda
    if ($next_tier !== null) {
      $threshold = (int) $next_tier['start_range'];
      $needed    = $threshold - $current_count;
      $dis_type  = $next_tier['dis_type'] ?? '';
      $dis_value = (float) ($next_tier['dis_value'] ?? 0);

      return [
        'show'      => true,
        'current'   => $current_count,
        'threshold' => $threshold,
        'needed'    => $needed,
        'fraction'  => $current_count . '/' . $threshold,
        'pct'       => min(100.0, ($current_count / max(1, $threshold)) * 100),
        'hint'      => lucci_awdp_build_hint($needed, $dis_type, $dis_value, $discount_items, $current_count),
        'hit'       => false,
      ];
    }

    // Desconto já atingido
    if ($current_tier !== null) {
      $dis_type  = $current_tier['dis_type'] ?? '';
      $dis_value = (float) ($current_tier['dis_value'] ?? 0);

      if ($dis_type === 'percentage') {
        $hint = sprintf(
          /* translators: %s: percentual de desconto */
          esc_html__('Você está recebendo %s%% de desconto!', 'lucci-fresh'),
          number_format($dis_value, 0)
        );
      } else {
        $hint = sprintf(
          /* translators: %s: valor de economia */
          esc_html__('Você está economizando %s!', 'lucci-fresh'),
          'R$&nbsp;' . number_format($dis_value, 2, ',', '.')
        );
      }

      return [
        'show'      => true,
        'current'   => $current_count,
        'threshold' => $max_threshold,
        'needed'    => 0,
        'fraction'  => $current_count . '/' . $max_threshold,
        'pct'       => 100.0,
        'hint'      => $hint,
        'hit'       => true,
      ];
    }
  }

  return ['show' => false];
}

/**
 * Monta a mensagem de dica com base no tipo e valor de desconto.
 */
function lucci_awdp_build_hint(int $needed, string $dis_type, float $dis_value, array $discount_items, int $current_count): string
{
  $label = $needed === 1
    ? esc_html__('marmita', 'lucci-fresh')
    : esc_html__('marmitas', 'lucci-fresh');

  if ($dis_type === 'percentage' && !empty($discount_items) && $current_count > 0) {
    // Calcula o preço médio apenas dos itens de "Marmitas" e aplica o desconto
    $subtotal = array_sum(array_map(
      fn($item) => (float) $item['line_subtotal'],
      $discount_items
    ));
    $avg_price        = $subtotal / $current_count;
    $discounted_price = $avg_price * (1 - $dis_value / 100);

    return sprintf(
      /* translators: 1: número de itens, 2: label (marmita/marmitas), 3: preço com desconto */
      esc_html__('Adicione mais %1$d %2$s e cada sai R$&nbsp;%3$s', 'lucci-fresh'),
      $needed,
      $label,
      number_format($discounted_price, 2, ',', '.')
    );
  }

  if ($dis_type === 'fixed') {
    return sprintf(
      /* translators: 1: número de itens, 2: label, 3: valor de desconto */
      esc_html__('Adicione mais %1$d %2$s e economize R$&nbsp;%3$s', 'lucci-fresh'),
      $needed,
      $label,
      number_format($dis_value, 2, ',', '.')
    );
  }

  return sprintf(
    /* translators: 1: número de itens, 2: label */
    esc_html__('Adicione mais %1$d %2$s para ganhar desconto', 'lucci-fresh'),
    $needed,
    $label
  );
}
