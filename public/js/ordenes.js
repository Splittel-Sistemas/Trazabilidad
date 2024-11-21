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