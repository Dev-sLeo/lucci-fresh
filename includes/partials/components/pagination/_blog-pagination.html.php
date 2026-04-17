<?php global $tpl_engine; ?>
<nav class="c-pagination" id="js-blog-pagination" aria-label="Paginação" style="display:none">
  <button class="c-pagination__arrow c-pagination__arrow--prev" id="js-pagination-prev" type="button" aria-label="Página anterior" disabled>
    <?php $tpl_engine->svg('icons/arrow-pagination-left') ?>
  </button>

  <ul class="c-pagination__pages" id="js-pagination-pages"></ul>

  <button class="c-pagination__arrow c-pagination__arrow--next" id="js-pagination-next" type="button" aria-label="Próxima página">
    <?php $tpl_engine->svg('icons/arrow-pagination-right') ?>
  </button>
</nav>