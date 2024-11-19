
        $(document).on('click', '#update-button', function(e) {
            e.preventDefault();  

            const formData = $('#edit-form').serialize(); // Serializa los datos del formulario
            const url = $('#edit-form').attr('action'); // Obtener la URL de acción del formulario

            $.ajax({
                type: 'PUT',
                url: url,
                data: formData,
                success: function(response) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Usuario actualizado con éxito.",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload(); // Recargar la página para ver los cambios
                    });
                },
                error: function(xhr) {
                    $('.alert-danger').text('Error al actualizar el usuario: ' + xhr.responseJSON.message).show(); // Muestra mensaje de error
                }
            });
        });

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();  

            const id = $(this).data('id');
            const url = `{{ route('registro.destroy', '') }}/${id}`; // URL de la ruta DELETE

            if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
                $.ajax({
                    type: 'DELETE',
                    url: url,
                    data: {
                        _token: '{{ csrf_token() }}' // Incluye el token CSRF
                    },
                    success: function(response) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Registro eliminado con éxito.",
                            showConfirmButton: true,
                            timer: 1500
                        });

                        $(`#registro-${id}`).remove(); // Elimina la fila de la tabla
                    },
                    error: function(xhr) {
                        Swal.fire({
                            position: "center",
                            icon: "error",
                            title: "Error al eliminar",
                            text: "No se pudo eliminar. Intenta nuevamente.",
                            showConfirmButton: true,
                            timer: 1500
                        });
                    }
                });
            }
        });
    
        $(document).ready(function() {
            $('#usuarios-table').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
                }
            });
        });
        