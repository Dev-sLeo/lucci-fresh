<?php global $tpl_engine; ?>
</main>
<?php if (function_exists('luccifresh_is_new_checkout_page') && luccifresh_is_new_checkout_page()) : ?>
  <?php $tpl_engine->partial('template/footer/footer-checkout') ?>
<?php else : ?>
  <?php $tpl_engine->partial('template/footer/newsletter') ?>
  <?php $tpl_engine->partial('template/footer/footer') ?>
  <?php $tpl_engine->partial('template/header/mobile-menu') ?>
<?php endif; ?>
<?php wp_footer(); ?>
</body>

</html>