@extends('layouts.menu')

@section('title', 'Migrar Datos Entre Tablas')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Migrar Datos Entre Tablas</h1>

    <div class="row">
        <!-- Columna para las dos tablas -->
        <div class="col-md-6">
            <!-- Tabla 1: Fuente -->
            <div class="table-responsive mb-4">
                <table class="table table-striped table-bordered" id="table-source">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplo de datos de tabla, que deberían venir del backend -->
                        <tr id="row-1" draggable="true" ondragstart="drag(event)">
                            <td>1</td>
                            <td>Cliente A</td>
                            <td>Producto X</td>
                            <td>100</td>
                            <td>2024-11-20</td>
                        </tr>
                        <tr id="row-2" draggable="true" ondragstart="drag(event)">
                            <td>2</td>
                            <td>Cliente B</td>
                            <td>Producto Y</td>
                            <td>200</td>
                            <td>2024-11-19</td>
                        </tr>
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
                <h4 class="text-center">Tabla 2 - Migrados</h4>
                <table class="table table-striped table-bordered" id="table-destination">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
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
