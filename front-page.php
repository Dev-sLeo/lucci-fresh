<?php get_header(); ?>
<?php global $tpl_engine; ?>

<div class="home">
  <?php $tpl_engine->partial('template/global/hero') ?>
  <?php $tpl_engine->partial('template/pages/home/category-slider') ?>
  <?php $tpl_engine->partial('template/pages/home/benefits') ?>
  <?php $tpl_engine->partial('template/pages/home/featured-products') ?>
  <?php $tpl_engine->partial('template/pages/home/product-grid') ?>
  <?php $tpl_engine->partial('template/global/about') ?>
  <?php $tpl_engine->partial('template/global/store') ?>
  <?php $tpl_engine->partial('template/global/testimonials') ?>
</div>

<?php get_footer(); ?>