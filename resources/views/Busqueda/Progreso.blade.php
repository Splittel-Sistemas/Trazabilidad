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
            transform: scale(1.08);
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
                        <div class="list-group lista-busqueda" id="ListaBusquedas" style="display: none;">
                        </div>
                    </div>
                </div> 
            </div>
            <!-- Tabla 1: Orden de Venta -->
            <div id="tablaVenta" class="card p-4" style="display:none;">
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
                            <span id="ordenFabricacionNumero" class="text-muted"></span>
                        </h5>
                        <span id="EstatusFabricacion"class="" style="position: absolute;right:4rem;">Estatus</span> 
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs--1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                                <path fill="currentColor" d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body ">
                        <!-- Barra de progreso -->
                        <h5 class="text-center">Progreso de piezas completadas</h5>
                        <div class="progress" style="height: 22px; border-radius: 10px; box-shadow: 0px 3px 6px rgba(0,0,0,0.2); overflow: hidden; width: 90%; margin-left: 5%;">
                            <div id="plemasProgressBar" class="progress-bar text-white fw-bold progress-animated" role="progressbar" 
                                style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;" 
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0%
                            </div>
                            <h6 class="mx-2 mt-2" id="Bloque0porciento">0%</h6>
                        </div>
                        <div style="height:12px;"></div>
                        <div class="row justify-content-center">
                            <!-- Primera fila (4 elementos) -->
                            <div class="row mt-3">
                                <div class="col-6 mb-3 Estacion_Hover">
                                    <div class="card shadow-sm border-0 p-2 text-center">
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
                                <div class="col-6 mb-3 Estacion_Hover">
                                    <div class="card shadow-sm border-0 p-2 text-center">
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
                                <div class="col-12 col-md-4 mb-3 Estacion_Hover">
                                    <div class="card shadow-sm border-0 p-2 text-center" style="background-color: #d4edda;">
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
                                <div class="col-12 col-md-4 mb-3 Estacion_Hover ">
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
                                <div class="col-12 col-md-4 mb-3 Estacion_Hover ">
                                    <div class="card shadow-sm border-0 p-2 text-center" style="background-color: #cce5ff;">
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
                            <br>
                            <!---->
                            <!--<div class="card p-3">-->
                            <h4 class="text-center mb-2 mt-2">Estaci&oacute;nes</h4>
                            <div  class="row mb-3" id="plemasCanvases">
                            </div>
                            <div class="col-11 col-sm-11 col-md-11 Estacion_Hover my-3" id="ContainerGraficaPorcentajeTiempos">
                                <div class="card rounded border-0 p-2" style="box-shadow: 3px 3px 3px 2px rgba(0.1, 0.1, 0.1, 0.2);">
                                    <div id="GraficaPorcentajeTiempos"></div>
                                </div>
                            </div>
                            <!--<div class="col-12 col-sm-12 col-md-12 Estacion_Hover">
                                <div class="card rounded border-0 p-2" style="box-shadow: 3px 3px 3px 2px rgba(0.1, 0.1, 0.1, 0.2);">
                                    <div id="GraficaPorcentajeTiempos1">dkjkjhjdlkjskdlajsl</div>
                                </div>
                            </div>-->
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
        <input type="hidden" id="idVenta" value="">
        <input type="hidden" id="idFabricacion" value="">     
    </div>
@endsection
@section('scripts')
    <!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                //url: 'route("graficador")), 
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
    /*$(document).on('click', '.ver-fabricacion', function (e) {
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

    });*/


        
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
<script>
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
        $('#Btn-BuscarOrden').trigger('click');
    }
    
</script>
<script>
    //Nuevos Metodos
    $(document).on('click', '.ver-fabricacion', function (e) {
        var ordenfabricacion = $(this).data('ordenfabricacion');
        var Bloque0porciento = $('#Bloque0porciento'); 
        var TiempoDuracion = $('#TiempoDuracion');
        var Produccion = $('#Produccion');
        var TiempoTotal = $('#TiempoTotal');
        var Muerto = $('#Muerto')
        var plemasCanvases = $('#plemasCanvases');
        var TiempoPromedio = $('#TiempoPromedio');
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
                    }
                    if(response.TiempoTotal != 0){
                        TiempoTotal.html('Tiempo total<br>'+response.TiempoTotal);
                    }
                    if(response.TiempoMuerto != 0){
                        Muerto.html('Tiempo total<br>'+response.TiempoMuerto);
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
                        CadenaTiempo="";
                    }
                    if(!response.TiempoPromedioSeg==""){
                        TiempoPromedio.html(response.TiempoPromedioSeg);
                    }else{
                        TiempoPromedio.html("");
                    }
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
                    $('#ordenFabricacionNumero').removeClass('text-muted').addClass('text-info').text(ordenfabricacion);
                    if (response.Estatus != "") {
                        var estadoFabricacion = response.Estatus || 'Desconocido';
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
                                EstacionesGraficas+='<div class="col-12 col-sm-3 col-md-4 my-2 Estacion_Hover">'+
                                                        '<div class="card rounded border-0 p-2" style="box-shadow: 3px 3px 3px 2px rgba(0.1, 0.1, 0.1, 0.2);">'+
                                                            '<h5 class="text-center">'+area.NombreArea+'</h5>'
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
                $('#example2Modal').modal('show');
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
                //}

    // Establecer la opción y renderizar el gráfico
    chart.setOption(option);
    $('#example2Modal').on('shown.bs.modal', function () {
    chart.resize();
    });
    // Hacer que el gráfico sea responsivo al cambiar el tamaño de la ventana
    window.addEventListener('resize', function() {
      chart.resize();
    });
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
        /*
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

        */
    });
</script>
@endsection