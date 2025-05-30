@extends('layouts.menu2')

@section('title', 'Roles & Permisos')

@section('styles')
    <!-- Meta CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Estilos específicos para los botones de cambio de estado -->
    <style>
        .permisos ul {
        display: flex;
        flex-wrap: wrap; /* Ajusta los elementos a la siguiente línea */
        padding: 0;
        margin: 0;
        }
        .permisos ul::-webkit-scrollbar {
            width: 6px; /* Ancho del scrollbar */
        }

        .permisos ul::-webkit-scrollbar-thumb {
            background-color: #c2c2c2; /* Color claro de Bootstrap (ejemplo: bg-light) */
            border-radius: 10px; /* Bordes redondeados del thumb */
        }

        .permisos ul::-webkit-scrollbar-track {
            background-color: #f8f9fa; 
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
        .Permisos-Colapse{
            height: 2.5rem;
            overflow: hidden;
        }


        .sub-permissions {
            display: none; /* Inicialmente oculto */
            padding-left: 20px;
            margin-top: 5px;
            font-size: 0.9rem;
            transition: all 0.3s ease-in-out; /* Efecto suave */
        }

        .sub-permissions .form-check {
            margin-left: 15px;
        }

        .card {
            border: 1px solid #ddd;
            margin-top: 10px;
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
        @if(Auth::user()->hasPermission("Crear Rol"))
            <a href="{{ route('RolesPermisos.create') }}" class="btn btn-outline-info mb-1">Crear Rol</a>
        @endif
        <div class="card p-4" style="display:block;" id="roles-table" data-list='{"valueNames":["nombreRol","permisos"],"page":10,"pagination":true}'>
            <div class="search-box mb-3 mx-auto">
              <form class="position-relative" data-bs-toggle="search" data-bs-display="static"><input class="form-control search-input search form-control-sm" type="search" placeholder="Search" aria-label="Search" />
                <span class="fas fa-search search-box-icon"></span>
              </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm fs--1">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="sort border-top ps-3" data-sort="nombreRol" style="width: 15%">Nombre del Rol</th>
                                <th class="sort border-top" data-sort="permisos" style="width: 70%">Permisos</th>
                                <th class="sort ps-3 border-top" style="width: 15%">Accion</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @foreach ($roles as $key=>$role)
                            <tr>
                                <td class="nombreRol align-middle ps-3">{{ $role->name }}</td>
                                <td class="permisos align-middle">
                                    <div class="row Permisos-Colapse" id="Fila{{$key}}">
                                        @foreach ($role->permissions as $permission)
                                            <div class="col-4 text-truncate">
                                                <span class="bullet">• {{ $permission->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($role->permissions->count()>6)
                                    <button id="BtnFila{{$key}}" onclick="MostrarPermisos('Fila{{$key}}')" class="btn btn-link m-0 p-0 float-end">Ver mas...</button>
                                    @endif
                                </td>
                                <td class="text-center pe-0">
                                    @if(Auth::user()->hasPermission("Editar Rol"))
                                    <button type="button" class="btn btn-outline-warning btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#roleModal" data-id="{{ $role->id }}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button> 
                                    @endif 
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                    <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar el rol -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Editar Rol <span id="EditarNombreAdministrador"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="roleEditForm" action="{{ route('RolesPermisos.update', '__roleId__') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-2 col-6">
                            <label for="roleName">Nombre del Rol</label>
                            <input type="text" class="form-control form-control-sm" id="roleName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="rolePermissions" class="mb-1">Permisos</label>
                            <small class="form-text text-muted">Seleccione uno o más permisos.</small>
                            <div class="form-check">
                                <input type="checkbox" id="MarcarTodoCheck" class="form-check-input">
                                <label for="MarcarTodo" class="form-check-label font-weight-bold mt-1">Marcar todo</label>
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
                        <button type="submit" class="btn btn-primary">Guardar</button>
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
            $('#EditarNombreAdministrador').html('');
            $.ajax({
                url: "{{ route('RolesPermisos.edit', '__roleId__') }}".replace('__roleId__', roleId),
                method: 'GET',
                success: function(data) {
                    var permissionsContainer = $('#rolePermissions');
                    permissionsContainer.empty(); // Limpiar contenedor de permisos
                    $('#roleName').val(data.name);
                    $('#EditarNombreAdministrador').html(data.name);
                    if(data.name=="ADMINISTRADOR"){
                        $('#roleName').prop('disabled', true);
                    }else{
                        $('#roleName').prop('disabled', false);
                    }
                    var checks="";
                    Object.entries(data.available_permissions).forEach(function(permission) {
                        checks+='<div class="col-3">';
                            if(permission[1].length>1){
                                checks+='<input onchange="MostrarInput(this,\'collapse'+permission[1][0]['id']+'\')" type="checkbox" name="permissions[]" id="permission_'+ permission[1][0]['id'] +'" value="'+permission[1][0]['id']+'" class="form-check-input sub-permission" data-parent="permission_'+permission[1][0]['id'] +'">'+
                                                    '<label for="permission_'+ permission[1][0]['id'] +'" class="form-check-label">'+
                                                        permission[1][0]['name'] +
                                                    '</label><div class="collapse collapse-permiso" id="collapse'+permission[1][0]['id']+'" style="display: block;">'+
                                                    '<hr class="m-0 p-0">';
                                BanderaContador=0;
                                permission[1].forEach(function(unico){
                                    if(BanderaContador>0){
                                        checks+='<div class="col-12 mx-3">'+
                                                    '<input type="checkbox" name="permissions[]" id="permission_'+ unico.id  +'" value="'+unico.id +'" class="form-check-input sub-permission" data-parent="permission_'+unico.id+'">'+
                                                    '<label for="permission_'+unico.id +'" class="form-check-label">'+
                                                                    unico.name +
                                                    '</label></div>';
                                    }
                                    BanderaContador++;
                                });
                                checks+='<hr class="m-1 p-0"></div>';
                            }else{
                                checks+='<input type="checkbox" name="permissions[]" id="permission_'+ permission[1][0]['id'] +'" value="'+permission[1][0]['id']+'" class="form-check-input sub-permission" data-parent="permission_'+permission[1][0]['id']+'">'+
                                                    '<label for="permission_'+ permission[1][0]['id'] +'" class="form-check-label">'+
                                                        permission[1][0]['name']+
                                                    '</label>';
                            }
                        checks+='</div>';
                    });
                    permissionsContainer.html(checks);
                    PermisosRol=data.permissions;
                    for(i=0;i<PermisosRol.length;i++){
                        $('#permission_'+PermisosRol[i]).prop('checked', true);
                    }
                },
                error: function(xhr, status, errorjs) {
                    error('Error', 'Hubo un error al cargar los datos del rol \n'+errorjs);
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
                    let allCheckboxes = document.querySelectorAll("input[name='permissions[]']");
                    var items = document.querySelectorAll('.collapse-permiso');
                                items.forEach(item => {
                                    $(item).addClass('show');
                                    $(item).slideDown();  
                                    // Solo abrir si está cerrado
                                        //new bootstrap.Collapse(item, {toggle: false}).show();
                                });
                }else{
                    permisos.forEach(checkbox => {
                        checkbox.checked=false;
                    });
                }
            });
        });
        function MostrarInput(checkbox,idcollapse){
            var collapseElement = $('#'+idcollapse);
            if(checkbox.checked){
                collapseElement.show();
            }else{
                collapseElement.hide();
                collapseElement.find('input[type="checkbox"]').prop('checked', false);
            }
        }
        function MostrarPermisos(NumRole){
            if ($('#'+NumRole).hasClass('Permisos-Colapse')) {
                $('#'+NumRole).removeClass('Permisos-Colapse');
                $('#Btn'+NumRole).html('Ver menos');
            } else {
                $('#'+NumRole).addClass('Permisos-Colapse');
                $('#Btn'+NumRole).html('Ver más...');
            }
        }
        @if (session('status'))
                success('Guardado corectamente!','{{ session('status')}}')
        @endif
        @if (session('success'))
            success('Guardado corectamente!','{{ session('success')}}')
        @endif
        @if (session('error'))
            error('Guardado corectamente!','{{ session('error')}}')
        @endif
    </script>
@endsection
