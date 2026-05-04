<?php
// includes/partials/template/pages/contact/_hero.html.php
global $tpl_engine;

$hero         = get_field('hero') ?? [];
$hero_eyebrow = $hero['eyebrow']     ?? __('Fale Conosco', 'lucci-fresh');
$hero_titulo  = $hero['title']       ?? __('Estamos aqui para te atender', 'lucci-fresh');
$hero_desc    = $hero['description'] ?? __('Fale com a gente pelo formulário abaixo ou pelos nossos canais de atendimento.', 'lucci-fresh');
?>
<section class="s-ct-hero">

  <div class="s-ct-hero__pattern" aria-hidden="true">
    <?php $tpl_engine->svg('pattern/pattern-hero') ?>
  </div>

  <div class="s-container">
    <div class="s-ct-hero__content">

      <p class="s-ct-hero__eyebrow"><?= esc_html($hero_eyebrow) ?></p>
      <h1 class="s-ct-hero__title"><?= esc_html($hero_titulo) ?></h1>

      <?php if ($hero_desc) : ?>
        <p class="s-ct-hero__description"><?= esc_html($hero_desc) ?></p>
      <?php endif; ?>

    </div>
  </div>

</section><!-- /.s-ct-hero -->