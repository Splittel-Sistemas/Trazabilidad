@extends('layouts.menu2') 

@section('title', 'Lineas') 

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
    <h4 class="mb-2 text-1100">Lineas</h4>
    </div>
</div>
<!-- Contenido principal -->
<div class="container my-4">

   
    <a href="{{ route('linea.create') }}" class="btn btn-outline-info mb-3">Agregar Linea</a>

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

    <div class="card p-4" style="display:block;" id="tableExample3" data-list="{&quot;valueNames&quot;:[&quot;apellido&quot;,&quot;nombre&quot;,&quot;email&quot;,&quot;roles&quot;,&quot;estatus&quot;],&quot;page&quot;:5,&quot;pagination&quot;:true}">
        <div class="search-box mb-3 mx-auto">
            <form class="position-relative d-flex align-items-center" data-bs-toggle="search" data-bs-display="static">
                <input class="form-control search-input search form-control-sm rounded-pill pe-5" 
                       type="search" 
                       placeholder="Buscar" 
                       aria-label="Buscar">
                <svg class="position-absolute end-0 me-3 search-box-icon" width="16" height="16" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="M500.3 443.7..."></path>
                </svg>
            </form>
            
        </div>
        <div class=" table-responsive">
                <table class="table table-striped table-sm fs--1 mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="sort border-top ps-3" data-sort="nombre">Nombre</th>
                            <th class="sort border-top ps-3" data-sort="numero">Numero de linea</th>
                            <th class="sort border-top ps-3" data-sort="descripcion">Descripcion</th>
                            <th class="sort border-top text-center  ps-3">Accion</th>
                        </tr>
                    </thead>
                    <tbody class="list"> </tbody>
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
    <div class="modal fade" id="lineaModal" tabindex="-1" role="dialog" aria-labelledby="lineaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="userModalLabel">Editar Linea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="lineaEditForm" action="{{ route('linea.update', )}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row mb-4">
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
                        <div class="form-group">
                            <label for="Descripcion">Descripción</label>
                            <input type="text" name="Descripcion" id="Descripcion" class="form-control form-control-sm" placeholder="Ingrese la descripción" required>
                            
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
</div>

@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
    $.ajax({
        url: '{{ route("lineas.datos") }}',
        type: "GET",
        dataType: "json",
        success: function (data) {
            let tbody = $(".list");
            tbody.empty();

            $.each(data, function (index, item) {
                let fila = `
                    <tr>
                        <td class="ps-3">${item.Nombre}</td>
                        <td class="ps-3">${item.NumeroLinea}</td>
                        <td class="ps-3">${item.Descripcion}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary btn-editar" 
                                    data-id="${item.id}" 
                                    data-nombre="${item.Nombre}" 
                                    data-numero="${item.NumeroLinea}" 
                                    data-descripcion="${item.Descripcion}">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar" 
                                    data-id="${item.id}" 
                                    data-nombre="${item.Nombre}">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(fila);
            });
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los datos:", error);
        }
    });


    $(document).on("click", ".btn-editar", function () {
        let id = $(this).data("id");
        let nombre = $(this).data("nombre");
        let numero = $(this).data("numero");
        let descripcion = $(this).data("descripcion");


        $("#lineaModalLabel").text("Editar Línea");
        $("#lineaEditForm").attr("action", `/linea/${id}`);
        $("#lineaEditForm").find("input[name='_method']").val("PUT"); 
        $("#Nombre").val(nombre);
        $("#NumeroLinea").val(numero);
        $("#Descripcion").val(descripcion);

 
        $("#lineaModal").modal("show");
    });
});


</script>