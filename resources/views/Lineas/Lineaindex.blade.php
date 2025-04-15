@extends('layouts.menu2') 
@section('title', 'Líneas') 
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
    <!--Modal Editar Linea-->
    <div class="modal fade" id="lineaModal" tabindex="-1" role="dialog" aria-labelledby="lineaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="userModalLabel">Editar L&iacute;nea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="userEditForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row mb-1">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="Nombre">Nombre</label>
                                    <input type="text" name="NombreE" id="NombreE" class="form-control form-control-sm">
                                    <small class="text-danger" id="Error_NombreE" style="display: none"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="NumeroLinea">Número de Línea</label>
                                    <input type="text" name="NumeroLineaE" id="NumeroLineaE" class="form-control form-control-sm">
                                    <small class="text-danger" id="Error_NumeroLineaE" style="display: none"></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="NumeroLinea">Color</label>
                                    <input type="color" id="ColorLineaE" name="ColorLineaE"  class="form-control form-control-color" title="Selecciona un color">
                                    <small class="text-danger" id="Error_ColorLineaE" style="display: none"></small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group my-1">
                            <label for="Descripcion">Descripción</label>
                            <textarea name="DescripcionE" id="DescripcionE" class="form-control form-control-sm"></textarea>
                            <small class="text-danger" id="Error_DescripcionE" style="display: none"></small>
                        </div>  
                        <div class="form-group my-1">
                            <label for="Descripcion">Selecciona &Aacute;reas</label><br>
                            <small class="mb-1">Presiona la tecla Ctrl o Shift para seleccionar m&aacute;s de una opci&oacute;n.</small>
                            <select class="form-select form-select-sm" aria-label=".form-select-sm example" size="4" name="AreasPosiblesE[]" id="AreasPosiblesE" multiple>
                                @foreach($Areas as $Area)
                                        <option value="{{$Area->id}}">{{$Area->nombre}}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="Error_AreasPosiblesE" style="display: none"></small>
                        </div>                   
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button  id="BtnEditarLinea" class="btn btn-sm btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--Modal Crear Linea-->
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
                                <input type="text" name="Nombre" id="Nombre" class="form-control form-control-sm" placeholder="Ingrese el nombre" >
                                <small class="text-danger" id="Error_Nombre" style="display: none"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="NumeroLinea">Número de Línea</label>
                                <input type="text" name="NumeroLinea" id="NumeroLinea" class="form-control form-control-sm" placeholder="Ingrese el número de línea" >
                                <small class="text-danger" id="Error_NumeroLinea" style="display: none"></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="NumeroLinea">Color</label>
                                <input type="color" id="ColorLinea" name="ColorLinea"  class="form-control form-control-color" title="Selecciona un color">
                                <small class="text-danger" id="Error_ColorLinea" style="display: none"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Descripcion">Descripción</label>
                                <textarea name="Descripcion" id="Descripcion" class="form-control form-control-sm" placeholder="Ingrese la descripción" ></textarea>
                                <small class="text-danger" id="Error_Descripcion" style="display: none"></small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group my-1">
                        <label for="Descripcion">Selecciona &Aacute;reas</label><br>
                        <small class="mb-1">Presiona la tecla Ctrl o Shift para seleccionar m&aacute;s de una opci&oacute;n.</small>
                        <select class="form-select form-select-sm" aria-label=".form-select-sm example" size="4" name="AreasPosibles[]" id="AreasPosiblesCrear" multiple>
                            {{--<option selected="" disabled>Selecciona Areas</option>--}}
                            @foreach($Areas as $Area)
                                    <option value="{{$Area->id}}">{{$Area->nombre}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="Error_AreasPosiblesCrear" style="display: none"></small>
                    </div> 
                    <!-- Verifica si hay un mensaje de error -->
                    @if(session('error'))
                        <div class="alert alert-danger mt-2" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button id="BtnCrearLinea" class="btn btn-sm btn-primary">
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
            $('#userEditForm')[0].reset();
            $('#AreasPosiblesE').prop('selectedIndex', -1);
            $('#Error_NombreE').hide();
            $('#Error_NumeroLineaE').hide();
            $('#Error_ColorLineaE').hide();
            $('#Error_DescripcionE').hide();
            $('#Error_AreasPosiblesE').hide();
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
                    $('#NombreE').val(response.Nombre);
                    $('#DescripcionE').val(response.Descripcion);
                    $('#NumeroLineaE').val(response.NumeroLinea);
                    $('#ColorLineaE').val(response.ColorLinea);
                    $('#userEditForm').attr('action', '{{ route('linea.update', 'lineaId') }}'.replace('lineaId', lineaId));
                    $('#lineaModal').modal('show');
                    if(!(response.AreasPosibles==null || response.AreasPosibles=="")){
                        AreasPosibles=(response.AreasPosibles).split(',').map(Number);
                        const SelectAreasPosiblesE = document.getElementById('AreasPosiblesE');
                        AreasPosibles.forEach(valor => {
                            const opcion = SelectAreasPosiblesE.querySelector(`option[value="${valor}"]`);
                            if (opcion) opcion.selected = true;
                        });
                    }
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
        $('#BtnEditarLinea').on('click', function() {
            event.preventDefault();
            Error_NombreE = $('#Error_NombreE');
            Error_NumeroLineaE = $('#Error_NumeroLineaE');
            Error_ColorLineaE = $('#Error_ColorLineaE');
            Error_DescripcionE = $('#Error_DescripcionE');
            Error_AreasPosiblesE = $('#Error_AreasPosiblesE');

            BanderaEnvioE=0;
            NombreE = $('#NombreE').val();
            if(!CadenaVacia(NombreE)){Error_NombreE.html('');Error_NombreE.hide();}else{Error_NombreE.html('*Campo Requerido');Error_NombreE.show();BanderaEnvioE=1;}
            NumeroLineaE = $('#NumeroLineaE').val();
            if(!CadenaVacia(NumeroLineaE)){Error_NumeroLineaE.html('');Error_NumeroLineaE.hide();}else{Error_NumeroLineaE.html('*Campo Requerido');Error_NumeroLineaE.show();BanderaEnvioE=1;}
            ColorLineaE = $('#ColorLineaE').val();
            if(!CadenaVacia(ColorLineaE)){Error_ColorLineaE.html('');Error_ColorLineaE.hide();}else{Error_ColorLineaE.html('*Campo Requerido');Error_ColorLineaE.show();BanderaEnvioE=1;}
            DescripcionE = $('#DescripcionE').val();
            if(!CadenaVacia(DescripcionE)){Error_DescripcionE.html('');Error_DescripcionE.hide();}else{Error_DescripcionE.html('*Campo Requerido');Error_DescripcionE.show();BanderaEnvioE=1;}
            AreasPosiblesE = $('#AreasPosiblesE').val();
            if(!CadenaVacia(AreasPosiblesE)){Error_AreasPosiblesE.html('');Error_AreasPosiblesE.hide();}else{Error_AreasPosiblesE.html('*Selecciona minimo una opción');Error_AreasPosiblesE.show();BanderaEnvioE=1;}
            if(BanderaEnvioE==1){
                return 0;
            }

            NombreE = $('#NombreE').val();
            NumeroLineaE = $('#NumeroLineaE').val();
            ColorLineaE = $('#ColorLineaE').val();
            DescripcionE = $('#DescripcionE').val();
            AreasPosiblesE = $('#AreasPosiblesE').val();
            lineaId = lineaId;
            enviando();

            $.ajax({
                url: '{{route('linea.update')}}' ,
                method: 'PUT',
                data:{
                    NombreE : NombreE,
                    NumeroLineaE : NumeroLineaE,
                    ColorLineaE : ColorLineaE,
                    DescripcionE : DescripcionE,
                    AreasPosiblesE : AreasPosiblesE,
                    lineaId : lineaId,
                },
                success: function(response) {
                    if (response.status=='success') {
                        success('Línea '+response.numlinea+" ",response.message);
                        $('#lineaModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 500); 
                    } else {
                        if(response.status=='LineaExiste'){
                            Error_NumeroLineaE.show();
                            Error_NumeroLineaE.html('*El número de línea '+response.numlinea+' ya existe')
                            error('Error al Guardar','El numero de Línea tiene que ser único, no se puede repetir');
                        }else{
                            error('Ocurrio un Error',response.message);
                        }
                    }
                },
                error: function(xhr, status) {
                    error('Ocurrio un Error',response.message);
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
        // Limpia los campos del formulario dentro del modal
        $('#crearModal').on('show.bs.modal', function () {
            $('#createLineForm')[0].reset();
            $('#AreasPosibles').prop('selectedIndex', -1);
            $('#Error_Nombre').hide();
            $('#Error_NumeroLinea').hide();
            $('#Error_ColorLinea').hide();
            $('#Error_Descripcion').hide();
            $('#Error_AreasPosiblesCrear').hide();
        });
        $('#BtnCrearLinea').on('click', function(e) {
            e.preventDefault();
            BanderaEnvio=0;
            Error_Nombre = $('#Error_Nombre');
            Error_NumeroLinea = $('#Error_NumeroLinea');
            Error_ColorLinea = $('#Error_ColorLinea');
            Error_Descripcion = $('#Error_Descripcion');
            Error_AreasPosiblesCrear = $('#Error_AreasPosiblesCrear');

            Nombre = $('#Nombre').val();
            if(!CadenaVacia(Nombre)){Error_Nombre.html('');Error_Nombre.hide();}else{Error_Nombre.html('*Campo Requerido');Error_Nombre.show();BanderaEnvio=1;}
            NumeroLinea = $('#NumeroLinea').val();
            if(!CadenaVacia(NumeroLinea)){Error_NumeroLinea.html('');Error_NumeroLinea.hide();}else{Error_NumeroLinea.html('*Campo Requerido');Error_NumeroLinea.show();BanderaEnvio=1;}
            ColorLinea = $('#ColorLinea').val();
            if(!CadenaVacia(ColorLinea)){Error_ColorLinea.html('');Error_ColorLinea.hide();}else{Error_ColorLinea.html('*Campo Requerido');Error_ColorLinea.show();BanderaEnvio=1;}
            Descripcion = $('#Descripcion').val();
            if(!CadenaVacia(Descripcion)){Error_Descripcion.html('');Error_Descripcion.hide();}else{Error_Descripcion.html('*Campo Requerido');Error_Descripcion.show();BanderaEnvio=1;}
            AreasPosiblesCrear = $('#AreasPosiblesCrear').val();
            if(!CadenaVacia(AreasPosiblesCrear)){Error_AreasPosiblesCrear.html('');Error_AreasPosiblesCrear.hide();}else{Error_AreasPosiblesCrear.html('*Selecciona minimo una opción');Error_AreasPosiblesCrear.show();BanderaEnvio=1;}
            if(BanderaEnvio==1){
                return 0;
            }
            const formData = new FormData();
            formData.append("Nombre", $('#Nombre').val());
            formData.append("NumeroLinea", $('#NumeroLinea').val());
            formData.append("ColorLinea", $('#ColorLinea').val());
            formData.append("Descripcion", $('#Descripcion').val());
            formData.append("AreasPosiblesCrear", $('#AreasPosiblesCrear').val());
            enviando();
            // Enviar los datos por AJAX
            $.ajax({
                url: '{{ route('linea.store') }}',
                method: 'POST',
                data: {
                    Nombre:Nombre,
                    NumeroLinea:NumeroLinea,
                    ColorLinea:ColorLinea,
                    Descripcion:Descripcion,
                    AreasPosiblesCrear:AreasPosiblesCrear,
                },
                success: function(response) {
                    if(response.status=='LineaExiste')
                    {
                        Error_NumeroLinea.html('*El número de línea '+response.numlinea+' ya existe');Error_NumeroLinea.show();
                        error('Error al Guardar','El numero de Línea tiene que ser único, no se puede repetir');
                        return 0;
                    }
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