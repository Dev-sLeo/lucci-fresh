<?php
global $tpl_engine;

$main_menu = wp_nav_menu([
  'theme_location' => 'header',
  'container'      => '',
  'menu_class'     => 'c-main-menu__list',
  'walker'         => new Main_Menu_Walker(),
  'echo'           => false,
]);

$account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/minha-conta/');
$cart_url    = function_exists('wc_get_cart_url')       ? wc_get_cart_url()                  : home_url('/carrinho/');
$cart_count  = (function_exists('WC') && WC()->cart)    ? WC()->cart->get_cart_contents_count() : 0;
?>
<header class="o-header">

  <?php $tpl_engine->partial('template/header/header-top') ?>

  <div class="o-header__main">
    <div class="s-container">
      <div class="o-header__inner">

        <!-- Logo -->
        <a href="<?= esc_url(home_url('/')) ?>" class="o-header__logo" aria-label="<?= esc_attr__('Lucci Fresh – Página inicial', 'lucci-fresh') ?>">
          <?php if (has_custom_logo()) : ?>
            <?php the_custom_logo(); ?>
          <?php else : ?>
            <span class="o-header__logo-text">Lucci Fresh</span>
          <?php endif; ?>
        </a>

        <!-- Nav (desktop) -->
        <nav class="o-header__nav" aria-label="<?= esc_attr__('Menu principal', 'lucci-fresh') ?>">
          <?= $main_menu ?>
        </nav>

        <!-- Actions -->
        <div class="o-header__actions">

          <!-- Busca (desktop) -->
          <button class="o-header__search" type="button" aria-label="<?= esc_attr__('Buscar', 'lucci-fresh') ?>">
            <?php $tpl_engine->svg('icons/lupa') ?>
          </button>

          <!-- Minha conta -->
          <a href="<?= esc_url($account_url) ?>" class="o-header__icon-btn" aria-label="<?= esc_attr__('Minha conta', 'lucci-fresh') ?>">
            <?php $tpl_engine->svg('icons/minha-conta') ?>
          </a>

          <!-- Carrinho -->
          <a href="<?= esc_url($cart_url) ?>" class="o-header__icon-btn o-header__icon-btn--cart" aria-label="<?= esc_attr__('Carrinho', 'lucci-fresh') ?>">
            <?php $tpl_engine->svg('icons/carrinho') ?>
            <?php if ($cart_count > 0) : ?>
              <span class="o-header__cart-count" aria-label="<?= esc_attr(sprintf(__('%d itens no carrinho', 'lucci-fresh'), $cart_count)) ?>">
                <?= esc_html($cart_count) ?>
              </span>
            <?php endif; ?>
          </a>

          <!-- Hamburger (mobile) -->
          <button class="o-header__hamburger js-mobile-menu-open" type="button" aria-label="<?= esc_attr__('Abrir menu', 'lucci-fresh') ?>" aria-expanded="false" aria-controls="mobile-menu">
            <?php $tpl_engine->svg('hamburguer-menu') ?>
          </button>

        </div><!-- /.o-header__actions -->

      </div><!-- /.o-header__inner -->
    </div><!-- /.s-container -->
  </div><!-- /.o-header__main -->

</header><!-- /.o-header -->