@extends('layouts.menu2')

@section('title', 'Empacado')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>  

</style>
@endsection
@section('content')
<div class="breadcrumbs mb-3">
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Empacado</h4>
        </div>
    </div>
</div>
<div class="col-6">
    <div class="card shadow-sm">
        <div class="card-body row" id="filtro">
            <label for="CodigoEscaner" class="col-form-label col-sm-12 pt-0">Proceso <span class="text-muted"></span></label>
            <div class="col-8">
                    <div class="form-check form-check-inline ">
                        <input class="form-check-input" type="radio" name="TipoProceso" id="Iniciar" >
                        <label class="form-check-label" for="Iniciar">
                        Entrada
                        </label>
                    </div>
            </div>
            <hr>
            <form id="filtroForm" method="post" class="form-horizontal row mt-0 needs-validation" novalidate="">
                <div class="col-8" id="CodigoDiv">
                    <div class="">
                        <label for="CodigoEscaner">C&oacute;digo <span class="text-muted">&#40;Escanea o Ingresa manual&#41;</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm"  id="CodigoEscaner" aria-describedby="CodigoEscanerHelp" placeholder="Escánea o ingresa manualmente.">
                            <div class="invalid-feedback" id="error_CodigoEscaner"></div>
                        </div>
                        <div class=" mt-1 list-group-sm" id="CodigoEscanerSuministro">
                        </div>
                    </div>
                </div>
                <div class="col-4" id="CantidadDiv" style="display: none">
                    <div class="form-group">
                        <label for="Cantidad">Cantidad</label>
                        <input type="text" class="form-control form-control-sm" id="Cantidad" aria-describedby="Cantidad" value="1" placeholder="Ingresa cantidad recibida.">
                        <div class="invalid-feedback" id="error_Cantidad"></div>
                    </div>
                </div>
                <div class="col-6 mt-2" id="RetrabajoDiv" style="display: none">
                    <div class="form-check">
                        <input class="form-check-input" id="Retrabajo" type="checkbox" />
                        <label class="form-check-label" for="Retrabajo">Enviar a retrabajo</label>
                    </div>
                </div>
                <div class="col-6 mt-2" id="IniciarBtn" style="display: none">
                    <button class="btn btn-primary btn-sm float-end" type="button" id="btnEscanear"><i class="fa fa-play"></i> Iniciar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div style="height: 30px;"></div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive card">
            <table id="EmpacadoTable" class="table table-sm">
                <thead>
                    <tr class="bg-light">
                        <th  class="text-center">Orden de Venta</th>
                        <th  class="text-center">Orden Fabricación</th>
                        <th  class="text-center">Total de Piezas</th>
                        <th  class="text-center">Cantidad Registrada</th>
                        <th  class="text-center">Fecha Entrega</th>
                        <th  class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody id="EmpacadoTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
$(document).ready(function () {
    cargarTablaEmpacado();

    setInterval(cargarTablaEmpacado, 30000);

    function cargarTablaEmpacado() {
        $.ajax({
            url: '{{ route("tabla.principal") }}',
            type: "GET",
            dataType: "json",
            success: function (data) {
                let tabla = $('#EmpacadoTable');
                
                if ($.fn.DataTable.isDataTable(tabla)) {
                    tabla.DataTable().destroy();
                }

                let tbody = $("#EmpacadoTableBody");
                tbody.empty();

                data.forEach((item) => {
                    let fila = `
                         <tr>
                            <td class="text-center">${item.OrdenVenta}</td>
                            <td class="text-center">${item.OrdenFabricacion}</td>
                            <td class="text-center">${item.CantidadTotal}</td>
                            <td class="text-center">${item.Cantidad}</td>
                            <td class="text-center">${item.FechaEntrega}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger">Finalizar</button>
                            </td>
                        </tr>
                    `;
                    tbody.append(fila);
                });

                DataTable('EmpacadoTable', true);
            },
            error: function () {
                console.log("Error al cargar los datos de la tabla.");
            },
        });
    }

    function DataTable(tabla, busqueda) {
        $('#' + tabla).DataTable({
            "pageLength": 10,
            "lengthChange": false,
            "paging": true,
            "searching": busqueda,
            "ordering": true,
            "info": true,
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                "search": "Buscar",
                
            },
            "initComplete": function(settings, json) {
                $('#' + tabla).css('font-size', '0.7rem');
            }
        });
    }
});

</script>
@endsection
