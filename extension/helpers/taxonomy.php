<?php

function get_tax_meta($term_id) {
    $option1 = get_option('taxonomy_cf');
    $option2 = get_option('levante_basic_apis_term_cf_');



    $option1[$term_id] = isset($option1[$term_id]) ? $option1[$term_id] : array();
    $option2[$term_id] = isset($option2[$term_id]) ? $option2[$term_id] : array();
    $option = array_merge($option1[$term_id], $option2[$term_id]);

    return $option;
}

function the_first_category_slug($postid = null) {
   echo get_first_category_slug($postid);
}

function get_first_category_slug($postid = null) {
    if ($postid == null) {
        $categories = (array)get_the_category();
    } else {
        $categories = (array)get_the_category($postid);
    }
   return $categories[0]->category_nicename;
}
