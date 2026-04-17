<?php

function sa_sanitize_spanish_chars ($filename) {
    $filename = preg_replace('/[ÍÌÏÎíìîï]/i', 'i', $filename);
    $filename = preg_replace('/[àáãâäÀÁÃÂÄ]/i', 'a', $filename);
    $filename = preg_replace('/[ÉÈÊËéèêë]/i', 'e', $filename);
    $filename = preg_replace('/[úùûüÚÙÛÜ]/i', 'u', $filename);
    $filename = preg_replace('/[ÓÒÖÔÕõôöóò]/i', 'o', $filename);
    $filename = preg_replace('/[ñÑ]/i', 'n', $filename);
    $filename = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $filename);
    $filename = preg_replace('/aa/i', 'a', $filename);
    $filename = preg_replace('/ea/i', 'e', $filename);
    $filename = preg_replace('/ua/i', 'u', $filename);
    $filename = preg_replace('/ia/i', 'i', $filename);
    $filename = preg_replace('/oa/i', 'o', $filename);
    return $filename;
}

add_filter('sanitize_file_name', 'sa_sanitize_spanish_chars', 10);
