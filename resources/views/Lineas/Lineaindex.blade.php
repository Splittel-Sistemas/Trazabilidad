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
    <a href="{{ route('linea.create') }}" class="btn btn-outline-info mb-3" data-bs-toggle="modal" data-bs-target="#crearModal">Agregar Linea</a>
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
    <div class="card p-4" style="display:block;" id="tableExample3">
        <div class="search-box mb-3 mx-auto">
            <form class="position-relative d-flex align-items-center" data-bs-toggle="search" data-bs-display="static">
                <input class="form-control search-input search form-control-sm rounded-pill pe-5" 
                       type="search" 
                       placeholder="Buscar" 
                       aria-label="Buscar">
                <svg class="position-absolute end-0 me-3 search-box-icon" width="16" height="16" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="M11.742 10.742a7.5 7.5 0 1 0-1.42 1.42l3.56 3.56a1 1 0 0 0 1.42-1.42l-3.56-3.56zM8 12a4 4 0 1 1 4-4 4 4 0 0 1-4 4z"></path>
                </svg>
            </form>
        </div>
        <div class=" table-responsive">
                <table class="table table-striped table-sm fs--1 mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="sort border-top ps-3" data-sort="nombre">Nombre</th>
                            <th class="sort border-top ps-3" data-sort="numero">Número de línea</th>
                            <th class="sort border-top ps-3" data-sort="descripcion">Descripción</th>
                            <th class="sort border-top ps-3" data-sort="activacion">Activar</th>    
                            <th class="sort border-top text-center ps-3">Acción</th>
                        </tr>
                        
                    </thead>
                    <tbody class="list"> </tbody>
                </table>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <span class="d-none d-sm-inline-block" data-list="{&quot;valueNames&quot;:[&quot;apellido&quot;,&quot;nombre&quot;,&quot;numero&quot;,&quot;descripcion&quot;,&quot;activacion&quot;],&quot;page&quot;:5,&quot;pagination&quot;:true}">
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
    <div class="modal fade" id="lineaModal" tabindex="-1" role="dialog" aria-labelledby="lineaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="userModalLabel">Editar Linea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="lineaEditForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Nombre">Nombre</label>
                                    <input type="text" name="Nombre" id="Nombre" class="form-control form-control-sm" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NumeroLinea">Número de Línea</label>
                                    <input type="text" name="NumeroLinea" id="NumeroLinea" class="form-control form-control-sm" required>
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
    <div class="modal fade" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="crearModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="crearModalLabel">Crear Linea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('linea.store') }}" method="POST" class="p-3 rounded bg-white">
                    @csrf
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Nombre">Nombre</label>
                                <input type="text" name="Nombre" id="Nombre" class="form-control form-control-sm" placeholder="Ingrese el nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="NumeroLinea">Número de Línea</label>
                                <input type="text" name="NumeroLinea" id="NumeroLinea" class="form-control form-control-sm" placeholder="Ingrese el número de línea" required>
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
        
                    <div class="d-flex justify-content-center mt-3">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-lg transition-all hover:bg-success hover:text-white">
                            Registrar
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
$(document).ready(function () {
    $.ajax({
        url: '{{ route("lineas.datos") }}',
        type: "GET",
        dataType: "json",
        success: function (data) {
            let tbody = $(".list");  
            tbody.empty();

            if (data.length === 0) {
                tbody.append('<tr><td colspan="5" class="text-center">No hay datos disponibles</td></tr>');
            } else {
                $.each(data, function (index, item) {
                    console.log('item.active:', item.active);
                    let fila = `
                        <tr>
                            <td class="ps-3 nombre">${item.Nombre}</td>
                            <td class="ps-3 numero">${item.NumeroLinea}</td>
                            <td class="ps-3 descripcion">${item.Descripcion}</td>
                            <td class="align-center estatus ps-8">
                                <div class="form-check form-switch ${item.active == 1 ? 'checkbox-activo' : ''}">
                                    <input class="form-check-input toggle-status" 
                                        style="transform:scale(1.5);" 
                                        type="checkbox" 
                                        id="ActivarLinea${item.NumeroLinea}" 
                                        onclick="DesactivarLinea(this);" 
                                        data-id="${item.NumeroLinea}" 
                                        ${item.active == 1 ? 'checked' : ''}>
                                </div>
                            </td>
                            <td class="text-center">
                               <button class="btn btn-outline-warning btn-sm btn-editar"
                                        data-id="${item.id}" 
                                        data-nombre="${item.Nombre}" 
                                        data-numero="${item.NumeroLinea}" 
                                        data-descripcion="${item.Descripcion}">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(fila);
                });
            }

            if (tbody.children().length > 0) {

                var options = {
                    valueNames: ['nombre', 'numero', 'descripcion', 'activacion'],
                    page: 5,
                    pagination: true
                };
                var userList = new List('contenedor-lista', options); 
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los datos:", error);
        }
    });
  
    window.DesactivarLinea = function(item) {
        var checkbox = $(item);
        var NumeroLinea = checkbox.data('id');
        var isActive = checkbox.prop('checked'); 
        var url = isActive ? "{{ route('lineas.activar') }}" : "{{ route('lineas.desactivar') }}";
        console.log("NumeroLinea:", NumeroLinea); 
        console.log("isActive:", isActive); 
        console.log("url:", url); 
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                NumeroLinea: NumeroLinea, 
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
$(document).on("click", ".btn-editar", function () {
    let id = $(this).data("id");
    let nombre = $(this).data("nombre");
    let numero = $(this).data("numero");
    let descripcion = $(this).data("descripcion");
    $("#lineaModalLabel").text("Editar Línea");
    let actionUrl = "{{ route('linea.update', ':numero') }}".replace(':numero', numero);
    $("#lineaEditForm").attr("action", actionUrl);
    $("#lineaEditForm").find("input[name='_method']").val("PUT");
    $("#Nombre").val(nombre);
    $("#NumeroLinea").val(numero);
    $("#Descripcion").val(descripcion);
    $("#lineaModal").modal("show");
});
$(document).on("submit", "#lineaEditForm", function (event) {
    event.preventDefault(); 
    let form = $(this);
    let formData = new FormData(this);
    fetch(form.attr("action"), {
        method: "POST", 
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest", 
            "X-CSRF-TOKEN": $('input[name="_token"]').val()
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'Línea actualizada correctamente.',
                showConfirmButton: false,
                timer: 2000
            });

            $("#lineaModal").modal("hide"); 
            setTimeout(() => location.reload(), 2000); 
        } else {
            let errorMessage = Object.values(data.errors)[0][0]; 
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
            });
        }
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error inesperado',
            text: 'Ocurrió un problema. Intenta de nuevo.',
        });
    });
});
</script>
