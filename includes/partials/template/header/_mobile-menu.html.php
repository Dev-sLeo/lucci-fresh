<?php global $tpl_engine; ?>
<?php

$main_menu = wp_nav_menu(array(
  'theme_location' => 'header',
  'depth'          => 3,
  'container'      => '',
  'menu_class'     => 'c-main-menu__list',
  'walker'         => new Main_Menu_Mobile_Walker(),
  'echo' => false,
));

$contact = get_field('contact', 'tema');
$header = get_field('header', 'tema');
$contact   = is_array($contact) ? $contact : [];
$linkedin   = $contact['linkedin']   ?? '';

?>
<div class="menu-mobile">
  <div class="menu-mobile__header">
    <div class="logo">
      <?php if (has_custom_logo()) : ?>
        <?php if (pathinfo(wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full')[0], PATHINFO_EXTENSION) === 'svg') : ?>
          <a href="<?= site_url() ?>" aria-label="Logo Principal">
            <?= processarArquivo(wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full')[0]) ?>
          </a>
        <?php else : ?>
          <?php the_custom_logo() ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <div class="close-icon">
      <?php $tpl_engine->svg('close-icon') ?>
    </div>
  </div>
  <div class="menu-container">
    <?= $main_menu ?>
    <div class="button-container">
      <a href="<?= esc_url($header['button']['url']) ?>" class="button button-border__blue">Contato</a>
      <a href="" class="button button-border__blue"
        href="<?= esc_url($linkedin); ?>"
        target="_blank"
        rel="noopener"
        aria-label="LinkedIn">Siga nosso LinkedIn
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
          <path d="M7.95715 11.533V19.6061" stroke="#0056FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M12.504 14.9929V19.6061M12.504 14.9929C12.504 13.082 14.0308 11.533 15.9142 11.533C17.7977 11.533 19.3244 13.082 19.3244 14.9929V19.6061M12.504 14.9929V11.533" stroke="#0056FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M7.96618 8.073H7.95593" stroke="#0056FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M2.8418 13.8397C2.8418 8.67478 2.8418 6.09235 4.42326 4.48782C6.00473 2.8833 8.55006 2.8833 13.6407 2.8833C18.7313 2.8833 21.2767 2.8833 22.8582 4.48782C24.4396 6.09235 24.4396 8.67478 24.4396 13.8397C24.4396 19.0045 24.4396 21.587 22.8582 23.1915C21.2767 24.796 18.7313 24.796 13.6407 24.796C8.55006 24.796 6.00473 24.796 4.42326 23.1915C2.8418 21.587 2.8418 19.0045 2.8418 13.8397Z" stroke="#0056FF" stroke-width="1.5" stroke-linejoin="round" />
        </svg>
      </a>
    </div>
  </div>
</div>