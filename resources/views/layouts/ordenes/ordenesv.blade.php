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
                            <button type="submit" class="btn btn-primary" id="searchBtn">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                    
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

        </div>
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
                    <tr class="table-light" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#details{{ $loop->index }}" aria-expanded="false" aria-controls="details{{ $loop->index }}">
                        <td class="text-center fw-bold align-middle" onclick="loadContent('details{{ $loop->index }}', {{ $orden['OV'] }})">
                            {{ $orden['OV'] }}
                        </td>
                    </tr>
                    <!-- Detalles colapsables -->
                    <tr id="details{{ $loop->index }}" class="collapse">
                        <td id="details{{ $loop->index."1" }}">

                        </td>
                        <!--<td colspan="1" class="bg-light">
                            <div class="p-3 border rounded shadow-sm">
                                <h5 class="text-primary mb-3">Detalles de la Orden</h5>
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Cliente:</strong> {{ isset($orden['Cliente']) ? $orden['Cliente'] : 'No disponible' }}</li>
                                    <li><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($orden['Fecha'])->format('d-m-Y') }}</li>
                                    <li><strong>Estado:</strong> {{ $orden['Estado'] == 'O' ? 'Abierta' : 'Cerrada' }}</li>
                                    <li><strong>Total:</strong> ${{ number_format($orden['Total'], 2) }}</li>
                                </ul>
                            </div>
                        </td>-->
                    </tr>
                @endforeach
                
                </tbody>
            </table>
        </div>
        
    </div>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- datatables -->
<link href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {  
        $('.datatable').DataTable({
            paging: true,
            searching: false,
            info: false,
            lengthChange: false,
            pageLength: 5,
            language: {
                paginate: {
                    previous: 'Anterior',
                    next: 'Siguiente'
                },
                emptyTable: 'No hay datos disponibles en la tabla',
            }
        });

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


    })
    //Funcion para cargar las partidas por OV
    function loadContent(idcontenedor, docNum) { 
            $.ajax({
                url: "{{ route('datospartida') }}",  
                method: "POST",
                data: {docNum: docNum,_token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                        $('#' + idcontenedor+"1").html("<p align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></p>");
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#' + idcontenedor+"1").html(response.message);
                    } else {
                        $('#' + idcontenedor+"1").html('<p>Error al cargar el contenido.</p>');  
                    }
                },
                error: function(xhr, status, error) {
                    $('#' + idcontenedor).html('<p>Error al cargar el contenido.</p>');  
                }
            });
        }
</script>

</script>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

@endsection
@endsection
