<?php
global $tpl_engine;

$footer  = get_field('footer',  'tema');
$footer    = is_array($footer)  ? $footer  : [];

// Footer fields
$logo       = $footer['logo']  ?? null;
$contact       = $footer['contato']  ?? null;
$copy_text  = $footer['copy']  ?? '';
$upsites    = $footer['upsites']  ?? '';
$privacy_policy = $footer['politica_de_privacidade']  ?? '';
?>

<footer class="c-footer" aria-label="Rodapé">
  <div class="u-line-background">
    <?php $tpl_engine->svg('line-background') ?>
  </div>
  <div class="c-footer__top">
    <div class="s-container">
      <div class="c-footer__top-inner">
        <?php if (!empty($logo['ID'])) : ?>
          <div class="c-footer__logo">
            <a href="<?= site_url() ?>">
              <?= render_media_image((int) $logo['ID'], 'full', ['loading' => 'lazy', 'decoding' => 'async']); ?>
            </a>
          </div>
        <?php endif; ?>
        <div class="c-footer__contact">
          <?php if (!empty($contact['telefone'])) : ?>
            <a href="tel:<?= preg_replace('/\D+/', '', $contact['telefone']); ?>" class="c-footer__contact-item"><?php $tpl_engine->svg('icons/contact/telefone') ?><?= esc_html($contact['telefone']); ?></a>
          <?php endif; ?>
          <?php if (!empty($contact['whatsapp'])) : ?>
            <a href="https://wa.me/<?= preg_replace('/\D+/', '', $contact['whatsapp']); ?>" class="c-footer__contact-item"><?php $tpl_engine->svg('icons/contact/whatsapp') ?><?= esc_html($contact['whatsapp']); ?></a>
          <?php endif; ?>
          <?php if (!empty($contact['e-mail'])) : ?>
            <a href="mailto:<?= sanitize_email($contact['e-mail']); ?>" class="c-footer__contact-item"><?php $tpl_engine->svg('icons/contact/email') ?><?= esc_html($contact['e-mail']); ?></a>
          <?php endif; ?>
        </div>
        <?php $tpl_engine->partial('template/footer/redes-sociais'); ?>
      </div><!-- /.c-footer__bottom-inner -->
    </div><!-- /.s-container -->
  </div>

  <!-- ── LOGO + NAV ──────────────────────────────────────────── -->
  <div class="c-footer__middle">
    <div class="s-container">
      <div class="c-footer__middle-inner">
        <?php if (has_nav_menu('footer')) : ?>
          <nav class="c-footer__nav" aria-label="Menu rodapé">
            <?php wp_nav_menu([
              'theme_location' => 'footer',
              'container'      => false,
              'menu_class'     => 'c-footer__menu',
              'fallback_cb'    => false,
              'depth'          => 1,
            ]); ?>
          </nav>
        <?php endif; ?>

      </div><!-- /.c-footer__bottom-inner -->
    </div><!-- /.s-container -->
  </div>

  <div class="c-footer__bottom">
    <div class="s-container">
      <div class="c-footer__bottom-inner">
        <div class="c-footer__images">
          <?php foreach ($footer['imagens'] as $image): ?>
            <?= render_media_image($image['id'], 'full', ['loading' => 'lazy', 'decoding' => 'async']); ?>
          <?php endforeach; ?>
        </div>
        <div class="c-footer__adress">
          <a href="<?= esc_url($footer['endereco']['link']); ?>" class="c-footer__contact-item" target="_blank">
            <?php $tpl_engine->svg('icons/contact/adress') ?>
            <?= esc_html($footer['endereco']['texto']); ?>
          </a>
        </div>
      </div><!-- /.c-footer__bottom-inner -->
    </div><!-- /.s-container -->
  </div>

  <!-- ── COPY BAR ────────────────────────────────────────────── -->
  <?php if ($copy_text) : ?>
    <div class="c-footer__copy-bar">
      <div class="s-container">
        <div class="c-footer__copy-bar-container">
          <p class="c-footer__copy"><?= esc_html($copy_text); ?> / Criação de Sites por <a href="<?= esc_url($upsites); ?>" target="_blank" rel="noopener noreferrer">Upsites</a></p>
          <a href="<?= esc_url($privacy_policy['url']); ?>" class="c-footer__privacy-policy">Politica de privacidade</a>
        </div>
      </div>
    </div>
  <?php endif; ?>

</footer>