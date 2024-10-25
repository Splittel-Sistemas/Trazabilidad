@extends('layouts.menu') 
@section('title', 'Registro Usuario') 

@section('content')
    <div class="container">
        <h1>Lista de Usuarios</h1>
        <a href="{{ route('registro.create') }}" class="btn btn-primary">Agregar Usuario</a>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($personal as $registro)
                    <tr id="registro-{{ $registro->id }}">
                        <td>{{ $registro->apellido }}</td>
                        <td>{{ $registro->nombre }}</td>
                        <td>{{ $registro->email }}</td>
                        <td>
                            <a href="#" class="btn btn-warning edit-button" data-id="{{ $registro->id }}" data-toggle="modal" data-target="#miModal">Editar</a>
                            <button type="button" class="btn btn-danger delete-button" data-id="{{ $registro->id }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mensajes de estado -->
    <div class="alert alert-danger" style="display: none;"></div>
    <div class="alert alert-success" style="display: none;"></div>

    <!-- Modal -->
    <div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="miModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <<div class="modal-header" style=" color: black;">
                    <h5 class="modal-title" id="miModalLabel" style="font-size: 1.25rem; font-weight: bold;">Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-form" action="{{ route('registro.update', $registro->id) }}" method="POST" class="shadow p-4 rounded bg-white">
                        @csrf
                        @method('PUT')
    
                        <input type="hidden" name="id" value="{{ $registro->id }}">
    
                        <div class="form-row mb-2">
                            <div class="form-group col-md-6">
                                <label for="apellido">Apellido</label>
                                <input type="text" name="apellido" id="apellido" class="form-control form-control-sm" value="{{ old('apellido', $registro->apellido) }}" required placeholder="Ingrese su apellido">
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="nombre">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control form-control-sm" value="{{ old('nombre', $registro->nombre) }}" required placeholder="Ingrese su nombre">
                            </div>
                        </div>
    
                        <div class="form-group mb-2">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control form-control-sm" value="{{ old('email', $registro->email) }}" required placeholder="Ingrese su correo electrónico">
                        </div>
    
                        <div class="form-group mb-4">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su nueva contraseña (dejar en blanco para no cambiar)">
                        </div>
    
                        <div class="form-group mb-4">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirme su nueva contraseña">
                        </div>
                        
                
                        <!-- Campo para seleccionar roles -->
                        <div class="form-row mb-4">
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold">Roles</label>
                                <small class="form-text text-muted">Seleccione uno o más roles.</small>
                        
                                @foreach ($roles as $value)
                                    <div class="form-check">
                                        <input type="checkbox" name="roles[]" id="role_{{ $value->id }}" value="{{ $value->id }}" class="form-check-input" 
                                            {{ (isset($registro) && $registro->roles->contains($value->id)) ? 'checked' : '' }}>
                                        <label for="role_{{ $value->id }}" class="form-check-label">{{ $value->name }}</label>
                                    </div>
                                @endforeach
                                
                                @error('roles') 
                                    <div class="text-danger">{{ $message }}</div> 
                                @enderror
                            </div>
                        
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold">Permisos</label>
                                <small class="form-text text-muted">Seleccione uno o más permisos.</small>
                                
                                @foreach ($permissions as $value)
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" id="permission_{{ $value->id }}" value="{{ $value->id }}" class="form-check-input" 
                                            {{ (isset($registro) && $registro->permissions->contains($value->id)) ? 'checked' : '' }}>
                                        <label for="permission_{{ $value->id }}" class="form-check-label">{{ $value->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        

                        <div class="modal-footer"><button type="button" class="btn btn-primary btn-sm" id="update-button">Actualizar Usuario</button></div>
                        

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts') 
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
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
    </script>
@endsection
