<?php

function url_current_params($url) {
    $current_params = explode('?', $url);

    if (count($current_params) == 1) {
        $current_params = array();
    } else {
        $broken_current_params = explode('&', $current_params[1]);
        $current_params = array();

        foreach ($broken_current_params as $param) {
            $param = explode('=', $param);
            $current_params[$param[0]] = (count($param) > 1) ? $param[1] : '';

        }
    }

    return $current_params;
}

function params_to_url($params) {
    $keys = array_keys($params);
    $values = array_values($params);

    $params_arr = array_map(function($key, $value) {
        return $key . '=' . urlencode($value);
    }, $keys, $values);

    $params_arr = implode('&', $params_arr);

    return $params_arr;
}

function append_params_to_url($url, $params) {
    $current_params = url_current_params($url);

    $params = array_merge($current_params, $params);
    $params = params_to_url($params);
    $base_url = explode('?', $url);
    $base_url = $base_url[0];

    return $base_url . '?' . $params;
}

function append_params_to_current_url($params) {
    $current_url = $_SERVER['REQUEST_URI'];
    return append_params_to_url($current_url, $params);
}
