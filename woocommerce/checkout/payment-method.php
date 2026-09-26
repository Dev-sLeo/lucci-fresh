<?php
/**
 * Override do template nativo do WooCommerce (checkout/payment-method.php,
 * versão 3.5.0) - além do subtítulo curto abaixo do título de cada gateway
 * (Figma nó 2106:978/1063/1160), o CONTEÚDO de dentro do card (.payment_box)
 * de 3 gateways específicos é substituído por markup fixo pixel-matched ao
 * Figma, em vez de confiar só no texto/campos que cada plugin imprime
 * sozinho:
 *
 * - asaas-pix (nó 2106:1008): o gateway não tem QR/lista/aviso nenhum
 *   antes do pedido existir (isso só aparece DEPOIS, na thank-you page -
 *   ver _pix-aguardando.html.php) - aqui é só um aviso + instruções
 *   estáticas, sem dado nenhum vindo do plugin.
 * - cod (nó 2106:1190): "Na entrega" nativamente não tem NENHUM campo -
 *   o campo "Nome de quem vai receber" é 100% deste tema (salvo em
 *   extension/woocommerce.php, hook woocommerce_checkout_update_order_meta
 *   + validação em woocommerce_checkout_process).
 * - rede_debit/rede_credit/maxipago_debit/maxipago_credit (nó 2106:1063):
 *   os campos de cartão continuam sendo os reais do plugin
 *   ($gateway->payment_fields()) - só o selo "Pagamento à vista" abaixo é
 *   fixo (a loja não parcela, então não existe seletor de parcelas real
 *   pra reaproveitar aqui).
 *
 * Qualquer gateway novo que não esteja nesses grupos cai no fallback
 * original (chama $gateway->payment_fields() normalmente).
 *
 * Resto do arquivo é idêntico ao original: mesmo markup de <li>/<input>/
 * <label>, mesma lógica de has_fields()/get_description() no fallback.
 * O ícone do gateway (get_icon(), ex.: logo da Rede) saiu do <label>
 * porque o Figma não mostra nenhum ícone ali - fica só o texto do título.
 */

defined('ABSPATH') || exit;

$luccifresh_payment_subtitles = [
    'asaas-pix'               => __('Aprovação em instantes', 'lucci-fresh'),
    'lkn_pix_for_woocommerce' => __('Aprovação em instantes', 'lucci-fresh'),
    'rede_debit'              => __('Pague com seu cartão', 'lucci-fresh'),
    'rede_credit'             => __('Pague com seu cartão', 'lucci-fresh'),
    'maxipago_debit'          => __('Pague com seu cartão', 'lucci-fresh'),
    'maxipago_credit'         => __('Pague com seu cartão', 'lucci-fresh'),
    'cod'                     => __('Pagamento na entrega', 'lucci-fresh'),
];
$luccifresh_payment_subtitle = $luccifresh_payment_subtitles[$gateway->id] ?? '';

$luccifresh_card_gateway_ids = ['rede_debit', 'rede_credit', 'maxipago_debit', 'maxipago_credit'];
$luccifresh_cart_total       = WC()->cart ? WC()->cart->get_total() : wc_price(0);
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr($gateway->id); ?>">
	<input id="payment_method_<?php echo esc_attr($gateway->id); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr($gateway->id); ?>" <?php checked($gateway->chosen, true); ?> data-order_button_text="<?php echo esc_attr($gateway->order_button_text); ?>" />

	<label for="payment_method_<?php echo esc_attr($gateway->id); ?>">
		<?php echo $gateway->get_title(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
	</label>
	<?php if ($luccifresh_payment_subtitle) : ?>
		<span class="payment-method-subtitle"><?php echo esc_html($luccifresh_payment_subtitle); ?></span>
	<?php endif; ?>

	<?php if ('asaas-pix' === $gateway->id) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr($gateway->id); ?>" <?php if (!$gateway->chosen) : ?>style="display:none;"<?php endif; ?>>
			<div class="c-pix-wait__notice">
				<p class="c-pix-wait__notice-title"><?php esc_html_e('Rápido, simples e seguro', 'lucci-fresh'); ?></p>
				<p class="c-pix-wait__notice-text"><?php esc_html_e('Você receberá o QR Code e o código Pix após revisar e confirmar o pedido.', 'lucci-fresh'); ?></p>
			</div>
			<ol class="c-pix-wait__steps">
				<li><?php esc_html_e('Abra o aplicativo do seu banco.', 'lucci-fresh'); ?></li>
				<li><?php esc_html_e('Leia o QR Code ou cole o código Pix.', 'lucci-fresh'); ?></li>
				<li>
					<?php
					echo esc_html(sprintf(
						/* translators: %s: valor total do pedido */
						__('Confirme o pagamento de %s.', 'lucci-fresh'),
						wp_strip_all_tags($luccifresh_cart_total)
					));
					?>
				</li>
			</ol>
			<p class="c-checkout-step__footnote"><?php esc_html_e('O pedido é confirmado após a aprovação do pagamento.', 'lucci-fresh'); ?></p>
		</div>

	<?php elseif ('cod' === $gateway->id) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr($gateway->id); ?>" <?php if (!$gateway->chosen) : ?>style="display:none;"<?php endif; ?>>
			<div class="form-row">
				<label for="cod_receiver_name">
					<?php esc_html_e('Nome de quem vai receber', 'lucci-fresh'); ?>
					<span class="required">*</span>
				</label>
				<input
					type="text"
					class="input-text"
					name="cod_receiver_name"
					id="cod_receiver_name"
					placeholder="<?php esc_attr_e('Nome', 'lucci-fresh'); ?>"
					value="<?php echo esc_attr(wc_clean(wp_unslash($_POST['cod_receiver_name'] ?? ''))); /* phpcs:ignore WordPress.Security.NonceVerification.Missing */ ?>"
				/>
			</div>
			<div class="c-checkout-step__info-box">
				<p class="c-checkout-step__info-box-title"><?php esc_html_e('Pagamento à vista', 'lucci-fresh'); ?></p>
				<p class="c-checkout-step__info-box-text">
					<?php
					echo esc_html(sprintf(
						/* translators: %s: valor total do pedido */
						__('1× de %s • sem juros', 'lucci-fresh'),
						wp_strip_all_tags($luccifresh_cart_total)
					));
					?>
				</p>
			</div>
		</div>

	<?php elseif (in_array($gateway->id, $luccifresh_card_gateway_ids, true)) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr($gateway->id); ?>" <?php if (!$gateway->chosen) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
			<div class="c-checkout-step__info-box">
				<p class="c-checkout-step__info-box-title"><?php esc_html_e('Pagamento à vista', 'lucci-fresh'); ?></p>
				<p class="c-checkout-step__info-box-text">
					<?php
					echo esc_html(sprintf(
						/* translators: %s: valor total do pedido */
						__('1× de %s • sem juros', 'lucci-fresh'),
						wp_strip_all_tags($luccifresh_cart_total)
					));
					?>
				</p>
			</div>
		</div>

	<?php elseif ($gateway->has_fields() || $gateway->get_description()) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr($gateway->id); ?>" <?php if (!$gateway->chosen) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
