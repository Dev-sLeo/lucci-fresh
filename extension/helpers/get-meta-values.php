<?php

function lvt_get_meta_values($id = null) {
    global $post;
    $id = $id ? $id : $post->ID;
    $meta = get_post_meta($id);
    $p_meta = array();

    $rpl_ptn = '/_cf_.*?_(.*)/';
    $rpl = '$1';

    foreach ($meta as $key => $value) {
        $key = preg_replace($rpl_ptn, $rpl, $key);
        $p_meta[$key] = $value[0];
    }

    return $p_meta;
}
