<?php
/**
 * Versão do "Resumo do pedido" para páginas pós-checkout (thank you), a
 * partir do $order já criado - não do carrinho (que já foi esvaziado nesse
 * ponto). Mesmas classes CSS de order-summary.html.php/_checkout-v2.scss
 * (`.luccifresh-summary-table`) para ficar visualmente idêntico.
 *
 * @var WC_Order $order
 */
defined('ABSPATH') || exit;

$item_count = 0;
foreach ($order->get_items() as $order_item) {
    $item_count += $order_item->get_quantity();
}
?>

<aside class="p-checkout-v2__summary" aria-label="<?= esc_attr__('Resumo do pedido', 'lucci-fresh'); ?>">
  <h3><?= esc_html__('Seu pedido', 'lucci-fresh'); ?></h3>
  <p class="p-checkout-v2__summary-subtitle">
    <?= esc_html(sprintf(
      /* translators: %d: número de itens no pedido */
      _n('%d item • preparado com carinho', '%d itens • preparados com carinho', $item_count, 'lucci-fresh'),
      $item_count
    )); ?>
  </p>

  <div id="order_review" class="woocommerce-checkout-review-order">
    <table class="shop_table luccifresh-summary-table">
      <tbody>
        <?php foreach ($order->get_items() as $order_item) :
          $quantity   = max(1, $order_item->get_quantity());
          $unit_price = $order_item->get_subtotal() / $quantity;
        ?>
          <tr>
            <td class="product-name" colspan="2">
              <span class="luccifresh-summary-item__name"><?= esc_html($order_item->get_name()); ?></span>
              <span class="luccifresh-summary-item__qty">
                <?= esc_html(sprintf(
                  /* translators: 1: quantidade, 2: preço unitário */
                  _n('%1$d unidade × %2$s', '%1$d unidades × %2$s', $quantity, 'lucci-fresh'),
                  $quantity,
                  wp_strip_all_tags(wc_price($unit_price))
                )); ?>
              </span>
              <span class="luccifresh-summary-item__total"><?= wp_kses_post(wc_price($order_item->get_subtotal())); ?></span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr class="cart-subtotal">
          <th><?= esc_html__('Subtotal', 'lucci-fresh'); ?></th>
          <td><?= wp_kses_post(wc_price($order->get_subtotal())); ?></td>
        </tr>
        <?php if ($order->get_shipping_total() > 0 || $order->get_shipping_method()) : ?>
          <tr class="shipping-total">
            <th><?= esc_html__('Entrega', 'lucci-fresh'); ?></th>
            <td><?= wp_kses_post(wc_price($order->get_shipping_total())); ?></td>
          </tr>
        <?php endif; ?>
        <?php foreach ($order->get_items('fee') as $fee_item) : ?>
          <tr class="fee">
            <th><?= esc_html($fee_item->get_name()); ?></th>
            <td><?= wp_kses_post(wc_price($fee_item->get_total())); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr class="order-total">
          <th><?= esc_html__('Total', 'lucci-fresh'); ?></th>
          <td><?= wp_kses_post(wc_price($order->get_total())); ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</aside>
