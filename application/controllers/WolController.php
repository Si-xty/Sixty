<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class WolController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Authentication');
        $this->Authentication->check_isAdmin();
    }

    public function index()
    {
        $this->load->view('templates/header');
		$this->load->view('templates/sidebar');
		$this->load->view('templates/navbar');
		$this->load->view('admin/wol');
		$this->load->view('templates/footer');
    }

    public function wol()
    {
        $mac_config_path = APPPATH . 'config/mac_config.php';

        if (file_exists($mac_config_path))
        {
            include($mac_config_path);

            if (isset($mac_address))
            {
                $command = "wakeonlan " . escapeshellarg($mac_address);
                shell_exec($command);
                
                // $data['message'] = "Se ha enviado el paquete m\u00E1gico a la direcci\u00F3n: " . $mac_address;

                echo json_encode([
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Wake-on-LAN ejecutado correctamente'
                ]);
            } else {
                // $data['message'] = "Error: La direcci\u00F3n MAC no est\u00E1 definida en el archivo de configuraci\u00F3n.";

                echo json_encode([
                    'success' => true,
                    'status' => 'error',
                    'message' => 'MAC no definida'
                ]);
            }
        } else {
            // $data['message'] = "Error: El archivo de configuraci\u00F3n no se encontr\u00F3.";

            echo json_encode([
                    'success' => true,
                    'status' => 'error',
                    'message' => 'Dirección archivo de configuración no encontrado'
                ]);
        }
    }
}