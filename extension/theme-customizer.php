<?php
//Define global

define('TCUSTOM_PATH', THEMELIB . '/theme-customizer');

foreach (glob(TCUSTOM_PATH . '/*.php') as $filename) {
    require_once($filename);
}
