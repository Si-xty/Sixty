<div class="content-wrapper">
    <div class="container-fluid mt-4">
        <div class="row">
            
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0 card-title" style="font-size: 1rem;">Panel de Control</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Herramientas:</h6>
                        
                        <!-- <div class="btn-group w-100 mb-2" role="group">
                            <button id="btnPunto" class="btn btn-info w-75 text-left btn-modo active" onclick="setModo('lugar')">
                                📍 Marcar Lugar
                            </button>
                            <button class="btn btn-outline-info w-25" onclick="limpiarPorTipo('lugar')" title="Borrar solo Puntos">
                                🗑
                            </button>
                        </div> -->

                        <!-- GRUPO 1: EDIFICIOS (ÁREAS) - AGREGADO OJITO -->
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

                        <!-- GRUPO 2: DEAS - AGREGADO OJITO -->
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

                        <!-- <button class="btn btn-success btn-block mb-2 font-weight-bold" onclick="calcularCobertura()">
                            ▶ CALCULAR COBERTURA
                        </button> -->
                        
                        <button class="btn btn-secondary btn-block btn-sm" onclick="limpiarMapa()">
                            ⚠️ Limpiar Mapa Completo
                        </button>
                    </div>
                </div>
                
                <div class="card mt-3 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Resultados</h6>
                    </div>
                    <div id="resultados" style="height: 250px; overflow-y: auto; padding: 10px; background: white; font-size: 0.9em;">
                        <p class="text-muted text-center mt-4">Selecciona una herramienta y haz clic en el mapa.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9 text-center">
                <div class="canvas-container" 
                    id="mapa-contenedor"
                    data-base-url="<?php echo site_url('mapa/'); ?>"
                    data-img-url="<?php echo base_url('dist/img/mapa_universidad.png'); ?>" 
                    data-ancho="500"
                    data-alto="351"
                    data-radio="150">
                    
                    <canvas id="mapaCanvas" style="width: 100%; height: auto; border: 1px solid #ccc;"></canvas>

                    <!-- MINI OPCIÓN DE EDICIÓN FLOTANTE (NUEVO) -->
                    <div id="popup-edicion" style="display: none; position: absolute; left: 0; top: 0; background: white; padding: 8px 12px; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); border: 1px solid #ccc; z-index: 1000; text-align: left; min-width: 200px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <span style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #6c757d;">Editar Nombre</span>
                            <!-- Mini 'x' para cerrar -->
                            <button type="button" class="close" style="font-size: 1.2rem; outline: none; line-height: 0.8;" onclick="cerrarPopupEdicion()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <input type="text" id="input-nombre-edicion" class="form-control form-control-sm" autocomplete="off">
                        <input type="hidden" id="id-area-edicion">
                        <small class="text-muted" style="font-size: 10px; display: block; margin-top: 4px; text-align: right;">Enter para guardar</small>
                    </div>

                </div>
                <p class="text-muted mt-2"><small>Clic: Agregar | Clic Derecho: Eliminar</small></p>
            </div>

        </div>
        <div class="row">
            <div class="col-9">
                <div style="margin-top: 15px;">
                    <label><b>Valores CSV (Solo datos):</b></label><br>
                    <textarea id="output-csv" rows="2" style="width: 100%; color: blue; font-weight: bold;" readonly></textarea>
                </div>
            </div>
            <div class="col-3">
                <div style="margin-top: 55px;">
                    <!-- <label><b>Valores CSV:</b></label><br> -->
                    <div style="display:flex; gap:5px;">
                        <!-- <textarea id="output-csv" rows="1" style="flex-grow:1; color: blue; font-weight: bold;" readonly></textarea> -->
                        <button type="button" class="btn btn-sm btn-primary" onclick="copiarCSV()">Copiar</button>
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>
</div>