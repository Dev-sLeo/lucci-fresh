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

        <?php if (has_custom_logo()) : ?>
          <?php the_custom_logo(); ?>
        <?php else : ?>
          <a href="<?= esc_url(home_url('/')) ?>" class="o-header__logo" aria-label="<?= esc_attr__('Lucci Fresh – Página inicial', 'lucci-fresh') ?>">
            <span class="o-header__logo-text">Lucci Fresh</span>
          </a>
        <?php endif; ?>

        <!-- Nav (desktop) -->
        <nav class="o-header__nav" aria-label="<?= esc_attr__('Menu principal', 'lucci-fresh') ?>">
          <?= $main_menu ?>
        </nav>

        <!-- Actions -->
        <div class="o-header__actions">

          <!-- Busca (desktop) -->
          <button class="o-header__search" type="button" aria-expanded="false" aria-label="<?= esc_attr__('Buscar', 'lucci-fresh') ?>">
            <span class="o-header__search-icon o-header__search-icon--default"><?php $tpl_engine->svg('icons/lupa') ?></span>
            <span class="o-header__search-icon o-header__search-icon--open">
              <svg width="49" height="49" viewBox="0 0 49 49" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24.5" cy="24.5" r="24.5" fill="white" />
                <path d="M30.8334 29.8333L35.5 34.5" stroke="#DA864D" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M33.1667 22.8333C33.1667 17.6787 28.988 13.5 23.8333 13.5C18.6787 13.5 14.5 17.6787 14.5 22.8333C14.5 27.988 18.6787 32.1667 23.8333 32.1667C28.988 32.1667 33.1667 27.988 33.1667 22.8333Z" stroke="#DA864D" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
              </svg>

            </span>
          </button>

          <!-- Busca (mobile) -->
          <button class="o-header__search o-header__search--mobile" type="button" aria-expanded="false" aria-label="<?= esc_attr__('Buscar', 'lucci-fresh') ?>">
            <span class="o-header__search-icon o-header__search-icon--default"><?php $tpl_engine->svg('icons/lupa') ?></span>
            <span class="o-header__search-icon o-header__search-icon--open"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                <path d="M19.8335 19.8333L24.5002 24.5" stroke="#DA864D" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M22.1667 12.8333C22.1667 7.67867 17.988 3.5 12.8333 3.5C7.67867 3.5 3.5 7.67867 3.5 12.8333C3.5 17.988 7.67867 22.1667 12.8333 22.1667C17.988 22.1667 22.1667 17.988 22.1667 12.8333Z" stroke="#DA864D" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
              </svg></span>
          </button>

          <!-- Minha Conta (desktop only) -->
          <a href="<?= esc_url($account_url) ?>" class="o-header__icon-btn o-header__icon-btn--account" aria-label="<?= esc_attr__('Minha conta', 'lucci-fresh') ?>">
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

  <!-- Search Bar -->
  <div class="o-header__search-bar" id="header-search-bar" aria-hidden="true">
    <div class="s-container">
      <form class="o-header__search-form" role="search" method="get" action="<?= esc_url(home_url('/')) ?>">
        <input
          class="o-header__search-input"
          type="search"
          name="s"
          id="header-search-input"
          placeholder="<?= esc_attr__('Buscar...', 'lucci-fresh') ?>"
          autocomplete="off"
          aria-label="<?= esc_attr__('Campo de busca', 'lucci-fresh') ?>">
        <button class="o-header__search-submit" type="submit" aria-label="<?= esc_attr__('Buscar', 'lucci-fresh') ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
            <path d="M19.8334 19.8333L24.5 24.5" stroke="#DA864D" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M22.1667 12.8333C22.1667 7.67867 17.988 3.5 12.8333 3.5C7.67867 3.5 3.5 7.67867 3.5 12.8333C3.5 17.988 7.67867 22.1667 12.8333 22.1667C17.988 22.1667 22.1667 17.988 22.1667 12.8333Z" stroke="#DA864D" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
        <button class="o-header__search-close js-search-close" type="button" aria-label="<?= esc_attr__('Fechar busca', 'lucci-fresh') ?>">
          <?php $tpl_engine->svg('close-icon') ?>
        </button>
      </form>
    </div>
  </div>

</header><!-- /.o-header -->