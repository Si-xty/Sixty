<div class="content-wrapper">
    <div class="container-fluid mt-4">
        <!-- Hero banner for map -->
        <div class="hero-banner mb-2 p-4 p-md-5 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="hero-title mb-2"><i class="fas fa-map-marker-alt mr-2"></i>Mapa DEA</h2>
                <p class="hero-subtitle mb-0">Edita edificios, posiciona DEAs y visualiza coberturas con flechas.</p>
            </div>
            <div class="hero-actions d-none d-md-block">
                <button class="btn btn-outline-light" type="button" data-toggle="collapse" data-target="#howto-collapse" aria-expanded="false" aria-controls="howto-collapse">
                    ¿Cómo usar?
                </button>
            </div>
        </div>
        <div class="collapse mb-3" id="howto-collapse">
            <div class="card card-body" style="border-radius: 0.5rem;">
                <div class="d-flex flex-wrap" style="gap: 8px;">
                    <span class="badge">Clic Izq: Agregar</span>
                    <span class="badge">Clic Der: Eliminar</span>
                    <span class="badge">Tecla V: Ocultar edificio</span>
                    <span class="badge">Shift + Clic: Seleccionar</span>
                    <span class="badge">Arrastrar esquina: Redimensionar</span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar tools -->
            <div class="col-lg-4 col-md-5 mb-3">
                <div class="mosaic-card mosaic-card-outline h-100">
                    <div class="mosaic-content">
                        <h3 class="mb-3"><i class="fas fa-tools mr-2"></i>Panel de Control</h3>

                        <!-- GRUPO 1: EDIFICIOS (ÁREAS) -->
                        <div class="btn-group w-100 mb-3" role="group">
                            <button id="btnArea" class="btn btn-warning w-50 text-left btn-modo" onclick="setModo('crear_area')">
                                📐 Dibujar Edificio
                            </button>
                            <button class="btn btn-outline-warning w-25" onclick="limpiarPorTipo('area')" title="Borrar Edificios">
                                🗑
                            </button>
                            <button id="ojo-area" class="btn btn-outline-warning w-15" onclick="toggleVisibilidad('area')" title="Ocultar/Mostrar Edificios">
                                👁️
                            </button>
                        </div>

                        <!-- GRUPO 2: DEAS -->
                        <div class="btn-group w-100 mb-2" role="group">
                            <button id="btnDea" class="btn btn-danger w-50 text-left btn-modo" onclick="setModo('dea')">
                                ⚡ Colocar DEA
                            </button>
                            <button class="btn btn-outline-danger w-25" onclick="limpiarPorTipo('dea')" title="Borrar solo DEAs">
                                🗑
                            </button>
                            <button id="ojo-dea" class="btn btn-outline-danger w-15" onclick="toggleVisibilidad('dea')" title="Ocultar/Mostrar DEAs">
                                👁️
                            </button>
                        </div>

                        <hr>

                        <button class="btn btn-secondary btn-block btn-sm" onclick="limpiarMapa()">
                            ⚠️ Limpiar Mapa Completo
                        </button>

                        <!-- Valores CSV integrados en el Panel de Control -->
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Valores CSV</span>
                                <button type="button" class="btn btn-primary btn-sm" onclick="copiarCSV()">Copiar</button>
                            </div>
                            <textarea id="output-csv" rows="2" style="width: 100%; color: #0d6efd; font-weight: 600;" readonly></textarea>
                        </div>
                    </div>
                </div>
        
            </div>

            <!-- Map canvas -->
            <div class="col-lg-8 col-md-7 mb-3">
                <div class="row">
                    <!-- Canvas a la izquierda -->
                    <div class="col-xl-8 col-lg-7 col-md-12 text-center mb-3 mb-lg-0">
                        <div class="mosaic-card mosaic-card-outline h-100">
                            <div class="mosaic-content">
                                <div class="canvas-container"
                                         id="mapa-contenedor"
                                         data-base-url="<?php echo site_url('mapa/'); ?>"
                                         data-img-url="<?php echo base_url('dist/img/mapa_universidad.png'); ?>"
                                         data-ancho="500"
                                         data-alto="351"
                                         data-radio="150">

                                    <canvas id="mapaCanvas" style="width: 100%; height: auto; border: 1px solid #e3e6ef; border-radius: 6px;"></canvas>

                                    <!-- Popup edición flotante -->
                                    <div id="popup-edicion" style="display: none; position: absolute; left: 0; top: 0; background: white; padding: 8px 12px; border-radius: 6px; box-shadow: 0 6px 16px rgba(0,0,0,0.15); border: 1px solid #e3e6ef; z-index: 1000; text-align: left; min-width: 220px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6c757d;">Editar Nombre</span>
                                            <button type="button" class="close" style="font-size: 1.2rem; outline: none; line-height: 0.8;" onclick="cerrarPopupEdicion()">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <input type="text" id="input-nombre-edicion" class="form-control form-control-sm" autocomplete="off">
                                        <input type="hidden" id="id-area-edicion">
                                        <small class="text-muted" style="font-size: 10px; display: block; margin-top: 4px; text-align: right;">Enter para guardar</small>
                                    </div>
                                </div>

                
                            </div>
                        </div>
                    </div>
                    <!-- Resultados a la derecha del canvas -->
                    <div class="col-xl-4 col-lg-5 col-md-12">
                        <div class="mosaic-card mosaic-card-outline h-100">
                            <div class="mosaic-content">
                                <h3 class="mb-2"><i class="fas fa-list-ul mr-2"></i>Resultados</h3>
                                <div id="resultados" style="max-height: 420px; overflow-y: auto; background: white; font-size: 0.9em;">
                                    <p class="text-muted text-center mt-4 mb-4">Selecciona una herramienta y haz clic en el mapa.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    
    </div>
</div>