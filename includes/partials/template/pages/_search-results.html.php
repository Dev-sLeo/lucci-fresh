<?php
// includes/partials/template/pages/_search-results.html.php
global $tpl_engine;

$search_term = get_search_query();
$paged       = max(1, get_query_var('paged'));
$per_page    = 12;

$query = new WP_Query([
  'post_type'      => 'product',
  'post_status'    => 'publish',
  's'              => $search_term,
  'posts_per_page' => $per_page,
  'paged'          => $paged,
  'orderby'        => 'relevance',
]);

$total   = (int) $query->found_posts;
$max_pag = (int) $query->max_num_pages;
?>
<section class="s-search-results">
  <div class="s-container">

    <!-- Header: termo + contagem -->
    <div class="s-search-results__header">
      <div class="s-search-results__heading">
        <h1 class="s-search-results__title">
          <?php if ($search_term) : ?>
            <?= esc_html($search_term) ?>
          <?php else : ?>
            <?= esc_html__('Busca', 'lucci-fresh') ?>
          <?php endif; ?>
        </h1>
        <?php if ($total > 0) : ?>
          <span class="s-search-results__divider" aria-hidden="true"></span>
          <p class="s-search-results__count">
            <?= esc_html(sprintf(
              _n('%d opção', '%d opções', $total, 'lucci-fresh'),
              $total
            )) ?>
          </p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Grid de produtos -->
    <?php if ($query->have_posts()) : ?>
      <div class="s-search-results__grid">
        <?php while ($query->have_posts()) : $query->the_post();
          $product = wc_get_product(get_the_ID());
          if (! $product) continue;
          $tpl_engine->partial('components/product-card', ['product' => $product]);
        endwhile;
        wp_reset_postdata(); ?>
      </div>

      <!-- Paginação -->
      <?php if ($max_pag > 1) : ?>
        <div class="s-search-results__pagination">
          <?= paginate_links([
            'base'      => get_pagenum_link(1) . '%_%',
            'format'    => 'page/%#%/',
            'current'   => $paged,
            'total'     => $max_pag,
            'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'type'      => 'list',
          ]) ?>
        </div>
      <?php endif; ?>

    <?php else : ?>
      <div class="s-search-results__empty">
        <p class="s-search-results__empty-text">
          <?= esc_html__('Nenhum produto encontrado para', 'lucci-fresh') ?>
          <?php if ($search_term) : ?>
            "<strong><?= esc_html($search_term) ?></strong>".
          <?php endif; ?>
        </p>
      </div>
    <?php endif; ?>

  </div>
</section>