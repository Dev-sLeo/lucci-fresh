<?php
/**
 * Cabeçalho "distraction-free" do checkout novo (sem menu/busca/carrinho),
 * conforme o Figma: logo + "Compra segura" à direita. A faixa de entrega
 * (header-top) continua igual à do site inteiro - só o menu principal some.
 */
defined('ABSPATH') || exit;
?>
<header class="o-header-checkout">
  <div class="s-container">
    <div class="o-header-checkout__inner">
      <?php if (has_custom_logo()) : ?>
        <?php the_custom_logo(); ?>
      <?php else : ?>
        <a href="<?= esc_url(home_url('/')) ?>" class="o-header-checkout__logo" aria-label="<?= esc_attr__('Lucci Fresh – Página inicial', 'lucci-fresh') ?>">
          <span>Lucci Fresh</span>
        </a>
      <?php endif; ?>

      <span class="o-header-checkout__badge"><?= esc_html__('Compra segura', 'lucci-fresh') ?></span>
    </div>
  </div>
</header>
<div class="o-header-checkout__divider"></div>
