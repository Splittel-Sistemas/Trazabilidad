@extends('layouts.menu')

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
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Roles & Permisos</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Dashboard</a></li>
                                <li><a href="#">Roles & Permisos</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contenido principal -->
    <div class="container my-4">
        
        <a href="{{ route('RolesPermisos.create') }}" class="btn btn-outline-info mb-3">Agregar Rol</a>
    
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
            <table id="roles-table" class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Nombre del Rol</th>
                        <th>Permisos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>
                                <ul>
                                    @foreach ($role->permissions as $permissions)
                                        <li>{{ $permissions->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <!-- Botón para editar los permisos del rol -->
                                <button class="btn btn-outline-info btn-sm btn-edit"data-toggle="modal"data-target="#roleModal"data-id="{{ $role->id }}"
                                    data-name="{{ $role->name }}"data-permissions="{{ implode(',', $role->permissions->pluck('id')->toArray()) }}"> Editar
                                </button>
                                <div class="form-check">
                                <input type="checkbox" name="roles[]" id="role" class="form-check-input"><label  class="form-check-label"></label>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
        <!-- Modal para editar el rol -->
    <div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Editar Rol</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="roleEditForm" method="POST" action="{{ route('RolesPermisos.update', 0) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="roleName">Nombre del Rol</label>
                            <input type="text" class="form-control" id="roleName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="rolePermissions">Permisos</label>
                            <select class="form-control" id="rolePermissions" name="permissions[]" multiple required>
                                <!-- Opciones de permisos irán aquí -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
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
        document.addEventListener('DOMContentLoaded', function () {
        
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function () {
                const roleId = this.getAttribute('data-id'); 
 
                fetch(`/RolesPermisos/${roleId}/edit`)
                    .then(response => response.json())
                    .then(data => {

                        const form = document.querySelector('#edit-role-form');
                        form.action = `/RolesPermisos/${roleId}`;

                        document.querySelector('#name').value = data.name;

                        const permissionsSelect = document.querySelector('#permission');
                        permissionsSelect.innerHTML = ''; 
                        data.permissions.forEach(permission => {
                            const option = document.createElement('option');
                            option.value = permission.id;
                            option.textContent = permission.name;
                            option.selected = data.assigned_permissions.includes(permission.id);
                            permissionsSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar los datos:', error);
                    });
            });
        });
    })
    $('#roleModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Botón que abrió el modal
    var roleId = button.data('id'); // Extraer el ID del rol
    var roleName = button.data('name'); // Extraer el nombre del rol
    var rolePermissions = button.data('permissions').split(','); // Extraer los permisos seleccionados

    var modal = $(this);
    modal.find('#roleName').val(roleName); // Llenar el campo de nombre del rol
    modal.find('[name="permissions[]"]').val(rolePermissions); // Llenar el campo de permisos

    // Actualizar la acción del formulario con el ID del rol
    var formAction = '{{ route("RolesPermisos.update", ":id") }}';
    formAction = formAction.replace(':id', roleId);
    modal.find('#roleEditForm').attr('action', formAction);
});

</script>
        
    
@endsection
