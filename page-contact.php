<?php
/*
 * Template Name: Contato
 */
get_header();
global $tpl_engine;
?>

<div class="p-contact">
  <?php $tpl_engine->partial('template/pages/contact/hero') ?>
  <?php $tpl_engine->partial('template/pages/contact/contact') ?>
</div>

<?php get_footer(); ?>