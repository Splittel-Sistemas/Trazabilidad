@extends('layouts.menu')

@section('title', 'Registro Usuario')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
</head>

@section('content')
    <div class="container">
        <h1>Lista de Usuarios</h1>
        <a href="{{ route('registro.create') }}" class="btn btn-primary mb-3">Agregar Usuario</a>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <table id="usuarios-table" class="table table-bordered table-striped">
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
                    <td>{{ $registro->name }}</td>
                    <td>{{ $registro->email }}</td>
                    <td>
                        <a href="#" class="btn btn-outline-warning" data-id="{{ $registro->id }}" data-toggle="modal" data-target="#miModal">Editar</a>
                        
                        <!-- Cambiar $person->id a $registro->id -->
                        <button class="btn btn-outline-success btn-sm activar" data-id="{{ $registro->id }}">
                            Activar
                        </button>
                        <button class="btn btn-outline-danger btn-sm desactivar" data-id="{{ $registro->id }}">
                            Desactivar
                        </button>
            
                        <button class="btn btn-outline-danger  btn-sm" data-id="{{ $registro->id }}" data-url="{{ route('registro.destroy', $registro->id) }}"> Eliminar</button>
                    </td>
                </tr>
            @endforeach
            
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="miModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="color: black;">
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
                    
                        <div class="form-group mb-10">
                            <label for="apellido">Apellido</label>
                            <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido', $registro->apellido) }}" placeholder="Ingrese su apellido">
                        </div>
                    
                        <div class="form-group mb-3">
                            <label for="name">Nombre</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $registro->name) }}" placeholder="Ingrese su nombre">
                        </div>
                    
                        <div class="form-group mb-3">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $registro->email) }}" placeholder="Ingrese su correo electrónico">
                        </div>
                    
                        <div class="form-group mb-4">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su nueva contraseña (dejar en blanco para no cambiar)">
                        </div>
                    
                        <div class="form-group mb-4">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirme su nueva contraseña">
                        </div>
                    
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
                        </div>
                    
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
                        <button type="submit" class="btn btn-primary btn-block" id="actualizar">Actualizar Usuario</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/usuario.js') }}"></script>
    <script>
   // Seleccionar todos los botones de "Activar"
document.querySelectorAll('.activar').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();

        let userId = this.getAttribute('data-id');  // Obtener el ID del usuario

        // Hacer la solicitud Ajax (POST) al backend para activar al usuario
        fetch('/users/activar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')  // CSRF Token
            },
            body: JSON.stringify({ id: userId })  // Enviar el ID del usuario
        })
        .then(response => response.json())
        .then(data => {
            // Mostrar el mensaje según la respuesta
            if (data.message) {
                alert(data.message);  // Si el mensaje es exitoso
                // Opcional: Actualizar el UI para reflejar que el usuario fue activado
            } else {
                alert(data.error);  // Si ocurre un error
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error en la solicitud');
        });
    });
});

// Seleccionamos todos los botones de "Desactivar"
document.querySelectorAll('.desactivar').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();

        let userId = this.getAttribute('data-id');  // Obtener el ID del usuario

        // Hacer la solicitud Ajax (POST) al backend
        fetch('/users/desactivar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')  // CSRF Token
            },
            body: JSON.stringify({ id: userId })  // Enviar el ID del usuario
        })
        .then(response => response.json())
        .then(data => {
            // Mostrar el mensaje según la respuesta
            if (data.message) {
                alert(data.message);  // Si el mensaje es exitoso
                // Opcional: Actualizar el UI para reflejar que el usuario fue desactivado
            } else {
                alert(data.error);  // Si ocurre un error
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error en la solicitud');
        });
    });
});




    </script>
    
@endsection
