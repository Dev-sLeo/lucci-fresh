<?php

function mark_the_line_down($string) {
    $Parsedown = new Parsedown();
    $string = $Parsedown->text($string);
    $string = preg_replace('/<\/?p.*?>/', '', $string);
    return $string;
}

function simple_markdown($string) {
    $Parsedown = new Parsedown();
    $string = $Parsedown->text($string);
    return $string;
}
