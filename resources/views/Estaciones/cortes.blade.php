@extends('layouts.menu')
@section('title', 'Planeacion')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
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
                                <th>Artículo</th>
                                <th>Descripción</th>
                                <th>Cantidad Total</th>
                                <th>Fecha Entrega SAP</th>
                                <th>Fecha de Entrega</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="ordenFabricacionTabletbody">
                            
                        </tbody>
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
    <div class="modal fade" id="modalDetalleOrden" tabindex="-1" aria-labelledby="modalDetalleOrdenLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
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
                        </form>
                        <div id="cortesGuardados" class="mt-3 text-success fw-bold">
                            <!-- Aquí se mostrará un resumen de los cortes registrados -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" id="confirmar" class="btn btn-success">Confirmar Orden</button>

                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="ordenFabricacionId" value="">

</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>$(document).ready(function() {
    // Inicialización de la tabla
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
            { data: 'Articulo' }, // Artículo
            { data: 'Descripcion' }, // Descripción
            { data: 'CantidadTotal' }, // Cantidad Total
            { data: 'FechaEntregaSAP' }, // Fecha Entrega SAP
            { data: 'FechaEntrega' }, // Fecha de Entrega
            { 
                data: 'id',
                render: function(data, type, row) {
                    return '<button class="btn btn-info btn-sm ver-detalles" onclick="modalfabricacion()" data-id="' + data + '" data-orden-fabricacion="' + row.OrdenFabricacion + '" data-articulo="' + row.Articulo + '" data-descripcion="' + row.Descripcion + '" data-cantidad-total="' + row.CantidadTotal + '" data-fecha-entrega-sap="' + row.FechaEntregaSAP + '" data-fecha-entrega="' + row.FechaEntrega + '">Ver Detalles</button>';
                }
            }
        ]
    });

    // Al hacer clic en "Ver Detalles"
    $('#ordenFabricacionTable').on('click', '.ver-detalles', function() {
        var ordenFabricacionId = $(this).data('id');
        var ordenFabricacion = $(this).data('orden-fabricacion');
        var articulo = $(this).data('articulo');
        var descripcion = $(this).data('descripcion');
        var cantidadTotal = $(this).data('cantidad-total');
        var fechaEntregaSAP = $(this).data('fecha-entrega-sap');
        var fechaEntrega = $(this).data('fecha-entrega');
    
        $('#modalDetalleOrden').modal('show');
        $('#modalBodyContent').html(` 
            <tr><td><strong>Orden de Fabricación:</strong></td><td data-orden-fabricacion="${ordenFabricacion}">${ordenFabricacion}</td></tr>
            <tr><td><strong>Artículo:</strong></td><td>${articulo}</td></tr>
            <tr><td><strong>Descripcion:</strong></td><td>${descripcion}</td></tr>
            <tr><td><strong>Cantidad Total:</strong></td><td>${cantidadTotal}</td></tr>
            <tr><td><strong>Fecha Entrega SAP:</strong></td><td>${fechaEntregaSAP}</td></tr>
            <tr><td><strong>Fecha Entrega:</strong></td><td data-fecha-entrega="${fechaEntrega}">${fechaEntrega}</td></tr>
        `);
        
        // **Actualizar el campo hidden `ordenFabricacionId` al hacer clic**
        $('#ordenFabricacionId').val(ordenFabricacionId);  // Aquí estamos actualizando el campo
    });

    // Confirmar y guardar los cortes
    $('#confirmar').click(function() {
        var numCortes = $('#numCortes').val().trim();
        var ordenFabricacionId = $('#ordenFabricacionId').val();

        if (!numCortes || numCortes <= 0) {
            alert('Por favor, ingrese un número válido de cortes.');
            return;
        }

        if (!ordenFabricacionId) {
            alert('No se ha seleccionado una orden de fabricación.');
            return;
        }

        // Enviar los datos como un array
        var datosPartidas = [{
            cantidad_partida: numCortes,  
            fecha_fabricacion: new Date().toISOString().split('T')[0], 
            orden_fabricacion_id: ordenFabricacionId
        }];

        console.log('Datos a enviar:', datosPartidas);

        $.ajax({
            url: '{{ route("guardar.partida") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                datos_partidas: datosPartidas
            },
            success: function(response) {
                if (response.status === 'success') {
                    alert('Partidas guardadas correctamente.');
                    $('#cortesGuardados').text('Número de cortes registrados: ' + numCortes);
                } else {
                    alert('Errores: ' + response.errores.join(', '));
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Error al guardar las partidas: ' + xhr.responseText);
            }
        });
    });

    // Mostrar la cantidad de cortes al ingresar el valor
    $('#numCortes').on('input', function() {
        var numCortes = $(this).val();
        if (numCortes) {
            $('#cortesGuardados').text('Número de cortes registrados: ' + numCortes);
        } else {
            $('#cortesGuardados').text('');
        }
    });

    // Guardar los cortes cuando se haga clic en "Guardar Cortes"
    $('#guardarCortes').on('click', function() {
        var numCortes = $('#numCortes').val();
        
        if (numCortes && numCortes >= 0) {
            $('#cortesGuardados').text('Cortes guardados: ' + numCortes);
            $('#numCortes').val('');
        } else {
            alert('Por favor, ingrese un número válido de cortes.');
        }
    });

    // Filtrado por fecha
    $('#buscarFecha').click(function(e) {
        e.preventDefault(); 
        var fechaSeleccionada = $('#fecha').val();
        
        if (!fechaSeleccionada) {
            table.rows().show();
            $('#noDataMessageFecha').hide();
            return;
        }

        var fechaSeleccionadaObj = new Date(fechaSeleccionada);
        var fechaSeleccionadaString = fechaSeleccionadaObj.toISOString().split('T')[0];

        table.rows().every(function() {
            var row = this.node();
            var fechaEntrega = $(row).find('td:eq(5)').text().trim();
            var fechaEntregaObj = new Date(fechaEntrega);
            var fechaEntregaString = fechaEntregaObj.toISOString().split('T')[0];

            if (fechaEntregaString === fechaSeleccionadaString) {
                $(row).show();
            } else {
                $(row).hide();
            }
        });

        if ($('#ordenFabricacionTable tbody tr:visible').length === 0) {
            $('#noDataMessageFecha').show();
        } else {
            $('#noDataMessageFecha').hide();
        }
    });

    // Buscar orden de venta
    $('#buscarOV').on('click', function(event) {
        event.preventDefault();
        var query = $('#query').val();

        if (query) {
            $.ajax({
                url: '/buscar-ordenes',
                type: 'GET',
                data: { query: query },
                beforeSend: function() {
                    $('#ordenFabricacionTabletbody').html("<tr><td colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></td></tr>");
                },
                success: function(response) {
                    $('#ordenFabricacionTabletbody').empty();
                    if (response.length > 0) {
                        let seen = new Set();
                        let uniqueResults = response.filter(item => {
                            const isDuplicate = seen.has(item.OrdenFabricacion);
                            seen.add(item.OrdenFabricacion);
                            return !isDuplicate;
                        });

                        uniqueResults.forEach(function(item) {
                            $('#ordenFabricacionTabletbody').append(`
                                <tr>
                                    <td>${item.OrdenFabricacion}</td>
                                    <td>${item.Articulo}</td>
                                    <td>${item.Descripcion}</td>
                                    <td>${item.CantidadTotal}</td>
                                    <td>${item.FechaEntregaSAP}</td>
                                    <td>${item.FechaEntrega}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm ver-detalles"
                                                data-orden-fabricacion="${item.OrdenFabricacion}"
                                                data-articulo="${item.Articulo}"
                                                data-descripcion="${item.Descripcion}"
                                                data-cantidad-total="${item.CantidadTotal}"
                                                data-fecha-entrega-sap="${item.FechaEntregaSAP}"
                                                data-fecha-entrega="${item.FechaEntrega}">
                                            Ver Detalles
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    } else {
                        $('#ordenFabricacionTable').append(`
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
        } else {
            alert('Por favor, ingresa un valor para buscar.');
        }
    });
});

</script>

@endsection