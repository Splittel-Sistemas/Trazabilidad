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

<script>
// Función para manejar el inicio del arrastre
function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
}

// Función para permitir el "drop" en la zona de dropzone
function allowDrop(event) {
    event.preventDefault();
}

// Función para manejar el "drop" y mover la fila a la otra tabla
function drop(event) {
    event.preventDefault();
    var data = event.dataTransfer.getData("text");
    var draggedRow = document.getElementById(data);

    // Crear una nueva fila en la tabla 2 (destino)
    var newRow = document.createElement('tr');
    newRow.id = draggedRow.id; // Preservar el id
    newRow.innerHTML = draggedRow.innerHTML + `<td><button class="btn btn-warning" onclick="regresarRow('${draggedRow.id}')">Regresar</button></td>`;
    
    // Añadir la nueva fila a la Tabla 2
    document.getElementById('table-2-content').appendChild(newRow);

    // Ocultar la fila original en la Tabla 1
    draggedRow.style.display = "none";
}

// Función para regresar la fila de la Tabla 2 a la Tabla 1
function regresarRow(rowId) {
    // Encontrar la fila en la tabla de destino (Tabla 2)
    var table2Row = document.querySelector(`#table-destination #${rowId}`);
    if (!table2Row) return; // Si no se encuentra, salir

    // Recuperar la tabla de origen (Tabla 1)
    var table1 = document.getElementById('table-source').getElementsByTagName('tbody')[0];

    // Encontrar la fila oculta en Tabla 1
    var table1Row = document.getElementById(rowId);
    if (table1Row) {
        // Restaurar la fila a Tabla 1 y hacerla visible nuevamente
        table1Row.style.display = '';
    } else {
        // Si la fila no está en Tabla 1, recrearla desde la Tabla 2
        var newRow = document.createElement('tr');
        newRow.id = rowId; // Restaurar el id
        newRow.innerHTML = table2Row.innerHTML.replace(/<td>.*?Regresar.*?<\/td>/, ''); // Quitar la columna del botón
        newRow.setAttribute('draggable', 'true'); // Restaurar el atributo draggable
        newRow.setAttribute('ondragstart', 'drag(event)'); // Restaurar el evento de arrastre
        table1.appendChild(newRow);
    }

    // Eliminar la fila de Tabla 2
    table2Row.remove();
}

// Activar los eventos de "allowDrop" y "drop"
document.getElementById('dropzone').addEventListener('dragover', allowDrop);
document.getElementById('dropzone').addEventListener('drop', drop);

</script>
@endsection
