<?php
global $tpl_engine;

the_post();
get_header();
?>
<section class="singular-page single-section">
  <div class="s-container">
    <?php the_content(); ?>
  </div>
</section>

<?php get_footer(); ?>