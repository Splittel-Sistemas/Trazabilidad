@extends('layouts.menu2')
@section('title', 'Planeación')
@section('styles')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.table-bordered {
    border: 1px solid #ddd;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #ddd;
}

.table thead.thead-dark {
    background-color: #4a90e2; /* Cambiar por un color que combine con tu proyecto */
    color: white;
    font-weight: bold;
}

.table tbody tr:nth-child(odd) {
    background-color: #f9f9f9; /* Fila alterna */
}

.table tbody tr:hover {
    background-color: #e6f7ff; /* Color al pasar el ratón */
    cursor: pointer;
}

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
}

.btn-outline-info {
    color: #17a2b8;
    border-color: #17a2b8;
}

.btn-outline-info:hover {
    background-color: #17a2b8;
    color: white;
}

    .search-box {
        margin-left: 0; /* Alinea al lado izquierdo */
    }


</style>

@endsection
@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs mb-4">
        <div class="row gy-3 mb-2 justify-content-between">
            <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Cortes</h4>
            </div>
        </div>
        <!--modulos sin corte y completados-->
        <ul class="nav nav-underline" id="myTab" role="tablist">
            <li class="nav-item" role="presentation"><a class="nav-link" id="proceso-tab" data-bs-toggle="tab" href="#tab-proceso" role="tab" aria-controls="tab-proceso" aria-selected="false" tabindex="-1">
                <font style="vertical-align: inherit;">
                    <font style="vertical-align: inherit;">Sin Corte y En Proceso</font>
                </font></a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="completado-tab" data-bs-toggle="tab" href="#tab-completado" role="tab" aria-controls="tab-completado" aria-selected="false" tabindex="-1">
                  <font style="vertical-align: inherit;">
                    <font style="vertical-align: inherit;">Completados</font>
                  </font>
                </a>
              </li>
          </ul>
          <div class="tab-content mt-3" id="myTabContent">
            <div class="tab-pane fade" id="tab-proceso" role="tabpanel" aria-labelledby="proceso-tab"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">
                <div class="col-6 mt-2">
                    <div class="accordion" id="accordionFiltroOV">
                        <div class="card shadow-sm">
                            <div class="accordion-item border-top border-300">
                                <!-- Filtro por fecha -->
                                <h4 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroOV" aria-expanded="true" aria-controls="collapseFiltroOV">
                                        <strong>Filtro Por Fecha </strong>
                                    </button>
                                </h4>
                                <div class="accordion-collapse collapse show" id="collapseFiltroOV" aria-labelledby="headingOne" data-bs-parent="#accordionFiltroOV">
                                    <div class="accordion-body pt-2">
                                        <form id="filtroForm" method="post" class="form-horizontal">
                                            @csrf
                                            <div class="row">
                                                <!-- Filtro por fecha -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="fecha" class="form-label"><strong>Filtro por Fecha</strong></label>
                                                    <div class="input-group">
                                                        <input type="date" name="fecha" id="fecha" class="form-control form-control-sm rounded-3">
                                                        <button id="buscarFecha" class="btn btn-outline-primary btn-sm ms-2 rounded-3">
                                                            <i class="fa fa-search"></i> Buscar
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Filtro por Orden de Fabricación -->
                                                <!--<div class="col-md-6 mb-3">
                                                    <label for="query" class="form-label"><strong>Filtro por Orden de Fabricación</strong></label>
                                                    <div class="input-group">
                                                        <input type="text" placeholder="Ingresa una Orden de Fabricación" name="query" id="query" class="form-control form-control-sm rounded-3">
                                                        <button id="buscarOV" class="btn btn-outline-primary btn-sm ms-2 rounded-3">
                                                            <i class="fa fa-search"></i> Buscar
                                                        </button>
                                                    </div>
                                                </div>-->
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cool mt-5">
                    <div class="card shadow-sm">
                        <div id="tableExample3" data-list='{"valueNames":["orden","articulo","descripcion","cantidad","fechaSAP","fechaEstimada","estatus"],"page":5,"pagination":true}'>
                            <!-- Search Box -->
                            <div class="search-box mb-3 ms-auto">
                                <div class="card shadow-sm">
                                    <form class="position-relative border rounded-3" data-bs-toggle="search" data-bs-display="static">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-0 rounded-start">
                                                    <i class="uil uil-search text-muted"></i>
                                                </span>
                                            </div>
                                            <input class="form-control search-input search form-control-sm rounded-end border-0" type="search" placeholder="Buscar" aria-label="Buscar">
                                        </div>
                                    </form>
                                </div>
                            </div>
                
                            <!-- Table -->
                            <div class="table-responsive">
                                <table id="procesoTable" class="table table-striped table-sm fs--1 mb-1">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th class="sort border-top ps-3" data-sort="orden">Or. Fabricación</th>
                                            <th class="sort border-top" data-sort="articulo">Artículo</th>
                                            <th class="sort border-top" data-sort="descripcion">Descripción</th>
                                            <th class="sort border-top" data-sort="cantidad">Cantidad Total</th>
                                            <th class="sort border-top" data-sort="fechaSAP">Fecha SAP</th>
                                            <th class="sort border-top" data-sort="fechaEstimada">Fecha Estimada</th>
                                            <th class="sort border-top" data-sort="estatus">Estatus</th>
                                            <th class="sort text-end align-middle pe-0 border-top">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        @foreach ($ordenesFabricacion as $orden)
                                            <tr>
                                                <td class="align-middle ps-3 orden">{{ $orden->OrdenFabricacion }}</td>
                                                <td class="align-middle articulo">{{ $orden->Articulo }}</td>
                                                <td class="align-middle descripcion">{{ $orden->Descripcion }}</td>
                                                <td class="align-middle cantidad">{{ $orden->CantidadTotal }}</td>
                                                <td class="align-middle fechaSAP">{{ $orden->FechaEntregaSAP }}</td>
                                                <td class="align-middle fechaEstimada">{{ $orden->FechaEntrega }}</td>
                                                <td class="align-middle estatus">
                                                    @php
                                                        $badgeClass = match ($orden->estatus) {
                                                            'En proceso' => 'badge badge-phoenix fs--2 badge-phoenix-warning',
                                                            'Sin cortes' => 'badge badge-phoenix fs--2 badge-phoenix-secondary',
                                                            default => 'badge badge-phoenix fs--2 badge-phoenix-danger',
                                                        };
                                                        $iconClass = match ($orden->estatus) {
                                                            'En proceso' => 'ms-1 fas fa-spinner',
                                                            'Sin cortes' => 'ms-1 fas fa-times',
                                                            default => 'ms-1 fas fa-exclamation-triangle',
                                                        };
                                                    @endphp
                                                    <span class="{{ $badgeClass }}">
                                                        {{ $orden->estatus }}
                                                        <i class="{{ $iconClass }}"></i>
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" style="padding: 2px 6px; font-size: 12px; border-radius: 4px;" data-id="{{ $orden->id }}">
                                                        <i class="bi bi-eye"></i> Detalles
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Pagination Controls -->
                        <div class="d-flex justify-content-between mt-3">
                            <span data-list-info="data-list-info"></span>
                            <div class="d-flex">
                                <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                                <ul class="mb-0 pagination"></ul>
                                <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                            </div>
                        </div>
                    </div>
                </div>
                </font></font>
            </div>
                    
            <div class="tab-pane fade" id="tab-completado" role="tabpanel" aria-labelledby="completado-tab"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">

                <div class="col-6 mt-2">
                    <div class="accordion" id="accordionFiltroUnico">
                        <div class="card shadow-sm">
                            <div class="accordion-item border-top border-300">
                                <!-- Filtro por fecha -->
                                <h4 class="accordion-header" id="headingFiltroUnico">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroUnico" aria-expanded="true" aria-controls="collapseFiltroUnico">
                                        <strong>Filtros</strong>
                                    </button>
                                </h4>
                                <div class="accordion-collapse collapse show" id="collapseFiltroUnico" aria-labelledby="headingFiltroUnico" data-bs-parent="#accordionFiltroUnico">
                                    <div class="accordion-body pt-2">
                                        <form id="formFiltroUnico" method="post" class="form-horizontal">
                                            @csrf
                                            <div class="row">
                                                <!-- Filtro por fecha -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="inputFechaUnica" class="form-label"><strong>Filtro por Fecha</strong></label>
                                                    <div class="input-group">
                                                        <input type="date" name="fecha" id="inputFechaUnica" class="form-control form-control-sm rounded-3">
                                                        <button id="btnBuscarFechaUnica" class="btn btn-outline-primary btn-sm ms-2 rounded-3">
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
                <div class="cool mt-5">
                    <div class="card shadow-sm">
                        <div id="tableExample3" data-list='{"valueNames":["orden","articulo","descripcion","cantidad","fechaSAP","fechaEstimada","estatus"],"page":5,"pagination":true}'>
                            <!-- Search Box -->
                            <div class="search-box mb-3 ms-auto">
                                <div class="card shadow-sm">
                                    <form class="position-relative border rounded-3" data-bs-toggle="search" data-bs-display="static">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-0 rounded-start">
                                                    <i class="uil uil-search text-muted"></i>
                                                </span>
                                            </div>
                                            <input class="form-control search-inputt search form-control-sm rounded-end border-0" type="search" placeholder="Buscar" aria-label="Buscar">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div class="card shadow-sm">
                                    <table id="completadoTable" class="table table-striped table-sm fs--1 mb-1">
                                        <thead class="bg-primary text-white">
                                            <tr>
                                                <th class="sort border-top ps-3" data-sort="orden">Or. Fabricación</th>
                                                <th class="sort border-top" data-sort="articulo">Artículo</th>
                                                <th class="sort border-top" data-sort="descripcion">Descripción</th>
                                                <th class="sort border-top" data-sort="cantidad">Cantidad Total</th>
                                                <th class="sort border-top" data-sort="fechaSAP">Fecha SAP</th>
                                                <th class="sort border-top" data-sort="fechaEstimada">Fecha Estimada</th>
                                                <th class="sort border-top" data-sort="estatus">Estatus</th>
                                                <th class="sort text-end align-middle pe-0 border-top">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">
                                            @foreach ($ordenesFabricacion as $orden)
                                                <tr>
                                                    <td class="align-middle ps-3 orden">{{ $orden->OrdenFabricacion }}</td>
                                                    <td class="align-middle articulo">{{ $orden->Articulo }}</td>
                                                    <td class="align-middle descripcion">{{ $orden->Descripcion }}</td>
                                                    <td class="align-middle cantidad">{{ $orden->CantidadTotal }}</td>
                                                    <td class="align-middle fechaSAP">{{ $orden->FechaEntregaSAP }}</td>
                                                    <td class="align-middle fechaEstimada">{{ $orden->FechaEntrega }}</td>
                                                    <td class="align-middle estatus">
                                                        @php
                                                            $badgeClass = match ($orden->estatus) {
                                                                'Completado' => 'badge badge-phoenix fs--2 badge-phoenix-success',
                                                                'En proceso' => 'badge badge-phoenix fs--2 badge-phoenix-warning',
                                                                'Sin cortes' => 'badge badge-phoenix fs--2 badge-phoenix-secondary',
                                                                default => 'badge badge-phoenix fs--2 badge-phoenix-danger',
                                                            };
                                                            $iconClass = match ($orden->estatus) {
                                                                'Completado' => 'ms-1 fas fa-check',
                                                                default => 'ms-1 fas fa-exclamation-triangle',
                                                            };
                                                        @endphp
                                                        <span class="{{ $badgeClass }}">
                                                            {{ $orden->estatus }}
                                                            <i class="{{ $iconClass }}"></i>
                                                        </span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" style="padding: 2px 6px; font-size: 12px; border-radius: 4px;" data-id="{{ $orden->id }}">
                                                            <i class="bi bi-eye"></i> Detalles
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                
                            <div class="d-flex justify-content-between mt-3">
                                <span data-list-info="data-list-info"></span>
                                <div class="d-flex">
                                    <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                                    <ul class="mb-0 pagination"></ul>
                                    <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </font></font>
            </div>
        <!-- Modal de Detalles de la Orden -->
        <div class="modal fade bd-example-modal-x" id="modalDetalleOrden" tabindex="-1"  role="dialog" aria-labelledby="modalDetalleOrdenLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    
                    <div class="modal-header p-2" style="background-color: #1d6cfd; --bs-bg-opacity: .8;">
                        <h5 class="modal-title" id="modalDetalleOrdenLabel">Detalles de la Orden de Fabricacion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Sección de detalles -->
                        <div class="mb-4">
                            <h5 class="text-secondary"><i class="bi bi-info-circle"></i></h5>
                            <table class="table table-striped table-bordered table-sm">
                                <tbody id="modalBodyContent">
                                    <!-- Aquí se insertarán los datos dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Apartado de cortes del día -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5 class="text-secondary"><i class="bi bi-scissors"></i> </h5>
                            <form id="formCortesDia" class="needs-validation d-flex align-items-center" novalidate>
                                <div class="mb-2 d-flex align-items-center">
                                    <label for="numCortes" class="form-label ms-2 mb-0">Registrar Cantidad:</label> <!-- Eliminar margen inferior con mb-0 -->
                                    <input type="number" class="form-control form-control-sm ms-2" id="numCortes" name="numCortes" min="0" placeholder="Ingresa el número" required>
                                    <button type="button" id="confirmar" class="btn btn-outline-success btn-sm ms-2" data-id="{{ $orden->id }}">Confirmar</button>
                                </div>
                                
                            </form>
                           
                            <div id="cortesGuardados" class="mt-3 text-success fw-bold">
                                <div class="table-responsive">
                                    <table id="tablaCortes" class="table table-bordered table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Cortes De Piezas</th>
                                                <th>Fecha De Registro</th>
                                                <th>Fecha De Finalizacion</th>
                                                <th> <button class="btn btn-outline-primary  btn-sm ms-2" id="pdfRangos" data-id="">Generar PDF de Rangos</button></th>
                                                
                                            </tr>
                                        </thead>
                                        <div class="modal-footer d-flex justify-content-between">
                                        </div>
                                        <tbody>
                                            <!-- Cortes de la tabla PartidasOF se reflejan aquí -->
                                        </tbody>
                                    </table> 
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal para mostrar la información de la orden -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header p-2" style="background-color: #84c3ec; --bs-bg-opacity: .8;">
                        <h5 class="modal-title" id="myModalLabel">Información de la Orden de Fabricación</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="color: red; font-size: 1.25rem; background: none; border: none; padding: 3; line-height: 2;">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <!-- Contenedor con desplazamiento dinámico -->
                            <div id="partidas-lista" style="max-height: 400px; overflow-y: auto;">
                               
                                    
                                <!-- Aquí se llenarán las partidas dinámicamente -->
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" id="btn-descargar-pdf" class="btn btn-primary" data-id="">Descargar PDF</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="myModalRangos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header p-2" style="background-color: #84c3ec; --bs-bg-opacity: .8;">
                        <h5 class="modal-title" id="exampleModalLabel">Selecciona los Rangos para el PDF</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="color: red; font-size: 1.25rem; background: none; border: none; padding: 3; line-height: 2;">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="formRangoPDF" method="POST" action="{{ route('pdfcondicion') }}">
                            @csrf
                            <input type="hidden" id="orden_fabricacion_id" name="id">
                            <div class="mb-3">
                                <label for="desde_no" class="form-label">Desde No:</label>
                                <input type="number" name="desde_no" id="desde_no">
                            </div>
                            <div class="mb-3">
                                <label for="hasta_no" class="form-label">Hasta No:</label>
                                <input type="number" name="hasta_no" id="hasta_no">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" id="btn-pdf-descarga" class="btn btn-primary" data-id="">Generar PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="ordenFabricacionId" value="">
    </div>
@endsection

@section('scripts')

<script>
$(document).ready(function() {
// Evento al hacer clic en "Detalles Completado"
$('#completadoTable' ).on('click', '.ver-detalles', function() {
    var ordenFabricacionId = $(this).data('id');

    // Asignar el ID de la orden de fabricación a los botones correspondientes
    $('#pdfRangos').attr('data-id', ordenFabricacionId);
    $('#btn-pdf-descarga').attr('data-id', ordenFabricacionId);
   

    // Obtener los detalles de la orden de fabricación
    $.ajax({
        url: '{{ route("corte.getDetalleOrden") }}',
        type: 'GET',
        data: { id: ordenFabricacionId },
        success: function(response) {
            if (response.success) {
                // Mostrar los detalles de la orden en el modal
                $('#modalBodyContent').html(`
                    <div class="table-responsive">
                              <table id="ordenFabricacionTable" class="table table-striped table-sm fs--1 mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="sort border-top ps-3" data-sort="orden">Or. Fabricación</th>
                                <th class="sort border-top" data-sort="articulo">Artículo</th>
                                <th class="sort border-top" data-sort="descripcion">Descripción</th>
                                <th class="sort border-top" data-sort="cantidad">Cantidad Total</th>
                                <th class="sort border-top" data-sort="fechaSAP">Fecha SAP</th>
                                <th class="sort border-top" data-sort="fechaEstimada">Fecha Estimada</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <tr>
                                <td class="align-middle ps-3 orden">${response.data.OrdenFabricacion}</td>
                                <td class="align-middle articulo">${response.data.Articulo}</td>
                                <td class="align-middle descripcion">${response.data.Descripcion}</td>
                                <td class="align-middle cantidad">${response.data.CantidadTotal}</td>
                                <td class="align-middle fechaSAP">${response.data.FechaEntregaSAP}</td>
                                <td class="align-middle fechaEstimada">${response.data.FechaEntrega}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                `);
                // Asignar el ID de la orden a un campo oculto
                $('#ordenFabricacionId').val(response.data.id);
                // Obtener los cortes registrados para esta orden
                obtenerCortes(ordenFabricacionId);
                // Mostrar el modal con los detalles de la orden
                $('#modalDetalleOrden').modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Error al obtener los detalles de la orden.');
        }
    });
});
//Evento al hacer clic en Detalles Sin Cortes, En proceso
$('#procesoTable' ).on('click', '.ver-detalles', function() {
    var ordenFabricacionId = $(this).data('id');

    // Asignar el ID de la orden de fabricación a los botones correspondientes
    $('#pdfRangos').attr('data-id', ordenFabricacionId);
    $('#btn-pdf-descarga').attr('data-id', ordenFabricacionId);
   

    // Obtener los detalles de la orden de fabricación
    $.ajax({
        url: '{{ route("corte.getDetalleOrden") }}',
        type: 'GET',
        data: { id: ordenFabricacionId },
        success: function(response) {
            if (response.success) {
                // Mostrar los detalles de la orden en el modal
                $('#modalBodyContent').html(`
                    <div class="table-responsive">
                              <table id="ordenFabricacionTable" class="table table-striped table-sm fs--1 mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="sort border-top ps-3" data-sort="orden">Or. Fabricación</th>
                                <th class="sort border-top" data-sort="articulo">Artículo</th>
                                <th class="sort border-top" data-sort="descripcion">Descripción</th>
                                <th class="sort border-top" data-sort="cantidad">Cantidad Total</th>
                                <th class="sort border-top" data-sort="fechaSAP">Fecha SAP</th>
                                <th class="sort border-top" data-sort="fechaEstimada">Fecha Estimada</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <tr>
                                <td class="align-middle ps-3 orden">${response.data.OrdenFabricacion}</td>
                                <td class="align-middle articulo">${response.data.Articulo}</td>
                                <td class="align-middle descripcion">${response.data.Descripcion}</td>
                                <td class="align-middle cantidad">${response.data.CantidadTotal}</td>
                                <td class="align-middle fechaSAP">${response.data.FechaEntregaSAP}</td>
                                <td class="align-middle fechaEstimada">${response.data.FechaEntrega}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                `);
                // Asignar el ID de la orden a un campo oculto
                $('#ordenFabricacionId').val(response.data.id);
                // Obtener los cortes registrados para esta orden
                obtenerCortes(ordenFabricacionId);
                // Mostrar el modal con los detalles de la orden
                $('#modalDetalleOrden').modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Error al obtener los detalles de la orden.');
        }
    });
});
$('#confirmar').click(function () {
    const numCortes = parseInt($('#numCortes').val().trim());
    const ordenFabricacionId = $('#ordenFabricacionId').val();

    if (!numCortes || numCortes <= 0 || isNaN(numCortes)) {
        alert('Por favor, ingrese un número válido de cortes.');
        return;
    }

    if (!ordenFabricacionId) {
        alert('No se ha seleccionado una orden de fabricación.');
        return;
    }
    var url = "{{ route('orden-fabricacion.cortes-info', ['ordenFabricacionId' => '__ordenFabricacionId__']) }}".replace('__ordenFabricacionId__', ordenFabricacionId);


    // Validar y guardar cortes
    $.ajax({
        url: url,
        type: 'GET',
        success: function (infoResponse) {
            if (!infoResponse.success) {
                alert('Error al obtener la información de la orden de fabricación: ' + infoResponse.message);
                return;
            }

            const cantidadTotal = parseInt(infoResponse.CantidadTotal);
            const cortesRegistrados = parseInt(infoResponse.cortes_registrados);

            if (cortesRegistrados + numCortes > cantidadTotal) {
                alert('El número total de cortes excede la cantidad total de la orden.');
                return;
            }

            // Preparar datos para guardar
            const datosPartidas = [{
                cantidad_partida: numCortes,
                fecha_fabricacion: new Date().toISOString().split('T')[0],
                orden_fabricacion_id: ordenFabricacionId
            }];

            $.ajax({
                url: '{{ route("guardar.partida") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    datos_partidas: datosPartidas
                },
                success: function (saveResponse) {
                    if (saveResponse.status === 'success') {
                        alert('Partidas guardadas correctamente.');

                        // Actualizar la tabla de cortes
                        obtenerCortes(ordenFabricacionId);
                        actualizarTablaPrincipal(ordenFabricacionId) 
                        actualizarTablasecundaria(ordenFabricacionId)
                    
                    } else {
                        alert('Errores: ' + saveResponse.errores.join(', '));
                    }
                },
                error: function (xhr) {
                    console.error('Error al guardar partidas:', xhr.responseText);
                    alert('Error al guardar las partidas: ' + xhr.responseText);
                }
            });
        },
        error: function (xhr) {
            console.error('Error al obtener información de cortes:', xhr.responseText);
            alert('Error al obtener información de cortes: ' + xhr.responseText);
        }
    });
});
// Función para obtener y mostrar los cortes registrados
function obtenerCortes(ordenFabricacionId) {
    $.ajax({
        url: '{{ route("corte.getCortes") }}',
        type: 'GET',
        data: { id: ordenFabricacionId },
        success: function (cortesResponse) {
            if (cortesResponse.success) {
                const cortesHtml = cortesResponse.data.reverse().map((corte, index) => `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${corte.cantidad_partida}</td>
                        <td>${corte.fecha_fabricacion}</td>
                        <td>${corte.FechaFinalizacion || ''}</td>
                        <td>
                            <button type="button" class="btn btn-outline-primary btn-generar-etiquetas" data-id="${corte.id}">Generar Etiquetas</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-danger btn-finalizar" data-id="${corte.id}">Finalizar</button>
                        </td>
                        
                    </tr>
                `).join('');

                $('#tablaCortes tbody').html(cortesHtml);
            } else {
                $('#tablaCortes tbody').html('<tr><td colspan="6" class="text-center">No se encontraron cortes.</td></tr>');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('Error al obtener los cortes.');
        }
    });
}
//actualizamos la tabla sin corte y en proceso
function actualizarTablaPrincipal() {
    $.ajax({
        url: '{{ route("actualizar.tabla") }}',
        method: 'GET',
        success: function (data) {
            const tabla = $('#procesoTable tbody');
            tabla.empty(); // Limpiar la tabla actual
            // Filtrar solo las órdenes con estatus "Completado"
            const ordenesFiltradas = data.filter(orden => orden.estatus === 'Sin cortes','En proceso');

            data.forEach((orden) => {
                const estatusClass = {
                    
                    'En proceso': 'badge badge-phoenix fs--2 badge-phoenix-warning',
                    'Sin cortes': 'badge badge-phoenix fs--2 badge-phoenix-secondary'
                }[orden.estatus] || 'badge-phoenix-danger';

                const estatusIcon = {
                    'En proceso': 'fas fa-stream',
                    'Sin cortes': 'fas fa-ban'
                }[orden.estatus] || 'fas fa-times';

                const row = `
                    <tr>
                        <td>${orden.OrdenFabricacion}</td>
                        <td>${orden.Articulo}</td>
                        <td>${orden.Descripcion}</td>
                        <td>${orden.CantidadTotal}</td>
                        <td>${orden.FechaEntregaSAP}</td>
                        <td>${orden.FechaEntrega}</td>
                        <td class="text-center align-middle">
                            <span class="badge ${estatusClass}">
                                <span class="fw-bold">${orden.estatus}</span>
                                <span class="${estatusIcon}"></span>
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" data-id="${orden.id}">
                                <i class="bi bi-eye me-1"></i> Detalles
                            </a>
                           
                        </td>
                    </tr>`;
                tabla.append(row);
            });
        },
        error: function () {
            alert('Ocurrió un error al actualizar la tabla.');
        }
    });
}
//actualizamos la tabla completado
function actualizarTablasecundaria() {
    $.ajax({
        url: '{{ route("actualizar.tabla") }}',
        method: 'GET',
        success: function (data) {
            const tabla = $('#completadoTable tbody');
            tabla.empty(); // Limpiar la tabla actual

            // Filtrar solo las órdenes con estatus "Completado"
            const ordenesFiltradas = data.filter(orden => orden.estatus === 'Completado');

            ordenesFiltradas.forEach((orden) => {
                const estatusClass = {
                    'Completado': 'badge badge-phoenix fs--2 badge-phoenix-success',
                }[orden.estatus] || 'badge-phoenix-danger';

                const estatusIcon = {
                    'Completado': 'fas fa-check',
                }[orden.estatus] || 'fas fa-times';

                const row = `
                    <tr>
                        <td>${orden.OrdenFabricacion}</td>
                        <td>${orden.Articulo}</td>
                        <td>${orden.Descripcion}</td>
                        <td>${orden.CantidadTotal}</td>
                        <td>${orden.FechaEntregaSAP}</td>
                        <td>${orden.FechaEntrega}</td>
                        <td class="text-center align-middle">
                            <span class="badge ${estatusClass}">
                                <span class="fw-bold">${orden.estatus}</span>
                                <span class="${estatusIcon}"></span>
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" data-id="${orden.id}">
                                <i class="bi bi-eye me-1"></i> Detalles
                            </a>
                        </td>
                    </tr>`;
                tabla.append(row);
            });
        },
        error: function () {
            alert('Ocurrió un error al actualizar la tabla.');
        }
    });
}

// Al hacer clic en el botón "Finalizar"
$(document).on('click', '.btn-finalizar', function() {
    var corteId = $(this).data('id');
    var fechaHoraActual = new Date().toISOString().slice(0, 19).replace('T', ' ');

    $.ajax({
        url: '{{ route("corte.finalizarCorte") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: corteId,
            fecha_finalizacion: fechaHoraActual
        },
        success: function(response)
        
        {
            if (response.success) {
                // Recargar la tabla de cortes
                obtenerCortes($('#ordenFabricacionId').val());
            } else {
                alert('Error al finalizar el corte: ' + response.message);
            }
            obtenerCortes(ordenFabricacionId);
            $.ajax({
                            url: "{{ route('orden-fabricacion.update-status') }}",
                            method: "POST",
                            data: {
                                id: ordenFabricacionId,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                if (response.success) {
                                    // Actualizar el badge de estatus en la tabla
                                    const row = $('tr[data-id="'+ ordenFabricacionId +'"]');
                                    const badge = row.find('.estatus .badge');

                                    let badgeClass;
                                    switch (response.estatus) {
                                        case 'Completado':
                                            badgeClass = 'badge-success';
                                            break;
                                        case 'En proceso':
                                            badgeClass = 'badge-warning';
                                            break;
                                        default:
                                            badgeClass = 'badge-danger';
                                    }

                                    badge.attr('class', `badge ${badgeClass}`).text(response.estatus);
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function (xhr) {
                                alert('Error al actualizar el estatus');
                            }
                        });
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Error al finalizar el corte.');
        }
    });
    
});
// Abrir modal para rangos PDF
$('#pdfRangos').on('click', function() {
    var ordenFabricacionId = $(this).attr('data-id');
    $('#orden_fabricacion_id').val(ordenFabricacionId);
    $('#myModalRangos').modal('show');
});
// Evento para mostrar la información de la orden cuando se hace clic en el botón
$(document).on('click', '.btn-generar-etiquetas', function() {
    var corteId = $(this).data('id');

    $.ajax({
        url: '{{ route("mostrar.etiqueta") }}',
        data: { id: corteId },
        type: 'GET',
        success: function(response) {
            if (response.error) {
                alert(response.error);
            } else {
                $('#partidas-lista').html('');

                var partidasHtml = '';
                response.partidas.forEach(function(partida) {
                    partidasHtml += `
                        <div>
                            <p><strong>No:</strong> ${partida.cantidad}</p>
                            <p><strong>Orden de Fabricación:</strong> ${partida.orden_fabricacion}</p>
                            <p><strong>Descripción:</strong> ${partida.descripcion}</p>
                        </div>
                        <hr>`;
                });

                $('#partidas-lista').html(partidasHtml);
                $('#myModal').modal('show');

                $('#btn-descargar-pdf').data('id', corteId);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error:', error);
            console.log('Detalles:', xhr.responseText);
            alert('Error al cargar los datos.');
        }
    });
});
// Evento para descargar el PDF cuando se hace clic en el botón de descargar
$(document).on('click', '#btn-descargar-pdf', function() {
    var corteId = $(this).data('id');
    if (!corteId) {
        alert('No se encontró el ID');
        return;
    }

    // Generar la URL usando Laravel route()
    var url = "{{ route('generar.pdf', ['id' => '__corteId__']) }}".replace('__corteId__', corteId);

    // Abre la URL para descargar el PDF
    window.open(url, '_blank');
});
// Filtro orden de fabricación
$('#buscarOV').on('click', function(event) {
    event.preventDefault(); // Previene el comportamiento por defecto

    var query = $('#query').val(); // Obtiene el valor de búsqueda
    var fechaHaceUnaSemana = new Date();
    fechaHaceUnaSemana.setDate(fechaHaceUnaSemana.getDate() - 7); // Resta 7 días para obtener la fecha de hace una semana
    fechaHaceUnaSemana = fechaHaceUnaSemana.toISOString().slice(0, 10); // Convierte la fecha al formato YYYY-MM-DD

    // Si el campo de búsqueda está vacío, se agrega el parámetro fechaHaceUnaSemana
    var data = { query: query };
    if (!query) {
        data.fechaHaceUnaSemana = fechaHaceUnaSemana; // Agrega fechaHaceUnaSemana solo si no hay consulta
    }

    $.ajax({
        url: '{{ route("buscar.ordenes") }}',
        type: 'GET',
        data: data,  // Envía los datos, que ahora incluyen fechaHaceUnaSemana si es necesario
        beforeSend: function() {
            // Muestra el loader en la tabla mientras se cargan los datos
            $('#ordenFabricacionTable tbody').html(`
                <tr>
                    <td colspan="8" align="center">
                        <img src="{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}" alt="Cargando..." />
                    </td>
                </tr>
            `);
        },
        success: function(response) {
            // Limpia las filas previas
            $('#ordenFabricacionTable tbody').empty();

            if (response.length > 0) {
                // Filtra los resultados para que no haya duplicados
                let seen = new Set();
                let uniqueResults = response.filter(item => {
                    const isDuplicate = seen.has(item.OrdenFabricacion);
                    seen.add(item.OrdenFabricacion);
                    return !isDuplicate;
                });

                // Agrega las filas a la tabla
                uniqueResults.forEach(item => {
                    // Determina las clases e íconos del estatus
                    let badgeClass = '';
                    let badgeIcon = '';
                    switch (item.estatus) {
                        case 'Completado':
                            badgeClass = 'badge badge-phoenix fs--2 badge-phoenix-success';
                            badgeIcon = 'ms-1 fas fa-check';
                            break;
                        case 'En proceso':
                            badgeClass = 'badge badge-phoenix fs--2 badge-phoenix-warning';
                            badgeIcon = 'ms-1 fas fa-spinner';
                            break;
                        case 'Sin cortes':
                            badgeClass = 'badge badge-phoenix fs--2 badge-phoenix-secondary';
                            badgeIcon = 'ms-1 fas fa-times';
                            break;
                        default:
                            badgeClass = 'badge-danger';
                            badgeIcon = 'fas fa-times';
                    }

                    // Crea una fila de la tabla
                    var row = `
                        <tr>
                            <td>${item.OrdenFabricacion}</td>
                            <td>${item.Articulo}</td>
                            <td>${item.Descripcion}</td>
                            <td>${item.CantidadTotal}</td>
                            <td>${item.FechaEntregaSAP}</td>
                            <td>${item.FechaEntrega}</td>
                            <td><span class="badge ${badgeClass} d-block mt-2" style="font-size: 12px;">
                                <span class="fw-bold">${item.estatus}</span>
                                <span class="ms-1 ${badgeIcon}"></span>
                            </span></td>
                            <td><a href="#" class="btn btn-outline-warning btn-xs ver-detalles d-flex align-items-center justify-content-center" 
                                style="padding: 2px 6px; font-size: 12px; border-radius: 4px;" data-id="${item.id}">
                                 Detalles
                            </a></td>
                        </tr>
                    `;
                    $('#ordenFabricacionTable tbody').append(row);
                });
            } else {
                // Si no hay resultados, muestra un mensaje
                $('#ordenFabricacionTable tbody').html(`
                    <tr>
                        <td colspan="8" class="text-center">No se encontraron resultados para la orden ingresada.</td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            alert('Error al buscar la orden: ' + error);
        }
    });
});
// Función para obtener la clase del badge según el estatus
function getBadgeClass(estatus) {
    switch (estatus) {
        case 'Completado': return 'badge-success';
        case 'En proceso': return 'badge-warning';
        default: return 'badge-danger';
    }
}
});
//estatus
$(document).on('click', '.btn-estatus', function () {
        const button = $(this);
        const id = button.data('id');

        $.ajax({
        url: "{{ route('orden-fabricacion.update-status') }}",
        method: "POST",
        data: {
            id: id,
            _token: "{{ csrf_token() }}"
        },
        success: function (response) {
            if (response.success) {
                // Actualizar el badge en la tabla
                const row = button.closest('tr');
                const badge = row.find('.estatus .badge');

                let badgeClass;
                let badgeIcon;
                switch (response.estatus) {
                    case 'Completado':
                        badgeClass = 'badge-phoenix-success';
                        badgeIcon = 'fas fa-check'; // Icono de éxito
                        break;
                    case 'En proceso':
                        badgeClass = 'badge-phoenix-warning';
                        badgeIcon = 'fas fa-stream'; // Icono de pendiente
                        break;
                    case 'Sin cortes':
                        badgeClass = 'badge-phoenix-secondary';
                        badgeIcon = 'fas fa-ban'; // Icono de bloqueado
                        break;
                    default:
                        badgeClass = 'badge-phoenix-danger';
                        badgeIcon = 'fas fa-times'; // Icono de error por defecto
                }

                // Actualizar la clase, icono y el texto del badge
                badge.attr('class', `badge ${badgeClass} d-block mt-2`)
                    .html(`<span class="fw-bold">${response.estatus}</span><span class="ms-1 ${badgeIcon}"></span>`);
            } else {
                alert(response.message);
            }
        },
        error: function (xhr) {
            alert('Error al actualizar el estatus');
        }
    });

});
//////////////////////////////////////////////////////////////////////////////////
//buscar por fecha
document.getElementById('buscarFecha').addEventListener('click', function (e) {
    e.preventDefault();
    
    const fecha = document.getElementById('fecha').value;
    
    if (!fecha) {
        alert('Por favor, selecciona una fecha.');
        return;
    }

    // Realiza la solicitud AJAX
    fetch('{{ route("Fitrar.Fecha") }}', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ fecha })
    })
    .then(response => {
        if (!response.ok) throw new Error('Error al filtrar los datos.');
        return response.json();
    })
    .then(data => {
        const tableBody = document.querySelector('#procesoTable tbody');
        tableBody.innerHTML = ''; // Limpiar la tabla

        // Filtrar los datos para excluir los completados
        const datosFiltrados = data.filter(item => item.estatus !== 'Completado');

        // Agrega las filas a la tabla
        datosFiltrados.forEach(item => {
            // Determina las clases e íconos del estatus
            let badgeClass = '';
            let badgeIcon = '';
            switch (item.estatus) {
                case 'En proceso':
                    badgeClass = 'badge badge-phoenix fs--2 badge-phoenix-warning';
                    badgeIcon = 'ms-1 fas fa-spinner';
                    break;
                case 'Sin cortes':
                    badgeClass = 'badge badge-phoenix fs--2 badge-phoenix-secondary';
                    badgeIcon = 'ms-1 fas fa-times';
                    break;
                default:
                    badgeClass = 'badge-danger';
                    badgeIcon = 'fas fa-times';
            }
            
            // Crea una fila de la tabla
            var row = `
                <tr>
                    <td>${item.OrdenFabricacion}</td>
                    <td>${item.Articulo}</td>
                    <td>${item.Descripcion}</td>
                    <td>${item.CantidadTotal}</td>
                    <td>${item.FechaEntregaSAP}</td>
                    <td>${item.FechaEntrega}</td>
                    <td><span class="badge ${badgeClass} d-block mt-2" style="font-size: 12px;">
                        <span class="fw-bold">${item.estatus}</span>
                        <span class="ms-1 ${badgeIcon}"></span>
                    </span></td>
                    <td><a href="#" class="btn btn-outline-warning btn-xs ver-detalles d-flex align-items-center justify-content-center" 
                        style="padding: 2px 6px; font-size: 12px; border-radius: 4px;" data-id="${item.id}">
                         Detalles
                    </a></td>
                </tr>
            `;
            $('#procesoTable tbody').append(row);
        });
    })
    .catch(error => {
        console.error('Error:', error.message);
        alert('Error al procesar la solicitud: ' + error.message);
    });
});


document.getElementById('btnBuscarFechaUnica').addEventListener('click', function (e) {
    e.preventDefault();

    const fecha = document.getElementById('inputFechaUnica').value;

    if (!fecha) {
        alert('Por favor, selecciona una fecha.');
        return;
    }

    // Realiza la solicitud AJAX
    fetch('{{ route("Fitrar.FechaS") }}', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ fecha })
    })
    .then(response => {
        if (!response.ok) throw new Error('Error al filtrar los datos.');
        return response.json();
    })
    .then(data => {
        const tableBody = document.querySelector('#completadoTable tbody');
        tableBody.innerHTML = ''; // Limpiar la tabla

        // Filtrar los datos para incluir solo los completados
        const datosFiltrados = data.filter(item => item.estatus === 'Completado');

        // Agrega las filas a la tabla
        datosFiltrados.forEach(item => {
            // Determina las clases e íconos del estatus
            let badgeClass = 'badge badge-phoenix fs--2 badge-phoenix-success';
            let badgeIcon = 'ms-1 fas fa-check';
            
            // Crea una fila de la tabla
            var row = `
                <tr>
                    <td>${item.OrdenFabricacion}</td>
                    <td>${item.Articulo}</td>
                    <td>${item.Descripcion}</td>
                    <td>${item.CantidadTotal}</td>
                    <td>${item.FechaEntregaSAP}</td>
                    <td>${item.FechaEntrega}</td>
                    <td><span class="badge ${badgeClass} d-block mt-2" style="font-size: 12px;">
                        <span class="fw-bold">${item.estatus}</span>
                        <span class="ms-1 ${badgeIcon}"></span>
                    </span></td>
                    <td><a href="#" class="btn btn-outline-warning btn-xs ver-detalles d-flex align-items-center justify-content-center" 
                        style="padding: 2px 6px; font-size: 12px; border-radius: 4px;" data-id="${item.id}">
                         Detalles
                    </a></td>
                </tr>
            `;
            $('#completadoTable tbody').append(row);
        });
    })
    .catch(error => {
        console.error('Error:', error.message);
        alert('Error al procesar la solicitud: ' + error.message);
    });
});







///////////////////////////////////////////////////////////
</script>
<script>
    $(document).on('click', '#proceso-tab', function () {
    // Realiza la solicitud AJAX
    $.ajax({
        url: "{{ route('ordenes.filtradas') }}", // Ruta Laravel
        method: "GET", // Método HTTP
        data: {
            estatus: ['En proceso', 'Sin cortes'], // Parámetros enviados
            _token: "{{ csrf_token() }}" // Token CSRF
        },
        success: function (data) {
            console.log(data);  // Inspecciona lo que se recibe desde el servidor

            const tableBody = $('#procesoTable tbody');
            tableBody.empty(); // Limpia la tabla

            // Verificar si 'data' es un arreglo
            if (Array.isArray(data)) {
                // Llena la tabla con los datos filtrados
                data.forEach(orden => {
                    tableBody.append(`
                        <tr>
                            <td class="align-middle ps-3 orden">${orden.OrdenFabricacion}</td>
                            <td class="align-middle articulo">${orden.Articulo}</td>
                            <td class="align-middle descripcion">${orden.Descripcion}</td>
                            <td class="align-middle cantidad">${orden.CantidadTotal}</td>
                            <td class="align-middle fechaSAP">${orden.FechaEntregaSAP}</td>
                            <td class="align-middle fechaEstimada">${orden.FechaEntrega}</td>
                            <td class="align-middle estatus">
                                <span class="${getBadgeClass(orden.estatus)}">
                                    ${orden.estatus}
                                    <i class="${getIconClass(orden.estatus)}"></i>
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" data-id="${orden.id}">
                                    <i class="bi bi-eye"></i> Detalles
                                </a>
                            </td>
                        </tr>
                    `);
                });
            } else {
                console.error('La respuesta no es un arreglo:', data);
            }
        },

    });
});

// Funciones auxiliares para asignar clases
function getBadgeClass(estado) {
    switch (estado) {
        case 'Completado': return 'badge badge-phoenix fs--2 badge-phoenix-success';
        case 'En proceso': return 'badge badge-phoenix fs--2 badge-phoenix-warning';
        case 'Sin cortes': return 'badge badge-phoenix fs--2 badge-phoenix-secondary';
        default: return 'badge badge-phoenix fs--2 badge-phoenix-danger';
    }
}

function getIconClass(estado) {
    switch (estado) {
        case 'Completado': return 'ms-1 fas fa-check';
        case 'En proceso': return 'ms-1 fas fa-spinner';
        case 'Sin cortes': return 'ms-1 fas fa-times';
        default: return 'ms-1 fas fa-exclamation-triangle';
    }
}
$.ajax({
    url: "{{ route('ordenes.completadas') }}",
    method: "GET",
    data: {
        estatus: ['Completado'],
        _token: "{{ csrf_token() }}"
    },
    success: function (data) {
        console.log(data); // Inspecciona lo que se recibe desde el servidor
        const tableBody = $('#completadoTable tbody');
        tableBody.empty(); // Limpia la tabla

        if (Array.isArray(data)) {
            data.forEach(orden => {
                tableBody.append(`
                    <tr>
                        <td class="align-middle ps-3 orden">${orden.OrdenFabricacion}</td>
                        <td class="align-middle articulo">${orden.Articulo}</td>
                        <td class="align-middle descripcion">${orden.Descripcion}</td>
                        <td class="align-middle cantidad">${orden.CantidadTotal}</td>
                        <td class="align-middle fechaSAP">${orden.FechaEntregaSAP}</td>
                        <td class="align-middle fechaEstimada">${orden.FechaEntrega}</td>
                        <td class="align-middle estatus">
                            <span class="${getBadgeClass(orden.estatus)}">
                                ${orden.estatus}
                                <i class="${getIconClass(orden.estatus)}"></i>
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" data-id="${orden.id}">
                                <i class="bi bi-eye"></i> Detalles
                            </a>
                        </td>
                    </tr>
                `);
            });
        } else {
            console.error('La respuesta no es un arreglo:', data);
        }
    },
    error: function (xhr, status, error) {
        console.error('Error en la solicitud AJAX:', error);
        alert('Ocurrió un error al cargar los datos.');
    }
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    var myTab = new bootstrap.Tab(document.getElementById('proceso-tab'));
    myTab.show();
});

    document.addEventListener('DOMContentLoaded', function() {
        let rows = document.querySelectorAll('#procesoTable tbody tr');

        rows.forEach(function(row) {
            let estatus = row.querySelector('.estatus').textContent.toLowerCase();

            // Ocultar las filas con estatus "Completado" al cargar la página
            if (estatus.includes('completado')) {
                row.style.display = 'none';
            }
        });
    });

    document.querySelector('.search-input').addEventListener('input', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('#procesoTable tbody tr');

        rows.forEach(function(row) {
            let estatus = row.querySelector('.estatus').textContent.toLowerCase();
            let textContent = row.textContent.toLowerCase();

            // Si el estatus es "Completado", no mostrar la fila
            if (estatus.includes('completado')) {
                row.style.display = 'none';
            } else {
                // Mostrar las filas que coinciden con el término de búsqueda
                if (textContent.includes(searchTerm)) {
                    row.style.display = '';  // Mostrar si coincide con la búsqueda
                } else {
                    row.style.display = 'none';  // Ocultar si no coincide
                }
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        let rows = document.querySelectorAll('#completadoTable tbody tr');

        rows.forEach(function(row) {
            let estatus = row.querySelector('.estatus').textContent.toLowerCase();

            // Ocultar las filas con estatus "Completado" al cargar la página
            if (estatus.includes('sin cortes', 'en proceso')) {
                row.style.display = 'none';
            }
        });
    });

    document.querySelector('.search-inputt').addEventListener('input', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('#completadoTable tbody tr');

        rows.forEach(function(row) {
            let estatus = row.querySelector('.estatus').textContent.toLowerCase();
            let textContent = row.textContent.toLowerCase();

            // Si el estatus es "Completado", no mostrar la fila
            if (estatus.includes('sin cortes', 'en proceso')) {
                row.style.display = 'none';
            } else {
                // Mostrar las filas que coinciden con el término de búsqueda
                if (textContent.includes(searchTerm)) {
                    row.style.display = '';  // Mostrar si coincide con la búsqueda
                } else {
                    row.style.display = 'none';  // Ocultar si no coincide
                }
            }
        });
    });
</script>





@endsection