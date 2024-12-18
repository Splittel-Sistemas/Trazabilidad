@extends('layouts.menu')
@section('title', 'Planeación')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Planeación</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Dashboard</a></li>
                            <li><a href="#">Cortes</a></li>
                            <li class="active">Planeación Cortes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="">
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
                                <!-- Los datos se llenarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Detalles de la Orden -->
        <div class="modal fade bd-example-modal-x" id="modalDetalleOrden" tabindex="-1"  role="dialog" aria-labelledby="modalDetalleOrdenLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
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
                                <div class="table-responsive">
                                    <table id="tablaCortes" class="table table-bordered table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Cortes</th>
                                                <th>Fecha De Registro</th>
                                            </tr>
                                        </thead>
                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="button" id="confirmar" class="btn btn-success">Confirmar Orden</button>
                                        </div>
                                        <tbody>
                                            <!-- Cortes de la tabla PartidasOF se reflejan aquí -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="ordenFabricacionId" value="">
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
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
                    return '<button class="btn btn-info btn-sm ver-detalles" data-id="' + data + '">Ver Detalles</button>';
                }
            }
        ]
    });

    // Al hacer clic en "Ver Detalles"
    $('#ordenFabricacionTable').on('click', '.ver-detalles', function() {
        var ordenFabricacionId = $(this).data('id');

        $.ajax({
            url: '{{ route("corte.getDetalleOrden") }}', // Ruta para obtener los detalles
            type: 'GET',
            data: { id: ordenFabricacionId },
            success: function(response) {
                if (response.success) {
                    $('#modalBodyContent').html(`
                        <tr><td><strong>Orden de Fabricación:</strong></td><td>${response.data.OrdenFabricacion}</td></tr>
                        <tr><td><strong>Artículo:</strong></td><td>${response.data.Articulo}</td></tr>
                        <tr><td><strong>Descripción:</strong></td><td>${response.data.Descripcion}</td></tr>
                        <tr><td><strong>Cantidad Total:</strong></td><td>${response.data.CantidadTotal}</td></tr>
                        <tr><td><strong>Fecha Entrega SAP:</strong></td><td>${response.data.FechaEntregaSAP}</td></tr>
                        <tr><td><strong>Fecha Entrega:</strong></td><td>${response.data.FechaEntrega}</td></tr>
                    `);
                    $('#ordenFabricacionId').val(response.data.id);
                    
                    // Obtener y mostrar los cortes registrados
                    $.ajax({
                        url: '{{ route("corte.getCortes") }}',
                        type: 'GET',
                        data: { id: ordenFabricacionId },
                        success: function(cortesResponse) {
                            if (cortesResponse.success) {
                                var cortesHtml = '';
                                cortesResponse.data.forEach(function(corte, index) {
                                    cortesHtml += `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${corte.cantidad_partida}</td>
                                            <td>${corte.fecha_fabricacion}</td>
                                             <td>
                                                <button type="button" class="btn btn-warning btn-finalizar" data-id="${corte.id}">Finalizar</button>
                                            </td>
                                        </tr>
                                    `;
                                });
                                $('#tablaCortes tbody').html(cortesHtml);
                            } else {
                                $('#tablaCortes tbody').html('<tr><td colspan="3" class="text-center">No se encontraron cortes.</td></tr>');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('Error al obtener los cortes.');
                        }
                    });

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

        // Finalizar corte
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
                success: function(response) {
                    if (response.success) {
                        alert('Corte finalizado correctamente.');
                        cargarTablaCortes(); 
                    } else {
                        alert('Error al finalizar el corte: ' + response.message);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Error al finalizar el corte.');
                }
            });
        });

    });

    // Manejo del evento cuando el modal es mostrado
    $('#modalDetalleOrden').on('shown.bs.modal', function() {
        $(this).removeAttr('aria-hidden'); // Asegura que aria-hidden se actualice correctamente
        $(this).find('button').first().focus(); // Enfoca el primer botón en el modal
    });

    // Manejo del evento cuando el modal es ocultado
    $('#modalDetalleOrden').on('hidden.bs.modal', function() {
        $('#triggerButton').focus(); // Devuelve el enfoque al botón que abrió el modal
    });

    // Confirmar y guardar los cortes
    $('#confirmar').click(function() {
        var numCortes = $('#numCortes').val().trim();
        var ordenFabricacionId = $('#ordenFabricacionId').val();

        // Verificar si numCortes es un número válido
        if (!numCortes || numCortes <= 0 || isNaN(numCortes)) {
            alert('Por favor, ingrese un número válido de cortes.');
            return;
        }

        // Verificar si se ha seleccionado una orden de fabricación
        if (!ordenFabricacionId) {
            alert('No se ha seleccionado una orden de fabricación.');
            return;
        }

        // Solicitar la cantidad total de la orden de fabricación
        $.ajax({
            url: '{{ route("ordenFabricacion.getCantidadTotal", ":id") }}'.replace(':id', ordenFabricacionId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var cantidadTotal = response.cantidad_total;

                    // Verificar si los cortes no exceden la cantidad total de la orden
                    if (numCortes > cantidadTotal) {
                        alert('El número de cortes no puede exceder la cantidad total de la orden (' + cantidadTotal + ').');
                        return;
                    }

                    // Si el número de cortes es válido, enviar los datos
                    var datosPartidas = [{
                        cantidad_partida: numCortes,  
                        fecha_fabricacion: new Date().toISOString().split('T')[0], 
                        orden_fabricacion_id: ordenFabricacionId
                    }];

                    console.log('Datos a enviar:', datosPartidas);

                    // Enviar los datos al servidor
                    $.ajax({
                        url: '{{ route("guardar.partida") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            datos_partidas: datosPartidas
                        },
                        success: function(response) {
                            console.log('Respuesta del servidor:', response);  // Agregar esta línea para depuración
                            if (response.status === 'success') {
                                alert('Partidas guardadas correctamente.');
                                // Aquí puedes añadir lógica adicional si quieres actualizar algo en la página
                            } else {
                                alert('Errores: ' + response.errores.join(', '));
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error al guardar partidas:', xhr.responseText);
                            alert('Error al guardar las partidas: ' + xhr.responseText);
                        }
                    });
                } else {
                    alert('Error al obtener la cantidad total de la orden.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener la cantidad total:', xhr.responseText);
                alert('Error al obtener la cantidad total de la orden.');
            }
        });
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
                                                data-id="${item.id}"
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
                        $('.ver-detalles').off('click').on('click', function() {
                        var ordenFabricacionId = $(this).data('id');
                        $('#ordenFabricacionId').val(ordenFabricacionId);
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