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
       

        .collapse {
            transition: all 0.3s ease;
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
        .Nav-fixed{
            position: fixed;
            top: 2rem;
            z-index: 1;
            background: #F5F7FA;
            width: 94%;
        }
        .Nav-Contend{
            position: relative;
            top: 10.5rem;
        }
    </style>
@endsection
@section('content')
        <div class="row gy-3 justify-content-between" id="Nav-fixed">
            <div class="col-xxl-6">
                <h2 class="mb-2 text-1100">Dashboard</h2>
                <div class="row g-3 justify-content-between mb-4 ">
                    <div class="col-auto">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-phoenix-primary" onclick="DashboardPrincipal('D','{{$FechaHoy}}','{{$FechaAyer}}');"><i class="fas fa-calendar-day"></i> D&iacute;a</button>
                            <button class="btn btn-phoenix-primary" onclick="DashboardPrincipal('S','{{$FechaHoy}}','{{$FechaAyer}}');"><i class="fas fa-calendar-week"></i> Semana</button>
                            <button class="btn btn-phoenix-primary" onclick="DashboardPrincipal('M','{{$FechaHoy}}','{{$FechaAyer}}');"><i class="fas fa-calendar-alt"></i> Mes</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex">
                            {{--<div class="search-box me-2 d-none d-xl-block">
                            <form class="position-relative" data-bs-toggle="search" data-bs-display="static"><input class="form-control search-input search" type="search" placeholder="Search by name" aria-label="Search">
                                <svg class="svg-inline--fa fa-magnifying-glass search-box-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="magnifying-glass" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><span class="fas fa-search search-box-icon"></span></svg>
                            </form>
                            </div>--}}
                            {{--<button class="btn px-3 btn-phoenix-secondary me-2 d-xl-none"><svg class="svg-inline--fa fa-magnifying-glass" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="magnifying-glass" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M500.3 443.7l-119.7-119.7c27.22-40.41 40.65-90.9 33.46-144.7C401.8 87.79 326.8 13.32 235.2 1.723C99.01-15.51-15.51 99.01 1.724 235.2c11.6 91.64 86.08 166.7 177.6 178.9c53.8 7.189 104.3-6.236 144.7-33.46l119.7 119.7c15.62 15.62 40.95 15.62 56.57 0C515.9 484.7 515.9 459.3 500.3 443.7zM79.1 208c0-70.58 57.42-128 128-128s128 57.42 128 128c0 70.58-57.42 128-128 128S79.1 278.6 79.1 208z"></path></svg><!-- <span class="fa-solid fa-search"></span> Font Awesome fontawesome.com --></button><button class="btn px-3 btn-phoenix-primary" type="button" data-bs-toggle="modal" data-bs-target="#filterModal" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><svg class="svg-inline--fa fa-filter" data-fa-transform="down-3" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="filter" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="" style="transform-origin: 0.5em 0.6875em;"><g transform="translate(256 256)"><g transform="translate(0, 96)  scale(1, 1)  rotate(0 0 0)"><path fill="currentColor" d="M3.853 54.87C10.47 40.9 24.54 32 40 32H472C487.5 32 501.5 40.9 508.1 54.87C514.8 68.84 512.7 85.37 502.1 97.33L320 320.9V448C320 460.1 313.2 471.2 302.3 476.6C291.5 482 278.5 480.9 268.8 473.6L204.8 425.6C196.7 419.6 192 410.1 192 400V320.9L9.042 97.33C-.745 85.37-2.765 68.84 3.854 54.87L3.853 54.87z" transform="translate(-256 -256)"></path></g></g></svg><!-- <span class="fa-solid fa-filter" data-fa-transform="down-3"></span> Font Awesome fontawesome.com --></button>
                            <div class="modal fade" id="filterModal" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border">
                                    <form id="addEventForm" autocomplete="off">
                                        <div class="modal-header border-200 p-4">
                                        <h5 class="modal-title text-1000 fs-2 lh-sm">Filter</h5><button class="btn p-1 text-900" type="button" data-bs-dismiss="modal" aria-label="Close"><svg class="svg-inline--fa fa-xmark fs--1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"></path></svg><!-- <span class="fas fa-times fs--1"></span> Font Awesome fontawesome.com --></button>
                                        </div>
                                        <div class="modal-body pt-4 pb-2 px-4">
                                        <div class="mb-3"><label class="fw-bold mb-2 text-1000" for="leadStatus">Lead Status</label><select class="form-select" id="leadStatus">
                                            <option value="newLead" selected="selected">New Lead</option>
                                            <option value="coldLead">Cold Lead</option>
                                            <option value="wonLead">Won Lead</option>
                                            <option value="canceled">Canceled</option>
                                            </select></div>
                                        <div class="mb-3"><label class="fw-bold mb-2 text-1000" for="createDate">Create Date</label><select class="form-select" id="createDate">
                                            <option value="today" selected="selected">Today</option>
                                            <option value="last7Days">Last 7 Days</option>
                                            <option value="last30Days">Last 30 Days</option>
                                            <option value="chooseATimePeriod">Choose a time period</option>
                                            </select></div>
                                        <div class="mb-3"><label class="fw-bold mb-2 text-1000" for="designation">Designation</label><select class="form-select" id="designation">
                                            <option value="VPAccounting" selected="selected">VP Accounting</option>
                                            <option value="ceo">CEO</option>
                                            <option value="creativeDirector">Creative Director</option>
                                            <option value="accountant">Accountant</option>
                                            <option value="executiveManager">Executive Manager</option>
                                            </select></div>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-end align-items-center px-4 pb-4 border-0 pt-3"><button class="btn btn-sm btn-phoenix-primary px-4 fs--2 my-0" type="submit"> <svg class="svg-inline--fa fa-arrows-rotate me-2 fs--2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="arrows-rotate" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M464 16c-17.67 0-32 14.31-32 32v74.09C392.1 66.52 327.4 32 256 32C161.5 32 78.59 92.34 49.58 182.2c-5.438 16.81 3.797 34.88 20.61 40.28c16.89 5.5 34.88-3.812 40.3-20.59C130.9 138.5 189.4 96 256 96c50.5 0 96.26 24.55 124.4 64H336c-17.67 0-32 14.31-32 32s14.33 32 32 32h128c17.67 0 32-14.31 32-32V48C496 30.31 481.7 16 464 16zM441.8 289.6c-16.92-5.438-34.88 3.812-40.3 20.59C381.1 373.5 322.6 416 256 416c-50.5 0-96.25-24.55-124.4-64H176c17.67 0 32-14.31 32-32s-14.33-32-32-32h-128c-17.67 0-32 14.31-32 32v144c0 17.69 14.33 32 32 32s32-14.31 32-32v-74.09C119.9 445.5 184.6 480 255.1 480c94.45 0 177.4-60.34 206.4-150.2C467.9 313 458.6 294.1 441.8 289.6z"></path></svg><!-- <span class="fas fa-arrows-rotate me-2 fs--2"></span> Font Awesome fontawesome.com -->Reset</button><button class="btn btn-sm btn-primary px-9 fs--2 my-0" type="submit">Done</button></div>
                                    </form>
                                    </div>
                                </div>
                            </div>--}}
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        </div>
        <div class="row gy-3 mb-4 justify-content-between" id="Nav-Contend">
            <h4 class="text-700 fw-semi-bold mb-2">Ordenes de Fabricaci&oacute;n</h4>
            <div class="col-xxl-12">
                 <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-md-4 col-xl-3 col-xxl-3">
                        <div class="card h-90">
                        <div class="card-body">
                            <div class="d-flex d-sm-block justify-content-between">
                            <div class="border-bottom-sm mb-sm-2">
                                <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center icon-wrapper-sm shadow-primary-100" style="transform: rotate(-7.45deg);">
                                    <span class="far fas fa-calendar-check text-primary fs-1 z-index-1 ms-2"></span>
                                </div>
                                <p class="text-700 fs--1 mb-0 ms-2 mt-3">Ordene de Fabricaci&oacute;n</p>
                                </div>
                                <p class="text-success mt-2 fs-2 fw-bold mb-0 mb-sm-4" ><span id="OFCantidadAbierta">0</span><span class="fs-0 text-900 lh-lg"> Abiertas</span></p>
                            </div>
                            <div class="d-flex flex-column justify-content-center flex-between-end d-sm-block text-end text-sm-start"><span class="badge badge-phoenix badge-phoenix-info fs--2 mb-2" ><span id="OFPorcentajeAbierta"></span>%</span>
                                <span class="mb-0 fs--1 text-700">que el d&iacute;a de ayer</span>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl-3 col-xxl-2">
                        <div class="card h-90">
                            <div class="card-body">
                                <div class="d-flex d-sm-block justify-content-between">
                                    <div class="border-bottom-sm mb-sm-2">
                                        <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center icon-wrapper-sm shadow-info-100" style="transform: rotate(-7.45deg);">
                                            <span class="fas fa-calendar-minus text-info fs-1 z-index-1 ms-2"></span></div>
                                        <p class="text-700 fs--1 mb-0 ms-2 mt-3">Ordene de Fabricaci&oacute;n</p>
                                        </div>
                                        <p class="text-warning mt-2 fs-2 fw-bold mb-0 mb-sm-4"><span id="OFCantidadCerrada">0</span> <span class="fs-0 text-900 lh-lg">Cerradas</span></p>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center flex-between-end d-sm-block text-end text-sm-start"><span class="badge badge-phoenix badge-phoenix-info fs--2 mb-2"><span id="OFPorcentajeCerrada">0</span>%</span>
                                        <span class="mb-0 fs--1 text-700">que el d&iacute;a de ayer</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl-6 col-xxl-4 gy-2 gy-md-1 ">
                        <div class="border-bottom">
                            <h5 class="pb-4 border-bottom">Grafica Ordenes de Fabricaci&oacute;n</h5>
                            <div id="OFGraficoAbiertasCerradas" style="width: 100%; height: 200px;"></div>
                        </div>
                    </div>
                    <hr>
                    <h4 class="text-700 fw-semi-bold my-1">Estaci&oacute;nes</h4>
                    <div class="row gy-3 justify-content-between">
                        <div class="col-xl-12 col-xxl-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="border-bottom">
                                        <h5 class="pb-4 border-bottom">Tiempo promedio por Pieza</h5>
                                        <p class="text-center">% de tiempo</p>
                                        <div id="OFGraficoEstacionesTiempos" style="width: 100%; height: 200px;"></div>
                                    </div>
                                    <div class="border-top">
                                        <div class="table-responsive scrollbar mx-n1 px-1">
                                            <table id="TableEstaciones" class="table table-sm  fs--1 leads-table" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Estación</th>
                                                        <th class="text-center">Programadas</th>
                                                        <th class="text-center">Pendientes</th>
                                                        <th class="text-center">En proceso</th>
                                                        <th class="text-center">Terminadas</th>
                                                        <th class="text-center">Promedio tiempo productivo(pieza)</th>
                                                        <th class="text-center">Promedio tiempo Muerto(pieza)</th>
                                                        <th class="text-center">Promedio tiempo Total(pieza)</th>
                                                        <th class="text-center">Promedio piezas/h</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list" id="BodyEstaciones">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--<div class="col-xl-12 col-xxl-12">
                            <div class="border-top">
                                <div class="table-responsive scrollbar mx-n1 px-1">
                                    <table id="TableEstaciones" class="table table-sm  fs--1 leads-table" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Estación</th>
                                                <th class="text-center">Programadas</th>
                                                <th class="text-center">Pendientes</th>
                                                <th class="text-center">En proceso</th>
                                                <th class="text-center">Terminadas</th>
                                                <th class="text-center">Promedio por pieza(t)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list" id="BodyEstaciones">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>--}}
                    </div>
                    <h4 class="text-700 fw-semi-bold my-1">L&iacute;neas</h4>
                    <div class="row gy-3 justify-content-between">
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
<script>
    window.onload = function (){
        DashboardPrincipal('D','{{$FechaHoy}}','{{$FechaAyer}}');
    }
    window.addEventListener("scroll", function () {
        const cuadro = document.getElementById("Nav-fixed");
        const NavContend = document.getElementById("Nav-Contend");
        const scrollY = window.scrollY;
        if (scrollY > 92) {
            cuadro.classList.add("Nav-fixed");
            NavContend.classList.add("Nav-Contend");
        } else {
            cuadro.classList.remove("Nav-fixed");
            NavContend.classList.remove("Nav-Contend");
        }
    });
    //Dashboard/Principal
    function DashboardPrincipal(LapsoTiempo,FechaInicio,FechaFin){
        OFCantidadAbierta = $('#OFCantidadAbierta');
        OFPorcentajeAbierta = $('#OFPorcentajeAbierta');
        OFCantidadCerrada = $('#OFCantidadCerrada'); 
        OFPorcentajeCerrada = $('#OFPorcentajeCerrada');
        BodyEstaciones = document.getElementById('BodyEstaciones');
        OFGraficoAbiertasCerradas = document.getElementById('OFGraficoAbiertasCerradas');
        OFGraficoEstacionesTiempos = document.getElementById('OFGraficoEstacionesTiempos');
        OFCantidadAbierta.html('');
        OFPorcentajeAbierta.html('');
        OFCantidadCerrada.html(''); 
        OFPorcentajeCerrada.html('');
        BodyEstaciones.innerHTML = "";
        if (echarts.getInstanceByDom(OFGraficoAbiertasCerradas)) {
            echarts.dispose(OFGraficoAbiertasCerradas); // Destruye la instancia previa
        }
        if (echarts.getInstanceByDom(OFGraficoEstacionesTiempos)) {
            echarts.dispose(OFGraficoEstacionesTiempos); // Destruye la instancia previa
        }
        $.ajax({
            url: "{{route('DashboardPrincipal')}}", 
            type: 'POST',
            data: {
                LapsoTiempo:LapsoTiempo,
                FechaInicio:FechaInicio,
                FechaFin:FechaFin,
            },
            beforeSend: function() {
                BodyEstaciones.innerHTML = "<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>";
            },
            success: function(response) {
                BodyEstaciones.innerHTML = "";
                OFCantidadAbierta.html(response.OFAbiertaCant);
                OFPorcentajeAbierta.html(response.PorcentajeAvanceA);
                OFCantidadCerrada.html(response.OFCerradaCant); 
                OFPorcentajeCerrada.html(response.PorcentajeAvanceC);
                var ChartOFGraficoAbiertasCerradas = echarts.init(OFGraficoAbiertasCerradas);
                var option = {
                    title: {
                        text: 'Ordenes Totales: '+(response.OFCerradaCant+response.OFAbiertaCant),
                        left: 'center',
                        top: 0,
                        textStyle: {
                            fontSize: 12,
                            fontWeight: 'bold'
                        }
                    },
                    color: ['#007bff', '#17a2b8'],
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c} ({d}%)'
                    },
                    legend: {
                        orient: 'vertical',
                        top: '1%',
                        left: 'left',
                        formatter: function (name) {
                            const series = option.series[0];
                            const item = series.data.find(i => i.name === name);
                            if (!item) return name;

                            const total = series.data.reduce((sum, i) => sum + i.value, 0);
                            if (total === 0) {
                                    return `${name}: 0%`;
                            }
                            const percent = ((item.value / total) * 100).toFixed(1);
                            return `${name}: ${percent}%`;
                        }
                    },
                    series: [
                        {
                            name: 'Información',
                            type: 'pie',
                            radius: ['40%', '70%'],
                            avoidLabelOverlap: false,
                            itemStyle: {
                            borderRadius: 1,
                            borderColor: '#fff',
                            borderWidth: 2
                            },
                            label: {
                            show: false,
                            position: 'center'
                            },
                            emphasis: {
                            label: {
                                show: true,
                                fontSize: 10,
                                fontWeight: 'bold'
                            }
                            },
                            labelLine: {
                            show: false
                            },
                            data: [
                                { value: response.OFAbiertaCant, name: 'Abiertas' },
                                { value: response.OFCerradaCant, name: 'Cerradas' },
                            ]
                        }
                    ]
                };
                ChartOFGraficoAbiertasCerradas.setOption(option);
                var DataSourceEstacionesTiempos = [
                    ['product', 'Tiempo Total', 'Tiempo Productivo', 'Tiempo Muerto']
                ];
                if ($.fn.DataTable.isDataTable('#TableEstaciones')) {
                    $('#TableEstaciones').DataTable().clear().destroy();
                }
                (response.Estaciones).forEach(element => {
                    if(element.id != 18){
                        DataSourceEstacionesTiempos.push([
                            element.nombre, // o como se llame el campo del nombre
                            //element.PorcentajeTiempoTotal, // duración de la orden
                            element.PorcentajeTiempoProductivo, // tiempo productivo
                            element.PorcentajeTiempoMuerto, // tiempo muerto
                            //element.TiempoTotal,
                            element.TiempoProductivo,
                            element.TiempoMuerto,
                        ]);
                    }
                    BodyEstaciones.innerHTML += `
                        <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                            <td class="fw-semi-bold text-1000 ps-0 py-0">
                                <a class="fw-bold text-primary" href="#!">`+element.nombre+`</a>
                            </td>
                            <td class="fw-semi-bold text-900 py-0 text-center">`+element.Programadas+`</td>
                            <td class="w-semi-bold text-900 py-0 text-center">`+element.Pendientes+`</td>
                            <td class="fw-semi-bold text-900 py-0 text-center">`+element.EnProceso+`</td>
                            <td class="fw-bold text-900 py-0 text-center">
                                <div class="d-flex align-items-center gap-3" title="`+element.PorcentajeTerminadas+`%">
                                    <div style="--phoenix-circle-progress-bar:`+element.PorcentajeTerminadas+`">
                                        <svg class="circle-progress-svg" width="40" height="40" viewBox="0 0 170 170">
                                            <circle class="progress-bar-rail" cx="60" cy="60" r="54" fill="none" stroke-linecap="round" stroke-width="14"></circle>
                                            <circle class="progress-bar-top" cx="60" cy="60" r="54" fill="none" stroke-linecap="round" stroke="#3874FF" stroke-width="14"></circle>
                                        </svg>
                                    </div>
                                    <h6 class="mb-0 text-900">`+element.Terminadas+`</h6>
                                </div>
                            </td>
                            <td class="fw-semi-bold text-900 py-0 text-center">`+element.TiempoPromedioPieza+`</td>
                            <td class="fw-semi-bold text-900 py-0 text-center">`+element.TiempoMuerto+`</td>
                             <td class="fw-semi-bold text-900 py-0 text-center">`+element.TiempoTotal+`</td>
                            <td class="fw-semi-bold text-900 py-0 text-center">`+element.CantidadPiezasHora+`</td>
                        </tr>`;
                });
                var ChartOFGraficoEstacionesTiempos = echarts.init(OFGraficoEstacionesTiempos);
                OptionEstaciones = {
                    legend: {},
                    tooltip: {
                        /*trigger: 'item',
                        formatter: function (params) {
                            const data = params.data;
                            const nombre = data[0];
                            const tipo = params.seriesName;
                            let porcentaje = '';
                            let tiempo = '';

                            if (tipo === 'Total') {
                                porcentaje = data[1];
                                tiempo = data[4];
                            } else if (tipo === 'Productivo') {
                                porcentaje = data[2];
                                tiempo = data[5];
                            } else if (tipo === 'Muerto') {
                                porcentaje = data[3];
                                tiempo = data[6];
                            }

                            return `
                                <strong>${nombre}</strong><br/>
                                ${tipo}: ${tiempo} seg (${porcentaje.toFixed(2)}%)
                            `;
                        }*/
                    },
                    dataset: {
                        source: DataSourceEstacionesTiempos
                    },
                    grid: {
                        left: '5%',
                        right: '5%',
                        bottom: 60,   // más espacio si se rotan etiquetas
                        top: '15%'
                    },
                    xAxis: {
                        type: 'category',
                        axisLabel: {
                            fontSize: 10,
                            rotate: 40,
                            overflow: 'truncate',
                            ellipsis: '...'
                        }
                    },
                    yAxis: {
                        axisLabel: {
                            fontSize: 10          // opcional, también reducir
                        }
                    },
                    series: [
                        //{ name: 'Total',type: 'bar', barWidth: '20%',stack: 'total', itemStyle: { color: '#1f77b4' } },  // azul fuerte
                        { name: 'Productivo',type: 'bar', barWidth: '20%',stack: 'total', itemStyle: { color: '#5DADE2' } },  // azul más claro
                        { name: 'Muerto',type: 'bar', barWidth: '20%',stack: 'total', itemStyle: { color: '#EC7063' } }   // azul muy claro (opcional)
                    ]
                };
                ChartOFGraficoEstacionesTiempos.setOption(OptionEstaciones);
                $('#TableEstaciones').DataTable({
                    pageLength: 10,
                    lengthChange: false,
                    ordering: false,
                    searching: false,
                    language: {
                        info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                        infoEmpty: "Mostrando 0 a 0 de 0 entradas",
                        infoFiltered: "(filtrado de _MAX_ entradas totales)",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "Siguiente",
                            previous: "Anterior"
                        }
                    }
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //$('#RetrabajoOFOpciones').html('');
            }
        });
    }
</script>
{{--
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5"></script>
<script>
     /*setInterval(function() {
        location.reload();
    }, 60000);    */                                                                                                                                                                                                                                                                                                         
    function setupClickListener(clickId, indicadoresId, hideIds) {
        document.getElementById(clickId).addEventListener('click', function() {
          

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
          

            // Asignar valores a las variables, asegurando que no sean null o undefined
            const porcentajeCerradas = parseFloat(data.porcentajeCerradas) || 0;  
            const porcentajeCompletadas = parseFloat(data.PorcentajeCompletadas) || 0;  
            const totalCompletadas = parseInt(data.TotalOFcompletadas) || 0;
            const faltanteTotal = parseInt(data.faltanteTotal) || 0;
            const Estimadopiezas = parseFloat(data.Estimadopiezas) || 0;
            const Cantidadpersonas = parseInt(data.Cantidadpersonas) || 0;

            // Mostrar los valores en la interfaz
            document.getElementById("piezas").textContent = Estimadopiezas.toFixed();
            document.getElementById("Cantidad").textContent = Cantidadpersonas;
            document.getElementById("Porcentajeplaneada").textContent = porcentajeCompletadas.toFixed(2);  
            document.getElementById("Porcentajefaltante").textContent = porcentajeCerradas.toFixed(2);  
            document.getElementById("Piezasfinalizadas").textContent = totalCompletadas;
            //document.getElementById("Piezasfinalizadas").textContent = totalCompletadas;
            document.getElementById("Piezafaltantes").textContent = faltanteTotal;
            //Piezaplaneadas

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
    function convertirSegundosAHMS(segundos) {
        var horas = Math.floor(segundos / 3600);
        var minutos = Math.floor((segundos % 3600) / 60);
        var segundosRestantes = segundos % 60;
        return `${horas}h ${minutos}m ${segundosRestantes}s`;
    }
    function crearGrafico(url, chartDomId) {
        var chartDom = document.getElementById(chartDomId);
        chartDom.style.width = "70vw";  
        chartDom.style.height = "450px"; 
        var myChart = echarts.init(chartDom);
        fetch(url)
            .then(response => response.json())
            .then(data => {
                var tiemposProduccionData = [];
                var tiemposMuertosData = [];
                var areas = Object.entries(data.numeroPRO).map(([nombre, valor]) => `${nombre} 'No'${valor}`);
                var produccionMap = {};
                data.produccion.forEach(item => {
                    produccionMap[item.Areas_id] = parseInt(item.tiempoProduccionActual, 10);
                });
                var finalResultMap = {};
                data.finalResult.forEach(item => {
                    var areasId = item.Areas_id.split(',').map(area => parseInt(area, 10));
                    areasId.forEach(id => {
                        finalResultMap[id] = item.TiempoMuerto;
                    });
                });
                areas.forEach(area => {
                    var areaId = areas.indexOf(area) + 3;
                    var tiempoProduccion = produccionMap[areaId] || 0;
                    var tiempoMuerto = finalResultMap[areaId] || 0;

                    tiemposProduccionData.push(tiempoProduccion);
                    tiemposMuertosData.push(tiempoMuerto);
                });
                var option = {
                    title: [
                        {
                            text: 'Cantidad de Piezas Registradas: ' + data.TotalPiezas,
                            left: 'center', // Centrar el título
                            top: '2%',
                            textStyle: {
                                fontSize: 14,
                                fontWeight: 'bold',
                                color: '#333'
                            }
                        },
                        {
                            text: 'Tiempo de Producción vs Tiempo Muerto',
                            left: 'center', // Centrar subtítulo
                            top: '6%',
                            textStyle: {
                                fontSize: 12,
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
                        left: 'center', // Centrar la leyenda
                        top: '12%',
                        textStyle: { fontSize: 10 }
                    },
                    grid: {
                        left: '1%',   // Margen izquierdo mayor para evitar cortes
                        right: '20%',  // Margen derecho mayor
                        bottom: '10%',
                        top: '20%',    // Más espacio en la parte superior
                        containLabel: true
                    },
                    xAxis: {
                        type: 'value',
                        axisLabel: {
                            formatter: convertirSegundosAHMS,
                            fontSize: 12,
                        },
                        splitLine: {
                            show: true,
                            lineStyle: { type: 'dashed', color: '#ccc' }
                        }
                    },
                    yAxis: {
                        type: 'category',
                        data: areas,
                        axisLabel: { fontSize: 12, margin: 50 },
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
                                fontSize: 10
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
                                fontSize: 10
                            },
                            itemStyle: { color: '#3B8F82' },
                            data: tiemposProduccionData
                        },
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
                window.addEventListener('resize', () => myChart.resize()); // Redimensionar la gráfica si cambia el tamaño de la pantalla
            })
            .catch(error => console.log('Error al cargar los datos del gráfico:', error));
    }
    crearGrafico("{{ route('graficastiempoMuerto') }}", 'grafica-tiempoD');
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
                    //$("#Piezasplaneadas").html(response.PlaneadoPorDia);
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
        const container = document.getElementById('lineas-container');
        container.innerHTML = ''; 
        data.lineas.forEach(linea => {
            // Valores por defecto
            const cantidadPersonas = linea.cantidad_personas ?? 0;
            const estimadoPiezas = linea.estimado_piezas ?? 0;
            const piezasCompletadas = linea.piezas_completadas ?? 0;
            const piezasFaltantes = linea.piezas_faltantes ?? 0;
            const porcentajeCompletadas = linea.porcentaje_completadas ?? 0;
            const porcentajeFaltantes = linea.porcentaje_faltantes ?? 0;

            const card = document.createElement('div');
            card.classList.add('col-md-4', 'mb-4');
            card.innerHTML = `
                <div class="col-sm-12 bg-white mb-4">
                    <div class="accordion-body bg-white pt-0">
                        <div class="card-body bg-white p-1">
                            <h5 class="p-1">Línea ${linea.id}</h5>
                            <div class="d-flex justify-content-between">
                                <div class="row">
                                    <h6 class="text-700 col-6">Cantidad personas: <span>${cantidadPersonas}</span></h6>
                                    <h6 class="text-700 col-6">Estimado de piezas por día: <span>${estimadoPiezas}</span></h6>
                                    <h6 class="text-700 col-6">Piezas Completadas: <span>${piezasCompletadas}</span></h6>
                                    <h6 class="text-700 col-6">Piezas faltantes: <span>${piezasFaltantes}</span></h6>
                                </div>
                            </div>
                            <div class="pb-1 pt-1 d-flex justify-content-center align-items-center">
                                <div class="p-0" id="lineasprocentaje${linea.id}" style="width: 9rem; height: 9rem"></div>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bullet-item bg-primary me-2"></div>
                                    <h6 class="text-900 fw-semi-bold flex-1 mb-0">Porcentaje Completadas</h6>
                                    <h6 class="text-900 fw-semi-bold mb-0"><span>${porcentajeCompletadas}</span>%</h6>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bullet-item bg-primary-200 me-2"></div>
                                    <h6 class="text-900 fw-semi-bold flex-1 mb-0">Porcentaje Faltantes</h6>
                                    <h6 class="text-900 fw-semi-bold mb-0"><span>${porcentajeFaltantes}</span>%</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(card);

            // Configuración de la gráfica
            var myChart = echarts.init(document.getElementById(`lineasprocentaje${linea.id}`));

            let chartData = [];
            let labelText = "0%";

            if (piezasCompletadas > 0 || piezasFaltantes > 0) {
                chartData = [
                    { value: piezasCompletadas, name: 'Completadas', itemStyle: { color: "#007BFF" } },
                    { value: piezasFaltantes, name: 'Faltantes', itemStyle: { color: '#D3D3D3' } }
                ];
                labelText = `${porcentajeCompletadas.toFixed(2)}%`;
            } else {
                // Si todo es 0, solo mostrar gris
                chartData = [{ value: 1, name: 'Sin Datos', itemStyle: { color: '#D3D3D3' } }];
                labelText = "N/A";
            }

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
                            formatter: labelText,
                            fontSize: 20,
                            fontWeight: 'bold'
                        },
                        labelLine: { show: false },
                        data: chartData
                    }
                ]
            };
            myChart.setOption(option);
        });
    })
    .catch(error => console.error("Error al cargar los datos:", error));

    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ route('tiempopromedio') }}")
            .then(response => response.json())
            .then(data => {
                if (data.TiempoCortes && data.TiempoCortes.length > 0) {
                    let tiempoCortes = data.TiempoCortes[0];
                    let tiempoFormateado = formatTime(tiempoCortes.SegundosPorUnidad);
                    let cortesElement = document.querySelector('#Cortes + p');
                    if (cortesElement) {
                        cortesElement.innerHTML = `Tiempo Promedio: ${tiempoFormateado}`;
                    }
                } else {
                    console.log('No hay datos de TiempoCortes disponibles');
                }
                let areaMapping = {
                    3: "Suministro", 
                    4: "Preparado", 
                    5: "Ensamble", 
                    6: "Pulido", 
                    7: "Medicion", 
                    8: "Visualizacion", 
                    9: "Empaquetado"
                };
                let finalResultData = {};
                data.finalResult.forEach(item => {
                    finalResultData[item.Areas] = {
                        tiempoEscaneado: item.tiempopiezas ?? 0,  // Asignamos tiempopiezas o 0 si no existe
                        tiempoNoEscaneado: null
                    };
                });
                data.result.forEach(itemGroup => {
                    itemGroup.forEach(item => {
                        let areaId = item.Areas;
                        if (!finalResultData[areaId]) {
                            finalResultData[areaId] = { tiempoEscaneado: null, tiempoNoEscaneado: item.Tiempopieza };
                        } else {
                            finalResultData[areaId].tiempoNoEscaneado = item.Tiempopieza;
                        }
                    });
                });
                Object.keys(finalResultData).forEach(areaId => {
                let area = finalResultData[areaId];
                let areaIdName = areaMapping[areaId];  // "Suministro" si areaId = 3
                if (areaIdName) {
                    let tiempoEscaneado = area.tiempoEscaneado && area.tiempoEscaneado > 0 ? formatTime(area.tiempoEscaneado) : "No disponible";
                    let tiempoNoEscaneado = area.tiempoNoEscaneado && area.tiempoNoEscaneado > 0 ? formatTime(area.tiempoNoEscaneado) : "No disponible";
                    let element = document.querySelector(`#${areaIdName} + p`);  // Verifica que el selector sea correcto
                    if (element) {
                        element.innerHTML = `Tiempo Escaneado: ${tiempoEscaneado}<br>Tiempo No Escaneado: ${tiempoNoEscaneado}`;
                    }
                }
            });

            })
            .catch(error => console.error('Error cargando los datos:', error));
    });
    function formatTime(seconds) {
        if (seconds <= 0) return "No disponible";

        let totalSeconds = Math.floor(seconds * 60); // Convertimos minutos decimales a segundos
        let hours = Math.floor(totalSeconds / 3600);
        let minutes = Math.floor((totalSeconds % 3600) / 60);
        let remainingSeconds = totalSeconds % 60;

        let timeParts = [];

        if (hours > 0) timeParts.push(`${hours} horas`);
        if (minutes > 0) timeParts.push(`${minutes} minutos`);
        if (remainingSeconds > 0 || timeParts.length === 0) timeParts.push(`${remainingSeconds} segundos`);

        return timeParts.join(" ");
    }


</script>
    --}}
@endsection
