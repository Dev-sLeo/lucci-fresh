<?php
global $tpl_engine;

$footer      = get_field('footer', 'tema');
$footer      = is_array($footer) ? $footer : [];
$redes       = $footer['rede_social'] ?? [];

if (!empty($redes)) : ?>
  <ul class="c-footer__redes-sociais">
    <?php foreach ($redes as $item) :
      $rede = $item['rede'] ?? '';
      $link = $item['link'] ?? '';

      if (empty($rede)) continue;
    ?>
      <li class="c-footer__rede-social-item">
        <a href="<?= esc_url($link); ?>"
          class="c-footer__rede-social-link"
          target="_blank"
          rel="noopener noreferrer"
          aria-label="<?= esc_attr(ucfirst($rede)); ?>">
          <?php $tpl_engine->svg('redes-sociais/' . $rede); ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>