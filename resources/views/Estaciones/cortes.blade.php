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
                                    <label for="query" class="form-label"><strong>Filtro por Orden de Venta</strong></label>
                                    <div class="input-group">
                                        <input type="text" placeholder="Ingresa una Orden de Venta" name="query" id="query" class="form-control form-control-sm">
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
                                    <th>Estatus</th>
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
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form>
                        <!-- Aquí se llenarán las partidas dinámicamente -->
                        <div id="partidas-lista"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" id="btn-descargar-pdf" class="btn btn-primary" data-id="">Descargar PDF</button>
                    </div>
                </form>
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
    var table = $('#ordenFabricacionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("corte.getData") }}',
            type: 'GET',
            dataSrc: 'data',
        },
        columns: [
            { data: 'OrdenFabricacion' },
            { data: 'Articulo' },
            { data: 'Descripcion' },
            { data: 'CantidadTotal' },
            { data: 'FechaEntregaSAP' },
            { data: 'FechaEntrega' },
            { 
                data: 'estatus',
                render: function(data, type, row) {
                    var badgeClass;
                    switch (data) {
                        case 'Completado':
                            badgeClass = 'badge-success';
                            break;
                        case 'En proceso':
                            badgeClass = 'badge-warning';
                            break;
                        default:
                            badgeClass = 'badge-danger';
                            break;
                    }
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            { 
                data: 'id',
                render: function(data, type, row) {
                    return '<button class="btn btn-outline-info btn-sm ver-detalles" data-id="' + data + '">Ver Detalles</button>';
                }
            }
        ]
    });





    // Al hacer clic en "Ver Detalles"
    $('#ordenFabricacionTable').on('click', '.ver-detalles', function() {
        
        var ordenFabricacionId = $(this).data('id');
      
        $('#pdfRangos').attr('data-id', ordenFabricacionId);
        $('#btn-pdf-descarga').attr('data-id', ordenFabricacionId)


        $.ajax({
            url: '{{ route("corte.getDetalleOrden") }}', // Ruta para obtener los detalles
            type: 'GET',
            data: { id: ordenFabricacionId },
            success: function(response) {
                if (response.success) {
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
                    <td colspan="5">${response.data.Descripcion}</td> <!-- Abarca más celdas -->
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

            $('#ordenFabricacionId').val(response.data.id);

                    
                    // Obtener y mostrar los cortes registrados
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

    if (!numCortes || numCortes <= 0 || isNaN(numCortes)) {
        alert('Por favor, ingrese un número válido de cortes.');
        return;
    }

    if (!ordenFabricacionId) {
        alert('No se ha seleccionado una orden de fabricación.');
        return;
    }

    // Obtener información de los cortes registrados
    $.ajax({
        url: `/orden-fabricacion/${ordenFabricacionId}/cortes-info`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var cantidadTotal = parseInt(response.cantidad_total);
                var cortesRegistrados = parseInt(response.cortes_registrados);
                var nuevoCorte = parseInt(numCortes);

                if (cortesRegistrados + nuevoCorte > cantidadTotal) {
                    alert('El número total de cortes excede la cantidad total de la orden.');
                    return;
                }

                // Guardar los nuevos cortes
                var datosPartidas = [{
                    cantidad_partida: nuevoCorte,
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



    // Función para recargar la tabla de cortes
    function cargarTablaCortes() {
        var ordenFabricacionId = $('#ordenFabricacionId').val();

        if (ordenFabricacionId) {
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
        }
    }

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
                                        <button class="btn btn-outline-info btn-sm ver-detalles"
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
    
})
$(document).ready(function () {
    // Evento para mostrar la información de la orden cuando se hace clic en el botón
    $(document).on('click', '.btn-generar-etiquetas', function () {
        var corteId = $(this).data('id'); 

        $.ajax({
            url: '{{ route("mostrar.etiqueta") }}',
            data: { id: corteId },
            type: 'GET',
            success: function (response) {
                if (response.error) {
                    alert(response.error);
                } else {
                    
                    $('#partidas-lista').html(''); 

                    
                    var partidasHtml = '';
                    response.partidas.forEach(function (partida) {
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
            error: function (xhr, status, error) {
                console.log('Error:', error);
                console.log('Detalles:', xhr.responseText);
                alert('Error al cargar los datos.');
            }
        });
    });
    // Evento para descargar el PDF cuando se hace clic en el botón de descargar
    $(document).on('click', '#btn-descargar-pdf', function () {
        var corteId = $(this).data('id');
        if (!corteId) {
            alert('No se encontró el ID');
            return;
        }
        // Abre la URL para descargar el PDF
        window.open('/generar-pdf?id=' + corteId, '_blank');
    });
});
$(document).ready(function() {
    $('#pdfRangos').on('click', function() {
        var ordenFabricacionId = $(this).attr('data-id');  
        $('#orden_fabricacion_id').val(ordenFabricacionId);  
        $('#myModalRangos').modal('show');  
    });
});


</script>

@endsection