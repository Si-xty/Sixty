<?php
// Partial para renderizar una columna Kanban
// Variables esperadas: $column (objeto columna, con $column->tasks opcional)
?>
<div class="kanban-column" data-column-id="<?= $column->column_id ?>" data-column-order="<?= $column->column_order ?>">
	<div class="kanban-column-header kanban-header-flex kanban-header-hover">
		<div class="kanban-column-title-container">
			<span class="column-name-editable"><?= html_escape($column->column_name) ?></span>
			<input type="text" class="column-name-input form-control" value="<?= html_escape($column->column_name) ?>" style="display: none;">
		</div>

		<div class="column-options-dropdown dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				   <i class="fas fa-ellipsis-v kanban-ellipsis"></i>
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
				<?php $this->load->view('kanban/_task_card', ['task' => $task]); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>
