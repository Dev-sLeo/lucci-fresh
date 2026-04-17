<?php
function processarArquivo($urlArquivo)
{
  $extensao = pathinfo($urlArquivo, PATHINFO_EXTENSION);
  if (strtolower($extensao) === 'svg') {
    $conteudo = file_get_contents($urlArquivo);
    return $conteudo;
  } else {
    return $urlArquivo;
  }
}
