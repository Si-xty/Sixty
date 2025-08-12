<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property UserModel $UserModel
 */

class LoginController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if($this->session->has_userdata('authenticated'))
        {   
            // echo json_encode(['status' => 'success', 'message' => 'Registrado correctamente']);
            $this->session->set_flashdata('status', 'Ya estas logeado');
            redirect(base_url('welcome'));
        }

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->load->model('UserModel');
    }
    
    public function index()
    {
        $this->load->view('auth/login.php');
    }

    public function a()
    {
        $this->load->view('a');
    }

    public function login()
    {
        $this->form_validation->set_rules('email', '"Correo electrónico"', 'trim|required');
        $this->form_validation->set_rules('password', '"Contraseña"', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error', 
                'message' => strip_tags(validation_errors())
            ]);
        } else {
            $email_or_user = $this->input->post('email');
            $input_password = $this->input->post('password');

            $userM = new UserModel();
            $user = $userM->loginUser($email_or_user);

            if ($user && password_verify($input_password, $user->password)) {
                $auth_userdetails = [
                    'user' => $user->user,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'rol' => $user->rol
                ];

                $this->session->set_userdata('authenticated', $user->rol);
                $this->session->set_userdata('auth_user', $auth_userdetails);
                $this->session->set_userdata('user_authenticated', $user->id);

                $user = new UserModel();
                // $user->updateConnection($this->session->userdata('user_authenticated'));

                echo json_encode([
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Logeado correctamente',
                    'redirect' => base_url('welcome'),
                ]);
            } else {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Correo electrónico o contraseña inválido.'
                ]);
            }
        }
    }

}

?>