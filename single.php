<?php
global $tpl_engine;

get_header();

if (have_posts()) :
  while (have_posts()) : the_post();
?>
    <div class="s-single-page">
      <?php $tpl_engine->partial('template/pages/single/hero'); ?>
      <?php $tpl_engine->partial('template/pages/single/content'); ?>
      <?php $tpl_engine->partial('template/pages/single/related'); ?>
    </div>
<?php
  endwhile;
endif;

get_footer();
?>