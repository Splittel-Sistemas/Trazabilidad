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
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>Filtrar Órdenes de Venta</strong>
                </div>
                <div class="card-body">
                    <form id="filtroForm" method="post" class="form-horizontal">
                        @csrf
                        <div class="row">
                            <!-- Filtro por fecha -->
                            <div class="col-md-6 mb-3">
                                <label for="fecha" class="form-label"><strong>Filtro por Fecha</strong></label>
                                <div class="input-group">
                                    <input type="date" name="fecha" id="fecha" class="form-control form-control-sm">
                                    <button id="buscarFecha" class="btn btn-primary btn-sm ms-2">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <!-- Filtro por Orden de Venta -->
                            <div class="col-md-6 mb-3">
                                <label for="query" class="form-label"><strong>Filtro por Orden de Venta</strong></label>
                                <div class="input-group">
                                    <input type="text" placeholder="Ingresa una Orden de Venta" name="query" id="query" class="form-control form-control-sm">
                                    <button id="buscarOV" class="btn btn-primary btn-sm ms-2">
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

    <!-- Tabla de datos sin filtro -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <strong>Órdenes de Fabricación</strong>
                </div>
                <div class="card-body table-responsive">
                    <table id="ordenFabricacionTable" class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Orden de Fabricación</th>
                                <th>Orden de Venta Artículo</th>
                                <th>Artículo</th>
                                <th>Descripción</th>
                                <th>Cantidad Total</th>
                                <th>Fecha Entrega SAP</th>
                                <th>Fecha de Entrega</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tr id="noDataMessageFecha" class="no-data-message" style="display: none;">
                            <td colspan="8" class="text-center">No hay datos disponibles para la fecha seleccionada.</td>
                        </tr>
                        <tr id="noDataMessageOV" class="no-data-message" style="display: none;">
                            <td colspan="8" class="text-center">No hay datos disponibles para la orden de venta seleccionada.</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Detalle -->
<div class="modal fade" id="modalDetalleOrden" tabindex="-1" aria-labelledby="modalDetalleOrdenLabel" aria-hidden="true">
    <div class="modal-dialog modal-md"> <!-- Cambié modal-sm a modal-md -->
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
                <!-- Apartado de cortes del día -->
                <div class="mt-4 p-3 bg-light rounded">
                    <h5 class="text-secondary"><i class="bi bi-scissors"></i> Cortes del Día</h5>
                    <form id="formCortesDia" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="numCortes" class="form-label">Número de Cortes Realizados:</label>
                            <input type="number" class="form-control" id="numCortes" name="numCortes" min="0" placeholder="Ingrese el número de cortes" required>
                            <div class="invalid-feedback">
                                Por favor, ingrese un número válido de cortes.
                            </div>
                        </div>
                        <button type="button" id="guardarCortes" class="btn btn-info w-100">
                            Guardar Cortes
                        </button>
                    </form>
                    <div id="cortesGuardados" class="mt-3 text-success fw-bold">
                        <!-- Aquí se mostrará un resumen de los cortes registrados -->
                    </div>
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
            url: '{{ route("corte.getData") }}',
            type: 'GET',
            dataSrc: 'data',
        },
        columns: [
            { data: 'OrdenFabricacion' }, // Orden de Fabricación
            { data: 'Articulo' }, // Orden de Venta Artículo
            { data: 'Descripcion' }, // Artículo
            { data: 'Descripcion' }, // Descripción
            { data: 'CantidadTotal' }, // Cantidad Total
            { data: 'FechaEntregaSAP' }, // Fecha Entrega SAP
            { data: 'FechaEntrega' }, // Fecha de Entrega
            { 
                data: 'id',
                render: function(data, type, row) {
                    return '<button class="btn btn-info btn-sm ver-detalles" data-id="' + data + '" data-orden-fabricacion="' + row.OrdenFabricacion + '" data-articulo="' + row.Articulo + '" data-descripcion="' + row.Descripcion + '" data-cantidad-total="' + row.CantidadTotal + '" data-fecha-entrega-sap="' + row.FechaEntregaSAP + '" data-fecha-entrega="' + row.FechaEntrega + '">Ver Detalles</button>';
                }
            }
        ]
    });

    // Maneja el clic del botón "Ver Detalles"
    $('#ordenFabricacionTable').on('click', '.ver-detalles', function() {
        // Obtener los datos de la fila seleccionada
        var ordenFabricacion = $(this).data('orden-fabricacion');
        var articulo = $(this).data('articulo');
        var descripcion = $(this).data('descripcion');
        var cantidadTotal = $(this).data('cantidad-total');
        var fechaEntregaSAP = $(this).data('fecha-entrega-sap');
        var fechaEntrega = $(this).data('fecha-entrega');

        // Llenar el modal con los datos
        $('#modalDetalleOrden').modal('show');
        $('#modalBodyContent').html(
            <tr><td><strong>Orden de Fabricación:</strong></td><td>${ordenFabricacion}</td></tr>
            <tr><td><strong>Orden de Venta Artículo:</strong></td><td>${articulo}</td></tr>
            <tr><td><strong>Artículo:</strong></td><td>${descripcion}</td></tr>
            <tr><td><strong>Cantidad Total:</strong></td><td>${cantidadTotal}</td></tr>
            <tr><td><strong>Fecha Entrega SAP:</strong></td><td>${fechaEntregaSAP}</td></tr>
            <tr><td><strong>Fecha Entrega:</strong></td><td>${fechaEntrega}</td></tr>
        );
    });
    $(document).ready(function() {
    // Al hacer clic en el botón "Guardar Cortes"
    $('#guardarCortes').click(function() {
        var numCortes = $('#numCortes').val();  // Número de cortes que el usuario ingresa
        var ordenFabricacionId = $('#ordenFabricacionId').val();  // ID de la orden de fabricación (lo capturas en el modal)
        var fechaFabricacion = $('#fechaFabricacion').val();  // Fecha de fabricación

        // Validación simple
        if (numCortes === '' || ordenFabricacionId === '' || fechaFabricacion === '') {
            alert('Por favor, complete todos los campos.');
            return;
        }

        // Enviar los datos al backend usando AJAX
        $.ajax({
            url: '{{ route('corte.guardarCorte') }}', // Ruta para guardar el corte
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                numCortes: numCortes,
                orden_fabricacion_id: ordenFabricacionId, // Enviar ID de orden de fabricación
                fecha_fabricacion: fechaFabricacion,  // Enviar la fecha de fabricación
            },
            success: function(response) {
                alert(response.success);  // Mostrar mensaje de éxito
            },
            error: function(response) {
                alert('Hubo un error al guardar el corte');
            }
        });
    });

    // Al hacer clic en "Confirmar Orden"
    $('#confirmarOrden').click(function() {
        var ordenFabricacionId = $('#ordenFabricacionId').val();  // O cualquier otro dato necesario para confirmar la orden

        $.ajax({
            url: '{{ route('corte.confirmarOrden') }}', // Ruta para confirmar la orden
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                orden_fabricacion_id: ordenFabricacionId, // ID de orden de fabricación
            },
            success: function(response) {
                alert(response.success);  // Confirmación de la orden
                // Aquí puedes agregar lógica adicional para cerrar el modal o realizar otras acciones
            },
            error: function(response) {
                alert('Hubo un error al confirmar la orden');
            }
        });
    });
    $(document).ready(function() {

    if (!$.fn.dataTable.isDataTable('#ordenFabricacionTable')) {
        var table = $('#ordenFabricacionTable').DataTable({
            // Configuraciones de la tabla si es necesario
        });
    } else {
        // Si ya está inicializada, obtenemos la instancia existente
        var table = $('#ordenFabricacionTable').DataTable();
    }

    // Evento para el filtrado por fecha cuando se hace clic en el botón "Buscar"
    $('#buscarFecha').click(function(e) {
        e.preventDefault(); // Prevenir el envío del formulario

        var fechaSeleccionada = $('#fecha').val(); // Obtenemos la fecha seleccionada

        // Si la fecha está vacía, mostramos todas las filas
        if (!fechaSeleccionada) {
            table.rows().show(); // Mostrar todas las filas
            $('#noDataMessageFecha').hide(); // Ocultamos el mensaje de "No hay datos"
            return;
        }

        // Convertimos la fecha seleccionada a un objeto Date
        var fechaSeleccionadaObj = new Date(fechaSeleccionada);

        // Filtro de las filas de la tabla según la fecha de entrega (FechaEntrega)
        table.rows().every(function() {
            var row = this.node();
            var fechaEntrega = $(row).find('td:eq(2)').text();  // Suponiendo que la FechaEntrega está en la tercera columna (índice 2)

            // Convertimos la fecha de la tabla a un objeto Date
            var fechaEntregaObj = new Date(fechaEntrega);

            // Comparamos si la fecha de entrega de la orden es igual a la fecha seleccionada
            if (fechaEntregaObj.toDateString() === fechaSeleccionadaObj.toDateString()) {
                $(row).show(); // Mostrar fila
            } else {
                $(row).hide(); // Ocultar fila
            }
        });

        // Verificamos si hay filas visibles, si no mostramos un mensaje de "No hay datos"
        if ($('#ordenFabricacionTable tbody tr:visible').length === 0) {
            $('#noDataMessageFecha').show();
        } else {
            $('#noDataMessageFecha').hide();
        }
    });
});

    
});



});
</script>

@endsection