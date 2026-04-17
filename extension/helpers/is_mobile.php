<?php

    function is_mobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    function is_tablet() {

      $detect = new Mobile_Detect_Class;

      if( $detect->isTablet() ){
        return true;
      } else {
        return false;
      }
    }
?>
