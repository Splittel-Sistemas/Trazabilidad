@extends('layouts.menu2')
@section('title', 'Progreso')
@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--estilos-->
    <style>
        .lista-busqueda{
            height:6rem;
            overflow-y: auto;
        }
        .lista-busqueda::-webkit-scrollbar {
            width: 8px; /* Ancho del scroll */
        }
        .lista-busqueda::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 10px;
            border: 2px solid #fff;
        }
        .lista-busqueda::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }
        .lista-busqueda::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            border-radius: 10px;
        }
        .progress-Porcentaje{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 100%;
            width: 4.8rem;
            height: 4.8rem;
            display: flex;
            justify-content: center;
        }
        .progress-circle {
            position: relative;
            left: 35%;
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1),
            inset 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .progress-Porcentaje h5{
            font-size: 1.3rem; /* Ajusta según sea necesario */
            font-weight: bold;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .Estacion_Hover {
            transition: transform 0.3s ease; /* Duración y tipo de transición */
        }
        .Estacion_Hover:hover{
            transform: scale(1.04);
        }
        #GraficaPorcentajeTiempos {
        width: 100%;
        height: 350px;
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
        <!--Filtro-->
        <div class="row">
            <div class="card shadow-sm border-light col-8 col-sm-6 p-2 mb-1">
                <div class="accordion" id="FiltroOrden">
                    <div class="accordion-item border-top border-300">
                        <h2 class="accordion-header" id="headingOne">
                            <button id="AccordeFiltroOrdenBtn" class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Busqueda por N&uacute;mero de Orden
                            </button>
                        </h2>
                        <div class="accordion-collapse collapse show" id="collapseOne" aria-labelledby="headingOne" data-bs-parent="#FiltroOrden">
                            <div class="accordion-body pt-0">
                                <div class="btn-group my-0" role="group" aria-label="Tipo de Orden">
                                    <input type="radio" class="btn-check" name="TipoOrden" id="TipoOrden1" autocomplete="off" checked>
                                    <label class="btn btn-sm btn-outline-primary" for="TipoOrden1">
                                        <i class="fas fa-shopping-cart me-1"></i> Orden de venta
                                    </label>
                                    <input type="radio" class="btn-check" name="TipoOrden" id="TipoOrden2" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-primary" for="TipoOrden2">
                                        <i class="fas fa-industry me-1"></i> Orden de fabricaci&oacute;n
                                    </label>
                                </div>
                                <hr class="my-1 p-0">
                                <div class="mb-2 col-10">
                                    <label for="NumeroOrden" class="form-label">N&uacute;mero de Orden</label>
                                    <div class="input-group">
                                        <input type="text" oninput="RegexNumeros(this)" class="form-control form-control-sm" id="NumeroOrden" placeholder="Ingresa número de Orden" autocomplete="off">
                                        <button class="btn btn-outline-primary btn-sm" id="Btn-BuscarOrden">
                                            Buscar
                                        </button>
                                    </div>
                                    <div class="list-group lista-busqueda" id="ListaBusquedas" style="display: none;height: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Detalles-->
        <div class="row bg-white">
            <div class="col-12 pt-4" id="DetallesOrdenFabricacion" style="display: none">
                <h4 class="mb-3" id="exampleModalLabel">
                    Detalles De Orden De Fabricacion:
                    <span id="ordenFabricacionNumero" class="text-muted"></span>
                    <span id="EstatusFabricacion"class="" style="position: absolute;right:4rem;">Estatus</span> 
                </h4>
                <hr>
                 <!-- Barra de progreso -->
                        <h4 class="text-center mb-3 text-muted">Orden de Venta: <span id="OrdenVenta"></span> &nbsp;&nbsp;&nbsp;&nbsp; Cliente: <span id="NombreCliente"></span></h4>
                        <h5 class="text-center mb-2">Progreso Total de piezas completadas</h5>
                        <div class="progress" style="height: 22px; border-radius: 5px; box-shadow: 0px 3px 6px rgba(0,0,0,0.2); overflow: hidden; width: 100%;">
                            <div id="plemasProgressBar" class="progress-bar text-white fw-bold progress-animated" role="progressbar" 
                                style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;" 
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0%
                            </div>
                            <h6 class="mx-2 mt-2" id="Bloque0porciento">0%</h6>
                        </div>
                        <div class="row justify-content-center mt-3">
                            <!-- Primera fila (4 elementos) -->
                            <h4 class="text-center mb-0 mt-2">Tiempos</h4>
                            <div class="row mt-3">
                                <div class="mx-0 col-12 col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header m-0 py-3 bg-info">

                                        </div>
                                        <div class="card-body">
                                            <div class="border-0 p-2 text-center">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="fa-stack fa-1x">
                                                        <i class="fas fa-circle fa-stack-2x text-success"></i>
                                                        <i class="fas fa-cog fa-stack-1x text-white "></i>
                                                    </span>
                                                </div>
                                                <h5 class="mt-2">Duración Total de Fabricación</h5>
                                                <p id="TiempoDuracion" class="text-muted fs--1 mb-0"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mx-0 col-12 col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header m-0 py-3 bg-info">

                                        </div>
                                        <div class="card-body">
                                            <div class="border-0 p-2 text-center">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="fa-stack fa-1x">
                                                        <i class="fas fa-circle fa-stack-2x text-success"></i>
                                                        <i class="fas fa-cogs fa-stack-1x text-white "></i>
                                                    </span>
                                                </div>
                                                <h5 class="mt-2">Duración Promedio por Pieza</h5>
                                                <p id="TiempoPromedio" class="text-muted fs--1 mb-0"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card shadow-sm border-0 text-center" style="background-color: #d4edda;">
                                        <div class="card-header m-0 py-2 bg-success">

                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="fa-stack fa-1x">
                                                    <i class="fas fa-circle fa-stack-2x text-success"></i>
                                                    <i class="fas fa-stopwatch fa-stack-1x text-white "></i>
                                                </span>
                                            </div>
                                            <h5 class="mt-2">Tiempo Productivo</h5>
                                            <p id="Produccion"  class="text-muted fs--1 mb-0">Tiempo Promedio</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card shadow-sm border-0 text-center" style="background-color: #f8d7da;">
                                        <div class="card-header m-0 py-2 bg-danger">

                                        </div>
                                        <div class="card-body">
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
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card shadow-sm border-0 text-center" style="background-color: #cce5ff;">
                                        <div class="card-header m-0 py-2 bg-primary">

                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="fa-stack fa-1x">
                                                    <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                                    <i class="fas fa-clock fa-stack-1x text-white"></i>
                                                </span>
                                            </div>
                                            <h5 class="mt-2">Tiempo Total Trabajado</h5>
                                            <p id="TiempoTotal"  class="text-muted fs--1 mb-0">Tiempo Total de la Orden</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <!---->
                            <!--<div class="card p-3">-->
                            <h4 class="text-center mb-2 mt-2">Estaci&oacute;nes</h4>
                            <div  class="row mb-3" id="plemasCanvases">
                            </div>
                            <hr>
                            <h4 class="text-center mb-0 mt-2">Estad&iacute;sticas</h4>
                            <div class="col-12 col-sm-8 col-md-8 Estacion_Hover my-1" id="ContainerGraficaPorcentajeTiempos">
                                <div class="card rounded border-0 p-2">
                                    <div id="GraficaPorcentajeTiempos"></div>
                                </div>
                            </div>
                            {{--<div class="col-12 col-sm-4 col-md-4 Estacion_Hover my-3" id="ContainerGraficaPorcentajeRetrabajos">
                                <div class="card rounded border-0 p-2">
                                    <div id="GraficaPorcentajeTiempos1">1111111</div>
                                </div>
                            </div>--}}
                        </div>
            </div>
            <div class="col-12 pt-4" id="DetallesOrdenVenta" style="display: none">
                <h4 class="mb-3">
                    Detalles De Orden De Venta:
                    <span id="OVNumero"></span>
                    <span id="OVEstatus"class="" style="position: absolute;right:4rem;">Estatus</span> 
                </h4>
                <hr>
                 <!-- Barra de progreso -->
                    <h4 class="text-center mb-3 text-muted">Cliente: <span id="OVNombreCliente"></span></h4>
                    <h5 class="text-center mb-2">Progreso de piezas completadas</h5>
                    <div class="progress" style="height: 22px; border-radius: 5px; box-shadow: 0px 3px 6px rgba(0,0,0,0.2); overflow: hidden; width: 100%;">
                        <div id="OVBarrraProgreso" class="progress-bar text-white fw-bold progress-animated" role="progressbar" 
                            style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;" 
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            0%
                        </div>
                        <h6 class="mx-2 mt-2" id="OVBloque0porciento">0%</h6>
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
            <input type="hidden" id="idVenta" value="">
            <input type="hidden" id="idFabricacion" value="">     
        </div>
@endsection
@section('scripts')
    <!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>   
    //cargar los datos de fabricacion
    let currentStageOpen = null; // <- para rastrear la etapa activa
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
<script>
    //Scripts utiles
    $('#Btn-BuscarOrden').on('click', function () {
        var NumeroOrden = $('#NumeroOrden').val().trim();
        if (NumeroOrden === '') {
            return;  
        }
        $('#NumeroOrden').val(NumeroOrden).trigger('input');
    });
    AjaxOrden = null;
    $('#TipoOrden1').on('change', function() {
        $('#ListaBusquedas').html('');
        $('#ListaBusquedas').hide();
    });
    $('#TipoOrden2').on('change', function() {
        $('#ListaBusquedas').html('');
        $('#ListaBusquedas').hide();
    });
    $('#NumeroOrden').on('input', function() {
        NumeroOrden = $('#NumeroOrden').val();
         if (AjaxOrden && typeof AjaxOrden.abort === 'function') {
            AjaxOrden.abort();
        }
        if (AjaxOrden && AjaxOrden.readyState !== 4) {
                AjaxOrden.abort();
                console.log("Petición anterior cancelada");
            }
        if(NumeroOrden.length<3){
            $('#ListaBusquedas').html('');
            $('#ListaBusquedas').hide();
            return 0;
        }
        OrdenVenta = $('#TipoOrden1').is(':checked');
        OrdenFabricacion = $('#TipoOrden2').is(':checked');
        TipoOrden = "OF";
        if(OrdenVenta == true){
            TipoOrden = "OV";
        }
        AjaxOrden = $.ajax({
            url: '{{ route("TipoOrden") }}',
            type: 'POST',
            data: { 
                NumeroOrden:NumeroOrden,
                TipoOrden:TipoOrden,
            },
            success: function (response) {
                $('#ListaBusquedas').html(response);
                if(response!=""){
                    $('#ListaBusquedas').show();
                }
            },
            error: function () {
                //alert('Error al obtener los datos de la venta.');
            }
        });
    });
    function SeleccionarNumOrden(NumeroOrden){
        $('#NumeroOrden').val(NumeroOrden);
        $('#ListaBusquedas').hide();
        $('.ver-fabricacion').trigger('click');
        $('#DetallesOrdenVenta').hide();
        $('#DetallesOrdenFabricacion').hide();
        OrdenVenta = $('#TipoOrden1').is(':checked');
        OrdenFabricacion = $('#TipoOrden2').is(':checked');
        if(OrdenVenta == true){
            OVNumero = document.getElementById('OVNumero');
            OVEstatus  = document.getElementById('OVEstatus');
            OVNombreCliente = document.getElementById('OVNombreCliente');
            OVBloque0porciento = document.getElementById('OVBloque0porciento');
            OVBarrraProgreso = document.getElementById('OVBarrraProgreso');
            OVNumero.innerHTML = NumeroOrden;
            $.ajax({
                url: '{{ route("Detalles.OrdenVenta") }}',
                type: 'GET',
                data: { id: NumeroOrden },
                success: function (response) {
                    if(response.Estatus != "error"){
                        OVNombreCliente.innerHTML = response.Cliente;
                        OVNumero.innerHTML = response.OV;
                        if (response.OVEstatus != "") {
                            var estadoFabricacion = response.OVEstatus || 'Desconocido';
                            var $estatusElem = $('#OVEstatus');
                            var icono = '';
                            $estatusElem.removeClass('bg-success bg-danger bg-secondary').addClass('badge');
                            if (estadoFabricacion === 'Abierta') {
                                $estatusElem.removeClass('bg-danger bg-secondary').addClass('bg-success');
                                icono = '<i class="fas fa-lock-open"></i>';  // Ícono de "Abierta"
                                $estatusElem.html(icono + ' Abierta');
                            } else if (estadoFabricacion === 'Cerrada') {
                                $estatusElem.removeClass('bg-success bg-secondary').addClass('bg-danger');
                                icono = '<i class="fas fa-lock"></i>';  // Ícono de "Cerrada"
                                $estatusElem.html(icono + ' Cerrada');
                                console.log('Estado: Cerrada, Clases: bg-danger');
                            } else {
                                $estatusElem.removeClass('bg-success bg-danger').addClass('bg-secondary');
                                icono = '<i class="fas fa-question-circle"></i>';  // Ícono de "Desconocido"
                                $estatusElem.html(icono + ' Estado desconocido');
                            }
                        }
                        var progreso = response.progreso;
                        if(progreso == 0){
                            $('#OVBloque0porciento').show();
                        }else{
                            $('#OVBloque0porciento').hide();
                        }
                        var OVprogressBar = $('#OVBarrraProgreso');
                        OVprogressBar.css('width', progreso + '%').text(progreso + '%');
                        OVprogressBar.removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                        // Asignar color según el porcentaje
                        if (progreso >= 0 && progreso < 20) {
                            OVprogressBar.addClass('bg-danger');  // Rojo
                        } else if (progreso >= 20 && progreso < 40) {
                            OVprogressBar.addClass('bg-warning');  // Naranja
                        } else if (progreso >= 40 && progreso < 70) {
                            OVprogressBar.addClass('bg-primary');  // Azul
                        } else if (progreso >= 70 && progreso < 90) {
                            OVprogressBar.addClass('bg-info');  // Celeste
                        } else {
                            OVprogressBar.addClass('bg-success');  // Verde
                        }
                        $('#DetallesOrdenVenta').fadeIn();
                    }else{
                        error("Error Orden de Venta "+NumeroOrden, response.message);
                    }
                },
                error: function () {
                    OVNumero.innerHTML = "";
                    OVEstatus.innerHTML = "";
                    $('#OVBarrraProgreso').removeClass('bg-success bg-danger bg-secondary').text('Sin datos');
                    /*var progressBar = $('#plemasProgressBar');
                    TiempoDuracion.html("");
                    progressBar.css('width', '0%').text('0%').removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                    $('#ordenFabricacionNumero').removeClass('text-info').addClass('text-muted').text(ordenfabricacion);
                    $('#EstatusFabricacion').removeClass('bg-success bg-danger bg-secondary').text('Sin datos');*/
                    errorBD();
                }
            }); 
        }else{
            var ordenfabricacion = $('#NumeroOrden').val();
            var Bloque0porciento = $('#Bloque0porciento'); 
            var TiempoDuracion = $('#TiempoDuracion');
            var Produccion = $('#Produccion');
            var TiempoTotal = $('#TiempoTotal');
            var Muerto = $('#Muerto')
            var plemasCanvases = $('#plemasCanvases');
            var TiempoPromedio = $('#TiempoPromedio');
            var OrdenVenta = $('#OrdenVenta');
            var NombreCliente = $('#NombreCliente');
            OrdenVenta.html('');
            NombreCliente.html('');
            plemasCanvases.html('');
            TiempoTotal.html('Tiempo Total')
            Produccion.html('Tiempo Total');
            Muerto.html('Tiempo Total');
            CadenaTiempo="Aún no ha comenzado el proceso";
            $.ajax({
                url: '{{ route("Detalles.Fabricacion") }}',
                type: 'GET',
                data: { id: ordenfabricacion },
                success: function (response) {
                    var progressBar = $('#plemasProgressBar');
                    if (response.Estatus !== 'Error') {
                        var progreso = response.progreso;
                        if(progreso==0){
                            Bloque0porciento.show();
                        }else{
                            Bloque0porciento.hide();
                        }
                        if(response.TiempoProductivo != 0){
                            Produccion.html('Tiempo total<br>'+response.TiempoProductivo);
                        }else{
                            Produccion.html('El dato se verá reflejado cuando la orden sea finalizada');
                        }
                        if(response.TiempoTotal != 0){
                            TiempoTotal.html('Tiempo total<br>'+response.TiempoTotal);
                        }else{
                            TiempoTotal.html('El dato se verá reflejado cuando la orden sea finalizada');
                        }
                        if(response.TiempoMuerto != 0){
                            Muerto.html('Tiempo total<br>'+response.TiempoMuerto);
                        }else{
                            Muerto.html('El dato se verá reflejado cuando la orden sea finalizada');
                        }
                        if(!response.TiempoDuracion==0){
                            CadenaTiempo="";
                            if(response.TiempoDuracion.y!=0){CadenaTiempo+=response.TiempoDuracion.y+" Años "}
                            if(response.TiempoDuracion.m!=0){CadenaTiempo+=response.TiempoDuracion.m+" Meses "}
                            if(response.TiempoDuracion.d!=0){CadenaTiempo+=response.TiempoDuracion.d+" Días "}
                            if(response.TiempoDuracion.h!=0){CadenaTiempo+=response.TiempoDuracion.h+" Horas "}
                            if(response.TiempoDuracion.i!=0){CadenaTiempo+=response.TiempoDuracion.i+" Minutos "}
                            if(response.TiempoDuracion.s!=0){CadenaTiempo+=response.TiempoDuracion.s+" Segundos"}
                        }else{
                            CadenaTiempo="El dato se verá reflejado cuando la orden sea finalizada";
                        }
                        if(!response.TiempoPromedioSeg==""){
                            TiempoPromedio.html(response.TiempoPromedioSeg);
                        }else{
                            TiempoPromedio.html("El dato se verá reflejado cuando la orden sea finalizada");
                        }
                        //Orden Venta y Nombre Cliente
                        OrdenVenta.html(response.OV);
                        NombreCliente.html(response.Cliente);
                        TiempoDuracion.html(CadenaTiempo);
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
                        $('#ordenFabricacionNumero').removeClass('text-muted').text(ordenfabricacion);
                        if (response.Estatus != "") {
                            var estadoFabricacion = response.Estatus || 'Desconocido';
                            var $estatusElem = $('#EstatusFabricacion');
                            var icono = '';
                            $estatusElem.removeClass('bg-success bg-danger bg-secondary').addClass('badge');
                            if (estadoFabricacion === 'Abierta') {
                                $estatusElem.removeClass('bg-danger bg-secondary').addClass('bg-success');
                                icono = '<i class="fas fa-lock-open"></i>';  // Ícono de "Abierta"
                                $estatusElem.html(icono + ' Abierta');
                            } else if (estadoFabricacion === 'Cerrada') {
                                $estatusElem.removeClass('bg-success bg-secondary').addClass('bg-danger');
                                icono = '<i class="fas fa-lock"></i>';  // Ícono de "Cerrada"
                                $estatusElem.html(icono + ' Cerrada');
                                console.log('Estado: Cerrada, Clases: bg-danger');
                            } else {
                                $estatusElem.removeClass('bg-success bg-danger').addClass('bg-secondary');
                                icono = '<i class="fas fa-question-circle"></i>';  // Ícono de "Desconocido"
                                $estatusElem.html(icono + ' Estado desconocido');
                            }
                        }
                        EstacionesGraficas ='';
                        var ArrayPorcentajeGrafica = [];
                        var ArrayNombrePorcentajeGrafica = [];
                        if((response.Estaciones).length!=0){
                            (response.Estaciones).forEach(area => {
                                ColorProgress="";
                                if(area.PorcentajeActual<25){
                                    ColorProgress = ' #D32F2F ';
                                }
                                if(area.PorcentajeActual>=25 && area.PorcentajeActual<50){
                                    ColorProgress = ' #FF7043 '; 
                                }
                                if(area.PorcentajeActual>=50 && area.PorcentajeActual<75){
                                    ColorProgress = ' #FFEB3B ';
                                }
                                if(area.PorcentajeActual>=75 && area.PorcentajeActual<=100){
                                    ColorProgress = ' #38f41a ';
                                }
                                if(area.AP != 1){
                                    EstacionesGraficas+='<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-4  col-xxl-3 my-2 Estacion_Hover">'+
                                                            '<div class="card rounded border-0 p-2" style="box-shadow: 3px 3px 3px 2px rgba(0.1, 0.1, 0.1, 0.2);">'+
                                                                '<div class="card-header py-1" style="background:'+ColorProgress+';"><h5 class="text-center text-white">'+area.NombreArea+'</h5></div>'
                                                                +'<div class="progress-container">'+
                                                                    '<div class="progress-circle" style="background: conic-gradient('+ColorProgress+' 0% '+area.PorcentajeActual+'%, #e0e0e0 '+area.PorcentajeActual+'% 100%);">'+
                                                                        '<div class="progress-Porcentaje"><h5>'+area.PorcentajeActual+'%</h5></div>'+   
                                                                    '</div>'+
                                                                '</div>';
                                    if(response.RequiereCorte == 0 && area.NombreArea=='Corte'){
                                        EstacionesGraficas+='<span class="badge bg-warning">No requiere Corte</span>';
                                    }
                                    EstacionesGraficas+='<small class="float-start"><span class="float-start">Piezas Normales:'+area.Normales+'</span><span class="float-end"> Piezas Retrabajo:'+area.Retrabajo+'</span></small><h6 class="text-center mt-2">Tiempos</h6><small>Duración: '+area.TiempoOrdenes+'</small><small>Productivo: '+area.TiempoProductivoEstacion+'</small>'+
                                                            '</div>'+
                                                        '</div>';
                                    ArrayPorcentajeGrafica.push({ value: area.TiempoEstacionSegundos, name: area.NombreArea});
                                    ArrayNombrePorcentajeGrafica.push(area.NombreArea);
                                }
                            });
                        }else{
                            EstacionesGraficas = '<h5 class="text-center">Aún no se asigna una Línea</h5>';
                        }
                        plemasCanvases.html(EstacionesGraficas);
                    } else {
                        TiempoDuracion.html("");
                        error('Error de la Orden de Fabricación',response.Message);
                        Bloque0porciento.show();
                        progressBar.css('width', '0%').text('0%').removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                        $('#ordenFabricacionNumero').removeClass('text-info').addClass('text-muted').text(ordenfabricacion);
                        $('#EstatusFabricacion').removeClass('bg-success bg-danger bg-secondary').text('Sin datos');
                    }
                    // Mostrar el modal
                    $('#DetallesOrdenFabricacion').fadeIn();
                    $('#AccordeFiltroOrdenBtn').trigger('click');
                    //if((response.Estaciones).length!=0){
                        var chart = echarts.init(document.getElementById('GraficaPorcentajeTiempos'));
                        var option = {
                            title: {
                                text: 'Porcentaje de Tiempo por Estación',
                                subtext: '% Tiempo en segundos',
                                left: 'center'
                            },
                            tooltip: {
                                trigger: 'item',
                                formatter: function (params) {
                                    var totalSeconds = params.value;
                                    var hours = Math.floor(totalSeconds / 3600);
                                    var minutes = Math.floor((totalSeconds % 3600) / 60);
                                    var seconds = totalSeconds % 60;
                                    var timeParts = [];
                                    if (hours > 0) {
                                        timeParts.push(`${hours}h`);
                                        timeParts.push(`${minutes}m`);
                                        timeParts.push(`${seconds}s`);
                                    }
                                    else if (minutes > 0){
                                        timeParts.push(`${minutes}m`);
                                        timeParts.push(`${seconds}s`);
                                    }
                                    else if (seconds > 0 || timeParts.length === 0) timeParts.push(`${seconds}s`);
                                    return `${params.name}: (${timeParts.join(' ')})`;
                                }
                                /*formatter: '{b}: ({c} segundos)'*/
                            },
                            legend: {
                                type: 'scroll',
                                orient: 'vertical',
                                left: '5%',
                                top: 'middle',
                                itemGap: 5,            // Más distancia entre entradas
                                //bottom: 20,
                                data: ArrayNombrePorcentajeGrafica
                            },
                            series: [
                                {
                                name: 'Tiempo en horas',
                                type: 'pie',
                                radius: ['25%', '40%'], 
                                center: ['50%', '50%'],
                                //avoidLabelOverlap: true,  // Previene superposición de etiquetas
                                itemStyle: {
                                    borderWidth: 1,         // Línea entre segmentos para separación visual
                                    borderColor: '#fff'
                                },
                                label: {
                                    //position: 'outside',    // Etiquetas afuera para mayor espacio
                                    //alignTo: 'labelLine',
                                    formatter: '{b}: {d}%', 
                                    //distance: 1            // Distancia desde el gráfico
                                },
                                labelLine: {
                                    smooth: false,
                                    length: 20              // Longitud de línea guía
                                },
                                data: ArrayPorcentajeGrafica,
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
                        if((response.Estaciones).length==0){
                            chart.setOption(option);
                            $('#ContainerGraficaPorcentajeTiempos').hide();
                        }else{
                            $('#ContainerGraficaPorcentajeTiempos').show();
                        }
                    // Establecer la opción y renderizar el gráfico
                    chart.setOption(option);
                    //$('#example2Modal').on('shown.bs.modal', function () {
                    chart.resize();
                    //});
                    // Hacer que el gráfico sea responsivo al cambiar el tamaño de la ventana
                    window.addEventListener('resize', function() {
                    chart.resize();
                    });//--}}
                },
                error: function () {
                    var progressBar = $('#plemasProgressBar');
                    TiempoDuracion.html("");
                    progressBar.css('width', '0%').text('0%').removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                    $('#ordenFabricacionNumero').removeClass('text-info').addClass('text-muted').text(ordenfabricacion);
                    $('#EstatusFabricacion').removeClass('bg-success bg-danger bg-secondary').text('Sin datos');
                    errorBD();
                }
            });  
        }
    }
</script>
@endsection