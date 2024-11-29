@extends('layouts.menu')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container mt-4">
    <h1 class="text-primary mb-4 text-center">Gestión de Órdenes de Venta</h1>

    <!-- Buscador -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body bg-light">
                    <form id="searchForm" action="{{ route('orders') }}" method="GET" class="d-flex align-items-center">
                        <div class="input-group">
                            <input type="text" name="query" id="orderSearch" class="form-control" placeholder="Buscar órdenes..." required value="{{ request('query') }}">
                            <input type="date" name="date" id="datePicker" class="form-control form-control-sm text-center w-auto mx-3 border-info" value="{{ request('date', $fechaHoy) }}">
                            <button type="submit" class="btn btn-primary shadow">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Navegación de Fechas -->
            <div class="d-flex justify-content-center align-items-center mb-4">
                <a href="#" id="prevDayBtn" class="btn btn-outline-secondary shadow-sm me-3">
                    <i class="bi bi-chevron-left"></i> Día Anterior
                </a>
                <a href="#" id="todayBtn" class="btn btn-outline-primary shadow-sm ms-3">
                    Día de Hoy <i class="bi bi-house"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Contenedor de las tablas -->
    <div class="row mb-5">
        <!-- Columna 1: Tabla de Órdenes de Venta -->
        <div class="col-md-6 mb-4">
            <h4 class="text-center text-secondary">Órdenes de Venta</h4>
            <div class="table-responsive shadow-sm border rounded">
                <table class="table table-hover">
                    <thead class="table-primary text-center">
                        <tr>
                            <th class="fw-bold">Órdenes de Venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ordenesVenta as $orden)
                        <tr class="table-light" id="details{{ $loop->index }}cerrar" style="cursor: pointer;" draggable="true" ondragstart="drag(event)" data-bs-toggle="collapse" data-bs-target="#details{{ $loop->index }}" aria-expanded="false" aria-controls="details{{ $loop->index }}">
                            <td onclick="loadContent('details{{ $loop->index }}', {{ $orden['OV'] }})">
                                {{ $orden['OV'] }}
                            </td>
                        </tr>
                        <tr id="details{{ $loop->index }}" class="collapse">
                            <td id="details{{ $loop->index . 'llenar' }}" class="bg-white p-3 shadow-sm">
                                <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Columna 2: Dropzone y Tabla de Migrados -->
        <div class="col-md-6">
            <!-- Área de Dropzone -->
            <!-- Dropzone Mejorado -->
            <div id="dropzone" 
                class="dropzone-area bg-light border-primary rounded d-flex flex-column align-items-center justify-content-center shadow-sm mb-4"
                ondragover="allowDrop(event)" 
                ondrop="drop(event)"
                style="min-height: 120px; transition: border-color 0.3s, background-color 0.3s;">
                <!-- Icono y Título -->
                <i class="bi bi-cloud-upload-fill text-primary mb-2" style="font-size: 2.5rem;"></i>
                <h5 class="text-primary">Arrastra los datos aquí</h5>
                <p class="text-muted">Suelta los artículos para migrar</p>
            </div>
            <!-- Tabla de Migrados -->
            <div class="table-responsive shadow-sm border rounded">
                <h4 class="text-center text-secondary">Tabla Migrados</h4>
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr>
                            <th>Artículo</th>
                            <th>Descripción</th>
                            <th>Cantidad OF</th>
                            <th>Fecha Entrega OF</th>
                            <th>Orden De F</th>
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

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/ordenesv.js') }}"></script>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
 //
 $(document).ready(function () {
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
let currentDate = moment();
const datePicker = $('#datePicker');
datePicker.val(currentDate.format('YYYY-MM-DD'));

function filterOrdersByDate(date) {
    let foundAnyOrder = false;

    $('.order-row').each(function () {
        const rowDate = $(this).data('date');
        if (rowDate === date) {
            $(this).show();
            foundAnyOrder = true;
        } else {
            $(this).hide();
        }
    });

    $('#noOrdersRow').toggleClass('d-none', foundAnyOrder);
}

datePicker.on('change', function () {
    filterOrdersByDate($(this).val());
});

$('#prevDayBtn').on('click', function (e) {
    e.preventDefault();
    currentDate.subtract(1, 'days');
    datePicker.val(currentDate.format('YYYY-MM-DD'));
    filterOrdersByDate(currentDate.format('YYYY-MM-DD'));
});

$('#todayBtn').on('click', function (e) {
    e.preventDefault();
    currentDate = moment();
    datePicker.val(currentDate.format('YYYY-MM-DD'));
    filterOrdersByDate(currentDate.format('YYYY-MM-DD'));
});

$('#searchForm').on('submit', function (e) {
    e.preventDefault();
    const docNum = $('#ordenSearch').val();
    loadContent(docNum);
});
});
function loadContent(idcontenedor, docNum) {
let elemento = document.getElementById(idcontenedor + "cerrar");
if (!elemento.classList.contains('collapsed')) {
    $.ajax({
        url: "{{ route('datospartida') }}",
        method: "POST",
        data: {
            docNum: docNum,
            _token: '{{ csrf_token() }}'
        },
        beforeSend: function () {
            $('#' + idcontenedor + "llenar").html("<p align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>");
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
function drag(event) {
event.dataTransfer.setData("text", event.target.id);
}
}
</script>
@endsection
