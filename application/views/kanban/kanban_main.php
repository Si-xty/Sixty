<div class="content-wrapper">
    <div class="kanban-header">
    </div>
    <div id="kanban-board-container">
        <div class="kanban-board">
            <?php foreach ($columns as $column): ?>
                <div class="kanban-column" data-column-id="<?= $column->column_id ?>" data-column-order="<?= $column->column_order ?>">
                    <div class="kanban-column-header kanban-header-flex kanban-header-hover">
                        <div class="kanban-column-title-container">
                            <span class="column-name-editable"><?= html_escape($column->column_name) ?></span>
                            <input type="text" class="column-name-input form-control" value="<?= html_escape($column->column_name) ?>" style="display: none;">
                        </div>

                        <div class="column-options-dropdown dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item delete-column" href="#" data-column-id="<?= $column->column_id ?>">Eliminar</a>
                            </div>
                        </div>
                    </div>
                    <div class="add-task-container">
                        <button class="add-task-btn" data-column-id="<?= $column->column_id ?>">
                            <i class="fas fa-plus"></i> Agregar tarea
                        </button>
                        <div class="add-task-popup" style="display:none;">
                            <form class="add-task-form-inline">
                                <input type="hidden" name="column_id" value="<?= $column->column_id ?>">
                                <div class="form-group">
                                    <input type="text" name="title" class="form-control" placeholder="Escriba un nombre para la tarea *" autofocus>
                                </div>
                                <ul class="task-options-list">
                                    <li><a href="#"><i class="fas fa-calendar-alt"></i> Establecer fecha de vencimiento</a></li>
                                    <li><a href="#"><i class="fas fa-user-plus"></i> Asignar</a></li>
                                </ul>
                                <button type="submit" class="btn btn-primary btn-block mt-3 add-task-final-btn">Agregar tarea</button>
                            </form>
                        </div>
                    </div>
                    <div class="kanban-tasks">
                        <?php if (!empty($column->tasks)): ?>
                            <?php foreach ($column->tasks as $task): ?>
                                <div class="kanban-task" data-task-id="<?= $task->task_id ?>">
                                    <div class="task-header-flex">
                                        <div class="task-title-wrapper">
                                            <p><?= html_escape($task->title) ?></p>
                                            <?php if (!empty($task->tags)): ?>
                                                <div class="tags-container" style="margin-top:4px;">
                                                    <?php foreach ($task->tags as $tag): ?>
                                                        <span class="tag" style="background:<?= html_escape($tag->color_code) ?>;">
                                                            <?= html_escape($tag->tag_name) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="task-options-dropdown dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <div class="dropdown-item dropdown-submenu tag-submenu-trigger" data-task-id="<?= $task->task_id ?>" style="position:relative;">
                                                    Etiquetas
                                                    <div class="dropdown-menu tag-submenu" style="display:none; position:absolute; left:100%; top:0; min-width:180px;">
                                                        <div class="tag-submenu-list" data-task-id="<?= $task->task_id ?>">
                                                            <div class="text-center text-muted small py-2">Cargando...</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a class="dropdown-item change-priority" href="#" data-task-id="<?= $task->task_id ?>">Prioridad</a>
                                                <a class="dropdown-item delete-task" href="#" data-task-id="<?= $task->task_id ?>">Eliminar</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="kanban-column-add">
                <button id="add-column-btn" class="kanban-column-header-btn">Agregar nueva columna...</button>
                <div id="add-column-input-container" style="display:none;">
                    <input type="text" id="add-column-input" class="form-control" placeholder="Escriba un nombre para la columna">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createBoardModal" tabindex="-1" role="dialog" aria-labelledby="createBoardModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBoardModalLabel">Crear Nuevo Tablero</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="create-board-form">
                    <div class="form-group">
                        <label for="boardName">Nombre del Tablero</label>
                        <input type="text" class="form-control" id="boardName" name="board_name" required>
                    </div>
                    <div class="form-group">
                        <label for="boardDescription">Descripción (Opcional)</label>
                        <textarea class="form-control" id="boardDescription" name="description"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">
                    <span id="modal-task-title" contenteditable="true"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <div class="form-group">
                    <label>Asignar</label>
                    <select class="form-control select2" multiple="multiple"></select>
                </div> -->

                <div class="form-group">
                    <label for="modal-task-notes">Notas</label>
                    <textarea class="form-control" id="modal-task-notes" placeholder="Escribe una descripción o agrega notas aquí"></textarea>
                </div>

                <div class="form-group">
                    <label>Etiquetas</label>
                    <select class="form-control select2" id="modal-task-tags" multiple="multiple" style="width: 100%;">
                        </select>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="modal-task-priority">Prioridad</label>
                        <select class="form-control" id="modal-task-priority">
                            <option>Low</option>
                            <option selected>Medium</option>
                            <option>High</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="modal-task-column">Columna</label>
                        <select class="form-control" id="modal-task-column">
                            </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary save-btn">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>