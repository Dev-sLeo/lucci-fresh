<?php
define('AJAXFILES', THEMELIB . '/ajax');

foreach (glob(AJAXFILES . '/*.php') as $filename) {
    require_once($filename);
}
