<?php

function query_filter_search_results($query) {
    if (!$query->is_search()) { return; }

    $query->set('orderby', 'date');

    if (b9_is_ajax()) {
        $query->set('posts_per_page', 6);
    }
}

// add_action('pre_get_posts', 'query_filter_search_results');
