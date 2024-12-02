@extends('layouts.menu')
@section('title', 'Planeacion')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{asset('css/Planecion.css')}}">
@endsection
@section('content')
<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Planeaci&oacute;n</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Dashboard</a></li>
                            <li><a href="#">Planeaci&oacute;n</a></li>
                            <li class="active">Planeaci&oacute;n Fabricaci&oacute;n</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-4">
    <!--<h1 class="text-primary mb-4 text-center">Gestión de Órdenes de Venta</h1>-->
    <!-- Buscador -->
   <!-- Buscador -->
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <strong>Filtrar Órdenes de Venta</strong>
                    <button id="filtro_ov" type="button" class="btn btn-link float-end collapsed" draggable="true" ondragstart="drag(event)" data-bs-toggle="collapse" data-bs-target="#filtro" aria-expanded="true" aria-controls="filtro">
                        <i class="fa fa-chevron-up"></i>
                    </button>
                </div>
                <div class="card-body card-block collapsed show" id="filtro">
                    <form id="filtroForm" method="post" class="form-horizontal" action="{{ route('filtros') }}">
                        @csrf
                        <div class="row">
                            <!-- Filtro por fecha -->
                            <div class="col-md-6">
                                <label for="" class="form-control-label me-2 col-12">
                                    <strong>Filtro por Fecha</strong>
                                </label>
                                <div class="input-group">
                                    <label for="startDate" class="form-control-label me-2 col-4">Fecha inicio:</label>
                                    <input type="date" name="startDate" id="startDate" class="form-control form-control-sm w-autoborder-primary col-8" value="{{ old('startDate', $fechaAyer) }}">
                                </div>
                                <div class="input-group pt-3">
                                    <label for="endDate" class="form-control-label me-2 col-4">Fecha fin:</label>
                                    <input type="date" name="endDate" id="endDate" class="form-control form-control-sm w-autoborder-primary col-8" value="{{ old('endDate', $fechaHoy) }}">
                                </div>
                                <div class="row form-group pt-3">
                                    <button type="submit" class="btn btn-primary btn-sm float-end">
                                        <i class="fa fa-search"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                            <!-- Filtro por Orden de Venta -->
                            <div class="col-md-6">
                                <label for="" class="form-control-label me-2 col-12">
                                    <strong>Filtro por Orden de Venta</strong>
                                </label>
                                <div class="input-group">
                                    <input type="text" placeholder="Ingresa una Orden de Venta" name="query" id="query" class="form-control form-control-sm w-autoborder-primary col-9">
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
</div>

    <!-- Contenedor de las tablas -->
    <div class="card p-3">
        <div class="row mb-5">
            <!-- Columna 1: Tabla de Órdenes de Venta -->
            <div class="col-md-6 mb-2">
                <div id="container_table_OV" class="table-responsive">
                    <table id="table_OV" class="table table-striped table-bordered" id="table-source">
                        <thead class="table-primary text-center">
                            <tr>
                                <th class="fw-bold">
                                    Órdenes de Venta <br>de 10-11-2024 a 12-11-2024 
                                    <div class="input-group ">
                                        <input type="text" placeholder="Ingresa una Orden de Venta" name="filtro_ov_tabla" oninput="filtro_ov_tabla(this.value);" id="filtro_ov_tabla" class="form-control form-control-sm   w-autoborder-primary col-12">
                                        <div class="input-group-btn">
                                            <button class="btn btn-primary btn-sm">
                                                <i class="fa fa-search"></i> buscar
                                            </button>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="table_OV_body">
                            @foreach ($ordenesVenta as $orden)
                            <tr class="table-light" id="details{{ $loop->index }}cerrar" style="cursor: pointer;" draggable="true" ondragstart="drag(event)" data-bs-toggle="collapse" data-bs-target="#details{{ $loop->index }}" aria-expanded="false" aria-controls="details{{ $loop->index }}">
                                <td onclick="loadContent('details{{ $loop->index }}', {{ $orden['OV'] }})">
                                    {{ $orden['OV']." - ".$orden['Cliente']}}
                                </td>
                            </tr>
                            <tr id="details{{ $loop->index }}" class="collapse">
                                <td class="table-border" id="details{{ $loop->index . 'llenar' }}">
                                    <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <!-- Navegación de Fechas -->
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <button id="prevDayBtn" class="btn btn-link"><i class="fa fa-arrow-left"></i> Anterior</button>
                        <button id="todayBtn" class="btn btn-link">Siguiente <i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
            <!-- Columna 2: Dropzone y Tabla de Migrados -->
            <div class="col-md-6 mb-2">
                <!-- Área de Dropzone -->
                <div id="dropzone" 
                    class="dropzone-area border-dashed border-primary p-4 text-center mb-4"
                    ondragover="allowDrop(event)" 
                    ondrop="drop(event)" 
                    style="border: 2px dashed #007bff; padding: 20px; text-align: center; min-height: 150px;">
                    <h4>Arrastra aquí los datos</h4>
                    <p class="text-muted">Suelta los artículos que deseas migrar aquí</p>
                </div>
                <!-- Tabla de Migrados -->
                <div class="table-responsive">
                    <h4 class="text-center">Tabla Migrados</h4>
                    <table class="table table-striped table-bordered" id="table-destination">
                        <thead>
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
    $('#buscarOV').on('click', function (e) {
        e.preventDefault();

        var query = $('#query').val().trim(); 

        if (!query) {
            alert("Por favor, ingresa una Orden de Venta.");
            return;
        }

        $.ajax({
            url: "{{ route('filtro') }}", 
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            data: {
                query: query, 
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#container_table_OV').html(response.data); 
                } else {
                    alert(response.message); 
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Ocurrió un error. Por favor, intenta nuevamente.');
            }
        });
    });
});

 $(document).ready(function() {
    $('#filtroForm').on('submit', function(e) {
        e.preventDefault(); 

        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        var query = $('#query').val() || ''; 

        if (new Date(startDate) > new Date(endDate)) {
            alert("La fecha de inicio no puede ser posterior a la fecha de fin.");
            return;
        }

        $.ajax({
            url: "{{ route('filtros') }}", 
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                startDate: startDate,
                endDate: endDate,
                query: query
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#container_table_OV').html(response.data); 
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Ocurrió un error. Por favor, intenta nuevamente.');
            }
        });
    });
});

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
document.getElementById('filtro_ov').addEventListener('click', function(event) {
    btn=document.getElementById('filtro_ov');
    if (!btn.classList.contains('collapsed')) {
        btn.innerHTML='<i class="fa fa-chevron-up"></i>';
    }else{
        btn.innerHTML='<i class="fa fa-chevron-down"></i>';
    }
});
function filtro_ov_tabla(ov){
    campo=0;
    let filas = document.querySelectorAll("#table_OV tbody tr");
    filas.forEach(fila => {
    let valorCelda = fila.cells[campo].innerText.trim();
    
    if(fila.classList.contains('table-light')){
        if (valorCelda.includes(ov)) {
            //alert("entre");
        fila.style.display = ""; 
        } else {
            //alert("no");
        fila.style.display = "none"; 
        }
    }
  });
}
</script>
@endsection
