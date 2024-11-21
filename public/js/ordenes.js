// Función para manejar el inicio del arrastre
function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
}

// Función para permitir el "drop" en las zonas de Dropzone
function allowDrop(event) {
    event.preventDefault();
}

// Función para manejar el "drop" y mover la orden a un nuevo estado
function drop(event, newState) {
    event.preventDefault();
    var data = event.dataTransfer.getData("text");
    var draggedRow = document.getElementById(data);

    // Crear un nuevo div con los datos de la fila arrastrada
    var rowData = draggedRow.innerHTML;
    var dropzoneContent;
    var dropzoneArea;

    // Determinar qué zona de Dropzone se usó y cuál es el nuevo estado
    if (newState === "pendiente") {
        dropzoneArea = 'dropzone-content-pendiente';
    } else if (newState === "enproceso") {
        dropzoneArea = 'dropzone-content-enproceso';
    } else if (newState === "completado") {
        dropzoneArea = 'dropzone-content-completado';
    }

    // Crear el div con la nueva orden y estado
    var newDataDiv = document.createElement('div');
    newDataDiv.classList.add('dropzone-item');
    newDataDiv.innerHTML = `<strong>Orden:</strong> ${rowData} <br><strong>Estado:</strong> ${newState}`;

    // Añadir el nuevo div a la zona correspondiente
    document.getElementById(dropzoneArea).appendChild(newDataDiv);

    // Actualizar el estado de la orden en el servidor
    updateOrderState(draggedRow.dataset.id, newState);
}

// Función para actualizar el estado de la orden
function updateOrderState(orderId, newState) {
    $.ajax({
        url: '/ordenes/' + orderId + '/update-state',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            state: newState
        },
        success: function(response) {
            console.log('Estado actualizado:', response);
        },
        error: function(xhr, status, error) {
            console.log('Error al actualizar el estado:', error);
        } 
    });
}

// Función para eliminar la orden
function deleteOrder(orderId) {
    if (confirm('¿Estás seguro de que deseas eliminar esta orden?')) {
        $.ajax({
            url: '/orden-venta/' + orderId + '/delete',
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Eliminar la fila de la tabla si la eliminación fue exitosa
                $('#orden-' + orderId).remove();
                alert('Orden eliminada correctamente');
            },
            error: function(xhr, status, error) {
                console.log('Error al eliminar la orden:', error);
                alert('Hubo un error al eliminar la orden.');
            }
        });
    }
}

// Activar los eventos "allowDrop" y "drop" para cada zona de Dropzone
document.getElementById('dropzone-pendiente').addEventListener('dragover', allowDrop);
document.getElementById('dropzone-enproceso').addEventListener('dragover', allowDrop);
document.getElementById('dropzone-completado').addEventListener('dragover', allowDrop);

document.getElementById('dropzone-pendiente').addEventListener('drop', function(event) {
    drop(event, "pendiente");
});
document.getElementById('dropzone-enproceso').addEventListener('drop', function(event) {
    drop(event, "enproceso");
});
document.getElementById('dropzone-completado').addEventListener('drop', function(event) {
    drop(event, "completado");
});
