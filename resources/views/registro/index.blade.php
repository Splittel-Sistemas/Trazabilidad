@extends('layouts.menu')

@section('title', 'Usuarios')

@section('styles')
    <!-- Meta CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Estilos específicos para los botones de cambio de estado -->
    <style>
        /* Estilo para el botón */
        .btn.toggle-status {
            border: none; /* Sin borde */
            background-color: transparent; /* Fondo transparente */
            padding: 10px;
            cursor: pointer;
        }

        /* Estilo para el ícono */
        .btn.toggle-status i {
            font-size: 1.5rem; /* Tamaño del ícono */
            transition: color 0.3s ease;
        }

        /* Estilos para cuando el estado está activo o inactivo */
        .btn.toggle-status.active i {
            color: #28a745; /* Verde */
        }

        .btn.toggle-status.inactive i {
            color: #dc3545; /* Rojo */
        }

        /* Estilo de hover para hacer crecer el ícono */
        .btn.toggle-status:hover i {
            transform: scale(1.2);
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Usuarios</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Dashboard</a></li>
                                <li><a href="#">Usuarios</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contenido principal -->
    <div class="container my-4">
        <a href="{{ route('registro.create') }}" class="btn btn-outline-info mb-3">Agregar Usuario</a>
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="table-responsive">
            <table id="usuarios-table" class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Apellido</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Estatus Usuario</th>
                        <th>Acciones</th>
                      
                    </tr>
                </thead>
                <tbody>
                    @foreach ($personal as $registro)
                    <tr>
                        <td>{{ $registro->apellido }}</td>
                        <td>{{ $registro->name }}</td>
                        <td>{{ $registro->email }}</td>
                        <td>
                            @foreach ($registro->roles as $role)
                                <span class="badge badge-info">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <button class="btn toggle-status {{ $registro->active ? 'active' : 'inactive' }}" data-id="{{ $registro->id }}" data-active="{{ $registro->active ? '1' : '0' }}">
                                <i class="fa {{ $registro->active ? 'fa-toggle-on' : 'fa-toggle-off' }}" aria-hidden="true"></i>
                            </button>
                            <!-- Agregar más acciones aquí -->
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#userModal" data-id="{{ $registro->id }}}">Editar Usuario</button>
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="userEditForm" method="POST" action="{{ route('registro.update', 0) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="apellido">Apellido</label>
                                    <input type="text" name="apellido" id="apellido" class="form-control" placeholder="Ingrese su apellido" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Ingrese su nombre" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su correo electrónico" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Contraseña</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su nueva contraseña">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar Contraseña</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirme su nueva contraseña">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="roles">Roles</label>
                            <div id="roles" class="form-check">
                                <!-- Los roles se generarán aquí dinámicamente -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
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
    
<script>
$(document).ready(function() {
    $('#usuarios-table').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
        }
    });
});
$(document).ready(function() {
    // Cuando se hace clic en un botón para editar un usuario
    $('button[data-toggle="modal"]').on('click', function() {
        var userId = $(this).data('id');  
        var url = '/registro/' + userId;  

        // Realizar una solicitud AJAX para obtener los datos del usuario
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                
                $('#apellido').val(response.apellido);
                $('#name').val(response.name);
                $('#email').val(response.email);
                $('#password').val('');  
                $('#password_confirmation').val('');  

                // Establecer la acción del formulario para el usuario correspondiente
                $('#userEditForm').attr('action', '/registro/' + userId);

                // Cargar los roles dinámicamente
                var roles = response.roles; 
                var rolesContainer = $('#roles');
                rolesContainer.empty(); 

                // Iterar sobre todos los roles y marcarlos según la asignación
                @foreach ($roles as $role)
                    var isChecked = roles.includes({{ $role->id }}) ? 'checked' : '';
                    rolesContainer.append(
                        '<div class="form-check">' +
                            '<input type="checkbox" class="form-check-input" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" ' + isChecked + '>' +
                            '<label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>' +
                        '</div>'
                    );
                @endforeach
            }
        });
    });
});
$(document).ready(function () {
            $('.toggle-status').on('click', function () {
                var button = $(this); 
                var userId = button.data('id'); 
                var isActive = button.data('active') == '1'; 

                // Determina la URL y el nuevo estado
                var url = isActive ? '/users/desactivar' : '/users/activar';
                var newState = isActive ? 0 : 1;

                // Realiza la solicitud AJAX
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        user_id: userId,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (response) {
                        // Cambia el estado visual del ícono
                        button.data('active', newState);
                        if (newState) {
                            button.removeClass('inactive').addClass('active');
                            button.find('i').removeClass('fa-toggle-off').addClass('fa-toggle-on');
                        } else {
                            button.removeClass('active').addClass('inactive');
                            button.find('i').removeClass('fa-toggle-on').addClass('fa-toggle-off');
                        }
                    },
                    error: function () {
                        alert('Hubo un error al cambiar el estado.');
                    }
                });
            });
        });
        document.querySelectorAll('.btn-warning').forEach(button => {
    button.addEventListener('click', function() {
        let userId = this.getAttribute('data-id');
        let userName = this.closest('tr').querySelector('.name').textContent; 
        let userEmail = this.closest('tr').querySelector('.email').textContent; 
        
        document.getElementById('user-id').value = userId;
        document.getElementById('name').value = userName;
        document.getElementById('email').value = userEmail;

        let formAction = '/registro/' + userId;
        document.getElementById('edit-form').action = formAction;
    });
   
});

    </script>
@endsection
