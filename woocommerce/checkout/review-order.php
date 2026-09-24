<?php
/**
 * Review order table.
 *
 * Cópia de woocommerce/templates/checkout/review-order.php (v5.2.0), com
 * mudanças que só entram em vigor quando o checkout novo está ativo (ver
 * luccifresh_new_checkout_enabled() em extension/woocommerce.php). No
 * checkout antigo (opção desligada) o comportamento é idêntico ao template
 * padrão do WooCommerce.
 *
 * Mudanças, todas condicionadas a $luccifresh_new_checkout:
 * 1. Cada produto vira um bloco de 3 linhas (nome / quantidade × preço
 *    unitário / subtotal em negrito) num único <td colspan="2">, em vez das
 *    2 colunas padrão do WooCommerce - o Figma não usa layout de tabela
 *    aqui, é um cartão só de texto empilhado.
 * 2. A lista de método de frete (radios) NÃO é impressa aqui, porque o step
 *    "Entrega" já a imprime via wc_cart_totals_shipping_html() (ver
 *    step-entrega.html.php) - evita duas listas de radio com o mesmo `name`
 *    brigando pela seleção. Em vez disso, mostra só o valor calculado (ou
 *    "A calcular" antes de escolher um método), como texto simples.
 *
 * O <table>/<tr>/<td> continuam existindo (só escondidos via CSS, nunca
 * removidos do markup) porque o AJAX nativo do WooCommerce
 * (assets/js/frontend/checkout.js, update_checkout) substitui o conteúdo
 * pelo seletor `.woocommerce-checkout-review-order-table` - trocar essa
 * estrutura por divs quebraria a atualização automática de totais ao mudar
 * endereço/frete.
 */

defined('ABSPATH') || exit;

$luccifresh_new_checkout = function_exists('luccifresh_new_checkout_enabled') && luccifresh_new_checkout_enabled();
?>
<table class="shop_table woocommerce-checkout-review-order-table<?= $luccifresh_new_checkout ? ' luccifresh-summary-table' : ''; ?>">
	<thead>
		<tr>
			<th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
			<th class="product-total"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		do_action('woocommerce_review_order_before_cart_contents');

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

			if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
				?>
				<tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
					<?php if ($luccifresh_new_checkout) : ?>
						<td class="product-name" colspan="2">
							<span class="luccifresh-summary-item__name"><?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?></span>
							<span class="luccifresh-summary-item__qty">
								<?php
								printf(
									/* translators: 1: quantidade, 2: preço unitário */
									esc_html(_n('%1$d unidade × %2$s', '%1$d unidades × %2$s', $cart_item['quantity'], 'lucci-fresh')),
									(int) $cart_item['quantity'],
									wp_kses_post(wc_price(wc_get_price_to_display($_product)))
								);
								echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</span>
							<span class="luccifresh-summary-item__total"><?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</td>
					<?php else : ?>
						<td class="product-name">
							<?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)) . '&nbsp;'; ?>
							<?php echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $cart_item['quantity']) . '</strong>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>
						<td class="product-total">
							<?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>
					<?php endif; ?>
				</tr>
				<?php
			}
		}

		do_action('woocommerce_review_order_after_cart_contents');
		?>
	</tbody>
	<tfoot>

		<tr class="cart-subtotal">
			<th><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
				<th><?php wc_cart_totals_coupon_label($coupon); ?></th>
				<td><?php wc_cart_totals_coupon_html($coupon); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ($luccifresh_new_checkout) : ?>

			<?php
			/**
			 * No Figma a linha "Entrega" aparece desde o step 1 (Seus
			 * dados), com "A calcular" - antes até do cliente informar
			 * endereço. show_shipping() do WooCommerce pode retornar false
			 * até o cliente preencher país/estado de entrega (dependendo da
			 * opção "woocommerce_shipping_cost_requires_address"), o que
			 * escondia a linha inteira bem cedo no fluxo. Aqui só olhamos
			 * needs_shipping() (o carrinho tem produto físico?) - o valor
			 * em si (calculado ou "A calcular") continua dependendo de
			 * método escolhido, como já era.
			 */
			?>
			<?php if (WC()->cart->needs_shipping()) : ?>
				<tr class="shipping-total">
					<th><?php esc_html_e('Entrega', 'lucci-fresh'); ?></th>
					<td>
						<?php
						$luccifresh_chosen_methods = function_exists('wc_get_chosen_shipping_method_ids') ? wc_get_chosen_shipping_method_ids() : [];
						echo $luccifresh_chosen_methods
							? wp_kses_post(WC()->cart->get_cart_shipping_total())
							: esc_html__('A calcular', 'lucci-fresh');
						?>
					</td>
				</tr>
			<?php endif; ?>

		<?php elseif (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

			<?php do_action('woocommerce_review_order_before_shipping'); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action('woocommerce_review_order_after_shipping'); ?>

		<?php endif; ?>

		<?php foreach (WC()->cart->get_fees() as $fee) : ?>
			<tr class="fee">
				<th><?php echo esc_html($fee->name); ?></th>
				<td><?php wc_cart_totals_fee_html($fee); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
			<?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
				<?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
						<th><?php echo esc_html($tax->label); ?></th>
						<td><?php echo wp_kses_post($tax->formatted_amount); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action('woocommerce_review_order_before_order_total'); ?>

		<tr class="order-total">
			<th><?php esc_html_e('Total', 'woocommerce'); ?></th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action('woocommerce_review_order_after_order_total'); ?>

	</tfoot>
</table>
