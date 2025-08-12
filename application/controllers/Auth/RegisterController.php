<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property Authentication $Authentication
 */

class RegisterController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // if($this->session->has_userdata('authenticated'))
        // {
        //     $this->session->set_flashdata('status', 'Ya estas logeado');
        //     redirect(base_url('ordencompra'));
        // }
        $this->load->model('Authentication');
        $this->Authentication->check_isAdmin();

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->load->model('UserModel');
    }

    public function index()
    {
        $this->load->view('templates/header.php'); 
        $this->load->view('templates/navbar.php'); 
        $this->load->view('templates/left-side.php'); 
        $this->load->view('auth/register.php');
        $this->load->view('templates/footer.php');
    }

    public function register()
    {
        $this->form_validation->set_rules('user', '"User"', 'trim|required|alpha');
        $this->form_validation->set_rules('rut', '"Rut"', 'trim|required');
        $this->form_validation->set_rules('first_name', '"Nombre"', 'trim|required|alpha');
        $this->form_validation->set_rules('last_name', '"Apellido"', 'trim|required|regex_match[/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/]');
        $this->form_validation->set_rules('rol', '"Rol"', 'trim|required|integer');
        $this->form_validation->set_rules('phone_num', '"Teléfono"', 'trim|integer');
        $this->form_validation->set_rules('email', '"Correo electrónico"', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', '"Contraseña"', 'trim|required');
        if($this->form_validation->run() == FALSE)
        {
            echo json_encode([
                'status' => 'error', 
                'message' => strip_tags(validation_errors())
            ]); 
        }
        else
        {
            $data = array(
                'user' => $this->input->post('user'),
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                'rut' => $this->input->post('rut'),
                'phone_num' => $this->input->post('phone_num'),
                'rol' => $this->input->post('rol'),
                'ubicacion' => $this->input->post('ubicacion'),
            );
            $register_user = new UserModel;

            $checking = $register_user->registerUser($data);
            if($checking)
            {
                echo json_encode(['status' => 'success', 'message' => 'Registrado correctamente']);
            }
            else
            {
                echo json_encode(['status' => 'error', 'message' => 'Algo salió mal, intente nuevamente.']);
            }
        }
    }
}


?>