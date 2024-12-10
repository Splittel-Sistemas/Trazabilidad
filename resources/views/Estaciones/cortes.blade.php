@extends('layouts.menu')
@section('title', 'Planeacion')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/Planecion.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection

@section('content')
<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>C&oacute;rtes</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Dashboard</a></li>
                            <li><a href="#">Planeaci&oacute;n</a></li>
                            <li class="active">C&oacute;rtes </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <!-- Buscador -->
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <strong>Filtrar Órdenes de Venta</strong>
                    <button id="filtro_ov" type="button" class="btn btn-link float-end collapsed" data-bs-toggle="collapse" data-bs-target="#filtro" aria-expanded="true" aria-controls="filtro">
                        <i class="fa fa-chevron-up"></i>
                    </button>
                </div>
                <div class="card-body card-block collapse show" id="filtro">
                    <form id="filtroForm" method="post" class="form-horizontal">
                        @csrf
                        <div class="row">
                            <!-- Filtro por fecha -->
                            <div class="col-md-6">
                                <label for="" class="form-control-label me-2 col-12">
                                    <strong>Filtro por Fecha</strong>
                                </label>
                                <div class="input-group">
                                    <label for="startDate" class="form-control-label me-2 col-4">Fecha :</label>
                                    <input type="date" name="startDate" id="startDate" class="form-control form-control-sm w-autoborder-primary col-8" >
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
                                <th>id</th>
                                <th>orden_venta_id</th>
                                <th>numero_fabricacion</th>
                                <th>fecha_fabricacion</th>
                                <th>estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $('#ordenFabricacionTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("corte.getData") }}',
        type: 'GET',
    },
    columns: [
        { data: 'id' },
        { data: 'orden_venta_id' },
        { data: 'numero_fabricacion' },
        { data: 'fecha_fabricacion' },
        { data: 'estado' },
        {
            data: 'orden_venta', // Relación con OrdenVenta
            render: function(data) {
                return data ? data.articulo : 'N/A'; // Muestra el campo 'articulo' de OrdenVenta
            }
        }
    ]
});

</script>
    
@endsection
