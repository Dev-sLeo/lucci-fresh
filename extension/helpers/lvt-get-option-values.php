<?php

function lvt_get_option_values($group, $option = null) {
    // var_dump($group);
    // var_dump($option);
    $option_string = get_option('mb25_ot_data_values');
    $option_obj = json_decode(str_replace('\'', '"', str_replace('\\\\\'', '\"',$option_string )));

    if ($option) {
        return $option_obj->$group->$option;
    } else {
        return $option_obj->$group;
    }

}
