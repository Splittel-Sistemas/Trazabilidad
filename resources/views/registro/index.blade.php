@extends('layouts.menu2') 

@section('title', 'Usuarios') 

@section('styles')
    <!-- Meta CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Estilos específicos para los botones de cambio de estado -->
    <style>
    
        /* Estilo para los íconos de los botones */
    .btn.toggle-status i {
        font-size: 1.5rem; /* Tamaño del ícono */
    }
    .badge-info {
        background-color: #17a2b8 !important; /* Color de fondo info */
        color: white !important; /* Color del texto */
    }
   


        /* Estilos para los botones toggle */
    .btn.toggle-status {
        border: none; /* Sin borde */
        background-color: transparent; /* Fondo transparente */
        padding: 10px;
        cursor: pointer;
        transform: scale(2);
        transition: transform 0.3s ease; /* Para la animación de escala */
    }

    /* Estilo cuando el estado está activo (verde) */
    .btn.toggle-status.active {
        color: #28a745; /* Verde para activo */
    }

    /* Estilo cuando el estado está inactivo (rojo) */
    .btn.toggle-status.inactive {
        color: #dc3545; /* Rojo para inactivo */
    }

    /* Hover sobre los botones */
    .btn.toggle-status:hover {
        transform: scale(2.2); /* Aumenta el tamaño al pasar el cursor */
    }
    </style>
@endsection

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs mb-4">
    <div class="row g-0">
        <div class="col-sm-6">
            <div class="page-header">
                <h1 class="fs-2">Usuarios</h1>
            </div>
        </div>
        <div class="col-sm-6 d-flex justify-content-end">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
            </ol>
        </div>
    </div>
</div>

<!-- Contenido principal -->
<div class="container my-4">
    <a href="{{ route('registro.create') }}" class="btn btn-outline-info mb-3">Agregar Usuario</a>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div id="tableExample3" data-list="{&quot;valueNames&quot;:[&quot;apellido&quot;,&quot;nombre&quot;,&quot;email&quot;,&quot;roles&quot;,&quot;estatus&quot;],&quot;page&quot;:5,&quot;pagination&quot;:true}">
        <div class="search-box mb-3 mx-auto">
            <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                <input class="form-control search-input search form-control-sm" type="search" placeholder="Buscar" aria-label="Buscar">
                <svg class="svg-inline--fa fa-magnifying-glass search-box-icon" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="M500.3 443.7..."></path>
                </svg>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-sm fs--1 mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="sort border-top" data-sort="nombre">Nombre</th>
                        <th class="sort border-top ps-3" data-sort="apellido">Apellido</th>
                        <th class="sort border-top" data-sort="email">Correo</th>
                        <th class="sort border-top" data-sort="roles">Role</th>
                        <th class="sort border-top" data-sort="estatus">Activo</th>
                        <th class="sort border-top text-center  pe-0">Acciones</th>
                    </tr>
                </thead>
                <tbody class="list">
                    @foreach ($personal as $registro)
                    <tr>
                        <td class="align-middle nombre">{{ $registro->name }}</td>
                        <td class="align-middle ps-3 apellido">{{ $registro->apellido }}</td>
                        <td class="align-middle email">{{ $registro->email }}</td>
                        <td class="align-middle roles">
                            @foreach ($registro->roles as $role)
                                <span class="badge badge-info">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="align-middle estatus">
                            <button class="btn toggle-status {{ $registro->active ? 'active' : 'inactive' }}" data-id="{{ $registro->id }}" data-active="{{ $registro->active ? '1' : '0' }}">
                                <i class="fa {{ $registro->active ? 'fa-toggle-on' : 'fa-toggle-off' }}" aria-hidden="true"></i>
                            </button>
                        </td>
                        <td class=" text-center pe-0">
                            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#userModal" data-id="{{ $registro->id }}"><i class="fas fa-edit"></i> Editar</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <span class="d-none d-sm-inline-block" data-list-info="data-list-info">1 a 5 artículos de 43</span>
            <div class="d-flex">
                <button class="page-link disabled" data-list-pagination="prev" disabled><svg class="svg-inline--fa fa-chevron-left" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M224 480c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25l192-192c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25L77.25 256l169.4 169.4c12.5 12.5 12.5 32.75 0 45.25C240.4 476.9 232.2 480 224 480z"></path></svg></button>
                <ul class="mb-0 pagination">
                    <li class="active"><button class="page" type="button" data-i="1" data-page="5">1</button></li>
                    <li><button class="page" type="button" data-i="2" data-page="5">2</button></li>
                    <li><button class="page" type="button" data-i="3" data-page="5">3</button></li>
                </ul>
                <button class="page-link" data-list-pagination="next"><svg class="svg-inline--fa fa-chevron-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M96 480c8.188 0 16.38-3.125 22.62-9.375l192-192c12.5-12.5 12.5-32.75 0-45.25l-192-192c-12.5-12.5-32.75-12.5-45.25 0s-12.5 32.75 0 45.25l169.4 169.4l-169.4 169.4c-12.5 12.5-12.5 32.75 0 45.25C79.62 476.9 87.81 480 96 480z"></path></svg></button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="userModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="userEditForm" method="POST" action="{{ route('registro.update', 0) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Fila para Apellido y Nombre juntos -->
                    <div class="row mb-4">
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
                    <!-- Fila para Correo Electrónico -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su email" required>
                        </div>
                    </div>
                    <div class="row mb-4">
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
                    <!-- Fila para Roles -->
                    <div class="form-group">
                        <label for="roles">Roles</label>
                        <div id="roles" class="form-check">
                            <!-- Los roles se generarán aquí dinámicamente -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>

    $(document).ready(function() {
        /*$('#usuarios-table').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
            }
        });*/

        // Cargar datos del modal
        $('button[data-bs-toggle="modal"]').on('click', function() {
            var userId = $(this).data('id');  
            var url = "{{ route('registro.show', ['id' => '__userId__']) }}".replace('__userId__', userId);
            //var url = '/registro/' + userId;  
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    console.log(response);  // Verifica la respuesta en la consola
                    $('#apellido').val(response.apellido);
                    $('#email').val(response.email);  
                    $('#name').val(response.name);
                    $('#password').val(response.password);
                   
                    
                    // Actualiza la acción del formulario
                    $('#userEditForm').attr('action', '/registro/' + userId);

                    // Agregar los roles
                    var roles = response.roles;
                    var rolesContainer = $('#roles');
                    rolesContainer.empty();

                    @foreach ($roles as $role)
                        var isChecked = roles.includes({{ $role->id }}) ? 'checked' : '';
                        rolesContainer.append(
                            '<div class="form-check">' +
                                '<input type="checkbox" class="form-check-input" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" ' + isChecked + '>' +
                                '<label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>' +
                            '</div>'
                        );
                    @endforeach

                    // Redibuja el modal
                    $('#userModal').modal('show');  // Este es el lugar donde debes asegurarte de que se redibuje después de la asignación
                }
            });



        });

        // Cambio de estado
        $('.toggle-status').on('click', function () {
    var button = $(this); 
    var userId = button.data('id'); 
    var isActive = button.data('active') == '1'; 

    // Usar Laravel route() para obtener la URL
    var url = isActive ? "{{ route('users.desactivar') }}" : "{{ route('users.activar') }}";
    var newState = isActive ? 0 : 1;

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            user_id: userId,
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        success: function (response) {
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
    
    </script>
@endsection
