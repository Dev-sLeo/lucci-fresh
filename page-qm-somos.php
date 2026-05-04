<?php
/*
 * Template Name: QM Somos
 */
get_header();
global $tpl_engine;
?>

<div class="p-qm-somos">
  <?php $tpl_engine->partial('template/pages/qm-somos/hero') ?>
  <?php $tpl_engine->partial('template/pages/qm-somos/description') ?>
  <?php $tpl_engine->partial('template/global/store') ?>
  <?php $tpl_engine->partial('template/global/testimonials') ?>
</div>

<?php get_footer(); ?>