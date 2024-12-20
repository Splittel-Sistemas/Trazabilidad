@extends('layouts.menu')

@section('title', 'Registro Usuario')

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
                    <form id="edit-form" action="" method="POST" class="shadow p-4 rounded bg-white">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id" id="user-id">

                        <div class="form-row mb-2">
                            <div class="form-group col-md-6">
                                <label for="apellido">Apellido</label>
                                <input type="text" name="apellido" id="apellido" class="form-control form-control-sm" required placeholder="Ingrese su apellido">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="nombre">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control form-control-sm" required placeholder="Ingrese su nombre">
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control form-control-sm" required placeholder="Ingrese su correo electrónico">
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

                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->id }}" class="form-check-input">
                                        <label for="role_{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group col-md-6">
                                <label class="font-weight-bold">Permisos</label>
                                <small class="form-text text-muted">Seleccione uno o más permisos.</small>

                                @foreach ($permissions as $permission)
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->id }}" class="form-check-input">
                                        <label for="permission_{{ $permission->id }}" class="form-check-label">{{ $permission->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" id="update-button">Actualizar Usuario</button>
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
    <script src="{{ asset('js/usuario.js') }}"></script>
@endsection
