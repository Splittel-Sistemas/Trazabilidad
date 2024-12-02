// Recuperar las consultas arrastradas desde localStorage
let consultasArrastradas = JSON.parse(localStorage.getItem('consultasArrastradas')) || [];

function drag(event) {
    const targetRow = event.target.closest('tr'); // Encontrar la fila que se está arrastrando
    const parentRow = targetRow.closest('.collapse'); // Verificar si el acorde está abierto

    // Si el acorde no está abierto, no permitir el arrastre
    if (!parentRow || !parentRow.classList.contains('show')) {
        event.preventDefault(); // No permitir el arrastre si el acorde está cerrado
        return;
    }

    // Verificar si la fila ya fue arrastrada
    if (consultasArrastradas.includes(targetRow.id)) {
        // Mostrar el mensaje en el contenedor
        const messageContainer = document.getElementById('message-container');
        messageContainer.style.display = 'block'; // Hacer visible el mensaje
        setTimeout(function() {
            messageContainer.style.display = 'none'; // Ocultar el mensaje después de 3 segundos
        }, 3000);
        event.preventDefault();
        return;
    }

    // Si el acorde está abierto, permitir el arrastre
    event.dataTransfer.setData("text", targetRow.id); // Asignar el ID de la fila arrastrada
}

function drop(event) {
    event.preventDefault();

    const draggedId = event.dataTransfer.getData("text"); // Obtener el ID de la fila arrastrada
    const draggedRow = document.getElementById(draggedId); // Obtener el elemento de la fila

    if (draggedRow) {
        // Verificar si la fila ya ha sido migrada
        if (!document.getElementById(`migrated-${draggedId}`)) {
            const newRow = document.createElement("tr"); // Crear una nueva fila
            newRow.innerHTML = draggedRow.innerHTML; // Copiar el contenido de la fila original

            // Agregar el botón "Regresar"
            const regresarButton = `
                <td>
                    <button class="btn btn-warning btn-sm" onclick="regresarRow('${draggedId}')">Regresar</button>
                </td>`;
            newRow.innerHTML += regresarButton;

            // Asignar un ID único a la nueva fila
            newRow.id = `migrated-${draggedId}`;
            document.getElementById("table-2-content").appendChild(newRow); // Agregar la nueva fila a la tabla de destino

            // Ocultar la fila original
            draggedRow.classList.add('migrated');
            draggedRow.style.display = "none";

            // Marcar como arrastrada y guardar en localStorage
            consultasArrastradas.push(draggedId);
            localStorage.setItem('consultasArrastradas', JSON.stringify(consultasArrastradas));
        } else {
            console.log("Fila ya migrada.");
        }
    } else {
        console.error("Fila arrastrada no encontrada:", draggedId);
    }
}

function allowDrop(event) {
    event.preventDefault(); 
}

function regresarRow(rowId) {
    const migratedRow = document.getElementById(`migrated-${rowId}`); // Obtener la fila migrada

    if (migratedRow) {
        const originalRow = document.getElementById(rowId); // Obtener la fila original
        if (originalRow) {
            originalRow.style.display = ""; // Mostrar la fila original
        }

        migratedRow.remove(); // Eliminar la fila migrada
        consultasArrastradas = consultasArrastradas.filter(id => id !== rowId); // Eliminar de la lista de arrastradas
        // Actualizar el localStorage
        localStorage.setItem('consultasArrastradas', JSON.stringify(consultasArrastradas));
    }
}

// Configuración de eventos para la zona de arrastre
document.getElementById("dropzone").addEventListener("dragover", allowDrop);
document.getElementById("dropzone").addEventListener("drop", drop);

// Al cargar la página, ocultar las filas que ya han sido arrastradas
document.addEventListener("DOMContentLoaded", () => {
    const rows = document.querySelectorAll('#table-source tr');
    rows.forEach(row => {
        if (consultasArrastradas.includes(row.id)) {
            row.style.display = "none"; // Ocultar la fila si ya fue arrastrada
        } else {
            row.style.display = ""; // Mostrar la fila si no fue arrastrada
        }
    });
});


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