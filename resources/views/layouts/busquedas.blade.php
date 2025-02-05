@extends('layouts.menu2')

@section('title', 'Busquedas')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--estilos-->
    <style>
        /*estilo de boton detalles*/
        .ver-detalles:hover {
            background-color: #0c705f; /
            transform: translateY(-2px) ; 
        }

        /*estilo de barrade progreso*/
        .progress-bar-stages {
                list-style: none;
                display: flex;
                justify-content: space-between;
                padding: 0;
                margin: 0;
            }

            .stage {
                text-align: center;
                cursor: pointer;
                flex: 1;
            }

            .stage-circle {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background-color: #a0d2e9;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 18px;
                margin: 0 auto;
            }

            .stage span {
                display: block;
                margin-top: 5px;
            }

            #progress-wrapper {
                padding: 10px;
                background-color: #f4f4f4;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .task-progress-bar {
                margin-bottom: 15px;
                padding: 5px;
                background-color: #fff;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .task-label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
                color: #333;
            }

            .task-progress {
                height: 20px;
                border-radius: 5px;
                background-color: #dfe4ea;
                text-align: center;
                color: white;
                line-height: 20px;
                font-weight: bold;
                transition: width 2s ease-in-out;
            }

            .wizard-buttons {
                text-align: center;
                margin-top: 20px;
            }

            .btn {
                padding: 10px 20px;
                border: none;
                cursor: pointer;
            }

            .btn-secondary {
                background-color: #6c757d;
                color: white;
            }

            .btn-primary {
                background-color: #007bff;
                color: white;
        }
        /* Estilos de los botones */
        .btn-primary, .btn-secondary {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 25px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    
        /* Barra de progreso */
        .task-progress-bar {
                margin-bottom: 15px; /* Mayor espacio entre barras */
                position: relative;
                background-color: #f1f1f1;
                border-radius: 10px;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
                padding: 8px; /* Menos padding */
                font-size: 12px; /* Reducir tamaño de la fuente en el contenedor */
            }

            /* Etiqueta de la tarea (orden) */
            .task-label {
                font-weight: normal; /* Reducir peso de la fuente */
                text-align: center;
                margin-bottom: 5px; /* Reducir margen debajo de la etiqueta */
            }

            /* Barra de progreso */
            .task-progress {
                height: 20px; /* Reducir altura de la barra */
                border-radius: 10px;
                position: relative;
                overflow: hidden;
                display: flex;
                align-items: center; /* Centrar el texto dentro de la barra */
                justify-content: center;
            }

            /* Texto que muestra el porcentaje */
            .progress-text {
                color: rgb(0, 0, 0);
                font-size: 12px; /* Reducir tamaño de texto */
                font-weight: normal; /* Reducir peso de la fuente */
            }

            /* Ajustar la apariencia de la barra cuando se pasa el progreso */
            .task-progress-bar .task-progress {
                font-size: 10px; /* Reducir tamaño de texto en la barra */
            }

            .task-progress-bar .task-progress span {
                font-size: 10px; /* Reducir aún más el tamaño del porcentaje */
        }

        /* Clase para resaltar solo el ícono y el texto */
        .selected-stage .stage-circle,
            .selected-stage span {
                background-color:#299ae6 ; 
                color: white; 
            }

            
            .selected-stage .stage-circle,
            .selected-stage span {
                background-color: #299ae6; 
                color: white;
        }

        /* Estilos generales */
        .grid-container {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr); 
                    gap: 0px; 
                    width: 100%;
                    max-width: 100%;
                    margin: 0 auto; 
                    padding: 0; 
                }

                .grid-item {
                    text-align: center;
                    padding: 10px;
                    border-radius: 8px; 
                    background-color: #f9f9f9; 
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    margin-bottom: 0; 
                }

                .grid-item:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); 
                }

                .small-title {
                    font-size: 18px; 
                    margin-bottom: 10px; 
                    font-weight: 600; 
                    color: #444; 
                    letter-spacing: 0.5px; 
                }

                canvas {
                    user-select: none;
                    -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
                    border: 2px solid #17a2b8; 
                    border-radius: 8px; 
                    width: 100%;
                    max-width: 250px;
                    height: 150px;
                    margin: 0 auto; 
                    padding: 0;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                    background-color: #fff; 
                    display: block; 
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
        <!--click orden venta-->
        <div class="card-body p-3" style="height: 85px; width: 20%; background-color: #fdfdfde7; border-radius: 8px; box-shadow: 0 2px 5px rgba(223, 223, 223, 0.1); color: white;">
            <!-- contenido -->
            <div class="form-check">
                <input class="form-check-input" id="flexRadioDefault1" type="radio" name="flexRadioDefault" checked onclick="toggleTable()">
                <label class="form-check-label text-black" for="flexRadioDefault1">Orden De Venta</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="flexRadioDefault2" type="radio" name="flexRadioDefault" onclick="toggleTable()">
                <label class="form-check-label text-black" for="flexRadioDefault2">Orden De Fabricación</label>
            </div>
        </div>
        <!-- Tabla 1: Orden de Venta -->
        <div id="tablaVenta" style="display:block;">
            <div class="card border border-light mx-auto" style="max-width: 420px; border-radius: 40px; box-shadow: 0 1px 1px rgba(23, 60, 182, 0.1); height: 50px; width: 44%;">
                <div class="card-body p-1 d-flex align-items-center" style="height: 100%;">
                    <form id="form-buscar-venta" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                            <input class="form-control search-input search form-control-sm" type="text" name="search" placeholder="Buscar Por Orden De Venta..." style="flex: 1; border-radius: 20px; padding: 10px;">
                            <button class="btn btn-outline-primary ml-2" type="button" id="buscarVenta" style="border-radius: 20px;">
                                <i class="uil uil-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div style="margin-top: 20px;"></div>
            <div class="table-responsive">
                <div class="card-body p-3" style="height: 100%; width: 100%; background-color: #fdfdfde7; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); color: white;">
                    <table class="table table-sm fs--1 mb-0">
                    <thead>
                        <tr class="bg-info text-white">
                            <th class="sort border-top ps-3" data-sort="venta">Orden De Venta</th>
                            <th class="sort border-top" data-sort="fabricacion">Nombre Cliente</th>
                            <th class="sort border-top text-center pe-3" data-sort="total">Detalles</th>
                        </tr>
                        </thead>
                        <tbody class="list" id="tabla-resultadosVenta">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Tabla 2: Orden de Fabricación -->
        <div id="tablaFabricacion" style="display:none;">
            <div class="card border border-light mx-auto" style="max-width: 420px; border-radius: 40px; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1); height: 50px; width: 44%;">
                <div class="card-body p-3 d-flex align-items-center" style="height: 100%;">
                    <form id="form-buscar-fabricacion" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                            <input class="form-control search-input search form-control-sm" type="text" id="inputBusquedaFabricacion" name="search" placeholder="Buscar Por Orden De Fabricación..." style="flex: 1; border-radius: 20px; padding: 10px;">
                            <button class="btn btn-outline-primary ml-2" type="button" id="buscarFabricacion" style="border-radius: 20px;">
                                <i class="uil uil-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div style="margin-top: 20px;"></div>
            <div class="card-body p-3" style="height: 100%; width: 100%; background-color: #fdfdfde7; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); color: white;">
                <table class="table table-sm fs--1 mb-0">
                    <thead>
                        <tr class="bg-info text-white">
                            <th class="sort border-top" data-sort="fabricacion">Orden De Fabricación</th>
                            <th class="sort border-top" data-sort="partidas">Artículo</th>
                            <th class="sort border-top" data-sort="partidas">Descripción</th>
                            <th class="sort border-top" data-sort="partidas">Cantidad Total</th>
                            
                            <th class="sort border-top" data-sort="estatus">Detalles</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-resultadosFabricacion">
                    </tbody>
                </table>
            </div>
        </div>
        <!--modal fabricacion-->
        <div class="modal fade" id="example2Modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-info" id="exampleModalLabel">
                            Detalles De Orden Fabricacion:
                            <span id="ordenFabricacionNumero" class="ms-3 text-muted"></span>
                        </h5>
                        
                        <button type="button" class="btn p-1" data-bs-dismiss="modal" aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs--1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                                <path fill="currentColor" d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        
                        <!-- Barra de progreso -->
                        <div class="progress" style="height: 22px; border-radius: 10px; box-shadow: 0px 3px 6px rgba(0,0,0,0.2); overflow: hidden; width: 90%; margin-left: 5%;">
                            <div id="plemasProgressBar" class="progress-bar text-white fw-bold progress-animated" role="progressbar" 
                                 style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;" 
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0%
                            </div>
                        </div>
                        <br>
                        <div class="grid-container" id="plemasCanvases">
                            <!-- Estación Cortes -->
                            <div class="grid-item">
                                <h1 class="small-title">Estación Cortes</h1>
                                <canvas id="plemasCorte" width="300" height="300"></canvas>
                            </div>
        
                            <!-- Estación Suministros -->
                            <div class="grid-item">
                                <h1 class="small-title">Estación Suministros</h1>
                                <canvas id="plemasSuministro" width="300" height="300"></canvas>
                            </div>
        
                            <!-- Estación Preparado -->
                            <div class="grid-item">
                                <h1 class="small-title">Estación Preparado</h1>
                                <canvas id="plemasPreparado" width="300" height="300"></canvas>
                            </div>
        
                            <!-- Estación Ensamble -->
                            <div class="grid-item">
                                <h1 class="small-title">Estación Ensamble</h1>
                                <canvas id="plemasEnsamble" width="300" height="300"></canvas>
                            </div>
        
                            <!-- Estación Pulido -->
                            <div class="grid-item">
                                <h1 class="small-title">Estación Pulido</h1>
                                <canvas id="plemasPulido" width="300" height="300"></canvas>
                            </div>
        
                            <!-- Estación Medición -->
                            <div class="grid-item">
                                <h1 class="small-title">Estación Medición</h1>
                                <canvas id="plemasMedicion" width="300" height="300"></canvas>
                            </div>
        
                            <!-- Estación Visualización -->
                            <div class="grid-item">
                                <h1 class="small-title">Estación Visualización</h1>
                                <canvas id="plemasVisualizacion" width="300" height="300"></canvas>
                            </div>
        
                            <!-- Estación Empaque -->
                            <div class="grid-item">
                                <h1 class="small-title">Estación Empaque</h1>
                                <canvas id="plemasEmpaque" width="300" height="300"></canvas>
                            </div>
                        </div>
        
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!--modal principal-->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-info" id="exampleModalLabel">
                            Detalles De Orden Venta:
                            <span id="ordenVentaNumero" class="ms-3 text-muted"></span>
                        </h5>
                        
                        
                        <button type="button" class="btn p-1" data-bs-dismiss="modal" aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs--1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                                <path fill="currentColor" d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Barra de progreso -->
                        <div class="progress" style="height: 20px;">
                            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        <br>
                        <!-- Lista de etapas -->
                        <ul class="progress-bar-stages">
                            <li class="stage" id="stage1">
                                <div class="stage-circle">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <span>1. Planeación</span>
                            </li>
                        
                            <li class="stage" id="stage2" >
                                <div class="stage-circle">
                                    <i class="fas fa-cut"></i>
                                </div>
                                <span>2. Corte</span>
                                
                                <!-- Contenedor de progreso que estará dentro del <li> -->
                                <div id="progress-wrapper-2" class="collapse" style="margin-top: 10px;">
                                    <!-- Las barras de progreso se generarán dinámicamente aquí -->
                                </div>
                            </li>
                        
                            <li class="stage" id="stage3" >
                                <div class="stage-circle">
                                    <i class="fas fa-cut"></i>
                                </div>
                                <span>3. Suministro</span>
                                
                                <!-- Contenedor de progreso que estará dentro del <li> -->
                                <div id="progress-wrapper-3" class="collapse" style="margin-top: 10px;">
                                    <!-- Las barras de progreso se generarán dinámicamente aquí -->
                                </div>
                            </li>
                        
                            <li class="stage" id="stage4">
                                <div class="stage-circle">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <span>4. Preparado</span>
                                <div id="progress-wrapper-4" class="collapse" style="margin-top: 10px;">
                                    <!-- Las barras de progreso se generarán dinámicamente aquí -->
                                </div>
                            </li>
                        
                            <li class="stage" id="stage5">
                                <div class="stage-circle">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <span>5. Ensamble</span>
                                <div id="progress-wrapper-5" class="collapse" style="margin-top: 10px;">
                                    <!-- Las barras de progreso se generarán dinámicamente aquí -->
                                </div>
                            </li>
                        
                            <li class="stage" id="stage6">
                                <div class="stage-circle">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <span>6. Pulido</span>
                                <div id="progress-wrapper-6" class="collapse" style="margin-top: 10px;">
                                    <!-- Las barras de progreso se generarán dinámicamente aquí -->
                                </div>
                            </li>
                        
                            <li class="stage" id="stage7">
                                <div class="stage-circle">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <span>7. Medición</span>
                                <div id="progress-wrapper-7" class="collapse" style="margin-top: 10px;">
                                    <!-- Las barras de progreso se generarán dinámicamente aquí -->
                                </div>
                            </li>
                        
                            <li class="stage" id="stage8">
                                <div class="stage-circle">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <span>8. Visualización</span>
                                <div id="progress-wrapper-8" class="collapse" style="margin-top: 10px;">
                                    <!-- Las barras de progreso se generarán dinámicamente aquí -->
                                </div>
                            </li>
                        
                            <li class="stage" id="stage9">
                                <div class="stage-circle">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <span>9. Empaque</span>
                                <div id="progress-wrapper-9" class="collapse" style="margin-top: 10px;">
                                    <!-- Las barras de progreso se generarán dinámicamente aquí -->
                                </div>
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
                        <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--modal fabricacion-->
        <input type="hidden" id="idVenta" value="">
        <input type="hidden" id="idFabricacion" value="">     
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

    // Ocultar ambas tablas por defecto
    document.getElementById("tablaVenta").style.display = "none";
    document.getElementById("tablaFabricacion").style.display = "none";

    // Mostrar la tabla correspondiente según el radio seleccionado
    if (radioVenta.checked) {
        document.getElementById("tablaVenta").style.display = "block";
        // Limpiar y mostrar el formulario de búsqueda
        document.getElementById("inputBusquedaVenta").value = '';
        document.getElementById("tabla-resultadosVenta").innerHTML = '';
    } else if (radioFabricacion.checked) {
        document.getElementById("tablaFabricacion").style.display = "block";
        // Limpiar y mostrar el formulario de búsqueda
        document.getElementById("inputBusquedaFabricacion").value = '';
        document.getElementById("tabla-resultadosFabricacion").innerHTML = '';
    }
    }
    // Cargar datos de la tabla de orden de venta
    $('#buscarVenta').on('click', function () {
        var search = $('input[name="search"]').val().trim();

        if (search === '') {
            return;  // No hace nada si el término de búsqueda está vacío
        }

        cargarDatosVenta(search);
    });
    //cargar los datode venta
    function cargarDatosVenta(search) {
        
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
                                    <a href="#" class="btn btn-info btn-sm ver-detalles" 
                                    data-id="${item.id}"
                                    data-ordenventa="${item.OrdenVenta}"
                                    data-nombrecliente="${item.NombreCliente}"
                                    style="border-radius: 3px; padding: 4px 8px; font-size: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); transition: background-color 0.3s, transform 0.2s;">
                                        <i class="bi bi-eye uil-comment-info"></i> Detalles
                                    </a>
                                </td>
                            </tr>`;
                            
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center">No se encontraron resultados</td></tr>');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error al cargar los datos de la Orden de Venta. Estado: ' + textStatus + ', Error: ' + errorThrown);
            }
        });
    }
    //detalles de la orden venta
    $(document).on('click', '.ver-detalles', function (e) {
        var ordenVenta = $(this).data('ordenventa');
        
        // Muestra el valor en el elemento
        $('#stage2').data('ordenventa', ordenVenta);
        $('#stage3').data('ordenventa', ordenVenta);
        $('#stage4').data('ordenventa', ordenVenta);
        $('#stage5').data('ordenventa', ordenVenta);
        $('#stage6').data('ordenventa', ordenVenta);
        $('#stage7').data('ordenventa', ordenVenta);
        $('#stage8').data('ordenventa', ordenVenta);
        $('#stage9').data('ordenventa', ordenVenta);

        $('.progress-bar').css('width', '0%').text('0%');
        $('.progress-bar-stages .stage').removeClass('pending active completed no-data').addClass('pending');

        $.ajax({
            url: '{{ route("Buscar.Venta.Detalle") }}',
            type: 'GET',
            data: { id: ordenVenta },
            success: function (response) {
                if (response.partidasAreas) {
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
                            $(estadoId).removeClass('pending active no-data').addClass('completed');
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

                    // Actualizar el porcentaje de progreso
                    var porcentaje = Math.round((etapasCompletadas / totalEtapas) * 100);
                    $('#progressBar').css('width', porcentaje + '%').text(porcentaje + '%');

                    // Actualiza el número de la orden en el modal
                    $('#ordenVentaNumero').removeClass('text-muted').addClass('text-info').text(ordenVenta);
                } else {
                    // Si no hay datos, aplica el estado "no-data"
                    $('.progress-bar-stages .stage').addClass('no-data');
                    $('#progressBar').css('width', '0%').text('0%');
                    // También puedes actualizar el número de orden en este caso
                    $('#ordenVentaNumero').removeClass('text-muted').addClass('text-info').text(ordenVenta);
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
                        const progreso = Math.min(firstOrder.Progreso, 100); // Limita a 100%
                        drawGauge(endpoint.id, progreso, '');
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

    // Cargar datos de la tabla de orden de fabricación
    $('#buscarFabricacion').on('click', function () {
        var search = $('#inputBusquedaFabricacion').val().trim();

        if (search === '') {
            return;  
        }

            cargarDatosFabricacion(search);
    });

    //cargar los datos de fabricacion
    function cargarDatosFabricacion(search) {
        $.ajax({
            url: '{{ route("Buscar.Fabricacion") }}', 
            method: 'GET',
            data: { search: search }, 
            success: function (data) {
                var tablaFabricacion = $('#tablaFabricacion');
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
                                
                                <td class="text-center align-middle">
                                    <a href="#" class="btn btn-info btn-sm ver-fabricacion" 
                                    data-id="${item.id}"
                                    data-ordenfabricacion="${item.OrdenFabricacion}"
                                    data-descripcion="${item.Descripcion}"
                                    data-cantidadtotal="${item.CantidadTotal}"
                                    style="border-radius: 3px; padding: 4px 8px; font-size: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); transition: background-color 0.3s, transform 0.2s;">
                                        <i class="bi bi-eye uil-comment-info"></i> Detalles
                                    </a>
                                </td>
                            </tr>`;
                        tbody.append(row);
                    });

                    tablaFabricacion.show(); // Mostrar la tabla si hay resultados
                } else {
                    tbody.append('<tr><td colspan="5" class="text-center">No se encontraron resultados</td></tr>');
                    tablaFabricacion.show(); // Mostrar la tabla aunque esté vacía con el mensaje
                }
            },
            error: function () {
                alert('Error al cargar los datos de la Orden de Fabricación.');
            }
        });
    }

    //detalles de la orden de fabricacion
    $(document).on('click', '.ver-fabricacion', function (e) {
        var ordenfabricacion = $(this).data('ordenfabricacion');
        console.log(ordenfabricacion);  // Para depuración

        $.ajax({
            url: '{{ route("Detalles.Fabricacion") }}',
            type: 'GET',
            data: { id: ordenfabricacion },
            success: function (response) {
                var progressBar = $('#plemasProgressBar');

                if (response.progreso !== undefined) {
                    var progreso = response.progreso;

                    // Actualizar la barra de progreso con animación
                    progressBar.css('width', progreso + '%').text(progreso + '%');

                    // Quitar clases de color antes de agregar la nueva
                    progressBar.removeClass('bg-danger bg-warning bg-info bg-success bg-primary');

                    // Asignar color según el porcentaje
                    if (progreso >= 0 && progreso < 20) {
                        progressBar.addClass('bg-danger');  // Rojo
                    } else if (progreso >= 20 && progreso < 40) {
                        progressBar.addClass('bg-warning');  // Naranja
                    } else if (progreso >= 40 && progreso < 70) {
                        progressBar.addClass('bg-primary');  // Azul para una transición más agradable
                    } else if (progreso >= 70 && progreso < 90) {
                        progressBar.addClass('bg-info');  // Celeste/Azul claro
                    } else {
                        progressBar.addClass('bg-success');  // Verde
                    }

                    // Actualizar el número de orden de fabricación
                    $('#ordenFabricacionNumero').removeClass('text-muted').addClass('text-info').text(ordenfabricacion);
                } else {
                    // Reiniciar la barra si no hay datos
                    progressBar.css('width', '0%').text('0%').removeClass('bg-danger bg-warning bg-info bg-success bg-primary');

                    // Actualizar el número de orden de fabricación con la clase 'text-muted'
                    $('#ordenFabricacionNumero').removeClass('text-info').addClass('text-muted').text(ordenfabricacion);
                }

                // Mostrar el modal
                $('#example2Modal').modal('show');
            },
            error: function () {
                alert('Error al obtener los datos de la fabricación.');
            }
        });


        const endpoints = [
            {
                tipo: 'plemasCorte',
                id: 'plemasCorte',
            },
            {
                tipo: 'plemasSuministro',
                id: 'plemasSuministro',
            },
            {
                tipo: 'plemasPreparado',
                id: 'plemasPreparado',
            },
            {
                tipo: 'plemasEnsamble',
                id: 'plemasEnsamble',

            },
            {
                tipo: 'plemasPulido',
                id: 'plemasPulido',
            },
            {
                tipo:'plemasMedicion',
                id: 'plemasMedicion',
            },
            {
                tipo: 'plemasVisualizacion',
                id: 'plemasVisualizacion',
            },
            {
                tipo: 'plemasEmpaque',
                id: 'plemasEmpaque',
            },

        ];
        endpoints.forEach(endpoint => {
            $.ajax({
                url: '{{ route("graficadoOF") }}',
                type: 'GET',
                data: { 
                    id: ordenfabricacion, 
                    tipo: endpoint.tipo, 
                },
                success: function (response) {
                    if (response.length > 0) {
                        const firstOrder = response[0];
                        let cantidadTotal = firstOrder.CantidadTotal;
                        let totalPartidas = firstOrder.TotalPartidas;
                        let retrabajo = 0;
                        let label = '';

                        if (totalPartidas > cantidadTotal) {
                            retrabajo = totalPartidas - cantidadTotal;
                            totalPartidas = cantidadTotal; 
                            label = `Retrabajo: ${retrabajo}`;
                        }

                        let progreso = Math.round((totalPartidas / cantidadTotal) * 100);
                        drawGauge(endpoint.id, progreso, label);
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

    //funcion para cargar los canvases general para Or-V Y Or-F
    function drawGauge(canvasId, value, label) {
        const canvas = document.getElementById(canvasId);
        canvas.style.webkitTapHighlightColor = 'rgba(0, 0, 0, 0)'; // Desactivar el resaltado táctil

        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = 85;
        const startAngle = Math.PI;
        const endAngle = 2 * Math.PI;

        // Variables para ajustar manualmente las posiciones de los números
        const offsetX = 0; // Desplazamiento horizontal de los números (0 = centrado)
        const offsetY = 30; // Desplazamiento vertical de los números (ajústalo según necesites)

        // Limpiar el lienzo
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Dibujar el arco de fondo con borde más delgado
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.lineWidth = 50;  // Ancho de línea ajustado para mejor balance
        ctx.strokeStyle = '#e0e0e0';  // Gris suave para el fondo
        ctx.lineCap = 'butt';
        ctx.stroke();

        // Determinar el color del arco según el valor
        let strokeColor;
        if (value <= 20) strokeColor = '#e74c3c'; // Rojo
        else if (value <= 50) strokeColor = '#f39c12'; // Naranja
        else if (value <= 90) strokeColor = '#f1c40f'; // Amarillo
        else strokeColor = '#15e631'; // Verde

        // Ajuste para hacer el principio y final del arco un poco cuadrado
        const valueAngle = startAngle + (value / 100) * (endAngle - startAngle);

        // Dibujar el arco del valor con color dinámico
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, valueAngle);
        ctx.strokeStyle = strokeColor;
        ctx.lineWidth = 40; // Ancho del arco ajustado para mejor visibilidad
        ctx.lineCap = 'butt'; // Ajustar el borde a cuadrado en el inicio y final
        ctx.stroke();

        // Determinar el color del texto del valor
        let valueTextColor = strokeColor; // Usar el mismo color del arco
        ctx.font = '30px Arial';  // Fuente más grande para el valor
        ctx.fillStyle = valueTextColor;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle'; // Asegura que el texto esté alineado verticalmente en el medio

        // Ajustar la posición vertical del texto para alinearlo con el arco
        // Aquí, `centerY` ajustado verticalmente para que esté alineado con el arco
        ctx.fillText(`${value}%`, centerX, centerY);

        // Etiqueta debajo del valor
        ctx.font = '18px Arial';
        ctx.fillStyle = '#17a2b8';  // Color suave para la etiqueta
        ctx.fillText(label, centerX, centerY + 40);  // Espaciado ajustado

        // Dibujar las marcas de 0 y 100 fuera del arco, ajustable manualmente
        ctx.font = '16px Arial';
        ctx.fillStyle = '#000000';  // Color para los números de la escala

        // Dibujar el "0" justo debajo del inicio del arco
        ctx.fillText('0', centerX - radius, centerY+ 20);

        // Dibujar el "100" en el final del arco
        ctx.fillText('100', centerX + radius, centerY + 20); // Ajusta la posición vertical
        //la posición
    }

    //para el clic de .stage
    $(document).ready(function() {
        $('.stage').on('click', function() {
            var stageId = $(this).attr('id');
            var progressWrapperId = '#progress-wrapper-' + stageId.replace('stage', '');
            $(progressWrapperId).collapse('toggle'); // Muestra u oculta el contenedor de progreso

            // Agregar o quitar la clase 'selected-stage' para resaltar el ícono y el texto
            $('.stage').removeClass('selected-stage');
            $(this).addClass('selected-stage');

            var ordenVenta = $(this).data('ordenventa'); // Obtiene la orden de venta asociada

            loadProgressData(ordenVenta, stageId); // Llamada unificada
        });
    });

    // Función para cargar los datos de progreso combinados
    function loadProgressData(ordenVenta, stageId) {
        $.ajax({
            url: '{{ route("graficarOR.OF") }}', // Ruta al controlador
            method: 'GET',
            data: {
                id: ordenVenta,
                stage: stageId, // Pasamos el identificador de la etapa
                _token: $('meta[name="csrf-token"]').attr('content') // Token de seguridad
            },
            success: function(response) {
                var progressWrapper = $('#progress-wrapper-' + stageId.replace('stage', ''));
                progressWrapper.empty(); // Limpiar el contenido anterior

                // Generar las barras de progreso dinámicamente
                response.forEach(function(item, index) {
                    var progressPercentage = item.Progreso;
                    var displayProgress = progressPercentage > 100 ? 100 : progressPercentage;
                    var progressBar = $('<div>', { class: 'task-progress-bar', id: 'task-progress' + (index + 1) });
                    var progressLabel = $('<div>', { class: 'task-label', text: 'Orden ' + item.OrdenesFabricacion });

                    // Lógica para asignar colores según el progreso
                    var progressColor;
                    if (displayProgress >= 0 && displayProgress <= 30) {
                        progressColor = 'red'; // Rojo
                    } else if (displayProgress > 30 && displayProgress <= 50) {
                        progressColor = 'orange'; // Naranja
                    } else if (displayProgress > 50 && displayProgress <= 90) {
                        progressColor = 'yellow'; // Amarillo
                    } else if (displayProgress > 90 && displayProgress <= 100) {
                        progressColor = '#05610a'; // Verde

                    }

                    // Crear la barra de progreso con el porcentaje
                    var progress = $('<div>', {
                        class: 'task-progress',
                        text: displayProgress + '%',
                        css: {
                            width: displayProgress + '%',
                            backgroundColor: progressColor // Aplicar el color
                        }
                    });

                    // Agregar el texto de la orden y el progreso dentro de la barra
                    var progressText = $('<div>', {
                        class: 'progress-text',
                        text: displayProgress + '%'
                    });

                    progressBar.append(progressLabel).append(progress).append(progressText);
                    progressWrapper.append(progressBar);

                    // Si el progreso es mayor a 100%, agregar una barra para el "Retrabajo"
                    if (progressPercentage > 100) {
                        var retrabajoPercentage = (progressPercentage - 100).toFixed(2);
                        var retrabajoBar = $('<div>', { class: 'task-progress-bar retrabajo', id: 'task-retrabajo' + (index + 1) });
                        var retrabajoLabel = $('<div>', { class: 'task-label', text: 'Retrabajo ' + item.OrdenesFabricacion });

                        var retrabajoProgress = $('<div>', {
                            class: 'task-progress',
                            text: retrabajoPercentage + '%',
                            css: {
                                width: retrabajoPercentage + '%',
                                backgroundColor: 'blue' // Color para retrabajo
                            }
                        });

                        var retrabajoText = $('<div>', {
                            class: 'progress-text',
                            text: retrabajoPercentage + '%'
                        });


                        retrabajoBar.append(retrabajoLabel).append(retrabajoProgress).append(retrabajoText);
                        progressWrapper.append(retrabajoBar);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    }

</script>
@endsection

