<?php
function send_contact_form()
{
	$response = array();

    $response = send_mail($_POST);

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	echo json_encode($response);

	die();
}
add_action('wp_ajax_nopriv_send_contact_form', 'send_contact_form');
add_action('wp_ajax_send_contact_form', 'send_contact_form');

?>