<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property UserModel $UserModel
 */

class LogoutController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Authentication');

        $this->load->model('UserModel');
    }

    public function logout()
    {
        $userloged = $this->session->userdata('user_authenticated');
        
		$this->UserModel->updateConnection($userloged);

        // BUSCAR PRIMERO LA INFORMACIÓN DE QUIEN ESTÁ LOGEADO, PARA MANDAR ESA INFO Y ACTUALIZAR LA ULTIMA CONEXION, LUEGO DESLOGEAR
        $this->session->unset_userdata('authenticated');
        $this->session->unset_userdata('auth_user');
        $this->session->unset_userdata('access_token');

        $this->session->set_flashdata('status', 'Te deslogeaste correctamente');
        redirect(base_url('login'));
    }
}

?>