<?php

function phpmailer_send_mail($from, $to, $subject, $message, $debug = 0)
{
    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // Additional headers
    $headers .= 'To: ' . $to['name'] . ' <' . $to['email'] . '>' . "\r\n";
    $headers .= 'From: ' . $from['name'] . ' <' . $from['email'] . '>' . "\r\n";

    $mail = mail($to['email'], $subject, $message['html'], $headers);

    if ($mail) {
        return array('success' => true);
    } else {
        return array('success' => false, 'error' => __('Houve um erro desconhecido ao tentar enviar mensagem.', 'mercury'));
    }
}

function send_mail($post) {
    $user_input = array();

    foreach ($post as $key => $value) {
        $user_input[$key] = trim(strip_tags($value));
    }

    $required_fields = array('name', 'email');
    $errors = array();
    $feedback = array(
        'success' => false
    );

    if ($user_input['name'] == '') {
        array_push($errors, 'O campo "nome" deve ser preenchido');
    }

    if ($user_input['email'] == '') {
        array_push($errors, 'O campo "e-mail" deve ser preenchido');
    }

    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $user_input['email'])) {
        array_push($errors, 'O e-mail fornecido deve ser válido');
    }

    if (count($errors) == 0) {
        $feedback['success'] = true;
        $from = array(
            'email' => $user_input['email'],
            'name' => $user_input['name']
        );

        $to = array(
            'email' => get_bloginfo('admin_email'),
            'name' => 'B9'
        );

        $subject = 'Formulário de contato';

        $message_alt = "Formulário de contato: \n\n";
        $message_alt .= "Nome: " . $user_input['name'] . "\n";
        $message_alt .= "E-mail: " . $user_input['email'] . "\n";
        $message_alt .= "Mensagem: " . $user_input['message'] . "\n";

        $message_html = "<h3>Formulário de contato:</h3>";
        $message_html .= "<strong>Nome:</strong> " . $user_input['name'] . "<br>";
        $message_html .= "<strong>E-mail:</strong> " . $user_input['email'] . "<br>";
        $message_html .= "<strong>Mensagem:</strong> " . $user_input['message'] . "<br>";

        $msg = array(
            'alt' => $message_alt,
            'html' => $message_html
        );

        phpmailer_send_mail($from, $to, $subject, $msg);
    } else {
        $feedback['errors'] = $errors;
        $feedback['errors_html'] = '<ul><li>' . implode("</li><li>", $errors) . '</li></ul>';
    }

    return $feedback;
}
