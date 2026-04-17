<?php global $tpl_engine; ?>
<form role="search" class="form-search" method="get" action="<?= esc_url(home_url('/blog')); ?>" data-aos="<?= wp_is_mobile() ? '' : 'fade-up' ?>">
  <div class="input-container">
    <input
      type="text"
      class="search-field"
      placeholder="<?php echo esc_attr_x('Pesquisar no blog', 'placeholder'); ?>"
      value="<?php echo get_search_query(); ?>"
      name="search" />
    <button type="submit">
      <?php $tpl_engine->svg('icon/icon-search'); ?>
    </button>
  </div>
</form>