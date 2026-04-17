<?php

/**
 * Estrelas estilo Google com preenchimento parcial usando o SVG fornecido.
 */
if (!function_exists('render_stars')) {
  function render_stars($rating = 0, $args = [])
  {
    $rating = floatval($rating);
    if ($rating < 0) $rating = 0;
    if ($rating > 5) $rating = 5;

    $size  = isset($args['size']) ? intval($args['size']) : 26;
    $gap   = isset($args['gap']) ? intval($args['gap']) : 4;
    $bg    = $args['bg']   ?? '#E6E6E6';  // estrela vazia
    $fill  = $args['fill'] ?? '#E6B100';  // dourado (igual seu SVG)
    $class = $args['class'] ?? 'c-rating';

    $path = 'M12.6329 0L15.6151 9.17834H25.2658L17.4582 14.8509L20.4404 24.0292L12.6329 18.3567L4.82531 24.0292L7.80753 14.8509L-2.95639e-05 9.17834H9.65065L12.6329 0Z';

    $uid = 'star_' . substr(md5(uniqid('', true)), 0, 10);

    ob_start(); ?>
    <div class="<?= esc_attr($class); ?>"
      aria-label="<?= esc_attr(sprintf('Avaliação %.1f de 5', $rating)); ?>"
      style="display:flex;align-items:center;gap:<?= esc_attr($gap); ?>px;">
      <?php for ($i = 1; $i <= 5; $i++) :
        $p = ($rating - ($i - 1));
        if ($p < 0) $p = 0;
        if ($p > 1) $p = 1;
        $percent = $p * 100;

        $clipId = $uid . '_clip_' . $i;
        $w = ($percent / 100) * 26; // viewBox width
      ?>
        <svg width="<?= esc_attr($size); ?>" height="<?= esc_attr($size); ?>"
          viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg"
          role="img" aria-hidden="true" style="display:block;flex:0 0 auto;">
          <defs>
            <clipPath id="<?= esc_attr($clipId); ?>">
              <path d="<?= esc_attr($path); ?>" />
            </clipPath>
          </defs>

          <!-- Base (vazia) -->
          <path d="<?= esc_attr($path); ?>" fill="<?= esc_attr($bg); ?>" />

          <!-- Preenchimento parcial (estilo Google) -->
          <g clip-path="url(#<?= esc_attr($clipId); ?>)">
            <rect x="0" y="0" width="<?= esc_attr($w); ?>" height="25" fill="<?= esc_attr($fill); ?>" />
          </g>
        </svg>
      <?php endfor; ?>
    </div>
<?php
    return ob_get_clean();
  }
}
