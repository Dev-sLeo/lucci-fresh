<?php
//Define global

define('CFPATH', THEMELIB . '/custom-fields');
foreach (glob(CFPATH . '/**/index.php') as $filename) {
    require_once($filename);
}

foreach (glob(CFPATH . '/**/**/index.php') as $filename) {
    require_once($filename);
}
