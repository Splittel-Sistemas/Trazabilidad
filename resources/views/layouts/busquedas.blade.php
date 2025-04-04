@extends('layouts.menu2')
@section('title', 'Progreso')
@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--estilos-->
    <style>
        
    .progress-scroll-container {
        margin-top: 20px;
        max-height: 300px;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
    }

    .task-progress-bar {
        margin-bottom: 15px;
        background-color: #e0e0e0;
        border-radius: 4px;
        position: relative;
        padding: 5px;
    }

    .task-label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .task-progress {
        height: 24px;
        line-height: 24px;
        color: #fff;
        text-align: right;
        padding-right: 8px;
        border-radius: 4px;
        font-weight: bold;
        transition: width 0.3s ease;
    }

    .progress-text {
        position: absolute;
        top: 5px;
        right: 10px;
        font-size: 12px;
        color: #000;
    }

    .retrabajo .task-progress {
        background-color: blue !important;
    }






        /*estilo de boton detalles*/
        .ver-detalles:hover {
            background-color: #0c705f; /
            transform: translateY(-2px) ; 
        }

        /*estilo de barrade progreso*/
        .card {
            overflow-x: auto; /* Permite scroll horizontal */
            padding: 10px; /* Espaciado opcional */
            border: 1px solid #ccc; /* Estilo de borde opcional */
            border-radius: 8px; /* Bordes redondeados */
            max-width: 100%; /* Que no se pase del contenedor padre */
        }

        .progress-bar-stages {
            list-style: none;
            display: flex;
            gap: 10px; /* Espacio entre elementos */
            width: max-content; /* Que se adapte al contenido interno */
            padding: 0;
            margin: 0;
        }

        .stage {
            text-align: center;
            cursor: pointer;
            flex: 0 0 auto; /* No se estira, conserva su tamaño */
            min-width: 80px; /* Tamaño mínimo para que se vea bien en scroll */
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
            font-size: 12px;
        }
        .card::-webkit-scrollbar {
            height: 8px;
        }

        .card::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .card::-webkit-scrollbar-thumb {
            background: #a0d2e9;
            border-radius: 4px;
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
            }*/

            .wizard-buttons {
                text-align: center;
                margin-top: 20px;
            }

            .btn {
                /*padding: 10px 20px;
                /*border: none;*/
                cursor: pointer;
            }

            .btn-secondary {
                background-color: #6c757d;
                color: rgba(255, 255, 255, 0.596);
            }

            .btn-primary {
                background-color: #007bff;
                color: rgba(255, 255, 255, 0.459);
            }
        
        .btn-primary, .btn-secondary {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 25px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    
        /* Barra de progreso */
        
        .task-progress-bar {
                margin-bottom: 15px; 
                position: relative;
                background-color: #f1f1f1;
                border-radius: 10px;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
                padding: 8px; 
                font-size:  
            }

            
            .task-label {
                font-weight: normal; 
                text-align: center;
                margin-bottom: 5px; 
            }

         
            .task-progress {
                height: 20px; 
                border-radius: 10px;
                position: relative;
                overflow: hidden;
                display: flex;
                align-items: center; 
                justify-content: center;
            }

          
            .progress-text {
                color: rgb(0, 0, 0);
                font-size: 12px; 
                font-weight: normal; 
            }

          
            .task-progress-bar .task-progress {
                font-size: 10px; 
            }

            .task-progress-bar .task-progress span {
                font-size: 10px; 
        }

      
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
                font-size: 1px; 
                padding: -100px; 
            }
            .status-box {
                position: relative;  /* Hacer que se pueda mover */
                left: 135px; /* Mover el elemento hacia la izquierda */
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100px;
                height: 30px;
                border-radius: 12px;
                background-color: gray;
                font-weight: bold;
                font-size: 14px;
                color: white;
                gap: 6px;
                transition: all 0.3s ease-in-out;
            }

            .status-box i {
                font-size: 1rem;
            }

            .status-box.bg-success {
                background-color: green;
            }

            .status-box.bg-danger {
                background-color: red;
            }

            .status-box.bg-secondary {
                background-color: gray;
            }

    </style>
@endsection
@section('content')
    <!-- Breadcrumbs -->
    <div class="row gy-3 mb-1 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Busquedas</h4>
        </div>
    </div>
    <div class="container mt-1">
        <div class="row">
            <div class="card shadow-sm border-light col-4 col-sm-6 p-2 mb-1">
                <div class="card-header m-1 p-1">
                    <div class="form-check ml-2">
                        <input class="form-check-input" id="TipoOrden1" type="radio" name="TipoOrden" checked>
                        <label class="form-check-label text-black" for="TipoOrden1">Orden de venta</label>
                    </div>
                    <div class="form-check ml-2">
                        <input class="form-check-input" id="TipoOrden2" type="radio" name="TipoOrden">
                        <label class="form-check-label text-black" for="TipoOrden2">Orden de fabricación</label>
                    </div>
                </div>
                <div class="card-body m-1 p-1">
                    <div class="mb-2 col-10">
                        <label for="NumeroOrden" class="form-label">N&uacute;mero de Orden</label>
                        <div class="input-group">
                            <input type="text" oninput="RegexNumeros(this)" class="form-control form-control-sm" id="NumeroOrden" placeholder="Ingresa número de Orden">
                            <button class="btn btn-outline-primary btn-sm" id="Btn-BuscarOrden">
                                Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tabla 1: Orden de Venta -->
            <div id="tablaVenta" class="card p-4" style="display:none;">
                <!--<div class="card border border-light mx-auto" style="max-width: 420px; border-radius: 40px; height: 50px; width: 44%;">
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
                </div>-->
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
                <!--<div class="card border border-light mx-auto" style="max-width: 420px; border-radius: 40px; height: 50px; width: 44%;">
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
                </div>-->
                
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
                        <span id="EstatusFabricacion"class="status-box">Estado</span> 
                        
                        <button type="button" class="btn p-1" data-bs-dismiss="modal" aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs--1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                                <path fill="currentColor" d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body ">
                        <!-- Barra de progreso -->
                        <div class="progress" style="height: 22px; border-radius: 10px; box-shadow: 0px 3px 6px rgba(0,0,0,0.2); overflow: hidden; width: 90%; margin-left: 5%;">
                            <div id="plemasProgressBar" class="progress-bar text-white fw-bold progress-animated" role="progressbar" 
                                 style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;" 
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0%
                            </div>
                        </div>
                        <div style="height:25px;"></div>
                        <div class="row justify-content-center">
                            <!-- Primera fila (4 elementos) -->
                            <div class="row">
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card shadow-sm border-0 p-2 text-center" style="background-color: #d4edda;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="fa-stack fa-1x">
                                                <i class="fas fa-circle fa-stack-2x text-success"></i>
                                                <i class="fas fa-stopwatch fa-stack-1x text-white "></i>
                                            </span>
                                        </div>
                                        <h5 class="mt-2">Productivo</h5>
                                        <p id="Produccion"  class="text-muted fs--1 mb-0">Tiempo Promedio</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card shadow-sm border-0 p-2 text-center" style="background-color: #cce5ff;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="fa-stack fa-1x">
                                                <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                                <i class="fas fa-clock fa-stack-1x text-white"></i>
                                            </span>
                                        </div>
                                        <h5 class="mt-2">Tiempo Total</h5>
                                        <p id="TiempoTotal"  class="text-muted fs--1 mb-0">Tiempo Total de la Orden</p>
                                    </div>
                                </div>
                            
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card shadow-sm border-0 p-2 text-center" style="background-color: #f8d7da;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="fa-stack fa-1x">
                                                <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                                <i class="fas fa-hourglass-empty fa-stack-1x text-white"></i>
                                            </span>
                                        </div>
                                        <h5 class="mt-2">Tiempo Muerto</h5>
                                        <p id="Muerto"  class="text-muted fs--1 mb-0">Tiempo Promedio</p>
                                    </div>
                                </div>
                            </div>
                        <br>
                        <!---->
                         <!--<div class="card p-3">-->
                            <h4 class="text-center mb-2 mt-2">Estaci&oacute;nes</h4>
                            <div class="grid-container" id="plemasCanvases">
                               
                                <div class="card">
                                    <div class="grid-item">
                                        <h1 class="small-title">Cortes</h1>

                                        <div class="canvas-container">
                                            <canvas id="plemasCorte" width="300" height="300"></canvas> 
                                        </div>
                                        
                                        <div class="title-container">
                                            <h1 class="small-title" id="titulo-cortes"></h1>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="grid-item">
                                    <h1 class="small-title">Suministros</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasSuministro" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-suministro"></h1>
                                    </div>
                                </div>

                                <div class="grid-item">
                                    <h1 class="small-title">Transicion</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasTransicion" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-transicion"></h1>
                                    </div>
                                </div>
            
                               
                                <div class="grid-item">
                                    <h1 class="small-title">Preparado</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasPreparado" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-preparado"></h1>
                                    </div>
                                </div>

                                <div class="grid-item">
                                    <h1 class="small-title">Ribonizado</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasRibonizado" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-ribonizado"></h1>
                                    </div>
                                </div>
            
                               
                                <div class="grid-item">
                                    <h1 class="small-title"> Ensamble</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasEnsamble" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-ensamble"></h1>
                                    </div>
                                </div>
                                <div class="grid-item">
                                    <h1 class="small-title"> Corte de Fibra</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasCorteFibra" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-corteFibra"></h1>
                                    </div>
                                </div>
            
                                
                                <div class="grid-item">
                                    <h1 class="small-title"> Pulido</h1>
                                    
                                    <div class="canvas-container">
                                        <canvas id="plemasPulido" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-pulido"></h1>
                                    </div>
                                </div>
                                <div class="grid-item">
                                    <h1 class="small-title">Armado</h1>
                                    <div class="canvas-container">
                                        <canvas id="plemasArmado" width="300" height="300"></canvas>
                                    </div>
                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-armado"></h1>
                                    </div>
                                </div>
                                <div class="grid-item">
                                    <h1 class="small-title">Inspeccion</h1>
                                    
                                    <div class="canvas-container">
                                        <canvas id="plemasInspeccion" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-inspeccion"></h1>
                                    </div>
                                </div>
                                <div class="grid-item">
                                    <h1 class="small-title">Polaridad</h1>
                                    
                                    <div class="canvas-container">
                                        <canvas id="plemasPolaridad" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-polaridad"></h1>
                                    </div>
                                </div>
            
                                <div class="grid-item">
                                    <h1 class="small-title">Crimpado</h1>
                                    
                                    <div class="canvas-container">
                                        <canvas id="plemasCrimpado" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-crimpado"></h1>
                                    </div>
                                </div>
            
            
                               
                                <div class="grid-item">
                                    <h1 class="small-title"> Medición</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasMedicion" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-medicion"></h1>
                                    </div>
                                </div>
            
                                
                                <div class="grid-item">
                                    <h1 class="small-title"> Visualización</h1>
                                
                                    <div class="canvas-container">
                                        <canvas id="plemasVisualizacion" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-visualizacion"></h1>
                                    </div>

                                </div>
                                <div class="grid-item">
                                    <h1 class="small-title"> Montaje</h1>

                                    <div class="canvas-container">
                                        <canvas id="plemasMontaje" width="300" height="300"></canvas>
                                    </div>

                                    <div class="title-container">
                                        <h1 class="small-title" id="titulo-montaje"></h1>
                                    </div>

                                </div>
            
                                <div class="grid-item">
                                    <h1 class="small-title"> Empaque</h1>

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
        </div>
        <!--modal principal-->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-info" id="exampleModalLabel">
                            Detalles Orden Venta:
                            <span id="ordenVentaNumero" class="ms-3 text-muted"></span>
                        </h5>
                        <span id="Estatus1"class="status-box">Estado</span> 
                      
                          
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
                        <div class="card">
                            <ul class="progress-bar-stages">
                                <li class="stage" id="stage2">
                                    <div class="stage-circle">
                                        <i class="fas fa-cut"></i>
                                    </div>
                                    <span>2. Corte</span>
                                </li>
                                <li class="stage" id="stage3">
                                    <div class="stage-circle">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <span>3. Suministro</span>
                                </li>
                                <li class="stage" id="stage4">
                                    <div class="stage-circle">
                                        <i class="fas fa-random"></i>
                                    </div>
                                    <span>4. Transición</span>
                                </li>
                                <li class="stage" id="stage5">
                                    <div class="stage-circle">
                                        <i class="fas fa-wrench"></i>
                                    </div>
                                    <span>5. Preparado</span>
                                </li>
                                <li class="stage" id="stage6">
                                    <div class="stage-circle">
                                        <i class="fas fa-align-left"></i>
                                    </div>
                                    <span>6. Ribonizado</span>
                                </li>
                                <li class="stage" id="stage7">
                                    <div class="stage-circle">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                    <span>7. Ensamble</span>
                                </li>
                                <li class="stage" id="stage8">
                                    <div class="stage-circle">
                                        <i class="fas fa-cut"></i>
                                    </div>
                                    <span>8. Corte de Fibra</span>
                                </li>
                                <li class="stage" id="stage9">
                                    <div class="stage-circle">
                                        <i class="fas fa-gem"></i>
                                    </div>
                                    <span>9. Pulido</span>
                                </li>
                                <li class="stage" id="stage10">
                                    <div class="stage-circle">
                                        <i class="fas fa-screwdriver"></i>
                                    </div>
                                    <span>10. Armado</span>
                                </li>
                                <li class="stage" id="stage11">
                                    <div class="stage-circle">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <span>11. Inspección</span>
                                </li>
                                <li class="stage" id="stage12">
                                    <div class="stage-circle">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <span>12. Polaridad</span>
                                </li>
                                <li class="stage" id="stage13">
                                    <div class="stage-circle">
                                        <i class="fas fa-link"></i>
                                    </div>
                                    <span>13. Crimpado</span>
                                </li>
                                <li class="stage" id="stage14">
                                    <div class="stage-circle">
                                        <i class="fas fa-ruler"></i>
                                    </div>
                                    <span>14. Medición</span>
                                </li>
                                <li class="stage" id="stage15">
                                    <div class="stage-circle">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <span>15. Visualización</span>
                                </li>
                                <li class="stage" id="stage16">
                                    <div class="stage-circle">
                                        <i class="fas fa-hammer"></i>
                                    </div>
                                    <span>16. Montaje</span>
                                </li>
                                <li class="stage" id="stage17">
                                    <div class="stage-circle">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <span>17. Empaque</span>
                                </li>

                            </ul>
                            <div id="progress-wrapper-container" class="progress-scroll-container"></div>

                        </div>
                        
                        <br> 
                        <h4 class="text-center mb-2 mt-2">Estaci&oacute;nes</h4>
                        
                        <div class="grid-container card p-2" id="canvases">
                            <!-- Estación Cortes -->
                            <div class="grid-item">
                                <h1 class="small-title">Cortes</h1>
                                <canvas id="Corte" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Suministros -->
                            <div class="grid-item">
                                <h1 class="small-title">Suministros</h1>
                                <canvas id="Suministro" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Transición -->
                            <div class="grid-item">
                                <h1 class="small-title">Transición</h1>
                                <canvas id="Transicion" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Preparado -->
                            <div class="grid-item">
                                <h1 class="small-title">Preparado</h1>
                                <canvas id="Preparado" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Ribonizado -->
                            <div class="grid-item">
                                <h1 class="small-title">Ribonizado</h1>
                                <canvas id="Ribonizado" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Ensamble -->
                            <div class="grid-item">
                                <h1 class="small-title">Ensamble</h1>
                                <canvas id="Ensamble" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Corte de Fibra -->
                            <div class="grid-item">
                                <h1 class="small-title">Corte de Fibra</h1>
                                <canvas id="CorteFibra" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Pulido -->
                            <div class="grid-item">
                                <h1 class="small-title">Pulido</h1>
                                <canvas id="Pulido" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Armado -->
                            <div class="grid-item">
                                <h1 class="small-title">Armado</h1>
                                <canvas id="Armado" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Inspección -->
                            <div class="grid-item">
                                <h1 class="small-title">Inspección</h1>
                                <canvas id="Inspeccion" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Polaridad -->
                            <div class="grid-item">
                                <h1 class="small-title">Polaridad</h1>
                                <canvas id="Polaridad" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Crimpado -->
                            <div class="grid-item">
                                <h1 class="small-title">Crimpado</h1>
                                <canvas id="Crimpado" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Medición -->
                            <div class="grid-item">
                                <h1 class="small-title">Medición</h1>
                                <canvas id="Medicion" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Visualización -->
                            <div class="grid-item">
                                <h1 class="small-title">Visualización</h1>
                                <canvas id="Visualizacion" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Montaje -->
                            <div class="grid-item">
                                <h1 class="small-title">Montaje</h1>
                                <canvas id="Montaje" width="300" height="300"></canvas>
                            </div>
                            
                            <!-- Estación Empaque -->
                            <div class="grid-item">
                                <h1 class="small-title">Empaque</h1>
                                <canvas id="Empaque" width="300" height="300"></canvas>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>   
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
        /*$.ajax({
            url: '{{ route("Buscar.Venta.Detalle") }}',
            type: 'GET',
            data: { id: ordenVenta },
            success: function (response) {
            // console.log('Respuesta completa:', response);  // Verifica la estructura de la respuesta

                if (response.partidasAreas.length > 0) {
                    var totalEtapas = $('.progress-bar-stages .stage').length;
                    var etapasCompletadas = 0;

                

                    

                    var porcentaje = response.Porcentaje;
                    $('#progressBar').css('width', porcentaje + '%').text(porcentaje + '%');
                    $('#ordenVentaNumero').removeClass('text-muted').addClass('text-info').text(ordenVenta);
                    var estadoDeVenta = response.Estatus.length > 0 ? response.Estatus[0].Estado : '';  // Verifica si existe al menos un estado

            // Depuración: Verifica el estado recibido
            console.log('Estado recibido:', estadoDeVenta);

            // Elimina todas las clases de estado anteriores
            $('#Estatus1').removeClass('badge bg-success bg-danger bg-secondary').addClass('badge');

            if (estadoDeVenta === 'Abierta') {
                $('#Estatus1').removeClass('bg-danger bg-secondary').addClass('bg-success').text('Abierta');
                console.log('Estado: Abierta, Clases: bg-success');
            } else if (estadoDeVenta === 'Cerrada') {
                $('#Estatus1').removeClass('bg-success bg-secondary').addClass('bg-danger').text('Cerrada');
                console.log('Estado: Cerrada, Clases: bg-danger');
            } else {
                $('#Estatus1').removeClass('bg-success bg-danger').addClass('bg-secondary').text('Estado desconocido');
                console.log('Estado desconocido, Clases: bg-secondary');
            }

                    } else {
                        $('.progress-bar-stages .stage').addClass('no-data');
                        $('#progressBar').css('width', '0%').text('0%');
                        $('#ordenVentaNumero').removeClass('text-muted').addClass('text-info').text(ordenVenta);

                        // Verificamos si hay datos en `Estatus`
                        if (response.Estatus.length > 0) {
                            var estados = response.Estatus.map(item => item.Estado).join(', ');
                            $('#Estatus1').text('Estado de la OV: ' + estados);
                        } else {
                            $('#Estatus1').text('Estado de la OV: No disponible');
                        }
                    }

                    $('#exampleModal').modal('show');
                },
                error: function () {
                    alert('Error al obtener los datos de la venta.');
                }
        });*/
        $.ajax({
            url: '{{ route("Buscar.Venta.Detalle") }}',
            type: 'GET',
            data: { id: ordenVenta },
            success: function (response) {
                console.log('Respuesta de la API:', response);  // Verifica la respuesta completa

                if (response.Estatus && response.Estatus.length > 0) {
                    var porcentaje = response.Porcentaje;
                    var progressBar = $('#progressBar');
                    
                    // Resetear clases de color
                    progressBar.removeClass('bg-danger bg-warning bg-primary bg-info bg-success');

                    // Aplicar clase de color según el progreso
                    if (porcentaje >= 0 && porcentaje < 20) {
                        progressBar.addClass('bg-danger');  // Rojo
                    } else if (porcentaje >= 20 && porcentaje < 40) {
                        progressBar.addClass('bg-warning');  // Naranja
                    } else if (porcentaje >= 40 && porcentaje < 70) {
                        progressBar.addClass('bg-primary');  // Azul
                    } else if (porcentaje >= 70 && porcentaje < 90) {
                        progressBar.addClass('bg-info');  // Celeste
                    } else {
                        progressBar.addClass('bg-success');  // Verde
                    }

                    progressBar.css('width', porcentaje + '%').text(porcentaje + '%');
                    $('#ordenVentaNumero').removeClass('text-muted').addClass('text-info').text(ordenVenta);

                    // Verificar si todas las órdenes están cerradas
                    var todasCerradas = response.Estatus.every(est => est.Estado === 'Cerrada');
                    var estadoDeVenta = todasCerradas ? 'Cerrada' : 'Abierta';
                    console.log('Estado calculado:', estadoDeVenta);
                    $('#exampleModal').modal('show');
                    $('#Estatus1').removeClass('badge bg-success bg-danger bg-secondary').addClass('badge');
                    
                    var icono = '';
                    if (estadoDeVenta === 'Abierta') {
                        $('#Estatus1').removeClass('bg-danger bg-secondary').addClass('bg-success');
                        icono = '<i class="fas fa-lock-open"></i>'; 
                        $('#Estatus1').html(icono + ' Abierta');
                        console.log('Estado: Abierta, Clases: bg-success');
                    } else {
                        $('#Estatus1').removeClass('bg-success bg-secondary').addClass('bg-danger');
                        icono = '<i class="fas fa-lock"></i>'; 
                        $('#Estatus1').html(icono + ' Cerrada');
                        console.log('Estado: Cerrada, Clases: bg-danger');
                    }
                } else {
                    console.log('No se encontró el estado de la venta');
                    $('.progress-bar-stages .stage').addClass('no-data');
                    $('#progressBar').css('width', '0%').text('0%');
                    $('#ordenVentaNumero').removeClass('text-muted').addClass('text-info').text(ordenVenta);
                }
            },
            error: function () {
                console.log('Error al obtener los datos de la venta');
            }
        });
       
        const endpoints = [
            { tipo: 'Cortes', id: 'corte' },
            { tipo: 'Suministros', id: 'suministro' },
            { tipo: 'Transicion', id: 'transicion' },
            { tipo: 'Preparado', id: 'preparado' },
            { tipo: 'Ribonizado', id: 'ribonizado' },
            { tipo: 'Ensamble', id: 'ensamble' },
            { tipo: 'CorteF', id: 'cortef' },
            { tipo: 'Pulido', id: 'pulido' },
            { tipo: 'Armado', id: 'armado' },
            { tipo: 'Inspeccion', id: 'inspeccion' },
            { tipo: 'Polaridad', id: 'polaridad' },
            { tipo: 'Crimpado', id: 'crimpado' },
            { tipo: 'Medicion', id: 'medicion' },
            { tipo: 'Visualizacion', id: 'visualizacion' },
            { tipo: 'Montaje', id: 'montaje' },
            { tipo: 'Empaque', id: 'empaque' },
        ];

       
        if (typeof ordenVenta === "undefined" || ordenVenta === null) {
            console.error("Error: ordenVenta no está definida.");
        } else {
            // Solo una llamada AJAX
            $.ajax({
                url: '{{ route("Buscar.Venta.Detalle") }}',
                url: 'route("graficador")), 
                type: 'GET',
                data: { id: ordenVenta }, // Solo pasamos el ID
                success: function(response) {
                    // Verificamos si 'data' está presente en la respuesta
                    if (!response.data) {
                        console.error("Error: No se encontraron datos en la respuesta.");
                        return;
                    }

                    endpoints.forEach(endpoint => {
                        const datos = response.data[endpoint.tipo]; // Ahora accedemos correctamente
                        if (datos && typeof datos.Progreso !== "undefined") {
                            const progreso = Math.min(datos.Progreso, 100);
                            drawGauge(endpoint.id, progreso, '');
                        } else {
                            drawGauge(endpoint.id, 0, 'Sin Datos');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error al obtener los datos:", error);
                    // Si falla todo, pinta todos los gauges con error
                    endpoints.forEach(endpoint => {
                        drawGauge(endpoint.id, 0, 'Error');
                    });
                }
            });

        }

    });
    // Cargar datos de la tabla de orden de fabricación
    $('#Btn-BuscarOrden').on('click', function () {
        var search = $('#NumeroOrden').val().trim();
        if (search === '') {
            return;  
        }
        var radioButton1 = document.getElementById("TipoOrden1");
        if (radioButton1.checked) {
            cargarDatosVenta(search);
        }else{
            cargarDatosFabricacion(search);
        }
    });
    //cargar los datos de fabricacion
    function cargarDatosFabricacion(search) {
        $('#tablaVenta').hide();
        var tablaFabricacion = $('#tablaFabricacion');
        tablaFabricacion.fadeIn(1000);
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
                console.log(ordenfabricacion);  
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
                            progressBar.removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                            // Asignar color según el porcentaje
                            if (progreso >= 0 && progreso < 20) {
                                progressBar.addClass('bg-danger');  // Rojo
                            } else if (progreso >= 20 && progreso < 40) {
                                progressBar.addClass('bg-warning');  // Naranja
                            } else if (progreso >= 40 && progreso < 70) {
                                progressBar.addClass('bg-primary');  // Azul
                            } else if (progreso >= 70 && progreso < 90) {
                                progressBar.addClass('bg-info');  // Celeste
                            } else {
                                progressBar.addClass('bg-success');  // Verde
                            }
                            $('#ordenFabricacionNumero').removeClass('text-muted').addClass('text-info').text(ordenfabricacion);
                            if (response.Estatus && response.Estatus.length > 0) {
                                var estadoFabricacion = response.Estatus[0].Estado || 'Desconocido';
                                var $estatusElem = $('#EstatusFabricacion');
                                var icono = '';
                                $estatusElem.removeClass('bg-success bg-danger bg-secondary').addClass('badge');

                                if (estadoFabricacion === 'Abierta') {
                                    $estatusElem.removeClass('bg-danger bg-secondary').addClass('bg-success');
                                    icono = '<i class="fas fa-lock-open"></i>';  // Ícono de "Abierta"
                                    $estatusElem.html(icono + ' Abierta');
                                    console.log('Estado: Abierta, Clases: bg-success');
                                } else if (estadoFabricacion === 'Cerrada') {
                                    $estatusElem.removeClass('bg-success bg-secondary').addClass('bg-danger');
                                    icono = '<i class="fas fa-lock"></i>';  // Ícono de "Cerrada"
                                    $estatusElem.html(icono + ' Cerrada');
                                    console.log('Estado: Cerrada, Clases: bg-danger');
                                } else {
                                    $estatusElem.removeClass('bg-success bg-danger').addClass('bg-secondary');
                                    icono = '<i class="fas fa-question-circle"></i>';  // Ícono de "Desconocido"
                                    $estatusElem.html(icono + ' Estado desconocido');
                                    console.log('Estado desconocido, Clases: bg-secondary');
                                }
                            }
                        } else {
                            progressBar.css('width', '0%').text('0%').removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                            $('#ordenFabricacionNumero').removeClass('text-info').addClass('text-muted').text(ordenfabricacion);
                            $('#EstatusFabricacion').removeClass('bg-success bg-danger bg-secondary').text('Sin datos');
                        }
                        $('#example2Modal').modal('show');
                    },
                    error: function () {
                        alert('Error al obtener los datos de la fabricación.');
                    }
                });
/*
                $.ajax({
                    url: '{{ route("graficadoOF") }}',
                    type: 'GET',
                    data: { id: ordenfabricacion },
                    success: function(response) {
                        if (response && response.estaciones) {
                            Object.keys(response.estaciones).forEach(tipo => {
                                let estacion = response.estaciones[tipo];

                                if (estacion && estacion.length > 0) {
                                    let datos = estacion[0]; 
                                    let porcentaje = datos.porcentaje ? Math.floor(datos.porcentaje) : 0;
                                    let label = datos.totalR > 0 ? `Retrabajo: ${datos.totalR}` : '';

                                    // Verifica si ya existe el contenedor, si no, lo crea dinámicamente
                                    if (!$(`#${tipo}`).length) {
                                        $(".grid-container").append(`
                                            <div class="grid-item">
                                                <h1 class="small-title">${tipo.replace('plemas', '')}</h1>
                                                <div class="canvas-container">
                                                    <canvas id="${tipo}" width="300" height="300"></canvas>
                                                </div>
                                                <div class="title-container">
                                                    <h1 class="small-title" id="titulo-${tipo}"></h1>
                                                </div>
                                            </div>
                                        `);
                                    }

                                    drawGauge(tipo, porcentaje, label);
                                } else {
                                    console.log(`No hay datos para ${tipo}`);
                                    drawGauge(tipo, 0, 'Sin Datos');
                                }
                            });
                        } else {
                            console.log('No hay datos para mostrar.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al obtener los datos:', error);
                    }
                });*/

                
                
            const endpoints = [
                { tipo: 'plemasCorte', id: 'plemasCorte', areaId: 2 },
                { tipo: 'plemasSuministrodia', id: 'plemasSuministro', areaId: 3 },
                { tipo: 'plemasTransiciondia', id: 'plemasTransicion', areaId: 4 },
                { tipo: 'plemasPreparadodia', id: 'plemasPreparado', areaId: 5 },
                { tipo: 'plemasRibonizadodia', id: 'plemasRibonizado', areaId: 6 },
                { tipo: 'plemasEnsambledia', id: 'plemasEnsamble', areaId: 7 },
                { tipo: 'plemasCortesFibradia', id: 'plemasCorteFibra', areaId: 8 },
                { tipo: 'plemasPulidodia', id: 'plemasPulido', areaId: 9 },
                { tipo: 'plemasArmadodia', id: 'plemasArmado', areaId: 10 },
                { tipo: 'plemasInspecciondia', id: 'plemasInspeccion', areaId: 11 },
                { tipo: 'plemasPolaridaddia', id: 'plemasPolaridad', areaId: 12 },
                { tipo: 'plemasCrimpadodia', id: 'plemasCrimpado', areaId: 13 },
                { tipo: 'plemasMediciondia', id: 'plemasMedicion', areaId: 14 },
                { tipo: 'plemasVisualizaciondia', id: 'plemasVisualizacion', areaId: 15 },
                { tipo: 'plemasMontajedia', id: 'plemasMontaje', areaId: 16 },
                { tipo: 'plemasEmpaque', id: 'plemasEmpaque', areaId: 17 },
            ];
            $.ajax({
                url: '{{ route("graficadoOF") }}',
                type: 'GET',
                data: { id: ordenfabricacion },
                success: function(response) {
                    if (response && response.estaciones) {
                        endpoints.forEach(endpoint => {
                            let estacion = response.estaciones[endpoint.tipo];
                            if (estacion && estacion.length > 0) {
                                let datos = estacion[0]; 
                                let porcentaje = datos.porcentaje ? Math.floor(datos.porcentaje) : 0;

                                let label = datos.totalR > 0 ? `Retrabajo: ${datos.totalR}` : '';
                                drawGauge(endpoint.id, porcentaje, label);
                            } else {
                                console.log(`No hay datos para ${endpoint.tipo}`);
                                drawGauge(endpoint.id, 0, 'Sin Datos');
                            }
                        });
                    } else {
                        console.log('No hay datos para mostrar.');
                        endpoints.forEach(endpoint => drawGauge(endpoint.id, 0, 'Sin Datos'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener los datos:', error);
                    endpoints.forEach(endpoint => drawGauge(endpoint.id, 0, 'Error'));
                }
            });
            $.ajax({
                url: '{{ route("tiempos.hrs") }}',
                method: 'GET',
                data: { 
                    id: ordenfabricacion,
                },
                success: function(response) {
                    // Asigna el tiempo total
                    document.getElementById("TiempoTotal").textContent = response.tiempototal.DuracionTotal;
                    document.getElementById("Muerto").textContent = response.TiempoMuertoFormato;
                    document.getElementById("Produccion").textContent = formatTiempo(response.totalSegundos);

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
                                    $('#titulo-transicion')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                case 5:
                                    $('#titulo-preparado')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                    case 6:
                                    $('#titulo-ribonizado')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                case 7:
                                    $('#titulo-ensamble')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                    case 8:
                                    $('#titulo-corteFibra')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                case 9:
                                    $('#titulo-pulido')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                    case 10:
                                    $('#titulo-armado')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                    case 11:
                                    $('#titulo-inspeccion')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                    case 12:
                                    $('#titulo-polaridad')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                case 13:
                                    $('#titulo-crimpado')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                    case 14:
                                    $('#titulo-medicion')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                case 15:
                                    $('#titulo-visualizacion')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                    case 16:
                                    $('#titulo-montaje')
                                        .text('Duración: ' + item.DuracionTotal)
                                        .css({
                                            'font-size': '10px',
                                            'color': 'black',
                                            'font-weight': 'bold'
                                        });
                                    break;
                                case 17:
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


        
function formatTiempo(totalSegundos) {
    // Asegurarse de que totalSegundos sea un número entero
    totalSegundos = parseInt(totalSegundos);

    // Calcular horas, minutos y segundos
    const horas = Math.floor(totalSegundos / 3600);
    totalSegundos %= 3600;
    const minutos = Math.floor(totalSegundos / 60);
    const segundos = totalSegundos % 60;

    // Crear una cadena con el formato adecuado
    let tiempoFormateado = "";

    // Si hay horas, las añadimos al resultado
    if (horas > 0) {
        tiempoFormateado += `${horas} hora${horas > 1 ? 's' : ''}`;
    }

    // Si hay minutos, las añadimos
    if (minutos > 0) {
        if (tiempoFormateado !== "") tiempoFormateado += " ";
        tiempoFormateado += `${minutos} minuto${minutos > 1 ? 's' : ''}`;
    }

    // Siempre añadimos los segundos si son mayores a 0
    if (segundos > 0 || tiempoFormateado === "") {
        if (tiempoFormateado !== "") tiempoFormateado += " ";
        tiempoFormateado += `${segundos} segundo${segundos > 1 ? 's' : ''}`;
    }

    return tiempoFormateado;
}
    //Cargar Ventas
    function cargarDatosVenta(search) {
        $('#tablaFabricacion').hide(100);
        $('#tablaVenta').fadeIn(1000);
        $.ajax({
            url: '{{ route("Buscar.Venta") }}',
            method: 'GET',
            data: { search: search },
            success: function (data) {
                var tbody = $('#tabla-resultadosVenta');
                tbody.empty();

                if (data.length > 0) {
                    data.forEach(function (item) {
                        //console.log(item);
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
        else strokeColor = '#12c72a'; // Verde

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
    /*
    $(document).ready(function() {
        $('.stage').on('click', function() {
            var stageId = $(this).attr('id');
            var ordenVenta = $(this).data('ordenventa');
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
        console.log('Enviando datos al controlador:');
    console.log('Orden de venta:', ordenVenta);
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
                        progressColor = '#12c72a'; // Verde

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
    }*/
    let currentStageOpen = null; // <- para rastrear la etapa activa

$(document).ready(function () {
    $('.stage').on('click', function () {
        const stageId = $(this).attr('id');
        const ordenVenta = $(this).data('ordenventa');
        const progressWrapper = $('#progress-wrapper-container');

        // Si el mismo stage se clickea otra vez, lo cerramos
        if (currentStageOpen === stageId) {
            currentStageOpen = null;
            progressWrapper.slideUp().empty(); // Oculta y limpia
            $('.stage').removeClass('selected-stage');
            return; // Detener ejecución
        }

        currentStageOpen = stageId; // Guardamos la nueva etapa abierta
        $('.stage').removeClass('selected-stage');
        $(this).addClass('selected-stage');

        progressWrapper.slideDown(); // Mostrar el contenedor
        loadProgressData(ordenVenta, stageId);
    });
});

function loadProgressData(ordenVenta, stageId) {
    console.log('Enviando datos al controlador:', ordenVenta, stageId);

    $.ajax({
        url: '{{ route("graficarOR.OF") }}',
        method: 'GET',
        data: {
            id: ordenVenta,
            stage: stageId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            const data = response.flat(); // Aplanar si viene doble array
            const progressWrapper = $('#progress-wrapper-container');
            progressWrapper.empty(); // Limpiar contenido anterior

            data.forEach(function (item, index) {
                const progressPercentage = parseFloat(item.Progreso);
                const displayProgress = Math.min(progressPercentage, 100);

                let progressColor = 'red';
                if (displayProgress > 30) progressColor = 'orange';
                if (displayProgress > 50) progressColor = 'yellow';
                if (displayProgress > 90) progressColor = '#12c72a';

                const progressBar = $('<div>', { class: 'task-progress-bar' });
                const progressLabel = $('<div>', { class: 'task-label', text: 'Orden ' + item.OrdenesFabricacion });

                const progress = $('<div>', {
                    class: 'task-progress',
                    text: displayProgress + '%',
                    css: {
                        width: displayProgress + '%',
                        backgroundColor: progressColor
                    }
                });

                const progressText = $('<div>', {
                    class: 'progress-text',
                    text: displayProgress + '%'
                });

                progressBar.append(progressLabel, progress, progressText);
                progressWrapper.append(progressBar);

                // Retrabajo
                if (progressPercentage > 100) {
                    const retrabajoPercentage = (progressPercentage - 100).toFixed(2);

                    const retrabajoBar = $('<div>', { class: 'task-progress-bar retrabajo' });
                    const retrabajoLabel = $('<div>', { class: 'task-label', text: 'Retrabajo ' + item.OrdenesFabricacion });

                    const retrabajoProgress = $('<div>', {
                        class: 'task-progress',
                        text: retrabajoPercentage + '%',
                        css: {
                            width: retrabajoPercentage + '%',
                            backgroundColor: 'blue'
                        }
                    });

                    const retrabajoText = $('<div>', {
                        class: 'progress-text',
                        text: retrabajoPercentage + '%'
                    });

                    retrabajoBar.append(retrabajoLabel, retrabajoProgress, retrabajoText);
                    progressWrapper.append(retrabajoBar);
                }
            });
        },
        error: function (xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error);
        }
    });
}


    // Cuando se haga clic en una fila para seleccionar la OrdenFabricacion
    $(document).on('click', '.ver-fabricacion', function () {
        var ordenfabricacion = $(this).data('ordenfabricacion');  
        console.log('Orden de fabricación seleccionada:', ordenfabricacion);  

        
        $('.VerMas').data('ordenfabricacion', ordenfabricacion);  
        //  console.log('Valor asignado al botón VerMas:', $('.VerMas').data('ordenfabricacion')); 
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
    function cambiarEstatus(estado) {
        const estatusElement = document.getElementById('Estatus');
  
        if (estado === 'abierto') {
            estatusElement.classList.add('open');
            estatusElement.classList.remove('closed');
            estatusElement.textContent = 'Abierto';
        } else {
            estatusElement.classList.add('closed');
            estatusElement.classList.remove('open');
            estatusElement.textContent = 'Cerrado';
        }
    }
</script>
@endsection