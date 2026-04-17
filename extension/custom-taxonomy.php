<?php
//Define global
define('CTPATH', THEMELIB . '/taxonomies');
foreach (glob(CTPATH . '/**/*.php') as $filename) {
    require_once($filename);
}

