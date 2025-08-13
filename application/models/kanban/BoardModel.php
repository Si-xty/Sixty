<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BoardModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Obtener un tablero por ID
    public function get_board($board_id, $user_id) {
        $this->db->where('board_id', $board_id);
        $this->db->where('user_id', $user_id); // Asegurar que solo el usuario propietario pueda acceder
        $query = $this->db->get('kanban_boards');
        return $query->row();
    }

    // Obtener todos los tableros de un usuario
    public function get_user_boards($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('board_id', 'ASC'); // O por un orden preferente
        $query = $this->db->get('kanban_boards');
        return $query->result();
    }

    // Crear un nuevo tablero
    public function create_board($data) {
        $this->db->insert('kanban_boards', $data);
        return $this->db->insert_id(); // Retorna el ID del nuevo tablero
    }

    // Actualizar un tablero
    public function update_board($board_id, $user_id, $data) {
        $this->db->where('board_id', $board_id);
        $this->db->where('user_id', $user_id); // Asegurar que solo el usuario propietario pueda actualizar
        $this->db->update('kanban_boards', $data);
        return $this->db->affected_rows(); // Retorna el número de filas afectadas
    }

    // Eliminar un tablero
    public function delete_board($board_id, $user_id) {
        $this->db->where('board_id', $board_id);
        $this->db->where('user_id', $user_id); // Asegurar que solo el usuario propietario pueda eliminar
        $this->db->delete('kanban_boards');
        return $this->db->affected_rows();
    }
}