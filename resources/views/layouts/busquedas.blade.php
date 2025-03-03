@extends('layouts.menu2')
@section('title', 'Progreso')
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
               
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .task-progress-bar {
                margin-bottom: 15px;
                padding: 5px;
               
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
               
                text-align: center;
                color: rgba(255, 255, 255, 0.493);
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
                /*border: none;*/
                cursor: pointer;
            }

            .btn-secondary {
                background-color: #6c757d;
                color: rgba(255, 255, 255, 0.596);
            }

            /*.btn-primary {
                background-color: #007bff;
                color: rgba(255, 255, 255, 0.459);
            }*/
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

        /* Estilos generales */
        .grid-container {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr); 
                    gap: 10px; 
                    width: 90%;
                    max-width: 120%;
                    margin: 0 auto; 
                    padding: 0; 
                }

                .grid-item {
                    text-align: center;
                    padding: 0px;
                    border-radius: 20px; 
                    background-color: #f9f9f9a4; 
                    box-shadow: 0 4px 5px rgba(0, 0, 0, 0.1); 
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
                   /* -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
                    border: 2px solid #17a2b8; */
                    border-radius: 8px; 
                    width: 100%;
                    max-width: 250px;
                    height: 150px;
                    margin: 0 auto; 
                    padding: 0;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                    
                    display: block; 
        }



                    #collapseContent {
                display: none;
                transition: all 0.3s ease;
            }

            .toggle-icon {
                font-size: 18px;
                font-weight: bold;
            }

            #estacionesContainer {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Se ajusta automáticamente */
                grid-template-rows: repeat(4, auto); /* 4 filas */
                gap: 1px; /* Espacio entre tarjetas */
                padding: 10px;
                justify-content: center;
            }

            .estacion-card {
                border: 1px solid #dddddda9;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                padding: 15px;
                text-align: center;
               
            }

            .estacion-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
            }

            /* Estilos para los títulos y texto */
            .card-title {
                font-size: 18px;
                font-weight: bold;
                color: #333;
            }

            .card-text {
                font-size: 14px;
                color: #666;
            }

            /* Estilos para las etiquetas (badge) */
            .badge {
                font-size: 14px;
                padding: 6px 12px;
                border-radius: 20px;
                margin-right: 5px;
            }

            .badge-success {
                background-color: #1a662c;
               
            }

            .badge-warning {
                background-color: #881410;
               
            }



            .btn-custom {
                width: 120px;
                height: 40px;
                font-size: 16px;
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px; /* Espacio entre el icono y el texto */
                border-radius: 8px; /* Bordes redondeados */
                background-color: #17a2b8; /* Color info mejorado */
              
                border: none;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .btn-custom:hover {
                background-color: #138496; /* Color más oscuro en hover */
                transform: translateY(-2px); /* Efecto al pasar el mouse */
                box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
            }

            .btn-custom i {
                font-size: 18px; /* Tamaño del icono */
            }



            .canvas-container {
                margin-bottom: -20px; 
            }
            .title-container {
                font-size: 1px; /* Ajusta el tamaño del texto */
                padding: -100px; /* Ajusta el espacio alrededor */
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
        <div class="row">
            <div class="card shadow-sm border-light col-4 p-3 mb-3">
                <div class="card-header m-0 p-0">
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" id="flexRadioDefault1" type="radio" name="flexRadioDefault" checked onclick="toggleTable()">
                        <label class="form-check-label text-black" for="flexRadioDefault1">Orden de venta</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" id="flexRadioDefault2" type="radio" name="flexRadioDefault" onclick="toggleTable()">
                        <label class="form-check-label text-black" for="flexRadioDefault2">Orden de fabricaci&oacute;n</label>
                    </div>
                </div>
            </div>
            <!-- Tabla 1: Orden de Venta -->
            <div id="tablaVenta" class="card p-4" style="display:block;">
                <div class="card border border-light mx-auto" style="max-width: 420px; border-radius: 40px; height: 50px; width: 44%;">
                    <div class="card-body p-0 d-flex align-items-center" style="height: 100%;">
                        <form id="form-buscar-venta" style="width: 100%;">
                            <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                                <input class="form-control search-input search form-control-sm" type="text" name="search" placeholder="Buscar Por Orden De Venta..." style="flex: 1;border-radius: 20px; padding: 10px;">
                                <button class="btn btn-outline-primary ml-2" type="button" id="buscarVenta" style="border-radius: 20px;">
                                    <i class="uil uil-search"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div style="margin-top: 20px;"></div>
                <div class="table-responsive">
                    <div class="card" >
                        <table class="table table-sm fs--1 mb-0">
                        <thead>
                            <tr class="bg-info text-white">
                                <th class="sort border-top ps-3" data-sort="venta">Orden De Venta</th>
                                <th class="sort border-top" data-sort="fabricacion">Nombre Cliente</th>
                                <th class="sort border-top text-center pe-3" data-sort="total">Accion</th>
                            </tr>
                            </thead>
                            <tbody class="list" id="tabla-resultadosVenta">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Tabla 2: Orden de Fabricación -->
            <div id="tablaFabricacion" class="card p-4" style="display:none;">
                <div class="card border border-light mx-auto" style="max-width: 420px; border-radius: 40px; height: 50px; width: 44%;">
                    <div class="card-body p-0 d-flex align-items-center" style="height: 100%;">
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
                <div class="card">
                    <table class="table table-sm fs--1 mb-0">
                        <thead>
                            <tr class="bg-info text-white">
                                <th class="sort border-top" data-sort="fabricacion">Orden De Fabricación</th>
                                <th class="sort border-top" data-sort="partidas">Artículo</th>
                                <th class="sort border-top" data-sort="partidas">Descripción</th>
                                <th class="sort border-top" data-sort="partidas">Cantidad Total</th>
                                
                                <th class="sort border-top" data-sort="estatus">Accion</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-resultadosFabricacion">
                        </tbody>
                    </table>
                </div>
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
                         <div class="card">
                            <div class="grid-container" id="plemasCanvases">
                                <!-- Estación Cortes -->
                                <div class="card">
                                    <div class="grid-item">
                                        <h1 class="small-title">Estación Cortes</h1>

                                        <div class="canvas-container">
                                            <canvas id="plemasCorte" width="300" height="300"></canvas> 
                                        </div>
                                        
                                        <div class="title-container">
                                            <h1 class="small-title" id="titulo-cortes"></h1>
                                        </div>
                                    </div>
                                </div>
                                
                    
                                <!-- Estación Suministros -->
                                <div class="grid-item">
                                    <h1 class="small-title">Estación Suministros</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasSuministro" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-suministro"></h1>
                                    </div>
                                </div>
            
                                <!-- Estación Preparado -->
                                <div class="grid-item">
                                    <h1 class="small-title">Estación Preparado</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasPreparado" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-preparado"></h1>
                                    </div>
                                </div>
            
                                <!-- Estación Ensamble -->
                                <div class="grid-item">
                                    <h1 class="small-title">Estación Ensamble</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasEnsamble" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-ensamble"></h1>
                                    </div>
                                </div>
            
                                <!-- Estación Pulido -->
                                <div class="grid-item">
                                    <h1 class="small-title">Estación Pulido</h1>
                                    
                                    <div class="canvas-container">
                                        <canvas id="plemasPulido" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-pulido"></h1>
                                    </div>
                                </div>
            
                                <!-- Estación Medición -->
                                <div class="grid-item">
                                    <h1 class="small-title">Estación Medición</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasMedicion" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-medicion"></h1>
                                    </div>
                                </div>
            
                                <!-- Estación Visualización -->
                                <div class="grid-item">
                                    <h1 class="small-title">Estación Visualización</h1>
                                
                                    <div class="canvas-container">
                                        <canvas id="plemasVisualizacion" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-visualizacion"></h1>
                                    </div>

                                </div>
            
                                <!-- Estación Empaque -->
                                <div class="grid-item">
                                    <h1 class="small-title">Estación Empaque</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasEmpaque" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-empaque"></h1>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!--
                        <div style="height: 30px;"></div>
                        <div class="text-end">
                            <button class="btn btn-info VerMas btn-custom">
                                <i class="fa fa-clock"></i> Más
                            </button>
                        </div>--->



                        
                        
                        <!-- Contenedor colapsable -->
                        <div class="collapse mt-3" id="collapseContent">
                            <div class="card">
                                <div class="card-body">
                                    <strong></strong><br>
                                    <div id="estacionesContainer" data-ordenfabricacion="ordenfabricacion" style="display: flex; flex-wrap: wrap; gap: 20px;">
                                        
                                        <!-- Aquí se cargarán los datos dinámicamente -->
                                    </div>
                                    
                                </div>
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
                            <!--
                            <li class="stage" id="stage1">
                                <div class="stage-circle">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <span>1. Planeación</span>
                            </li>-->
                        
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
                                <td >${item.OrdenVenta}</td>
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
            { tipo: 'cortes', id: 'corte' },
            { tipo: 'suministros', id: 'suministro' },
            { tipo: 'preparado', id: 'preparado' },
            { tipo: 'ensamble', id: 'ensamble' },
            { tipo: 'pulido', id: 'pulido' },
            { tipo: 'medicion', id: 'medicion' },
            { tipo: 'visualizacion', id: 'visualizacion' },
            { tipo: 'empaque', id: 'empaque' },
        ];

        // Verifica si la variable ordenVenta está definida
        if (typeof ordenVenta === "undefined" || ordenVenta === null) {
            console.error("Error: ordenVenta no está definida.");
        } else {
            endpoints.forEach(endpoint => {
                $.ajax({
                    url: @json(route("graficador")), // Corrige la forma de obtener la URL en Blade
                    type: 'GET',
                    data: { 
                        id: ordenVenta,  
                        tipo: endpoint.tipo
                    },
                    success: function(response) {
                        console.log(`Respuesta de ${endpoint.tipo}:`, response); // Depuración
                        
                        if (response.result && response.result.length > 0) {
                            const progreso = Math.min(response.Progreso.Progreso, 100); // Acceder correctamente
                            drawGauge(endpoint.id, progreso, ''); 
                        } else {
                            console.log(`No hay datos para ${endpoint.tipo}`);
                            drawGauge(endpoint.id, 0, 'Sin Datos'); 
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(`Error al obtener los datos de ${endpoint.tipo}:`, error);
                        drawGauge(endpoint.id, 0, 'Error'); 
                    }
                });
            });
        }
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
                    { tipo: 'plemasCorte', id: 'plemasCorte' },
                    { tipo: 'plemasSuministro', id: 'plemasSuministro' },
                    { tipo: 'plemasPreparado', id: 'plemasPreparado' },
                    { tipo: 'plemasEnsamble', id: 'plemasEnsamble' },
                    { tipo: 'plemasPulido', id: 'plemasPulido' },
                    { tipo: 'plemasMedicion', id: 'plemasMedicion' },
                    { tipo: 'plemasVisualizacion', id: 'plemasVisualizacion' },
                    { tipo: 'plemasEmpaque', id: 'plemasEmpaque' },
                ];

                endpoints.forEach(endpoint => {
                    $.ajax({
                        url: '{{ route("graficadoOF") }}',  // Ruta del controlador
                        type: 'GET',
                        data: { 
                            id: ordenfabricacion,  // Asumiendo que 'ordenfabricacion' está disponible
                            tipo: endpoint.tipo,   // Enviar tipo dinámico
                        },
                        success: function(response) {
                            if (response && response.CantidadTotal !== undefined) {  
                                let cantidadTotal = response.CantidadTotal;
                                let totalPartidas = response.TotalPartidas;
                                let retrabajo = 0;
                                let progreso = 0;
                                let label = '';

                                if (totalPartidas > cantidadTotal) {
                                    // Si hay más partidas que la cantidad total, el exceso es retrabajo
                                    retrabajo = totalPartidas - cantidadTotal;
                                    totalPartidas = cantidadTotal; // Solo consideramos las primeras unidades
                                }

                                // Calcular el porcentaje con las unidades dentro del límite de cantidad total
                                progreso = (totalPartidas / cantidadTotal) * 100;
                                
                                // Redondear el porcentaje a un valor entero
                                progreso = Math.round(progreso); // O usar Math.floor() si prefieres redondear hacia abajo
                                
                                if (retrabajo > 0) {
                                    label = `Retrabajo: ${retrabajo}`;
                                }

                                drawGauge(endpoint.id, progreso, label);
                            } else {
                                console.log('No hay datos para mostrar.');
                                drawGauge(endpoint.id, 0, 'Sin Datos');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(`Error al obtener los datos de ${endpoint.tipo}:`, error);
                            drawGauge(endpoint.id, 0, 'Error');
                        }
                    });
                });



            $.ajax({
            url: '{{ route("tiempos.hrs") }}',
            method: 'GET',
            data: { 
                id: ordenfabricacion,
            },
            success: function(response) {
                // Verificar si la respuesta tiene tiempos de cortes
                if (response.tiemposcortes.length > 0) {
                    $('#titulo-cortes')
                        .text('Duración: ' + response.tiemposcortes[0].Duracion)
                        .css({
                            'font-size': '10px',
                            'color': 'black',
                            'font-weight': 'bold'
                        });
                } else {
                    $('#titulo-cortes')
                        .text('Sin datos de duración')
                        .css({
                            'font-size': '10px',
                            'color': 'red',
                            'font-weight': 'bold'
                        });
                }

                // Verificar si tiemposareas tiene datos
                let hasData = false;

                if (response.tiemposareas.length > 0) {
                    response.tiemposareas.forEach(function(item) {
                        hasData = true;
                        switch (item.Areas_id) {
                            case 3:
                                $('#titulo-suministro')
                                    .text('Duración: ' + item.DuracionTotal)
                                    .css({
                                        'font-size': '10px',
                                        'color': 'black',
                                        'font-weight': 'bold'
                                    });
                                break;
                            case 4:
                                $('#titulo-preparado')
                                    .text('Duración: ' + item.DuracionTotal)
                                    .css({
                                        'font-size': '10px',
                                        'color': 'black',
                                        'font-weight': 'bold'
                                    });
                                break;
                            case 5:
                                $('#titulo-ensamble')
                                    .text('Duración: ' + item.DuracionTotal)
                                    .css({
                                        'font-size': '10px',
                                        'color': 'black',
                                        'font-weight': 'bold'
                                    });
                                break;
                            case 6:
                                $('#titulo-pulido')
                                    .text('Duración: ' + item.DuracionTotal)
                                    .css({
                                        'font-size': '10px',
                                        'color': 'black',
                                        'font-weight': 'bold'
                                    });
                                break;
                            case 7:
                                $('#titulo-medicion')
                                    .text('Duración: ' + item.DuracionTotal)
                                    .css({
                                        'font-size': '10px',
                                        'color': 'black',
                                        'font-weight': 'bold'
                                    });
                                break;
                            case 8:
                                $('#titulo-visualizacion')
                                    .text('Duración: ' + item.DuracionTotal)
                                    .css({
                                        'font-size': '10px',
                                        'color': 'black',
                                        'font-weight': 'bold'
                                    });
                                break;
                            case 9:
                                $('#titulo-empaque')
                                    .text('Duración: ' + item.DuracionTotal)
                                    .css({
                                        'font-size': '10px',
                                        'color': 'black',
                                        'font-weight': 'bold'
                                    });
                                break;
                            default:
                                console.warn('Área no reconocida:', item.Areas_id);
                        }
                    });
                }

                if (!hasData) {
                    $('#titulo-suministro').text('Sin datos de duración').css({
                        'font-size': '10px',
                        'color': 'red',
                        'font-weight': 'bold'
                    });

                    $('#titulo-preparado').text('Sin datos de duración').css({
                        'font-size': '10px',
                        'color': 'red',
                        'font-weight': 'bold'
                    });

                    $('#titulo-ensamble').text('Sin datos de duración').css({
                        'font-size': '10px',
                        'color': 'red',
                        'font-weight': 'bold'
                    });

                    $('#titulo-pulido').text('Sin datos de duración').css({
                        'font-size': '10px',
                        'color': 'red',
                        'font-weight': 'bold'
                    });

                    $('#titulo-medicion').text('Sin datos de duración').css({
                        'font-size': '10px',
                            'color': 'red',
                            'font-weight': 'bold'
                        });

                        $('#titulo-visualizacion').text('Sin datos de duración').css({
                            'font-size': '10px',
                            'color': 'red',
                            'font-weight': 'bold'
                        });

                        $('#titulo-empaque').text('Sin datos de duración').css({
                            'font-size': '10px',
                            'color': 'red',
                            'font-weight': 'bold'
                        });
                    }
                },
                error: function() {
                    console.error('Error al obtener los datos');
                }
            });
    });

    //funcion para cargar los canvases general para Or-V Y Or-F
    function drawGauge(canvasId, value, label) {
        const canvas = document.getElementById(canvasId);
        canvas.style.webkitTapHighlightColor = 'rgba(0, 0, 0, 0)'; // Desactivar el resaltado táctil

        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = 100;
        const startAngle = Math.PI;
        const endAngle = 2 * Math.PI;

        // Variables para ajustar manualmente las posiciones de los números
        const offsetX = 0; // Desplazamiento horizontal de los números (0 = centrado)
        const offsetY = 10; // Desplazamiento vertical de los números (ajústalo según necesites)

        // Limpiar el lienzo
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Dibujar el arco de fondo con borde más delgado
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.lineWidth = 20;  // Ancho de línea ajustado para mejor balance
        ctx.strokeStyle = '#e0e0e0';  // Gris suave para el fondo
        ctx.lineCap = 'butt';
        ctx.stroke();

        // Determinar el color del arco según el valor
        let strokeColor;
        if (value <= 20) strokeColor = '#e74c3c'; // Rojo
        else if (value <= 50) strokeColor = '#f39c12'; // Naranja
        else if (value <= 90) strokeColor = '#f1c40f'; // Amarillo
        else strokeColor = '#1a662c'; // Verde

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
                        progressColor = '#1a662c'; // Verde

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

    // Cuando se haga clic en una fila para seleccionar la OrdenFabricacion
    $(document).on('click', '.ver-fabricacion', function () {
        var ordenfabricacion = $(this).data('ordenfabricacion');  // Obtener el valor de ordenfabricacion desde la fila
        console.log('Orden de fabricación seleccionada:', ordenfabricacion);  // Verifica que se obtiene el valor

        // Asignar ese valor al botón VerMas
        $('.VerMas').data('ordenfabricacion', ordenfabricacion);  // Asigna el valor al botón
        console.log('Valor asignado al botón VerMas:', $('.VerMas').data('ordenfabricacion'));  // Verifica que se asignó correctamente

        // Si deseas que el texto del botón también cambie para indicar la orden seleccionada:
       // $('.VerMas').text(` Orden ${ordenfabricacion} +`);
    });

    // Lógica cuando se hace clic en el botón VerMas
    $(document).on('click', '.VerMas', function (e) {
    var ordenfabricacion = $(this).data('ordenfabricacion');
    console.log('Orden de fabricación en el botón VerMas:', ordenfabricacion);

    if (!ordenfabricacion) {
        alert("No se ha seleccionado ninguna Orden de Fabricación.");
        return;
    }

    let content = $("#collapseContent");
    let container = $("#estacionesContainer");
    let icon = $(this).find('.toggle-icon');

    let isOpen = content.hasClass("show");

    $.ajax({
            url: '{{ route("tiempo.orden") }}',
            type: "GET",
            data: { ordenfabricacion: ordenfabricacion },
            dataType: "json",
            success: function (response) {
                console.log('Datos recibidos:', response);
                container.html("");

                response.forEach(resultado => {
                    let tiempoDuracion = "No registrado";
                    
                    if (resultado.Tiempoinicio && resultado.Tiempofin) {
                        let inicio = new Date(resultado.Tiempoinicio);
                        let fin = new Date(resultado.Tiempofin);
                        let diferencia = fin - inicio; // Diferencia en milisegundos

                        let dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
                        let horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        let minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));

                        let partes = [];
                        if (dias > 0) partes.push(`${dias} día${dias > 1 ? 's' : ''}`);
                        if (horas > 0) partes.push(`${horas} hora${horas > 1 ? 's' : ''}`);
                        if (minutos > 0) partes.push(`${minutos} minuto${minutos > 1 ? 's' : ''}`);

                        tiempoDuracion = partes.join(", ");
                    }

                    let card = `
                        <div class="card estacion-card">
                            <div class="card-body">
                                <h5 class="card-title">${resultado.fase}</h5>
                                <p class="card-text">
                                    <strong>Duración:</strong> <span class="badge ${tiempoDuracion !== "No registrado" ? 'badge-success' : 'badge-warning'}">${tiempoDuracion}</span><br>
                                </p>
                            </div>
                        </div>`;
                    container.append(card);
                });

                if (isOpen) {
                    content.removeClass("show").slideUp();
                    icon.text('+');
                } else {
                    content.addClass("show").slideDown();
                    icon.text('−');
                }
            },
            error: function () {
                alert("Error al cargar los tiempos de las estaciones.");
            }
        });
    });
</script>
@endsection