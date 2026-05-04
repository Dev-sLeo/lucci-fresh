<?php

/**
 * Template: Categoria de produto (product_cat)
 *
 * Exibe: Hero com desconto progressivo → Grid com filtro por subcategoria →
 *        "Os mais pedidos" (slider igual à home) → Depoimentos
 */
defined('ABSPATH') || exit;

global $tpl_engine;
get_header();
?>

<div class="p-category">

  <?php $tpl_engine->partial('template/pages/category/hero'); ?>

  <?php $tpl_engine->partial('template/pages/category/product-filter'); ?>

  <?php $tpl_engine->partial('template/pages/home/featured-products'); ?>

  <?php $tpl_engine->partial('template/global/testimonials'); ?>

</div>

<?php get_footer(); ?>