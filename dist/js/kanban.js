$(function () {
    // ----------------------------------------------------
    // Funcionalidad de Arrastrar y Soltar (Drag & Drop)
    // ----------------------------------------------------
    
    // Obtener el elemento del contenedor del tablero Kanban.
    // CORRECCIÓN: Apuntando al contenedor de las columnas
    var kanbanBoard = document.querySelector('.kanban-board'); 

    if (kanbanBoard) {
        // Inicializar Sortable para las columnas
        var sortableColumns = new Sortable(kanbanBoard, {
            animation: 150,
            handle: '.kanban-column-header',
            filter: '.kanban-column-add', 
            draggable: '.kanban-column',
            onEnd: function (evt) {
                var newOrder = Array.from(evt.from.children).map(function(column, index) {
                    if (column.classList.contains('kanban-column-add')) {
                        return null;
                    }
                    return {
                        id: column.dataset.columnId,
                        order: index
                    };
                }).filter(Boolean);

                $.post(base_url + 'kanban/update_column_order', { new_order: newOrder })
                    .fail(function(xhr, status, error) {
                        showAjaxErrorNotification('Error de comunicación con el servidor al actualizar el orden de las columnas.');
                    });
            }
        });

        // Inicializar Sortable para las tareas dentro de cada columna.
        $('.kanban-tasks').each(function() {
            new Sortable(this, {
                group: 'tasks',
                animation: 150,
                onEnd: function (evt) {
                    var taskId = evt.item.dataset.taskId;
                    var newColumnId = $(evt.to).closest('.kanban-column').data('column-id');
                    var newOrder = Array.from(evt.to.children).indexOf(evt.item);
                    
                    $.post(base_url + 'kanban/update_task_position', { 
                        task_id: taskId,
                        new_column_id: newColumnId,
                        new_order: newOrder
                    }).fail(function(xhr, status, error) {
                        showAjaxErrorNotification('Error de comunicación con el servidor al mover la tarea.');
                    });
                }
            });
        });
    }

    // ----------------------------------------------------
    // Lógica de Modals y Formularios (AJAX)
    // ----------------------------------------------------

    // Lógica para el formulario de creación de tablero.
    $('#create-board-form').submit(function(e) {
        e.preventDefault();
        
        $.post(base_url + 'kanban/create_board', $(this).serialize(), function(response) {
            if (response.success) {
                window.location.href = base_url + 'kanban/load_board/' + response.board_id;
            } else {
                showErrorNotification(response.message || 'Error desconocido.');
            }
        }, 'json')
        .fail(function(xhr, status, error) {
            console.error("Error AJAX al crear tablero: " + status + " - " + error);
            console.log("Respuesta del servidor: ", xhr.responseText);
            showAjaxErrorNotification('Error de comunicación con el servidor al crear el tablero.');
        });
    });
    
    // Lógica para el modal de crear columna.
    $('#create-column-form').submit(function(e) {
        e.preventDefault();
        var boardId = typeof currentBoardId !== 'undefined' ? currentBoardId : null;
        if (!boardId) {
            showErrorNotification('Por favor, crea un tablero primero.');
            return;
        }
        var formData = $(this).serialize() + '&board_id=' + boardId;
        $.post(base_url + 'kanban/create_column', formData, function(response) {
            if (response.success) {
                // showSuccessNotification('Columna creada con éxito!');
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                showErrorNotification(response.message || 'Error al crear la columna.');
            }
        }, 'json')
        .fail(function(xhr, status, error) {
            console.error("Error AJAX al crear columna: " + status + " - " + error);
            console.log("Respuesta del servidor: ", xhr.responseText);
            showAjaxErrorNotification('Error de comunicación con el servidor al crear la columna.');
        });
    });
    

    // ----------------------------------------------------
    // Lógica para la creación de tareas
    // ----------------------------------------------------

    // Muestra el input de la tarea y oculta el botón al hacer clic
    $(document).on('click', '.add-task-btn', function(e) {
        e.preventDefault();
        var popup = $(this).siblings('.add-task-popup');
        $('.add-task-popup').not(popup).hide();
        popup.toggle();

        if (popup.is(':visible')) {
            popup.find('input[name="title"]').focus();
        }
    });

    // CORRECCIÓN: Maneja el envío del formulario de tarea en línea
    $(document).on('submit', '.add-task-form-inline', function(e) {
        e.preventDefault();
        const form = $(this);
        const taskName = form.find('input[name="title"]').val().trim();
        const columnId = form.find('input[name="column_id"]').val();
        const currentUserId = $('#kanban-board-container').data('user-id');
        const defaultPriority = 'Medium';

        console.log('Tarea a crear:', {
            title: taskName,
            column_id: columnId,
            user_id: currentUserId,
            priority: defaultPriority
        });

        if (taskName.length === 0) {
            showErrorNotification('El nombre de la tarea es obligatorio.');
            return;
        }

        $.post(base_url + 'kanban/create_task', {
            title: taskName,
            column_id: columnId,
            user_id: currentUserId,
            priority: defaultPriority
        }, function(response) {
            if (response.success) {
                // showSuccessNotification('Tarea creada con éxito!');
                const newTaskHtml = `
                    <div class="kanban-task" data-task-id="${response.task_id}">
                        <div class="task-header-flex">
                            <div class="task-title-wrapper">
                                <p>${taskName}</p>
                            </div>
                            <div class="task-options-dropdown dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item manage-tags" href="#" data-task-id="${response.task_id}">Etiquetas</a>
                                    <a class="dropdown-item change-priority" href="#" data-task-id="${response.task_id}">Prioridad</a>
                                    <a class="dropdown-item delete-task" href="#" data-task-id="${response.task_id}">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                form.closest('.kanban-column').find('.kanban-tasks').prepend(newTaskHtml);
                form.find('input[name="title"]').val('');
                $('.add-task-popup').hide();
            } else {
                showErrorNotification(response.message || 'Error al crear la tarea.');
            }
        }, 'json').fail(function() {
            showAjaxErrorNotification('Error de comunicación con el servidor al crear la tarea.');
        });
    });

    $(document).ready(function() {
        // Escucha el evento de clic en cualquier lugar de la página
        $(document).on('click', function(event) {
            // Si el clic fue en el fondo del modal de crear tablero
            if ($(event.target).is('#createBoardModal.modal')) {
                // Mueve el foco a un lugar seguro (el cuerpo de la página)
                $('body').focus();
                
                // Cierra el modal de crear tablero
                $('#createBoardModal').modal('hide');
            }
        });

        // Escucha el evento de ocultar del modal de crear tablero
        $('#createBoardModal').on('hidden.bs.modal', function () {
            // Elimina el backdrop
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', '');
        });
    });

    // $(document).on('click', function(e) {
    //     if (!$(e.target).closest('.add-task-popup').length && !$(e.target).closest('.add-task-btn').length) {
    //         $('.add-task-popup').hide();
    //     }
    // });
        
    // ----------------------------------------------------
    // Lógica de la Lista de Tableros
    // ----------------------------------------------------

    $('.clickable-row').on('click', function(e) {
        const url = $(this).data('url');
        if (!$(e.target).closest('.dropdown').length) {
            window.location.href = url;
        }
    });

    $(document).on('click', '.delete-board', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var boardId = $(this).data('board-id');
        var boardElement = $(this).closest('.plan-item');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esto eliminará el tablero y todas sus columnas y tareas!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + 'kanban/delete_board', { board_id: boardId }, function(response) {
                    if (response.success) {
                        showSuccessNotification('Tablero eliminado con éxito!');
                        boardElement.remove();
                    } else {
                        showErrorNotification(response.message || 'Error al eliminar el tablero.');
                    }
                }, 'json').fail(function() {
                    showAjaxErrorNotification('Error de comunicación con el servidor.');
                });
            }
        });
    });

    $(document).on('click', '.rename-board', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var boardId = $(this).data('board-id');
        var currentName = $(this).data('board-name');
        var boardElement = $(this).closest('.plan-item');

        Swal.fire({
            title: 'Renombrar Tablero',
            input: 'text',
            inputValue: currentName,
            inputLabel: 'Nuevo nombre del tablero',
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.post(base_url + 'kanban/rename_board', {
                    board_id: boardId,
                    new_name: result.value
                }, function(response) {
                    if (response.success) {
                        showSuccessNotification('Tablero renombrado con éxito!');
                        boardElement.find('.board-name').text(result.value);
                        boardElement.find('.rename-board').data('board-name', result.value);
                    } else {
                        showErrorNotification(response.message || 'Error al renombrar el tablero.');
                    }
                }, 'json').fail(function() {
                    showAjaxErrorNotification('Error de comunicación con el servidor.');
                });
            }
        });
    });

    // ----------------------------------------------------
    // Lógica para la creación de columnas
    // ----------------------------------------------------

    // Función para manejar la creación de la columna y la actualización de la vista
    function createColumn() {
        const input = $('#add-column-input');
        const newColumnName = input.val().trim();
        const boardId = typeof currentBoardId !== 'undefined' ? currentBoardId : null;

        if (newColumnName.length > 0) {
            if (!boardId) {
                showErrorNotification('ID de tablero no encontrado.');
                return;
            }

            $.post(base_url + 'kanban/create_column', {
                board_id: boardId,
                column_name: newColumnName
            }, function(response) {
                if (response.success) {
                    // showSuccessNotification('Columna creada con éxito!');

                    const newColumnHtml = `
                        <div class="kanban-column" data-column-id="${response.column_id}" data-column-order="${response.column_order}">
                            <div class="kanban-column-header kanban-header-flex kanban-header-hover">
                                <div class="kanban-column-title-container">
                                    <span class="column-name-editable">${newColumnName}</span>
                                    <input type="text" class="column-name-input form-control" value="${newColumnName}" style="display: none;">
                                </div>
                                <div class="column-options-dropdown dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item delete-column" href="#" data-column-id="${response.column_id}">Eliminar</a>
                                    </div>
                                </div>
                            </div>
                            <div class="add-task-container">
                                <button class="add-task-btn" data-column-id="${response.column_id}">
                                    <i class="fas fa-plus"></i> Agregar tarea
                                </button>
                                <div class="add-task-popup" style="display: none;">
                                    <form class="add-task-form-inline">
                                        <input type="hidden" name="column_id" value="${response.column_id}">
                                        <div class="form-group">
                                            <input type="text" name="title" class="form-control" placeholder="Escriba un nombre de tarea *" autofocus>
                                        </div>
                                        <ul class="task-options-list">
                                            <li><a href="#"><i class="fas fa-calendar-alt"></i>Establecer fecha de vencimiento</a></li>
                                            <li><a href="#"><i class="fas fa-user-plus"></i>Asignar</a></li>
                                        </ul>
                                        <button type="submit" class="btn btn-primary btn-block mt-3 add-task-final-btn">Agregar tarea</button>
                                    </form>
                                </div>
                            </div>
                            <div class="kanban-tasks"></div>
                        </div>
                    `;

                    $('.kanban-board').find('.kanban-column-add').before(newColumnHtml);

                    // Reinicializa Sortable en los nuevos contenedores de tareas
                    $('.kanban-tasks').last().each(function() {
                        new Sortable(this, {
                            group: 'tasks',
                            animation: 150,
                        });
                    });
                    
                    input.val('');
                    $('#add-column-input-container').hide();
                    $('#add-column-btn').show();
                } else {
                    showErrorNotification(response.message || 'Error al crear la columna.');
                }
            }, 'json').fail(function() {
                showAjaxErrorNotification('Error de comunicación con el servidor al crear la columna.');
            });
        } else {
            // Si el campo está vacío, simplemente restablece el estado de la UI
            input.val('');
            $('#add-column-input-container').hide();
            $('#add-column-btn').show();
        }
    }

    $('#add-column-btn').on('click', function() {
        $(this).hide();
        $('#add-column-input-container').show();
        $('#add-column-input').focus();
    });

    // Evento keypress (Enter)
    $('#add-column-input').on('keypress', function(e) {
        if (e.which === 13) {
            createColumn();
        }
    });

    // Evento blur (clic afuera)
    $('#add-column-input').on('blur', function() {
        createColumn();
    });


    // ----------------------------------------------------
    // Lógica para mostrar/ocultar el menú de columna
    // ----------------------------------------------------

    $(document).on('click', '.kanban-column-header .dropdown-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var dropdown = $(this).closest('.column-options-dropdown');

        $('.column-options-dropdown').not(dropdown).removeClass('is-open');

        // Alterna la clase 'is-open' para mostrar/ocultar el menú actual
        dropdown.toggleClass('is-open');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.column-options-dropdown').length) {
            $('.column-options-dropdown').removeClass('is-open');
        }
    });


    // ----------------------------------------------------
    // Lógica para Renombrar Columnas
    // ----------------------------------------------------
    

    // Permite editar el nombre de la columna haciendo click en cualquier parte del header, excepto en el dropdown
    $(document).on('click', '.kanban-column-header', function(e) {
        // Si el click es en el dropdown de opciones o en los 3 puntos, no editar
        if (
            $(e.target).closest('.column-options-dropdown').length ||
            $(e.target).hasClass('dropdown-toggle')
        ) {
            return;
        }
        var header = $(this);
        var span = header.find('.column-name-editable');
        var input = header.find('.column-name-input');
        span.hide();
        input.show().focus().select();
    });

    $(document).on('keypress', '.column-name-input', function(e) {
        if (e.which === 13) { // 13 es la tecla Enter
            $(this).blur();
        }
    });
    
    $(document).on('blur', '.column-name-input', function() {
        var input = $(this);
        var span = input.siblings('.column-name-editable');
        var columnId = input.closest('.kanban-column').data('column-id');
        var newName = input.val().trim();
        var currentName = span.text().trim();

        // Oculta el input y muestra el span
        input.hide();
        span.show();

        // Si el nombre no ha cambiado o está vacío, no hacer nada
        if (newName === currentName || newName === "") {
            input.val(currentName); // Restaura el valor original
            return;
        }

        $.post(base_url + 'kanban/rename_column', {
            column_id: columnId,
            new_name: newName
        }, function(response) {
            if (response.success) {
                // showSuccessNotification('Columna renombrada con éxito!');
                span.text(newName);
            } else {
                showErrorNotification(response.message || 'Error al renombrar la columna.');
                input.val(currentName);
                span.text(currentName);
            }
        }, 'json').fail(function() {
            showAjaxErrorNotification('Error de comunicación con el servidor.');
            input.val(currentName);
            span.text(currentName);
        });
    });


    // ----------------------------------------------------
    // Lógica para Eliminar Columnas
    // ----------------------------------------------------

    $(document).on('click', '.delete-column', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var columnId = $(this).data('column-id');
        var columnElement = $(this).closest('.kanban-column');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esto eliminará la columna y todas sus tareas!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + 'kanban/delete_column', { column_id: columnId }, function(response) {
                    if (response.success) {
                        // showSuccessNotification('Columna eliminada con éxito!');
                        columnElement.remove();
                    } else {
                        showErrorNotification(response.message || 'Error al eliminar la columna.');
                    }
                }, 'json').fail(function() {
                    showAjaxErrorNotification('Error de comunicación con el servidor.');
                });
            }
        });
    });


    // ----------------------------------------------------
    // Lógica para mostrar/ocultar el menú de tarea
    // ----------------------------------------------------

    $(document).on('click', '.kanban-task .dropdown-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var dropdown = $(this).closest('.task-options-dropdown');
        $('.task-options-dropdown').not(dropdown).removeClass('is-open');
        dropdown.toggleClass('is-open');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.task-options-dropdown').length) {
            $('.task-options-dropdown').removeClass('is-open');
        }
    });


    // ----------------------------------------------------
    // Funcionalidades para las opciones del menú de tarea
    // ----------------------------------------------------

    // 2. Cambiar Prioridad
    $(document).on('click', '.change-priority', function(e) {
        e.preventDefault();
        var taskId = $(this).data('task-id');
        // Aquí deberás implementar la lógica para mostrar un modal con las opciones de prioridad
        // y enviar la solicitud AJAX
        showInfoNotification('Funcionalidad de cambiar prioridad aún no implementada.');
    });

    // Submenú de etiquetas al hacer hover
    $(document).on('mouseenter', '.tag-submenu-trigger', function(e) {
        var $submenu = $(this).find('.tag-submenu');
        var $list = $submenu.find('.tag-submenu-list');
        var taskId = $(this).data('task-id');
        $submenu.show();
        // Si ya está cargado, no recargar
        if ($list.data('loaded')) return;
        $list.html('<div class="text-center text-muted small py-2">Cargando...</div>');
        // Obtener etiquetas disponibles y las asignadas a la tarea
        $.get(base_url + 'kanban/get_all_tags', function(resp) {
            if (resp.success && resp.tags) {
                // Obtener las etiquetas ya asignadas a la tarea
                var taskTags = [];
                var $task = $('.kanban-task[data-task-id="' + taskId + '"]');
                $task.find('.tags-container .tag').each(function() {
                    taskTags.push($(this).text().trim());
                });
                var html = '';
                if (resp.tags.length === 0) {
                    html = '<div class="text-center text-muted small py-2">No hay etiquetas</div>';
                } else {
                    resp.tags.forEach(function(tag) {
                        var checked = taskTags.includes(tag.tag_name);
                        html += '<div class="tag-submenu-item d-flex align-items-center px-2 py-1" style="cursor:pointer;" data-tag-id="' + tag.tag_id + '" data-task-id="' + taskId + '">';
                        html += '<span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:' + tag.color_code + ';margin-right:7px;"></span>';
                        html += '<span>' + tag.tag_name + '</span>';
                        html += '<span class="ml-auto">' + (checked ? '<i class="fas fa-check text-success"></i>' : '') + '</span>';
                        html += '</div>';
                    });
                }
                $list.html(html).data('loaded', true);
            } else {
                $list.html('<div class="text-center text-danger small py-2">Error al cargar etiquetas</div>');
            }
        }, 'json');
    });

    $(document).on('mouseleave', '.tag-submenu-trigger', function(e) {
        var $submenu = $(this).find('.tag-submenu');
        setTimeout(function() { $submenu.hide(); }, 200);
    });

    // Click en una etiqueta del submenú: asignar o quitar
    $(document).on('click', '.tag-submenu-item', function(e) {
        e.stopPropagation();
        var tagId = $(this).data('tag-id');
        var taskId = $(this).data('task-id');
        var $item = $(this);
        // Detectar si ya está asignada
        var isAssigned = $item.find('i.fa-check').length > 0;
        // Obtener los tag_ids actuales de la tarea
        var $task = $('.kanban-task[data-task-id="' + taskId + '"]');
        var currentTagIds = [];
        $task.find('.tags-container .tag').each(function() {
            var tagName = $(this).text().trim();
            var tagIdAttr = $(this).data('tag-id');
            if (tagIdAttr) currentTagIds.push(tagIdAttr);
        });
        // Si no hay data-tag-id en el span, reconstruir usando nombres
        if (currentTagIds.length === 0) {
            // fallback: usar nombres
            $.get(base_url + 'kanban/get_all_tags', function(resp) {
                if (resp.success && resp.tags) {
                    var tagNameToId = {};
                    resp.tags.forEach(function(tag) { tagNameToId[tag.tag_name] = tag.tag_id; });
                    $task.find('.tags-container .tag').each(function() {
                        var tagName = $(this).text().trim();
                        if (tagNameToId[tagName]) currentTagIds.push(tagNameToId[tagName]);
                    });
                    toggleTagAssignment();
                }
            }, 'json');
        } else {
            toggleTagAssignment();
        }
        function toggleTagAssignment() {
            var newTagIds = currentTagIds.slice();
            if (isAssigned) {
                // Quitar
                newTagIds = newTagIds.filter(function(id) { return id != tagId; });
            } else {
                // Agregar
                newTagIds.push(tagId);
            }
            // Obtener datos actuales de la tarea desde el DOM
            var title = $task.find('.task-title-wrapper p').text().trim();
            var columnId = $task.closest('.kanban-column').data('column-id');
            var priority = 'Medium'; // Por defecto, o puedes obtenerlo si está en el DOM
            var notes = '';
            // Si tienes campos de prioridad o notas en el DOM, obtén su valor real aquí
            // Enviar todos los datos requeridos
            $.post(base_url + 'kanban/update_task', {
                task_id: taskId,
                title: title,
                column_id: columnId,
                priority: priority,
                notes: notes,
                tags: newTagIds
            }, function(resp) {
                if (resp.success) {
                    // Refrescar visualmente las etiquetas de la tarea
                    $.get(base_url + 'kanban/get_all_tags', function(resp2) {
                        if (resp2.success && resp2.tags) {
                            var tagMap = {};
                            resp2.tags.forEach(function(tag) { tagMap[tag.tag_id] = tag; });
                            var html = '';
                            newTagIds.forEach(function(id) {
                                var tag = tagMap[id];
                                if (tag) {
                                    html += '<span class="tag" style="background:' + tag.color_code + ';" data-tag-id="' + tag.tag_id + '">' + tag.tag_name + '</span> ';
                                }
                            });
                            $task.find('.tags-container').html(html);
                        }
                    }, 'json');
                    // Refrescar el submenú
                    $item.closest('.tag-submenu-list').removeData('loaded');
                    $item.closest('.tag-submenu-list').html('<div class="text-center text-muted small py-2">Actualizando...</div>');
                } else {
                    showErrorNotification('No se pudo actualizar la tarea');
                }
            }, 'json');
        }
    });

    // 4. Eliminar Tarea
    $(document).on('click', '.delete-task', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var taskId = $(this).data('task-id');
        var taskElement = $(this).closest('.kanban-task');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción no se puede revertir!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + 'kanban/delete_task', { task_id: taskId }, function(response) {
                    if (response.success) {
                        // showSuccessNotification('Tarea eliminada con éxito!');
                        taskElement.remove();
                    } else {
                        showErrorNotification(response.message || 'Error al eliminar la tarea.');
                    }
                }, 'json').fail(function() {
                    showAjaxErrorNotification('Error de comunicación con el servidor.');
                });
            }
        });
    });


    $(document).ready(function() {
        const taskModal = $('#taskModal');

        // Abre el modal de edición de tarea
        $(document).on('click', '.kanban-task', function() {
            const taskId = $(this).data('task-id');
            
            // Llamada AJAX para obtener los datos
            $.ajax({
                url: base_url + 'kanban/get_task_details',
                method: 'POST',
                data: { task_id: taskId },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.task) {
                        const task = response.task;

                        // Llenar el modal
                        $('#modal-task-title').text(task.title).data('task-id', task.task_id);
                        $('#modal-task-notes').val(task.description);
                        $('#modal-task-priority').val(task.priority);

                        const columnSelect = $('#modal-task-column');
                        columnSelect.empty();
                        $('.kanban-column').each(function() {
                            const id = $(this).data('column-id');
                            const name = $(this).find('.column-name-editable').text();
                            const option = $('<option>').val(id).text(name);
                            if (id == task.column_id) {
                                option.prop('selected', true);
                            }
                            columnSelect.append(option);
                        });

                        // Llenar etiquetas y reiniciar Select2 (eliminado: carga desde endpoint inexistente)
                        $('#modal-task-tags').val([]).trigger('change');


    // Renderiza la opción de etiqueta con color
    function formatTagOption(tag) {
        if (!tag.id) return tag.text;
        var color = $(tag.element).data('color') || '#888';
        return $('<span><span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:' + color + ';margin-right:7px;vertical-align:middle;"></span>' + tag.text + '</span>');
    }

                        // Mostrar el modal
                        taskModal.modal('show');
                        $('.modal-backdrop').css('z-index', 1040);
                        taskModal.css('z-index', 1050);
                    } else {
                        showErrorNotification(response.message || 'Error al cargar los detalles de la tarea.');
                    }
                },
                error: function() {
                    showAjaxErrorNotification('Error de comunicación con el servidor.');
                }
            });
        });

        // Guardar cambios del modal
        $(document).on('click', '.save-btn', function() {
            const taskId = $('#modal-task-title').data('task-id');
            const newTitle = $('#modal-task-title').text();
            const newNotes = $('#modal-task-notes').val();
            const newPriority = $('#modal-task-priority').val();
            const newColumnId = $('#modal-task-column').val();
            const newTags = $('#modal-task-tags').val();

            $.ajax({
                url: base_url + 'kanban/update_task',
                method: 'POST',
                data: {
                    task_id: taskId,
                    title: newTitle,
                    notes: newNotes,
                    priority: newPriority,
                    column_id: newColumnId,
                    tags: newTags
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSuccessNotification('Tarea actualizada con éxito!');
                        
                        const taskElement = $('.kanban-task[data-task-id="' + taskId + '"]');
                        taskElement.find('.task-title-wrapper p').text(newTitle);
                        
                        if (taskElement.closest('.kanban-column').data('column-id') != newColumnId) {
                            $('.kanban-column[data-column-id="' + newColumnId + '"] .kanban-tasks').append(taskElement);
                        }
                        
                        taskModal.modal('hide');
                    } else {
                        showErrorNotification(response.message || 'Error al actualizar la tarea.');
                    }
                },
                error: function() {
                    showAjaxErrorNotification('Error de comunicación con el servidor.');
                }
            });
        });

        // Cierra el modal al hacer clic en el botón de cerrar
        $(document).on('click', '#taskModal .close, #taskModal [data-dismiss="modal"]', function() {
            taskModal.modal('hide');
        });

        // Cierra el modal al hacer clic fuera de él
        $(document).on('click', function(event) {
            if ($(event.target).hasClass('modal')) {
                taskModal.modal('hide');
            }
        });

        taskModal.on('hidden.bs.modal', function () {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        });

    });

    
    

    // ----------------------------------------------------
    // Lógica para abrir el modal de edición de tarea
    // ----------------------------------------------------
    

    // Solución extra: Si el modal se queda bloqueado, permite cerrar con click en backdrop
    $('#taskModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });

});