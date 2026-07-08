<?php
// extension/helpers/cart-discount-info.php
// Retorna o progresso do desconto por quantidade (plugin Progressive Pricing) para o banner do carrinho.
defined('ABSPATH') || exit;

/**
 * Agrupa os itens do carrinho pelo conjunto de faixas (tiers) do Progressive Pricing
 * que se aplica a cada um (resolvido via PPP_Pricing_Engine::resolve_tier_set).
 *
 * Itens cujo produto não tem nenhuma faixa configurada (categoria ppp_category
 * sem tiers, ou produto sem categoria) são ignorados.
 *
 * @return array<string, array{tiers: array<int,array>, items: array, quantity: int}>
 *         chave = hash do conjunto de tiers
 */
function lucci_group_cart_items_by_ppp_tiers(WC_Cart $cart): array
{
  $groups = [];

  foreach ($cart->get_cart() as $cart_item) {
    $product = $cart_item['data'] ?? null;
    if (!$product instanceof WC_Product) {
      continue;
    }

    $tiers = PPP_Pricing_Engine::resolve_tier_set($product);
    if (empty($tiers)) {
      continue;
    }

    $key = md5(wp_json_encode($tiers));

    if (!isset($groups[$key])) {
      $groups[$key] = [
        'tiers'    => $tiers,
        'items'    => [],
        'quantity' => 0,
      ];
    }

    $groups[$key]['items'][]  = $cart_item;
    $groups[$key]['quantity'] += (int) $cart_item['quantity'];
  }

  return $groups;
}

/**
 * Calcula o progresso em direção à próxima faixa de desconto do Progressive Pricing.
 *
 * Quando o carrinho tem itens cobertos por mais de um conjunto de faixas
 * (ex.: categorias de preço diferentes), usa o grupo com maior quantidade.
 *
 * Retorna um array com:
 *  - show      (bool)   — se deve exibir o banner
 *  - current   (int)    — quantidade atual no grupo
 *  - threshold (int)    — quantidade necessária para a próxima faixa
 *  - needed    (int)    — quantos itens faltam
 *  - fraction  (string) — ex: "3/5"
 *  - pct       (float)  — percentual de preenchimento da barra (0–100)
 *  - hint      (string) — mensagem de dica (HTML seguro)
 *  - hit       (bool)   — true se a faixa de maior desconto já foi atingida
 *
 * @return array
 */
function lucci_get_ppp_discount_info(): array
{
  if (!class_exists('PPP_Pricing_Engine') || !function_exists('WC') || !WC()->cart) {
    return ['show' => false];
  }

  $cart = WC()->cart;
  if ($cart->is_empty()) {
    return ['show' => false];
  }

  $groups = lucci_group_cart_items_by_ppp_tiers($cart);
  if (empty($groups)) {
    return ['show' => false];
  }

  // Usa o grupo com a maior quantidade de itens (faixa mais relevante para o cliente)
  usort($groups, fn($a, $b) => $b['quantity'] - $a['quantity']);
  $group = $groups[0];

  $tiers         = $group['tiers'];
  $current_count = $group['quantity'];
  $items         = $group['items'];

  $last_tier     = end($tiers);
  $max_threshold = (int) $last_tier['min_qty'];

  $next_tier    = null;
  $current_tier = null;

  foreach ($tiers as $tier) {
    $min = (int) $tier['min_qty'];
    $max = '' === $tier['max_qty'] ? null : (int) $tier['max_qty'];

    if ($current_count < $min) {
      if ($next_tier === null) {
        $next_tier = $tier;
      }
    } elseif (null === $max || $current_count <= $max) {
      $current_tier = $tier;
    }
  }

  // Próxima faixa ainda não atingida
  if ($next_tier !== null) {
    $threshold = (int) $next_tier['min_qty'];
    $needed    = $threshold - $current_count;

    return [
      'show'      => true,
      'current'   => $current_count,
      'threshold' => $threshold,
      'needed'    => $needed,
      'fraction'  => $current_count . '/' . $threshold,
      'pct'       => min(100.0, ($current_count / max(1, $threshold)) * 100),
      'hint'      => lucci_ppp_build_hint($needed, $next_tier, $items, $current_count),
      'hit'       => false,
    ];
  }

  // Maior faixa de desconto já atingida
  if ($current_tier !== null) {
    return [
      'show'      => true,
      'current'   => $current_count,
      'threshold' => $max_threshold,
      'needed'    => 0,
      'fraction'  => $current_count . '/' . $max_threshold,
      'pct'       => 100.0,
      'hint'      => lucci_ppp_build_hit_hint($current_tier, $items, $current_count),
      'hit'       => true,
    ];
  }

  return ['show' => false];
}

/**
 * Calcula o preço unitário médio REGULAR (sem nenhum desconto do Progressive
 * Pricing já aplicado) dos itens informados. Usar `line_subtotal` seria errado
 * aqui: assim que uma faixa de desconto é atingida, o PPP já reduz o preço dos
 * itens no carrinho — subtrair o desconto de novo em cima desse valor já
 * descontado faz o cálculo ficar cada vez mais errado a cada faixa.
 *
 * @param array $items         Itens do carrinho cobertos pelo conjunto de faixas
 * @param int   $current_count Quantidade total desses itens
 */
function lucci_ppp_avg_regular_price(array $items, int $current_count): float
{
  if ($current_count <= 0) {
    return 0.0;
  }

  $total = 0.0;
  foreach ($items as $item) {
    $product = $item['data'] ?? null;
    if (!$product instanceof WC_Product) {
      continue;
    }
    $regular_price = (float) $product->get_regular_price();
    $total += $regular_price * (int) $item['quantity'];
  }

  return $total / $current_count;
}

/**
 * Monta a mensagem de dica para quando o desconto ainda não foi atingido.
 *
 * @param array $tier  Faixa do Progressive Pricing (min_qty, max_qty, discount_type, discount_value)
 * @param array $items Itens do carrinho cobertos por esse conjunto de faixas
 */
function lucci_ppp_build_hint(int $needed, array $tier, array $items, int $current_count): string
{
  $label = $needed === 1
    ? esc_html__('marmita', 'lucci-fresh')
    : esc_html__('marmitas', 'lucci-fresh');

  $dis_type  = $tier['discount_type'] ?? '';
  $dis_value = (float) ($tier['discount_value'] ?? 0);

  if ('percent' === $dis_type && !empty($items) && $current_count > 0) {
    $avg_price        = lucci_ppp_avg_regular_price($items, $current_count);
    $discounted_price = $avg_price * (1 - $dis_value / 100);

    return sprintf(
      /* translators: 1: número de itens, 2: label (marmita/marmitas), 3: preço com desconto */
      esc_html__('Adicione mais %1$d %2$s e cada sai R$&nbsp;%3$s', 'lucci-fresh'),
      $needed,
      $label,
      number_format($discounted_price, 2, ',', '.')
    );
  }

  if ('fixed_price' === $dis_type && $dis_value > 0) {
    return sprintf(
      /* translators: 1: número de itens, 2: label, 3: preço unitário */
      esc_html__('Adicione mais %1$d %2$s e cada sai R$&nbsp;%3$s', 'lucci-fresh'),
      $needed,
      $label,
      number_format($dis_value, 2, ',', '.')
    );
  }

  if ('fixed_amount' === $dis_type && $dis_value > 0 && !empty($items) && $current_count > 0) {
    $avg_price        = lucci_ppp_avg_regular_price($items, $current_count);
    $discounted_price = max(0, $avg_price - $dis_value);

    return sprintf(
      /* translators: 1: número de itens, 2: label (marmita/marmitas), 3: preço com desconto */
      esc_html__('Adicione mais %1$d %2$s e cada sai R$&nbsp;%3$s', 'lucci-fresh'),
      $needed,
      $label,
      number_format($discounted_price, 2, ',', '.')
    );
  }

  return sprintf(
    /* translators: 1: número de itens, 2: label */
    esc_html__('Adicione mais %1$d %2$s para ganhar desconto', 'lucci-fresh'),
    $needed,
    $label
  );
}

/**
 * Monta a mensagem de dica para quando a maior faixa de desconto já foi atingida.
 *
 * @param array $tier          Faixa do Progressive Pricing
 * @param array $items         Itens do carrinho cobertos por esse conjunto de faixas
 * @param int   $current_count Quantidade atual no grupo
 */
function lucci_ppp_build_hit_hint(array $tier, array $items = [], int $current_count = 0): string
{
  $dis_type  = $tier['discount_type'] ?? '';
  $dis_value = (float) ($tier['discount_value'] ?? 0);

  if ('percent' === $dis_type) {
    return sprintf(
      /* translators: %s: percentual de desconto */
      esc_html__('Você está recebendo %s%% de desconto!', 'lucci-fresh'),
      number_format($dis_value, 0)
    );
  }

  if ('fixed_amount' === $dis_type && !empty($items) && $current_count > 0) {
    $avg_price        = lucci_ppp_avg_regular_price($items, $current_count);
    $discounted_price = max(0, $avg_price - $dis_value);

    return sprintf(
      /* translators: %s: preço unitário com desconto */
      esc_html__('Você está pagando apenas R$&nbsp;%s cada!', 'lucci-fresh'),
      number_format($discounted_price, 2, ',', '.')
    );
  }

  if ('fixed_amount' === $dis_type) {
    return sprintf(
      /* translators: %s: valor de economia */
      esc_html__('Você está economizando %s!', 'lucci-fresh'),
      'R$&nbsp;' . number_format($dis_value, 2, ',', '.')
    );
  }

  if ('fixed_price' === $dis_type) {
    return sprintf(
      /* translators: %s: preço unitário com desconto */
      esc_html__('Você está pagando apenas R$&nbsp;%s cada!', 'lucci-fresh'),
      number_format($dis_value, 2, ',', '.')
    );
  }

  return esc_html__('Você já está recebendo o maior desconto disponível!', 'lucci-fresh');
}
