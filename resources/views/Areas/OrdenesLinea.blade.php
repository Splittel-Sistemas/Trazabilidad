@extends('layouts.menu2')
@section('title', 'Ordenes en Línea')
@section('styles')
<style>
    /* Positioning the toast in the top-right corner */
    #ToastGuardado {
        position: fixed; /* Fixed position */
        top: 5rem; /* Distance from the top */
        right: 20px; /* Distance from the right */
        z-index: 1050; /* Ensure it's above other content */
    }
    .number-line{
        font-size: 6rem;
        font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }
</style>
@endsection
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Ordenes en L&iacute;nea</h4>
        </div>
    </div>
    <div class="row justify-content-end">
        <div class="col-12 col-sm-8">
            <div class="row">
                <div class="col-6 col-sm-3">
                    <button class="category nav-link btn bg-white w-100 px-1 pt-4 pb-3 fs-0 active" id="tab-sale-101" data-bs-toggle="tab" data-bs-target="#sale-101" type="button" role="tab" aria-selected="true" data-vertical-category-tab="data-vertical-category-tab">
                        <span class="category-icon text-800 fs-2 fa-solid fa-chart-pie"></span>
                        <span class="d-block fs-2 fw-bolder lh-1 text-900 mt-3 mb-2">Normal</span>
                        <span class="d-block text-900 fw-normal mb-0 fs--1">N&uacute;mero de ordenes</span>
                        <span id="totalDetenidasN" class="d-block fs-2 fw-bolder lh-1 text-900 mt-3 mb-2">0</span>
                    </button>
                </div>
                <div class="col-6 col-sm-3">
                    <button style="background-color: rgb(139, 224, 252);" class="category nav-link btn w-100 px-1 pt-4 pb-3 fs-0 active" id="tab-sale-101" data-bs-toggle="tab" data-bs-target="#sale-101" type="button" role="tab" aria-selected="true" data-vertical-category-tab="data-vertical-category-tab">
                        <span class="category-icon text-800 fs-2 fa-solid fa-exclamation-circle"></span>
                        <span class="d-block fs-2 fw-bolder lh-1 text-900 mt-3 mb-2">Urgente</span>
                        <span class="d-block text-900 fw-normal mb-0 fs--1">N&uacute;mero de ordenes</span>
                        <span id="totalDetenidasU" class="d-block fs-2 fw-bolder lh-1 text-900 mt-3 mb-2">0</span>
                    </button>
                </div>
                <div class="col-6 col-sm-3">
                    <button style="background-color: rgb(252, 248, 139);" class="category nav-link btn w-100 px-1 pt-4 pb-3 fs-0 active" id="tab-sale-101" data-bs-toggle="tab" data-bs-target="#sale-101" type="button" role="tab" aria-selected="true" data-vertical-category-tab="data-vertical-category-tab">
                        <span class="category-icon text-800 fs-2 fa-solid fa-pause-circle"></span>
                        <span class="d-block fs-2 fw-bolder lh-1 text-900 mt-3 mb-2">Detenida</span>
                        <span class="d-block text-900 fw-normal mb-0 fs--1">N&uacute;mero de ordenes</span>
                        <span id="totalDetenidasD" class="d-block fs-2 fw-bolder lh-1 text-900 mt-3 mb-2">0</span>
                    </button>
                </div>
                <div class="col-6 col-sm-3">
                    <button style="background-color: rgb(255, 128, 44);color:white;" class="category nav-link btn w-100 px-1 pt-4 pb-3 fs-0 active" id="tab-sale-101" data-bs-toggle="tab" data-bs-target="#sale-101" type="button" role="tab" aria-selected="true" data-vertical-category-tab="data-vertical-category-tab">
                        <span class="category-icon text-800 fs-2 fa-solid fa-ba"></span>
                        <span class="d-block fs-2 fw-bolder lh-1 text-900 mt-3 mb-2">Prioridad</span>
                        <span class="d-block text-900 fw-normal mb-0 fs--1">N&uacute;mero de ordenes</span>
                        <span id="totalDetenidasP" class="d-block fs-2 fw-bolder lh-1 text-900 mt-3 mb-2">0</span>
                    </button>
                </div>
                <!--<div class="col-3 border bg-white">Nomal</div>
                <div class="col-3 border text-center" style="background-color: rgb(139, 224, 252);">Urgente</div>
                <div class="col-3 border text-center" style="background-color: rgb(252, 248, 139);">Detenida</div>
                <div class="col-3 border text-center" style="background-color: rgb(255, 128, 44);color:white;">Prioridad</div>-->
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="card shadow-sm">
                <div class="card-header p-2" id="filtroEntrada" style="background: grey">
                    <h3 for="CodigoEscaner" class="col-sm-12 p-0 text-center text-white"><i class="fa fa-list-ol"></i><span id="LineaDescripcion"> L&iacute;nea no definida</span></h3>
                </div>
                <div class="card-body row my-0 py-1" id="filtroSalida">
                    <div class="col-12">
                        <h3 id="LineaLinea" class="text-center text-muted">L&iacute;nea</h3>
                        <h1 id="LineaNumero" class="text-center number-line" style="color: grey">Todas</h1>
                    </div>
                </div>
            </div>
        </div>
        <div id="ContentTabla" class="col-12 mt-2" style="display: none">
            <div class="card" id="DivCointainerTableSuministro" >
            </div>
        </div>
        <div id="ContentTablaPendientes" class="col-12 mt-2">
            <div class="card" id="DivCointainerTablePendientes">
                <h4 class="text-center mt-2 p-0">Ordenes de Fabricaci&oacute;n Pendientes</h4>
                <div class="d-flex justify-content-start">
                    <div class="col-3 mx-2 Apuntarbox" id="Apuntarbox">
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text" id="inputGroup-sizing-sm">Núm. L&iacute;nea:</span>
                            <select class="form-select form-select-sm" aria-label="FiltroLinea" id="FiltroLinea">
                                <option data-color="grey" data-numero="Todas" data-nombre="Línea no definida" selected="" disabled>Selecciona L&iacute;nea</option>
                                <option data-color="grey" data-numero="Todas" data-nombre="Línea no definida" value="-1">Todas</option>
                                @foreach($Lineas as $Linea)
                                    <option data-color = "{{$Linea->ColorLinea}}" data-numero="{{$Linea->NumeroLinea}}" data-nombre="{{$Linea->Nombre}}" value="{{$Linea->id}}">{{$Linea->Nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="TablaPreparadoPendientes" class="table table-sm fs--4 mb-1">
                        <thead>
                            <tr class="bg-light">
                                <th>Orden Fabricación</th>
                                <th>Artículo</th>
                                <th>Descripción</th>
                                <th>Cantidad Completada</th>
                                <th>Cantidad Faltante</th>
                                <th>Cantidad Asignada a L&iacute;nea</th>
                                <th>Total Orden Fabricaci&oacute;n</th>
                                <th>Estatus</th>
                                <th>L&iacute;nea</th>
                                <th>L&iacute;nea</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="TablaPreparadoPendientesBody" class="list">
                            <tr>
                                <td colspan='100%' align='center'>
                                    <div class='d-flex justify-content-center align-items-center'>
                                        <div class='spinner-grow text-primary' role='status'>
                                            <span class='visually-hidden'>Loading...</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div  id="ContainerToastGuardado"></div>
@endsection
@section('scripts')
<script>
    //NUEVOS CODIGOS
    let timeout;
    $(document).ready(function() {
        $('#FiltroLinea').on('change', function() {
            var numero = $(this).find(':selected').data('numero');
            var nombre = $(this).find(':selected').data('nombre');
            var color =$(this).find(':selected').data('color');
            // Actualizamos el HTML
            $('#LineaDescripcion').html(nombre);
            $('#LineaNumero').html(numero);
            $('#filtroEntrada').css('background', color).css('color', '#fff');
            $('#LineaNumero').css('color', color);
            var val = $(this).val();
            if(!(val == "" || val == null)){
                if(val == -1) {
                    $('#Apuntarbox').addClass('Apuntarbox');
                    table.column(8).search('').draw();
                } else {
                    table.column(8).search(val).draw();
                    $('#Apuntarbox').removeClass('Apuntarbox');
                }
            }
            filtrar_numero_ordenes();
        });
        setInterval(RecargarTablaPendientes,600000);//180000
        RecargarTablaPendientes();
    });
    function RecargarTablaPendientes(){
        $.ajax({
            url: "{{route('AreaTablaPendientes')}}",
            type: 'POST',
            data: {
                Area: '{{$Area}}'
            },
            beforeSend: function() {
            },
            success: function(response) {
                if ($.fn.DataTable.isDataTable('#TablaPreparadoPendientes')) {
                    $('#TablaPreparadoPendientes').DataTable().clear().destroy();
                }
                $('#TablaPreparadoPendientesBody').html(response);
                table = $('#TablaPreparadoPendientes').DataTable(
                    {"order": [],
                    "pageLength": 50,
                    "columnDefs": [
                            {
                                "targets": [3,4,8,10], 
                                "visible": false, 
                                "searchable": true 
                            }
                        ],"language": {
                            "sProcessing":     "Procesando...",
                            "sLengthMenu":     "Mostrar _MENU_ registros",
                            "sZeroRecords":    "No se encontraron resultados",
                            "sInfo":           "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                            "sInfoEmpty":      "Mostrando de 0 a 0 de 0 registros",
                            "sInfoFiltered":   "(filtrado de _MAX_ registros en total)",
                            "sSearch":         "Buscar:",
                            "sUrl":            "",
                        }
                    }
                );
                $('#FiltroLinea').on('change', function() {
                    var val = $(this).val();
                    if(!(val == "" || val == null)){
                        if(val == -1) {
                            table.column(8).search('').draw();
                        } else {
                            table.column(8).search(val).draw();
                        }
                    }
                });
                $('#FiltroLinea').trigger('change');
                filtrar_numero_ordenes();
            },
            error: function(xhr, status, error) {
               console.log('Ocurrio un error al traer las Ordenes Pendientes' ); 
            }
        });
    }
    function filtrar_numero_ordenes(){
        document.getElementById('totalDetenidasD').innerHTML = document.querySelectorAll('#TablaPreparadoPendientesBody tr[data-status="D"]').length;
        document.getElementById('totalDetenidasP').innerHTML = document.querySelectorAll('#TablaPreparadoPendientesBody tr[data-status="P"]').length;
        document.getElementById('totalDetenidasU').innerHTML = document.querySelectorAll('#TablaPreparadoPendientesBody tr[data-status="U"]').length;
        document.getElementById('totalDetenidasN').innerHTML = document.querySelectorAll('#TablaPreparadoPendientesBody tr[data-status="N"]').length;
    }
</script>
@endsection