<?php
/**
 * Rodapé "distraction-free" do checkout novo - só a barra final (sem
 * newsletter, sem menu, sem redes sociais), conforme o Figma.
 */
defined('ABSPATH') || exit;
?>
<footer class="o-footer-checkout">
  <div class="s-container">
    <div class="o-footer-checkout__inner">
      <p><?= esc_html__('Lucci Fresh • Refeições feitas com amor', 'lucci-fresh') ?></p>
      <p>
        <a href="<?= esc_url(home_url('/politica-de-privacidade/')) ?>"><?= esc_html__('Privacidade', 'lucci-fresh') ?></a>
        <span aria-hidden="true"> · </span>
        <?= esc_html__('Precisa de ajuda?', 'lucci-fresh') ?>
        <a href="https://wa.me/5511960752237">(11) 96075-2237</a>
      </p>
    </div>
  </div>
</footer>
