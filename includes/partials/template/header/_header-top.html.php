<?php
$module  = get_field('header', 'tema');
$top_text = $module['top_text'] ?? __('REFEIÇÕES CONGELADAS &bull; ENTREGAMOS EM ATÉ 23 KM DA VILA PRUDENTE-SP &bull; TAXA GRÁTIS ACIMA DE R$600,00', 'lucci-fresh');
?>
<div class="o-header__top">
  <div class="s-container">
    <p class="o-header__top-text"><?= wp_kses($top_text, ['strong' => [], 'em' => [], 'br' => []]) ?></p>
  </div>
</div>