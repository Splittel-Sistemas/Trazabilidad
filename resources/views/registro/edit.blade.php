@extends('layouts.menu') 
@section('title', 'Editar Usuario')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Editar Usuario: {{ $registro->nombre }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="edit-form" action="{{ route('registro.update', $registro->id) }}" method="POST" class="shadow p-4 rounded bg-white">
            @csrf
            @method('PUT')

            <input type="hidden" name="id" value="{{ $registro->id }}">

            <div class="form-group mb-10">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido', $registro->apellido) }}" required placeholder="Ingrese su apellido">
            </div>

            <div class="form-group mb-3">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $registro->nombre) }}" required placeholder="Ingrese su nombre">
            </div>

            <div class="form-group mb-3">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $registro->email) }}" required placeholder="Ingrese su correo electrónico">
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
            <div class="form-group mb-4">
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

            <!-- Campo para seleccionar permisos -->
            <div class="form-group mb-4">
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

            <button type="button" class="btn btn-primary btn-block" id="update-button">Actualizar Usuario</button>
        </form>
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
                        window.history.back(); 
                    });
                },
                error: function(xhr) {
                    $('.alert-danger').text('Error al actualizar el usuario: ' + xhr.responseJSON.message).show(); // Muestra mensaje de error
                }
            });
        });
    </script>
@endsection
