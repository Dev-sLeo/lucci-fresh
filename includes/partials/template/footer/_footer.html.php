<?php
global $tpl_engine;

$footer  = get_field('footer', 'tema');
$footer  = is_array($footer) ? $footer : [];

$logo            = $footer['logo']                    ?? null;
$endereco        = $footer['endereco']                ?? [];
$addr_text       = $endereco['text']                  ?? '';
$addr_link       = $endereco['link']                  ?? '';
$redes_sociais   = $footer['redes_sociais']           ?? [];
$privacy_policy  = $footer['politica_de_privacidade'] ?? [];
$term_use  = $footer['termos_de_uso'] ?? [];
$upsites         = $footer['upsites']                 ?? '';
$copy_text       = $footer['copy']                    ?? '';

$footer_nav = wp_nav_menu([
  'theme_location' => 'footer',
  'container'      => '',
  'menu_class'     => 'o-footer__nav-list',
  'fallback_cb'    => false,
  'depth'          => 1,
  'echo'           => false,
]);
?>

<footer class="o-footer" aria-label="<?= esc_attr__('Rodapé', 'lucci-fresh') ?>">

  <!-- ── MAIN AREA: brand | nav | social ──────────────────────── -->
  <div class="o-footer__main">
    <div class="s-container">
      <div class="o-footer__inner">

        <!-- Brand: logo + address -->
        <div class="o-footer__brand">
          <?php if (!empty($logo['url'])) : ?>
            <a href="<?= esc_url(home_url('/')) ?>" class="o-footer__logo" aria-label="<?= esc_attr__('Lucci Fresh – Página inicial', 'lucci-fresh') ?>">
              <img src="<?= esc_url($logo['url']) ?>" alt="<?= esc_attr($logo['alt'] ?? 'Lucci Fresh') ?>" width="<?= esc_attr($logo['width'] ?? '') ?>" height="<?= esc_attr($logo['height'] ?? '') ?>" loading="lazy" decoding="async">
            </a>
          <?php elseif (has_custom_logo()) : ?>
            <a href="<?= esc_url(home_url('/')) ?>" class="o-footer__logo">
              <?php the_custom_logo(); ?>
            </a>
          <?php else : ?>
            <a href="<?= esc_url(home_url('/')) ?>" class="o-footer__logo o-footer__logo--text">Lucci Fresh</a>
          <?php endif; ?>

          <?php if ($addr_text) : ?>
            <address class="o-footer__address">
              <?php if ($addr_link) : ?>
                <a href="<?= esc_url($addr_link) ?>" target="_blank" rel="noopener noreferrer"><?= esc_html($addr_text) ?></a>
              <?php else : ?>
                <?= esc_html($addr_text) ?>
              <?php endif; ?>
            </address>
          <?php endif; ?>
        </div><!-- /.o-footer__brand -->

        <!-- Nav: 3-column grid via CSS -->
        <?php if ($footer_nav) : ?>
          <nav class="o-footer__nav" aria-label="<?= esc_attr__('Menu rodapé', 'lucci-fresh') ?>">
            <?= $footer_nav ?>
          </nav>
        <?php endif; ?>

        <!-- Social icons -->
        <?php
        if (empty($redes_sociais)) {
          $redes_sociais = [
            ['rede' => 'instagram', 'link' => '#'],
            ['rede' => 'facebook',  'link' => '#'],
            ['rede' => 'whatsapp',  'link' => '#'],
          ];
        }
        ?>
        <div class="o-footer__social">
          <?php foreach ($redes_sociais as $item) :
            $rede = $item['rede'] ?? '';
            $link = $item['link'] ?? '';
            if (!$rede) continue;
          ?>
            <a href="<?= esc_url($link ?: '#') ?>" class="o-footer__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?= esc_attr(ucfirst($rede)) ?>">
              <?php $tpl_engine->svg('redes-sociais/' . $rede) ?>
            </a>
          <?php endforeach; ?>
        </div><!-- /.o-footer__social -->

      </div><!-- /.o-footer__inner -->
    </div><!-- /.s-container -->
  </div><!-- /.o-footer__main -->

  <!-- ── DIVIDER ─────────────────────────────────────────────── -->
  <div class="o-footer__divider">
    <div class="s-container">
      <hr>
    </div>
  </div>

  <!-- ── COPY BAR ────────────────────────────────────────────── -->
  <div class="o-footer__copy-bar">
    <div class="s-container">
      <div class="o-footer__copy-inner">
        <p class="o-footer__copy">
          <?php
          $copy_out = $copy_text ?: __('Lucci Fresh – todos os direitos reservados', 'lucci-fresh');
          echo esc_html($copy_out);
          if ($upsites) : ?>
            &nbsp;/&nbsp;<?= esc_html__('Criação de Sites por', 'lucci-fresh') ?> <a href="<?= esc_url($upsites) ?>" target="_blank" rel="noopener noreferrer">Upsites</a>
          <?php endif; ?>
        </p>
        <div class="o-footer__copy-inner-right">
          <?php if (!empty($term_use['url'])) : ?>
            <a href="<?= esc_url($term_use['url']) ?>" class="o-footer__privacy"
              <?= !empty($term_use['target']) ? 'target="' . esc_attr($term_use['target']) . '" rel="noopener noreferrer"' : '' ?>>
              <?= esc_html($term_use['title'] ?: __('Termos de uso', 'lucci-fresh')) ?>
            </a>
          <?php endif; ?>
          <?php if (!empty($privacy_policy['url'])) : ?>
            <a href="<?= esc_url($privacy_policy['url']) ?>" class="o-footer__privacy"
              <?= !empty($privacy_policy['target']) ? 'target="' . esc_attr($privacy_policy['target']) . '" rel="noopener noreferrer"' : '' ?>>
              <?= esc_html($privacy_policy['title'] ?: __('Política de privacidade', 'lucci-fresh')) ?>
            </a>
          <?php endif; ?>
        </div>
      </div><!-- /.o-footer__copy-inner -->
    </div><!-- /.s-container -->
  </div><!-- /.o-footer__copy-bar -->

</footer><!-- /.o-footer -->