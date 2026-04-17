<?php
define('QFPATH', THEMELIB . '/query-filters');
foreach (glob(QFPATH . '/*.php') as $filename) {
    require_once($filename);
}
