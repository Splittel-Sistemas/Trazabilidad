@extends('layouts.menu2')

@section('title', 'Roles & Permisos')

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

    <div class="breadcrumbs mb-4">
        <div class="row g-0">
            <div class="col-sm-6">
                <div class="page-header">
                    <h1 class="fs-2">Roles & Permisos</h1>
                </div>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Areas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Roles & Permisos</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container my-4">
        <a href="{{ route('RolesPermisos.create') }}" class="btn btn-outline-info mb-3">Agregar Rol</a>
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
        <div id="roles-table" data-list='{"valueNames":["nombreRol","permisos"],"page":5,"pagination":true}'>
            <div class="search-box mb-3 mx-auto">
                <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                    <input class="form-control search-input search form-control-sm" type="search" placeholder="Buscar" aria-label="Buscar">
                    <svg class="svg-inline--fa fa-magnifying-glass search-box-icon" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                        <path fill="currentColor" d="M500.3 443.7..."></path>
                    </svg>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm fs--1">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="sort border-top ps-3" data-sort="nombreRol">Nombre del Rol</th>
                            <th class="sort border-top" data-sort="permisos">Permisos</th>
                            <th class="sort text-end align-middle pe-0 border-top">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @foreach ($roles as $role)
                        <tr>
                            <td class="nombreRol align-middle ps-3">{{ $role->name }}</td>
                            <td class="permisos align-middle">
                                <ul>
                                    @foreach ($role->permissions as $permissions)
                                        <li>{{ $permissions->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="acciones align-middle text-end pe-0">
                                    <button class="btn btn-outline-warning btn-sm btn-edit" data-id="{{ $role->id }}" data-bs-toggle="modal" data-bs-target="#roleModal">
                                        Editar
                                    </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <span class="d-none d-sm-inline-block" data-list-info="data-list-info">1 a 5 artículos de {{ count($roles) }}</span>
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
    <!-- Modal para editar el rol -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Editar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="roleEditForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="roleName">Nombre del Rol</label>
                            <input type="text" class="form-control" id="roleName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="rolePermissions">Permisos</label>
                            <div id="rolePermissions" class="form-check">
                                <!-- Los checkboxes se generarán aquí dinámicamente -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inicialización de DataTables con idioma en español
           /* $('#roles-table').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
                }
            });*/

            // Configuración global de CSRF para solicitudes AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        // Evento para cargar datos del rol al abrir el modal
        $(document).on('click', '.btn-edit', function () {
            var roleId = $(this).data('id'); 

            // Realizar solicitud AJAX para obtener datos del rol
            $.ajax({
                url: `/RolesPermisos/${roleId}/edit`,
                method: 'GET',
                success: function (data) {
                    $('#roleName').val(data.name);
                    var formAction = `/RolesPermisos/${data.id}`;
                    $('#roleEditForm').attr('action', formAction);

                    var permissionsContainer = $('#rolePermissions');
                    permissionsContainer.empty();
                    data.available_permissions.forEach(permission => {
                        permissionsContainer.append(`
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="permission-${permission.id}" 
                                       name="permissions[]" value="${permission.id}" 
                                       ${data.permissions.includes(permission.id) ? 'checked' : ''}>
                                <label class="form-check-label" for="permission-${permission.id}">
                                    ${permission.name}
                                </label>
                            </div>
                        `);
                    });

                },
                error: function () {
                    Swal.fire('Error', 'No se pudieron cargar los datos del rol.', 'error');
                }
            });
        });

        // Evento para enviar el formulario de edición
        $(document).on('submit', '#roleEditForm', function (event) {
            event.preventDefault(); // Prevenir el envío tradicional del formulario

            var form = $(this);
            var formData = form.serialize();

            
            $.ajax({
                url: form.attr('action'),
                method: 'PUT',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Éxito', response.message, 'success').then(() => {
                            $('#roleModal').modal('hide'); 
                            location.reload(); 
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Ocurrió un problema', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo actualizar el rol.', 'error');
                }
            });
        });
    </script>
@endsection
