<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class WolController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Authentication');
        // Asegura que solo admin pueda acceder
        $this->Authentication->checkAdmin();
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
                
                // Respuesta JSON consistente
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'status' => 'success',
                        'message' => 'Paquete WOL enviado',
                    ]));
            } else {
                // Dirección MAC no definida
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'status' => 'error',
                        'message' => 'La dirección MAC no está definida en la configuración',
                    ]));
            }
        } else {
            // Archivo de configuración no encontrado
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Archivo de configuración WOL no encontrado',
                ]));
        }
    }
}