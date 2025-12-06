// ==========================================
// ARCHIVO: assets/js/mapa_logica.js
// ==========================================

// 1. OBTENER CONFIGURACIÓN DESDE EL DOM (DATA ATTRIBUTES)
// Buscamos el div que definimos en la vista index.php
const container = document.getElementById('mapa-contenedor');

if (!container) {
    console.error("Error: No se encontró el contenedor #mapa-contenedor con la configuración.");
}

// Convertimos los atributos data-* en un objeto de configuración útil
// Nota: data-base-url se convierte automáticamente en dataset.baseUrl
const CONFIG = {
    baseUrl: container.dataset.baseUrl,
    urlImagen: container.dataset.imgUrl,
    anchoReal: parseFloat(container.dataset.ancho),
    altoReal: parseFloat(container.dataset.alto),
    radioDea: parseFloat(container.dataset.radio)
};

// ==========================================
// 2. VARIABLES GLOBALES DEL CANVAS
// ==========================================
let canvas = document.getElementById('mapaCanvas');
let ctx = canvas.getContext('2d');
let img = new Image();

let puntos = [];         // Almacenará todos los objetos (DEAs y Lugares)
let modoActual = 'null';
let scaleX = 0;          // Escala Horizontal (Px / Metro)
let scaleY = 0;          // Escala Vertical (Px / Metro)
let construccionPoligono = []; // Array temporal para guardar los puntos mientras dibujas
let cursorPos = {x: 0, y: 0};  // Para dibujar la línea guía elástica

let visibilidad = {
    area: true,  // Edificios visibles por defecto
    dea: true,   // DEAs visibles por defecto
    lugar: true  // Puntos visibles por defecto
};

// Conexiones gráficas DEA -> Edificio (cuando cobertura individual >= 99%)
let conexionesDeaEdificio = []; // {deaId, deaX, deaY, edificioNombre, cx, cy}

// ==========================================
// 3. INICIALIZACIÓN
// ==========================================

// Cargamos la imagen definida en la configuración
img.src = CONFIG.urlImagen;

img.onload = function() {
    // 1. Ajustar el tamaño del canvas al tamaño real de la imagen
    canvas.width = img.width;
    canvas.height = img.height;
    
    // 2. Calcular las escalas basándonos en las medidas reales (metros)
    scaleX = canvas.width / CONFIG.anchoReal;
    scaleY = canvas.height / CONFIG.altoReal;
    
    // 3. Cargar puntos existentes desde la Base de Datos
    cargarPuntosDeBD();
    
    console.log(`Mapa iniciado. Escalas: X=${scaleX.toFixed(2)}, Y=${scaleY.toFixed(2)}`);
};

// ==========================================
// 4. GESTIÓN DE EVENTOS (CLICS)
// ==========================================

function obtenerCoordenadasMouse(e) {
    const rect = canvas.getBoundingClientRect();
    // Factor de escala: Tamaño Real Imagen / Tamaño Visual en Pantalla
    const factorX = canvas.width / rect.width;
    const factorY = canvas.height / rect.height;

    return {
        x: (e.clientX - rect.left) * factorX,
        y: (e.clientY - rect.top) * factorY
    };
}

canvas.addEventListener('mousemove', function(e) {
    // Usamos la nueva función corregida
    const coords = obtenerCoordenadasMouse(e);
    cursorPos.x = coords.x;
    cursorPos.y = coords.y;
    
    // Si estamos dibujando un polígono, redibujar constantemente para ver la línea
    if (modoActual === 'crear_area' && construccionPoligono.length > 0) {
        redibujarMapa();
    }
});

canvas.addEventListener('mousedown', function(e) {
    // Usamos la nueva función corregida
    const coords = obtenerCoordenadasMouse(e);
    const x = coords.x;
    const y = coords.y;

    if (e.button === 0) { // Clic Izquierdo
        if (modoActual === null) {
            alert("Por favor, selecciona una herramienta del panel primero.");
            return;
        }

        // Si estamos en modo área y hacemos clic en un edificio existente -> ABRIR EDICIÓN
        if (modoActual === 'crear_area') {
            let areaClickeada = buscarAreaEnPunto(x, y);
            
            if (areaClickeada) {
                // (Usamos e.clientX/Y para posicionar el div HTML absoluto)
                const rect = canvas.getBoundingClientRect();
                abrirPopupEdicion(areaClickeada, (x / (canvas.width / rect.width)), (y / (canvas.height / rect.height)));
                return; 
            }
            
            gestionarClicPoligono(x, y);
        } else {
            agregarPunto(x, y);
        }
        // --------------------------------------

    } else if (e.button === 2) {
        eliminarPuntoCercano(x, y);
    }
});

// Función auxiliar para saber si clicamos un edificio
function buscarAreaEnPunto(x, y) {
    // Recorremos las áreas guardadas
    for(let p of puntos) {
        if (p.tipo === 'area' && p.vertices) {
            if (puntoEnPoligono(x, y, p.vertices)) {
                return p; // Retornamos el edificio encontrado
            }
        }
    }
    return null;
}

function gestionarClicPoligono(x, y) {
    // 1. Si ya tenemos puntos, verificar si hicimos clic en el PRIMERO para CERRAR
    if (construccionPoligono.length > 0) {
        let primerPunto = construccionPoligono[0];
        let dist = Math.sqrt(Math.pow(x - primerPunto.x, 2) + Math.pow(y - primerPunto.y, 2));

        // Si estamos a menos de 15px del inicio, cerramos el polígono
        if (dist < 15) {
            finalizarPoligono();
            return;
        }
    }

    // 2. Si no cerramos, agregamos un vértice nuevo
    construccionPoligono.push({x: x, y: y});
}

function finalizarPoligono() {
    if (construccionPoligono.length < 3) {
        alert("Un edificio necesita al menos 3 puntos.");
        construccionPoligono = [];
        redibujarMapa();
        return;
    }

    let count = puntos.filter(p => p.tipo === 'area').length + 1;
    let nombre = "Edificio " + count;
    
    // Usamos el primer punto como "ancla" (x, y)
    let centro = construccionPoligono[0]; 

    // Guardar en BD
    let nuevoArea = {
        tipo: 'area',
        x: centro.x,
        y: centro.y,
        vertices: JSON.parse(JSON.stringify(construccionPoligono)), // Copia profunda
        nombre: nombre
    };

    // AJAX GUARDAR (Similar a agregarPunto pero enviando vertices)
    $.ajax({
        url: CONFIG.baseUrl + 'ajax_guardar',
        type: 'POST',
        data: JSON.stringify(nuevoArea), // Asegúrate de que vertices vaya aquí
        contentType: "application/json",
        dataType: 'json',
        success: function(res) {
            nuevoArea.id = res.id;
            puntos.push(nuevoArea);
            construccionPoligono = []; // Limpiamos temporal
            // Recalcular cobertura y redibujar líneas
            calcularCobertura();
            redibujarMapa();
        }
    });
}

// Prevenir que salga el menú contextual nativo al dar clic derecho
canvas.addEventListener('contextmenu', event => event.preventDefault());

// Función llamada desde los botones del Panel de Control
function setModo(modo) {
    modoActual = modo;
    
    // Actualizar clases visuales de los botones (Bootstrap/CSS)
    $('.btn-modo').removeClass('active');
    if(modo === 'lugar') {
        $('#btnPunto').addClass('active');
    } else {
        $('#btnDea').addClass('active');
    }
}

// ==========================================
// 5. LÓGICA DE DATOS Y AJAX
// ==========================================

function agregarPunto(x, y) {
    let nombre = "";
    
    // 1. CÁLCULO INMEDIATO DEL NOMBRE
    // Al mirar el array local 'puntos' (que actualizaremos al instante), 
    // el conteo siempre será correcto aunque hagas clics muy rápidos.
    if (modoActual === 'lugar') {
        let count = puntos.filter(p => p.tipo === 'lugar').length + 1;
        nombre = "Punto " + count;
    } else {
        let count = puntos.filter(p => p.tipo === 'dea').length + 1;
        nombre = "DEA " + count;
    }

    // 2. CREAR ID TEMPORAL
    // Usamos el tiempo actual + un random para identificar este punto mientras viaja al servidor
    let tempID = 'temp_' + Date.now() + Math.random();

    let nuevoPunto = {
        id: tempID,      // ID provisorio (string)
        tipo: modoActual,
        x: x,
        y: y,
        nombre: nombre,
        guardando: true  // Flag opcional (útil si quisieras ponerlo gris mientras carga)
    };

    // 3. AGREGAR AL ARRAY Y DIBUJAR DE INMEDIATO (La Clave)
    // Esto reserva el "cupo" en el array para que el siguiente clic ya sepa que existe este punto.
    puntos.push(nuevoPunto);
    redibujarMapa();

    const data = {
        tipo: modoActual,
        x: x,
        y: y,
        nombre: nombre
    };

    // 4. ENVIAR AL SERVIDOR (Ajax)
    $.ajax({
        url: CONFIG.baseUrl + 'ajax_guardar',
        type: 'POST',
        data: JSON.stringify(data),
        contentType: "application/json",
        dataType: 'json',
        success: function(response) {
            if(response.status === 'ok') {
                // 5. ACTUALIZAR EL ID REAL (ÉXITO)
                // Buscamos nuestro punto temporal en el array y le asignamos el ID real de la BD
                let puntoEnArray = puntos.find(p => p.id === tempID);
                if (puntoEnArray) {
                    puntoEnArray.id = response.id; // Aquí recibe el ID numérico real (ej: 45)
                    delete puntoEnArray.guardando; // Quitamos la marca de "guardando"
                    // No hace falta redibujar, visualmente es idéntico
                }
                // Si agregamos un DEA o un Área, recalculamos para mostrar conexiones
                if (modoActual === 'dea' || modoActual === 'crear_area') {
                    calcularCobertura();
                    redibujarMapa();
                }
            }
        },
        error: function() {
            // 6. ROLLBACK (ERROR)
            // Si falló el servidor, borramos el punto del mapa para no mentirle al usuario
            puntos = puntos.filter(p => p.id !== tempID);
            redibujarMapa();
            alert("Error de conexión. El punto no se pudo guardar.");
        }
    });
}

function cargarPuntosDeBD() {
    console.log("📥 Iniciando carga de puntos..."); 

    $.get(CONFIG.baseUrl + 'ajax_obtener_puntos', function(data) {
        // Aseguramos que data sea un objeto, por si jQuery no lo parseó autom.
        let datos = (typeof data === 'string') ? JSON.parse(data) : data;
        
        console.log("📦 Datos recibidos (Rows):", datos.length);

        puntos = datos.map(p => {
            let verticesFinal = null;

            // LÓGICA ROBUSTA PARA VÉRTICES
            if (p.vertices) {
                if (typeof p.vertices === 'object') {
                    // CASO 1: CodeIgniter ya lo entregó como objeto/array
                    verticesFinal = p.vertices;
                } else if (typeof p.vertices === 'string' && p.vertices !== "null") {
                    // CASO 2: Viene como texto JSON, hay que parsear
                    try {
                        verticesFinal = JSON.parse(p.vertices);
                    } catch(e) {
                        console.error("⚠️ Error parseando JSON de vértices para:", p.nombre, e);
                    }
                }
            }

            return {
                id: p.id,
                tipo: p.tipo,
                x: parseFloat(p.x_coord),
                y: parseFloat(p.y_coord),
                nombre: p.nombre,
                vertices: verticesFinal // Asignamos el dato procesado
            };
        });
        
        // Debug: Cuántas áreas detectamos
        let areasDetectadas = puntos.filter(p => p.tipo === 'area').length;
        console.log(`✅ Procesado: ${puntos.length} elementos. (Áreas detectadas: ${areasDetectadas})`);
        // Tras cargar, calcular cobertura para dibujar conexiones y luego redibujar
        calcularCobertura();
        redibujarMapa();
    });
}

function eliminarPuntoCercano(clickX, clickY) {
    let index = -1;
    let minDist = 15; // Tolerancia en píxeles

    // Recorremos todos los puntos para ver cuál clicamos
    for(let i = 0; i < puntos.length; i++) {
        let p = puntos[i];
        
        // ============================================================
        // 1. FILTRO DE VISIBILIDAD (NUEVO)
        // ============================================================
        // Si el tipo de elemento está oculto (ojito cerrado), NO se puede borrar.
        // Es como si fuera un fantasma: no interactúa con el clic.
        if (visibilidad[p.tipo] === false) {
            continue; // Salta al siguiente punto
        }

        // ============================================================
        // 2. FILTRO DE MODO/HERRAMIENTA (NUEVO)
        // ============================================================
        // Si tenemos una herramienta seleccionada, solo permitimos borrar
        // elementos que correspondan a esa herramienta.
        if (modoActual !== null) {
            // Mapeamos el nombre del modo al nombre del tipo de dato
            // modo: 'crear_area' -> tipo: 'area'
            // modo: 'dea'        -> tipo: 'dea'
            // modo: 'lugar'      -> tipo: 'lugar'
            let tipoPermitido = (modoActual === 'crear_area') ? 'area' : modoActual;

            if (p.tipo !== tipoPermitido) {
                continue; // Ignoramos este punto porque no coincide con la herramienta actual
            }
        }

        // ============================================================
        // 3. DETECCIÓN DE CLIC (HIT)
        // ============================================================
        let hit = false;

        // CHECK A: Distancia al punto central (Sirve para DEAs, Puntos y el "ancla" del Área)
        let dx = p.x - clickX;
        let dy = p.y - clickY;
        let dist = Math.sqrt(dx*dx + dy*dy);
        
        if (dist < minDist) {
            hit = true;
        } 
        // CHECK B: Si es un ÁREA, verificamos si el clic está DENTRO del polígono
        else if (p.tipo === 'area' && p.vertices && Array.isArray(p.vertices)) {
            // Solo hacemos el cálculo matemático costoso si pasaron los filtros anteriores
            if (puntoEnPoligono(clickX, clickY, p.vertices)) {
                hit = true;
            }
        }

        if (hit) {
            index = i;
            break; // Encontramos uno, detenemos la búsqueda
        }
    }

    if(index !== -1) {
        let p = puntos[index];
        // Llamada al servidor para eliminar
            $.post(CONFIG.baseUrl + 'ajax_eliminar', {id: p.id}, function(res){
                puntos.splice(index, 1);
                // Recalcular y redibujar tras eliminar
                calcularCobertura();
                redibujarMapa();
            });
        // let nombreTipo = (p.tipo === 'area') ? "el Edificio" : p.nombre;
        
        // if(confirm(`¿Estás seguro de eliminar ${nombreTipo}?`)) {
            
        // }
    }
}

function limpiarPorTipo(tipoObjetivo) {
    let nombre = (tipoObjetivo === 'area') ? "todas las Áreas" : "todos los DEAs";
    
    if(confirm(`¿Deseas eliminar ${nombre}?`)) {
        $.post(CONFIG.baseUrl + 'ajax_limpiar', { tipo: tipoObjetivo }, function(response) {
            // Filtro visual instantáneo
            puntos = puntos.filter(p => p.tipo !== tipoObjetivo);
            calcularCobertura();
            redibujarMapa();
            document.getElementById('resultados').innerHTML = `<small class="text-success">${nombre} eliminados.</small>`;
        }, 'json');
    }
}

function limpiarMapa() {
    if(confirm("⚠️ ¿ESTÁS SEGURO?\n\nEsto borrará TODO el mapa (DEAs y Puntos).")) {
        $.post(CONFIG.baseUrl + 'ajax_limpiar', {}, function(){
            puntos = [];
            calcularCobertura();
            redibujarMapa();
            document.getElementById('resultados').innerHTML = "Mapa reiniciado por completo.";
        }, 'json');
    }
}

// ==========================================
// 6. FUNCIONES DE DIBUJO (CANVAS)
// ==========================================

function redibujarMapa() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(img, 0, 0);

    // 1. DIBUJAR ÁREAS (Solo si visibilidad.area es TRUE)
    if (visibilidad.area) {
        puntos.forEach(p => { 
            if (p.tipo === 'area') dibujarArea(p); 
        });
    }

    // 2. DIBUJAR DEAS (Solo si visibilidad.dea es TRUE)
    if (visibilidad.dea) {
        puntos.forEach(p => { 
            if (p.tipo === 'dea') dibujarDEA(p);
        });
    }

    // 3. DIBUJAR PUNTOS (Solo si visibilidad.lugar es TRUE)
    if (visibilidad.lugar) {
        puntos.forEach(p => { 
            if (p.tipo === 'lugar') dibujarLugar(p);
        });
    }

    // 4. Dibujar Polígono en Construcción (Siempre visible si lo estás dibujando)
    if (construccionPoligono.length > 0) {
        // ... (Tu código de dibujo de línea elástica sigue igual aquí) ...
        ctx.beginPath();
        ctx.moveTo(construccionPoligono[0].x, construccionPoligono[0].y);
        for (let i = 1; i < construccionPoligono.length; i++) {
            ctx.lineTo(construccionPoligono[i].x, construccionPoligono[i].y);
        }
        ctx.lineTo(cursorPos.x, cursorPos.y);
        ctx.strokeStyle = "orange";
        ctx.lineWidth = 2;
        ctx.stroke();
        
        ctx.beginPath();
        ctx.arc(construccionPoligono[0].x, construccionPoligono[0].y, 5, 0, Math.PI*2);
        ctx.fillStyle = "green";
        ctx.fill();
    }

    // 5. Dibujar flechas DEA -> Edificio solo si los DEAs están visibles
    if (visibilidad.dea && conexionesDeaEdificio.length > 0) {
        ctx.strokeStyle = '#ff0000';
        ctx.lineWidth = 2;
        conexionesDeaEdificio.forEach(con => {
            dibujarFlecha(con.deaX, con.deaY, con.cx, con.cy, {
                color: '#ff0000',
                lineWidth: 2,
                headLength: 12,
                headAngleDeg: 30
            });
        });
    }
}

// Dibuja una flecha desde (x1,y1) hasta (x2,y2)
function dibujarFlecha(x1, y1, x2, y2, opts) {
    const color = (opts && opts.color) || '#ff0000';
    const lw = (opts && opts.lineWidth) || 2;
    const headLength = (opts && opts.headLength) || 12; // px
    const headAngleDeg = (opts && opts.headAngleDeg) || 30; // grados

    // Línea principal
    ctx.strokeStyle = color;
    ctx.lineWidth = lw;
    ctx.beginPath();
    ctx.moveTo(x1, y1);
    ctx.lineTo(x2, y2);
    ctx.stroke();

    // Calcular dirección
    const dx = x2 - x1;
    const dy = y2 - y1;
    const angle = Math.atan2(dy, dx);
    const headAngle = headAngleDeg * Math.PI / 180;

    // Puntos del triángulo de la punta
    const xA = x2 - headLength * Math.cos(angle - headAngle);
    const yA = y2 - headLength * Math.sin(angle - headAngle);
    const xB = x2 - headLength * Math.cos(angle + headAngle);
    const yB = y2 - headLength * Math.sin(angle + headAngle);

    // Dibujo de la punta (rellena)
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.moveTo(x2, y2);
    ctx.lineTo(xA, yA);
    ctx.lineTo(xB, yB);
    ctx.closePath();
    ctx.fill();
}

function dibujarArea(area) {
    // Validación estricta: si no hay vértices o el array está vacío, no hacemos nada
    if (!area.vertices || !Array.isArray(area.vertices) || area.vertices.length === 0) {
        return; 
    }

    let verts = area.vertices; 

    ctx.beginPath();
    ctx.moveTo(verts[0].x, verts[0].y);
    for (let i = 1; i < verts.length; i++) {
        ctx.lineTo(verts[i].x, verts[i].y);
    }
    ctx.closePath();

    ctx.fillStyle = "rgba(0, 0, 255, 0.3)"; 
    ctx.fill();
    ctx.strokeStyle = "blue";
    ctx.lineWidth = 2;
    ctx.stroke();

    // === CÁLCULO DEL CENTRO (CENTROIDE) ===
    let totalX = 0;
    let totalY = 0;
    
    // Sumamos todas las coordenadas
    for (let i = 0; i < verts.length; i++) {
        totalX += verts[i].x;
        totalY += verts[i].y;
    }

    // Dividimos por la cantidad de vértices para sacar el promedio
    let centroX = totalX / verts.length;
    let centroY = totalY / verts.length;

    // === DIBUJAR TEXTO EN EL CENTRO ===
    ctx.fillStyle = "black"; 
    ctx.font = "bold 12px Arial";
    ctx.textAlign = "center";      // Alineación horizontal centrada
    ctx.textBaseline = "middle";   // Alineación vertical centrada
    
    ctx.fillText(area.nombre, centroX, centroY);

    // Restauramos la alineación normal para que no afecte a otros elementos del mapa
    ctx.textAlign = "start";
    ctx.textBaseline = "alphabetic";
}

function dibujarLugar(p) {
    ctx.beginPath();
    ctx.arc(p.x, p.y, 5, 0, 2 * Math.PI);
    ctx.fillStyle = "blue";
    ctx.fill();
    ctx.strokeStyle = "white";
    ctx.lineWidth = 1;
    ctx.stroke();
    
    // Etiqueta de texto
    ctx.font = "bold 12px Arial";
    ctx.fillStyle = "blue";
    ctx.fillText(p.nombre, p.x + 8, p.y + 4);
}

function dibujarDEA(p) {
    // 1. Dibujar el centro del DEA (Cuadrado rojo)
    ctx.fillStyle = "#00aa00";
    ctx.fillRect(p.x - 5, p.y - 5, 10, 10);
    
    // ctx.strokeStyle = "black";
    ctx.strokeStyle = "#006600";
    ctx.lineWidth = 1;
    ctx.strokeRect(p.x - 5, p.y - 5, 10, 10);
    
    // Etiqueta de texto
    ctx.font = "bold 12px Arial";
    ctx.fillStyle = "#006600";
    ctx.fillText(p.nombre, p.x, p.y - 10);
    // Nota: Se elimina la elipse de cobertura; ahora las conexiones se dibujan en redibujarMapa
}

// ==========================================
// 7. CÁLCULO MATEMÁTICO DE COBERTURA
// ==========================================

function calcularCobertura() {
    // Reiniciar conexiones antes de nuevo cálculo
    conexionesDeaEdificio = [];
    // 1. Obtenemos las listas
    let areas = puntos.filter(p => p.tipo === 'area');
    let deas = puntos.filter(p => p.tipo === 'dea');
    
    // --- MOVER ÚLTIMO AL PRINCIPIO ---
    if (areas.length > 1) {
        let ultimoEdificio = areas.pop(); 
        areas.unshift(ultimoEdificio);
    }

    let divRes = document.getElementById('resultados');
    let boxCSV = document.getElementById('output-csv'); 

    divRes.innerHTML = "<b>--- ANÁLISIS DE COBERTURA TOTAL ---</b><br>";

    let listaValores = []; // Aquí guardaremos los decimales (0.00 - 1.00)

    if (areas.length === 0) {
        divRes.innerHTML += "<small>No hay edificios dibujados.</small>";
        if(boxCSV) boxCSV.value = ""; 
        return;
    }

    areas.forEach(edificio => {
        let verts = (typeof edificio.vertices === 'string') ? JSON.parse(edificio.vertices) : edificio.vertices;
        
        divRes.innerHTML += `<div style="margin-bottom:10px; border-bottom:1px solid #ccc; padding-bottom:5px;">`;
        divRes.innerHTML += `<u>Edificio: <b>${edificio.nombre}</b></u><br>`;

        let resultado = calcularCoberturaUnion(verts, deas);
        
        // ESTE ES EL VALOR EN PORCENTAJE (Ej: 89.5 o 100)
        let valorPorcentaje = resultado.porcentajeTotal; 

        // Lógica de "Cumple/No Cumple" y redondeo visual
        let estilo = "color: red; font-weight:bold;";
        let icono = "❌";
        let mensaje = "NO CUMPLE";

        // Si está casi al 100%, lo forzamos a 100% cerrado
        if (valorPorcentaje >= 99.5) { 
            valorPorcentaje = 100.00;
            estilo = "color: green; font-weight:bold;";
            icono = "✅";
            mensaje = "CUMPLE (100% Cubierto)";
        }

        // CSV binario: 1 si cubre >= 99%, 0 si no.
        let cumpleBinario = (resultado.porcentajeTotal >= 99.0) ? 1 : 0;
        listaValores.push(cumpleBinario);
        // ---------------------------------------

        // MOSTRAR EN PANTALLA (Usamos valorPorcentaje para que se vea como %)
        divRes.innerHTML += `Cobertura Real: <span style="${estilo}">${valorPorcentaje.toFixed(2)}%</span> ${icono}<br>`;
        divRes.innerHTML += `<small>Estado: ${mensaje}</small><br>`;
        
        if (resultado.detalleIndividual.length > 0) {
            let textoDetalle = resultado.detalleIndividual.map(item => {
                let color = item.cobertura > 0 ? '#333' : '#999'; 
                return `<span style="color:${color}">${item.nombre} (<b>${item.cobertura.toFixed(2)}%</b>)</span>`;
            }).join(', ');

            divRes.innerHTML += `<div style="font-size:12px; margin-top:4px; color:#555;">
                                    <b>DEAs cercanos:</b><br> ${textoDetalle}
                                 </div>`;
        } else {
            divRes.innerHTML += `<small style="color:#666">No hay DEAs cubriendo este edificio.</small>`;
        }
        divRes.innerHTML += `</div>`;

        // Registrar conexiones DEA -> Edificio cuando la cobertura individual >= 99%
        if (resultado.detalleIndividual.length > 0) {
            // Calcular centroide del edificio para trazar la línea
            let totalX = 0, totalY = 0;
            verts.forEach(v => { totalX += v.x; totalY += v.y; });
            let cx = totalX / verts.length;
            let cy = totalY / verts.length;

            resultado.detalleIndividual.forEach(item => {
                if (item.cobertura >= 99.5) {
                    conexionesDeaEdificio.push({
                        deaId: item.id,
                        deaX: item.x,
                        deaY: item.y,
                        edificioNombre: edificio.nombre,
                        cx: cx,
                        cy: cy
                    });
                }
            });
        }
    });

    // Llenar la caja CSV con los valores YA convertidos a decimales
    if (boxCSV) {
        boxCSV.value = listaValores.join(' ');
    }
}

// Mantén tu función de copiar igual (ya funciona con lo que tenga la caja)
function copiarCSV() {
    let copyText = document.getElementById("output-csv");
    if (!copyText || !copyText.value) return;

    copyText.select();
    copyText.setSelectionRange(0, 99999); 

    // Copiamos exactamente lo que hay en la caja (que ya son decimales: 0.xx)
    navigator.clipboard.writeText(copyText.value).then(function() {
        // alert("¡Copiado!: " + copyText.value);
    }, function(err) {
        console.error("Error al copiar: ", err);
    });
}

// === ALGORITMO DE ESCANEO (RASTERIZACIÓN MEJORADA) ===
function calcularCoberturaUnion(verticesEdificio, listaTodosDeas) {
    // 1. Definir la "Caja" (Bounding Box) del edificio
    let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
    verticesEdificio.forEach(v => {
        if(v.x < minX) minX = v.x;
        if(v.x > maxX) maxX = v.x;
        if(v.y < minY) minY = v.y;
        if(v.y > maxY) maxY = v.y;
    });

    // 2. Filtrar DEAs cercanos (Optimización espacial)
    let radioMaxPixels = Math.max(CONFIG.radioDea * scaleX, CONFIG.radioDea * scaleY);
    let diagonalEdificio = Math.sqrt(Math.pow(maxX - minX, 2) + Math.pow(maxY - minY, 2));

    let deasCercanos = listaTodosDeas.filter(d => {
        let centroEdificioX = (minX + maxX) / 2;
        let centroEdificioY = (minY + maxY) / 2;
        let dist = Math.sqrt(Math.pow(d.x - centroEdificioX, 2) + Math.pow(d.y - centroEdificioY, 2));
        return dist < (diagonalEdificio/2 + radioMaxPixels);
    });

    // === PREPARAR CONTADORES INDIVIDUALES ===
    // Creamos un mapa para contar los píxeles de cada DEA: { "dea_id": 0 }
    let contadoresDEAs = {};
    deasCercanos.forEach(d => {
        contadoresDEAs[d.id] = 0; // Iniciamos en 0
    });

    let puntosTotalesEdificio = 0;
    let puntosCubiertosUnion = 0; // Puntos cubiertos por AL MENOS UNO
    
    // Precisión del escaneo (2 es óptimo)
    let paso = 2; 

    let radioX = CONFIG.radioDea * scaleX;
    let radioY = CONFIG.radioDea * scaleY;
    let radioXCuadrado = radioX * radioX;
    let radioYCuadrado = radioY * radioY;

    // 3. Bucle de escaneo
    for (let x = minX; x <= maxX; x += paso) {
        for (let y = minY; y <= maxY; y += paso) {
            
            // A. ¿El píxel es parte del edificio?
            if (puntoEnPoligono(x, y, verticesEdificio)) {
                puntosTotalesEdificio++;
                
                let cubiertoPorAlguien = false;

                // B. REVISAR TODOS LOS DEAS CERCANOS (Ya no hacemos break)
                deasCercanos.forEach(dea => {
                    // Fórmula de elipse
                    let valor = (Math.pow(x - dea.x, 2) / radioXCuadrado) + 
                                (Math.pow(y - dea.y, 2) / radioYCuadrado);
                    
                    if (valor <= 1) {
                        // 1. Este DEA cubre este píxel -> Sumamos a su contador personal
                        contadoresDEAs[dea.id]++;
                        
                        // 2. Marcamos que el píxel está cubierto en general
                        cubiertoPorAlguien = true;
                    }
                });

                // Si al menos un DEA cubrió este píxel, suma al total del edificio (Unión)
                if (cubiertoPorAlguien) {
                    puntosCubiertosUnion++;
                }
            }
        }
    }

    if (puntosTotalesEdificio === 0) return { porcentajeTotal: 0, detalleIndividual: [] };

    // === CALCULAR PORCENTAJES FINALES ===
    
    // 1. Porcentaje de la Unión (El que valida si cumple o no)
    let porcentajeUnion = (puntosCubiertosUnion / puntosTotalesEdificio) * 100;

    // 2. Porcentajes Individuales (Para el listado)
    let detalle = deasCercanos.map(dea => {
        let pixelesEsteDea = contadoresDEAs[dea.id];
        let porcentajeEsteDea = (pixelesEsteDea / puntosTotalesEdificio) * 100;
        
        return {
            id: dea.id,
            nombre: dea.nombre,
            x: dea.x,
            y: dea.y,
            cobertura: porcentajeEsteDea
        };
    });

    // Ordenamos de mayor aporte a menor aporte para que se vea bonito
    detalle.sort((a, b) => b.cobertura - a.cobertura);

    return {
        porcentajeTotal: porcentajeUnion,
        detalleIndividual: detalle
    };
}

// === ALGORITMO MATEMÁTICO ===
function calcularPorcentajeCobertura(vertices, dea) {
    // 1. Definir la "Caja" (Bounding Box) del edificio para no escanear todo el mapa
    let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
    vertices.forEach(v => {
        if(v.x < minX) minX = v.x;
        if(v.x > maxX) maxX = v.x;
        if(v.y < minY) minY = v.y;
        if(v.y > maxY) maxY = v.y;
    });

    // 2. Escanear píxel por píxel (o cada 2px para velocidad) dentro de la caja
    let puntosDentroEdificio = 0;
    let puntosDentroDEA = 0;
    let paso = 2; // Precisión (1 es máxima, 5 es rápida)

    // Radio visual en píxeles (considerando que puede ser un óvalo)
    let radioX = CONFIG.radioDea * scaleX;
    let radioY = CONFIG.radioDea * scaleY;

    for (let x = minX; x <= maxX; x += paso) {
        for (let y = minY; y <= maxY; y += paso) {
            
            // A. ¿El punto (x,y) es parte del edificio?
            if (puntoEnPoligono(x, y, vertices)) {
                puntosDentroEdificio++;

                // B. Si es parte del edificio... ¿Está dentro del radio del DEA?
                // Fórmula de la elipse: (x-h)^2/rx^2 + (y-k)^2/ry^2 <= 1
                let valor = Math.pow(x - dea.x, 2) / Math.pow(radioX, 2) + 
                            Math.pow(y - dea.y, 2) / Math.pow(radioY, 2);
                
                if (valor <= 1) {
                    puntosDentroDEA++;
                }
            }
        }
    }

    if (puntosDentroEdificio === 0) return 0;
    return (puntosDentroDEA / puntosDentroEdificio) * 100;
}

// Función auxiliar: Ray Casting Algorithm para ver si un punto está en un polígono
function puntoEnPoligono(x, y, vertices) {
    let inside = false;
    for (let i = 0, j = vertices.length - 1; i < vertices.length; j = i++) {
        let xi = vertices[i].x, yi = vertices[i].y;
        let xj = vertices[j].x, yj = vertices[j].y;
        
        let intersect = ((yi > y) != (yj > y)) &&
            (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) inside = !inside;
    }
    return inside;
}

function toggleVisibilidad(tipo) {
    // 1. Invertir estado
    visibilidad[tipo] = !visibilidad[tipo];
    
    // 2. Actualizar icono del botón
    let btn = document.getElementById('ojo-' + tipo);
    if (visibilidad[tipo]) {
        btn.innerHTML = '👁️'; // Ojo abierto
        btn.classList.remove('active'); // Quitamos estilo presionado
    } else {
        btn.innerHTML = '🚫'; // Prohibido / Ojo cerrado
        btn.classList.add('active'); // Añadimos estilo presionado (se verá más oscuro)
    }

    // 3. Redibujar el mapa con la nueva configuración
    redibujarMapa();
}

function abrirPopupEdicion(area, x, y) {
    let popup = document.getElementById('popup-edicion');
    let input = document.getElementById('input-nombre-edicion');
    let idHidden = document.getElementById('id-area-edicion');

    // 1. Rellenar datos
    input.value = area.nombre;
    idHidden.value = area.id;

    // 2. Posicionar Popup (cerca del clic)
    popup.style.left = x + 'px';
    popup.style.top = y + 'px';
    popup.style.display = 'block';

    // 3. Enfocar y seleccionar texto
    input.focus();
    input.select();
}

function cerrarPopupEdicion() {
    document.getElementById('popup-edicion').style.display = 'none';
}

// Cerrar si clicamos fuera (en el canvas pero lejos del popup)
// OJO: Esto puede requerir lógica extra en el mousedown global si quieres que cierre al clicar el mapa

// Escuchar tecla ENTER en el input
document.getElementById('input-nombre-edicion').addEventListener("keyup", function(event) {
    if (event.key === "Enter") {
        guardarEdicionNombre();
    }
});

function guardarEdicionNombre() {
    let nuevoNombre = document.getElementById('input-nombre-edicion').value;
    let idArea = document.getElementById('id-area-edicion').value;

    if (!nuevoNombre.trim()) return;

    // 1. Actualizar visualmente (Optimista)
    let area = puntos.find(p => p.id == idArea);
    if (area) {
        area.nombre = nuevoNombre;
        // Si cambia el nombre del edificio, solo redibujamos texto,
        // pero mantenemos coherencia de líneas recalculando cobertura
        calcularCobertura();
        redibujarMapa();
    }

    // 2. Enviar al servidor
    $.post(CONFIG.baseUrl + 'ajax_actualizar_nombre', { id: idArea, nombre: nuevoNombre }, function(res) {
        cerrarPopupEdicion();
    });
}