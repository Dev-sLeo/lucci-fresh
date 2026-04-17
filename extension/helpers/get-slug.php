<?php
function gerar_slug($string)
{
  $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
  $slug = strtolower($slug);
  $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
  $slug = trim($slug, '-');
  return $slug;
}
