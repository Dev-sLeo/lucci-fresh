<?php

function lvt_save_meta_fields($prefix, $fields, $la = false) {
    global $post;
    $early_return = false;

    foreach ($fields as $field => $params) {
        if (!isset($_REQUEST[$prefix . $field])) {
            $early_return = true;
            break;
        }
    }

    if ($early_return) { return; }

    foreach ($fields as $field => $params) {
        $field_name = $prefix . $field;
        update_post_meta($post->ID, $field_name, $_REQUEST[$field_name]);
    }
}
