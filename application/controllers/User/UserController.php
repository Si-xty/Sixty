<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property UserModel $UserModel
 */

class UserController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if(!$this->session->has_userdata('authenticated'))
        {   
            // echo json_encode(['status' => 'success', 'message' => 'Registrado correctamente']);
            $this->session->set_flashdata('status', 'No estás logeado');
            redirect(base_url('login'));
        }

        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->load->model('UserModel');
    }

    public function index()
    {
        $this->load->view('templates/header');
		$this->load->view('templates/sidebar');
		$this->load->view('templates/navbar');
		$this->load->view('user/profile.php');
		$this->load->view('templates/footer');
    }
}

?>