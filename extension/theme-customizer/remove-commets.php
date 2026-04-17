<?php

/**
 * Remove comentários do WordPress completamente (UI, suporte, feeds e REST API).
 * Cole no functions.php do tema (ou em um plugin).
 */

add_action('init', function () {

  // 1) Desabilita comentários/pingbacks em qualquer post type que suporte comments
  foreach (get_post_types([], 'names') as $post_type) {
    if (post_type_supports($post_type, 'comments')) {
      remove_post_type_support($post_type, 'comments');
      remove_post_type_support($post_type, 'trackbacks');
    }
  }
}, 100);

// 2) Fecha comentários e pings no front (fallback)
add_filter('comments_open', '__return_false', 9999);
add_filter('pings_open', '__return_false', 9999);

// 3) Não retorna comentários existentes para o template
add_filter('comments_array', '__return_empty_array', 9999);

// 4) Remove páginas/menus de comentários do admin + barra superior
add_action('admin_menu', function () {
  remove_menu_page('edit-comments.php');
}, 999);

add_action('wp_before_admin_bar_render', function () {
  global $wp_admin_bar;
  if (is_object($wp_admin_bar)) {
    $wp_admin_bar->remove_node('comments');
  }
}, 999);

// 5) Remove widget “Atividade Recente” que pode mostrar comentários
add_action('wp_dashboard_setup', function () {
  remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}, 999);

// 6) Redireciona se alguém tentar acessar a tela de comentários diretamente
add_action('admin_init', function () {
  global $pagenow;
  if ($pagenow === 'edit-comments.php') {
    wp_safe_redirect(admin_url());
    exit;
  }
}, 1);

// 7) Remove links de comentários do frontend (feeds)
add_filter('post_comments_feed_link', '__return_empty_string', 9999);

// 8) Remove endpoints de comentários da REST API + bloqueia acesso caso algum plugin re-registre
add_filter('rest_endpoints', function ($endpoints) {

  // Remove as rotas padrão do WP para comentários
  foreach (array_keys($endpoints) as $route) {
    if (strpos($route, '/wp/v2/comments') !== false) {
      unset($endpoints[$route]);
    }
  }

  return $endpoints;
}, 9999);

add_filter('rest_pre_dispatch', function ($result, $server, $request) {
  $route = $request->get_route();

  // Bloqueia qualquer tentativa de acessar comentários via REST
  if (strpos($route, '/wp/v2/comments') !== false) {
    return new WP_Error(
      'rest_comments_disabled',
      'Comentários estão desativados neste site.',
      ['status' => 404]
    );
  }

  return $result;
}, 9999, 3);
