@extends('layouts.menu2')
@section('title', 'Planeacion')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{asset('css/Planecion.css')}}">
@endsection
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
        <h4 class="mb-2 text-1100">Planeaci&oacute;n</h4>
        </div>
    </div>
    <!-- Filtro por fecha -->
    <div class="row mb-2">
        <div class="col-6">
            <div class="col-sm-12">
                <div class="card border border-light ">
                  <div class="card-body p-2">
                    <div class="accordion" id="accordionFiltroOV">
                        <div class="accordion-item border-top border-300 p-0">
                            <h4 class="accordion-header mx-2" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroOV" aria-expanded="true" aria-controls="collapseFiltroOV">
                                Filtros <!--FechaOrden de Venta-->
                                </button>
                            </h4>
                            <div class="accordion-collapse collapse show" id="collapseFiltroOV" aria-labelledby="headingOne" data-bs-parent="#accordionFiltroOV">
                                <div class="accordion-body pt-0">
                                    <form id="filtroForm" method="post" class="form-horizontal row g-3 needs-validation" novalidate="">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12 ">
                                                <h5 class="mt-2">Filtro por Fecha</h5>
                                                <div class="row">
                                                    <div class=" col-6">
                                                        <label class="form-label" for="startDateInput">Fecha inicio </label>
                                                        <input type="date" name="startDate" id="startDate" class="form-control form-control-sm w-autoborder-primary" value="{{$FechaInicio}}">
                                                        <input type="hidden" name="startDate_filtroantnext" id="startDate_filtroantnext" class="form-control form-control-sm w-autoborder-primary" value="{{$FechaInicio}}">
                                                    </div>
                                                    <div class=" col-6">
                                                        <label class="form-label" for="endDateInput">Fecha fin </label>
                                                        <input type="date" name="endDate" id="endDate" class="form-control form-control-sm w-autoborder-primary" value="{{$FechaFin}}">
                                                        <input type="hidden" name="endDate_filtroantnext" id="endDate_filtroantnext" class="form-control form-control-sm w-autoborder-primary" value="{{$FechaFin}}">
                                                        <div class="invalid-feedback" id="error_endDate"></div>
                                                    </div>
                                                </div>
                                                <div class=" pt-1">
                                                    <button type="button" class="btn btn-primary btn-sm float-end" id="Filtro_fecha-btn">
                                                        <i class="fa fa-search"></i> Filtrar
                                                    </button>
                                                </div>
                                            </div>
                                            <h5 class="mt-2">Filtro por Orden</h5>
                                            <div class="col-6">
                                                <label class="form-label" for="TipoOrden">Tipo de Orden</label>
                                                <select class="form-select" onchange="BuscarOrden();" id="TipoOrden" aria-label="Default select example">
                                                    <option value="OV">Orden Venta</option>
                                                    <option value="OF">Orden Fabricaci&oacute;n</option>
                                                  </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label" for="FiltroOrden">N&uacute;mero de Orden</label>
                                                <input type="text" name="FiltroOrden" id="FiltroOrden" oninput="BuscarOrden();RegexNumeros(this);" class="form-control form-control-sm w-autoborder-primary" value="">
                                                <div class=" pt-1">
                                                    <button type="button" onclick="BuscarOrden();" class="btn btn-primary btn-sm float-end" id="Filtro_Orden-btn">
                                                        <i class="fa fa-search"></i> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
        </div>
    </div>
    <!-- Contenedor de las tablas -->
    <div class="card p-3">
        <div class="row mb-5">
            <!-- Columna 1: Tabla de Órdenes de Venta -->
            <div class="col-md-6 mb-2">
                <div class="col-12">
                    <!-- Navegación de Fechas -->
                    <h4 class="text-primary fw-bold text-center">Fechas</h4>
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <a  id="back_filterBtn" class="btn btn-link me-1 mb-1"><i class="fa fa-arrow-left"></i> Anterior</a>
                        <a  id="next_filterBtn" class="btn btn-link me-1 mb-1">Siguiente <i class="fa fa-arrow-right"></i></a>
                    </div>
                        <div id="container_table_OV" class="table-responsive">
                            <table id="table_OV" class="table table-sm fs--1 mb-0">
                                <thead class="text-center table-primary">
                                    <tr>
                                        <th class="fw-bold" colspan="1">
                                            <span id="filtro-fecha-Ov">Órdenes de Venta <br> <p>{{\Carbon\Carbon::parse($FechaInicio)->format('d/m/Y')}} - {{\Carbon\Carbon::parse($FechaFin)->format('d/m/Y')}} </p></span>
                                            <div class="input-group ">
                                                <input type="text" placeholder="Ingresa una Orden de Venta" name="filtro_ov_tabla" oninput="filtro_ov_tabla(this.value,'table_OV');" id="filtro_ov_tabla" class="form-control form-control-sm   w-autoborder-primary col-12">
                                                <!-- busca por OV<input type="text" placeholder="Ingresa una Orden de Venta" name="Filtro_buscarOV" id="Filtro_buscarOV"  class="form-control form-control-sm   w-autoborder-primary col-12">-->
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="fa fa-search"></i> buscar
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="list" id="table_OV_body">
                                    @if ($status=="empty")
                                        <tr class="text-center mt-4">
                                            <td>No existen Ordenes de Venta para el periodo <br>{{$FechaFin}} - {{$FechaInicio}}</td>
                                        </tr>
                                    @elseif($status=="success")
                                    @php $bandera=0; @endphp
                                        @foreach ($datos as $orden)
                                            @if($orden['Estatus']>0)
                                            @php $bandera=1; @endphp
                                                <tr class="table-light" id="details{{ $loop->index }}cerrar" style="cursor: pointer;" draggable="true">
                                                    <td role="button" data-bs-toggle="collapse" data-bs-target="#details{{ $loop->index }}" aria-expanded="false" aria-controls="details{{ $loop->index }}" onclick="loadContent('details{{ $loop->index }}', {{ $orden['OV'] }}, `{{ $orden['Cliente'] }}`)">
                                                        {{ $orden['OV']." - ".$orden['Cliente']}}
                                                    </td>
                                                </tr>
                                                <tr id="details{{ $loop->index }}" class="collapse">
                                                    <td class="table-border" id="details{{ $loop->index . 'llenar' }}">
                                                        <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        @if($bandera==0)
                                        <tr class="text-center mt-4">
                                            <td>No existen Ordenes de Venta para el periodo <br>{{$FechaFin}} - {{$FechaInicio}}</td>
                                        </tr>
                                        @endif
                                    @else
                                    <tr class="text-center mt-4"><td>Ocurrio un error!, No fue posible cargar los datos</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
            <!-- Columna 2: Dropzone y Tabla de Migrados -->
            <div class="col-md-6 mb-2">
                <!-- Área de Dropzone -->
                <!--Christian-->
                <div class="row">
                    <div class="col-5 mb-1 pt-1">
                        <label for="EncargadoCorte" >Selecciona Operador</label>
                        <select name="EncargadoCorte" id="EncargadoCorte" class="form-select form-select-sm border-primary w-100" onchange="RecargarTablaOF();">
                            <option value="" disabled>Selecciona operador</option>
                            <option value="OSC">Ordenes sin Corte</option>
                            @foreach($UsuariosCortes as $key=>$l)
                                <option value="{{ $l->id }}" {{ $key == 0 ? 'selected' : '' }}>
                                    {{$l->name}} {{ $l->apellido }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-7 mb-1 pt-1">
                        <label for="Filtrofecha_table2">Selecciona fecha de planeación</label>
                        <div class="input-group">
                            <input type="date" name="FiltroOF_Fecha_table2" id="FiltroOF_Fecha_table2" class="form-control form-control-sm border-primary" placeholder="Ingresa Orden de fabricación" value="{{$FechaFin}}">
                            <button id="buscarOV" class="btn btn-primary btn-sm" onclick="RecargarTablaOF();">
                                Mostrar
                            </button>
                        </div>
                    </div>
                </div>
                <div ondrop="drop(event)" ondragover="allowDrop(event)" 
                    class="dropzone mt-4 border-dashed rounded-2 min-h-0 mb-4">
                    <h5>Arrastra la Orden de Fabricacion</h5>
                    <p class="text-muted">Suelta la Orden Fabricacion</p>
                </div>
                <!-- Tabla de Migrados -->
                <div id="container_table_OF_migrados" class="table-responsive">
                    <table class="table table-sm fs--1 mb-0 table-striped" id="table_OF_migrados">
                        <thead class="table-primary text-center">
                            <tr>
                                <th colspan="6" class="fw-bold">
                                    <p style="color: black" id="filtro-fecha-Ov">Planeaci&oacute;n <br> <span id="FiltroOF_text">Fecha: {{\Carbon\Carbon::parse($FechaFin)->format('d/m/Y')}}</span></p>
                                    <div class="input-group">
                                        <input type="text" name="FiltroOF_table2"  id="FiltroOF_table2" class="form-control form-control-sm   w-autoborder-primary col-12" placeholder="Buscar Orden de fabricación u Orden de Venta" >
                                        <button id="buscarOV" class="btn btn-sm btn-primary">
                                            <i class="fa fa-search"></i> buscar
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th>&Oacute;rden Venta</th>
                                <th>&Oacute;rden Fabricaci&oacute;n</th>
                                <th>Urgente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="table-2-content">
                          
                            <!-- Aquí se añadirán las filas movidas -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast ver las Ordenes de Fabricacion Pendientes por asignar-->
    <div id="element" class="d-flex flex-center" aria-live="polite" aria-atomic="true"  style="position: fixed; bottom: 0.5rem; right: 1rem; z-index: 1050;display:none;" data-bs-delay="30000">
        <div class="toast show p-0" role="alert" data-bs-autohide="false" aria-live="assertive" aria-atomic="true" >
            <div class="toast-header bg-danger text-white p-1">
                <strong class="me-auto">Alerta</strong>
                <small>Hace un momento</small>
                <button class="btn ms-2 p-0" type="button" data-bs-dismiss="toast" aria-label="Close"><span class="uil uil-times fs-1"></span></button>
            </div>
            <div class="toast-body p-1">
                Hay partidas que aún no se han Planeado.
                <button type="button" class="btn btn-outline-danger float-end m-1" data-bs-toggle="modal" onclick="LlenarTablaVencidas()" data-bs-target="#ModalPlaneacionVencidos">Mostrar</button>
            </div>
        </div>
    </div>
    <!-- Modal Planeacion de Ordenes de Fabricacion vencido o por vencer-->
    <div id="ModalPlaneacionVencidos" class="modal fade"  role="dialog" aria-labelledby="ModalPlaneacionVencidosLabel" aria-hidden="true" style="overflow-y: auto;">
        <div class="modal-dialog modal-lg" style="max-width: 90%; width: 90%;">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="ModalPlaneacionVencidosLabel">Partidas faltantes de Planeación</h5><button class="btn p-1 btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6" id="Content_TablaVencidas">
                            <div id="container_table_OV_Vencidas" class="table-responsive">
                                <table id="table_OV_Vencidas" class="table table-sm fs--1 mb-0">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th class="fw-bold">
                                                <span id="filtro-fecha-Ov_Vencidas">Órdenes de Venta <br> <p>faltantes de Planeación </p></span>
                                                <div class="input-group ">
                                                    <input type="text" placeholder="Ingresa una Orden de Venta" oninput="filtro_ov_tabla(this.value,'table_OV_Vencidas');" name="Filtro_buscarOV_Vencidas" id="Filtro_buscarOV_Vencidas"  class="form-control form-control-sm   w-autoborder-primary col-12">
                                                    <button class="btn btn-primary btn-sm">
                                                        <i class="fa fa-search"></i> buscar
                                                    </button>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_OV_body_Vencidas">
                                        @foreach ($datos as $orden)
                                            <tr class="table-light" id="details{{ $loop->index }}cerrar" style="cursor: pointer;" draggable="true">
                                                <td role="button" data-bs-toggle="collapse" data-bs-target="#details{{ $loop->index }}" onclick="loadContent('details{{ $loop->index }}', {{ $orden['OV'] }}, `{{ $orden['Cliente'] }}`)">
                                                    {{ $orden['OV']." - ".$orden['Cliente']}}
                                                </td>
                                            </tr>
                                            <tr id="details{{ $loop->index }}" class="collapse">
                                                <td class="table-border" id="details{{ $loop->index . 'llenar' }}">
                                                    <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- Área de Dropzone -->
                            <div class="row">
                                <div class="col-5 mb-0 pt-0">
                                    <label for="EncargadoCorteModal" >Selecciona Operador</label>
                                    <select name="EncargadoCorteModal" id="EncargadoCorteModal" class="form-select form-select-sm border-primary w-100" onchange="RecargarTablaOF();">
                                        <option value="" disabled>Selecciona operador</option>
                                        <option value="OSC" >Ordenes sin Corte</option>
                                        @foreach($UsuariosCortes as $key=>$l)
                                            <option value="{{ $l->id }}" {{ $key == 0 ? 'selected' : '' }}>
                                                {{ $l->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-7 m-0">
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label for="Filtrofecha_table2">Selecciona la fecha de planeaci&oacute;n:</label>
                                            <div class="input-group">
                                                <input type="date" name="FiltroOF_Fecha_table2_vencidas" onchange="PartidasOF_modal(this)"  id="FiltroOF_Fecha_table2_vencidas" class="form-control form-control-sm   w-autoborder-primary col-12" placeholder="Ingresa Orden de fabricación" value="{{$FechaFin}}">
                                                <button id="buscarOV_vencidas" onclick="RecargarTablaOF();" class="btn btn-primary btn-sm">
                                                    Mostrar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div ondrop="drop(event)" ondragover="allowDrop(event)" 
                                class="dropzone mt-4 border-dashed rounded-2 min-h-0 mb-4">
                                <h5>Arrastra la Orden de Fabricacion</h5>
                                <p class="text-muted">Suelta la Orden Fabricacion </p>
                            </div>
                            <!-- Tabla de Migrados -->
                            <div id="container_table_OF_migrados_vencidos" class="table-responsive">
                                <table class="table table-sm fs--1 mb-0 table-striped" id="table_OF_migrados_vencidos">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th colspan="6" class="fw-bold">
                                                <p style="color: black" id="filtro-fecha-Ov_vencidos">Órdenes de Fabricación <br> <span id="FiltroOF_vencidos_text">Fecha: {{\Carbon\Carbon::parse($FechaFin)->format('d/m/Y')}}</span></p>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>&Oacute;rden Venta</th>
                                            <th>&Oacute;rden Fabrici&oacute;n</th>
                                            <th>Urgente</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-2-content_vencidos">
                                        <!-- Aquí se añadirán las filas movidas -->
                                    </tbody>
                                </table>
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
    <!-- Modal Detalles Ordenes de Fabricacion-->
    <div class="modal fade m-4" id="ModalOrdenesFabricacion" data-bs-focus="false" role="dialog" aria-labelledby="ModalOrdenesFabricacionLabel" aria-hidden="true" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white" id="ModalOrdenesFabricacionLabel">Partidas faltantes de Planeación</h5><button class="btn p-1 btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body border" id="ModalOrdenesFabricacionBody">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="FechaInicioActual" value="{{$FechaInicio }}">
    <input type="hidden" id="FechaFinActual" value="{{$FechaFin}}">
@endsection
@section('scripts')
<script src="{{ asset('js/OrdenesVenta.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    currentRequest = null;
    $(document).ready(function() {
        $('#element').toast('show');
        TablaOrdenFabricacion("{{$FechaFin}}");
        $('#Filtro_fecha-btn').click(function() {
            var startDate = $('#startDate').val();  
            var endDate = $('#endDate').val(); 
            if(CompararFechas(startDate,endDate)){
                if ($('#endDate').hasClass('is-invalid')) {
                    $('#endDate').removeClass('is-invalid');
                }
                $('#error_endDate').html('');
            }else{
                $('#endDate').addClass('is-invalid');
                $('#error_endDate').html('Requerido Fecha fin menor a Fecha inicio');
                return 0;
            }
            $('#FechaInicioActual').val(startDate);
            $('#FechaFinActual').val(endDate);
            $.ajax({
                url: "{{route('PlaneacionFF')}}", 
                type: 'POST',
                data: {
                    startDate: startDate, 
                    endDate:endDate,
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                    $('#table_OV_body').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>")
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $('#table_OV_body').html(response.data);  
                        $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>'+FormatoFecha(response.fechaHoy)+' - '+FormatoFecha(response.fechaAyer)+'</p>');
                    } else if(response.status=="empty") {
                        $('#table_OV_body').html('<p class="text-center">No existen registros para el periodo '+FormatoFecha(response.fechaHoy)+' - '+FormatoFecha(response.fechaAyer)+'</p>');
                        $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>'+FormatoFecha(response.fechaHoy)+' - '+FormatoFecha(response.fechaAyer)+'</p>');
                    }else{
                        error("Ocurrio un error!....","Los datos no pudieron ser procesados correctamente");
                    }
                },
                error: function(xhr, status, error) {
                    errorBD();
                }
            });
        });
        /*$('#Filtro_buscarOV').on('input',function() {
            RegexNumeros(document.getElementById('Filtro_buscarOV'));
            OV=$('#Filtro_buscarOV').val();
            if(CadenaVacia(OV)){
                return 0;
            }
            if(OV.length<2){
                $('#Filtro_fecha-btn').trigger('click');
                return 0;
            }else{$('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>Filtro: '+OV+'</p>');}
            $.ajax({
                url: "{{route('PlaneacionFOV')}}", 
                type: 'PUT',
                data: {
                    OV: OV,
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                    $('#table_OV_body').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>")
                    // You can display a loading spinner here
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>Filtro: '+OV+'</p>');
                        $('#table_OV_body').html(response.data);
                    } else if(response.status==="empty") {
                        $('#table_OV_body').html('<p>No existen registros para lo orden de venta '+OV+'</p>');
                    }else{
                        error("Ocurrio un error!....","Los datos no pudieron ser procesados correctamente");
                    }
                },
                error: function(xhr, status, error) {
                    //errorBD();
                }
            });
        });*/
        $('#FiltroOF_table2').on('input',function() {
            RegexNumeros(document.getElementById('FiltroOF_table2'));
            FiltroOF_table2=$('#FiltroOF_table2').val();
            $('#FiltroOF_text').html('<p>Filtro: '+FiltroOF_table2+'</p>');
            FiltroLinea = $('#EncargadoCorte').val();//christian
            /*if(CadenaVacia(FiltroOF_table2)){
                return 0;
            }*/
            if(FiltroOF_table2.length<2){
                $('#FiltroOF_Fecha_table2').trigger('change');
                return 0;
            }else{$('#FiltroOF_Fecha_table2').html('<br><p>Filtro: '+FiltroOF_table2+'</p>');}
            $.ajax({
                url: "{{route('PlaneacionFOFOV')}}", 
                type: 'GET',
                data: {
                   FiltroLinea: FiltroLinea, //christian
                    FiltroOF_table2: FiltroOF_table2,
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                    $('#table-2-content').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>")
                },
                success: function(response) {
                if (response.status === 'success') {
                        //$('#FiltroOF_text').html('<p>Filtro: '+FiltroOF_table2+'</p>');
                        $('#table-2-content').html(response.tabla);
                    } else if(response.status==="empty") {
                        $('#table-2-content').html("<tr><td colspan='100%' align='center'>No existen registros para lo orden de venta "+FiltroOF_table2+"</td></tr>");
                    }else{
                        //error("Ocurrio un error!....","los datos no pudieron ser procesados correctamente");
                    }
                },
                error: function(xhr, status, error) {
                    //errorBD();
                }
            });
        });
        $('#back_filterBtn').click(function() {
            var startDate =$('#FechaInicioActual').val();  
            startDate=RestarDia(startDate)
            var endDate = $('#FechaInicioActual').val(); 
            endDate=RestarDia(endDate);
            $('#FechaInicioActual').val(startDate);
            $('#FechaFinActual').val(endDate);
            $.ajax({
                url: "{{route('PlaneacionFF')}}", 
                type: 'POST',
                data: {
                    startDate: startDate, 
                    endDate:endDate,
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                    $('#table_OV_body').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>")
                    // You can display a loading spinner here
                },
                success: function(response) {
                    $('#startDate').val(startDate);  
                    $('#endDate').val(endDate); 
                    if (response.status === 'success') {
                        $('#table_OV_body').html(response.data);  
                        $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>'+FormatoFecha(response.fechaHoy)+' - '+FormatoFecha(response.fechaAyer)+'</p>');
                    } else if(response.status==="empty") {
                        $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>'+FormatoFecha(response.fechaHoy)+' - '+FormatoFecha(response.fechaAyer)+'</p>');
                        $('#table_OV_body').html('<p class="text-center">No existen registros para el periodo <br>'+FormatoFecha(startDate)+' - '+FormatoFecha(endDate)+'</p>');
                    }else{
                        error("Ocurrio un error!....","Los datos no pudieron ser procesados correctamente");
                    }
                    $('#startDate_filtroantnext').val(startDate);  
                    $('#endDate_filtroantnext').val(endDate); 
                },
                error: function(xhr, status, error) {
                    errorBD();
                }
            });
        });
        $('#next_filterBtn').click(function() {
            var startDate =$('#FechaFinActual').val();  
            startDate=SumarDia(startDate);
            var endDate = $('#FechaFinActual').val(); 
            endDate=SumarDia(endDate);
            $('#FechaInicioActual').val(startDate);
            $('#FechaFinActual').val(endDate);
            $.ajax({
                url: "{{route('PlaneacionFF')}}", 
                type: 'POST',
                data: {
                    startDate: startDate, 
                    endDate:endDate,
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                    $('#table_OV_body').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>")
                    // You can display a loading spinner here
                },
                success: function(response) {
                    $('#startDate').val(startDate);  
                    $('#endDate').val(endDate); 
                    if (response.status === 'success') {
                        $('#table_OV_body').html(response.data);  
                        $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>'+FormatoFecha(response.fechaHoy)+' - '+FormatoFecha(response.fechaAyer)+'</p>');
                    } else if(response.status==="empty") {
                        $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>'+FormatoFecha(response.fechaHoy)+' - '+FormatoFecha(response.fechaAyer)+'</p>');
                        $('#table_OV_body').html('<p class="text-center">No existen registros para el periodo <br> '+FormatoFecha(startDate)+' - '+FormatoFecha(endDate)+'</p>');
                    }else{
                        error("Ocurrio un error!....","Los datos no pudieron ser procesados correctamente");
                    }
                    $('#startDate_filtroantnext').val(startDate);  
                    $('#endDate_filtroantnext').val(endDate); 
                },
                error: function(xhr, status, error) {
                    errorBD();
                }
            });
        });
        @if($VerificarSAP==0)
            error("Error SAP", "El servidor SAP no esta disponible en este momento, estamos trabajando en ello.");
        @endif
    });
    function loadContent(idcontenedor, docNum, cliente) {
        let elemento = document.getElementById(idcontenedor + "cerrar");
        if (!elemento.classList.contains('collapsed')) {
            $.ajax({
                url: "{{route('PartidasOF')}}",
                method: "GET",
                data: {
                    docNum: docNum, 
                    cliente:cliente,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function () {
                    $('#' + idcontenedor + "llenar").html("<div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div>");
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $('#' + idcontenedor + "llenar").html(response.message);
                    } else {
                        $('#' + idcontenedor + "llenar").html('<p>Error al cargar el contenido.</p>');
                    }
                },
                error: function (xhr, status, error) {
                    $('#' + idcontenedor + "llenar").html('<p>Error al cargar el contenido.</p>');
                }
            });
        } else {
            $('#' + idcontenedor + "llenar").html('');
        }
    }
    function loadContentVencidas(idcontenedor, docNum, cliente) {
        let elemento = document.getElementById(idcontenedor + "cerrar");
        if (!elemento.classList.contains('collapsed')) {
            $.ajax({
                url: "{{route('PartidasOF')}}",
                method: "GET",
                data: {
                    docNum: docNum, 
                    cliente:cliente,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function () {
                    $('#' + idcontenedor + "llenar").html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>");
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $('#' + idcontenedor + "llenar").html(response.message);
                    } else {
                        $('#' + idcontenedor + "llenar").html('<p>Error al cargar el contenido.</p>');
                    }
                },
                error: function (xhr, status, error) {
                    $('#' + idcontenedor + "llenar").html('<p>Error al cargar el contenido.</p>');
                }
            });
        } else {
            $('#' + idcontenedor + "llenar").html('');
        }
    }
    function SeleccionaFilas(campo) {
        const selectAllCheckbox = document.getElementById(campo.id);
        const checkboxes = document.querySelectorAll("."+campo.id+"rowCheckbox");
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                SeleccionarFila(null, checkbox);  // Actualiza la clase "selected"
            });
    }
    function SeleccionaFilasCorte(campo) {
        const selectAllCheckbox = document.getElementById(campo.id);
        const checkboxes = document.querySelectorAll("."+campo.id);
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
    }
    function SeleccionarFila(event, checkbox) {
        const row = checkbox.closest('tr');
        if (checkbox.checked) {
            row.classList.add("selected");
        } else {
            row.classList.remove("selected");
        }
    }
    //Permitir que el elemento sea soltado
    function allowDrop(event) {
        event.preventDefault();
    }
    // Al arrastrar uno o más elementos de la tabla
    function drag(event) {
        let selectedRows = document.querySelectorAll(".selected");
        let rowIds = [];
        selectedRows.forEach(row => {
            rowIds.push(row.id);  // Almacenamos los IDs de las filas seleccionadas
        });
        event.dataTransfer.setData("text", rowIds.join(","));
    }
    // Al soltar los elementos en el dropzone
    function drop(event) {
        let Datosenviar = [];
        event.preventDefault();
        const data = event.dataTransfer.getData("text");
        var inputFecha = document.getElementById('FiltroOF_Fecha_table2');
        var modal = $('#ModalPlaneacionVencidos');
        var responsablecorte = $('#EncargadoCorte').val();
        if (modal.is(':visible')) {
            inputFecha = document.getElementById('FiltroOF_Fecha_table2_vencidas');
            responsablecorte = $('#EncargadoCorteModal').val();
        }
        const rowIds = data.split(",");
        if (data.length === 0) {
            return 0;
        }
        if (!inputFecha.value) {
            error("Ocurrio un error!","Debes seleccionar una fecha valida");
            return 0;
        }
        const fechaHoy = new Date('{{\Carbon\Carbon::parse($FechaInicio)->format('Y-m-d')}}');
            const fechaPlaneacion = new Date(inputFecha.value);
            if(fechaPlaneacion<fechaHoy){
                error("Ocurrio un error!","Los datos no pudieron ser procesados correctamente, la fecha de planeación tiene que ser igual o mayor a la fecha de Actual");
                return 0;
            }
        rowIds.forEach(id => {
            IdRow = document.getElementById(id);
            cells = IdRow.getElementsByTagName("td");
            var checkbox = cells[8].querySelector('input[type="checkbox"]');
            let isChecked = checkbox.checked;
            var checkboxCorte = cells[10].querySelector('input[type="checkbox"]');
            let isCheckedCorte = checkboxCorte.checked;
            var LLC = cells[11].querySelector('input[type="hidden"]').value;
            if(CadenaVacia(cells[1].innerHTML)){
                error("Ocurrio un error!","Orden de fabricación no valida");
                return 0;
            }
            if(CadenaVacia(cells[6].innerHTML)){
                error("Ocurrio un error!","Orden de Venta no valida");
                return 0;
            }
            if(CadenaVacia(cells[4].innerHTML)){
                error("Ocurrio un error!","Cantidad no valida");
                return 0;
            }
            Datosenviar.push({
                OF:cells[1].innerHTML,
                Articulo:cells[2].innerHTML,
                Descripcion:cells[3].innerHTML,
                Cantidad:cells[4].innerHTML,
                Fecha_entrega:cells[5].innerHTML,
                OV:cells[6].innerHTML,
                Cliente:cells[7].innerHTML,
                Linea:1,//cells[9].innerHTML,
                Fecha_planeada:inputFecha.value,
                Escanner:isChecked,
                Corte:isCheckedCorte,
                responsablecorte: responsablecorte,
                LLC:LLC,
            });
            IdRow.remove();
        });
        //console.log("Datos a enviar:", Datosenviar);
        $.ajax({
            url: "{{route('PartidasOFGuardar')}}", 
            type: 'POST',
            data: {
                DatosPlaneacion: JSON.stringify(Datosenviar),
                _token: '{{ csrf_token() }}'  
            },
            
            beforeSend: function() {
                //$('#table_OV_body').html("<p align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>")
                // You can display a loading spinner here
            },
            success: function(response) {
                if (response.status === 'success') {
                    var OrdenFabricacion="";
                    for(var i=0;i<(response.NumOF).length;i++){
                        if(i==0){
                            OrdenFabricacion+=response.NumOF[i];
                        }else{
                            OrdenFabricacion+=","+response.NumOF[i];
                        }
                    }
                    if (modal.is(':visible')) {
                        inputFecha = document.getElementById('FiltroOF_Fecha_table2_vencidas');
                        LlenarTablaVencidas();
                    }else{
                        TablaOrdenFabricacion(inputFecha.value);
                    }
                    $('#Filtro_fecha-btn').trigger('click');
                    success("Guardado!","Las ordenes de fabricación "+OrdenFabricacion+" se guardaron correctamente!");
                    Noguardadas="";
                    NumOFNoGuardada = response.NumOFNoGuardada;
                    if(NumOFNoGuardada.length>0){
                        for(i=0;i<NumOFNoGuardada.length;i++){
                            Noguardadas+=response.NumOFNoGuardada[i]+", ";
                        }
                        error("Ocurrio un error!","Las Ordenes de Fabricación "+Noguardadas+" Ya se encuentran Planeadas con un Número de Venta diferente por favor verifique.");
                    }
                } else if(response.status==="empty") {
                    Noguardadas="";
                    NumOFNoGuardada = response.NumOFNoGuardada;
                    if(NumOFNoGuardada.length>0){
                        for(i=0;i<NumOFNoGuardada.length;i++){
                            Noguardadas+=response.NumOFNoGuardada[i]+", ";
                        }
                        error("Ocurrio un error!","Las Ordenes de Fabricación "+Noguardadas+" Ya se encuentran Planeadas con un Número de Venta diferente por favor verifique.");
                    }
                } else if(response.status==='errordate'){
                    for(i=0;i<Datosenviar.length;i++){
                        //loadContent('details21',Datosenviar[i]['OV'], `Error`);
                    }
                    error("Ocurrio un error!","Los datos no pudieron ser procesados correctamente, la fecha de planeación tiene que ser igual o mayor a la fecha de Actual");
                }else{
                    for(i=0;i<Datosenviar.length;i++){
                        //loadContent('details21',Datosenviar[i]['OV'], `Error`);
                    }
                    $('#Filtro_fecha-btn').trigger('click');
                    error("Ocurrio un error!....","Los datos no pudieron ser procesados correctamente");
                }
            },
            error: function(xhr, status) {
                for(i=0;i<Datosenviar.length;i++){
                        //loadContent('details21',Datosenviar[i]['OV'], `Error`);
                }
                error("Ocurrio un error!","Los datos no pudieron ser guardados");
            }
        });
    }
    $('#FiltroOF_Fecha_table2').on('change',function() {
        const fechaObjeto = new Date(document.getElementById('FiltroOF_Fecha_table2').value);
        const dia = String(fechaObjeto.getDate()+1).padStart(2, '0');
        const mes = String(fechaObjeto.getMonth() + 1).padStart(2, '0'); // Los meses comienzan en 0
        const año = fechaObjeto.getFullYear();
        document.getElementById('FiltroOF_text').innerHTML= "Fecha: "+dia+"/"+mes+"/"+año;
        TablaOrdenFabricacion(document.getElementById('FiltroOF_Fecha_table2').value);
    });
    function TablaOrdenFabricacion(fecha){
        var modal = $('#ModalPlaneacionVencidos');
        if (modal.is(':visible')) {
            var ResponsableUser_id = $('#EncargadoCorteModal').val();
            fecha=$('#FiltroOF_Fecha_table2_vencidas').val();
        }else{
            var ResponsableUser_id = $('#EncargadoCorte').val();
        }
        $.ajax({
            url: "{{route('PartidasOFFiltroFechas_Tabla')}}", 
            type: 'POST',
            data: {
                fecha: fecha,
                ResponsableUser_id: ResponsableUser_id,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                if (modal.is(':visible')) {
                    fecha2=$('#FiltroOF_Fecha_table2').val();
                    if(fecha==fecha2){
                        $('#table-2-content').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>");
                    }
                        $('#table-2-content_vencidos').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>");
                    }else{
                        $('#table-2-content').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>");
                    }
                // You can display a loading spinner here
            },
            success: function(response) {
                if (modal.is(':visible')) {
                    tabla=(response.tabla);
                    if(fecha==fecha2){
                        $('#table-2-content').html(tabla);
                    }
                    /*const regex = /<td class="text-center"><button type="button" onclick="DetallesOrdenFabricacion\('[^']*'\)" class="btn-sm btn-primary"><i class="fa fa-eye"><\/i>\s*Ver<\/button><\/td>/g;
                    tabla = tabla.replace(regex, 'style="display:none;"');*/
                    $('#table-2-content_vencidos').html(tabla);

                }else{
                    $('#table-2-content').html(response.tabla);
                }
            },
            error: function(xhr, status, error) {
                //errorBD();
            }
        }); 
    }
    function DetallesOrdenFabricacion(NumOF){
        Titulo=$('#ModalOrdenesFabricacionLabel');
        Cuerpo=$('#ModalOrdenesFabricacionBody');
        Titulo.html('Detalles Orden de Fabricación ');
        Cuerpo.html('');
        //$('#ModalPlaneacionVencidos').modal('hide');
        //$('#ModalPlaneacionVencidos').modal('hide');
        $('#ModalOrdenesFabricacion').modal('show'); // Muestra el modal
        $.ajax({
            url: "{{route('PartidasOF_Detalles')}}", 
            type: 'GET',
            data: {
                NumOF: NumOF,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                Cuerpo.html("<div class='d-flex justify-content-center align-items-center' style='width:100%;'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div>")
                // You can display a loading spinner here
            },
            success: function(response) {
                if(response.status=="success"){
                    Cuerpo.html(response.tabla);
                    Titulo.html('Detalles Orden de Fabricación '+response.OF);
                }else{
                    Cuerpo.html(response.tabla);
                }
                //$('#table-2-content').html(response.tabla);
                //$('#ModalOrdenesFabricacionLabel').html('Titulo');
            },
            error: function(xhr, status, error) {
                Cuerpo.html('<p class="text-center">No existen información para esta Orden de Fabricación</p>');
                $('#ModalOrdenesFabricacion').modal('hide'); // Muestra el modal
                errorBD();
            }
        }); 
    }
    function RegresarOrdenFabricacion(NumOF){
        $.ajax({
            url: "{{route('PartidasOFRegresar')}}", 
            type: 'DELETE',
            data: {
                NumOF: NumOF,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                //Cuerpo.html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>")
            },
            success: function(response) {
                if(response.status=="success"){
                    var modal = $('#ModalPlaneacionVencidos');
                    if (modal.is(':visible')) {
                        $fecha=document.getElementById('FiltroOF_Fecha_table2_vencidas').value;
                        LlenarTablaVencidas();
                    }else{
                        $fecha=document.getElementById('FiltroOF_Fecha_table2').value;
                        TablaOrdenFabricacion($fecha);
                    }
                    $('#Filtro_fecha-btn').trigger('click');
                    success("Guardado","Orden de Fabricación  "+response.OF+" regresada correctamente");
                }else if(response.status=="iniciado"){
                    error("Error!","La Orden de Fabricación ya se encuentra iniciada, no es posible detenerla");
                }else if(response.status=="error"){
                    error("Error!","Registro no encontrado");
                }else{
                    error("Ocurrio un error!","Ingresa un registro valido");
                }
            },
            error: function(xhr, status, error) {
                errorBD();
            }
        }); 
    }
    /*function CambiarEscaner(escaner,id){
        escanear=escaner.checked;
        $.ajax({
            url: "{{route('CambiarEstatusEscaner')}}", 
            type: 'POST',
            data: {
                Escanear: escanear,
                Id: id,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                //Cuerpo.html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>")
                // You can display a loading spinner here
            },
            success: function(response) {
                if(response.status=='success'){
                    if(response.valor=='false'){
                        success('Guardado correctamente!','Escáner desactivado!')
                    }else{
                        success('Guardado correctamente!','Escáner Activado!')
                    }
                }
            },
            error: function(xhr, status, error) {
                error('Ocurrio un erro!', 'El Tipo Escaner no se pudo actualizar')
            }
        }); 
    }*/
    function CambiarUrgencia(urgente,id){
        urgente=urgente.checked;
        $.ajax({
            url: "{{route('CambiarEstatusUrgencia')}}", 
            type: 'POST',
            data: {
                Urgente: urgente,
                Id: id,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                /*if(response.status=='success'){
                    if(response.valor=='false'){
                        success('Guardado correctamente!','Urgente desactivado!')
                    }else{
                        success('Guardado correctamente!','Urgente Activado!')
                    }
                }*/
                RecargarTablaOF();
            },
            error: function(xhr, status, error) {
                error('Ocurrio un erro!', 'El Tipo urgente no se pudo actualizar')
            }
        }); 
    }
    function LlenarTablaVencidas(){
        fecha=$('#FiltroOF_Fecha_table2_vencidas').val();
        Cuerpo=$('#table_OV_body_Vencidas');
        $.ajax({
            url: "{{route('LlenarTablaVencidasOV')}}", 
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                Cuerpo.html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>")
            },
            success: function(response) {
                //DetallesOrdenFabricacion(id);
                if(response.status=="success"){
                    Cuerpo.html(response.data);
                    $('#FiltroOF_vencidos_text').html(fecha);
                    TablaOrdenFabricacion(fecha);
                    //Titulo.html('Detalles Orden de Fabricación '+response.OF);
                }else{
                    Cuerpo.html(response.data);
                    TablaOrdenFabricacion(fecha);
                }
                //$('#table-2-content').html(response.tabla);
                //$('#ModalOrdenesFabricacionLabel').html('Titulo');
            },
            error: function(xhr, status, error) {
                Cuerpo.html("");
                errorBD();
            }
        }); 
    }
    function RecargarTablaOF(){
        fecha=$('#FiltroOF_Fecha_table2').val();
        TablaOrdenFabricacion(fecha);
    }
    function MostrarBtnFaltantes(boton){
        $('#'+boton).fadeIn(1000);
    }
    function PartidasOF_modal(fecha){
        TablaOrdenFabricacion(fecha.value);
    }
    // Función para buscar una palabra en una tabla con clase 'table-light'
    function buscarPalabraEnTabla(palabra) {
        // Obtener todas las filas de la tabla con la clase 'table-light'
        const filas = document.querySelectorAll('.table-light tbody tr');
        
        // Iterar sobre cada fila
        filas.forEach(fila => {
            // Obtener todas las celdas (td) de la fila
            const celdas = fila.querySelectorAll('td');
            
            // Convertir las celdas a un array para usar includes
            const celdasTexto = Array.from(celdas).map(celda => celda.textContent.trim());
            
            // Verificar si la palabra está presente en alguna celda
            if (celdasTexto.some(texto => texto.toLowerCase().includes(palabra.toLowerCase()))) {
                // Si la palabra está presente, resaltar la fila
                fila.style.backgroundColor = 'yellow';  // Puedes cambiar el color de fondo si lo deseas
            } else {
                // Si no está presente, dejar la fila sin cambios
                fila.style.backgroundColor = ''; 
            }
        });
    }
    function filtro_ov_tabla(ov,tabla){
        campo=0;
        let filas = document.querySelectorAll("#"+tabla+" tbody tr");
        console.log(filas);
        $('#'+tabla+' .collapse').collapse('hide');
        filas.forEach(fila => {  
            let valorCelda = fila.cells[campo].innerText.trim();
            valorCelda = valorCelda.toLowerCase();
            ov = ov.toLowerCase();
            if(fila.id.includes("cerrar")){
                if (valorCelda.includes(ov)) {
                fila.style.display = ""; 
                } else {
                fila.style.display = "none"; 
                }
            }
        });
    }
    function mostrarCalendario(OrdenFabricacion) {
        document.getElementById('btnEditar_' + OrdenFabricacion).classList.add('d-none');
        $('#fechaSeleccionada_' + OrdenFabricacion).prop('disabled',false);
        document.getElementById('btnGuardar_' + OrdenFabricacion).classList.remove('d-none');
    }
    function guardarFecha(OrdenFabricacion) {
        $('#Error_'+OrdenFabricacion).hide();
        $('#Error_'+OrdenFabricacion).html('');
        let fecha = document.getElementById('fechaSeleccionada_' + OrdenFabricacion).value;
        if (!fecha) {
            Swal.fire({
                title: 'Atención',
                text: 'Por favor selecciona una fecha.',
                icon: 'warning',
                confirmButtonText: 'Cerrar'
            });
            return;
        }
        // Obtener la fecha actual en formato YYYY-MM-DD
        let fechaActual = '{{\Carbon\Carbon::parse($FechaFin)->format('Y-m-d')}}';
        // Validar si la fecha seleccionada es menor a la actual
        if (fecha < fechaActual) {
            $('#Error_'+OrdenFabricacion).show();
            $('#Error_'+OrdenFabricacion).html('*La fecha de planeación no puede ser menor a la fecha actual');
            return 0;
        }
        Swal.fire({
            title: 'Actualizar',
            text: '¿Estás Seguro que deseas actualizar la fecha de Planeación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar',
            input: 'textarea',
            inputPlaceholder: 'Comentario...',
            preConfirm: (value) => {
            if (!value) {
                Swal.showValidationMessage(
                    'Campo obligatorio'
                );
                return false; // Evita que la alerta se cierre
                }
                return value; // Devuelve el valor si es válido
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (csrfTokenMeta) {
                    comentario=result.value;
                    let csrfToken = csrfTokenMeta.getAttribute('content');
                    $.ajax({
                        url: "{{ route('ActualizarFechaPlaneacion') }}",
                        type: "PUT",
                        data: {
                            OrdenFabricacion: OrdenFabricacion,
                            fecha_entrega: fecha,
                            comentario:comentario,
                            _token: csrfToken 
                        },
                        success: function(data) {
                            RecargarTablaOF();
                            Swal.fire({
                                title: 'Éxito',
                                text: 'La fecha fue actualizada correctamente.',
                                icon: 'success',
                                confirmButtonText: 'Cerrar'
                            });
                            $('#fechaSeleccionada_' + OrdenFabricacion).prop('disabled',true);
                            document.getElementById('btnGuardar_' + OrdenFabricacion).classList.add('d-none');
                            document.getElementById('btnEditar_' + OrdenFabricacion).classList.remove('d-none');
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Hubo un problema al actualizar la fecha.',
                                icon: 'error',
                                confirmButtonText: 'Cerrar'
                            });
                            console.error('Error:', error);
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se encontró el token CSRF.',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                }
            }
        });
    }
    function BuscarOrden(){
            if (currentRequest) {
                currentRequest.abort();
            }
            //TipoOrden  FiltroOrden
            FiltroOrden = $('#FiltroOrden').val();
            TipoOrden = $('#TipoOrden').val();
            if(FiltroOrden.length == 0){
                $('#Filtro_fecha-btn').trigger('click');
                return 0;
            }else if(FiltroOrden.length < 3){
                return 0;
            }else{
                if(TipoOrden == 'OV'){
                    $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>Filtro: '+FiltroOrden+'</p>');
                }else{
                    $('#filtro-fecha-Ov').html('Órdenes de Fabricación<br><p>Filtro: '+FiltroOrden+'</p>');
                }
            }
            currentRequest = $.ajax({
                url: "{{route('PlaneacionFOV')}}", 
                type: 'POST',
                data: {
                    OV: FiltroOrden,
                    TipoOrden: TipoOrden,
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                    $('#table_OV_body').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>")
                    // You can display a loading spinner here
                },
                success: function(response) {
                    if (response.status === 'success') {
                        if(TipoOrden == 'OV'){
                            $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>Filtro: '+FiltroOrden+'</p>');
                        }else{
                            $('#filtro-fecha-Ov').html('Órdenes de Fabricación<br><p>Filtro: '+FiltroOrden+'</p>');
                        }
                        $('#table_OV_body').html(response.data);
                    } else if(response.status==="empty") {
                        if(TipoOrden == 'OV'){
                            $('#table_OV_body').html('<td class="text-center">No existen registros para lo orden de venta '+FiltroOrden+' o ya se encuentra planeada</td>');
                        }else{
                            $('#table_OV_body').html('<td class="text-center">No existen registros para lo orden de Fabricación '+FiltroOrden+' o ya se encuentra planeada</td>');
                        }
                    }else{
                        error("Ocurrio un error!....","Los datos no pudieron ser procesados correctamente");
                    }
                },
                error: function(xhr, status, error) {
                    //errorBD();
                }
            });
            /*RegexNumeros(document.getElementById('Filtro_buscarOV'));
            OV=$('#Filtro_buscarOV').val();
            if(CadenaVacia(OV)){
                return 0;
            }
            if(OV.length<2){
                $('#Filtro_fecha-btn').trigger('click');
                return 0;
            }else{$('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>Filtro: '+OV+'</p>');}
            $.ajax({
                url: "{{route('PlaneacionFOV')}}", 
                type: 'PUT',
                data: {
                    OV: OV,
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                    $('#table_OV_body').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>")
                    // You can display a loading spinner here
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#filtro-fecha-Ov').html('Órdenes de Venta<br><p>Filtro: '+OV+'</p>');
                        $('#table_OV_body').html(response.data);
                    } else if(response.status==="empty") {
                        $('#table_OV_body').html('<p>No existen registros para lo orden de venta '+OV+'</p>');
                    }else{
                        error("Ocurrio un error!....","Los datos no pudieron ser procesados correctamente");
                    }
                },
                error: function(xhr, status, error) {
                    //errorBD();
                }
            });*/
    }
    function CambiarEstatusCorte(IdordenFabricacion,evento){
        EstatusCorte = document.getElementById('RequiereCorteupdate');
        EstatusCorteCheck=EstatusCorte.checked;
        errorEncargadoCorteUpdate = $('#errorEncargadoCorteUpdate');
        EncargadoCorteUpdate = $('#EncargadoCorteUpdate');
        errorEncargadoCorteUpdate.html('');
        ResponsableCorte = null;
        if(EstatusCorteCheck==true){
           if(EncargadoCorteUpdate.val() == null || EncargadoCorteUpdate.val() == 'OSC'){
            errorEncargadoCorteUpdate.html('* Para actualizar el campo Requiere Corte, selecciona un responsable.');
            EstatusCorte.checked = false;
            return 0;
           }
        }else{
            if(evento=='change'){
                return 0;
            }
        }
        //errorEncargadoCorteUpdate
        $.ajax({
            url: "{{route('CambiarCorteEstatus')}}", 
            type: 'POST',
            data: {
                Corte: EstatusCorteCheck,
                Id: IdordenFabricacion,
                IdEncargado: EncargadoCorteUpdate.val(),
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status == 'error'){
                    error('Ocurrio un error!','El Requiere Corte no se pudo actualizar.')
                }else if(response.status == 'success'){
                    DetallesOrdenFabricacion(IdordenFabricacion);
                    /*if(response.valor == false){
                        EstatusCorte.checked = true;
                    }
                    if(response.valor == true){
                        EstatusCorte.checked = false;
                    }*/
                }
                /*if(response.status=='success'){
                    if(response.valor=='false'){
                        success('Guardado correctamente!','Urgente desactivado!')
                    }else{
                        success('Guardado correctamente!','Urgente Activado!')
                    }
                }*/
                RecargarTablaOF();
            },
            error: function(xhr, status, error) {
                error('Ocurrio un erro!', 'Requiere Corte no se pudo actualizar')
            }
        }); 
    }
    function FinalizarOrdenFabricacion(IdOrdenFabricación){
        const modalEl = document.getElementById('ModalOrdenesFabricacion');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance && modalInstance._focustrap) {
            modalInstance._focustrap.deactivate();
        }
        Swal.fire({
            title: 'Finalizar Orden de Fabricación',
            text: '¿Deseas finalizar la Orden de Fabricación?',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText:"Cancelar",
            confirmButtonText: 'Finalizar',
            input: 'textarea',
            inputPlaceholder: 'Ingrese el motivo para finalizar la Orde de Fabricación',  
            inputAttributes: {
                id: 'Motivo'
            },
            inputValidator: () => null,
            didOpen: () => {
                const textarea = Swal.getInput(); // obtiene el <textarea>
                const errorText = document.createElement('p');
                errorText.id = 'errorMotivo';
                errorText.className = 'text-danger';
                errorText.style.marginTop = '2px';
                errorText.style.marginLeft = '40px';
                errorText.style.display = 'none'; // oculto por defecto
                errorText.innerText = '*El motivo es obligatorio';
                textarea.insertAdjacentElement('afterend', errorText);
            },
            preConfirm: () => {
                const textarea = Swal.getInput();
                if(!CadenaVacia(textarea.value)){
                    $('#errorMotivo').hide();
                }else{
                    $('#errorMotivo').show();
                    return false;
                }
            }
        }).then((result) => {
        if (result.isConfirmed) {
            var Motivo = result.value;
            $.ajax({
                url: "{{route('FinalizarOrdenFabricacion')}}", 
                type: 'POST',
                data: {
                    idOF:IdOrdenFabricación,
                    Motivo:Motivo,
                    Estacion:'1',
                },
                beforeSend: function() {
                    //$('#ModalDetalleBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                },
                success: function(response) {
                    if(response==1){
                        success('Guardado Correctamente','La Orden de Fabricación ha sido finalizada correctamente!');
                    }else{
                        error('Error al Finalizar la orden de fabricación!', 'Los datos no pudieron ser procesados correctamente, si persiste el error contacta a TI');
                    }
                    RecargarTablaOF();
                    $('#ModalOrdenesFabricacion').modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    errorBD();
                }
            });
        }
      });
    }
</script>
@endsection
