<?php
/**
 * Order again button
 *
 * Baseado no template original do WooCommerce (order/order-again.php),
 * com a classe de botão do tema (u-button u-button__wood) no lugar
 * da classe padrão do WooCommerce.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined('ABSPATH') || exit;
?>

<p class="order-again">
	<a href="<?php echo esc_url($order_again_url); ?>" class="u-button u-button__wood"><?php esc_html_e('Order again', 'woocommerce'); ?></a>
</p>
