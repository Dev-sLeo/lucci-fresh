<?php

define('POST_TYPES', THEMELIB . '/post-types');
foreach (glob(POST_TYPES . '/*.php') as $filename) {
    require_once($filename);
}
