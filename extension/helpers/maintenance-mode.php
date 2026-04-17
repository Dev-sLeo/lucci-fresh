<?php

/***************************************************************************
* @Author: Boutros AbiChedid
* @Date:   November 14, 2011
* @Websites: http://bacsoftwareconsulting.com/ ; http://blueoliveonline.com/
* @Description: Function that puts WordPress Website in maintenance mode.
* @Tested on: WordPress version 3.2.1 (but it works on earlier versions.)
****************************************************************************/
// function activate_maintenance_mode() {
//     //If the current user is NOT an 'Administrator' or NOT 'Super Admin' then display Maintenance Page.
//     if (!(current_user_can( 'read' )) && ENV != 'development') {
//         if (get_option('_qdi_global_public') !== '1') {
//             //Kill WordPress execution and display HTML maintenance message.
//             header('Content-type: text/html; charset=utf-8');
//             echo file_get_contents(PATHS_INC . '/self-contained/maintenance.html');
//             exit;
//         }
//     }
// }
// add_action('get_header', 'activate_maintenance_mode');
