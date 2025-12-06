<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MapaModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        // $this->load->database();
    }

    // Obtener todos los puntos para pintarlos al cargar
    public function obtener_puntos($user_id) {
        $this->db->where('user_id', $user_id);
        return $this->db->get('puntos_mapa')->result_array();
    }

    // Guardar un nuevo punto
    public function guardar_punto($datos) {
        // Si viene el array de vértices, lo convertimos a JSON para guardarlo
        if (isset($datos['vertices']) && is_array($datos['vertices'])) {
            $datos['vertices'] = json_encode($datos['vertices']);
        }
        
        $this->db->insert('puntos_mapa', $datos);
        return $this->db->insert_id();
    }

    // Eliminar un punto (y actualizar el mapa)
    public function eliminar_punto($id, $user_id) {
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        return $this->db->delete('puntos_mapa');
    }

    // Limpiar por tipo
    public function limpiar_por_tipo($user_id, $tipo) {
        $this->db->where('user_id', $user_id);
        $this->db->where('tipo', $tipo); 
        return $this->db->delete('puntos_mapa');
    }
    
    // Limpiar todo el mapa
    public function limpiar_todo($user_id) {
        $this->db->where('user_id', $user_id);
        return $this->db->delete('puntos_mapa');
    }
}