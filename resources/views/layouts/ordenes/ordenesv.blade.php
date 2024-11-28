@extends('layouts.menu')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container mt-4">
    <h1 class="text-primary mb-4 text-center">Gestión de Órdenes de Venta</h1>

    <!-- Buscador -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form id="searchForm" action="{{ route('orders') }}" method="GET" class="d-flex align-items-center">
                        <div class="input-group">
                            <input type="text" name="query" id="datos.partida" class="form-control" placeholder="Buscar órdenes..." required value="{{ request('query') }}">
                            <input type="date" name="date" id="datePicker" class="form-control form-control-sm text-center w-auto mx-3 shadow-sm border-primary" value="{{ request('date', $fechaHoy) }}">
                            <button type="submit" class="btn btn-primary btn-custom" id="searchBtn">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Navegación de Fechas -->
            <div class="d-flex justify-content-center align-items-center mb-4">
                <button id="prevDayBtn" class="btn btn-outline-secondary btn-custom me-3">
                    <i class="bi bi-chevron-left"></i> Día Anterior
                </button>
                <button id="todayBtn" class="btn btn-outline-primary btn-custom ms-3">
                    Día de Hoy <i class="bi bi-house"></i>
                </button>
            </div>
        </div>

        <!-- Tabla de Órdenes -->
        <div class="row justify-content-center py-4">
            <div class="col-12">
                <table class="table table-hover table-bordered shadow-sm w-75 mx-auto">
                    <thead class="table-primary text-center">
                        <tr>
                            <td class="fw-bold">Órdenes de Venta</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ordenesVenta as $orden)
                        <!-- Fila principal -->
                        <tr class="table-light" data-bs-toggle="collapse" data-bs-target="#details{{ $loop->index }}" aria-expanded="false" aria-controls="details{{ $loop->index }}">
                            <td onclick="loadContent('details{{ $loop->index }}', {{ $orden['OV'] }})">
                                {{ $orden['OV'] }}
                            </td>
                        </tr>
                        <!-- Detalles colapsables -->
                        <tr id="details{{ $loop->index }}" class="collapse">
                            <td id="details{{ $loop->index }}1"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Estilos -->
<style>
    .btn-custom {
        border-radius: 25px;
        font-weight: bold;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    .btn-outline-primary {
        color: #007bff;
        border-color: #007bff;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table-primary {
        background-color: #e3f2fd;
    }

    .table-bordered {
        border: 2px solid #dee2e6;
    }

    .accordion-indicator {
        cursor: pointer;
    }

    .loading-indicator {
        text-align: center;
    }
</style>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        
        let currentDate = moment();

        
        const datePicker = $('#datePicker');
        datePicker.val(currentDate.format('YYYY-MM-DD'));

        
        $('#prevDayBtn').on('click', function () {
            currentDate.subtract(1, 'days');
            datePicker.val(currentDate.format('YYYY-MM-DD'));
        });

        
        $('#todayBtn').on('click', function () {
            currentDate = moment();
            datePicker.val(currentDate.format('YYYY-MM-DD'));
        });
    });

    
    function loadContent(idcontenedor, docNum) {
    const contentElement = $('#' + idcontenedor + "1");
    if (contentElement.is(':empty')) {
        contentElement.html(
            "<div class='loading-indicator'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' alt='Cargando...' /></div>"
        );

        $.ajax({
            url: "{{ route('datospartida') }}",
            method: "POST",
            data: {
                docNum: docNum,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.status === 'success') {
                    contentElement.html(response.message);
                } else {
                    contentElement.html('<p>Error al cargar el contenido.</p>');
                }
            },
            error: function () {
                contentElement.html('<p>Error al cargar el contenido.</p>');
            }
        });
    }
}



</script>
@endsection
