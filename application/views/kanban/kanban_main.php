<div class="content-wrapper">
    <div class="kanban-header">
    </div>
    <div id="kanban-board-container">
        <div class="kanban-board">
            <?php foreach ($columns as $column): ?>
                <?php $this->load->view('kanban/_column', ['column' => $column]); ?>
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