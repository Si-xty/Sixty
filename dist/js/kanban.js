$(function () {
    // Inicializar Select2 para la selección de tags
    $('.select2').select2({
        dropdownParent: $('#addTaskModal')
    });

    // ----------------------------------------------------
    // Funcionalidad de Arrastrar y Soltar (Drag & Drop)
    // ----------------------------------------------------
    
    // Inicializar Sortable para las columnas
    var kanbanBoard = document.getElementById('kanban-board-container');
    var sortableColumns = new Sortable(kanbanBoard, {
        animation: 150,
        handle: '.kanban-column-header',
        onEnd: function (evt) {
            var newOrder = Array.from(evt.to.children).map(function(column, index) {
                return {
                    id: column.dataset.columnId,
                    order: index
                };
            });
            // Llama a la API para actualizar el orden de las columnas
            $.post(base_url + 'kanban/update_column_order', { new_order: newOrder });
        }
    });

    // Inicializar Sortable para las tareas dentro de cada columna
    $('.kanban-tasks').each(function() {
        new Sortable(this, {
            group: 'tasks',
            animation: 150,
            onEnd: function (evt) {
                var taskId = evt.item.dataset.taskId;
                var newColumnId = $(evt.to).closest('.kanban-column').data('column-id');
                var newOrder = Array.from(evt.to.children).indexOf(evt.item);
                
                // Llama a la API para actualizar la posición de la tarea
                $.post(base_url + 'kanban/update_task_position', { 
                    task_id: taskId,
                    new_column_id: newColumnId,
                    new_order: newOrder
                });
            }
        });
    });

    // ----------------------------------------------------
    // Lógica de Modals y Formularios (AJAX)
    // ----------------------------------------------------

    // Lógica para el modal de crear tablero
    $('#create-board-form').submit(function(e) {
        e.preventDefault();
        $.post(base_url + 'kanban/create_board', $(this).serialize(), function(response) {
            if (response.success) {
                alert('Tablero creado con éxito!');
                window.location.reload(); // Recargar para ver el nuevo tablero
            } else {
                alert('Error al crear el tablero: ' + response.message);
            }
        }, 'json');
    });
    
    // Lógica para el modal de crear columna
    $('#create-column-form').submit(function(e) {
        e.preventDefault();
        var boardId = currentBoardId;
        if (!boardId) {
            alert('Por favor, crea un tablero primero.');
            return;
        }
        var formData = $(this).serialize() + '&board_id=' + boardId;
        $.post(base_url + 'kanban/create_column', formData, function(response) {
            if (response.success) {
                alert('Columna creada con éxito!');
                window.location.reload();
            } else {
                alert('Error al crear la columna: ' + response.message);
            }
        }, 'json');
    });

    // Lógica para el modal de añadir tarea
    // Prepara el modal con el ID de la columna
    $('#addTaskModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var columnId = button.data('column-id');
        var modal = $(this);
        modal.find('#task-column-id').val(columnId);
    });
    
    // Lógica para el formulario de añadir tarea
    $('#add-task-form').submit(function(e) {
        e.preventDefault();
        $.post(base_url + 'kanban/create_task', $(this).serialize(), function(response) {
            if (response.success) {
                alert('Tarea creada con éxito!');
                window.location.reload();
            } else {
                alert('Error al crear la tarea: ' + response.message);
            }
        }, 'json');
    });
});