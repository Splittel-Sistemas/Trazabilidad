function drop(event) {
    event.preventDefault(); 
    const draggedId = event.dataTransfer.getData("text");
    const draggedRow = document.getElementById(draggedId);
    if (draggedRow) {
        
        if (!document.getElementById(`migrated-${draggedId}`)) {
            
            const newRow = document.createElement("tr");
            newRow.innerHTML = draggedRow.innerHTML;

            
            const regresarButton = `
                <td>
                    <button class="btn btn-warning btn-sm" onclick="regresarRow('${draggedId}')">Regresar</button>
                </td>`;
            newRow.innerHTML += regresarButton;

            
            newRow.id = `migrated-${draggedId}`;

            
            document.getElementById("table-2-content").appendChild(newRow);

            
            draggedRow.style.display = "none";
        } else {
            console.log("Fila ya migrada.");
        }
    } else {
        console.error("Fila arrastrada no encontrada:", draggedId);
    }
}
function drag(event) {
    
    event.dataTransfer.setData("text", event.target.id);
}
function allowDrop(event) {
    event.preventDefault(); 
}
function regresarRow(rowId) {
    const migratedRow = document.getElementById(`migrated-${rowId}`);

    if (migratedRow) {
        
        const originalRow = document.getElementById(rowId);
        if (originalRow) {
            originalRow.style.display = ""; 
        }

        
        migratedRow.remove();
    }
}
document.getElementById("dropzone").addEventListener("dragover", allowDrop);
document.getElementById("dropzone").addEventListener("drop", drop);
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