@extends('layouts.menu')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container mt-4">
    <h1 class="text-primary mb-4 text-center">Gestión de Órdenes de Venta</h1>

    <!-- Buscador -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="searchForm" action="{{ route('orders') }}" method="GET" class="d-flex align-items-center justify-content-between">
                        <div class="input-group w-50">
                            <input type="text" name="query" id="orders" class="form-control" placeholder="Buscar órdenes..." required value="{{ request('query') }}">
                            <input type="date" name="date" id="orders" class="form-control form-control-sm text-center w-auto mx-3 shadow-sm border-primary" value="{{ request('date', $fechaHoy) }}">
                        </div>
                        <button type="submit" class="btn btn-primary" id="datospartida">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación de Fechas -->
    <div class="d-flex justify-content-center align-items-center mb-4">
        <a href="#" id="prevDayBtn" class="btn btn-outline-secondary me-3">
            <i class="bi bi-chevron-left"></i> Día Anterior
        </a>

        <a href="#" id="todayBtn" class="btn btn-outline-primary ms-3">
            Día de Hoy <i class="bi bi-house"></i>
        </a>
    </div>

    <!-- Tabla de Órdenes de Venta -->
    <div class="row justify-content-center py-4">
        <div class="col-12">
            <table class="table table-hover table-bordered shadow-sm w-75 mx-auto" id="ordersTable">
                <thead class="table-primary text-center">
                    <tr>
                        <th class="fw-bold">Órdenes de Venta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ordenesVenta as $orden)
                    <tr class="table-light" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#details{{ $loop->index }}" aria-expanded="false" aria-controls="details{{ $loop->index }}">
                        <td class="text-center fw-bold align-middle" onclick="loadContent('details{{ $loop->index }}', {{ $orden['OV'] }})">
                            {{ $orden['OV'] }}
                        </td>
                    </tr>
                    <tr id="details{{ $loop->index }}" class="collapse">
                        <td colspan="1" class="bg-light">
                            <div class="p-3 border rounded shadow-sm">
                                <h5 class="text-primary mb-3">Detalles de la Orden</h5>
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Cliente:</strong> {{ isset($orden['Cliente']) ? $orden['Cliente'] : 'No disponible' }}</li>
                                    <li><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($orden['Fecha'])->format('d-m-Y') }}</li>
                                    <li><strong>Estado:</strong> {{ $orden['Estado'] == 'O' ? 'Abierta' : 'Cerrada' }}</li>
                                    <li><strong>Total:</strong> ${{ number_format($orden['Total'], 2) }}</li>
                                </ul>
                                <div id="details{{ $loop->index }}-partidas"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables CSS y JS -->
<link href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<script>
   $(document).ready(function () {
    // Inicializar Moment.js con la fecha actual
    let currentDate = moment();

    // Selector de fecha
    const datePicker = $('#datePicker');

    // Actualizar la fecha en el campo datePicker
    datePicker.val(currentDate.format('YYYY-MM-DD'));

    // Evento de cambio de fecha
    datePicker.on('change', function () {
        const selectedDate = $(this).val();
        filterOrdersByDate(selectedDate);
    });

    // Botón "Día de Ayer"
    $('#prevDayBtn').on('click', function (e) {
        e.preventDefault();
        currentDate = currentDate.subtract(1, 'days');
        const prevDay = currentDate.format('YYYY-MM-DD');
        datePicker.val(prevDay);
        filterOrdersByDate(prevDay);
    });

    // Botón "Día de Hoy"
    $('#todayBtn').on('click', function (e) {
        e.preventDefault();
        currentDate = moment(); // Reinicia a la fecha actual
        const today = currentDate.format('YYYY-MM-DD');
        datePicker.val(today);
        filterOrdersByDate(today);
    });

    // Función para filtrar por fecha
    function filterOrdersByDate(date) {
        const rows = $('#ordersTable tbody tr'); // Seleccionar las filas de la tabla
        rows.each(function () {
            const rowDate = $(this).data('date');
            if (rowDate === date) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});



    function loadContent(idcontenedor, docNum) {
    $.ajax({
        url: "{{ route('datospartida') }}",  // Ruta del controller
        method: "POST",
        data: {
            docNum: docNum,
            _token: '{{ csrf_token() }}'  // Pasar el token CSRF
        },
        success: function(response) {
            console.log(response);  // Log para ver la respuesta completa

            if (response.status === 'success') {
                let html = '';

                // Verificar si response.html es un array y tiene datos
                if (Array.isArray(response.html) && response.html.length > 0) {
                    // Crear un contenedor para las partidas
                    response.html.forEach(partida => {
                        html += `
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">No. Parte: ${partida['NoParte'] ?? 'No disponible'}</h5>
                                    <p><strong>Descripción:</strong> ${partida['Descripcion'] ?? 'No disponible'}</p>
                                    <p><strong>Clasificación Ticket:</strong> ${partida['ClasificacionTicket'] ?? 'No disponible'}</p>
                                    <p><strong>Fecha:</strong> ${moment(partida['Fecha']).format('DD-MM-YYYY')}</p>
                                    <p><strong>Cliente:</strong> ${partida['Cliente'] ?? 'No disponible'}</p>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += '<div class="alert alert-warning">No se encontraron partidas para esta orden.</div>';
                }

                $("#" + idcontenedor).html(html);
            } else {
                $("#" + idcontenedor).html('<p class="text-danger">Error: ' + response.message + '</p>');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', xhr.responseText);
            $("#" + idcontenedor).html('<p class="text-danger">Error al cargar los detalles.</p>');
        }
    });
}
</script>

@endsection
