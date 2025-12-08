<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header -->
	<!-- <div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Inicio</h1>
				</div>
			</div>
		</div>
	</div> -->

	<!-- Main content -->
	<section class="content ">
		<div class="container-fluid">
			<!-- Hero section -->
			<div class="hero-banner mt-4 mb-4 p-4 p-md-5 d-flex align-items-center justify-content-between">
				<div>
					<h2 class="hero-title mb-2">Bienvenido a Sixty</h2>
					<p class="hero-subtitle mb-0">Tu espacio para planificar, visualizar y actuar.</p>
				</div>
				<div class="hero-actions d-none d-md-block">
					<a href="<?= base_url('kanban') ?>" class="btn btn-light mr-2"><i class="fas fa-columns mr-1"></i> Kanban</a>
					<a href="<?= base_url('mapa') ?>" class="btn btn-light mr-2"><i class="fas fa-map-marker-alt mr-1"></i> Mapa DEA</a>
					<a href="<?= base_url('calendar') ?>" class="btn btn-light"><i class="far fa-calendar-alt mr-1"></i> Calendario</a>
				</div>
			</div>

			<!-- Mosaic layout -->
			<div class="row">
				<!-- Kanban: tall gradient card -->
				<div class="col-lg-4 col-md-6 mb-3">
					<a href="<?= base_url('kanban') ?>" class="mosaic-link">
						<div class="mosaic-card mosaic-card-gradient h-100">
							<div class="mosaic-icon"><i class="fas fa-columns"></i></div>
							<div class="mosaic-content">
								<h3>Kanban</h3>
								<p>Gestiona tus tareas por tableros, columnas y etiquetas.</p>
							</div>
						</div>
					</a>
				</div>

				<!-- Calendario: wide card with calendar visual placeholder -->
				<div class="col-lg-8 col-md-6 mb-3">
					<a href="<?= base_url('calendar') ?>" class="mosaic-link">
						<div class="mosaic-card h-100 mosaic-card-outline">
							<div class="mosaic-content d-flex align-items-center justify-content-between pr-3">
								<div>
									<h3><i class="far fa-calendar-alt mr-2"></i>Calendario</h3>
									<p>Eventos y recordatorios al alcance de un clic.</p>
								</div>
								<div class="calendar-preview d-none d-md-block">
									<div class="calendar-preview-grid">
										<span class="dot bg-success"></span>
										<span class="dot bg-info"></span>
										<span class="dot bg-warning"></span>
										<span class="dot bg-danger"></span>
										<span class="dot bg-primary"></span>
										<span class="dot bg-secondary"></span>
										<span class="dot bg-success"></span>
										<span class="dot bg-info"></span>
										<span class="dot bg-warning"></span>
									</div>
								</div>
							</div>
						</div>
					</a>
				</div>
			</div>

			<div class="row">
				<!-- Mapa DEA: image-like card -->
				<div class="col-lg-6 col-md-6 mb-3">
					<a href="<?= base_url('mapa') ?>" class="mosaic-link">
						<div class="mosaic-card h-100 mosaic-map">
							<div class="mosaic-overlay">
								<h3><i class="fas fa-map-marker-alt mr-2"></i>Mapa DEA</h3>
								<p>Relaciones DEA → edificios, cobertura y edición dinámica.</p>
							</div>
						</div>
					</a>
				</div>

				<!-- Estadísticas/Resumen: compact info card -->
				<div class="col-lg-3 col-md-6 mb-3">
					<div class="mosaic-card h-100 mosaic-card-outline">
						<div class="mosaic-content">
							<h3><i class="fas fa-chart-line mr-2"></i>Resumen</h3>
							<ul class="list-unstyled mb-0 small">
								<li><span class="text-muted">Tableros activos:</span> <strong id="stat-boards">—</strong></li>
								<li><span class="text-muted">Tareas hoy:</span> <strong id="stat-tasks-today">—</strong></li>
								<li><span class="text-muted">Eventos esta semana:</span> <strong id="stat-events">—</strong></li>
							</ul>
						</div>
					</div>
				</div>

				<!-- Acciones recientes: activity stream -->
				<div class="col-lg-3 col-md-6 mb-3">
					<div class="mosaic-card h-100 mosaic-card-outline">
						<div class="mosaic-content">
							<h3><i class="fas fa-stream mr-2"></i>Actividad</h3>
							<ul class="list-unstyled mb-0 small" id="activity-list">
								<li class="text-muted">No hay actividad reciente.</li>
							</ul>
						</div>
					</div>
				</div>
			</div>

		</div>
	</section>
</div>
