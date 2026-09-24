<?php
/**
 * Indicador de 4 etapas. Por padrão (chamado do wizard) só a etapa 1 fica
 * ativa e o JS (checkoutSteps.js) atualiza as classes ao navegar. Passando
 * `$all_done = true` (ver woocommerce/checkout/thankyou.php, tela
 * "Aguardando Pix") todas as etapas aparecem concluídas (✓), sem nenhuma
 * marcada como ativa - o pedido já foi enviado, não há mais navegação.
 */
defined('ABSPATH') || exit;

$steps = [
  1 => __('Seus dados', 'lucci-fresh'),
  2 => __('Entrega', 'lucci-fresh'),
  3 => __('Pagamento', 'lucci-fresh'),
  4 => __('Revisão', 'lucci-fresh'),
];

$all_done = isset($all_done) && $all_done;
?>

<ol class="c-checkout-progress" data-checkout-progress>
  <?php foreach ($steps as $number => $label) : ?>
    <li
      class="c-checkout-progress__item<?= ($all_done || 1 === $number) ? ' is-active' : ''; ?><?= $all_done ? ' is-done' : ''; ?>"
      data-checkout-progress-item="<?= (int) $number; ?>"
      role="presentation"
      tabindex="-1"
    >
      <span class="c-checkout-progress__number" data-checkout-progress-number><?= $all_done ? '✓' : (int) $number; ?></span>
      <span class="c-checkout-progress__label"><?= esc_html($label); ?></span>
    </li>
  <?php endforeach; ?>
</ol>
