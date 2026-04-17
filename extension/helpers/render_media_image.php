<?php
function render_media_image($imagem, $size = 'full', $attrs = [])
{
  // Normaliza inputs
  if (is_numeric($imagem)) {
    $id      = intval($imagem);
    $url     = wp_get_attachment_image_url($id, $size);
    $mime    = get_post_mime_type($id);
    $alt     = get_post_meta($id, '_wp_attachment_image_alt', true);
  } elseif (is_array($imagem)) {
    $id      = $imagem['id'] ?? null;
    $url     = $imagem['url'] ?? ($id ? wp_get_attachment_image_url($id, $size) : null);
    $mime    = $id ? get_post_mime_type($id) : null;
    $alt     = $imagem['alt'] ?? ($id ? get_post_meta($id, '_wp_attachment_image_alt', true) : '');
  } elseif (is_string($imagem)) {
    $id      = attachment_url_to_postid($imagem);
    $url     = $imagem;
    $mime    = $id ? get_post_mime_type($id) : null;
    $alt     = $id ? get_post_meta($id, '_wp_attachment_image_alt', true) : '';
  } else {
    return '';
  }

  if (!$url) return '';

  $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
  $is_svg = ($mime === 'image/svg+xml' || $ext === 'svg');

  // Monta atributos extras (para SVG inline também)
  $attr_html = '';
  foreach ($attrs as $key => $value) {
    $attr_html .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
  }

  /**
   * SVG → inline
   */
  if ($is_svg) {
    $svg = @file_get_contents($url);

    if (!$svg) {
      return '<img src="' . esc_url($url) . '" alt="' . esc_attr($alt) . '"' . $attr_html . ' loading="lazy">';
    }

    // Adiciona alt como title se não existir
    if ($alt && !preg_match('/<title>/', $svg)) {
      $svg = preg_replace(
        '/<svg([^>]*)>/',
        '<svg$1><title>' . esc_html($alt) . '</title>',
        $svg,
        1
      );
    }

    // Insere atributos do usuário no <svg>
    return preg_replace(
      '/<svg/',
      '<svg' . $attr_html,
      $svg,
      1
    );
  }

  /**
   * Raster → usa wp_get_attachment_image com lazy loading
   */
  $img_attrs = array_merge([
    'alt'     => $alt,
    'loading' => 'lazy',
  ], $attrs);

  return wp_get_attachment_image($id, $size, false, $img_attrs);
}

function render_hero_background_image($image, array $attrs = []): string
{
  $image_source = $image;

  // Permite receber a estrutura completa do ACF ou apenas o ID
  if (is_array($image) && array_key_exists('ID', $image)) {
    $image_source = $image['ID'];
  }

  if (empty($image_source)) {
    return '';
  }

  return render_media_image($image_source, 'full', get_hero_media_attributes($attrs));
}
