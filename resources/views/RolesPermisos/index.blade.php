@extends('layouts.menu2')

@section('title', 'Roles & Permisos')

@section('styles')
    <!-- Meta CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Estilos específicos para los botones de cambio de estado -->
    <style>
        .btn.toggle-status {
            border: none;
            background-color: transparent;
            padding: 10px;
            cursor: pointer;
        }

        .btn.toggle-status i {
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .btn.toggle-status.active i {
            color: #28a745;
        }

        .btn.toggle-status.inactive i {
            color: #dc3545;
        }

        .btn.toggle-status:hover i {
            transform: scale(1.2);
        }
        .permisos ul {
        display: flex;
        flex-wrap: wrap; /* Ajusta los elementos a la siguiente línea */
        max-height: 3rem; /* Limita la altura para filas más pequeñas */
        overflow-y: auto; /* Agrega scrollbar si hay demasiados elementos */
        padding: 0;
        margin: 0;
    }

    .permisos li {
        flex: 1 1 calc(20% - 0.4rem); /* Hasta 5 elementos por fila */
        max-width: calc(20% - 0.4rem);
        margin-bottom: 0.25rem; /* Menor espacio vertical entre filas */
        white-space: nowrap; /* Evita que el texto se rompa en varias líneas */
        overflow: hidden; /* Oculta texto excedente */
        text-overflow: ellipsis; /* Muestra "..." si el texto es muy largo */
        text-align: left; /* Alinea el texto a la izquierda */
        font-size: 0.85rem; /* Tamaño de texto más pequeño */
        color: #333; /* Texto con color neutro */
    }
    .permissions-container {
        display: flex;
        flex-wrap: wrap; /* Permite que los elementos pasen a una nueva línea si no caben */
        gap: 10px;       /* Espaciado entre elementos */
    }

    .form-check {
        display: flex;
        align-items: center; /* Alinea el checkbox con el texto */
    }



    .search-input {
    border-radius: 10px; /* Ajusta según necesidad */
}



       

    </style>
@endsection

@section('content')
    <!-- Breadcrumbs -->
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
        <h4 class="mb-2 text-1100">Roles & Permisos</h4>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container my-4">
        @if(Auth::user()->hasPermission("RolesEdit"))

        <a href="{{ route('RolesPermisos.create') }}" class="btn btn-outline-info mb-3">Agregar Rol</a>
       
        @endif
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
        <div class="card p-4" style="display:block;" id="roles-table" data-list='{"valueNames":["nombreRol","permisos"],"page":5,"pagination":true}'>
            <div class="search-box mb-3 mx-auto">
                <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                    <input class="form-control search-input search form-control-sm rounded-pill" type="search" placeholder="Buscar" aria-label="Buscar">
                </form>   
            </div>
            <div class="table-responsive">
                <div class="card shadow-sm">
                    <table class="table table-bordered table-striped table-sm fs--1" data-list='{"page":5,"pagination":{"innerWindow":2,"left":1,"right":1}}'>
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="sort border-top ps-3" data-sort="nombreRol" style="width: 15%">Nombre del Rol</th>
                                <th class="sort border-top" data-sort="permisos" style="width: 70%">Permisos</th>
                                <th class="sort ps-3 border-top" style="width: 15%">Accion</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @foreach ($roles as $role)
                            <tr>
                                <td class="nombreRol align-middle ps-3">{{ $role->name }}</td>
                                <td class="permisos align-middle">
                                    <ul class="list-unstyled d-flex flex-wrap">
                                        @foreach ($role->permissions as $permission)
                                            <li class="me-2 mb-1 text-truncate">
                                                <span class="bullet">•</span> {{ $permission->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="text-center pe-0">
                                    @if(Auth::user()->hasPermission("RolesEdit"))
                                    <button type="button" class="btn btn-outline-warning btn-sm btn-edit" 
                                            data-bs-toggle="modal" data-bs-target="#roleModal" 
                                            data-id="{{ $role->id }}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button> 
                                    @endif 
                                </td>
                              
                            </tr>
                            
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                    <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
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
                <form id="roleEditForm" action="{{ route('RolesPermisos.update', '__roleId__') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label for="roleName">Nombre del Rol</label>
                            <input type="text" class="form-control" id="roleName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="rolePermissions" class="mb-1">Permisos</label>
                            <div class="form-check mr-3 mb-2 col-6 mb-2">
                                <input type="checkbox" id="MarcarTodoCheck" class="form-check-input">
                                <label for="MarcarTodo" class="form-check-label">Marcar todo</label>
                            </div>
                            <div class="container">
                                <div id="rolePermissions"  class="row">
                                    <!-- Los checkboxes se generarán aquí dinámicamente -->
                                </div>
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

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        $('.btn-edit').on('click', function() {
            var roleId = $(this).data('id');
            var formAction = "{{ route('RolesPermisos.update', '__roleId__') }}".replace('__roleId__', roleId);
            $('#roleEditForm').attr('action', formAction);
            $.ajax({
                url: "{{ route('RolesPermisos.edit', '__roleId__') }}".replace('__roleId__', roleId),
                method: 'GET',
                success: function(data) {
                    $('#roleName').val(data.name);
                    var permissionsContainer = $('#rolePermissions');
                    permissionsContainer.empty();
                    data.available_permissions.forEach(function(permission) {
                        permissionsContainer.append(`
                            <div class="form-check col-4">
                                <input type="checkbox" class="form-check-input" id="permission-${permission.id}" name="permissions[]" value="${permission.id}" ${data.permissions.includes(permission.id) ? 'checked' : ''}>
                                <label class="form-check-label" for="permission-${permission.id}">${permission.name}</label>
                            </div>
                        `);
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Hubo un error al cargar los datos del rol.', 'error');
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('MarcarTodoCheck').addEventListener('change', function(){
                var estaMarcado = $('#MarcarTodoCheck').prop('checked');
                var permisos = document.querySelectorAll('#rolePermissions .form-check-input');
                if (estaMarcado) {
                    permisos.forEach(checkbox => {
                        checkbox.checked=true;
                    });
                }else{
                    permisos.forEach(checkbox => {
                        checkbox.checked=false;
                    });
                }
            });
        });
    </script>
@endsection
