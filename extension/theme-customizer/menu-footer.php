<?php
global $tpl_engine;

class Menu_Footer_Walker extends Walker_Nav_Menu {
    function start_el ( &$output, $item, $depth = 1, $args = array(), $id = 0 ) {
        $indent = str_repeat("\t", $depth);
        $item_has_child = array_search('menu-item-has-children', $item->classes) !== FALSE;
        $modal_class = '';
        if (($key = array_search('js-open-modal', $item->classes)) !== false) {
            $modal_class = $item->classes[$key];
            unset($item->classes[$key]);
        }

        if ($depth == 0) {
            $item_classes = implode(' ', $item->classes);
            if($item->url != '') {
                $output .= sprintf("\n<li class='%s c-footer-menu__main-item'><a class='%s c-footer-menu__main-link' onfocus='blur();' href='%s'>%s</a>",$item_classes, $modal_class, $item->url, $item->title);
            } else {
                $output .= sprintf("\n<li class='%s c-footer-menu__main-item'><span>%s</span>",$item_classes, $item->title);
            }

        } else if ($depth == 1) {
            $item_classes = implode(' ', $item->classes);
            $output .= sprintf("\n<li class='c-footer-menu__sub-item'><a class='%s sub-menu-item' onfocus='blur();' href='%s'>%s</a>",$item_classes, $item->url, $item->title);
        }
    }

    function end_el( &$output, $item, $depth = 1, $args = array(), $id = 0 ) {
        $output .= "</li>\n";
    }
}
