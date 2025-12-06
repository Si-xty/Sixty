<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Input $input
**/

class MainController extends CI_Controller
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

        $this->load->model('UserModel');
    }

    public function index()
    {
        redirect(base_url('login'));
    }
}