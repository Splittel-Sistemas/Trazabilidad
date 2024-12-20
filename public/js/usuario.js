
    // Acción de actualizar usuario
    $(document).on('click', '#actualizar', function(e) {
        e.preventDefault();  
        
        const formData = $('#edit-form').serialize();  // Serializa los datos del formulario
        const url = $('#edit-form').attr('action');  // Obtener la URL de acción del formulario
        const userId = $('#user-id').val();  // Obtener el ID del usuario desde el campo correspondiente

        $.ajax({
            type: 'PUT',
            url: `/registro/${userId}`,  // Corregido para usar comillas invertidas
            data: formData,  // Enviar los datos del formulario
            success: function(response) {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Usuario actualizado con éxito.",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();  // Recargar la página para ver los cambios
                });
            },
            error: function(xhr) {
                $('.alert-danger').text('Error al actualizar el usuario: ' + xhr.responseJSON.message).show();  // Muestra mensaje de error
            }
        });
    });

    $(document).ready(function() {
        // Configura el token CSRF en las cabeceras de las solicitudes AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        // Ejemplo de una solicitud de eliminación de un registro
        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();
    
            const id = $(this).data('id');
            const url = $(this).data('url');
    
            if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
                $.ajax({
                    type: 'DELETE',
                    url: url,
                    success: function(response) {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Registro eliminado con éxito.',
                            showConfirmButton: true,
                            timer: 1500
                        });
                        $(`#registro-${id}`).remove();  // Eliminar la fila de la tabla
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'Error al eliminar',
                            text: 'No se pudo eliminar el registro. Intenta nuevamente.',
                            showConfirmButton: true,
                            timer: 1500
                        });
                    }
                });
            }
        });
    });
    
    
    // Inicialización de DataTable
    $(document).ready(function() {
        $('#usuarios-table').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
            }
        });
    });

