@extends('layouts.menu2')
@section('title', 'Dashboard')
@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dashboard Layout */
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            padding: 30px;
            justify-items: center;
        }

        /* Summary Section */
        .summary-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .summary-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            min-width: 250px;
            font-size: 1.3em;
            font-weight: bold;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        /* Hover effect for summary boxes */
        .summary-box:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        /* Icon Styling */
        .summary-box i {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        /* Order Status Styling */
        .closed-orders {
            border-top: 4px solid #28a745;
            color: #28a745;
        }

        .open-orders {
            border-top: 4px solid #dc3545;
            color: #dc3545;
        }

        /* Specific hover effects for each type of status */
        .closed-orders:hover {
            background-color: #218838;
            
            border-top: 4px solid #218838;
        }

        .open-orders:hover {
            background-color: #c82333;
          
            border-top: 4px solid #c82333;
        }

        /* Progress Box Container */
        .progress-box-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
            width: 100%; /* Ensures full width */
            max-width: 1200px; /* Increases max width */
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin: 0 auto; /* Centers the container */
        }

        /* Progress Bar Container */
        .progress-bar-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .progress-bar-container span {
            font-size: 16px;
            width: 150px;
        }

        /* Progress Bar Styling */
        .progress-bar {
            width: 70%;  /* Maintains 70% of the container's width */
            height: 20px; /* Increases the height for better visibility */
            background-color: #ddd;
            border-radius: 5px;
            transition: width 0.5s ease;
        }

        /* Hover effect for progress bars */
        .progress-bar-container:hover {
            transform: translateY(-3px);
            cursor: pointer;
        }

        /* Chart Section */
        .chart-container {
            width: 100%;
            max-width: 800px;
           
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Hover effect for chart boxes */
        .chart-container:hover {
            transform: translateY(-12px);
            cursor: pointer;
        }

        /* Full width chart (Day Chart) */
        .chart-container.full-width {
            width: 100%;
            max-width: 100%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .summary-container {
                flex-direction: column;
                align-items: center;
            }

            .chart-container {
                width: 10%;
            }
        }
        .progress-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        }

        .progress-label {
            width: 100px; /* Ajusta el ancho según el espacio disponible */
            font-weight: bold;
            margin-right: 10px;
            text-align: right; /* Alinea el texto a la derecha */
        }

        .progress {
            flex-grow: 1;
            height: 22px;
            border-radius: 10px;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 90%;
            margin-left: 5%;
        }
        .progress-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        .fondo-rojo {
            background-color: red;
        }


        /*contendor de progreso */
      
       
        .grid-container {
            display: grid;
            grid-template-columns: repeat(4, 0fr);
            grid-template-rows: repeat(2, auto);
            gap: 30px;
            padding: 1px;
            max-width: 4500px;
            margin: auto;
        }
        .grid-item {
        border: 2px solid #ddd;  /* Borde alrededor del contenedor */
        padding: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;  /* Efecto suave en la transformación */
        border-radius: 10px;
        text-align: center;
        }

        .grid-item:hover {
        transform: translateY(-10px);  /* Movimiento hacia arriba cuando se pasa el cursor */
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);  /* Sombra para resaltar el contenedor */
        }

        .small-title {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .chart-container {
                width: 75vw;
                max-width:1400px;
                height: 400px;
                margin: 20px auto;
            }

            /*estilo de boton*/
            .btn-outline-info {
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease-in-out;
        }

        .btn-outline-info:hover {
            background-color: #17a2b8;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Acordeón con bordes suaves */
        .card {
            margin-bottom: 1rem;
        }

        .collapse {
            transition: all 0.3s ease;
        }

        .card-body {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Mejorar la apariencia del contenedor del gráfico */
        .chart-container {
           
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #chart-day, #chart-month, #chart-hour {
        width: 900px;
        height: 350px;
        }
        /*linea roja*/
        .hr{
            border-top: 10px solid rgb(189, 42, 42); 
            width: 100%; 
            margin: 1px auto;
        }
        .hr2{
            border-top: 5px solid rgb(22, 95, 163);
            width: 100%;
            margin: 2px auto;
        }        
        /*.col-12.col-md-3 {
            transition: all 0.3s ease; 
            position: relative; 
            border-radius: 90px; 
            overflow: hidden; 
            box-sizing: border-box; 
            padding: 1px; 
            border: 4x solid transparent; 
        }*/
        .btn-menu{
            padding: 0.2rem;
            box-sizing: border-box; 
            border-radius: 4rem;
            box-shadow: 0 1px 2px rgba(75, 75, 75, 0.2);
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .btn-menu:hover{
            background: #f8f9fa;
            transform: scale(1.02);
            /*additive-symbols: box-shadow: 0 3px 5px rgba(75, 75, 75, 0.2);
            border: 1px solid #eeeeee;*/
        }
        /* Efecto de hover */
        /*.col-12.col-md-3:hover {
            
            border: 1px solid #eeeeee; 
            box-shadow: 0 3px 5px rgba(0, 123, 255, 0.2); 
            transform: translateY(-1px); 
        }*/

        /* Opcional: Efecto de hover en el ícono */
        /*.col-12.col-md-3:hover .fa-stack i {
            color: #007bff; 
        }*/

        .center-text {
            display: flex;
            justify-content: center;  /* Centra horizontalmente */
            align-items: center;  /* Centra verticalmente */
            text-align: center;  /* Asegura que el texto esté centrado */
            width: 100%;
        }
        /* Estilo para los botones activos */
        .activebtn {
            background-color: #f8f9fa;
            color: white;
            border-radius: 4rem;
            box-shadow: 0px 2px 4px rgb(185, 185, 185);
        }



    </style>
    <style>
        .linea-row {
            margin-top: 20px; /* Ajusta el valor según la separación que desees */
        }
    </style>
@endsection
@section('content')
<!--botones principales Dia,Semana,mes-->
<div class="card text-center">
        <hr class="hr2">
            <h1 class="progress-title mt-3 mb-4"></h1>
            <div class="row justify-content-center">
                <!-- Órdenes Cerradas (Completadas) -->
                <div class="col-12 col-md-3 mb-4 m-1">
                    <div class="d-flex align-items-center justify-content-center activebtn btn-menu" id="click-dia" style="cursor: pointer;">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-calendar-day" style="font-size: 30px; color: #007bff;"></i>
                        </span>
                        <div class="ms-1">
                            <h4  class="mb-0" >Órdenes por Día</h4>
                            <p class="text-muted fs--1 mb-0"></p>
                        </div>
                    </div>
                </div>
                <!-- Órdenes Abiertas (En Proceso) -->
                <div class="col-12 col-md-3 mb-4 m-1 ">
                    <div class="d-flex align-items-center justify-content-center btn-menu" id="click-semana" style="cursor: pointer;">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-calendar-week" style="font-size: 30px; color: #ffc107;"></i>
                        </span>
                        <div class="ms-1">
                            <h4  class="mb-0" >Órdenes por Semana</h4>
                            <p class="text-muted fs--1 mb-0"></p>
                        </div>
                    </div>
                </div>
                <!-- Total de Órdenes -->
                <div class="col-12 col-md-3 mb-4 m-1">
                    <div class="d-flex align-items-center justify-content-center btn-menu" id="click-mes" style="cursor: pointer;">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-calendar-alt" style="font-size: 30px; color: #28a745;"></i>
                        </span>
                        <div class="ms-1">
                            <h4  class="mb-0" >Órdenes por Mes</h4>
                            <p class="text-muted fs--1 mb-0"></p>  
                        </div>
                    </div>
                </div>
            </div>
        <hr class="hr2">
</div>
<!--Indicadores de Producción por Dia-->
    <hr class="hr">
    <div style="height: 10px;"></div>
    <div id="indicadores-dia" class="mb-4">
        <div class="col-sm-12 bg-white mb-4">
            <div class="accordion-body bg-white pt-0">
                <div class="card-body bg-white p-1">
                    <h5 class="p-1">
                        Capacidad Productiva &nbsp;
                        <span id="Fecha_Grafica">{{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}</span>
                    </h5>
                    <div class="d-flex justify-content-between">
                        <div class="row">
                            <h6 class="text-700 col-6">
                                Cantidad personas: <span id="Cantidad">0</span>
                            </h6>
                            <h6 class="text-700 col-6">
                                Estimado de piezas por día: <span id="piezas">0</span>
                            </h6>
                            <h6 class="text-700 col-6">
                                Piezas Completadas: <span id="Piezaplaneadas">0</span>
                            </h6>
                            <h6 class="text-700 col-6">
                                Piezas faltantes: <span id="Piezafaltantes">0</span>
                            </h6>
                            <h6 class="text-700 col-6"></h6>
                        </div>
                    </div>
                    <div class="pb-1 pt-1 d-flex justify-content-center align-items-center">
                        <div class="p-0" id="PorcentajePlaneacion" style="width: 9rem; height: 9rem"></div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="bullet-item bg-primary me-2"></div>
                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">
                                Porcentaje Completadas
                            </h6>
                            <h6 class="text-900 fw-semi-bold mb-0">
                                <span id="Porcentajeplaneada">0</span>%
                            </h6>
                        </div>

                        <div class="d-flex align-items-center mb-2">
                            <div class="bullet-item bg-primary-200 me-2"></div>
                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">
                                Porcentaje Faltantes
                            </h6>
                            <h6 class="text-900 fw-semi-bold mb-0">
                                <span id="Porcentajefaltante">0</span>%
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="row" id="lineas-container"></div>   
    <div class="card text-center pb-3">
            <h1 class="progress-title mt-3 mb-4">Indicadores de Producción por Dia</h1>
            <div class="row justify-content-center">
                <!-- Órdenes Cerradas (Completadas) -->
                <div class="col-12 col-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-check-circle" style="font-size: 30px; color: #28a745;"></i>
                        </span>
                        <div class="ms-3">
                            <h4 id="ordenesCompletadasDia" class="mb-0">Órdenes Cerradas</h4>
                            <p class="text-muted fs--1 mb-0">Órdenes fabricación del dia cerradas</p>
                        </div>
                    </div>
                </div>
                <!-- Órdenes Abiertas (En Proceso) -->
                <div class="col-12 col-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-sync-alt" style="font-size: 30px; color: #ffc107;"></i>
                        </span>
                        <div class="ms-3">
                            <h4 id="ordenesAbiertasDia" class="mb-0">Órdenes Abiertas</h4>
                            <p class="text-muted fs--1 mb-0">Órdenes fabricación del dia abiertas</p>
                        </div>
                    </div>
                </div>
                <!-- Total de Órdenes -->
                <div class="col-12 col-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-boxes" style="font-size: 30px; color: #007bff;"></i>
                        </span>
                        <div class="ms-3">
                            <h4 id="totalOrdenesDia" class="mb-0">Total De Órdenes</h4>
                            <p class="text-muted fs--1 mb-0">Total de todas las órdenes del dia</p>
                        </div>
                    </div>
                </div>
            </div>
            <div style="height: 10px;"></div>
            <div class="container">
                <div class="row mb-4">
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasCortedia" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasSuministrodia" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasPreparadodia" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasEnsambledia" width="150" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasPulidodia" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasMediciondia" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasVisualizaciondia" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasEmpaquedia" width="150" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div style="height: 10px;"></div>
    <div class="card" style="display: none;">
            <div class="col-10 col-md-18 col-lg-12 mx-auto">
                <h1 class="progress-title">Progreso de Piezas por Dia</h1>
                <p id="chart-hour-fecha" style="font-size: 14px; color: gray;"></p> 
                <div id="chart-hour" class="chart-container"></div>
            </div>
    </div>
    <div style="height: 10px;"></div>
    <div class="card">
            <p id="grafica-tiempo-dia" style="font-size: 14px; color: gray;"></p> 
            <div id="grafica-tiempoD" class="chart-container" style="height: 400px;"></div> 
    </div>
</div>
<!--Progreso de semana-->
<div id="indicadores-semana">
    <div class="card text-center">
            <h1 class="progress-title mt-3 mb-4">Indicadores de Producción de la semana</h1>
            <div class="row justify-content-center">
                <!-- Órdenes Cerradas (Completadas) -->
                <div class="col-12 col-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-check-circle" style="font-size: 30px; color: #28a745;"></i>
                        </span>
                        <div class="ms-3">
                            <h4 id="ordenesCompletadasemana" class="mb-0">Órdenes Cerradas</h4>
                            <p class="text-muted fs--1 mb-0">Órdenes fabricación del la semana cerradas</p>
                        </div>
                    </div>
                </div>
                <!-- Órdenes Abiertas (En Proceso) -->
                <div class="col-12 col-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-sync-alt" style="font-size: 30px; color: #ffc107;"></i>
                        </span>
                        <div class="ms-3">
                            <h4 id="ordenesAbiertasemana" class="mb-0">Órdenes Abiertas</h4>
                            <p class="text-muted fs--1 mb-0">Órdenes fabricación del la semana abiertas</p>
                        </div>
                    </div>
                </div>
                <!-- Total de Órdenes -->
                <div class="col-12 col-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-boxes" style="font-size: 30px; color: #007bff;"></i>
                        </span>
                        <div class="ms-3">
                            <h4 id="totalOrdenesemana" class="mb-0">Total De Órdenes</h4>
                            <p class="text-muted fs--1 mb-0">Total de todas las órdenes del la semana</p>
                        </div>
                    </div>
                </div>
            </div>
            <div style="height: 10px;"></div>
            <div class="container">
                <div class="row mb-4">
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasCortesemana" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasSuministrosemana" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasPreparadosemana" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasEnsamblesemana" width="150" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasPulidosemana" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasMedicionsemana" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasVisualizacionsemana" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasEmpaquesemana" width="150" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <div style="height: 10px;"></div>
    <div class="card">
            <h1 class="progress-title">Progreso de la Semana</h1>
            <p id="chart-day-rango" style="font-size: 14px; color: gray;"></p>  
            <div id="chart-day" class="chart-container"></div>
    </div>
</div>
<!--progreso de la mes-->
<div id="indicadores-mes">
        <div style="height: 10px;"></div>
        <div class="card" style="display: none">
            <h1 class="progress-title">Ordenes Fabricación</h1>
            <div class="grid-container" style="display: flex; justify-content: center;">
                <div class="grid-item">
                    <h1 class="small-title"></h1>
                    <canvas id="plemasordenes" width="700" height="300"></canvas>
                </div>     
            </div>
        </div>
        <div class="card text-center">
            <h1 class="progress-title mt-3 mb-4">Indicadores de Producción del Mes</h1>
            <div class="row justify-content-center">
                <!-- Órdenes Cerradas (Completadas) -->
                <div class="col-12 col-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-check-circle" style="font-size: 30px; color: #28a745;"></i>
                        </span>
                        <div class="ms-3">
                            <h4 id="ordenesCompletadas" class="mb-0">Órdenes Cerradas</h4>
                            <p class="text-muted fs--1 mb-0">Órdenes fabricación del mes cerradas</p>
                        </div>
                    </div>
                </div>
                <!-- Órdenes Abiertas (En Proceso) -->
                <div class="col-12 col-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-sync-alt" style="font-size: 30px; color: #ffc107;"></i>
                        </span>
                        <div class="ms-3">
                            <h4 id="ordenesAbiertas" class="mb-0">Órdenes Abiertas</h4>
                            <p class="text-muted fs--1 mb-0">Órdenes fabricación del mes abiertas</p>
                        </div>
                    </div>
                </div>
                <!-- Total de Órdenes -->
                <div class="col-12 col-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                            <i class="fas fa-boxes" style="font-size: 30px; color: #007bff;"></i>
                        </span>
                        <div class="ms-3">
                            <h4 id="totalOrdenes" class="mb-0">Total De Órdenes</h4>
                            <p class="text-muted fs--1 mb-0">Total de todas las órdenes del mes</p>
                        </div>
                    </div>
                </div>
            </div>
            <div style="height: 10px;"></div>
            <div class="container">
                <div class="row mb-4">
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasCorte" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasSuministro" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasPreparado" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasEnsamble" width="150" height="150"></canvas>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasPulido" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasMedicion" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasVisualizacion" width="150" height="150"></canvas>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <div class="grid-item">
                            <h1 class="small-title"></h1>
                            <canvas id="plemasEmpaque" width="150" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="height: 10px;"></div>
        <div class="card">
            <h1 class="progress-title">Progreso del Mes</h1>
            <p id="chart-month-mes" style="font-size: 14px; color: gray;"></p> <!-- Aquí se mostrará la fecha -->
            <div id="chart-month" class="chart-container"></div>
        </div>
</div>
<hr class="hr">
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5"></script>
<script>
     /*setInterval(function() {
        location.reload();
    }, 60000);    */                                                                                                                                                                                                                                                                                                       
        
    function setupClickListener(clickId, indicadoresId, hideIds) {
        document.getElementById(clickId).addEventListener('click', function() {
            console.log('¡Se hizo clic!');

            // Ocultar todas las secciones
            hideIds.forEach(id => {
                document.getElementById(id).style.display = 'none';
            });

            // Mostrar la sección correspondiente
            let indicadores = document.getElementById(indicadoresId);
            indicadores.style.display = 'block';

            // Desplazar hacia la sección
            //indicadores.scrollIntoView({ behavior: 'smooth' });
        });
    }
    function init() { 
        document.getElementById('indicadores-dia').style.display = 'block'; 
        document.getElementById('indicadores-semana').style.display = 'none'; 
        document.getElementById('indicadores-mes').style.display = 'none';
        setupClickListener('click-dia', 'indicadores-dia', ['indicadores-semana', 'indicadores-mes']);
        setupClickListener('click-semana', 'indicadores-semana', ['indicadores-dia', 'indicadores-mes']);
        setupClickListener('click-mes', 'indicadores-mes', ['indicadores-dia', 'indicadores-semana']);
    }
    window.onload = init;
    document.addEventListener("DOMContentLoaded", function () {
        PorcentajeLlenadas();
        setInterval(PorcentajeLlenadas, 30000);
        cargarIndicadores("{{ route('indicadores-cedia') }}", [
            "plemasCortedia", "plemasSuministrodia", "plemasPreparadodia", "plemasEnsambledia",
            "plemasPulidodia", "plemasMediciondia", "plemasVisualizaciondia", "plemasEmpaquedia"
        ]);

        cargarIndicadores("{{ route('indicadores.CE') }}", [
            "plemasCorte", "plemasSuministro", "plemasPreparado", "plemasEnsamble",
            "plemasPulido", "plemasMedicion", "plemasVisualizacion", "plemasEmpaque"
        ]);
        cargarIndicadores("{{ route('indicadores.CEsemana') }}", [
            "plemasCortesemana", "plemasSuministrosemana", "plemasPreparadosemana", "plemasEnsamblesemana",
            "plemasPulidosemana", "plemasMedicionsemana", "plemasVisualizacionsemana", "plemasEmpaquesemana"
        ]);
    });
    function cargarIndicadores(ruta, estaciones) {
        fetch(ruta)
            .then(response => response.json())
            .then(data => {
                estaciones.forEach(id => {
                    let canvas = document.getElementById(id);
                    if (!canvas) return;

                
                    let container = canvas.parentElement;
                    container.style.display = "flex";
                    container.style.alignItems = "center";
                    container.style.gap = "1px"; 

                    let ctx = canvas.getContext("2d");
                    canvas.style.width = "95px";
                    canvas.style.height = "97px";

                    let completado = data[id] ? data[id].completado : 0;
                    let pendiente = data[id] ? data[id].pendiente : 0;
                    let totalOrdenes = data[id] ? data[id].totalOrdenes : 0; 

                    let porcentajeCompletado = totalOrdenes > 0 ? ((completado / totalOrdenes) * 100).toFixed(2) : 0;
                    let porcentajePendiente = totalOrdenes > 0 ? (((totalOrdenes - completado) / totalOrdenes) * 100).toFixed(2) : 0;

                    let infoDiv = container.querySelector(".info-grafico");
                    if (!infoDiv) {
                        infoDiv = document.createElement("div");
                        infoDiv.classList.add("info-grafico");
                        container.appendChild(infoDiv);
                    }

                    let nombreEstacion = id.replace("plemas", "").replace("semana", "").replace("dia", "");

                    infoDiv.innerHTML = ` 
                        <strong>${nombreEstacion}</strong><br>
                        Cerradas: <span style="color: #28a745;">${completado}/${totalOrdenes} (${porcentajeCompletado}%)</span><br>
                        Abiertas: <span style="color: #FFC107;">${totalOrdenes - completado}/${totalOrdenes} (${porcentajePendiente}%)</span>
                    `;

                    infoDiv.style.fontSize = "14px";

                    new Chart(ctx, {
                        type: "doughnut",
                        data: {
                            labels: ["Cerradas", "Abiertas"],
                            datasets: [{
                                data: (totalOrdenes === completado) ? [completado, 0] : [completado, totalOrdenes - completado],
                                backgroundColor: (totalOrdenes === completado) ? ["#28a745"] : ["#28a745", "#FFC107"],
                                cutout: "70%"
                            }]
                        },
                        options: {
                            responsive: false,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    align: 'center'
                                },
                                tooltip: {
                                    enabled: true,
                                    mode: 'index',
                                    intersect: false
                                }
                            },
                            elements: {
                                arc: {
                                    borderWidth: 0
                                }
                            }
                        }
                    });

                });
            })
            .catch(error => console.log('Error al cargar los datos:', error));
    }
    function generarGrafico(url, containerId, itemName) {
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const datasetSource = [[itemName, ...data.labels]];
                data.series.forEach((serie) => {
                    datasetSource.push([serie.name, ...serie.data]);
                });
                const fechaContainer = document.getElementById(`${containerId}-fecha`);
                const rangoContainer = document.getElementById(`${containerId}-rango`);
                const mesContainer = document.getElementById(`${containerId}-mes`);
                if (mesContainer) {
                    mesContainer.classList.add('center-text'); 
                    mesContainer.textContent = ` ${data.mes}`;
                }
                if (fechaContainer) {
                    fechaContainer.classList.add('center-text'); 
                    fechaContainer.textContent = `${data.fecha}`;
                }
                if (rangoContainer) {
                    rangoContainer.classList.add('center-text'); 
                    rangoContainer.textContent = ` ${data.rangoSemana}`;
                }
                const option = {
                tooltip: { trigger: 'axis' },
                legend: { left: '5%' },
                dataset: { source: datasetSource },
                xAxis: { type: 'category' },
                yAxis: { gridIndex: 0 },
                grid: {
                    left: containerId === 'chart-month' ? '5%' : '50%',
                    right: containerId === 'chart-month' ? '50%' : '5%',
                    bottom: '10%',
                    containLabel: true
                },
                series: data.series.map(() => ({
                    type: 'line',
                    smooth: true,
                    seriesLayoutBy: 'row',
                    emphasis: { focus: 'series' }
                })).concat([
                    {
                        type: 'pie',
                        id: 'pie',
                        radius: '35%',
                        center: containerId === 'chart-month' ? ['75%', '50%'] : ['20%', '50%'],
                        emphasis: { focus: 'self' },
                        label: {
                            formatter: `{b}: {@[${data.labels[0]}]} ({d}%)`
                        },
                        encode: {
                            itemName: itemName,
                            value: data.labels[0],
                            tooltip: data.labels[0]
                        }
                    }
                ]),
                toolbox: {
                    feature: {
                        saveAsImage: {
                            name: `${data.fecha || ''}${data.rangoSemana || ''}${data.mes || ''}` // Verifica que los valores no sean undefined
                        }
                    }
                }
            };

                const chart = echarts.init(document.getElementById(containerId));

                chart.on('updateAxisPointer', function (event) {
                    const xAxisInfo = event.axesInfo[0];
                    if (xAxisInfo) {
                        const dimension = xAxisInfo.value + 1;
                        chart.setOption({
                            series: [{
                                id: 'pie',
                                label: { formatter: `{b}: {@[${dimension}]} ({d}%)` },
                                encode: { value: dimension, tooltip: dimension }
                            }]
                        });
                    }
                });

                chart.setOption(option);
            })
            .catch(error => {
                console.log(`Error al cargar los datos del gráfico (${itemName}):`, error);
            });
    }
    // Llamadas a la función para generar gráficos
    generarGrafico("{{ route('tablas.semana') }}", "chart-day", "Día");
    generarGrafico("{{ route('tablas.mes') }}", "chart-month", "Semana");
    generarGrafico("{{ route('tablas.hora') }}", "chart-hour", "Mes");
    //peticiones de wizarp
    $(document).ready(function() {
        // Petición para el primer endpoint
        $.ajax({
            url: "{{ route('wizarpmes.dashboard') }}", 
           
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#ordenesCompletadas').text('Órdenes Cerradas: ' + data.ordenesCompletadas);
                $('#ordenesAbiertas').text('Órdenes Abiertas: ' + data.ordenesAbiertas);
                $('#totalOrdenes').text('Total de Órdenes: ' + data.totalOrdenes);
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
            }
        });

        // Petición para el segundo endpoint
        $.ajax({
            url: "{{ route('wizarpdia.dashboard') }}", 
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#ordenesCompletadasDia').text('Órdenes Cerradas: ' + data.ordenesCompletadas);
                $('#ordenesAbiertasDia').text('Órdenes Abiertas: ' + data.ordenesAbiertas);
                $('#totalOrdenesDia').text('Total de Órdenes: ' + data.totalOrdenes);
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
            }
        });

        // Petición para el tercer endpoint
        $.ajax({
            url: "{{ route('wizarp.dashboard') }}", 
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#ordenesCompletadasemana').text('Órdenes Cerradas: ' + data.ordenesCompletadas);
                $('#ordenesAbiertasemana').text('Órdenes Abiertas: ' + data.ordenesAbiertas);
                $('#totalOrdenesemana').text('Total de Órdenes: ' + data.totalOrdenes);
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
            }
        });
    });
    fetch("{{ route('orden.cerredas') }}")
    .then(response => response.json())
    .then(data => {
        const id = "plemasordenes"; 
        let canvas = document.getElementById(id);
        if (!canvas) return;

        let container = canvas.parentElement;
        container.style.display = "flex";
        container.style.alignItems = "center";
        container.style.gap = "10px";

        let myChart = echarts.init(canvas);

        // Obtener datos del backend
        let completadas = data.ordenesCompletadas.length > 0 ? data.ordenesCompletadas.length : 0;
        let abiertas = data.ordenesAbiertas.length > 0 ? data.ordenesAbiertas.length : 0;
        let totalOrdenes = data.totalOrdenes > 0 ? data.totalOrdenes : (completadas + abiertas);

        // Calcular porcentajes
        let porcentajeCompletadas = totalOrdenes > 0 ? ((completadas / totalOrdenes) * 100).toFixed(2) : 0;
        let porcentajeAbiertas = totalOrdenes > 0 ? (((totalOrdenes - completadas) / totalOrdenes) * 100).toFixed(2) : 0;

        // Mostrar los datos en el indicador
        let infoDiv = container.querySelector(".info-grafico");
        if (!infoDiv) {
            infoDiv = document.createElement("div");
            infoDiv.classList.add("info-grafico");
            container.appendChild(infoDiv);
        }

        infoDiv.innerHTML = `
            <strong>Órdenes</strong><br>
            Cerradas: <span style="color: #28a745;">${completadas}/${totalOrdenes} (${porcentajeCompletadas}%)</span><br>
            Abiertas: <span style="color: #ffc107;">${totalOrdenes - completadas}/${totalOrdenes} (${porcentajeAbiertas}%)</span><br>
            Órdenes de Fabricación: <span style="color: #dc3545; white-space: nowrap;">${totalOrdenes}</span>
        `;

        // Definir datos del gráfico
        let dataSeries = [
            { value: abiertas, name: 'Abiertas', itemStyle: { color: '#ffc107' } } // Amarillo para Abiertas
        ];

        // Solo agregar "Cerradas" si hay alguna cerrada
        if (completadas > 0) {
            dataSeries.unshift({ value: completadas, name: 'Cerradas', itemStyle: { color: '#28a745' } }); // Verde para Completadas
        }

        // Definir opciones del gráfico
        let option = {
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left'
            },
            series: [
                {
                    name: 'Órdenes',
                    type: 'pie',
                    radius: '80%',
                    data: dataSeries,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        // Renderizar gráfico
        myChart.setOption(option);
    });

    document.addEventListener("DOMContentLoaded", function () {
    fetch("{{ route('dashboard.indicador') }}")
        .then(response => response.json())
        .then(data => {
            console.log("Datos recibidos:", data); // Depuración

            // Asignar valores a las variables, asegurando que no sean null o undefined
            const porcentajeCerradas = parseFloat(data.porcentajeCerradas) || 0;  
            const porcentajeCompletadas = parseFloat(data.PorcentajeCompletadas) || 0;  
            const totalCompletadas = parseInt(data.TotalOfCompletadas) || 0;  
            const faltanteTotal = parseInt(data.faltanteTotal) || 0;
            const Estimadopiezas = parseFloat(data.Estimadopiezas) || 0;
            const Cantidadpersonas = parseInt(data.Cantidadpersonas) || 0;

            // Mostrar los valores en la interfaz
            document.getElementById("piezas").textContent = Estimadopiezas.toFixed();
            document.getElementById("Cantidad").textContent = Cantidadpersonas;
            document.getElementById("Porcentajeplaneada").textContent = porcentajeCompletadas.toFixed(2);  
            document.getElementById("Porcentajefaltante").textContent = porcentajeCerradas.toFixed(2);  
            document.getElementById("Piezaplaneadas").textContent = totalCompletadas;
            document.getElementById("Piezafaltantes").textContent = faltanteTotal;

            // Definir color del gráfico basado en el porcentaje completado
            let color = "#007BFF"; // Color por defecto (azul)
            if (totalCompletadas === 0 && faltanteTotal === 0) {
                color = "#D3D3D3"; // Gris si no hay datos
            } else if (porcentajeCompletadas > 1) {
                color = "#FF0000"; // Rojo si el porcentaje supera 100%
            } else if (porcentajeCompletadas > 0.9) {
                color = "#FFA500"; // Naranja (90-100%)
            } else if (porcentajeCompletadas > 0.8) {
                color = "#FFFF00"; // Amarillo (80-90%)
            }

            // Inicializar gráfico si el contenedor existe
            let chartContainer = document.getElementById("PorcentajePlaneacion");
            if (!chartContainer) {
                console.error("Elemento 'PorcentajePlaneacion' no encontrado en el DOM.");
                return;
            }

            let myChart = echarts.init(chartContainer);
            let option = {
                tooltip: { trigger: 'item' },
                legend: { show: false },
                series: [
                    {
                        name: 'Planeación',
                        type: 'pie',
                        radius: ['60%', '70%'],
                        avoidLabelOverlap: false,
                        itemStyle: {
                            borderRadius: 10,
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        label: {
                            show: true,
                            position: 'center',
                            formatter: totalCompletadas === 0 && faltanteTotal === 0 
                                ? '0.00' 
                                : `${porcentajeCompletadas.toFixed(2)}`,
                            fontSize: 20,
                            fontWeight: 'bold'
                        },
                        labelLine: { show: false },
                        data: totalCompletadas === 0 && faltanteTotal === 0
                            ? [{ value: 1, name: 'Sin datos', itemStyle: { color: "#D3D3D3" } }]
                            : [
                                { value: totalCompletadas, name: 'Total Completados', itemStyle: { color: color } },
                                { value: faltanteTotal, name: 'Total Faltante', itemStyle: { color: '#D3D3D3' } }
                            ]
                    }
                ]
            };

            myChart.setOption(option);
        })
        .catch(error => console.error("Error al obtener los datos:", error));
});

// Función para convertir segundos a formato H:M:S
function convertirSegundosAHMS(segundos) {
    var horas = Math.floor(segundos / 3600);
    var minutos = Math.floor((segundos % 3600) / 60);
    var segundosRestantes = segundos % 60;
    return `${horas}h ${minutos}m ${segundosRestantes}s`;
}
function crearGrafico(url, chartDomId) {
    var chartDom = document.getElementById(chartDomId);
    var myChart = echarts.init(chartDom);
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Mapeo de los datos
            var tiemposProduccionData = [];
            var tiemposMuertosData = [];
            var areas = Object.entries(data.numeroPRO).map(([nombre,valor]) => `${nombre} 'No'${valor}`);; // Reemplaza con los nombres de las áreas correctas según el id

            // Crear un diccionario para los tiempos de producción, utilizando Areas_id como clave
            var produccionMap = {};
            data.produccion.forEach(item => {
                produccionMap[item.Areas_id] = parseInt(item.tiempoProduccionActual, 10);
            });

            // Crear un diccionario para los tiempos muertos
            var finalResultMap = {};
            data.finalResult.forEach(item => {
                var areasId = item.Areas_id.split(',').map(area => parseInt(area, 10)); // Convierte el string Areas_id a un array de ids
                areasId.forEach(id => {
                    finalResultMap[id] = item.TiempoMuerto; // Asocia cada área con su tiempo muerto
                });
            });

            // Llenar los arrays con los datos
            areas.forEach(area => {
                var areaId = areas.indexOf(area) + 3; // Asume que las áreas comienzan en 3
                var tiempoProduccion = produccionMap[areaId] || 0;
                var tiempoMuerto = finalResultMap[areaId] || 0;

                tiemposProduccionData.push(tiempoProduccion);
                tiemposMuertosData.push(tiempoMuerto);
            });

            // Preparar los datos para la gráfica de pastel
            var tiemposPorPieza = data.tiemposPorPieza.map(item => {
                // Convertir los segundos a horas, minutos y segundos
                var tiempoPorPieza = item.TiempoPorPieza;
                var horas = Math.floor(tiempoPorPieza / 3600);
                var minutos = Math.floor((tiempoPorPieza % 3600) / 60);
                var segundos = tiempoPorPieza % 60;

                return {
                    name: item.Area,
                    value: item.TiempoPorPieza,
                    label: {
                        formatter: `{b}: ${horas}h ${minutos}m ${segundos}s`
                    }
                };
            });

            var option = {
                title: [
                    {
                        text: 'Gráfica de Tiempo por Piezas',
                        left: '78%',
                        top: '2%',
                        textAlign: 'center',
                        textStyle: {
                            fontSize: 10,
                            fontWeight: 'bold'
                        }
                    },
                    {
                        text: 'Cantidad de Piezas Registradas: ' + data.TotalPiezas,
                        left: '50%', 
                        top: '-1%', 
                        textAlign: 'center',
                        textStyle: {
                            fontSize: 11,
                            fontWeight: 'bold',
                            color: '#333'
                        }
                    },
                    {
                        text: 'Tiempo de Producción vs Tiempo Muerto',
                        left: '13%',
                        top: '2%',
                        textStyle: {
                            fontSize: 10,
                            fontWeight: 'bold'
                        }
                    }
                ],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' },
                    formatter: function (params) {
                        return params.map(item => `${item.seriesName}: ${convertirSegundosAHMS(item.value)}`).join('<br>');
                    }
                },
                legend: {
                    data: ['Tiempo Muerto', 'Tiempo de Producción'],
                    left: '13%',
                    top: '7%',
                    textStyle: {
                        fontSize: 8,
                    }
                },
                grid: {
                    left: '5%',
                    right: '44%',
                    bottom: '10%',
                    top: '15%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value',
                    axisLabel: {
                        formatter: convertirSegundosAHMS,
                        fontSize: 10,
                    },
                    splitLine: {
                        show: true,
                        lineStyle: { type: 'dashed', color: '#ccc' }
                    }
                },
                yAxis: {
                    type: 'category',
                    data: areas,
                    axisLabel: { fontSize: 10, margin: 50 },
                    axisTick: { show: false },
                    axisLine: { show: true, lineStyle: { color: '#000', width: 2 } }
                },
                series: [
                    {
                        name: 'Tiempo Muerto',
                        type: 'bar',
                        stack: 'total',
                        label: {
                            show: true,
                            position: 'inside',
                            formatter: params => convertirSegundosAHMS(params.value),
                            fontSize: 9
                        },
                        itemStyle: { color: '#FF6F61' },
                        data: tiemposMuertosData
                    },
                    {
                        name: 'Tiempo de Producción',
                        type: 'bar',
                        stack: 'total',
                        label: {
                            show: true,
                            position: 'inside',
                            formatter: params => convertirSegundosAHMS(params.value),
                            fontSize: 9
                        },
                        itemStyle: { color: '#3B8F82' },
                        data: tiemposProduccionData
                    },
                    {
                        type: 'pie',
                        radius: '35%',
                        center: ['78%', '50%'],
                        data: tiemposPorPieza,
                        label: {
                            show: true,
                            formatter: '{b}: {c} ({d}%)',
                            fontSize: 10
                        }
                    }
                ],
                toolbox: {
                    feature: {
                        saveAsImage: {
                            name: 'tiempos_de_produccion_' + new Date().toLocaleDateString().replace(/\//g, '_'),
                            type: 'png'
                        }
                    }
                }
            };

            myChart.setOption(option);
        })
        .catch(error => console.log('Error al cargar los datos del gráfico:', error));
}
crearGrafico("{{ route('graficastiempoMuerto') }}", 'grafica-tiempoD');









   /* function GraficasTiempo(url, containerId, itemName, fechaId) {
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Asignar los valores informativos en los elementos del HTML
            const tiempodeareasContainer = document.getElementById('tiempodeareas');
            const tiempoprmedioPiezasContainer = document.getElementById('tiempoprmedioPiezas');
            const cantidadTotalContainer = document.getElementById('cantidadTotal');

            if (tiempodeareasContainer) {
                tiempodeareasContainer.textContent = data.tiempodeareas;  // Muestra el tiempo total de áreas
            }

            if (tiempoprmedioPiezasContainer) {
                tiempoprmedioPiezasContainer.textContent = data.tiempoprmedioPiezas;  // Muestra el tiempo promedio por pieza
            }

            if (cantidadTotalContainer) {
                cantidadTotalContainer.textContent = data.cantidadTotal;  // Muestra la cantidad total de piezas
            }

            const option = {
                title: {
                    text: 'Tiempo por Áreas',
                    subtext: 'Datos de Producción',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item',
                    formatter: function (params) {
                        // Verificar si `params.data` tiene la propiedad `formatted` y devolverla
                        if (params.data && params.data.formatted) {
                            return `${params.name}: ${params.data.formatted}`;
                        }
                        return `${params.name}: ${params.value} segundos`;  // Fallback a los segundos si no hay `formatted`
                    }
                },
                legend: {
                    orient: 'vertical',
                    left: 'left'
                },
                series: [
                    {
                        name: 'Tiempo por Área',
                        type: 'pie',
                        radius: '50%',
                        data: data.graficoData.map(item => ({
                            name: item.name,
                            value: item.value,  // Los segundos reales
                            formatted: item.formatted  // Asegúrate de que `formatted` esté presente
                        })),
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ],
                toolbox: {
                    feature: {
                        saveAsImage: {
                            name: `${data.tiempodeareas || ''}${data.rangoSemana || ''}${data.mes || ''}`
                        }
                    }
                }
            };

            const chart = echarts.init(document.getElementById(containerId));
            chart.setOption(option);
        })
        .catch(error => {
            console.log(`Error al cargar los datos del gráfico (${itemName}):`, error);
        });
    }


    GraficasTiempo("{{ route('graficastiempo') }}", "grafica-tiempoD", "DIa", "grafica-tiempo-Dia");
    */

        /*
        /////////////////////////
            /*
            // Obtener progreso general del dashboard
            function fetchProgresoDash() {
                fetch("{{ route('progreso.dash') }}")
                    .then(response => response.json())
                    .then(data => {
                        console.log("Progreso General:", data.progreso);
                        if (data && data.progreso) {
                            updateProgressBars(data.progreso); // Actualizar las barras con los datos generales
                        } else {
                            console.log("No se recibieron datos de progreso válidos.");
                        }
                    })
                    .catch(error => {
                        console.log('Error obteniendo datos de progreso:', error);
                    });
            }
            // Actualizar las barras de progreso del dashboard
            function updateProgressBars(progreso) {
                Object.keys(progreso).forEach(area => {
                    const porcentaje = progreso[area];
                    let progressBarGeneral = document.getElementById(`progress-${area}`);
                    if (progressBarGeneral) {
                        animateProgressBar(progressBarGeneral, porcentaje);
                    }
                });
            }

                fetch("{{ route('orden.cerredas') }}")
                .then(response => response.json())
                .then(data => {
                    const id = "plemasordenes"; 
                    let canvas = document.getElementById(id);
                    if (!canvas) return;

                    let container = canvas.parentElement;
                    container.style.display = "flex";
                    container.style.alignItems = "center";
                    container.style.gap = "10px";

                    let myChart = echarts.init(canvas);

                    // Obtener datos del backend
                    let completadas = data.ordenesCompletadas.length > 0 ? data.ordenesCompletadas.length : 0;
                    let abiertas = data.ordenesAbiertas.length > 0 ? data.ordenesAbiertas.length : 0;
                    let totalOrdenes = data.totalOrdenes > 0 ? data.totalOrdenes : (completadas + abiertas);

                    // Calcular porcentajes
                    let porcentajeCompletadas = totalOrdenes > 0 ? ((completadas / totalOrdenes) * 100).toFixed(2) : 0;
                    let porcentajeAbiertas = totalOrdenes > 0 ? (((totalOrdenes - completadas) / totalOrdenes) * 100).toFixed(2) : 0;

                    // Mostrar los datos en el indicador
                    let infoDiv = container.querySelector(".info-grafico");
                    if (!infoDiv) {
                        infoDiv = document.createElement("div");
                        infoDiv.classList.add("info-grafico");
                        container.appendChild(infoDiv);
                    }

                    infoDiv.innerHTML = `
                        <strong>Órdenes</strong><br>
                        Cerradas: <span style="color: #28a745;">${completadas}/${totalOrdenes} (${porcentajeCompletadas}%)</span><br>
                        Abiertas: <span style="color: #ffc107;">${totalOrdenes - completadas}/${totalOrdenes} (${porcentajeAbiertas}%)</span><br>
                        Órdenes de Fabricacion: <span style="color: #dc3545; white-space: nowrap;">${totalOrdenes}</span>

                    `;


                    // Definir opciones del gráfico
                    let option = {
                        tooltip: {
                            trigger: 'item'
                        },
                        legend: {
                            orient: 'vertical',
                            left: 'left'
                        },
                        series: [
                            {
                                name: 'Órdenes',
                                type: 'pie',
                                radius: '80%',
                                data: [
                                    { value: completadas, name: 'Cerradas', itemStyle: { color: '#28a745' } }, // Verde para Completadas
                                    { value: abiertas, name: 'Abiertas', itemStyle: { color: '#ffc107' } },  // Amarillo para Abiertas
                                    
                                ],
                                emphasis: {
                                    itemStyle: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };

                    // Renderizar gráfico
                    myChart.setOption(option);
                })
                .catch(error => console.log('Error al cargar los datos:', error));*/
            /*
            function cargarOrdenesCerradas() {
                $.ajax({
                    url: "{{ route('tabla.abiertas') }}",
                    method: 'GET',
                    success: function (data) {
                        // Mostrar el porcentaje de órdenes cerradas
                        $('#ordenFabricacionNumero').text(data.ordenesAbiertasCount); 

                        // Llenar la tabla con los datos
                        var tabla = $('#orden-list');
                        tabla.empty(); 

                        // Iterar sobre los datos y agregar filas a la tabla
                        data.ordenes.forEach(function (orden) {
                            var fila = `<tr>
                                <td>${orden.OrdenFabricacion}</td>
                                <td>${orden.Articulo}</td>
                                <td>${orden.Descripcion}</td>
                                <td>${orden.CantidadTotal}</td>
                                <td>${orden.SumaTotalcantidad_partida}</td>
                                
                            </tr>`;
                            tabla.append(fila);
                        });
                    },
                    error: function (error) {
                        console.log("Error al cargar los datos: ", error);
                    }
                });
            }

            function cargarOrdenesCompletas() {
                $.ajax({
                    url: "{{ route('tabla.completas') }}",
                    method: 'GET',
                    success: function (data) {
                        // Mostrar el porcentaje de órdenes cerradas
                        $('#ordenesCompletadasNumero').text(data.retrabajo); 

                        // Llenar la tabla con los datos
                        var tabla = $('#ordenes-completadas-list');
                        tabla.empty(); 

                        data.ordenes.forEach(function (orden) {
                            const collapseId = `collapse-${orden.OrdenFabricacion}`;

                            // Crear fila principal
                            const fila = document.createElement('tr');
                            fila.innerHTML = `
                                <td>${orden.OrdenFabricacion}</td>
                                <td>${orden.Articulo}</td>
                                <td>${orden.Descripcion}</td>
                                <td>${orden.CantidadTotal}</td>
                                <td>${orden.cantidad_partida}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm btn-ver-mas" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                                        Ver más
                                    </button>
                                </td>
                            `;

                            // Crear fila de detalles con acordeón Bootstrap
                            const detailRow = document.createElement('tr');
                            detailRow.innerHTML = `
                                <td colspan="6">
                                    <div class="collapse" id="${collapseId}">
                                        <div class="card">
                                            <div class="card-body">
                                                <strong>Tiempos de Etapas</strong><br>
                                                <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                                                    ${createEstacionCard('Corte', orden.TiempoCorte, orden.FinCorte)}
                                                    ${createEstacionCard('Suministro', orden.TiempoSuministro, orden.FinSuministro)}
                                                    ${createEstacionCard('Preparado', orden.TiempoPreparado, orden.FinPreparado)}
                                                    ${createEstacionCard('Ensamble', orden.TiempoEnsamble, orden.FinEnsamble)}
                                                    ${createEstacionCard('Pulido', orden.TiempoPulido, orden.FinPulido)}
                                                    ${createEstacionCard('Medición', orden.TiempoMedicion, orden.FinMedicion)}
                                                    ${createEstacionCard('Visualización', orden.TiempoVisualizacion, orden.FinVisualizacion)}
                                                    ${createEstacionCard('Empaque', orden.TiempoAbierto, orden.FinAbierto)}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            `;

                            // Agregar filas a la tabla
                            tabla.append(fila);
                            tabla.append(detailRow);
                        });
                    },
                    error: function (error) {
                        console.log("Error al cargar los datos: ", error);
                    }
                });
            }

            // Obtener progreso de órdenes de fabricación
            function fetchProgresoOF() {
                fetch("{{ route('of.progreso') }}")
                    .then(response => response.json())
                    .then(data => {
                        console.log("Progreso Órdenes de Fabricación:", data.progreso);
                        if (data.progreso) {
                            updateOFProgressBars(data.progreso); 
                        } else {
                            console.log("No se recibieron datos de progreso de orden de fabricación.");
                        }
                    })
                    .catch(error => {
                        console.log('Error obteniendo datos de progreso de orden de fabricación:', error);
                    });
            }

            function updateOFProgressBars(progreso) {
                Object.keys(progreso).forEach(orden => {
                    const progresoOrden = progreso[orden].detalle;
                    Object.keys(progresoOrden).forEach(areaName => {
                        const porcentaje = progresoOrden[areaName];
                        let progressBarGeneral = document.getElementById(`step-${areaName}`);
                        if (progressBarGeneral) {
                            animateProgressBar(progressBarGeneral, porcentaje);
                        }
                    });
                });
            }

            function animateProgressBar(bar, porcentaje) {
                bar.style.width = `${porcentaje}%`;
                bar.innerHTML = `${porcentaje}%`;
            }

            // Animar las barras de progreso
            function animateProgressBar(progressBar, percentage) {
                progressBar.style.width = `${percentage}%`;
                progressBar.setAttribute('aria-valuenow', percentage);
                progressBar.textContent = `${percentage}%`;
            }


            // Llamadas iniciales a las funciones de progreso
            fetchProgresoDash(); 
            fetchProgresoOF(); 
            $(document).ready(function () {
                cargarOrdenesCerradas();
                cargarOrdenesCompletas();
            });
            function generarGrafico(url, containerId, itemName) {
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        const datasetSource = [[itemName, ...data.labels]];

                        data.series.forEach((serie) => {
                            datasetSource.push([serie.name, ...serie.data]);
                        });

                        // Asignar valores a los elementos del HTML
                        const fechaContainer = document.getElementById(`${containerId}-fecha`);
                        const rangoContainer = document.getElementById(`${containerId}-rango`);
                        const mesContainer = document.getElementById(`${containerId}-mes`);
                        
                        if(mesContainer){
                            mesContainer.textContent = ` ${data.mes}`;
                        }

                        if (fechaContainer) {
                            fechaContainer.textContent = ` ${data.fecha}`;
                        }

                        if (rangoContainer) {
                            rangoContainer.textContent = ` ${data.rangoSemana}`;
                        }
                        const option = {
                        tooltip: { trigger: 'axis' },
                        legend: { left: '5%' },
                        dataset: { source: datasetSource },
                        xAxis: { type: 'category' },
                        yAxis: { gridIndex: 0 },
                        grid: {
                            left: containerId === 'chart-month' ? '5%' : '50%',
                            right: containerId === 'chart-month' ? '50%' : '5%',
                            bottom: '10%',
                            containLabel: true
                        },
                        series: data.series.map(() => ({
                            type: 'line',
                            smooth: true,
                            seriesLayoutBy: 'row',
                            emphasis: { focus: 'series' }
                        })).concat([
                            {
                                type: 'pie',
                                id: 'pie',
                                radius: '35%',
                                center: containerId === 'chart-month' ? ['75%', '50%'] : ['20%', '50%'],
                                emphasis: { focus: 'self' },
                                label: {
                                    formatter: `{b}: {@[${data.labels[0]}]} ({d}%)`
                                },
                                encode: {
                                    itemName: itemName,
                                    value: data.labels[0],
                                    tooltip: data.labels[0]
                                }
                            }
                        ]),
                        toolbox: {
                            feature: {
                                saveAsImage: {
                                    name: `${data.fecha || ''}${data.rangoSemana || ''}${data.mes || ''}` // Verifica que los valores no sean undefined
                                }
                            }
                        }
                    };

                        const chart = echarts.init(document.getElementById(containerId));

                        chart.on('updateAxisPointer', function (event) {
                            const xAxisInfo = event.axesInfo[0];
                            if (xAxisInfo) {
                                const dimension = xAxisInfo.value + 1;
                                chart.setOption({
                                    series: [{
                                        id: 'pie',
                                        label: { formatter: `{b}: {@[${dimension}]} ({d}%)` },
                                        encode: { value: dimension, tooltip: dimension }
                                    }]
                                });
                            }
                        });

                        chart.setOption(option);
                    })
                    .catch(error => {
                        console.log(`Error al cargar los datos del gráfico (${itemName}):`, error);
                    });
            }
            // Llamadas a la función para generar gráficos
            generarGrafico("{{ route('tablas.semana') }}", "chart-day", "Día");
            generarGrafico("{{ route('tablas.mes') }}", "chart-month", "Semana");
            generarGrafico("{{ route('tablas.hora') }}", "chart-hour", "Mes");

            $(document).ready(function() {
                // Petición para el primer endpoint
                $.ajax({
                    url: "{{ route('wizarp.dashboard') }}", 
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#ordenesCompletadas').text('Órdenes Cerradas: ' + data.ordenesCompletadas);
                        $('#ordenesAbiertas').text('Órdenes Abiertas: ' + data.ordenesAbiertas);
                        $('#totalOrdenes').text('Total de Órdenes: ' + data.totalOrdenes);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error: ' + error);
                    }
                });

                // Petición para el segundo endpoint
                $.ajax({
                    url: "{{ route('wizarpdia.dashboard') }}", 
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#ordenesCompletadasDia').text('Órdenes Cerradas: ' + data.ordenesCompletadas);
                        $('#ordenesAbiertasDia').text('Órdenes Abiertas: ' + data.ordenesAbiertas);
                        $('#totalOrdenesDia').text('Total de Órdenes: ' + data.totalOrdenes);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error: ' + error);
                    }
                });

                // Petición para el tercer endpoint
                $.ajax({
                    url: "{{ route('wizarpmes.dashboard') }}", 
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#ordenesCompletadasemana').text('Órdenes Cerradas: ' + data.ordenesCompletadas);
                        $('#ordenesAbiertasemana').text('Órdenes Abiertas: ' + data.ordenesAbiertas);
                        $('#totalOrdenesemana').text('Total de Órdenes: ' + data.totalOrdenes);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error: ' + error);
                    }
                });
            });





            document.addEventListener("DOMContentLoaded", function () {
                cargarIndicadores("{{ route('indicadores-cedia') }}", [
                    "plemasCortedia", "plemasSuministrodia", "plemasPreparadodia", "plemasEnsambledia",
                    "plemasPulidodia", "plemasMediciondia", "plemasVisualizaciondia", "plemasEmpaquedia"
                ]);

                cargarIndicadores("{{ route('indicadores.CE') }}", [
                    "plemasCorte", "plemasSuministro", "plemasPreparado", "plemasEnsamble",
                    "plemasPulido", "plemasMedicion", "plemasVisualizacion", "plemasEmpaque"
                ]);
                cargarIndicadores("{{ route('indicadores.CEsemana') }}", [
                    "plemasCortesemana", "plemasSuministrosemana", "plemasPreparadosemana", "plemasEnsamblesemana",
                    "plemasPulidosemana", "plemasMedicionsemana", "plemasVisualizacionsemana", "plemasEmpaquesemana"
                ]);
            });

            function cargarIndicadores(ruta, estaciones) {
                fetch(ruta)
                    .then(response => response.json())
                    .then(data => {
                        estaciones.forEach(id => {
                            let canvas = document.getElementById(id);
                            if (!canvas) return;

                        
                            let container = canvas.parentElement;
                            container.style.display = "flex";
                            container.style.alignItems = "center";
                            container.style.gap = "1px"; 

                            let ctx = canvas.getContext("2d");
                            canvas.style.width = "95px";
                            canvas.style.height = "97px";

                            let completado = data[id] ? data[id].completado : 0;
                            let pendiente = data[id] ? data[id].pendiente : 0;
                            let totalOrdenes = data[id] ? data[id].totalOrdenes : 0; 

                            let porcentajeCompletado = totalOrdenes > 0 ? ((completado / totalOrdenes) * 100).toFixed(2) : 0;
                            let porcentajePendiente = totalOrdenes > 0 ? (((totalOrdenes - completado) / totalOrdenes) * 100).toFixed(2) : 0;

                            let infoDiv = container.querySelector(".info-grafico");
                            if (!infoDiv) {
                                infoDiv = document.createElement("div");
                                infoDiv.classList.add("info-grafico");
                                container.appendChild(infoDiv);
                            }

                            let nombreEstacion = id.replace("plemas", "").replace("semana", "").replace("dia", "");

                            infoDiv.innerHTML = ` 
                                <strong>${nombreEstacion}</strong><br>
                                Cerradas: <span style="color: #28a745;">${completado}/${totalOrdenes} (${porcentajeCompletado}%)</span><br>
                                Abiertas: <span style="color: #FFC107;">${totalOrdenes - completado}/${totalOrdenes} (${porcentajePendiente}%)</span>
                            `;

                            infoDiv.style.fontSize = "14px";

                            new Chart(ctx, {
                                type: "doughnut",
                                data: {
                                    labels: ["Cerradas", "Abiertas"],
                                    datasets: [{
                                        data: (totalOrdenes === completado) ? [completado, 0] : [completado, totalOrdenes - completado],
                                        backgroundColor: (totalOrdenes === completado) ? ["#28a745"] : ["#28a745", "#FFC107"],
                                        cutout: "70%"
                                    }]
                                },
                                options: {
                                    responsive: false,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            align: 'center'
                                        },
                                        tooltip: {
                                            enabled: true,
                                            mode: 'index',
                                            intersect: false
                                        }
                                    },
                                    elements: {
                                        arc: {
                                            borderWidth: 0
                                        }
                                    }
                                }
                            });

                        });
                    })
                    .catch(error => console.log('Error al cargar los datos:', error));
            }*/
        
    function PorcentajeLlenadas(){
        fecha=$('#FiltroOF_Fecha_table2').val();
        $.ajax({
                url: "{{route('CapacidadProductiva')}}", 
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                },
                success: function(response) {
                    color="#007BFF";
                    PorcentajeFaltante=0;
                    if(response.PorcentajePlaneada>80){
                        color='#FFFF00';
                    } 
                    if(response.PorcentajePlaneada>90){
                        color='#FFA500';
                    } 
                    if(response.PorcentajePlaneada>100){
                        color='#FF0000';
                    }
                    if(response.PorcentajeFaltante>0){
                        PorcentajeFaltante=response.PorcentajeFaltante;
                    }
                    $("#Cantidadpersonas").html(response.NumeroPersonas);
                    $("#Estimadopiezas").html(response.CantidadEstimadaDia);
                    $("#Piezasplaneadas").html(response.PlaneadoPorDia);
                    $("#Porcentajefaltante").html(PorcentajeFaltante);
                    $("#Porcentajeplaneada").html(response.PorcentajePlaneada);
                    $('#Fecha_Grafica').html(response.Fecha_Grafica);
                    $('#Piezasfaltantes').html(response.Piezasfaltantes);
                    var myChart = echarts.init(document.getElementById('PrcentajePlaneacion'));
                    var option = {
                    tooltip: {
                        trigger: 'item'
                    },
                    legend: {
                        show:false,
                    },
                    series: [
                        {
                        name: 'Planeación',
                        type: 'pie',
                        radius: ['60%', '70%'],
                        avoidLabelOverlap: false,
                        itemStyle: {
                            borderRadius: 10,
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        label: {
                            show: true,
                            position: 'center',
                            formatter: response.PorcentajePlaneada+'%',
                            fontSize: 20,
                            fontWeight: 'bold'
                        },
                        
                        labelLine: {
                            show: false
                        },
                        data: [
                            { value: response.PorcentajePlaneada, name: 'Total Planeado', itemStyle: { color: color } },
                            { value: PorcentajeFaltante, name: 'Total faltante estimado', itemStyle: { color: '#D3D3D3' } }
                        ]
                        }
                    ]
                    };

                    // 3. Aplicar la configuración al gráfico
                    myChart.setOption(option);
                }
        });
    }
    // Función para activar el botón
    function activarBoton(id) {
        document.querySelectorAll('.d-flex').forEach(function(button) {
            button.classList.remove('activebtn');
        });
        document.getElementById(id).classList.add('activebtn');
    }
    // Añadir eventos de clic a los botones
    document.getElementById("click-dia").addEventListener("click", function() {
        activarBoton("click-dia");
    });
    document.getElementById("click-semana").addEventListener("click", function() {
        activarBoton("click-semana");
    });
    document.getElementById("click-mes").addEventListener("click", function() {
        activarBoton("click-mes");
    });

</script>


<script>
fetch("{{ route('lineas.indicador') }}")
    .then(response => response.json())
    .then(data => {
        const porcentajeCerradas = parseFloat(data.porcentajeCerradas) || 0;
        const porcentajeCompletadas = parseFloat(data.PorcentajeCompletadas) || 0;
        const totalOfTotal = parseInt(data.TotalOfTotal) || 0;
        const totalCompletadas = parseInt(data.TotalOFcompletadas) || 0;
        const faltanteTotal = parseInt(data.faltanteTotal) || 0
        const container = document.getElementById('lineas-container');
        container.innerHTML = ''; 
        data.lineas.forEach(linea => {
            const card = document.createElement('div');
            card.classList.add('col-md-4', 'mb-4');
            card.innerHTML = `
                <div class="col-sm-12 bg-white mb-4">
                    <div class="accordion-body bg-white pt-0">
                        <div class="card-body bg-white p-1">
                            <h5 class="p-1">
                                Línea ${linea.id}
                               
                            </h5>
                            <div class="d-flex justify-content-between">
                                <div class="row">
                                    <h6 class="text-700 col-6">
                                        Cantidad personas: <span id="Cantidadpersonas${linea.id}">${linea.cantidad_personas}</span>
                                    </h6>
                                    <h6 class="text-700 col-6">
                                        Estimado de piezas por día: <span id="Estimadopiezas${linea.id}">${linea.estimado_piezas}</span>
                                    </h6>
                                    <h6 class="text-700 col-6">
                                        Piezas Completadas: <span id="Piezasplaneadas${linea.id}">${linea.piezas_completadas}</span>
                                    </h6>
                                    <h6 class="text-700 col-6">
                                        Piezas faltantes: <span id="Piezasfaltantes${linea.id}">${linea.piezas_faltantes}</span>
                                    </h6>
                                </div>
                            </div>
                            <div class="pb-1 pt-1 d-flex justify-content-center align-items-center">
                                <div class="p-0" id="lineasprocentaje${linea.id}" style="width: 9rem; height: 9rem"></div>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bullet-item bg-primary me-2"></div>
                                    <h6 class="text-900 fw-semi-bold flex-1 mb-0">
                                        Porcentaje Completadas
                                    </h6>
                                    <h6 class="text-900 fw-semi-bold mb-0">
                                        <span id="Porcentajeplaneada${linea.id}">${linea.porcentaje_completadas}</span>%
                                    </h6>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bullet-item bg-primary-200 me-2"></div>
                                    <h6 class="text-900 fw-semi-bold flex-1 mb-0">
                                        Porcentaje Faltantes
                                    </h6>
                                    <h6 class="text-900 fw-semi-bold mb-0">
                                        <span id="Porcentajefaltante${linea.id}">${linea.porcentaje_faltantes}</span>%
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(card);
            var myChart = echarts.init(document.getElementById(`lineasprocentaje${linea.id}`));
            var option = {
                tooltip: { trigger: 'item' },
                legend: { show: false },
                series: [
                    {
                        name: 'Planeación',
                        type: 'pie',
                        radius: ['60%', '70%'],
                        avoidLabelOverlap: false,
                        itemStyle: {
                            borderRadius: 10,
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        label: {
                            show: true,
                            position: 'center',
                            formatter: `${linea.porcentaje_completadas.toFixed(2)}%`,
                            fontSize: 20,
                            fontWeight: 'bold'
                        },
                        labelLine: { show: false },
                        data: [
                            { value: linea.piezas_completadas, name: 'Completadas', itemStyle: { color: "#007BFF" } },
                            { value: linea.piezas_faltantes, name: 'Faltantes', itemStyle: { color: '#D3D3D3' } }
                        ]
                    }
                ]
            };
            myChart.setOption(option);
        });
    })
    .catch(error => console.log('Error al obtener los datos:', error));
</script>


@endsection