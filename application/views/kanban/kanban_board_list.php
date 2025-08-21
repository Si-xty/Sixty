<div class="content-wrapper">
    <!-- <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Mis planes</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createBoardModal">
                        <i class="fas fa-plus"></i> Nuevo plan
                    </button>
                </div>
            </div>
        </div>
    </section> -->

    <section class="content">
        <div class="container-fluid pt-5">
            <div class="row">
                <div class="col-12">
                    <div class="plan-list-header">
                        <span class="plan-name-col">Nombre</span>
                        <span class="plan-privacy-col">Privacidad</span>
                        <span class="plan-last-access-col">Último acceso</span>
                    </div>
                    <?php if (!empty($boards)): ?>
                        <?php foreach ($boards as $board): ?>
                            <div class="plan-item d-flex justify-content-between align-items-center clickable-row" data-url="<?= base_url('kanban/load_board/' . $board->board_id) ?>">
        <span class="plan-name-col d-flex justify-content-between align-items-center">
            <span class="d-flex align-items-center text-dark text-decoration-none">
                <i class="fas fa-tasks text-muted mr-2"></i>
                <span class="board-name"><?= html_escape($board->board_name) ?></span>
            </span>
            <div class="dropdown board-options-dropdown">
                <a href="#" class="dropdown-toggle text-muted" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item rename-board" href="#" data-board-id="<?= $board->board_id ?>" data-board-name="<?= html_escape($board->board_name) ?>">Renombrar</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item delete-board" href="#" data-board-id="<?= $board->board_id ?>">Eliminar</a>
                </div>
            </div>
        </span>
        <span class="plan-privacy-col">
            <i class="fas fa-lock text-muted mr-1"></i> Solo usted
        </span>
        <span class="plan-last-access-col">
            <?php
                // Código para la fecha
                if ($board-> last_modified) {
                    $last_accessed = new DateTime($board-> last_modified);
                    $now = new DateTime();
                    $interval = $now->diff($last_accessed);

                    if ($interval->y > 0) {
                        echo 'Hace ' . $interval->y . ' año(s)';
                    } elseif ($interval->m > 0) {
                        echo 'Hace ' . $interval->m . ' mes(es)';
                    } elseif ($interval->d > 0) {
                        echo 'Hace ' . $interval->d . ' día(s)';
                    } elseif ($interval->h > 0) {
                        echo 'Hace ' . $interval->h . ' hora(s)';
                    } elseif ($interval->i > 0) {
                        echo 'Hace ' . $interval->i . ' minuto(s)';
                    } else {
                        echo 'Hace unos segundos';
                    }
                } else {
                    echo 'Nunca';
                }
            ?>
        </span>
    </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center mt-3">
                            No hay tableros creados. ¡Crea uno nuevo para empezar!
                        </div>
                    <?php endif; ?>
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