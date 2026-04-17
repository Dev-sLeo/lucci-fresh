<?php
// constants
define('HELPERS', THEMELIB . '/helpers');

foreach (glob(HELPERS . '/*.php') as $filename) {
    require_once($filename);
}
