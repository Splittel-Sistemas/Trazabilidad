@extends('layouts.menu2')

@section('title', 'Busquedas')

@section('styles')
    <!-- Meta CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Contenedor principal */
        .progress-container {
            width: 100%;
            padding: 20px 0;
            text-align: center;
            position: relative;
            margin-bottom: 40px;
        }
    
        /* Barra de progreso */
        .progress {
            width: 100%;
            height: 30px;
            background-color: #f0f0f0;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    
        .progress-bar {
            height: 100%;
            text-align: center;
            color: white;
            line-height: 30px;
            border-radius: 15px;
            transition: width 0.5s ease, background-color 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
    
        /* Lista de etapas */
        .progress-bar-stages {
            list-style: none;
            display: flex;
            justify-content: space-between;
            padding: 0;
            margin: 0;
            position: relative;
            width: 100%;
            align-items: center;
        }
    
        .stage {
            position: relative;
            text-align: center;
            flex: 1;
            transition: all 0.4s ease;
            color: #999;
            cursor: pointer;
            opacity: 0.7;
        }
    
        .stage:hover {
            opacity: 1;
        }
    
        .stage-circle {
            width: 60px;
            height: 60px;
            background-color: #e0e0e0;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
        }
    
        .stage-circle i {
            font-size: 24px;
            color: #fff;
        }
    
        .stage.pending .stage-circle {
            background-color: #e0e0e0;
            transform: scale(1);
        }
    
        .stage.pending span {
            color: #999;
        }
    
        .stage.completed .stage-circle {
            background-color: #28a745;
            transform: scale(1.2);
            box-shadow: 0 0 12px rgba(0, 255, 0, 0.5);
        }
    
        .stage.completed span {
            color: #28a745;
            font-weight: bold;
        }
    
        /* Estilos de los botones */
        .btn-primary, .btn-secondary {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 25px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
            transform: scale(1.05);
        }
    
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #4e555b;
            transform: scale(1.05);
        }
    
        .btn-primary:active, .btn-secondary:active {
            transform: scale(1);
        }
    
        .ml-2 {
            margin-left: 10px;
        }
        /* Estilo para la etapa activa */
.stage.active .stage-circle {
    background-color: #007bff; /* Azul para la etapa activa */
    transform: scale(1.2);
    box-shadow: 0 0 12px rgba(0, 123, 255, 0.5);
}

.stage.active span {
    color: #007bff;
    font-weight: bold;
}

/* Etapa completada (mantiene el estilo existente) */
.stage.completed .stage-circle {
    background-color: #28a745; /* Verde para etapa completada */
    transform: scale(1.2);
    box-shadow: 0 0 12px rgba(40, 167, 69, 0.5);
}

.stage.completed span {
    color: #28a745;
    font-weight: bold;
}

/* Etapa pendiente */
.stage.pending .stage-circle {
    background-color: #e0e0e0; /* Gris claro para pendientes */
    transform: scale(1);
}

.stage.pending span {
    color: #4492ec;
}


    .grid-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr); /* 4 columnas */
      gap: 20px; /* Espacio entre los elementos */
      width: 100%; /* Ajusta el ancho total del contenedor */
      max-width: 1200px;
    }
    .grid-item {
      text-align: center; /* Centrar el texto y el canvas */
    }
    .small-title {
      font-size: 16px; /* Tamaño ajustado */
      margin-bottom: 10px; /* Espacio entre el título y el canvas */
    }
    canvas {
      user-select: none;
      -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
      border: 1px solid #ddd; /* Opcional: agrega borde a los canvas */
      bord
    }


    </style>
@endsection

@section('content')
    <!-- Breadcrumbs -->
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Busquedas</h4>
        </div>
    </div>
    <div class="container my-4">
        <div class="form-check">
            <input class="form-check-input" id="flexRadioDefault1" type="radio" name="flexRadioDefault" checked onclick="toggleTable()">
            <label class="form-check-label" for="flexRadioDefault1">Buscar Por Orden De Venta</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" id="flexRadioDefault2" type="radio" name="flexRadioDefault" onclick="toggleTable()">
            <label class="form-check-label" for="flexRadioDefault2">Buscar Por Orden De Fabricación</label>
        </div>
        <!-- Tabla 1: Orden de Venta -->
        <div id="tablaVenta" style="display:block;">
            <form id="form-buscar-venta">
                <div class="d-flex">
                    <input class="form-control search-input search form-control-sm" type="text" name="search" placeholder="Buscar Por Orden De Venta...."style="flex: 1 1 0%;">
                    <button class="btn btn-outline-primary" type="button" id="buscarVenta" style="flex: 0 1 auto;">Buscar</button>
                </div>
            </form>
            <div style="margin-top: 20px;"></div>
            <table class="table table-striped table-sm fs--1 mb-0">
                <thead class="bg-info">
                    <tr>
                        <th class="sort border-top ps-3" data-sort="venta">Orden De Venta</th>
                        <th class="sort border-top" data-sort="fabricacion">Nombre Cliente</th>
                        <th class="sort border-top" data-sort="total">Detalles</th>
                        
                    </tr>
                </thead>
                <tbody id="tabla-resultadosVenta">
                </tbody>
            </table>
        </div>
        <!-- Tabla 2: Orden de Fabricación -->
        <div id="tablaFabricacion" style="display:none;">
            <form id="form-buscar-fabricacion">
                <div class="d-flex">
                    <input class="form-control search-input search form-control-sm" type="text" id="inputBusquedaFabricacion" name="search" placeholder="Buscar Por Orden De Fabricación..." style="flex: 1 1 40%;">
                    <button class="btn btn-outline-primary" type="button" id="buscarFabricacion" style="flex: 0 1 auto;">Buscar</button>
                </div>
            </form>
            <div style="margin-top: 20px;"></div>
            <table class="table table-striped table-sm fs--1 mb-0">
                <thead class="bg-info">
                    <tr>
                        <th class="sort border-top" data-sort="fabricacion">Orden De Fabricación</th>
                        <th class="sort border-top" data-sort="partidas">Articulo</th>
                        <th class="sort border-top" data-sort="partidas">Descripcion</th>
                        <th class="sort border-top" data-sort="partidas">Cantidad Total</th>
                        <th class="sort border-top" data-sort="estatus">Estatus</th>
                    </tr>
                </thead>
                <tbody id="tabla-resultadosFabricacion">  
                </tbody>
            </table>
        </div>
        <!--modal principal-->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detalles de Venta</h5>
                        <button type="button" class="btn p-1" data-bs-dismiss="modal" aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs--1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                                <path fill="currentColor" d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Barra de progreso -->
                        <div class="progress">
                            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        <!-- Lista de etapas -->
                        <ul class="progress-bar-stages">
                            <li class="stage" id="progress-tab1">
                                <div class="stage-circle">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <span>1. Planeación</span>
                            </li>
                            <li class="stage" id="progress-tab2">
                                <div class="stage-circle">
                                    <i class="fas fa-cut"></i>
                                </div>
                                <span>2. Corte</span>
                            </li>
                            <li class="stage" id="progress-tab3">
                                <div class="stage-circle">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <span>3. Suministro</span>
                            </li>
                            <li class="stage" id="progress-tab4">
                                <div class="stage-circle">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <span>4. Preparado</span>
                            </li>
                            <li class="stage" id="progress-tab5">
                                <div class="stage-circle">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <span>5. Ensamble</span>
                            </li>
                            <li class="stage" id="progress-tab6">
                                <div class="stage-circle">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <span>6. Pulido</span>
                            </li>
                            <li class="stage" id="progress-tab7">
                                <div class="stage-circle">
                                    <i class="fas fa-ruler"></i>
                                </div>
                                <span>7. Medición</span>
                            </li>
                            <li class="stage" id="progress-tab8">
                                <div class="stage-circle">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <span>8. Visualización</span>
                            </li>
                            <li class="stage" id="progress-tab9">
                                <div class="stage-circle">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <span>9. Empaquetado</span>
                            </li>
                            
                        </ul>
                        <br> 
                        <div class="grid-container" id="canvases">
                        
                            <!-- Estación Cortes -->
                            <div class="grid-item">
                              <h1 class="small-title">Estación Cortes</h1>
                              <canvas id="corte" width="300" height="300"></canvas>
                            </div>
                        
                            <!-- Estación Suministros -->
                            <div class="grid-item">
                              <h1 class="small-title">Estación Suministros</h1>
                              <canvas id="suministro" width="300" height="300"></canvas>
                            </div>
                            
                        
                            <!-- Estación Preparado -->
                            <div class="grid-item">
                              <h1 class="small-title">Estación Preparado</h1>
                              <canvas id="preparado" width="300" height="300"></canvas>
                            </div>
                        
                            <!-- Estación Ensamble -->
                            <div class="grid-item">
                              <h1 class="small-title">Estación Ensamble</h1>
                              <canvas id="ensamble" width="300" height="300"></canvas>
                            </div>
                        
                            <!-- Estación Pulido -->
                            <div class="grid-item">
                              <h1 class="small-title">Estación Pulido</h1>
                              <canvas id="pulido" width="300" height="300"></canvas>
                            </div>
                        
                            <!-- Estación Medición -->
                            <div class="grid-item">
                              <h1 class="small-title">Estación Medición</h1>
                              <canvas id="medicion" width="300" height="300"></canvas>
                            </div>
                        
                            <!-- Estación Visualización -->
                            <div class="grid-item">
                              <h1 class="small-title">Estación Visualización</h1>
                              <canvas id="visualizacion" width="300" height="300"></canvas>
                            </div>
                        
                            <!-- Estación Empaque -->
                            <div class="grid-item">
                              <h1 class="small-title">Estación Empaque</h1>
                              <canvas id="empaque" width="300" height="300"></canvas>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="idVenta" value="">    
    </div>
@endsection

@section('scripts')
    <!-- Scripts -->
    <script src="vendors/echarts/echarts.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
    // Función para alternar entre las tablas de Venta y Fabricación
    function toggleTable() {
        var radioVenta = document.getElementById("flexRadioDefault1");
        var radioFabricacion = document.getElementById("flexRadioDefault2");

        if (radioVenta.checked) {
            document.getElementById("tablaVenta").style.display = "block";
            document.getElementById("tablaFabricacion").style.display = "none";
            cargarDatosVenta();  
        } else if (radioFabricacion.checked) {
            document.getElementById("tablaVenta").style.display = "none";
            document.getElementById("tablaFabricacion").style.display = "block";
            cargarDatosFabricacion();  // Cargar los datos de la tabla de fabricación
        }
    }

    // Cargar datos de la tabla de orden de venta
    function cargarDatosVenta() {
        var search = $('input[name="search"]').val();
        $.ajax({
            url: '{{ route("Buscar.Venta") }}',
            method: 'GET',
            data: { search: search },
            success: function (data) {
                console.log(data);  

                var tbody = $('#tabla-resultadosVenta');
                tbody.empty();

                if (data.length > 0) {
                    data.forEach(function (item) {
                        console.log(item);  
                        var row = `
                            <tr>
                                <td>${item.OrdenVenta}</td>
                                <td>${item.NombreCliente}</td>
                                <td class="text-center align-middle">
                                    <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" 
                                        data-id="${item.id}"
                                        data-ordenventa="${item.OrdenVenta}"
                                        data-nombrecliente="${item.NombreCliente}">
                                        <i class="bi bi-eye"></i> Detalles
                                    </a>
                                </td>
                            </tr>`;
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="5">No se encontraron resultados</td></tr>');
                }


            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error al cargar los datos de la Orden de Venta. Estado: ' + textStatus + ', Error: ' + errorThrown);
            }
        });
    }

    //detalles de la princip
    $(document).on('click', '.ver-detalles', function (e) {
        var ordenVenta = $(this).data('ordenventa');
    
        $('.progress-bar').css('width', '0%').text('0%');
        $('.progress-bar-stages .stage').removeClass('pending active completed').addClass('pending');

        $.ajax({
            url: '{{ route("Buscar.Venta.Detalle") }}',
            type: 'GET',
            data: { 
                id: ordenVenta, 
            
            },
            success: function (response) {
                if (response.partidasAreas) {
                    if (response.partidasAreas !== null) {
                        var totalEtapas = $('.progress-bar-stages .stage').length;
                        var etapasCompletadas = 0;

                        response.partidasAreas.forEach(function (partida) {
                            var estadoId = {
                                'planeacion': '#stage1',
                                'corte': '#stage2',
                                'suministro': '#stage3',
                                'preparado': '#stage4',
                                'ensamble': '#stage5',
                                'pulido': '#stage6',
                                'medicion': '#stage7',
                                'visualizacion': '#stage8',
                                'Abierto': '#stage9',
                            }[partida.Estado];

                            if (estadoId) {
                                $(estadoId).removeClass('pending').addClass('completed');
                                etapasCompletadas++;
                            }
                        });

                        var estadoActivo = response.partidasAreas.find(partida => partida.EstadoActual);
                        if (estadoActivo) {
                            var activoId = {
                                'planeacion': '#stage1',
                                'corte': '#stage2',
                                'suministro': '#stage3',
                                'preparado': '#stage4',
                                'ensamble': '#stage5',
                                'pulido': '#stage6',
                                'medicion': '#stage7',
                                'visualizacion': '#stage8',
                                'Abierto': '#stage9',
                            }[estadoActivo.Estado];

                            if (activoId) {
                                $(activoId).removeClass('pending completed').addClass('active');
                            }
                        }

                        var porcentaje = Math.round((etapasCompletadas / totalEtapas) * 100);
                        $('#progressBar').css('width', porcentaje + '%').text(porcentaje + '%');
                    } else {
                        $('#progressBar').css('width', '0%').text('0%');
                        $('.progress-bar-stages .stage').removeClass('pending active completed').addClass('pending');
                    }
                } else {

                    $('#progressBar').css('width', '0%').text('0%');
                    $('.progress-bar-stages .stage').removeClass('pending active completed').addClass('pending');
                }

                $('#exampleModal').modal('show');
            },
            error: function () {
                alert('Error al obtener los datos de la venta.');
            }
        });
        const endpoints = [
            {
                tipo: 'cortes',
                id: 'corte',
            },
            {
                tipo: 'suministros',
                id: 'suministro',
            },
            {
                tipo: 'preparado',
                id: 'preparado',
            },
            {
                tipo: 'ensamble',
                id: 'ensamble',

            },
            {
                tipo: 'pulido',
                id: 'pulido',
            },
            {
                tipo:'medicion',
                id: 'medicion',
            },
            {
                tipo: 'visualizacion',
                id: 'visualizacion',
            },
            {
                tipo: 'empaque',
                id: 'empaque',
            },

        ];

        endpoints.forEach(endpoint => {
            $.ajax({
                url: '{{ route("graficador") }}', 
                type: 'GET',
                data: { 
                    id: ordenVenta, 
                    tipo: endpoint.tipo, 
                },
                success: function (response) {
                    if (response.length > 0) {
                        const firstOrder = response[0];
                        drawGauge(endpoint.id, firstOrder.Progreso, `Orden: ${firstOrder.OrdenVenta}`);
                    } else {
                        console.log('No hay datos para mostrar.');
                        drawGauge(endpoint.id, 0, 'Sin Datos');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(`Error al obtener los datos de ${endpoint.tipo}:`, error);
                }
            });
        });


    });

    //funcion para cargar los canvases
    function drawGauge(canvasId, value, label) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');

        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = 120;
        const startAngle = Math.PI;
        const endAngle = 2 * Math.PI;

        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw background arc
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.lineWidth = 20;
        ctx.strokeStyle = '#e0e0e0'; // Color del fondo (gris claro)
        ctx.stroke();

        // Draw foreground arc (el progreso)
        ctx.beginPath();
        const valueAngle = startAngle + (value / 100) * (endAngle - startAngle);
        ctx.arc(centerX, centerY, radius, startAngle, valueAngle);
        ctx.strokeStyle = '#3b82f6'; // Color del progreso (puedes cambiarlo)
        ctx.stroke();

        // Draw center text
        ctx.font = '24px Arial';
        ctx.fillStyle = '#000'; // Color del texto
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(`${value}%`, centerX, centerY - 20); // Muestra el valor en el centro

        // Draw label below
        ctx.font = '16px Arial';
        ctx.fillStyle = '#555'; // Color del texto de la etiqueta
        ctx.fillText(label, centerX, centerY + 40); // Muestra la etiqueta debajo
    }

    // Cargar datos de la tabla de orden de fabricación

    function cargarDatosFabricacion() {
        var search = $('#inputBusquedaFabricacion').val(); 

        $.ajax({
            url: '{{ route("Buscar.Fabricacion") }}', 
            method: 'GET',
            data: { search: search }, 
            success: function (data) {
                var tbody = $('#tabla-resultadosFabricacion');
                tbody.empty(); 

                if (data.length > 0) {
                    data.forEach(function (item) {
                        var row = `
                            <tr>
                                <td>${item.OrdenFabricacion}</td>
                                <td>${item.Articulo}</td>
                                <td>${item.Descripcion}</td>
                                <td>${item.CantidadTotal}</td>
                            <td>
                                    <div class="progress" style="height: 25px; background-color: #f2f2f2; border-radius: 5px; overflow: hidden;">
                                        <div 
                                            class="progress-bar progress-bar-striped progress-bar-animated" 
                                            role="progressbar" 
                                            aria-valuenow="${item.progreso}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100" 
                                            style="width: ${item.progreso}%; font-weight: bold; font-size: 14px; line-height: 25px; color: #fff;
                                                background-color: ${item.progreso < 20 ? '#f44336' : item.progreso < 50 ? '#ff9800' : item.progreso < 70 ? '#ffeb3b' : '#4caf50'};">
                                            ${Math.round(item.progreso)}%
                                        </div>
                                    </div>
                                </td>
                                

                            </tr>`;
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="4">No se encontraron resultados</td></tr>');
                }
            },
            error: function () {
                alert('Error al cargar los datos de la Orden de Fabricación');
            }
        });
    }

    // Filtrar tabla según lo que se escribe en el buscador
    function filtrarTabla(tipo) {
        var input, filter, table, tr, td, i, txtValue;
        if (tipo === 'venta') {
            input = document.getElementById("buscadorVenta");
            table = document.getElementById("tabla-resultadosVenta");
        } else {
            input = document.getElementById("buscadorFabricacion");
            table = document.getElementById("tabla-resultadosFabricacion");
        }

        filter = input.value.toUpperCase();
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td");
            let match = false;
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        match = true;
                        break;
                    }
                }
            }
            if (match) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }

    // Cargar los datos iniciales al cargar la página
    $(document).ready(function () {
        cargarDatosVenta();  

        $('#buscarVenta').on('click', function () {
            cargarDatosVenta();
        });

        
        $('input[name="search"]').on('keypress', function (e) {
            if (e.keyCode === 13) { 
                e.preventDefault();
                cargarDatosVenta();
            }
        });

        
        $('#buscarFabricacion').on('click', function () {
            cargarDatosFabricacion();
        });

        
        $('#inputBusquedaFabricacion').on('keypress', function (e) {
            if (e.keyCode === 13) { 
                e.preventDefault();
                cargarDatosFabricacion();
            }
        });
    });

</script>
@endsection

