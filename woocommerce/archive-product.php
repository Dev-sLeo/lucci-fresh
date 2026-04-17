<?php

/**
 * Archive Product (Tours) - Template da página de arquivo de tours
 */
defined('ABSPATH') || exit;

global $tpl_engine;
get_header();
?>

<div class="p-tours">

  <?php $tpl_engine->partial('template/pages/tours/hero'); ?>
  <?php $tpl_engine->partial('template/pages/tours/filter'); ?>

  <?php $tpl_engine->partial('template/pages/home/product-slider', ['data' => [
    'title'         => 'Próximos Tours',
    'ver_todos_url' => '',
    'slider_id'     => 'proximos-tours',
  ]]); ?>

  <?php $tpl_engine->partial('template/pages/home/product-slider', ['data' => [
    'title'     => 'Tour por meses',
    'slider_id' => 'tour-por-meses',
    'tabs'      => [
      ['label' => 'Janeiro',   'id' => 'jan', 'taxonomy' => 'tour_mes', 'term_slug' => 'janeiro'],
      ['label' => 'Fevereiro', 'id' => 'fev', 'taxonomy' => 'tour_mes', 'term_slug' => 'fevereiro'],
      ['label' => 'Março',     'id' => 'mar', 'taxonomy' => 'tour_mes', 'term_slug' => 'marco'],
      ['label' => 'Abril',     'id' => 'abr', 'taxonomy' => 'tour_mes', 'term_slug' => 'abril'],
      ['label' => 'Maio',      'id' => 'mai', 'taxonomy' => 'tour_mes', 'term_slug' => 'maio'],
      ['label' => 'Junho',     'id' => 'jun', 'taxonomy' => 'tour_mes', 'term_slug' => 'junho'],
      ['label' => 'Julho',     'id' => 'jul', 'taxonomy' => 'tour_mes', 'term_slug' => 'julho'],
      ['label' => 'Agosto',    'id' => 'ago', 'taxonomy' => 'tour_mes', 'term_slug' => 'agosto'],
      ['label' => 'Setembro',  'id' => 'set', 'taxonomy' => 'tour_mes', 'term_slug' => 'setembro'],
      ['label' => 'Outubro',   'id' => 'out', 'taxonomy' => 'tour_mes', 'term_slug' => 'outubro'],
      ['label' => 'Novembro',  'id' => 'nov', 'taxonomy' => 'tour_mes', 'term_slug' => 'novembro'],
      ['label' => 'Dezembro',  'id' => 'dez', 'taxonomy' => 'tour_mes', 'term_slug' => 'dezembro'],
    ],
  ]]); ?>

  <?php $tpl_engine->partial('template/pages/home/product-slider', ['data' => [
    'title'     => 'Estilo de Tour',
    'slider_id' => 'estilo-tour',
    'tabs'      => [
      ['label' => 'Cultural',              'id' => 'cultural',     'taxonomy' => 'estilo_tour', 'term_slug' => 'cultural'],
      ['label' => 'Degustações & Compras', 'id' => 'degustacoes',  'taxonomy' => 'estilo_tour', 'term_slug' => 'degustacoes-compras'],
      ['label' => 'Experiência',           'id' => 'experiencia',  'taxonomy' => 'estilo_tour', 'term_slug' => 'experiencia'],
      ['label' => 'Natureza',              'id' => 'natureza',     'taxonomy' => 'estilo_tour', 'term_slug' => 'natureza'],
      ['label' => 'Templo',                'id' => 'templo',       'taxonomy' => 'estilo_tour', 'term_slug' => 'templo'],
      ['label' => 'Trem',                  'id' => 'trem',         'taxonomy' => 'estilo_tour', 'term_slug' => 'trem'],
      ['label' => 'Rural',                 'id' => 'rural',        'taxonomy' => 'estilo_tour', 'term_slug' => 'rural'],
      ['label' => 'Walking Tour',          'id' => 'walking-tour', 'taxonomy' => 'estilo_tour', 'term_slug' => 'walking-tour'],
    ],
  ]]); ?>

</div>

<?php get_footer(); ?>