<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Client; // Ya que usas Google_Client()
use Google\Service\Oauth2; // Para Google_Service_Oauth2()

/**
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property UserModel $UserModel
 * @property config $config
 */


class GoogleController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        log_message('debug', 'DEBUG: GoogleController constructor - Antes de cargar Sendgrid_mailer.');
        $this->load->library('sendgrid_mailer');
        $this->load->library('mailjet_mailer');
        log_message('debug', 'DEBUG: GoogleController constructor - Después de cargar Sendgrid_mailer.');
        if (isset($this->Sendgrid_mailer)) {
            log_message('debug', 'DEBUG: GoogleController constructor - $this->Sendgrid_mailer EXISTE después de la carga.');
        } else {
            log_message('error', 'DEBUG: GoogleController constructor - $this->Sendgrid_mailer NO EXISTE después de la carga.');
        }
        $this->load->library('session');
        $this->load->config('google');
        $this->load->model('UserModel');

        if($this->session->has_userdata('authenticated'))
        {   
            // echo json_encode(['status' => 'success', 'message' => 'Registrado correctamente']);
            $this->session->set_flashdata('status', 'Ya estas logeado');
            redirect(base_url('welcome'));
        }

    }
    
    public function index()
    {
        $client = new Client();

        $client->setClientId($this->config->item('google_client_id'));
        $client->setClientSecret($this->config->item('google_client_secret'));
        $client->setRedirectUri($this->config->item('google_redirect_uri'));
        $client->addScope("email");
        $client->addScope("profile");

        if (isset($_GET['code'])) {
            try {
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                $this->session->set_userdata('google_access_token', $token);

                $client->setAccessToken($token);

                $google_service = new Oauth2($client);
                $user_info = $google_service->userinfo->get();

                $google_id = $user_info->id;
                $email = $user_info->email;
                $first_name = $user_info->givenName ?? '';
                $last_name = $user_info->familyName ?? '';
                $full_name = $user_info->name ?? '';
                $picture = $user_info->picture ?? null;

                $user = $this->UserModel->getUserByGoogleId($google_id);

                if (!$user) {
                    $user = $this->UserModel->getUserByEmail($email);
                    if (!$user) {
                        $user_data_to_save = [
                            'google_id' => $google_id,
                            'email' => $email,
                            'user' => $full_name,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'full_name' => $full_name,
                            'picture' => $picture,
                            'rol' => 'usuario',
                            'password' => null
                        ];
                        $new_user_id = $this->UserModel->registerGoogleUser($user_data_to_save);
                        $user = $this->UserModel->getUserById($new_user_id);

                        if ($user && !empty($user->email)) { 

                            $subject = '¡Bienvenido a Sixty!';
                            $html_body = '
                                <html>
                                <head>
                                    <title>Bienvenido a Sixty</title>
                                </head>
                                <body>
                                    <h1>¡Hola, ' . htmlspecialchars($user->full_name) . '!</h1>
                                    <p>Gracias por registrarte en Sixty.</p>
                                    <p>Estamos emocionados de tenerte a bordo. ¡Explora todas nuestras funciones!</p>
                                    <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
                                    <p>Saludos cordiales,<br>El equipo de Sixty</p>
                                </body>
                                </html>
                            ';
                            $text_body = strip_tags($html_body);
                            $text_body = html_entity_decode($text_body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $text_body = preg_replace('/\s+/', ' ', $text_body);
                            $text_body = trim($text_body);
                            $text_body = "¡Hola, " . $user->full_name . "! Gracias por registrarte en Sixty.cl. ¡Explora nuestras funciones!";


                            // if ($this->mailjet_mailer->send_email($user->email, $user->full_name, $subject, $html_body, $text_body)) {
                            //     log_message('info', 'Correo de bienvenida (API) enviado a: ' . $user->email);
                            // } else {
                            //     log_message('error', 'Fallo el envio de correo de bienvenida (API) a: ' . $user->email);
                            // }            
                        }
                    } else {
                        $update_data = [
                            'google_id' => $google_id,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'full_name' => $full_name,
                            'user' => $full_name,
                            'picture' => $picture
                        ];
                        $this->UserModel->updateUser($user->id, $update_data); 
                        $user = $this->UserModel->getUserById($user->id); 
                    }
                }
                
                if ($user) {
                    $auth_userdetails = [
                        'id' => $user->id,
                        'rut' => $user->rut, 
                        'user' => $user->user, 
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'full_name' => $user->full_name, 
                        'rol' => $user->rol,
                        'email' => $user->email,
                        'google_id' => $user->google_id,
                        'picture' => $user->picture
                    ];

                    $this->session->set_userdata('authenticated', $user->rol); 
                    $this->session->set_userdata('auth_user', $auth_userdetails);
                    $this->session->set_userdata('user_authenticated', $user->id);
                    $this->session->set_userdata('user_google', $user->google_id);
                    $this->session->set_userdata('logged_in', TRUE); 

                    $this->session->set_flashdata('status', 'Has iniciado sesión con Google.');
                    redirect(base_url('welcome'));
                } else {
                    $this->session->set_flashdata('status', 'Error desconocido al procesar el usuario después de Google Login.');
                    log_message('error', 'Google Login: $user object is null after processing.');
                    redirect(base_url('login'));
                }

            } catch (Exception $e) {
                $this->session->set_flashdata('status', 'Error al iniciar sesión con Google: ' . $e->getMessage());
                log_message('error', 'Google Login Error: ' . $e->getMessage());
                redirect(base_url('login'));
            }
        } else {
            $this->session->unset_userdata('google_access_token');
            $this->session->unset_userdata('authenticated');
            $this->session->unset_userdata('auth_user');
            $this->session->unset_userdata('user_authenticated');
            $this->session->unset_userdata('user_google');
            $this->session->unset_userdata('logged_in');

            $auth_url = $client->createAuthUrl();
            redirect($auth_url);
        }
        
    }

    public function profile() {
        $user_id_from_session = $this->session->userdata('user_authenticated');

        if ($user_id_from_session) {
            $user_from_db = $this->UserModel->getUserById($user_id_from_session);
            
            if ($user_from_db) {
                $data['logged_user_data'] = [
                    'id' => $user_from_db->id,
                    'rut' => $user_from_db->rut,
                    'user' => $user_from_db->user,
                    'first_name' => $user_from_db->first_name,
                    'last_name' => $user_from_db->last_name,
                    'full_name' => $user_from_db->full_name,
                    'rol' => $user_from_db->rol,
                    'email' => $user_from_db->email,
                    'google_id' => $user_from_db->google_id,
                    'picture' => $user_from_db->picture 
                ];
            } else {
                $this->session->unset_userdata('authenticated');
                $this->session->unset_userdata('auth_user');
                $this->session->unset_userdata('user_authenticated');
                $this->session->set_flashdata('status', 'Tu sesión ha expirado o el usuario no existe.');
                redirect(base_url('login'));
            }

            if ($this->session->userdata('google_access_token')) {
                $client = new Client();
                $client->setClientId($this->config->item('google_client_id'));
                $client->setClientSecret($this->config->item('google_client_secret'));
                $client->setAccessToken($this->session->userdata('google_access_token'));
                $client->addScope("email");
                $client->addScope("profile");
                $google_service = new Oauth2($client);
                $data['user_info_google_api'] = $google_service->userinfo->get();
            }
            
            $this->load->view('auth/profile', $data);
        } else {
            redirect(base_url('login'));
        }
    }

    // public function logout() {
    //     $this->session->unset_userdata('access_token');
    //     redirect(base_url('login'));
    // }
}

?>