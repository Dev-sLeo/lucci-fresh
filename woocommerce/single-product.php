<?php

/**
 * Single Product - Template de página de produto individual
 */
defined('ABSPATH') || exit;

get_header();
global $tpl_engine;

while (have_posts()) :
  the_post();
  global $product;
  if (!$product instanceof WC_Product) {
    $product = wc_get_product(get_the_ID());
  }
?>

  <div class="p-single-product">
    <?php $tpl_engine->partial('template/pages/single-product/hero') ?>
    <?php $tpl_engine->partial('template/pages/single-product/benefits') ?>
    <?php $tpl_engine->partial('template/pages/single-product/content') ?>
    <?php $tpl_engine->partial('template/pages/single-product/related') ?>
    <?php $tpl_engine->partial('template/global/testimonials') ?>
  </div>

<?php endwhile;

get_footer();
