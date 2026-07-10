<?php
$module  = get_field('header', 'tema');
$top_text = wp_is_mobile() ? $module['top']['mobile'] : $module['top']['text'];
?>
<div class="o-header__top">
  <div class="s-container">
    <p class="o-header__top-text"><?= wp_kses($top_text, ['strong' => [], 'em' => [], 'br' => []]) ?></p>
  </div>
</div>