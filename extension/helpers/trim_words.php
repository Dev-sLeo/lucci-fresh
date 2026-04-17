<?php

function lvt_trim_words($phrase, $max_words = 6) {
   $phrase_array = explode(' ',$phrase);
   if(count($phrase_array) > $max_words && $max_words > 0)
      $phrase = implode(' ',array_slice($phrase_array, 0, $max_words)).'...';
   return $phrase;
}
