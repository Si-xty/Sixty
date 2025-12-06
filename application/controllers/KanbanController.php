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

        $this->load->model('Authentication');
        $this->Authentication->checkTester();

        // ----------------------------------------------------
        // Lógica de Autenticación y Autorización
        // ----------------------------------------------------
        $is_logged_in = $this->session->userdata('logged_in');
        $user_role = $this->session->userdata('authenticated');

        if (!$is_logged_in) {
            redirect('auth/login'); 
        }

        // if ($user_role !== '1') {
        //     redirect('welcome');
        // }
    }

    public function index() {
        $user_id = $this->session->userdata('user_authenticated');
        $data['boards'] = $this->BoardModel->get_user_boards($user_id);
        $data['titulo'] = 'Kanban Board';

        // Si el usuario tiene tableros, cargar el primer tablero por defecto
        // if (!empty($data['boards'])) {
        //     $first_board_id = $data['boards'][0]->board_id;
        //     $data['columns'] = $this->ColumnModel->get_columns_by_board($first_board_id);
        //     $data['all_tasks'] = $this->get_tasks_with_tags_structured($first_board_id);
        //     $data['current_board_id'] = $first_board_id;
        // } else {
        //     // Si no hay tableros, pasar datos vacíos
        //     $data['columns'] = [];
        //     $data['tasks'] = [];
        //     $data['current_board_id'] = NULL;
        // }

        // Cargar la vista principal del Kanban
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('kanban/navbar_boards');
        $this->load->view('kanban/kanban_board_list', $data);
        $this->load->view('templates/footer');
    }
    
    // Método para cargar un tablero específico
    public function load_board($board_id) {
        $user_id = $this->session->userdata('user_authenticated');
        $board = $this->BoardModel->get_board($board_id, $user_id);
        
        if ($board) {
            $update_data = ['last_modified' => date('Y-m-d H:i:s')];
            $this->BoardModel->update_board($board_id, $user_id, $update_data);

            $data['titulo'] = $board->board_name;
            $data['boards'] = $this->BoardModel->get_user_boards($user_id);
            
            // 1. Obtener las columnas
            $columns = $this->ColumnModel->get_columns_by_board($board_id);
            
            // 2. Obtener las tareas estructuradas por columna
            $tasks_by_column = $this->get_tasks_with_tags_structured($board_id);
            
            // 3. Unir las tareas a sus respectivas columnas
            foreach ($columns as $column) {
                // Si la columna tiene tareas en el array $tasks_by_column, se las asigna.
                // Si no, se asigna un array vacío.
                $column->tasks = isset($tasks_by_column[$column->column_id]) ? $tasks_by_column[$column->column_id] : [];
            }
            
            $data['columns'] = $columns; // Ahora $data['columns'] tiene las tareas anidadas
            $data['current_board_id'] = $board_id;
            $data['current_board'] = $board;
            $data['currentUserId'] = $user_id;

            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar');
            $this->load->view('kanban/navbar_board', $data);
            $this->load->view('kanban/kanban_main', $data); // Pasa los datos combinados a la vista
            $this->load->view('templates/footer');
        } else {
            show_error('No tienes permisos para ver este tablero o no existe.', 403); 
        }
    }

    private function get_tasks_with_tags_structured($board_id) {
        // Devuelve las tareas agrupadas por columna, cada tarea con sus tags (array de objetos)
        $all_tasks = $this->TaskModel->get_tasks_with_tags_by_board($board_id);
        $tasks_by_column = [];
        foreach ($all_tasks as $task) {
            // Asigna los tags usando TagModel
            $task->tags = $this->TagModel->get_tags_by_task($task->task_id);
            if (!isset($tasks_by_column[$task->column_id])) {
                $tasks_by_column[$task->column_id] = [];
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
        $board_id = $this->input->post('board_id');
        $column_name = $this->input->post('column_name');

        if (empty($board_id) || empty($column_name)) {
            $response = ['success' => false, 'message' => 'Faltan datos para crear la columna.'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $last_order = $this->ColumnModel->get_last_column_order($board_id);

        $data = [
            'board_id' => $board_id,
            'column_name' => $column_name,
            'column_order' => $last_order
        ];

        $column_id = $this->ColumnModel->create_column($data);

        if ($column_id) {
            // Obtener la columna recién creada como objeto
            $column = $this->ColumnModel->get_column($column_id);
            $column->tasks = [];
            // Renderizar la vista parcial de columna
            $column_html = $this->load->view('kanban/_column', ['column' => $column], true);
            $response = [
                'success' => true,
                'column_id' => $column_id,
                'column_name' => $column_name,
                'column_order' => $last_order,
                'column_html' => $column_html
            ];
        } else {
            $response = ['success' => false, 'message' => 'Error al crear la columna.'];
        }

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

                // Obtener el menor task_order de la columna
                $first_task = $this->db->select_min('task_order')->where('column_id', $column_id)->get('kanban_tasks')->row();
                $new_order = ($first_task->task_order !== NULL) ? $first_task->task_order - 1 : 0;

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
                    // Obtener la tarea recién creada con tags
                    $task = $this->TaskModel->get_task($task_id);
                    $task->tags = $this->TagModel->get_tags_by_task($task_id);
                    // Renderizar la vista parcial
                    $task_html = $this->load->view('kanban/_task_card', ['task' => $task], true);
                    $response = [
                        'success' => true,
                        'task_id' => $task_id,
                        'task_html' => $task_html
                    ];
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
            $column_id = $this->input->post('column_id');
            $ordered_task_ids = $this->input->post('ordered_task_ids');
            if (!is_array($ordered_task_ids) || !$column_id) {
                $response = ['success' => false, 'message' => 'Datos de orden no válidos.'];
            } else {
                $result = $this->TaskModel->update_tasks_order_in_column($column_id, $ordered_task_ids);
                if ($result) {
                    $response = ['success' => true];
                } else {
                    $response = ['success' => false, 'message' => 'Error al actualizar el orden de las tareas.'];
                }
            }
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    }

    public function rename_board() {
        $board_id = $this->input->post('board_id');
        $new_name = $this->input->post('new_name');

        if ($this->BoardModel->rename_board($board_id, $new_name)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo renombrar el tablero.']);
        }
    }

    public function delete_board() {
        $user_id = $this->session->userdata('user_authenticated');
        $board_id = $this->input->post('board_id');

        $columns = $this->ColumnModel->get_columns_by_board($board_id);

        if (!empty($columns)) {
            $column_ids = array_map(function($column) {
                return $column->column_id;
            }, $columns);
            
            $this->TaskModel->delete_tasks_by_columns($column_ids, $user_id);
            $this->ColumnModel->delete_columns_by_board($board_id);
        }

        if ($this->BoardModel->delete_board($board_id, $user_id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el tablero.']);
        }
    }

    public function rename_column() {
        $column_id = $this->input->post('column_id');
        $new_name = $this->input->post('new_name');
        
        if ($this->ColumnModel->rename_column($column_id, $new_name)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo renombrar la columna.']);
        }
    }

    public function delete_column() {
        $user_id = $this->session->userdata('user_authenticated');
        $column_id = $this->input->post('column_id');

        $this->TaskModel->delete_tasks_by_columns($column_id, $user_id);

        if ($this->ColumnModel->delete_column($column_id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar la columna.']);
        }
    }

    public function delete_task() {
        $task_id = $this->input->post('task_id');
        if ($this->TaskModel->delete_task($task_id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar la tarea.']);
        }
    }
    

    // Método para obtener los detalles de una tarea
    public function get_task_details() {
        $task_id = $this->input->post('task_id');
        $task = $this->TaskModel->get_task($task_id);
        
        if ($task) {
            $response = [
                'success' => true,
                'task'    => $task
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Tarea no encontrada.'
            ];
        }

        echo json_encode($response);
    }

    
    // Método para actualizar los datos de una tarea (incluye etiquetas)
    public function update_task() {
        $task_id    = $this->input->post('task_id');
        $title      = $this->input->post('title');
        $notes      = $this->input->post('notes');
        $priority   = $this->input->post('priority');
        $column_id  = $this->input->post('column_id');
        $tags       = $this->input->post('tags'); // array de tag_id[]
        if ($tags === null) {
            // Si no se envía el parámetro, forzar array vacío
            $tags = [];
        }

        $data = [
            'title' => $title,
            'description' => $notes,
            'priority' => $priority,
            'column_id' => $column_id
        ];
        $update_status = $this->TaskModel->update_task($task_id, $data);
        $tags_updated = false;
        if (is_array($tags)) {
            $tags_updated = $this->TagModel->assign_tags_to_task($task_id, $tags);
        }
        // Si se actualizó la tarea o las etiquetas, considerar éxito
        if ($update_status || $tags_updated) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'No se pudo actualizar la tarea'];
        }
        echo json_encode($response);
    }

    /**
     * Devuelve todas las etiquetas disponibles (para el submenú de etiquetas)
     */
    public function get_all_tags() {
        if ($this->input->is_ajax_request()) {
            $tags = $this->TagModel->get_all_tags();
            $task_id = $this->input->get('task_id');
            $assigned_tag_ids = [];
            if ($task_id) {
                $assigned_tags = $this->TagModel->get_tags_by_task($task_id);
                foreach ($assigned_tags as $tag) {
                    $assigned_tag_ids[] = $tag->tag_id;
                }
            }
            $response = [
                'success' => true,
                'tags' => $tags,
                'assigned_tag_ids' => $assigned_tag_ids
            ];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        } else {
            show_404();
        }
    }

    /**
     * Actualiza el color de una etiqueta (AJAX)
     */
    public function update_tag_color() {
        if ($this->input->is_ajax_request()) {
            $tag_id = $this->input->post('tag_id');
            $color_code = $this->input->post('color_code');
            if (empty($tag_id) || empty($color_code)) {
                $response = ['success' => false, 'message' => 'Faltan datos para actualizar el color.'];
            } else {
                $result = $this->TagModel->update_tag_color($tag_id, $color_code);
                if ($result) {
                    $response = ['success' => true];
                } else {
                    $response = ['success' => false, 'message' => 'No se pudo actualizar el color.'];
                }
            }
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        } else {
            show_404();
        }
    }

    /**
     * Devuelve el HTML parcial de una tarea (para refresco instantáneo en el frontend)
     */
    public function get_task_card_html() {
        $task_id = $this->input->post('task_id');
        $task = $this->TaskModel->get_task($task_id);
        if ($task) {
            // Obtener los tags actualizados
            $task->tags = $this->TagModel->get_tags_by_task($task_id);
            $task_html = $this->load->view('kanban/_task_card', ['task' => $task], true);
            $response = [
                'success' => true,
                'task_html' => $task_html
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Tarea no encontrada.'
            ];
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * Devuelve el HTML de todas las tareas de una columna (para refresco instantáneo en el frontend)
     */
    public function get_column_tasks_html() {
        $column_id = $this->input->post('column_id');
        // Obtener las tareas de la columna
        $tasks = $this->TaskModel->get_tasks_by_column($column_id);
        // Para cada tarea, obtener sus tags
        foreach ($tasks as $task) {
            $task->tags = $this->TagModel->get_tags_by_task($task->task_id);
        }
        // Renderizar solo el bloque de tareas
        $tasks_html = '';
        foreach ($tasks as $task) {
            $tasks_html .= $this->load->view('kanban/_task_card', ['task' => $task], true);
        }
        $response = [
            'success' => true,
            'tasks_html' => $tasks_html
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
}