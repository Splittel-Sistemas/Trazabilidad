@extends('layouts.menu')
@section('title', 'Planeacion')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/Planecion.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

@endsection

@section('content')
<div class="container mt-4">
    <!-- Buscador -->
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <strong>Filtrar Órdenes de Venta</strong>
                </div>
                <div class="card-body card-block collapse show" id="filtro">
                    <form id="filtroForm" method="post" class="form-horizontal">
                        @csrf
                        <div class="row">
                            <!-- Filtro por fecha -->
                            <div class="col-md-6">
                                <label for="" class="form-control-label me-2 col-12">
                                    <strong>Filtro por Fecha</strong>
                                </label>
                                <div class="input-group">
                                    <label for="fecha" class="form-control-label me-2 col-4">Fecha :</label>
                                    <input type="date" name="fecha" id="fecha" class="form-control form-control-sm col-8">
                                    <div class="input-group-btn">
                                        <button id="buscarFecha" class="btn btn-primary btn-sm">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Filtro por Orden de Venta -->
                            <div class="col-md-6">
                                <label for="" class="form-control-label me-2 col-12">
                                    <strong>Filtro por Orden de Venta</strong>
                                </label>
                                <div class="input-group">
                                    <input type="text" placeholder="Ingresa una Orden de Venta" name="query" id="query" class="form-control form-control-sm col-9">
                                    <div class="input-group-btn">
                                        <button id="buscarOV" class="btn btn-primary btn-sm">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de datos sin filtro -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <strong>Órdenes de Fabricación</strong>
                </div>
                <div class="card-body table-responsive">
                    <table id="ordenFabricacionTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>OrdenVenta_id</th>
                                <th>OrdenFabricacion</th>
                                <th>FechaEntrega</th>
                                <th>Articulo</th>
                                <th>OrdenVentaArticulo</th>
                                <th>Acciones</th> 
                            </tr>
                        </thead>
                        <tbody></tbody> 
                        <tr id="noDataMessageFecha" class="no-data-message" style="display: none;">
                            <td colspan="7" class="text-center">No hay datos disponibles para la fecha seleccionada.</td>
                        </tr>
                        <tr id="noDataMessageOV" class="no-data-message" style="display: none;">
                            <td colspan="7" class="text-center">No hay datos disponibles para la orden de venta seleccionada.</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetalleOrden" tabindex="-1" aria-labelledby="modalDetalleOrdenLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetalleOrdenLabel">Detalles de la Orden</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tbody id="modalBodyContent">
                            <!-- Aquí se insertarán los datos -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmarOrden" class="btn btn-success">Confirmar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Regresar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Detalle -->
<div class="modal fade" id="modalDetalleOrden" tabindex="-1" aria-labelledby="modalDetalleOrdenLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="modalDetalleOrdenLabel">Detalles de la Orden</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Sección de detalles -->
                <div class="mb-4">
                    <h5 class="text-secondary"><i class="bi bi-info-circle"></i> Información de la Orden</h5>
                    <table class="table table-striped table-bordered">
                        <tbody id="modalBodyContent">
                            <!-- Aquí se insertarán los datos dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="confirmarOrden" class="btn btn-success">Confirmar Orden</button>
            </div>
        </div>
    </div>
</div>

    
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#ordenFabricacionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("cortes.getData") }}',  // Llamada al controlador para obtener los datos
            type: 'GET',
            dataSrc: 'data',  // Asegúrate de que los datos se devuelvan correctamente en 'data'
        },
        columns: [
            { data: 'orden_fabricacion' },  // Asegúrate de usar los nombres correctos de las columnas
            { data: 'fecha_entrega' },
            { data: 'articulo' },
            { 
                data: 'ordenVenta.orden_venta_articulo',  // Relación con OrdenVenta, usa el nombre correcto
                render: function(data) {
                    return data ? data : 'N/A';  // Si no hay datos, muestra 'N/A'
                }
            },
            {
                data: 'id',  // ID de la orden para obtener los detalles
                render: function(data) {
                    return '<button class="btn btn-info btn-sm ver-detalles" data-id="' + data + '">Ver Detalles</button>';
                }
            }
        ]
    });

    // Evento para abrir el modal con los detalles de la orden
    $(document).on('click', '.ver-detalles', function() {
        var id = $(this).data('id');  // Obtener el ID de la orden seleccionada

        // Realizar la solicitud AJAX para obtener los detalles de la orden
        $.ajax({
            url: '/cortes/' + id + '/detalles',  // URL del controlador que devuelve los detalles
            method: 'GET',
            success: function(response) {
                // Insertar los detalles de la orden en el modal
                $('#modalBodyContent').html(`
                    <tr><td><strong>Orden de Fabricación:</strong> ${response.orden_fabricacion}</td></tr>
                    <tr><td><strong>Fecha de Entrega:</strong> ${response.fecha_entrega}</td></tr>
                    <tr><td><strong>Artículo:</strong> ${response.articulo}</td></tr>
                    <tr><td><strong>Orden de Venta Artículo:</strong> ${response.ordenVenta ? response.ordenVenta.orden_venta_articulo : 'N/A'}</td></tr>
                    <tr><td><strong>Estado:</strong> ${response.estado}</td></tr>
                `);

                // Mostrar el modal
                $('#modalDetalleOrden').modal('show');
            },
            error: function() {
                alert('No se pudieron cargar los detalles de la orden.');
            }
        });
    });
});

</script>
@endsection