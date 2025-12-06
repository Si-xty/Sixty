<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MapaController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('mapa_dea/MapaModel');
        $this->load->helper('url');

        $this->load->model('Authentication');
        $this->Authentication->checkTester();

        $is_logged_in = $this->session->userdata('logged_in');
        $user_role = $this->session->userdata('authenticated');

        if (!$is_logged_in) {
            redirect('auth/login'); 
        }
    }

    public function index() {
        // Carga la vista principal
        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('mapa/mapa_dea');
        $this->load->view('templates/footer');
    }

    // AJAX: Obtener datos al iniciar
    public function ajax_obtener_puntos() {
        $user_id = $this->session->userdata('user_authenticated');

        $data = $this->MapaModel->obtener_puntos($user_id);
        echo json_encode($data);
    }

    // AJAX: Guardar punto
    public function ajax_guardar() {
        $input = json_decode($this->input->raw_input_stream, true);
        $user_id = $this->session->userdata('user_authenticated');

        if (!$user_id) { echo json_encode(['status' => 'error']); return; }

        $datos = [
            'user_id' => $user_id,
            'tipo'    => $input['tipo'],
            'nombre'  => $input['nombre'],
            // Si es área, x_coord e y_coord serán el centroide (o el primer punto)
            'x_coord' => $input['x'], 
            'y_coord' => $input['y']
        ];

        // Si es un área, guardamos los vértices
        if ($input['tipo'] === 'area' && isset($input['vertices'])) {
            $datos['vertices'] = json_encode($input['vertices']);
        }

        $id = $this->MapaModel->guardar_punto($datos);
        echo json_encode(['status' => 'ok', 'id' => $id]);
    }

    // AJAX: Eliminar punto
    public function ajax_eliminar() {
        $id = $this->input->post('id'); 
        $user_id = $this->session->userdata('user_authenticated');
        $this->MapaModel->eliminar_punto($id, $user_id);
        echo json_encode(['status' => 'ok']);
    }

    // AJAX: Limpiar todo
    public function ajax_limpiar() {
        $user_id = $this->session->userdata('user_authenticated');
        
        if (!$user_id) {
            echo json_encode(['status' => 'error']);
            return;
        }

        $tipo_a_borrar = $this->input->post('tipo'); 

        if ($tipo_a_borrar) {
            $this->MapaModel->limpiar_por_tipo($user_id, $tipo_a_borrar);
        } else {
            $this->MapaModel->limpiar_todo($user_id);
        }
        
        echo json_encode(['status' => 'ok']);
    }

    public function ajax_actualizar_nombre() {
        $user_id = $this->session->userdata('user_authenticated');
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        if ($user_id && $id) {
            // Aseguramos que el punto pertenezca al usuario antes de editar
            $this->db->where('id', $id);
            $this->db->where('user_id', $user_id);
            $this->db->update('puntos_mapa', ['nombre' => $nombre]);
        }
        echo json_encode(['status' => 'ok']);
    }
}