<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ColumnModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Obtener todas las columnas de un tablero específico
    public function get_columns_by_board($board_id) {
        $this->db->where('board_id', $board_id);
        $this->db->order_by('column_order', 'ASC');
        $query = $this->db->get('kanban_columns');
        return $query->result();
    }

    // Obtener una columna por ID
    public function get_column($column_id) {
        $this->db->where('column_id', $column_id);
        $query = $this->db->get('kanban_columns');
        return $query->row();
    }

    // Crear una nueva columna
    public function create_column($data) {
        $this->db->insert('kanban_columns', $data);
        return $this->db->insert_id();
    }

    // Actualizar una columna
    public function update_column($column_id, $data) {
        $this->db->where('column_id', $column_id);
        $this->db->update('kanban_columns', $data);
        return $this->db->affected_rows();
    }

    // Eliminar una columna
    public function delete_column($column_id) {
        $this->db->where('column_id', $column_id);
        return $this->db->delete('kanban_columns');
    }

    public function get_last_column_order($board_id) {
        $this->db->select_max('column_order');
        $this->db->where('board_id', $board_id);
        $query = $this->db->get('kanban_columns');
        $row = $query->row();
        return $row->column_order ? $row->column_order + 1 : 0;
    }

    public function update_column_order($column_id, $new_order) {
        $this->db->where('column_id', $column_id);
        $this->db->update('kanban_columns', ['column_order' => $new_order]);
        return $this->db->affected_rows();
    }

    
    public function delete_columns_by_board($board_id) {
        $this->db->where('board_id', $board_id);
        return $this->db->delete('kanban_columns');
    }

    public function rename_column($column_id, $new_name) {
        $this->db->set('column_name', $new_name);
        $this->db->where('column_id', $column_id);
        return $this->db->update('kanban_columns');
    }
}