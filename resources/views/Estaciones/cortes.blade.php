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
                            <div class="col-md-6 mb-3">
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
                            <div class="col-md-6 mb-3">
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
                                <th></th>
                                <th>OrdenVenta_id</th>
                                <th>OrdenFabricacion</th>
                                <th>OrdenVentaArticulo</th>
                                <th>Articulo</th> 
                                <th>Descripcion</th>
                                <th>CantidadTotal</th>
                                <th>FechaEntregaSAP</th>
                                <th>FechaEntrega</th> 
                                <th>created_at</th> 
                                <th>updated_at</th> 
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
                <!-- Apartado de cortes del día -->
                <div class="mt-4 p-3 bg-light rounded">
                    <h5 class="text-secondary"><i class="bi bi-scissors"></i> Cortes del Día</h5>
                    <form id="formCortesDia" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="numCortes" class="form-label">Número de Cortes Realizados:</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="numCortes" 
                                name="numCortes" 
                                min="0" 
                                placeholder="Ingrese el número de cortes"
                                required>
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

@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
  $(document).ready(function() {
    alert();
    var table = $('#ordenFabricacionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("corte.getData") }}',
            type: 'GET',
            dataSrc: 'data',  // Asegúrate de que el nombre del campo es correcto
        },
        columns: [
            { data: 'id' },
            { data: 'OrdenVenta_id' },  // Nombre del campo 'OrdenVenta_id'
            { data: 'OrdenFabricacion' },  // Nombre del campo 'OrdenFabricacion'
            { data: 'Articulo' },
            { data: 'Descripcion' },
            { data: 'CantidadTotal' },
            { data: 'FechaEntregaSAP' },
            { data: 'FechaEntrega' },
            { data: 'created_at' },
            { data: 'updated_at' },
            { 
                data: 'id',
                render: function(data) {

                    return '<button class="btn btn-info btn-sm ver-detalles" data-id="' + data + '">Ver Detalles</button>';
                }
            }
        ]
    });
    alert('hh');
});

    // Filtro por Orden de Venta
    $('#buscarOV').on('click', function (e) {
        e.preventDefault();

        var query = $('#query').val().trim(); // Obtener el valor del filtro

        if (!query) {
            alert("Por favor, ingresa una Orden de Venta.");
            return;
        }

        // Realizar la petición AJAX
        $.ajax({
            url: "{{ route('corte.filtroData') }}", // Ruta para obtener los datos filtrados por orden de venta
            method: "GET",
            data: {
                query: query, // Parámetro para enviar al backend
            },
            success: function (response) {
                // Limpiar la tabla
                table.clear();

                if (response.data && response.data.length > 0) {
                    // Insertar los datos nuevos en la tabla
                    renderTableData(response.data);
                    $('#noDataMessageOV').hide(); // Ocultar mensaje de no resultados
                } else {
                    $('#noDataMessageOV').show(); // Mostrar mensaje "No se encontraron resultados"
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Ocurrió un error. Por favor, intenta nuevamente.');
            }
        });
    });

    // Filtro por Fecha
    $('#buscarFecha').on('click', function (e) {
        e.preventDefault();

        var fecha = $('#fecha').val().trim(); // Obtener la fecha seleccionada

        if (!fecha) {
            alert("Por favor, selecciona una fecha.");
            return;
        }

        // Realizar la petición AJAX
        $.ajax({
            url: "{{ route('corte.filtroFechaData') }}", // Ruta para obtener los datos filtrados por fecha
            method: "GET",
            data: {
                fecha: fecha, // Parámetro para enviar al backend
            },
            success: function (response) {
                // Limpiar la tabla
                table.clear();

                if (response.data && response.data.length > 0) {
                    // Insertar los datos nuevos en la tabla
                    renderTableData(response.data);
                    $('#noDataMessageFecha').hide(); // Ocultar mensaje de no resultados
                } else {
                    $('#noDataMessageFecha').show(); // Mostrar mensaje "No se encontraron resultados"
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Ocurrió un error. Por favor, intenta nuevamente.');
            }
        });
    });

    // Función para renderizar los datos en la tabla
    function renderTableData(data) {
        var rows = '';
        data.forEach(function (item) {
            rows += `<tr>
                        <td>${item.orden_fabricacion}</td>
                        <td>${item.fecha_entrega}</td>
                        <td>${item.articulo}</td>
                        <td>${item.OrdenVentaArticulo || 'N/A'}</td>
                        <td><button class="btn btn-info btn-sm ver-detalles" data-id="${item.id}">Ver Detalles</button></td>
                    </tr>`;

        });
        // Agregar las nuevas filas en la tabla
        table.rows.add($(rows)).draw();
    }

    // Evento para guardar cortes del día
   /* $('#guardarCortes').on('click', function () {
        const numCortes = $('#numCortes').val();
        if (numCortes === "" || parseInt(numCortes) < 0) {
            alert('Por favor, ingrese un número válido de cortes.');
            return;
        }
        // Mostrar resumen de cortes guardados
        $('#cortesGuardados').html(
            <p><strong>Cortes registrados hoy:</strong> ${numCortes} cortes</p>
        );
        // Limpiar el campo de entrada
        $('#numCortes').val('');
    });*/

    // Evento para ver detalles de una orden de fabricación
    /*$(document).on('click', '.ver-detalles', function () {
        var id = $(this).data('id');  // Obtener el ID de la orden
        $.ajax({
            url: '/detalles/' + id,  // URL donde se cargan los detalles
            method: 'GET',
            success: function(response) {
                // Si la respuesta es exitosa, insertar los datos en el modal
                $('#modalBodyContent').html(
                    <tr> <td><strong>Orden de Fabricación:</strong> ${response.orden_fabricacion.orden_fabricacion}</td></tr>
                    <tr><td><strong>Fecha de Entrega:</strong> ${response.orden_fabricacion.fecha_entrega}</td></tr>
                    <tr><td><strong>Articulo:</strong> ${response.orden_fabricacion.articulo}</td></tr>
                    <tr><td><strong>Articulo Orden Venta:</strong> ${response.orden_fabricacion.orden_venta_articulo}</td></tr>
                    <tr><td><strong>Estado:</strong> ${response.orden_fabricacion.estado}</td></tr>
                );

                // Mostrar el modal después de cargar los datos
                $('#modalDetalleOrden').modal('show');
            },
            error: function() {
                alert('No se pudieron cargar los detalles.');
            }
        });
    });
    $(document).on('click', '#confirmarOrden', function () {
    // Obtener los valores necesarios
    var ordenFabricacionId = $("#orden_fabricacion_id").val();
    var cantidadCortes = $("#cantidad_cortes").val();
    var cantidad = $("#cantidad").val();

    // Validar los campos
    /*if (!ordenFabricacionId || !cantidadCortes || !cantidad) {
        alert("Por favor, completa todos los campos.");
        return;
    }*/

    // Realizar la petición AJAx
    /*$.ajax({
            url: "{{ route('partida.guardar') }}",  // Ruta para guardar los datos
            method: "POST",
            data: {
                _token: '{{ csrf_token() }}',  // Token CSRF para seguridad
                orden_fabricacion_id: ordenFabricacionId,
                cantidad_cortes: cantidadCortes,
                cantidad: cantidad
            },
            success: function (response) {
                // Limpiar y actualizar la tabla con los nuevos datos
                table.clear();

                if (response.data && response.data.length > 0) {
                    renderTableData(response.data);  // Reutilizar la función para renderizar datos
                    alert("Orden confirmada con éxito.");
                } else {
                    alert("No se encontraron datos para actualizar.");
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Ocurrió un error al guardar la orden. Por favor, intenta nuevamente.');
            }
        });
    });

}); 
*/



</script>

@endsection