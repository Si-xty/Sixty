<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TagModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }


    // Obtener todos los tags disponibles (solo lectura, sin filtro por usuario)
    public function get_all_tags() {
        $this->db->order_by('tag_name', 'ASC');
        $query = $this->db->get('kanban_tags');
        return $query->result();
    }


    // Obtener un tag por ID (solo lectura)
    public function get_tag($tag_id) {
        $this->db->where('tag_id', $tag_id);
        $query = $this->db->get('kanban_tags');
        return $query->row();
    }


    // Métodos de creación, edición y borrado de tags eliminados (solo lectura desde ahora)


    // Asignar tags a una tarea (múltiples tags)
    public function assign_tags_to_task($task_id, $tag_ids) {
        // Elimina los tags actuales
        $this->db->where('task_id', $task_id);
        $this->db->delete('kanban_task_tags');

        // Inserta los nuevos tags
        if (!empty($tag_ids)) {
            $batch_data = [];
            foreach ($tag_ids as $tag_id) {
                $batch_data[] = [
                    'task_id' => $task_id,
                    'tag_id' => $tag_id
                ];
            }
            $this->db->insert_batch('kanban_task_tags', $batch_data);
        }
        return TRUE;
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

    // Actualizar el color de una etiqueta
    public function update_tag_color($tag_id, $color_code) {
        $this->db->where('tag_id', $tag_id);
        return $this->db->update('kanban_tags', ['color_code' => $color_code]);
    }
}