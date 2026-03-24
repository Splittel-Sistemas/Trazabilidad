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
        .OF-Estacionhover {
            transition: transform 0.3s ease; /* Duración y tipo de transición */
        }
        .OF-Estacionhover:hover{
            transform: scale(1.04);
        }
        #OF_EstatusFabricacion, #OV_EstatusFabricacion{
            transform: scale(1.2);
        }
        /*Nuevo Codigo CSS*/
        .gauge-wrapper{
            position: relative;
            width: 80%;
            margin: 0 auto;
        }
        .gauge{
            width: 100%;
            fill: none;
            stroke-width: 10;
            stroke-linecap: round;
        }
        .gauge-bg{
            stroke: #e9ecef;
        }
        .gauge-fill{
            /*stroke: #14a0f7;*/
            /* El valor 126 representa el largo total del arco */
            stroke-dasharray: 126;
            stroke-dashoffset: 126; /* Inicia vacío */
            transition: stroke-dashoffset 1.5s ease-in-out;
        }
        .percentage-label{
            position: absolute;
            bottom: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 150%;
            font-weight: bold;
            color: #2c3e50;
        }
        /*badge-top*/
        .badge-top{
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        #OF_NoCorte{
            width: 90%;
            position: absolute;
            top: 35%;
            left: 5%;
            z-index: 2;
            background: rgb(0, 47, 255);
            color: white;
        }
        #OF_Prioridad{
            transform: scale(1.2);
        }
        .progress{
            min-width: 10rem;
        }
    </style>
@endsection
@section('content')
    <!--Nuevo Codigo-->
    <!-- Breadcrumbs -->
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Progreso</h4>
        </div>
    </div>
    <nav style="--phoenix-breadcrumb-divider: '&gt;&gt;';" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{route('index.operador')}}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Progreso</li>
        </ol>
    </nav>
    <div class="container mt-3">
        <!--Filtro-->
        <div class="row">
            <div class="col-12 p-0 mb-1">
                <div class="row justify-content-center">
                    <div class="col-3">
                        <label class="form-label" for="tipoOrden">Tipo de Orden</label>
                        <select class="form-select form-select-sm" aria-label=".form-select-sm" id="TipoOrden">
                            <option value="OV"><i class="fas fa-shopping-cart me-1"></i> Orden de venta</option>
                            <option value="OF"><i class="fas fa-industry me-1"></i> Orden de fabricaci&oacute;n</option>
                        </select>
                    </div>
                    <div class="mb-2 col-7">
                        <label class="form-label" for="basic-url">Ingresa n&uacute;mero de orden</label>
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
        <!--Detalles-->
        <div class="row">
            <!--Orden Venta-->
            <div class="col-12 pt-4 rounded border-0" id="DetallesOrdenVenta" style="display: none;">
                <div class="card shadow" id="OV_DetallesOrdenFabricacion_Detalles">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Detalles</h5>
                        <p class="mb-0 mt-2 mx-3" style="position: absolute;right:1rem;top:0;">
                            <span class="badge bg-danger" id="OV_EstatusFabricacion"></span> 
                        </p>
                    </div>
                    <div class="card-body px-0 py-0">
                        <h5 class="text-center mt-2" id="OV_">OV</h5>
                        <h5 class="text-center mx-2" id="OV_cliente">Cliente</h5>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">No. OF</th>
                                        <th class="text-center">Articulo</th>
                                        <th class="text-center">Descripci&oacute;n</th>
                                        <th class="text-center">Fecha Entrega</th>
                                        <th>Estatus</th>
                                        <th class="text-center">Avance Total</th>
                                    </tr>
                                </thead>
                                <tbody id="OV_tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--Orden Fabricacion-->
            <div class="col-12 pt-3 " id="DetallesOrdenFabricacion" style="display: none;">
                <div class="card shadow" id="DetallesOrdenFabricacion_Detalles">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Detalles</h5>
                        <p class="mb-0 mt-2 mx-3" style="position: absolute;right:1rem;top:0;">
                            <span class="badge bg-danger" id="OF_EstatusFabricacion"></span> 
                        </p>
                    </div>
                    <div class="card-body px-0 py-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">No. OF</th>
                                        <th class="text-center">No. OV</th>
                                        <th class="text-center">Cliente</th>
                                        <th>Estatus</th>
                                        <th class="text-center">Avance Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center fw-black p-1" id="OF_ordenFabricacionNumero"></td>
                                        <td class="text-center fw-black p-1" id="OF_OrdenVenta"></td>
                                        <td class="text-center p-1" id="OF_NombreCliente"></td>
                                        <td class="p-1"><span id="OF_Prioridad" class="badge rounded-pill">normal</span></td>
                                        <td class="text-center p-1">
                                            <div class="d-flex justify-content-between mb-1 mx-4">
                                                <small><span id="OF_CantidadActual">75</span>/<span id="OF_CantidadTotal">100</span></small>
                                                <small class="fw-bold" id="OF_CantidadActual_Porcentaje">75%</small>
                                            </div>
                                            <div class="progress mx-2" style="height:10px">
                                                <div  id="OF_CantidadActual_PorcentajeProgress" class="progress-bar bg-info" style="width: 75%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="fw-black py-0 px-5" colspan="3">Descripci&oacute;n</td>
                                        <td class="text-center fw-black p-0" colspan="1">Fecha Entrega</td>
                                        <td class="text-center fw-black p-0" colspan="1">Fecha Fin</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center p-1" colspan="3" id="OF_Descripcion" style="font-size: 0.80rem;"></td>
                                        <td class="text-center p-1" colspan="1" id="OF_FechaEntrega" style="font-size: 0.80rem;"></td>
                                        <td class="text-center p-1" colspan="1" id="OF_FechaFin" style="font-size: 0.80rem;"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-3">
                    <hr class="my-4">
                    <h4>Seguimiento</h4>
                    <p class="mb-1">Órden de Fabricación por Estaci&oacute;n.</p>
                    <h5 class="mb-4 text-muted" id="OF_TiempoTotal"></h5>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3 mb-3 OF-Estacionhover">
                            <div class="card border border-light">
                                <div class="card-body">
                                    <h4 class="card-title mb-1">Planeaci&oacute;n</h4>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="text-700" id="text-planeacion-date"></h6>
                                        </div>
                                        <h4>
                                            <span class=" badge-top badge badge-phoenix badge-phoenix-primary rounded-pill fs--1 ms-2">
                                                <span class="badge-label" id="text-planeacion-CantidadCompletada"></span>
                                            </span>
                                        </h4>
                                    </div>
                                    <div class="gauge-container border-bottom">
                                        <div class="gauge-wrapper">
                                            <svg viewBox="0 0 100 55" class="gauge">
                                                <path class="gauge-bg" d="M10,45 A40,40 0 0,1 90,45" />
                                                <path id="progress_planeacion" class="gauge-fill" d="M10,45 A40,40 0 0,1 90,45" />
                                            </svg>
                                            <div class="percentage-label" id="text-planeacion">0%</div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="d-flex align-items-center mb-2">
                                            <div class="bullet-item bg-primary me-2"></div>
                                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Inicio</h6>
                                            <h6 class="text-900 fw-semi-bold mb-0" id="text-planeacion-FechaInicio"></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3 mb-3 OF-Estacionhover">
                            <div class="card border border-light h-100">
                                <div class="card-body pb-1">
                                    <h4 class="card-title mb-1">Corte</h4>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="text-700" id="text-corte-date"></h6>
                                        </div>
                                        <h4>
                                            <span class=" badge-top badge badge-phoenix badge-phoenix-primary rounded-pill fs--1 ms-2">
                                                <span class="badge-label" id="text-corte-CantidadCompletada"></span>
                                            </span>
                                        </h4>
                                    </div>
                                    <h3 class="text-center px-2" id="OF_NoCorte" style="display: none">No Requiere Corte</h3>
                                    <div class="gauge-container border-bottom">
                                        <div class="gauge-wrapper">
                                            <svg viewBox="0 0 100 55" class="gauge">
                                                <path class="gauge-bg" d="M10,45 A40,40 0 0,1 90,45" />
                                                <path id="progress_corte" class="gauge-fill" d="M10,45 A40,40 0 0,1 90,45" />
                                            </svg>
                                            <div class="percentage-label" id="text-corte">0%</div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="d-flex align-items-center mb-2">
                                            <div class="bullet-item bg-primary me-2"></div>
                                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Inicio</h6>
                                            <h6 class="text-900 fw-semi-bold mb-0" id="text-corte-FechaInicio"></h6>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                            <div class="bullet-item bg-primary-100 me-2"></div>
                                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Fin</h6>
                                            <h6 class="text-900 fw-semi-bold mb-0" id="text-corte-FechaFin"></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2" id="UsuariosNombre_Corte">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3 mb-3 OF-Estacionhover">
                            <div class="card border border-light h-100">
                                <div class="card-body pb-1">
                                    <h4 class="card-title mb-1">Suministro</h4>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="text-700" id="text-suministro-date"></h6>
                                        </div>
                                        <h4>
                                            <span class=" badge-top badge badge-phoenix badge-phoenix-primary rounded-pill fs--1 ms-2">
                                                <span class="badge-label" id="text-suministro-CantidadCompletada"></span>
                                            </span>
                                        </h4>
                                    </div>
                                    <div class="gauge-container border-bottom">
                                        <div class="gauge-wrapper">
                                            <svg viewBox="0 0 100 55" class="gauge">
                                                <path class="gauge-bg" d="M10,45 A40,40 0 0,1 90,45" />
                                                <path id="progress_suministro" class="gauge-fill" d="M10,45 A40,40 0 0,1 90,45" />
                                            </svg>
                                            <div class="percentage-label" id="text-suministro">0%</div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="d-flex align-items-center mb-2">
                                            <div class="bullet-item bg-primary me-2"></div>
                                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Inicio</h6>
                                            <h6 class="text-900 fw-semi-bold mb-0" id="text-suministro-FechaInicio"></h6>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                            <div class="bullet-item bg-primary-100 me-2"></div>
                                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Fin</h6>
                                            <h6 class="text-900 fw-semi-bold mb-0" id="text-suministro-FechaFin"></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2" id="UsuariosNombre_Suministro">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3 mb-3 OF-Estacionhover">
                            <div class="card border border-light h-100">
                                <div class="card-body pb-1">
                                    <h4 class="card-title mb-1">Asignaci&oacute;n</h4>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="text-700" id="text-asignacion-date"></h6>
                                        </div>
                                        <h4>
                                            <span class=" badge-top badge badge-phoenix badge-phoenix-primary rounded-pill fs--1 ms-2">
                                                <span class="badge-label" id="text-asignacion-CantidadCompletada"></span>
                                            </span>
                                        </h4>
                                    </div>
                                    <div class="gauge-container border-bottom">
                                        <div class="gauge-wrapper">
                                            <svg viewBox="0 0 100 55" class="gauge">
                                                <path class="gauge-bg" d="M10,45 A40,40 0 0,1 90,45" />
                                                <path id="progress_asignacion" class="gauge-fill" d="M10,45 A40,40 0 0,1 90,45" />
                                            </svg>
                                            <div class="percentage-label" id="text-asignacion">0%</div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="d-flex align-items-center mb-2">
                                            <div class="bullet-item bg-primary me-2"></div>
                                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Inicio</h6>
                                            <h6 class="text-900 fw-semi-bold mb-0" id="text-asignacion-FechaInicio"></h6>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                            <div class="bullet-item bg-primary-100 me-2"></div>
                                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Fin</h6>
                                            <h6 class="text-900 fw-semi-bold mb-0" id="text-asignacion-FechaFin"></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2" id="UsuariosNombre_Asignacion">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3 mb-3 OF-Estacionhover">
                            <div class="card border border-light h-100">
                                <div class="card-body pb-1">
                                    <h4 class="card-title mb-1">Empaque</h4>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="text-700" id="text-empaque-date"></h6>
                                        </div>
                                        <h4>
                                            <span class=" badge-top badge badge-phoenix badge-phoenix-primary rounded-pill fs--1 ms-2">
                                                <span class="badge-label" id="text-empaque-CantidadCompletada"></span>
                                            </span>
                                        </h4>
                                    </div>
                                    <div class="gauge-container border-bottom">
                                        <div class="gauge-wrapper">
                                            <svg viewBox="0 0 100 55" class="gauge">
                                                <path class="gauge-bg" d="M10,45 A40,40 0 0,1 90,45" />
                                                <path id="progress_empaque" class="gauge-fill" d="M10,45 A40,40 0 0,1 90,45" />
                                            </svg>
                                            <div class="percentage-label" id="text-empaque">0%</div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="d-flex align-items-center mb-2">
                                            <div class="bullet-item bg-primary me-2"></div>
                                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Inicio</h6>
                                            <h6 class="text-900 fw-semi-bold mb-0" id="text-empaque-FechaInicio"></h6>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                            <div class="bullet-item bg-primary-100 me-2"></div>
                                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Fin</h6>
                                            <h6 class="text-900 fw-semi-bold mb-0" id="text-empaque-FechaFin"></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2" id="UsuariosNombre_Empaque">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Scripts -->
    <script src="{{asset('js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('menu2/vendors/echarts/echarts.min.js')}}"></script>
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
        //Trae una lista al buscar un numero de orden
        $('#Btn-BuscarOrden').on('click', function () {
            var NumeroOrden = $('#NumeroOrden').val().trim();
            if (NumeroOrden === '') {
                return;  
            }
            $('#NumeroOrden').val(NumeroOrden).trigger('input');
        });
        AjaxOrden = null;
        //Esconde la lista y borra el numero escrito de la OV al cambiar
        $('#TipoOrden').on('change', function(){
            $('#ListaBusquedas').html('');
            $('#ListaBusquedas').hide(); 
            $('#NumeroOrden').val(''); 
        })
        //Al escribir en el input de busqueda, va trayendo las OF u OV
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
            TipoOrden = $('#TipoOrden').val();
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
        function SeleccionarNumOrden(NumeroOrden,Tipo){
            if(Tipo == 'OF'){
                $('#NumeroOrden').val(NumeroOrden);
            }
            $('#ListaBusquedas').hide();
            $('.ver-fabricacion').trigger('click');
            if(Tipo == 'OF'){
                $('#DetallesOrdenVenta').hide();
            }
            $('#DetallesOrdenFabricacion').hide();
            OrdenVenta = $('#TipoOrden').val();
            DetallesOrdenFabricacion_Detalles =$('#DetallesOrdenFabricacion_Detalles');
            DetallesOrdenFabricacion_Detalles.css('display','none');
            if(OrdenVenta == 'OV' && Tipo=='OF'){
                //Variables
                OV_= $('#OV_');
                OV_cliente = $('#OV_cliente');
                OV_EstatusFabricacion = $('#OV_EstatusFabricacion');
                OV_tbody = $('#OV_tbody');
                //Limpiar Variables
                OV_.html('');
                OV_cliente.html('');
                OV_EstatusFabricacion.html('');
                OV_tbody.html('');
                $.ajax({
                    url: '{{ route("Detalles.OrdenVenta") }}',
                    type: 'GET',
                    data: { id: NumeroOrden },
                    success: function (response) {
                        if(response.Estatus != "error"){
                            //Generales OV
                            OV_.html("OV "+response.OV);
                            OV_cliente.html("Cliente "+ response.OV_Cliente);
                            OV_EstatusFabricacion.html(response.OV_Estatus);
                            (response.OV_Estatus=='Cerrada')?OV_EstatusFabricacion.removeClass('bg-success'):OV_EstatusFabricacion.removeClass('bg-danger');
                            (response.OV_Estatus=='Cerrada')?OV_EstatusFabricacion.addClass('bg-danger'):OV_EstatusFabricacion.addClass('bg-success');
                            OrdenesFabricacion = "";
                            (response.OV_Arr).forEach(OF => {
                                OrdenesFabricacion += `<tr class="tr_OF">
                                        <td class="text-center fw-black p-1"><button class="btn btn-sm btn-soft-secondary me-1 mb-1 btn-OF" type="button" onclick="SeleccionarNumOrden(${OF.OF},'OV')">${OF.OF}</button></td>
                                        <td class="text-center p-1" style="font-size: 0.80rem;">${OF.OF_Articulo}</td>
                                        <td class="text-center p-1" style="font-size: 0.80rem;">${OF.OF_Descripcion}</td>
                                        <td class="text-center p-1" style="font-size: 0.80rem;">${OF.OF_FechaEntregaSAP}</td>
                                        <td class="p-1"><span class="badge rounded-pill">${(OF.OF_Cerrada==1)?'<span class="badge bg-success">Abierta</span>':'<span class="badge bg-danger" id="OV_EstatusFabricacion">Cerrada</span>'}</span></td>
                                        <td class="text-center p-1">
                                            <div class="d-flex justify-content-between mb-1 mx-4">
                                                <small><span>${OF.OF_CantidadActual}</span>/<span>${OF.OF_CantidadTotal}</span></small>
                                                <small class="fw-bold">${OF.OF_ProgresoTotal}%</small>
                                            </div>
                                            <div class="progress mx-2" style="height:10px">
                                                <div class="progress-bar bg-info" style="width: ${OF.OF_ProgresoTotal}%"></div>
                                            </div>
                                        </td>
                                    </tr>`;    
                            });
                            OV_tbody.html(OrdenesFabricacion);
                            $('#DetallesOrdenVenta').show();
                        }else{
                            error("Error Orden de Venta "+NumeroOrden, response.message);
                        }
                    },
                    error: function () {
                        errorBD();
                    }
                }); 
            }else{
                var ordenfabricacion = NumeroOrden;
                //Variables
                if(Tipo=='OF'){
                    ordenfabricacion = $('#NumeroOrden').val();
                    DetallesOrdenFabricacion_Detalles.css('display','')
                }
                //Nuevas variables
                var OF_CantidadActual_PorcentajeProgress = $('#OF_CantidadActual_PorcentajeProgress')
                var OF_CantidadActual = $('#OF_CantidadActual')
                var OF_CantidadActual_Porcentaje = $('#OF_CantidadActual_Porcentaje')
                var OF_CantidadTotal = $('#OF_CantidadTotal')
                var OF_Prioridad = $('#OF_Prioridad')
                var OF_EstatusFabricacion = $('#OF_EstatusFabricacion')
                var OF_OrdenVenta = $('#OF_OrdenVenta')
                var OF_NombreCliente = $('#OF_NombreCliente')
                var OF_ordenFabricacionNumero = $('#OF_ordenFabricacionNumero')
                var OF_TiempoTotal = $('#OF_TiempoTotal')
                var OF_NoCorte = $('#OF_NoCorte');
                var UsuariosNombre_Corte = $('#UsuariosNombre_Corte')
                var UsuariosNombre_Suministro = $('#UsuariosNombre_Suministro')
                var UsuariosNombre_Asignacion = $('#UsuariosNombre_Asignacion')
                var UsuariosNombre_Empaque = $('#UsuariosNombre_Empaque')
                OF_Descripcion = $('#OF_Descripcion')
                OF_FechaEntrega = $('#OF_FechaEntrega')
                OF_FechaFin = $('#OF_FechaFin')
                //Planeacion
                    var OF_text_planeacion_date = $('#text-planeacion-date');
                    var OF_text_planeacion_CantidadCompletada = $('#text-planeacion-CantidadCompletada');
                    var OF_text_planeacion_FechaInicio = $('#text-planeacion-FechaInicio');
                //Corte
                    var OF_text_corte_date = $('#text-corte-date');
                    var OF_text_corte_CantidadCompletada = $('#text-corte-CantidadCompletada');
                    var OF_text_corte_FechaInicio = $('#text-corte-FechaInicio');
                    var OF_text_corte_FechaFin = $('#text-corte-FechaFin');
                //Suministro
                    var OF_text_suministro_date = $('#text-suministro-date');
                    var OF_text_suministro_CantidadCompletada = $('#text-suministro-CantidadCompletada');
                    var OF_text_suministro_FechaInicio = $('#text-suministro-FechaInicio');
                    var OF_text_suministro_FechaFin = $('#text-suministro-FechaFin');
                //Asignacion
                    var OF_text_asignacion_date = $('#text-asignacion-date');
                    var OF_text_asignacion_CantidadCompletada = $('#text-asignacion-CantidadCompletada');
                    var OF_text_asignacion_FechaInicio = $('#text-asignacion-FechaInicio');
                    var OF_text_asignacion_FechaFin = $('#text-asignacion-FechaFin');
                //Empaque
                    var OF_text_empaque_date = $('#text-empaque-date');
                    var OF_text_empaque_CantidadCompletada = $('#text-empaque-CantidadCompletada');
                    var OF_text_empaque_FechaInicio = $('#text-empaque-FechaInicio');
                    var OF_text_empaque_FechaFin = $('#text-empaque-FechaFin');

                //Limpiar campos nuevos
                    OF_CantidadActual_PorcentajeProgress.css('width',"0%");;
                    OF_CantidadActual.html('');
                    OF_CantidadActual_Porcentaje.html('');
                    OF_CantidadTotal.html('');
                    OF_Prioridad.html('');
                    OF_Prioridad.css('color','')
                    OF_Prioridad.css('background','#fff')
                    OF_OrdenVenta.html('');
                    OF_NombreCliente.html('');
                    OF_ordenFabricacionNumero.html('');
                    OF_EstatusFabricacion.html('');
                    OF_TiempoTotal.html('');
                    OF_NoCorte.css('display','none')
                    UsuariosNombre_Corte.html();
                    UsuariosNombre_Suministro.html('');
                    UsuariosNombre_Asignacion.html('');
                    UsuariosNombre_Empaque.html('');
                    OF_Descripcion.html('');
                    OF_FechaEntrega.html('');
                    OF_FechaFin.html('');
                //Planeacion
                    OF_text_planeacion_date.html('');
                    OF_text_planeacion_CantidadCompletada.html('');
                    OF_text_planeacion_FechaInicio.html('');
                //Corte
                    OF_text_corte_date.html('');
                    OF_text_corte_CantidadCompletada.html('');
                    OF_text_corte_FechaInicio.html('');
                    OF_text_corte_FechaFin.html('');
                //Suministro
                    OF_text_suministro_date.html('');
                    OF_text_suministro_CantidadCompletada.html('');;
                    OF_text_suministro_FechaInicio.html('');
                    OF_text_suministro_FechaFin.html('');
                //Asignacion
                    OF_text_asignacion_date.html('');
                    OF_text_asignacion_CantidadCompletada.html('');
                    OF_text_asignacion_FechaInicio.html('');
                //Empaque
                    OF_text_empaque_date.html('');
                    OF_text_empaque_CantidadCompletada.html('');
                    OF_text_empaque_FechaInicio.html('');
                    OF_text_empaque_FechaFin.html('');
                    OF_text_asignacion_FechaFin.html('');
                Gauge(0,'progress_planeacion','text-planeacion')
                Gauge(0,'progress_corte','text-corte')
                Gauge(0,'progress_suministro','text-suministro')
                Gauge(0,'progress_asignacion','text-asignacion')
                Gauge(0,'progress_empaque','text-empaque')
                $.ajax({
                    url: '{{ route("Detalles.Fabricacion") }}',
                    type: 'GET',
                    data: { id: ordenfabricacion },
                    success: function (response) {
                        var progressBar = $('#plemasProgressBar');
                        if (response.Estatus !== 'Error') {
                            OF_CantidadActual_PorcentajeProgress.css('width',response.ProgresoPorcentaje+"%");;
                            OF_CantidadActual.html(response.ProgresoCantidad);
                            OF_CantidadActual_Porcentaje.html(response.ProgresoPorcentaje+"%");
                            OF_CantidadTotal.html(response.CantidadTotal);
                            OF_Prioridad.html(response.Prioridad);
                            OF_Prioridad.css('color',response.Prioridad_color);
                            OF_Prioridad.css('background',response.Prioridad_background);
                            OF_OrdenVenta.html(response.OV);
                            OF_NombreCliente.html(response.Cliente);
                            OF_ordenFabricacionNumero.html(response.OrdenFabricacion);
                            OF_Descripcion.html(response.OF_descripcion);
                            OF_FechaEntrega.html(response.OF_FechaEntrega);
                            OF_FechaFin.html(response.OF_FechaFin);
                            OF_EstatusFabricacion.html(response.Estatus);
                            (response.Estatus=='Cerrada')?OF_EstatusFabricacion.removeClass('bg-success'):OF_EstatusFabricacion.removeClass('bg-danger');
                            (response.Estatus=='Cerrada')?OF_EstatusFabricacion.addClass('bg-danger'):OF_EstatusFabricacion.addClass('bg-success');
                            //Cantidad Completada
                            OF_text_planeacion_CantidadCompletada.html(response.CantidadTotal+"/"+response.CantidadTotal);
                            OF_text_corte_CantidadCompletada.html(response.PiezasActual_corte+"/"+response.CantidadTotal);
                            OF_text_suministro_CantidadCompletada.html(response.PiezasActual_suministro+"/"+response.CantidadTotal);
                            OF_text_asignacion_CantidadCompletada.html(response.PiezasActual_asignacion+"/"+response.CantidadTotal);
                            OF_text_empaque_CantidadCompletada.html(response.PiezasActual_empaque+"/"+response.CantidadTotal);
                            //Porcentaje
                            Porcentaje_planeacion = 100;
                            Porcentaje_corte = ((response.PiezasActual_corte/response.CantidadTotal)*100);
                            Porcentaje_suministro = ((response.PiezasActual_suministro/response.CantidadTotal)*100);
                            Porcentaje_asignacion = ((response.PiezasActual_asignacion/response.CantidadTotal)*100);
                            Porcentaje_empaque = ((response.PiezasActual_empaque/response.CantidadTotal)*100);
                            Gauge(Porcentaje_planeacion.toFixed(2) ,'progress_planeacion','text-planeacion')
                            Gauge(Porcentaje_corte.toFixed(2) ,'progress_corte','text-corte')
                            Gauge(Porcentaje_suministro.toFixed(2) ,'progress_suministro','text-suministro')
                            Gauge(Porcentaje_asignacion.toFixed(2) ,'progress_asignacion','text-asignacion')
                            Gauge(Porcentaje_empaque.toFixed(2) ,'progress_empaque','text-empaque')
                            //Fecha Inicio
                            FechaInicio_pla = response.FechaInicio_planeacion;
                            FechaInicio_pla = (FechaInicio_pla === "") ? '--'  : FechaMexico(FechaInicio_pla);
                            FechaInicio_corte = (response.FechaInicio_corte === "") ? '--'  : FechaMexico(response.FechaInicio_corte);
                            FechaInicio_sum = (response.FechaInicio_suministro === "") ? '--'  : FechaMexico(response.FechaInicio_suministro);
                            FechaInicio_asig = (response.FechaInicio_asignacion === "") ? '--'  : FechaMexico(response.FechaInicio_asignacion);
                            FechaInicio_emp = (response.FechaInicio_empaque === "") ? '--'  : FechaMexico(response.FechaInicio_empaque);
                            OF_text_planeacion_FechaInicio.html(FechaInicio_pla);
                            OF_text_corte_FechaInicio.html(FechaInicio_corte);
                            OF_text_suministro_FechaInicio.html(FechaInicio_sum);
                            OF_text_asignacion_FechaInicio.html(FechaInicio_asig);
                            OF_text_empaque_FechaInicio.html(FechaInicio_emp);
                            //FechaFin
                            FechaFin_corte = (response.FechaFin_corte === "") ? '--'  : FechaMexico(response.FechaFin_corte);
                            FechaFin_sum = (response.FechaFin_suministro === "") ? '--'  : FechaMexico(response.FechaFin_suministro);
                            FechaFin_asig = (response.FechaFin_asignacion === "") ? '--'  : FechaMexico(response.FechaFin_asignacion);
                            FechaFin_emp = (response.FechaFin_empaque === "") ? '--'  : FechaMexico(response.FechaFin_empaque);
                            OF_text_corte_FechaFin.html(FechaFin_corte);
                            OF_text_suministro_FechaFin.html(FechaFin_sum);
                            OF_text_asignacion_FechaFin.html(FechaFin_asig);
                            OF_text_empaque_FechaFin.html(FechaFin_emp);
                            //TiempoTranscurrido
                            OF_text_corte_date.html(response.TiempoTotal_corte);
                            OF_text_suministro_date.html(response.TiempoTotal_suministro);
                            OF_text_asignacion_date.html(response.TiempoTotal_asignacion);
                            OF_text_empaque_date.html(response.TiempoTotal_empaque);
                            OF_TiempoTotal.html(response.TiempoTotalOrden);
                            //Si no requiere corte aplica esto
                            if(response.RequiereCorte == 0){
                                Gauge(0 ,'progress_corte','text-corte');
                                OF_text_corte_date.html('');
                                OF_text_corte_FechaFin.html('');
                                OF_text_corte_FechaInicio.html('');
                                OF_NoCorte.css('display','');
                            }
                            UsuariosNombre_C = ``;
                            UsuariosNombre_S = ``;
                            UsuariosNombre_A = ``;
                            UsuariosNombre_E = ``;
                            if(response.RequiereCorte==1){
                                (response.UsuariosEstaciones.Corte).forEach(usuario => {
                                    UsuariosNombre_C += `<div class="d-flex align-items-center bg-soft rounded-pill mb-1" title="${usuario}">
                                                        <span class="uil uil-user me-2"></span>
                                                        <p class="text-800 fw-bold fs--1 mb-0 text-capitalize" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${usuario}</p>
                                                        </div>`
                                });
                            }
                            (response.UsuariosEstaciones.Suministro).forEach(usuario => {
                                UsuariosNombre_S += `<div class="d-flex align-items-center bg-soft rounded-pill mb-1" title="${usuario}">
                                                    <span class="uil uil-user me-2"></span>
                                                    <p class="text-800 fw-bold fs--1 mb-0 text-capitalize" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${usuario}</p>
                                                    </div>`
                            });
                            (response.UsuariosEstaciones.Asignacion).forEach(usuario => {
                                UsuariosNombre_A += `<div class="d-flex align-items-center bg-soft rounded-pill mb-1" title="${usuario}">
                                                    <span class="uil uil-user me-2"></span>
                                                    <p class="text-800 fw-bold fs--1 mb-0 text-capitalize" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${usuario}</p>
                                                    </div>`
                            });
                            (response.UsuariosEstaciones.Empaque).forEach(usuario => {
                                UsuariosNombre_E += `<div class="d-flex align-items-center bg-soft rounded-pill mb-1" title="${usuario}">
                                                    <span class="uil uil-user me-2"></span>
                                                    <p class="text-800 fw-bold fs--1 mb-0 text-capitalize" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${usuario}</p>
                                                    </div>`
                            });
                            UsuariosNombre_Corte.html(UsuariosNombre_C);
                            UsuariosNombre_Suministro.html(UsuariosNombre_S);
                            UsuariosNombre_Asignacion.html(UsuariosNombre_A);
                            UsuariosNombre_Empaque.html(UsuariosNombre_E);
                            $('#DetallesOrdenFabricacion').fadeIn();
                        } else {
                            error('Error de la Orden de Fabricación',response.Message);
                        }
                    },
                    error: function () {
                        errorBD();
                    }
                });  
            }
        }
        function ColorAleatorio() {
            const letras = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letras[Math.floor(Math.random() * 16)];
            }
            return color;
        }
        function Gauge(Porcentaje,Nombre_id,PorcentajeCantidad){
            const porcentajeObjetivo = Porcentaje; 
            let ColorProgress="";
            if(Porcentaje<25){
                ColorProgress = ' #D32F2F ';
            }
            if(Porcentaje>=25 && Porcentaje<50){
                ColorProgress = ' #FF7043 '; 
            }
            if(Porcentaje>=50 && Porcentaje<75){
                ColorProgress = ' #FFEB3B ';
            }
            if(Porcentaje>=75 && Porcentaje<=100){
                ColorProgress = ' #38f41a ';
            }
            const largoMaximo = 126;
            const offset = largoMaximo - (porcentajeObjetivo / 100 * largoMaximo);
            $('#'+Nombre_id).css('stroke',ColorProgress);
            setTimeout(function() {
                $('#'+Nombre_id).css('stroke-dashoffset', offset);
            }, 300);
            $({ countNum: 0 }).animate({ countNum: porcentajeObjetivo }, {
                duration: 1500,
                easing: 'swing',
                step: function() {
                    $('#'+PorcentajeCantidad).text(Math.ceil(this.countNum) + "%");
                },
                complete: function() {
                    $('#'+PorcentajeCantidad).text(porcentajeObjetivo + "%");
                }
            });
        }
        function FechaMexico(fechaISO) {
            const fecha = new Date(fechaISO);
            const opciones = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            };
            return fecha.toLocaleString('es-MX', opciones).replace(',', '');
        }
        document.addEventListener('click', function(e) {
            tr_OF = document.querySelectorAll('.tr_OF');
            tr_OF.forEach(tr => {
                tr.classList.remove('bg-300');
            });
            // Verificar si el clic fue en un botón con la clase 'btn-accion'
            if (e.target && e.target.classList.contains('btn-OF')) {
                // Encontrar el <tr> más cercano al botón clickeado
                let fila = e.target.closest('tr');
                // Alternar la clase CSS para pintar/despintar
                fila.classList.add('bg-300');
            }
        });
    </script>
@endsection