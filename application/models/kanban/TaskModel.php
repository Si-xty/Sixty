<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TaskModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Obtener todas las tareas de una columna específica
    public function get_tasks_by_column($column_id) {
        $this->db->where('column_id', $column_id);
        $this->db->order_by('task_order', 'ASC'); // Muy importante para el orden en el frontend
        $query = $this->db->get('kanban_tasks');
        return $query->result();
    }

    // Obtener una tarea por ID
    public function get_task($task_id) {
        $this->db->where('task_id', $task_id);
        $query = $this->db->get('kanban_tasks');
        return $query->row();
    }

    // Crear una nueva tarea
    public function create_task($data) {
        $this->db->insert('kanban_tasks', $data);
        return $this->db->insert_id();
    }

    // Actualizar una tarea
    public function update_task($task_id, $data) {
        $this->db->where('task_id', $task_id);
        $this->db->update('kanban_tasks', $data);
        return $this->db->affected_rows();
    }

    // Eliminar una tarea
    public function delete_task($task_id) {
        $this->db->where('task_id', $task_id);
        $this->db->delete('kanban_tasks');
        return $this->db->affected_rows();
    }

    // Actualizar la columna de una tarea y su orden (ej. después de arrastrar)
    public function update_task_position($task_id, $new_column_id, $new_order) {
        $data = [
            'column_id' => $new_column_id,
            'task_order' => $new_order
        ];
        $this->db->where('task_id', $task_id);
        $this->db->update('kanban_tasks', $data);
        return $this->db->affected_rows();
    }

    // Obtener las tareas de un tablero incluyendo sus tags
    public function get_tasks_with_tags_by_board($board_id) {
        $this->db->select('t.*, GROUP_CONCAT(kbt.tag_id) as tag_ids, GROUP_CONCAT(kbt.tag_name) as tag_names, GROUP_CONCAT(kbt.color_code) as tag_colors');
        $this->db->from('kanban_tasks t');
        $this->db->join('kanban_columns kc', 't.column_id = kc.column_id');
        $this->db->join('kanban_boards kb', 'kc.board_id = kb.board_id');
        $this->db->join('kanban_task_tags ktt', 't.task_id = ktt.task_id', 'left');
        $this->db->join('kanban_tags kbt', 'ktt.tag_id = kbt.tag_id', 'left');
        $this->db->where('kb.board_id', $board_id);
        $this->db->group_by('t.task_id');
        $this->db->order_by('kc.column_order ASC, t.task_order ASC');
        $query = $this->db->get();
        return $query->result();
    }

}