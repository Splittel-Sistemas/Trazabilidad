@extends('layouts.menu2')

@section('title', 'Traslados de Almacenes')

@section('content')
<div class="container mt-5 bg-white p-5">
    <h1 class="mb-4 text-center">Traslados de Almacenes</h1>

    <div class="row">
        <div class="col-md-12 mb-3">
            <a href="{{ route('traslados.create') }}" class="btn btn-sm btn-primary">Crear</a>
        </div>
        <div class="col-md-12">
            <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Pendientes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Completados</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <h4 class="text-center">Traslados Pendientes</h4>
                    <table class="table table-striped table-bordered table-sm text-center" id="tablaPendientes">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Codigo</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>Alta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trasladosPendientes as $indice => $traslado)
                            <tr>
                                <td>{{ $indice + 1 }}</td>
                                <td>T{{ str_pad($traslado->id, 6, "0", STR_PAD_LEFT) }}</td>
                                <td>
                                    @switch($traslado->estado)
                                    @case('Generado')
                                    <span class="badge text-bg-primary">{{ $traslado->estado }}</span>
                                    @break

                                    @case('Parcial')
                                    <span class="badge text-bg-warning">{{ $traslado->estado }}</span>
                                    @break

                                    @case('Recibido')
                                    <span class="badge text-bg-success">{{ $traslado->estado }}</span>
                                    @break

                                    @case('Cancelado')
                                    <span class="badge text-bg-danger">{{ $traslado->estado }}</span>
                                    @break
                                    @endswitch
                                </td>
                                <td>{{ $traslado->usuario }}</td>
                                <td>{{ $traslado->alta }}</td>
                                <td>
                                    <a href="{{ route('traslados.show', $traslado->id) }}" class="btn btn-sm btn-primary">Detalles</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <h4 class="text-center">Traslados Terminados</h4>

                    <form class="row" style="margin-bottom: 2rem;" id="filtroFechas">
                        <div class="col-md-2">
                            <label for="finicio">Fecha Inicio</label>
                            <input type="date" name="inicio" id="finicio" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label for="ffin">Fecha Fin</label>
                            <input type="date" name="fin" id="ffin" class="form-control">
                        </div>

                        <div class="col-md-2" style="display: flex;align-content: space-between;justify-content: flex-start;align-items: flex-end;">
                            <input type="submit" value="Filtrar" class="btn btn-sm btn-success" style="max-height: 40px;">
                        </div>
                    </form>

                    <table class="table table-striped table-bordered table-sm text-center" id="tablaCompletados">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Codigo</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>Alta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trasladosCompletos as $indice => $traslado)
                            <tr>
                                <td>{{ $indice + 1 }}</td>
                                <td>T{{ str_pad($traslado->id, 6, "0", STR_PAD_LEFT) }}</td>
                                <td>
                                    @switch($traslado->estado)
                                    @case('Generado')
                                    <span class="badge text-bg-primary">{{ $traslado->estado }}</span>
                                    @break

                                    @case('Parcial')
                                    <span class="badge text-bg-warning">{{ $traslado->estado }}</span>
                                    @break

                                    @case('Recibido')
                                    <span class="badge text-bg-success">{{ $traslado->estado }}</span>
                                    @break

                                    @case('Cancelado')
                                    <span class="badge text-bg-danger">{{ $traslado->estado }}</span>
                                    @break
                                    @endswitch
                                </td>
                                <td>{{ $traslado->usuario }}</td>
                                <td>{{ $traslado->alta }}</td>
                                <td>
                                    <a href="{{ route('traslados.show', $traslado->id) }}" class="btn btn-sm btn-primary">Detalles</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let tablaCompletados;
    let tablaPendientes;
    window.addEventListener('DOMContentLoaded', function() {
        tablaCompletados = $('#tablaCompletados').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
        });

        tablaPendientes = $('#tablaPendientes').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
        });
    });

    const formulario = document.getElementById('filtroFechas');
    const fechaInicio = document.getElementById('finicio');
    const fechaFin = document.getElementById('ffin');

    formulario.addEventListener('submit', function(event) {
        event.preventDefault();

        if (fechaInicio.value.trim() == '') {
            Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            }).fire({
                icon: "warning",
                title: "No se a establecido una fehca de inicio para el filtro"
            });

            return;
        }

        if (fechaFin.value.trim() == '') {
            Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            }).fire({
                icon: "warning",
                title: "No se a establecido una fehca de fin para el filtro"
            });

            return;
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('traslados.dateFilter') }}",
            data: {
                'fechaInicio': fechaInicio.value,
                'fechaFin': fechaFin.value
            },
            cache: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                console.info(data);
                    tablaCompletados.clear();

                    tablaCompletados.rows.add(data);

                    tablaCompletados.draw();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    title: "Advertencia!",
                    text: `${'Error: ' + errorThrown + ' - ' + jqXHR.responseText}`,
                    icon: "error"
                });
            }
        });
    });
</script>
@endsection