<?php
$is_search = (array_key_exists('s', $_GET) && $_GET['s'] != '') ? $_GET['s'] : false;

$var_name = (isset($custom_var_name)) ? $custom_var_name : 'pagina';

// normalize page
$page = isset($page) ? (int) $page : (array_key_exists($var_name, $_GET) ? (int) $_GET[$var_name] : 1);
if ($page < 1) {
  $page = 1;
}

// allow override of posts per page via $custom_posts_per_page
$posts_per_page = isset($custom_posts_per_page) ? (int) $custom_posts_per_page : null;

// if a query wasn't provided, build one using the posts_per_page (fallback 15)
if (!isset($query)) {
  $ppp = $posts_per_page ? $posts_per_page : 15;
  $args = array(
    'post_type' => 'post',
    'posts_per_page' => $ppp,
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_status' => 'publish',
    'paged' => $page,
  );

  if ($is_search) :
    $args['s'] = $is_search;
  endif;

  $wp_query = new WP_Query($args);
  $query = $wp_query;
} else {
  // use posts_per_page from query if not overridden
  if (! $posts_per_page) {
    $posts_per_page = isset($query->query_vars['posts_per_page']) ? (int) $query->query_vars['posts_per_page'] : 15;
  }
}

if (!isset($base_url)) {
  $base_url = $_SERVER['REQUEST_URI'];
  $arr_base_url = explode("?", $base_url, 2);
  $base_url = $arr_base_url[0];
}

if (!isset($anchor)) {
  $anchor = false;
}

$var_name = (isset($custom_var_name)) ? $custom_var_name : 'pagina';

$max = (int) $query->max_num_pages;

if ($page == 0) {
  $page = 1;
}
$posts_per_page = $posts_per_page ?: (isset($query->query_vars['posts_per_page']) ? (int)$query->query_vars['posts_per_page'] : 15);
$pages_per_side = 3;
$pages_to_show = ($pages_per_side * 2) + 1;

$current_query_vars = array();
foreach ($_GET as $key => $value) {
  if ($key == $var_name) {
    continue;
  }
  if ($key == 'q') {
    continue;
  }
  if (is_array($value)) {
    foreach ($value as $val) {
      array_push($current_query_vars, $key . '[]=' . $val);
    }
  } else {
    array_push($current_query_vars, $key . '=' . $value);
  }
}

array_push($current_query_vars, $var_name . '=');

$current_query_vars = implode('&', $current_query_vars);

$base_url = $base_url . '?' . $current_query_vars;
$init = ($page - $pages_per_side);
if ($init < 1) {
  $init = 1;
}

$end = $init + $pages_to_show;
if ($end > $max) {
  $end = $max + 1;
}
$next_page = $page + 1;
$previous_page = $page - 1;

?>

<?php if ($max > 1): ?>
  <div class="o-paginate o-paginate--center" role="navigation">

    <div class="o-paginate__content">

      <?php
      if (wp_is_mobile()) {
        $start = max(1, $page - 1);
        $end = min($max, $page + 1);
      } else {
        $start = max(1, $page - 2);
        $end = min($max, $page + 2);
      }


      if ($start > 3) {
        $start = $page - 2;
      }

      if ($page > 1):
        $prev_number = str_pad($previous_page, 2, '0', STR_PAD_LEFT);
      ?>
        <a href="<?php echo $base_url . $previous_page . ($anchor ? $anchor : ''); ?>"
          class="c-paginate__item c-paginate__item--prev"
          title="Página anterior" role="link">
          <svg width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.58327 17.9166C9.58327 17.9166 1.25002 11.7793 1.25 9.5833C1.24998 7.3873 9.58333 1.25 9.58333 1.25" stroke="#50B4A4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>

        </a>
      <?php
      endif;

      if ($start > 1): ?>
        <?php $first_number = str_pad(1, 2, '0', STR_PAD_LEFT); ?>
        <a href="<?php echo $base_url; ?>1<?php echo ($anchor) ? $anchor : ''; ?>"
          class="c-paginate__item c-paginate__item-number" title="Ir para página <?php echo $first_number; ?>" role="link"><?php echo $first_number; ?></a>
        <?php if ($start > 2): ?>
          <span class="c-paginate__item c-paginate__item--ellipsis">...</span>
        <?php endif; ?>
      <?php endif; ?>

      <?php for ($i = $start; $i <= $end; $i++): ?>
        <?php $page_number = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
        <?php if ($i == $page): ?>
          <a class="c-paginate__item c-paginate__item-number c-paginate__item--active"
            title="Página atual - <?php echo $page_number; ?>" role="link">
            <?php echo $page_number; ?>
          </a>
        <?php else: ?>
          <a href="<?php echo $base_url; ?><?php echo $i; ?><?php echo ($anchor) ? $anchor : ''; ?>"
            class="c-paginate__item c-paginate__item-number" title="Ir para página <?php echo $page_number; ?>" role="link">
            <?php echo $page_number; ?>
          </a>
        <?php endif; ?>
      <?php endfor; ?>

      <?php if ($end < $max - 1): ?>
        <span class="c-paginate__item c-paginate__item--ellipsis">...</span>
      <?php endif; ?>

      <?php if ($end < $max): ?>
        <?php $max_number = str_pad($max, 2, '0', STR_PAD_LEFT); ?>
        <a href="<?php echo $base_url; ?><?php echo $max; ?><?php echo ($anchor) ? $anchor : ''; ?>"
          class="c-paginate__item c-paginate__item-number" title="Ir para página <?php echo $max_number; ?>" role="link">
          <?php echo $max_number; ?>
        </a>
      <?php endif; ?>

      <?php
      // Botão próximo
      if ($page < $max):
        $next_number = str_pad($next_page, 2, '0', STR_PAD_LEFT);
      ?>
        <a href="<?php echo $base_url . $next_page . ($anchor ? $anchor : ''); ?>"
          class="c-paginate__item c-paginate__item--next"
          title="Próxima página" role="link">
          <svg width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1.25023 17.9166C1.25023 17.9166 9.58348 11.7793 9.5835 9.5833C9.58351 7.3873 1.25016 1.25 1.25016 1.25" stroke="#50B4A4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </a>
      <?php endif; ?>

    </div>
  </div>
<?php endif; ?>