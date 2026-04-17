<?php

function lvt_trim_paragraphs($content_to_trim, $count = 2) {
    $trimmed_content = strip_tags($content_to_trim);
    $trimmed_content = explode("\n", $trimmed_content);

    $parsed_content = array();
    foreach ($trimmed_content as $paragraph) {
      $paragraph = trim($paragraph);
      if (empty($paragraph)) { continue; }

      array_push($parsed_content, $paragraph);

      if (count($parsed_content) == $count) { break; }
    }

    $trimmed_content = implode("\n\n", $parsed_content);
    $trimmed_content = wptexturize($trimmed_content);
    $trimmed_content = wpautop($trimmed_content);

    return $trimmed_content;
}
