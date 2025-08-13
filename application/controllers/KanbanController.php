<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KanbanController extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('kanban/BoardModel');
        $this->load->model('kanban/ColumnModel');
        $this->load->model('kanban/TaskModel');
        $this->load->model('kanban/TagModel');
        $this->load->helper('url');

        // ----------------------------------------------------
        // Lógica de Autenticación y Autorización
        // ----------------------------------------------------
        $is_logged_in = $this->session->userdata('logged_in');
        $user_role = $this->session->userdata('authenticated');

        if (!$is_logged_in) {
            redirect('auth/login'); 
        }

        if ($user_role !== '1') {
            redirect('welcome');
        }
    }

    public function index() {
        $user_id = $this->session->userdata('user_authenticated');
        $data['boards'] = $this->BoardModel->get_user_boards($user_id);

        // Si el usuario tiene tableros, cargar el primer tablero por defecto
        if (!empty($data['boards'])) {
            $first_board_id = $data['boards'][0]->board_id;
            $data['columns'] = $this->ColumnModel->get_columns_by_board($first_board_id);
            $data['all_tasks'] = $this->get_tasks_with_tags_structured($first_board_id);
            $data['current_board_id'] = $first_board_id;
        } else {
            // Si no hay tableros, pasar datos vacíos
            $data['columns'] = [];
            $data['tasks'] = [];
            $data['current_board_id'] = NULL;
        }

        // Cargar la vista principal del Kanban
        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('templates/navbar');
        $this->load->view('kanban_main', $data);
        $this->load->view('templates/footer');
    }
    
    // Método para cargar un tablero específico
    public function load_board($board_id) {
        $user_id = $this->session->userdata('user_authenticated');
        $board = $this->BoardModel->get_board($board_id, $user_id);
        
        if ($board) {
            $data['boards'] = $this->BoardModel->get_user_boards($user_id);
            $data['columns'] = $this->ColumnModel->get_columns_by_board($board_id);
            $data['all_tasks'] = $this->get_tasks_with_tags_structured($board_id);
            $data['current_board_id'] = $board_id;
            
            $this->load->view('templates/header');
            $this->load->view('templates/sidebar');
            $this->load->view('templates/navbar');
            $this->load->view('kanban_main', $data);
            $this->load->view('templates/footer');
        } else {
            show_error('No tienes permisos para ver este tablero o no existe.', 403); 
        }
    }

    private function get_tasks_with_tags_structured($board_id) {
        $all_tasks_raw = $this->TaskModel->get_tasks_with_tags_by_board($board_id);

        $tasks_by_column = [];
        foreach ($all_tasks_raw as $task) {
            if (!isset($tasks_by_column[$task->column_id])) {
                $tasks_by_column[$task->column_id] = [];
            }
            if ($task->tag_ids) {
                $tag_ids = explode(',', $task->tag_ids);
                $tag_names = explode(',', $task->tag_names);
                $tag_colors = explode(',', $task->tag_colors);
                $task->tags = [];
                foreach ($tag_ids as $key => $tag_id) {
                    $task->tags[] = (object) [
                        'tag_id' => $tag_id,
                        'tag_name' => $tag_names[$key],
                        'color_code' => $tag_colors[$key],
                    ];
                }
            } else {
                $task->tags = [];
            }
            $tasks_by_column[$task->column_id][] = $task;
        }
        return $tasks_by_column;
    }
    
    // ----------------------------------------------------
    // Métodos para la API RESTful (AJAX) - C R U D
    // ----------------------------------------------------

    /**
     * Crea un nuevo tablero
     */
    public function create_board() {
        if ($this->input->is_ajax_request()) {
            $user_id = $this->session->userdata('user_authenticated');
            $board_name = $this->input->post('board_name');
            $description = $this->input->post('description');
            
            // Validación básica
            if (empty($board_name)) {
                $response = ['success' => false, 'message' => 'El nombre del tablero es obligatorio.'];
            } else {
                $data = [
                    'user_id' => $user_id,
                    'board_name' => $board_name,
                    'description' => $description
                ];
    
                $board_id = $this->BoardModel->create_board($data);
    
                if ($board_id) {
                    $response = ['success' => true, 'board_id' => $board_id];
                } else {
                    $response = ['success' => false, 'message' => 'Error al crear el tablero.'];
                }
            }
            
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    }
    
    /**
     * Crea una nueva columna
     */
    public function create_column() {
        // 1. Recupera los datos del formulario
        $board_id = $this->input->post('board_id');
        $column_name = $this->input->post('column_name');

        // Aquí deberías añadir validación, por si los datos vienen vacíos
        if (empty($board_id) || empty($column_name)) {
            // Manejar el error: faltan datos
            $response = ['success' => false, 'message' => 'Faltan datos para crear la columna.'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        // 2. Crea un array de datos
        $data = [
            'board_id' => $board_id, // Aquí pasas la variable, NO la etiqueta PHP
            'column_name' => $column_name,
            'column_order' => 0 // Ajusta esto según tu lógica
        ];

        // 3. Llama al modelo para insertar los datos
        $column_id = $this->ColumnModel->create_column($data);

        if ($column_id) {
            $response = ['success' => true, 'column_id' => $column_id, 'message' => 'Columna creada con éxito.'];
        } else {
            $response = ['success' => false, 'message' => 'Error al crear la columna.'];
        }

        // Envía la respuesta JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    
    /**
     * Actualiza el orden de las columnas
     */
    public function update_column_order() {
        if ($this->input->is_ajax_request()) {
            $new_order = $this->input->post('new_order');
            
            if (is_array($new_order)) {
                $result = true;
                foreach ($new_order as $item) {
                    $res = $this->ColumnModel->update_column_order($item['id'], $item['order']);
                    if (!$res) $result = false;
                }
                
                $response = ['success' => $result];
            } else {
                $response = ['success' => false, 'message' => 'Datos de orden no válidos.'];
            }
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    }
    
    /**
     * Crea una nueva tarea
     */
    public function create_task() {
        if ($this->input->is_ajax_request()) {
            $user_id = $this->session->userdata('user_authenticated');
            $column_id = $this->input->post('column_id');
            $title = $this->input->post('title');
            $description = $this->input->post('description');
            $priority = $this->input->post('priority');
            $tags = $this->input->post('tags');

            if (empty($title) || empty($column_id)) {
                $response = ['success' => false, 'message' => 'El título de la tarea y el ID de la columna son obligatorios.'];
            } else {
                $last_task = $this->db->select_max('task_order')->where('column_id', $column_id)->get('kanban_tasks')->row();
                $new_order = ($last_task->task_order !== NULL) ? $last_task->task_order + 1 : 0;
    
                $data = [
                    'user_id' => $user_id,
                    'column_id' => $column_id,
                    'title' => $title,
                    'description' => $description,
                    'priority' => $priority,
                    'task_order' => $new_order
                ];
                
                $task_id = $this->TaskModel->create_task($data);
    
                if ($task_id) {
                    if (!empty($tags)) {
                        $this->TagModel->assign_tags_to_task($task_id, $tags);
                    }
                    $response = ['success' => true, 'task_id' => $task_id];
                } else {
                    $response = ['success' => false, 'message' => 'Error al crear la tarea.'];
                }
            }
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    }
    
    /**
     * Actualiza la posición de una tarea (Drag & Drop)
     */
    public function update_task_position() {
        if ($this->input->is_ajax_request()) {
            $task_id = $this->input->post('task_id');
            $new_column_id = $this->input->post('new_column_id');
            $new_order = $this->input->post('new_order');

            if (!isset($task_id) || !isset($new_column_id) || !isset($new_order)) {
                $response = ['success' => false, 'message' => 'Faltan parámetros para actualizar la posición de la tarea.'];
            } else {
                $result = $this->TaskModel->update_task_position($task_id, $new_column_id, $new_order);
    
                if ($result) {
                    $response = ['success' => true];
                } else {
                    $response = ['success' => false, 'message' => 'Error al actualizar la posición de la tarea.'];
                }
            }
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    }

}