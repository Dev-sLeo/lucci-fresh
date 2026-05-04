<?php
global $tpl_engine;

$main_menu = wp_nav_menu([
  'theme_location' => 'mobile',
  'depth'          => 2,
  'container'      => '',
  'menu_class'     => 'c-main-menu__list',
  'walker'         => new Main_Menu_Mobile_Walker(),
  'echo'           => false,
]);

$footer        = get_field('footer', 'tema');
$footer        = is_array($footer) ? $footer : [];
$redes_sociais = $footer['redes_sociais'] ?? [];
?>
<div class="menu-mobile" id="mobile-menu" aria-hidden="true">

  <div class="menu-mobile__header">
    <?php if (has_custom_logo()) : ?>
      <?php the_custom_logo(); ?>
    <?php else : ?>
      <a href="<?= esc_url(home_url('/')) ?>" class="menu-mobile__logo" aria-label="<?= esc_attr__('Lucci Fresh – Página inicial', 'lucci-fresh') ?>">
        <span class="menu-mobile__logo-text">Lucci Fresh</span>
      </a>
    <?php endif; ?>
    <button class="menu-mobile__close js-mobile-menu-close" type="button" aria-label="<?= esc_attr__('Fechar menu', 'lucci-fresh') ?>">
      <?php $tpl_engine->svg('close-icon') ?>
    </button>
  </div>

  <nav class="menu-mobile__nav" aria-label="<?= esc_attr__('Menu mobile', 'lucci-fresh') ?>">
    <?= $main_menu ?>
  </nav>

  <?php
  if (empty($redes_sociais)) {
    $redes_sociais = [
      ['rede' => 'instagram', 'link' => '#'],
      ['rede' => 'facebook',  'link' => '#'],
      ['rede' => 'whatsapp',  'link' => '#'],
    ];
  }
  ?>
  <div class="menu-mobile__social">
    <?php foreach ($redes_sociais as $item) :
      $rede = $item['rede'] ?? '';
      $link = $item['link'] ?? '';
      if (!$rede) continue;
    ?>
      <a href="<?= esc_url($link ?: '#') ?>" class="menu-mobile__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?= esc_attr(ucfirst($rede)) ?>">
        <?php $tpl_engine->svg('redes-sociais/' . $rede) ?>
      </a>
    <?php endforeach; ?>
  </div>

</div>