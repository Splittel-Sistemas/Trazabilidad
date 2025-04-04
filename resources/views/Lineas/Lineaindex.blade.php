@extends('layouts.menu2') 
@section('title', 'Lineas') 
@section('styles')
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
    <h4 class="mb-2 text-1100">Lineas</h4>
    </div>
</div>
<!-- Contenido principal -->
<div class="container my-4">
    @if(Auth::user()->hasPermission("Crear Linea"))
        <a href="{{ route('linea.create') }}" class="btn btn-outline-info mb-3" data-bs-toggle="modal" data-bs-target="#crearModal">Agregar Linea</a>
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
        <div class="search-box mb-3 mx-auto">
            <form class="position-relative d-flex align-items-center" data-bs-toggle="search" data-bs-display="static">
                <input class="form-control search-input search form-control-sm rounded-pill pe-5" 
                       type="search" 
                       placeholder="Buscar" 
                       aria-label="Buscar">
                <svg class="position-absolute end-0 me-3 search-box-icon" width="16" height="16" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="M8 0C3.58 0 0 3.58 0 8s3.58 8 8 8 8-3.58 8-8S12.42 0 8 0zm0 14C4.69 14 2 11.31 2 8s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"></path>
                </svg>
            </form>
        </div>
        <div class=" table-responsive">
                <table class="table table-striped table-sm fs--1 mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="sort border-top text-center ps-3" data-sort="numero">Número de línea</th>
                            <th class="sort border-top ps-3" data-sort="nombre">Nombre</th>
                            <th class="sort border-top ps-3" data-sort="descripcion">Descripción</th>
                            <th class="sort border-top ps-3" data-sort="activacion">Activar</th>    
                            <th class="sort border-top ps-3" data-sort="activacion">Color</th>
                            <th class="sort border-top text-center ps-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @foreach ($linea as $linea)
                        <tr>
                            <td class="align-middle text-center numero ps-3">{{ $linea->NumeroLinea }}</td>
                            <td class="align-middle nombre ps-3">{{ $linea->Nombre }}</td>
                            <td class="align-middle descripcion ps-3">{{ $linea->Descripcion }}</td>
                            <td class="align-center estatus ps-8">
                                @if(Auth::user()->hasPermission("Activar/Desactivar Linea"))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input toggle-status" style="transform:scale(1.5);" 
                                            type="checkbox" 
                                            id="ActivarUsuario{{ $linea->id }}" 
                                            data-id="{{ $linea->id }}" 
                                            data-active="{{ $linea->active ? '1' : '0' }}" 
                                            {{ $linea->active ? 'checked' : '' }} 
                                            onclick="DesactivarLinea(this)">
                                    </div>
                                @endif
                            </td>
                            <td><div class="p-3" style="background: {{ $linea->ColorLinea }};"></div></td>
                                <td class="text-center pe-0">
                                    @if(Auth::user()->hasPermission("Editar Linea"))
                                        <button type="button" class="btn btn-outline-warning btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#lineaModal" 
                                            data-id="{{ $linea->id }}">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                    @endif
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
    <div class="modal fade" id="lineaModal" tabindex="-1" role="dialog" aria-labelledby="lineaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="userModalLabel">Editar Linea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="userEditForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="Nombre">Nombre</label>
                                    <input type="text" name="Nombre" id="Nombre" class="form-control form-control-sm" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="NumeroLinea">Número de Línea</label>
                                    <input type="text" name="NumeroLinea" id="NumeroLinea" class="form-control form-control-sm" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="NumeroLinea">Color</label>
                                    <input type="color" id="ColorLinea" name="ColorLinea"  class="form-control form-control-color" title="Selecciona un color">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Descripcion">Descripción</label>
                            <textarea name="Descripcion" id="Descripcion" class="form-control form-control-sm"></textarea>
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
    <div class="modal fade" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="crearModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="crearModalLabel">Crear L&iacute;nea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancelar"></button>
                </div>
                <form id="createLineForm" class="p-3 rounded bg-white">
                    @csrf
                    <div class="row mb-1">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="Nombre">Nombre</label>
                                <input type="text" name="Nombre" id="Nombre" class="form-control form-control-sm" placeholder="Ingrese el nombre" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="NumeroLinea">Número de Línea</label>
                                <input type="text" name="NumeroLinea" id="NumeroLinea" class="form-control form-control-sm" placeholder="Ingrese el número de línea" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="NumeroLinea">Color</label>
                                <input type="color" id="ColorLinea" name="ColorLinea"  class="form-control form-control-color" title="Selecciona un color">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Descripcion">Descripción</label>
                                <textarea name="Descripcion" id="Descripcion" class="form-control form-control-sm" placeholder="Ingrese la descripción" required></textarea>
                            </div>
                        </div>
                    </div>
    
                    <!-- Verifica si hay un mensaje de error -->
                    @if(session('error'))
                        <div class="alert alert-danger mt-2" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-primary btn-lg">
                           Guardar
                        </button>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        var lineaId;
        function Editarlinea(linea) {
            lineaId = $(linea).data('id');  
            var url = "{{ route('linea.show', ['id' => '__lineaId__']) }}".replace('__lineaId__', lineaId);
            $('#Nombre').val('');
            $('#Descripcion').val('');
            $('#NumeroLinea').val('');
            $('#ColorLinea').val('');
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    console.log(response);
                    $('#Nombre').val(response.Nombre);
                    $('#Descripcion').val(response.Descripcion);
                    $('#NumeroLinea').val(response.NumeroLinea);
                    $('#ColorLinea').val(response.ColorLinea);
                    $('#userEditForm').attr('action', '{{ route('linea.update', 'lineaId') }}'.replace('lineaId', lineaId));
                    $('#lineaModal').modal('show');
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo cargar la información del servidor', 'error');
                }
            });
        }
        // Asociar el evento click a los botones de edición
        $(document).on('click', 'button[data-id]', function() {
            Editarlinea(this);  
        });
        // Manejo del formulario de actualización
        $(document).on('submit', '#userEditForm', function(event) {
            event.preventDefault();
            var actionUrl = '{{ route('linea.update', 'lineaId') }}'.replace('lineaId', lineaId);
            console.log('Acción URL:', actionUrl);  
            $('#userEditForm').attr('action', actionUrl);
            var form = $(this);
            var formData = form.serialize();
            $.ajax({
                url: actionUrl,
                method: 'PUT',
                data: formData + '&_method=PUT',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', response.message, 'success').then(() => {
                            $('#lineaModal').modal('hide');
                            location.reload(); 
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Ocurrió un problema', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('Estado:', status);
                    console.log('Respuesta:', xhr.responseText);
                    Swal.fire('Error', 'Hubo un problema en el servidor.', 'error');
                }
            });
        });
        //ACTIVAR Y DESACTIVAR LINEA
        window.DesactivarLinea = function(item) {
            var checkbox = $(item);
            var id = checkbox.data('id');  
            var isActive = checkbox.prop('checked');
            var url = isActive ? "{{ route('lineas.activar') }}" : "{{ route('lineas.desactivar') }}";
            console.log("id:", id);
            console.log("isActive:", isActive);
            console.log("url:", url);
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    id: id,  
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log("Respuesta del servidor:", response);
                    if (response.success) {
                        checkbox.prop('checked', isActive);
                    } else {
                        alert('Error: ' + response.message);
                        checkbox.prop('checked', !isActive);
                    }
                },
                error: function (xhr, status, error) {
                    console.log("Error en la solicitud AJAX:", error);
                    checkbox.prop('checked', !isActive);
                    alert('Hubo un error al cambiar el estado.');
                }
            });
        };
    });
    $(document).ready(function() {
        $('#createLineForm').on('submit', function(e) {
            e.preventDefault(); 
            var formData = $(this).serialize();
            Swal.fire({
                title: 'Enviando...',
                text: 'Por favor espere.',
                icon: 'info',
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Enviar los datos por AJAX
            $.ajax({
                url: '{{ route('linea.store') }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Si la respuesta es exitosa, mostrar mensaje
                    Swal.fire({
                        title: 'Éxito',
                        text: 'La línea fue registrada correctamente.',
                        icon: 'success',
                        confirmButtonText: 'Cerrar'
                    });
                    // Cerrar el modal y limpiar el formulario
                    $('#crearModal').modal('hide');
                    $('#createLineForm')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema al registrar la línea. Inténtelo nuevamente.',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                }
            });
        });
    });
</script>