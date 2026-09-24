<?php
/**
 * Override do template nativo do WooCommerce (checkout/payment-method.php,
 * versão 3.5.0) - a ÚNICA mudança é adicionar um subtítulo curto abaixo do
 * título de cada gateway, para bater com os "cards" de método de pagamento
 * do Figma (nó 2106:978/1063/1160). Nenhum gateway real tem esse subtítulo
 * nos dados ("Aprovação em instantes" etc. não existem no título/descrição
 * de nenhum deles) - por isso é uma cópia fixa mapeada por ID do gateway,
 * com fallback silencioso (sem subtítulo) para qualquer gateway novo que
 * não esteja no mapa. O ícone do gateway (get_icon(), ex.: logo da Rede)
 * saiu do <label> porque o Figma não mostra nenhum ícone ali - fica só o
 * texto do título.
 *
 * Resto do arquivo é idêntico ao original: mesmo markup de <li>/<input>/
 * <label>/.payment_box, mesma lógica de has_fields()/get_description().
 * Se atualizar o WooCommerce e o template original mudar de versão, vale
 * comparar com o novo woocommerce/templates/checkout/payment-method.php.
 */

defined('ABSPATH') || exit;

$luccifresh_payment_subtitles = [
    'lkn_pix_for_woocommerce' => __('Aprovação em instantes', 'lucci-fresh'),
    'rede_debit'              => __('Pague com seu cartão', 'lucci-fresh'),
    'rede_credit'             => __('Pague com seu cartão', 'lucci-fresh'),
    'maxipago_debit'          => __('Pague com seu cartão', 'lucci-fresh'),
    'maxipago_credit'         => __('Pague com seu cartão', 'lucci-fresh'),
    'cod'                     => __('Pagamento na entrega', 'lucci-fresh'),
];
$luccifresh_payment_subtitle = $luccifresh_payment_subtitles[$gateway->id] ?? '';
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr($gateway->id); ?>">
	<input id="payment_method_<?php echo esc_attr($gateway->id); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr($gateway->id); ?>" <?php checked($gateway->chosen, true); ?> data-order_button_text="<?php echo esc_attr($gateway->order_button_text); ?>" />

	<label for="payment_method_<?php echo esc_attr($gateway->id); ?>">
		<?php echo $gateway->get_title(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
	</label>
	<?php if ($luccifresh_payment_subtitle) : ?>
		<span class="payment-method-subtitle"><?php echo esc_html($luccifresh_payment_subtitle); ?></span>
	<?php endif; ?>
	<?php if ($gateway->has_fields() || $gateway->get_description()) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr($gateway->id); ?>" <?php if (!$gateway->chosen) : /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>style="display:none;"<?php endif; /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
