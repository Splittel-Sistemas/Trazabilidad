@extends('layouts.menu2') 

@section('title', 'Usuarios') 

@section('styles')
    <!-- Meta CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .btn.toggle-status i {
            font-size: 1.5rem;
        }
        .badge-info {
            background-color: #17a2b8 !important;
            color: white !important;
        }
        .btn.toggle-status {
            border: none;
            background-color: transparent;
            padding: 10px;
            cursor: pointer;
            transform: scale(2);
            transition: transform 0.3s ease; 
        }
        .btn.toggle-status.active {
            color: #28a745; /* Verde para activo */
        }
        .btn.toggle-status.inactive {
            color: #dc3545; /* Rojo para inactivo */
        }
        .btn.toggle-status:hover {
            transform: scale(2.2); /* Aumenta el tamaño al pasar el cursor */
        }
        #roles .form-check {
            display: flex;
            align-items: center;
            margin-right: 1rem; /* Espaciado entre roles */
            margin-bottom: 0.5rem; /* Espaciado vertical para nuevas filas */
        }
        .search-box-icon {
            top: 50%;
            transform: translateY(-50%);
            color: #888; /* Color del icono */
            pointer-events: none; /* Evita que el icono sea clickeable */
        }
    </style>
@endsection

@section('content')
<!-- Breadcrumbs -->
<div class="row gy-3 mb-2 justify-content-between">
    <div class="col-md-9 col-auto">
    <h4 class="mb-2 text-1100">Usuarios</h4>
    </div>
</div>
<!-- Contenido principal -->
<div class="container my-4">
    @if(Auth::user()->hasPermission("Crear Usuario"))
        <a href="{{ route('registro.create') }}" class="btn btn-outline-info mb-3">Agregar Usuario</a>
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
    <div class="card p-4" style="display:block;" id="tableExample3" data-list="{&quot;valueNames&quot;:[&quot;apellido&quot;,&quot;nombre&quot;,&quot;email&quot;,&quot;roles&quot;,&quot;estatus&quot;],&quot;page&quot;:10,&quot;pagination&quot;:true}">
        <!--<div class="row justify-content-end g-0">
            <div class="col-auto px-3">
                <span>Registros</span>
                <select id="NumeroRegistros" class="form-select form-select-sm">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
              </select>
            </div>
        </div>
        <div class="search-box mb-2 mx-auto">
            <form class="position-relative d-flex align-items-center" data-bs-toggle="search" data-bs-display="static">
                <input class="form-control search-input search form-control-sm rounded-pill pe-5" 
                    type="search" 
                    placeholder="Buscar" 
                    aria-label="Buscar">
                <svg class="position-absolute end-0 me-3 search-box-icon" width="16" height="16" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="M8 0C3.58 0 0 3.58 0 8s3.58 8 8 8 8-3.58 8-8S12.42 0 8 0zm0 14C4.69 14 2 11.31 2 8s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"></path>
                </svg>
            </form>
        </div>-->
        <div class="table-responsive">
                <table id="Tabla-Usuarios" class="table table-striped table-sm fs--1 mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="sort border-top ps-3" data-sort="nombre">Nombre</th>
                            <th class="sort border-top ps-3" data-sort="apellido">Apellido (s)</th>
                            <th class="sort border-top ps-3" data-sort="email">Correo</th>
                            <th class="sort border-top ps-3" data-sort="password">Contraseña</th>
                            <th class="sort border-top ps-3" data-sort="roles">Rol</th>
                            <th class="sort border-top ps-3" data-sort="estatus">Activar</th>
                            <th class="sort border-top text-center  ps-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @foreach ($personal as $registro)
                        <tr>
                            <td class="align-middle nombre ps-3">{{ $registro->name }}</td>
                            <td class="align-middle apellido ps-3">{{ $registro->apellido }}</td>
                           <td class="align-middle email ps-3">
                                {{ $registro->email }}
                            </td>
                            <td class="align-middle contraseña ps-3">
                                @if (Str::startsWith($registro->password, ['$2y$', '$2b$', '$2a$']) && strlen($registro->password) === 60)
                                    ********
                                @else
                                    {{ $registro->password }}
                                @endif
                            </td>
                            <td class="align-middle roles ps-3">
                                @foreach ($registro->roles as $role)
                                    <span class="badge badge-info">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="align-center estatus ps-8">
                                @if(Auth::user()->hasPermission("Activar/Desactivar Usuario"))
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-status" style="transform:scale(1.5);"
                                        type="checkbox" id="ActivarUsuario{{ $registro->id }}" 
                                        data-id="{{ $registro->id }}" 
                                        data-active="{{ $registro->active ? '1' : '0' }}" 
                                        {{ $registro->active ? 'checked' : '' }}>
                                </div>
                                @endif
                            </td>
                            <td class=" text-center pe-2 m-0">
                                @if(Auth::user()->hasPermission("Editar Usuario"))
                                    <button type="button" class="btn btn-outline-warning btn-sm d-inline-flex align-items-center" onclick="EditarUsuario(this);" data-bs-toggle="modal" data-bs-target="#userModal" data-id="{{ $registro->id }}"><i class="fas fa-edit"></i> Editar</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
        <!--<div class="d-flex justify-content-between mt-3">
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
        </div>-->
</div>
<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="userModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="userEditForm" action="{{ route('registro.update', ['id' => $registro->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="ModalBodyEditarUsuario">
                    <!-- Fila para Apellido y Nombre juntos -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Ingrese su nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido">Apellido (s)</label>
                                <input type="text" name="apellido" id="apellido" class="form-control" placeholder="Ingrese su apellido" required>
                            </div>
                        </div>
                    </div>
                    <!-- Fila para Correo Electrónico -->
                    <div class="form-group mb-2">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su email" required>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row mb-2" id="ContainerPassword">
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
                    <div class="form-group mt-2">
                        <label for="roles">Rol</label>
                        <div id="roles" class="form-check d-flex flex-wrap">
                            <!-- Los roles se generarán aquí dinámicamente -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class='d-flex justify-content-center align-items-center'>
                        <div class='spinner-grow text-primary' role='status' id="ModalBodyEditarUsuarioCargar">
                            <span class='visually-hidden'>Loading...</span>
                        </div>
                    </div>
                    <br>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        // Manejar el envío del formulario a través de AJAX
        $(document).on('submit', '#userEditForm', function(event) {
            event.preventDefault();
            var form = $(this);
            var formData = form.serialize();
            $.ajax({
                url: form.attr('action'),
                method: 'POST', 
                data: formData + '&_method=PUT', 
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        Swal.fire('Éxito', response.message, 'success').then(() => {
                            $('#userModal').modal('hide');
                            location.reload(); 
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Ocurrió un problema', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Hubo un problema en el servidor.', 'error');
                }
            });
        });
    });
    function EditarUsuario(registro){
        var userId = $(registro).data('id');  
        var ContainerPassword = $('#ContainerPassword');
        var ModalBodyEditarUsuario = $('#ModalBodyEditarUsuario');
        var ModalBodyEditarUsuarioCargar = $('#ModalBodyEditarUsuarioCargar');
        ModalBodyEditarUsuario.hide();
        ContainerPassword.hide();
        var url = "{{ route('registro.show', ['id' => '__userId__']) }}".replace('__userId__', userId);
        $('#apellido').val('');
        $('#email').val('');  
        $('#name').val('');
        $('#password').val('');
        $('#password_confirmation').val('');
        $.ajax({
            url: url,
            method: 'GET',
            beforeSend: function() {
                    ModalBodyEditarUsuarioCargar.show();
            },
            success: function(response) {
                ModalBodyEditarUsuario.show();
                ModalBodyEditarUsuarioCargar.hide();
                if(response.role == 'A'){
                    ContainerPassword.show();
                }
                $('#apellido').val(response.apellido);
                $('#email').val(response.email);  
                $('#name').val(response.name);
                $('#password').val('');
                $('#password_confirmation').val('');
                $('#userEditForm').attr('action', '{{ route('registro.update', '__userId__') }}'.replace('__userId__', userId));
                var roles = response.roles;
                var rolesContainer = $('#roles');
                rolesContainer.empty();
                @foreach ($roles as $role)
                    var isChecked = roles.includes({{ $role->id }}) ? 'checked' : '';
                    rolesContainer.append(
                        '<div class="form-check">' +
                            '<input type="radio" class="form-check-input" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" ' + isChecked + '>' +
                            '<label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>' +
                        '</div>'
                    );
                @endforeach

                // Mostrar el modal
                $('#userModal').modal('show');
            },
            error: function(xhr, status, error) {
                $('#userModal').modal('hide');
                error('Ocurrio un error!,los datos no pudieron ser procesados, intente de nuevo! si el error persiste contacte a TI.');
            }
        });
    }
    $(document).ready(function () {
        $('.toggle-status').on('change', function () {
            var checkbox = $(this);
            var userId = checkbox.data('id');
            var isActive = checkbox.is(':checked'); 
            var url = isActive ? "{{ route('users.activar') }}" : "{{ route('users.desactivar') }}";
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    user_id: userId,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (response) {
                    checkbox.data('active', isActive ? '1' : '0');
                },
                error: function () {
                    checkbox.prop('checked', !isActive);
                    alert('Hubo un error al cambiar el estado.');
                }
            });
        });
    });
    new DataTable('#Tabla-Usuarios', {
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
            "sInfoFiltered": "(filtrado de _MAX_ entradas en total)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sPrevious": "Anterior",
                "sNext": "Siguiente",
                "sLast": "Último"
            }
        }
        });
</script>
@endsection
