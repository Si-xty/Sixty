<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MailController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('mailjet_mailer');
        $this->load->model('Authentication');
        $this->Authentication->checkAdmin();
    }

    // Al acceder a /mailjet, envía un correo al usuario autenticado y responde por JSON
    public function index() {
        $user = $this->session->userdata('auth_user');
        if (!$user || empty($user['email'])) {
            $response = [
                'success' => false,
                'message' => 'No se encontró el correo del usuario autenticado.'
            ];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $to_email = $user['email'];
        // $to_email = 'kristianhernandez.2003@gmail.com';
        $to_name = $user['user'];
        $subject = '¡Bienvenido a Sixty!';

        // Leer el template de bienvenida
        $template_path = APPPATH . 'views/emails/welcome_email.html';
        $html_body = file_exists($template_path) ? file_get_contents($template_path) : '';
        $html_body = str_replace('{{name}}', htmlspecialchars($to_name), $html_body);

        $text_body = 'Hola ' . $to_name . ', gracias por registrarte en Sixty. Estamos felices de tenerte en la comunidad.';

        $result = $this->mailjet_mailer->send_email($to_email, $to_name, $subject, $html_body, $text_body);

        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Correo de bienvenida enviado correctamente a ' . $to_email
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'No se pudo enviar el correo de bienvenida.'
            ];
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }


    public function send_email()
    {
        if ($this->input->is_ajax_request()) {
            $to_email = $this->input->post('to_email');
            $to_name = $this->input->post('to_name');
            $subject = $this->input->post('subject');
            $html_body = $this->input->post('html_body');
            $text_body = $this->input->post('text_body');

            if (empty($to_email) || empty($subject) || empty($html_body)) {
                $response = [
                    'success' => false,
                    'message' => 'Faltan datos obligatorios para enviar el correo.'
                ];
            } else {
                $result = $this->mailjet_mailer->send_email($to_email, $to_name, $subject, $html_body, $text_body);

                if ($result) {
                    $response = [
                        'success' => true,
                        'message' => 'Correo enviado correctamente.'
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'No se pudo enviar el correo.'
                    ];
                }
            }
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
}