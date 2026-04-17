<?php
/**
 * Single Product - Template de página de produto individual
 */
defined('ABSPATH') || exit;
get_header();
?>

<main class="p-single-product">
  <div class="s-container">
    <?php while (have_posts()) : the_post(); ?>
      <?php wc_get_template_part('content', 'single-product'); ?>
    <?php endwhile; ?>
  </div>
</main>

<?php get_footer(); ?>
