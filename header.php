<?php

global $tpl_engine;
global $qdi_config;
global $wp;
$current_url = home_url($wp->request);
$site_url = get_site_url();

$current_id = get_the_ID();

$title = get_the_title();

$add_to_body_class = '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5">
  <meta name="HandheldFriendly" content="true">
  <meta http-equiv="X-UA-Compatible" content="IE=9">
  <meta http-equiv="X-UA-TextLayoutMetrics" content="gdi" />
  <meta name="format-detection" content="telephone=no">
  <meta name="author" content="Criação de Site por UpSites">
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css" />
  <style>
    <?php
    if ((ENV == 'production')) {
      echo file_get_contents(TEMPLATEPATH . '/public/css/inline.min.css');
    } else {
      echo file_get_contents(TEMPLATEPATH . '/public/css/inline.min.css');
    }
    ?>
  </style>
  <?php if (ENV == 'production'): ?>
    <link rel="preload" role="production-styles" href="<?php bloginfo('template_url'); ?>/public/css/main.min.css?ver=<?php echo THEME_VERSION; ?>" as="style" onload="this.rel='stylesheet'">
    <noscript>
      <link role="production-styles" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/public/css/main.min.css?ver=<?php echo THEME_VERSION; ?>">
    </noscript>
  <?php else: ?>
    <link rel="preload" href="<?php bloginfo('template_url'); ?>/public/css/main.min.css?ver=<?php echo THEME_VERSION; ?>" as="style" onload="this.rel='stylesheet'">
    <noscript>
      <link role="production-styles" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/public/css/main.min.css?ver=<?php echo THEME_VERSION; ?>">
    </noscript>
  <?php endif; ?>

  <script async src="<?php bloginfo('template_url'); ?>/public/preload/loadCSS.js"></script>

  <?php $tpl_engine->partial('template/scripts'); ?>
  <?php wp_head(); ?>
</head>


<body <?php body_class($add_to_body_class); ?>>
  <?php wp_body_open(); ?>
  <?php $tpl_engine->partial('template/header/header') ?>
  <main id="main" class="main">