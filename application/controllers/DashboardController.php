<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Input $input
**/

class DashboardController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Authentication');
        $this->Authentication->checkAdmin();

        // ----------------------------------------------------
        // Lógica de Autenticación y Autorización
        // ----------------------------------------------------
        $is_logged_in = $this->session->userdata('logged_in');
        $user_role = $this->session->userdata('authenticated');

        if (!$is_logged_in) {
            redirect('login'); 
        }

        $this->load->model('UserModel');
    }

    public function index()
    {
        // Carga la vista principal
        $data['titulo'] = 'Dashboard';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar');
        $this->load->view('templates/sidebar');
        $this->load->view('dashboard');
        $this->load->view('templates/footer');
    }
}