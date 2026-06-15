<?php
global $tpl_engine;

the_post();
get_header();

$singular_page_classes = 'singular-page';

if (function_exists('is_account_page') && is_account_page()) {
  $singular_page_classes .= is_user_logged_in() ? ' is-logged-in' : ' is-logged-out';
}

?>
<section class="<?= esc_attr($singular_page_classes); ?>">
  <div class="s-container">
    <?php the_content(); ?>
  </div>
</section>

<?php get_footer(); ?>