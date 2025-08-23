
<div class="kanban-task" data-task-id="<?= $task->task_id ?>">
	<div class="task-header-flex">
		<div class="task-title-wrapper">
			<p><?= html_escape($task->title) ?></p>
			<div class="tags-container" style="margin-top:4px;">
				<?php if (!empty($task->tags)): ?>
					<?php foreach ($task->tags as $tag): ?>
						<span class="tag" style="background:<?= html_escape($tag->color_code) ?>;">
							<?= html_escape($tag->tag_name) ?>
						</span>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			   <i class="fas fa-ellipsis-v kanban-ellipsis"></i>
		</a>
		<div class="task-options-dropdown dropdown-menu dropdown-menu-right">
			<div class="dropdown-item tag-submenu-trigger" data-task-id="<?= $task->task_id ?>" style="position:relative;">
				Etiquetas
				<span style="position:absolute; right:12px; top:50%; transform:translateY(-50%);">
					<i class="fas fa-caret-right" style="font-size:13px;"></i>
				</span>
			</div>
			<a class="dropdown-item change-priority" href="#" data-task-id="<?= $task->task_id ?>">Prioridad</a>
			<a class="dropdown-item delete-task" href="#" data-task-id="<?= $task->task_id ?>">Eliminar</a>
		</div>
	</div>
</div>
