@extends('layouts.menu')

@section('title', 'Ordenes De Venta')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Ordenes De Venta</h1>

    <div class="row">
        <!-- Columna para las dos tablas -->
        <div class="col-md-6">
            <!-- Tabla 1: Fuente -->
            <div class="table-responsive mb-4">
                <h4 class="text-center">Tabla de Usuarios</h4>
                <table class="table table-striped table-bordered" id="table-source">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Creado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr id="row-{{ $user->id }}" draggable="true" ondragstart="drag(event)">
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : 'No disponible' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Columna para el Dropzone y la segunda tabla -->
        <div class="col-md-6">
            <!-- Dropzone para mover elementos a la Tabla 2 -->
            <div id="dropzone" class="dropzone-area" style="border: 2px dashed #007bff; padding: 20px; text-align: center; min-height: 150px;">
                <h3>Arrastra aquí</h3>
            </div>

            <!-- Tabla 2: Destino -->
            <div class="table-responsive mt-4">
                <h4 class="text-center">Usuarios Migrados</h4>
                <table class="table table-striped table-bordered" id="table-destination">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Creado</th>
                            <th>Acción</th>
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
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/ordenes.js') }}"></script>
@endsection
