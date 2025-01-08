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
                        <h1>Cortes</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Dashboard</a></li>
                            <li><a href="#">Cortes</a></li>
                            <li class="active">Cortes</li>
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
                                        <button id="buscarFecha" class="btn btn-outline-primary btn-sm ms-2">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>

                                <!-- Filtro por Orden de Venta -->
                                <div class="col-md-6 mb-3">
                                    <label for="query" class="form-label"><strong>Filtro por Orden de Fabricacion</strong></label>
                                    <div class="input-group">
                                        <input type="text" placeholder="Ingresa una Orden de Fabricacion" name="query" id="query" class="form-control form-control-sm">
                                        <button id="buscarOV" class="btn btn-outline-primary btn-sm ms-2">
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
                        <table id="ordenFabricacionTable" class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Or.Fabricación</th>
                                    <th>Artículo</th>
                                    <th>Descripción</th>
                                    <th>Cantidad Total</th>
                                    <th>Fecha SAP</th>
                                    <th>Fecha Estimada</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ordenesFabricacion as $orden)
                                    <tr>
                                        <td>{{ $orden->OrdenFabricacion }}</td>
                                        <td>{{ $orden->Articulo }}</td>
                                        <td>{{ $orden->Descripcion }}</td>
                                        <td>{{ $orden->CantidadTotal }}</td>
                                        <td>{{ $orden->FechaEntregaSAP }}</td>
                                        <td>{{ $orden->FechaEntrega }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match ($orden->estatus) {
                                                    'Completado' => 'badge-success',
                                                    'En proceso' => 'badge-warning',
                                                    default => 'badge-danger',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $orden->estatus }}</span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-outline-info btn-sm ver-detalles" data-id="{{ $orden->id }}">
                                                Ver Detalles
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
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
                            <h5 class="text-secondary"><i class="bi bi-info-circle"></i></h5>
                            <table class="table table-striped table-bordered">
                                <tbody id="modalBodyContent">
                                    <!-- Aquí se insertarán los datos dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Apartado de cortes del día -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5 class="text-secondary"><i class="bi bi-scissors"></i> </h5>
                            <form id="formCortesDia" class="needs-validation d-flex align-items-center" novalidate>
                                <div class="mb-3 d-flex align-items-center">
                                    <label for="numCortes" class="form-label ms-2 mb-0">Registrar Cantidad:</label> <!-- Eliminar margen inferior con mb-0 -->
                                    <input type="number" class="form-control form-control-sm ms-2" id="numCortes" name="numCortes" min="0" placeholder="Ingresa el número" required>
                                    <button type="button" id="confirmar" class="btn btn-success btn-sm ms-2">Confirmar Corte</button>
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
                                                <th> <button class="btn btn-info  btn-sm ms-2" id="pdfRangos" data-id="">Generar PDF de Rangos</button></th>
                                                
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
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
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
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">Información de la Orden de Fabricación</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
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
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Selecciona los Rangos para el PDF</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
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

// Inicialización de la tabla de DataTables
$('#ordenFabricacionTable').DataTable({
    "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
    }
});

// Recarga de la tabla cada 30 segundos
setInterval(function() {
    table.ajax.reload(null, false); // false para mantener la paginación actual
}, 30000);

// Evento al hacer clic en "Ver Detalles"
$('#ordenFabricacionTable').on('click', '.ver-detalles', function() {
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
                        <table class="table table-striped table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Orden de Fabricación:</strong></td>
                                    <td>${response.data.OrdenFabricacion}</td>
                                    <td><strong>Artículo:</strong></td>
                                    <td>${response.data.Articulo}</td>
                                    <td><strong>Cantidad Total:</strong></td>
                                    <td>${response.data.CantidadTotal}</td>
                                </tr>
                                <tr>
                                    <td><strong>Descripción:</strong></td>
                                    <td colspan="5">${response.data.Descripcion}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha Entrega SAP:</strong></td>
                                    <td>${response.data.FechaEntregaSAP}</td>
                                    <td><strong>Fecha Entrega:</strong></td>
                                    <td>${response.data.FechaEntrega}</td>
                                    <td></td>
                                    <td></td>
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

// Función para obtener y mostrar los cortes registrados
function obtenerCortes(ordenFabricacionId) {
    $.ajax({
        url: '{{ route("corte.getCortes") }}',
        type: 'GET',
        data: { id: ordenFabricacionId },
        success: function(cortesResponse) {
            if (cortesResponse.success) {
                var cortesHtml = '';

                // Invertir el orden de los cortes antes de iterar sobre ellos
                cortesResponse.data.reverse().forEach(function(corte, index) {
                    cortesHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${corte.cantidad_partida}</td>
                            <td>${corte.fecha_fabricacion}</td>
                            <td>${corte.FechaFinalizacion}</td>
                            <td>
                                <button type="button" class="btn btn-outline-info btn-generar-etiquetas" data-id="${corte.id}">Generar Etiquetas</button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-danger btn-finalizar" data-id="${corte.id}">Finalizar</button>
                            </td>
                        </tr>
                    `;
                });

                // Insertar las filas en la tabla de cortes
                $('#tablaCortes tbody').html(cortesHtml);
            } else {
                // Mensaje cuando no hay cortes
                $('#tablaCortes tbody').html('<tr><td colspan="3" class="text-center">No se encontraron cortes.</td></tr>');
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Error al obtener los cortes.');
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
        success: function(response) {
            if (response.success) {
                // Recargar la tabla de cortes
                obtenerCortes($('#ordenFabricacionId').val());
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

// Abrir modal para rangos PDF
$('#pdfRangos').on('click', function() {
    var ordenFabricacionId = $(this).attr('data-id');
    $('#orden_fabricacion_id').val(ordenFabricacionId);
    $('#myModalRangos').modal('show');
});

// Filtrado por fecha
$('#buscarFecha').click(function(e) {
    e.preventDefault();
    var fechaSeleccionada = $('#fecha').val();

    // Mostrar todas las filas si no se selecciona ninguna fecha
    if (!fechaSeleccionada) {
        $('#ordenFabricacionTable tbody tr').show();
        $('#noDataMessageFecha').hide();
        return;
    }

    // Convertir la fecha seleccionada en un formato comparable
    var fechaSeleccionadaObj = new Date(fechaSeleccionada);
    var fechaSeleccionadaString = fechaSeleccionadaObj.toISOString().split('T')[0];

    // Filtrar las filas de la tabla
    var hayDatos = false;
    $('#ordenFabricacionTable tbody tr').each(function() {
        var fechaEntrega = $(this).find('td:eq(5)').text().trim();
        var fechaEntregaObj = new Date(fechaEntrega);
        var fechaEntregaString = fechaEntregaObj.toISOString().split('T')[0];

        // Comparar fechas y mostrar/ocultar filas
        if (fechaEntregaString === fechaSeleccionadaString) {
            $(this).show();
            hayDatos = true;
        } else {
            $(this).hide();
        }
    });

    // Mostrar mensaje si no hay datos visibles
    if (!hayDatos) {
        $('#noDataMessageFecha').show();
    } else {
        $('#noDataMessageFecha').hide();
    }
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
    // Abre la URL para descargar el PDF
    window.open('/generar-pdf?id=' + corteId, '_blank');
});

// Filtro orden de fabricación
$('#buscarOV').on('click', function(event) {
    event.preventDefault();
    var query = $('#query').val();

    if (query) {
        $.ajax({
            url: '/buscar-ordenes',
            type: 'GET',
            data: { query: query },
            beforeSend: function() {
                var table = $('#ordenFabricacionTable').DataTable();
                table.clear().draw(); // Limpia la tabla antes de la carga

                // Muestra el mensaje de carga
                $('#ordenFabricacionTable tbody').html(`
                    <tr>
                        <td colspan="8" align="center">
                            <img src="{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}" />
                        </td>
                    </tr>
                `);
            },
            success: function(response) {
                var table = $('#ordenFabricacionTable').DataTable();
                table.clear(); // Limpia la tabla para agregar los nuevos datos

                if (response.length > 0) {
                    let seen = new Set();
                    let uniqueResults = response.filter(item => {
                        const isDuplicate = seen.has(item.OrdenFabricacion);
                        seen.add(item.OrdenFabricacion);
                        return !isDuplicate;
                    });

                    // Agrega los resultados únicos a la tabla
                    uniqueResults.forEach(item => {
                        var row = [
                            item.OrdenFabricacion,
                            item.Articulo,
                            item.Descripcion,
                            item.CantidadTotal,
                            item.FechaEntregaSAP,
                            item.FechaEntrega,
                            `<span class="badge ${getBadgeClass(item.estatus)}">${item.estatus}</span>`,
                            `<a href="#" class="btn btn-outline-info btn-sm ver-detalles" data-id="${item.id}">Ver Detalles</a>`
                        ];
                        table.row.add(row).draw();
                    });
                } else {
                    // Si no hay resultados, mostrar mensaje
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
    } else {
        alert('Por favor, ingresa un valor para buscar.');
    }
});

// Función para obtener la clase del badge según el estatus
function getBadgeClass(estatus) {
    switch (estatus) {
        case 'Completado': return 'badge-success';
        case 'En proceso': return 'badge-warning';
        default: return 'badge-danger';
    }
}

$('#confirmar').click(function() {
    var numCortes = $('#numCortes').val().trim();
    var ordenFabricacionId = $('#ordenFabricacionId').val();  // Asegúrate de que este valor sea correcto
    console.log('Orden de Fabricación ID:', ordenFabricacionId);  // Verifica en la consola si tiene el valor correcto

    // Validaciones de los campos
    if (!numCortes || numCortes <= 0 || isNaN(numCortes)) {
        alert('Por favor, ingrese un número válido de cortes.');
        return;
    }

    if (!ordenFabricacionId) {
        alert('No se ha seleccionado una orden de fabricación.');
        return;
    }

    $.ajax({
        url: `/orden-fabricacion/${ordenFabricacionId}/cortes-info`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var cantidadTotal = parseInt(response.cantidad_total);
                var cortesRegistrados = parseInt(response.cortes_registrados);
                var nuevoCorte = parseInt(numCortes);

                // Validar que el número de cortes no exceda la cantidad total
                if (cortesRegistrados + nuevoCorte > cantidadTotal) {
                    alert('El número total de cortes excede la cantidad total de la orden.');
                    return;
                }

                // Preparar los datos para guardar
                var datosPartidas = [{
                    cantidad_partida: nuevoCorte,
                    fecha_fabricacion: new Date().toISOString().split('T')[0],
                    OrdenFabricacion_id: ordenFabricacionId
                }];

                // Guardar las nuevas partidas
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

                            // Actualizar la tabla sin refrescar la página
                            var nuevaFila = `
                                <tr>
                                    <td>${response.partida.id}</td>
                                    <td>${nuevoCorte}</td>
                                    <td>${datosPartidas[0].fecha_fabricacion}</td>
                                </tr>`;
                            $('#tablaPartidas tbody').append(nuevaFila);
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
                alert('Error al obtener la información de la orden de fabricación: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener información de cortes:', xhr.responseText);
            alert('Error al obtener información de cortes: ' + xhr.responseText);
        }
    });
});


});



/*

    success: function(response) {
    console.log('Respuesta:', response); // Esto te ayudará a verificar los datos
    if (response.success) {
        var cantidadTotal = parseInt(response.cantidad_total);
        var cortesRegistrados = parseInt(response.cortes_registrados);
        var nuevoCorte = parseInt(numCortes);

        // Validar que el número de cortes no exceda la cantidad total
        if (cortesRegistrados + nuevoCorte > cantidadTotal) {
            alert('El número total de cortes excede la cantidad total de la orden.');
            return;
        }

        // Preparar los datos para guardar
        var datosPartidas = [{
            cantidad_partida: nuevoCorte,
            fecha_fabricacion: new Date().toISOString().split('T')[0],
            OrdenFabricacion_id: ordenFabricacionId
        }];

        // Guardar las nuevas partidas
        $.ajax({
            url: '{{ route("guardar.partida") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                datos_partidas: datosPartidas
            },
            success: function(response) {
                console.log('Respuesta de guardar partida:', response); // Verifica la respuesta del guardado
                if (response.status === 'success') {
                    alert('Partidas guardadas correctamente.');

                    // Actualizar la tabla sin refrescar la página
                    var nuevaFila = `
                        <tr>
                            <td>${response.partida.id}</td>
                            <td>${nuevoCorte}</td>
                            <td>${datosPartidas[0].fecha_fabricacion}</td>
                        </tr>`;
                    $('#tablaPartidas tbody').append(nuevaFila);
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
        alert('Error al obtener la información de la orden de fabricación: ' + response.message);
    }
}

*/



</script>

@endsection