<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Kanban</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <select id="board-selector" class="form-control" onchange="window.location.href = base_url + 'kanban/load_board/' + this.value;">
                        <?php if (!empty($boards)): ?>
                            <?php foreach ($boards as $board): ?>
                                <option value="<?= $board->board_id ?>" <?= ($current_board_id == $board->board_id) ? 'selected' : '' ?>>
                                    <?= html_escape($board->board_name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No hay tableros, crea uno nuevo</option>
                        <?php endif; ?>
                    </select>
                    <button class="btn btn-primary mt-2" data-toggle="modal" data-target="#createBoardModal">Crear nuevo tablero</button>
                </div>
            </div>

            <div id="kanban-board-container" class="kanban-board">
                <?php if (!empty($columns)): ?>
                    <?php foreach ($columns as $column): ?>
                        <div class="card kanban-column" data-column-id="<?= $column->column_id ?>">
                            <div class="card-header kanban-column-header">
                                <h3 class="card-title"><?= html_escape($column->column_name) ?></h3>
                                <div class="card-tools">
                                    <a href="#" class="btn btn-tool" data-toggle="modal" data-target="#addTaskModal" data-column-id="<?= $column->column_id ?>"><i class="fas fa-plus"></i></a>
                                </div>
                            </div>
                            <div class="card-body kanban-tasks" id="column-<?= $column->column_id ?>">
                                <?php if (isset($all_tasks[$column->column_id])): ?>
                                    <?php foreach ($all_tasks[$column->column_id] as $task): ?>
                                        <div class="card kanban-task" data-task-id="<?= $task->task_id ?>">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= html_escape($task->title) ?></h5>
                                                <p class="card-text"><small><?= html_escape($task->description) ?></small></p>
                                                <?php if (!empty($task->tags)): ?>
                                                    <div class="mt-2">
                                                        <?php foreach ($task->tags as $tag): ?>
                                                            <span class="badge" style="background-color: <?= html_escape($tag->color_code) ?>; color: #fff;"><?= html_escape($tag->tag_name) ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <div class="kanban-column-add">
                    <button class="btn btn-primary btn-block h-100" data-toggle="modal" data-target="#createColumnModal"><i class="fas fa-plus"></i> Añadir Columna</button>
                </div>
            </div>
        </div>
    </section>
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

<div class="modal fade" id="createColumnModal" tabindex="-1" role="dialog" aria-labelledby="createColumnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createColumnModalLabel">Crear Nueva Columna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="create-column-form">
                    <div class="form-group">
                        <label for="columnName">Nombre de la Columna</label>
                        <input type="text" class="form-control" id="columnName" name="column_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Añadir Tarea</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-task-form">
                    <input type="hidden" id="task-column-id" name="column_id">
                    <div class="form-group">
                        <label for="taskTitle">Título de la Tarea</label>
                        <input type="text" class="form-control" id="taskTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="taskDescription">Descripción</label>
                        <textarea class="form-control" id="taskDescription" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="taskPriority">Prioridad</label>
                        <select class="form-control" id="taskPriority" name="priority">
                            <option>Low</option>
                            <option selected>Medium</option>
                            <option>High</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tags</label>
                        <select class="form-control select2" multiple="multiple" data-placeholder="Selecciona tags" name="tags[]" style="width: 100%;">
                            </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Crear Tarea</button>
                </form>
            </div>
        </div>
    </div>
</div>