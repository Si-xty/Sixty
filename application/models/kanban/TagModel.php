<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TagModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Obtener todos los tags de un usuario (o tags generales si user_id es NULL)
    public function get_user_tags($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->or_where('user_id', NULL); // Incluir tags generales si los hay
        $query = $this->db->get('kanban_tags');
        return $query->result();
    }

    // Obtener un tag por ID
    public function get_tag($tag_id) {
        $this->db->where('tag_id', $tag_id);
        $query = $this->db->get('kanban_tags');
        return $query->row();
    }

    // Crear un nuevo tag
    public function create_tag($data) {
        $this->db->insert('kanban_tags', $data);
        return $this->db->insert_id();
    }

    // Actualizar un tag
    public function update_tag($tag_id, $user_id, $data) {
        $this->db->where('tag_id', $tag_id);
        $this->db->where('user_id', $user_id); // Asegurar que solo el propietario o un tag general se actualice
        $this->db->update('kanban_tags', $data);
        return $this->db->affected_rows();
    }

    // Eliminar un tag
    public function delete_tag($tag_id, $user_id) {
        $this->db->where('tag_id', $tag_id);
        $this->db->where('user_id', $user_id); // Asegurar que solo el propietario o un tag general se elimine
        $this->db->delete('kanban_tags');
        return $this->db->affected_rows();
    }

    // Asignar tags a una tarea
    public function assign_tags_to_task($task_id, $tag_ids) {
        $this->db->where('task_id', $task_id);
        $this->db->delete('kanban_task_tags'); // Primero elimina los tags existentes para esta tarea

        if (!empty($tag_ids)) {
            $batch_data = [];
            foreach ($tag_ids as $tag_id) {
                $batch_data[] = [
                    'task_id' => $task_id,
                    'tag_id' => $tag_id
                ];
            }
            return $this->db->insert_batch('kanban_task_tags', $batch_data);
        }
        return TRUE; // No hay tags para asignar, pero la operación fue exitosa
    }

    // Obtener tags de una tarea específica
    public function get_tags_by_task($task_id) {
        $this->db->select('kt.tag_id, kt.tag_name, kt.color_code');
        $this->db->from('kanban_task_tags ktt');
        $this->db->join('kanban_tags kt', 'ktt.tag_id = kt.tag_id');
        $this->db->where('ktt.task_id', $task_id);
        $query = $this->db->get();
        return $query->result();
    }
}