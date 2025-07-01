@extends('layouts.menu2')
@section('title', 'Estatus Ordenes de Fabricación')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    #myTab li a{
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        padding: 0.5rem 1rem 0.5rem 1rem;
    }
    #myTab li a:hover{
        background: #f1f1f1;
        border: solid 1px #e7e7e7;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    #myTab li .active{
        border: solid 1px #e7e7e7;
        border-bottom: solid white;
    }
    #myTab li .active:hover{
        border: solid 1px #e7e7e7;
        border-bottom: solid white;
    }
    #hr-menu{
        padding: 0;
        margin: 0;
    }
    #AbiertasTable td{
        padding: 0;
        margin: 0;
    }
    .MostrarMenos{
        height: 1rem;
        overflow: hidden;
    }
</style>
@endsection
@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs mb-3">
        <div class="row gy-3 mb-2 justify-content-between">
            <div class="col-md-9 col-auto">
                <h4 class="mb-2 text-1100">Estatus Ordenes de Fabricaci&oacute;n</h4>
            </div>
        </div>
    </div>
        <div class="card">
            <div class="card-body">
                <!-- Módulos sin corte y completados -->
                <ul class="nav nav-underline" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="Abiertas-tab" data-bs-toggle="tab" href="#tab-OFAbiertas" role="tab" aria-controls="tab-OFAbiertas" aria-selected="false" tabindex="-1">
                            Abiertas
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="completado-tab" data-bs-toggle="tab" href="#tab-OFFinalizadas" role="tab" aria-controls="tab-OFFinalizadas" aria-selected="false" tabindex="-1">
                            Cerradas
                        </a>
                    </li>
                </ul>
                <hr id="hr-menu">
                <div class="tab-content mt-4 " id="ContainerTablas">
                    <!-- Tab Abiertas -->
                    <div class="tab-pane fade show active" id="tab-OFAbiertas" role="tabpanel" aria-labelledby="Abiertas-tab">
                        <div class="col-6 mt-2  mb-4 ">
                            <div class="accordion ml-3" id="accordionFiltroOFAbiertas">
                                <div class="accordion-item shadow-sm card border border-light">
                                    <h6 class="accordion-header" id="headingFiltroOFAbiertas">
                                        <button class="accordion-button btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroOFAbiertas" aria-expanded="true" aria-controls="collapseFiltroOFAbiertas">
                                            <strong>Filtro Fecha</strong>
                                        </button>
                                    </h6>
                                    <div class="accordion-collapse collapse collapse" id="collapseFiltroOFAbiertas" aria-labelledby="headingFiltroOFAbiertas" data-bs-parent="#accordionFiltroOFAbiertas">
                                        <div class="accordion-body pt-2">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="BuscarFecha('{{$FechaFin}}','{{$FechaFin}}','Abierto')"><i class="fa-solid fa-calendar"></i> Día</button>
                                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="BuscarFecha('{{$FechaInicio}}','{{$FechaFin}}','Abierto')"><i class="fa-solid fa-calendar-week"></i> Semana</button>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputFechaInicioA" class="form-label"><strong>Fecha Inicio</strong></label>
                                                        <div class="input-group">
                                                            <input type="date" name="fecha" id="inputFechaInicioA" value="{{$FechaInicio}}" class="form-control form-control-sm">
                                                        </div>
                                                        <small class="text-danger" id="error_inputFechaInicioA"></small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputFechaInicioA" class="form-label"><strong>Fecha Fin</strong></label>
                                                        <div class="input-group">
                                                            <input type="date" name="fecha" id="inputFechaFinA" value="{{$FechaFin}}" class="form-control form-control-sm">
                                                        </div>
                                                         <small class="text-danger" id="error_inputFechaFinA"></small>
                                                    </div>
                                                    <p class="text-center m-0 p-0">D&iacute;a</p>
                                                    <div class="col-12 d-flex justify-content-center">
                                                        <a type="button" id="PreviusA" class="btn btn-link py-1 px-3" style="transform:scale(1.3)" onclick="BuscarPorDia('{{$FechaFin}}','{{$FechaFin}}','Abierto','A')">Anterior <i class="fas fa-arrow-left"></i></a>
                                                        <span id="SpanFechaA" class="pb-0">{{ \Carbon\Carbon::parse($FechaFin)->format('d/m/Y') }}</span>
                                                        <a type="button" id="NextA" class="btn btn-link py-1 px-3" style="transform:scale(1.3)" onclick="BuscarPorDia('{{$FechaFin}}','{{$FechaFin}}','Abierto','S')"><i class="fas fa-arrow-right"></i> Siguiente</a>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <button id="buscarAbiertas" class="btn btn-primary btn-sm float-end">
                                                            <i class="fa fa-search"></i> Buscar
                                                        </button>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive card">
                            <h4 class="text-muted text-center pt-2">Ordenes de Fabricaci&oacute;n Abiertas</h4>
                            <div id="AbiertasTablebtn"></div>
                            <table id="AbiertasTable" class="table table-sm" style="width: 100%;">
                                <thead>
                                    <tr class="bg-light">
                                        <th>#</th>
                                        <th>Orden Fabricaci&oacute;n</th>
                                        <th>Orden Venta</th>
                                        @if(Auth::user()->hasPermission("Vista Planeacion"))
                                            <th>Responsable Corte</th>
                                        @endif
                                        <th>Artículo</th>
                                        <th>Descripción</th>
                                        <th>Cantidad Total</th>
                                        <th>Fecha Planeaci&oacute;n</th>
                                        <th>Fecha Entrega SAP</th>
                                        <th>Tipo de Escaneo</th>
                                        <th>Urgencia</th>
                                        <th>LLC</th>
                                        <th>&Uacute;ltima Estaci&oacute;n</th>
                                        <th>Estatus</th>
                                    </tr>
                                </thead>
                                <tbody id="AbiertasTableBody">
                                @foreach($OrdenFabricacionAbiertas as $key => $orden)
                                    <tr>
                                        <td class="text-center">{{$key+1}}</td>
                                        <td class="text-center">{{ $orden->OrdenFabricacion}}</td>
                                        <td class="text-center">{{ $orden->OrdenVenta}}</td>
                                        @if(Auth::user()->hasPermission("Vista Planeacion"))
                                            <td><div class="badge badge-phoenix fs--2 badge-phoenix-info"><span class="fw-bold">{{ isset($orden->ResponsableUser)?$orden->ResponsableUser:"Sin corte"}}</span></div></td>
                                        @endif
                                        <td class="text-center">{{ $orden->Articulo}}</td>
                                        <td class="text-center">
                                            <div class="MostrarMenos" id="collapseA{{ $orden->OrdenFabricacion}}">
                                                {{ $orden->Descripcion }}
                                            </div>
                                            <a  onclick="MostrarMas('collapseA{{ $orden->OrdenFabricacion}}',this);" class="btn btn-sm btn-link">Ver más</a>
                                        </td>
                                        <td class="text-center">{{ $orden->CantidadTotal }}</td>
                                        <td class="text-center">{{ $orden->FechaEntrega }}</td>
                                        <td class="text-center">{{ $orden->FechaEntregaSAP }}</td>
                                        <td class="text-center">{{ ($orden->Escaner == 0)?"Uno a uno":"Masivo"}}</td>
                                        <td class="text-center">{{ ($orden->Urgencia == 'N')?"Normal":"Urgente" }}</td>
                                        <td class="text-center">{{ ($orden->LLC == 0)?"No":"Si" }}</td>
                                        <td class="text-center">{{ $orden->UltimaEstacion }}</td>
                                        <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-OFFinalizadas" role="tabpanel" aria-labelledby="completado-tab">
                        <div class="col-6 mt-2  mb-4 ">
                            <div class="accordion ml-3" id="accordionFiltroOFCerradas">
                                <div class="accordion-item shadow-sm card border border-light">
                                    <h6 class="accordion-header" id="headingFiltroOFCerradas">
                                        <button class="accordion-button btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroOFCerradas" aria-expanded="true" aria-controls="collapseFiltroOFCerradas">
                                            <strong>Filtro Fecha</strong>
                                        </button>
                                    </h6>
                                    <div class="accordion-collapse collapse collapse" id="collapseFiltroOFCerradas" aria-labelledby="headingFiltroOFCerradas" data-bs-parent="#accordionFiltroOFCerradas">
                                        <div class="accordion-body pt-2">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="BuscarFecha('{{$FechaFin}}','{{$FechaFin}}','Cerrado')"><i class="fa-solid fa-calendar"></i> Día</button>
                                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="BuscarFecha('{{$FechaInicio}}','{{$FechaFin}}','Cerrado')"><i class="fa-solid fa-calendar-week"></i> Semana</button>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputFechaInicioC" class="form-label"><strong>Fecha Inicio</strong></label>
                                                        <div class="input-group">
                                                            <input type="date" name="fecha" id="inputFechaInicioC" value="{{$FechaInicio}}" class="form-control form-control-sm">
                                                        </div>
                                                        <small class="text-danger" id="error_inputFechaInicioC"></small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputFechaInicioC" class="form-label"><strong>Fecha Fin</strong></label>
                                                        <div class="input-group">
                                                            <input type="date" name="fecha" id="inputFechaFinC" value="{{$FechaFin}}" class="form-control form-control-sm">
                                                        </div>
                                                        <small class="text-danger" id="error_inputFechaFinC"></small>
                                                    </div>
                                                    <p class="text-center m-0 p-0">D&iacute;a</p>
                                                    <div class="col-12 d-flex justify-content-center">
                                                        <a type="button" id="PreviusC" class="btn btn-link py-1 px-3" style="transform:scale(1.3)" onclick="BuscarPorDia('{{$FechaFin}}','{{$FechaFin}}','Cerrado','A')">Anterior <i class="fas fa-arrow-left"></i></a>
                                                        <span id="SpanFechaC" class="pb-0">{{ \Carbon\Carbon::parse($FechaFin)->format('d/m/Y') }}</span>
                                                        <a type="button" id="NextC" class="btn btn-link py-1 px-3" style="transform:scale(1.3)" onclick="BuscarPorDia('{{$FechaFin}}','{{$FechaFin}}','Cerrado','S')"><i class="fas fa-arrow-right"></i> Siguiente</a>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <button id="buscarCerradas" class="btn btn-primary btn-sm float-end">
                                                            <i class="fa fa-search"></i> Buscar
                                                        </button>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive card">
                            <h4 class="text-muted text-center pt-2">Ordenes de Fabricaci&oacute;n Cerradas</h4>
                            <div id="CerradasTablebtn"></div>
                            <table id="CerradasTable" class="table table-sm" style="width: 100%;">
                                <thead>
                                    <tr class="bg-light">
                                        <th>#</th>
                                        <th>Orden Fabricaci&oacute;n</th>
                                        <th>Orden Venta</th>
                                        @if(Auth::user()->hasPermission("Vista Planeacion"))
                                            <th>Responsable Corte</th>
                                        @endif
                                        <th>Artículo</th>
                                        <th>Descripción</th>
                                        <th>Cantidad Total</th>
                                        <th>Fecha Planeaci&oacute;n</th>
                                        <th>Fecha Entrega SAP</th>
                                        <th>Tipo de Escaneo</th>
                                        <th>Urgencia</th>
                                        <th>LLC</th>
                                        <th>&Uacute;ltima Estaci&oacute;n</th>
                                        <th>Estatus</th>
                                    </tr>
                                </thead>
                                <tbody id="CerradasTableBody">
                                @foreach($OrdenFabricacionCerradas as $key => $orden)
                                    <tr>
                                        <td class="text-center">{{$key+1}}</td>
                                        <td class="text-center">{{ $orden->OrdenFabricacion}}</td>
                                        <td class="text-center">{{ $orden->OrdenVenta}}</td>
                                        @if(Auth::user()->hasPermission("Vista Planeacion"))
                                            <td><div class="badge badge-phoenix fs--2 badge-phoenix-info"><span class="fw-bold">{{ isset($orden->ResponsableUser)?$orden->ResponsableUser:"Sin corte"}}</span></div></td>
                                        @endif
                                        <td class="text-center">{{ $orden->Articulo}}</td>
                                        <td class="text-center">                                            <div class="MostrarMenos" id="collapseC{{ $orden->OrdenFabricacion}}">
                                                {{ $orden->Descripcion }}
                                            </div>
                                            <a  onclick="MostrarMas('collapseC{{ $orden->OrdenFabricacion}}',this);" class="btn btn-sm btn-link">Ver más</a>
                                        </td>
                                        <td class="text-center">{{ $orden->CantidadTotal }}</td>
                                        <td class="text-center">{{ $orden->FechaEntrega }}</td>
                                        <td class="text-center">{{ $orden->FechaEntregaSAP }}</td>
                                        <td class="text-center">{{ ($orden->Escaner == 0)?"Uno a uno":"Masivo"}}</td>
                                        <td class="text-center">{{ ($orden->Urgencia == 'N')?"Normal":"Urgente" }}</td>
                                        <td class="text-center">{{ ($orden->LLC == 0)?"No":"Si" }}</td>
                                        <td class="text-center">{{ $orden->UltimaEstacion }}</td>
                                        <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-primary"><span class="fw-bold">Cerrada</span></div></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
@endsection
@section('scripts')
<script>
    var DatosOrdenFabricacionA = null;
    var DatosOrdenFabricacionC = null;
    $(document).ready(function() {
        DataTable('AbiertasTable',true)
        DataTable('CerradasTable',true);
        $('#buscarAbiertas').on('click', function() {
            if (DatosOrdenFabricacionA && DatosOrdenFabricacionA.readyState !== 4) {
                DatosOrdenFabricacionA.abort();
                console.log("Petición anterior cancelada");
            }
            let FechaInicio = $('#inputFechaInicioA').val();
            let FechaFin = $('#inputFechaFinA').val();
            let Estatus = 'Abiertas';
            if(FechaInicio > FechaFin){
                $('#inputFechaInicioA').addClass('is-invalid');
                $('#error_inputFechaInicioA').html('*Fecha Inicio no puede ser mayor a Fecha Fin');
                return 0;
            }else{
                $('#inputFechaInicioA').removeClass('is-invalid');
                $('#error_inputFechaInicioA').html('');
            }
            let url = `{{route('EstatusOrdenesFabricacion')}}/${FechaInicio || ''}/${FechaFin || ''}/${Estatus || ''}`;
            DatosOrdenFabricacionA = $.ajax({
                url: url, 
                type: 'GET',
                beforeSend: function() {
                    $('#AbiertasTableBody').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>");
                },
                success: function(response) {
                    if(response.status=="success"){
                        $('#AbiertasTable').DataTable().destroy();
                        $('#AbiertasTableBody').html(response.BodyTabla);
                        DataTable('AbiertasTable',true);
                    }
                }
            });
        });
        $('#buscarCerradas').on('click', function() {
             if (DatosOrdenFabricacionC && DatosOrdenFabricacionC.readyState !== 4) {
                DatosOrdenFabricacionC.abort();
                console.log("Petición anterior cancelada");
            }
            let FechaInicio = $('#inputFechaInicioC').val();
            let FechaFin = $('#inputFechaFinC').val();
            let Estatus = 'Cerradas';
            if(FechaInicio > FechaFin){
                $('#inputFechaInicioC').addClass('is-invalid');
                $('#error_inputFechaInicioC').html('*Fecha Inicio no puede ser mayor a Fecha Fin');
                return 0;
            }else{
                $('#inputFechaInicioC').removeClass('is-invalid');
                $('#error_inputFechaInicioC').html('');
            }
            let url = `{{route('EstatusOrdenesFabricacion')}}/${FechaInicio || ''}/${FechaFin || ''}/${Estatus || ''}`;
            DatosOrdenFabricacionC = $.ajax({
                url: url, 
                type: 'GET',
                beforeSend: function() {
                    $('#CerradasTableBody').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>");
                },
                success: function(response) {
                    if(response.status=="success"){
                        $('#CerradasTable').DataTable().destroy();
                        $('#CerradasTableBody').html(response.BodyTabla);
                        DataTable('CerradasTable',true);
                    }
                }
            });
        });
        $('#inputFechaFinA').on('change', function() {
            let FechaInicio = $('#inputFechaInicioA').val();
            let FechaFin = $('#inputFechaFinA').val();
            if(FechaInicio > FechaFin){
                $('#inputFechaInicioA').addClass('is-invalid');
                $('#error_inputFechaInicioA').html('*Fecha Inicio no puede ser mayor a Fecha Fin');
                return 0;
            }else{
                $('#inputFechaInicioA').removeClass('is-invalid');
                $('#error_inputFechaInicioA').html('');
            }
        });
        $('#inputFechaInicioA').on('change', function() {
            let FechaInicio = $('#inputFechaInicioA').val();
            let FechaFin = $('#inputFechaFinA').val();
            if(FechaInicio > FechaFin){
                $('#inputFechaInicioA').addClass('is-invalid');
                $('#error_inputFechaInicioA').html('*Fecha Inicio no puede ser mayor a Fecha Fin');
                return 0;
            }else{
                $('#inputFechaInicioA').removeClass('is-invalid');
                $('#error_inputFechaInicioA').html('');
            }
        });
        $('#inputFechaFinC').on('change', function() {
            let FechaInicio = $('#inputFechaInicioC').val();
            let FechaFin = $('#inputFechaFinC').val();
            if(FechaInicio > FechaFin){
                $('#inputFechaInicioC').addClass('is-invalid');
                $('#error_inputFechaInicioC').html('*Fecha Inicio no puede ser mayor a Fecha Fin');
                return 0;
            }else{
                $('#inputFechaInicioC').removeClass('is-invalid');
                $('#error_inputFechaInicioC').html('');
            }
        });
        $('#inputFechaInicioC').on('change', function() {
            let FechaInicio = $('#inputFechaInicioC').val();
            let FechaFin = $('#inputFechaFinC').val();
            if(FechaInicio > FechaFin){
                $('#inputFechaInicioC').addClass('is-invalid');
                $('#error_inputFechaInicioC').html('*Fecha Inicio no puede ser mayor a Fecha Fin');
                return 0;
            }else{
                $('#inputFechaInicioC').removeClass('is-invalid');
                $('#error_inputFechaInicioC').html('');
            }
        });
    });
    function DataTable(tabla, busqueda){
        var table = $('#'+tabla).DataTable({
            //"dom": 'Blfrtip',
            "language": {
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                "infoEmpty": "Mostrando 0 a 0 de 0 entrada(s)",
                "infoFiltered": "(filtrado de _MAX_ entradas totales)",
                "lengthMenu": "Mostrar _MENU_ entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros coincidentes",
            },
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: 'Exportar Excel',
                    title: tabla+"_"+"{{date('Ymd')}}",
                    className: 'btn btn-sm btn-success mx-3 mt-0 mb-2'
                }
            ],
            "initComplete": function(settings, json) {
                $('#'+tabla).css('font-size', '0.7rem');
            }
        });
        var table = $('#' + tabla).DataTable();
        table.buttons().container().appendTo('#'+tabla+'btn');
    }
    function BuscarFecha(FechaInicio,FechaFin,Estatus){
        FechaInicio = FechaInicio;
        FechaFin = FechaFin;
        if(Estatus == 'Abierto'){
            $('#inputFechaInicioA').val(FechaInicio);
            $('#inputFechaFinA').val(FechaFin);
            $('#buscarAbiertas').trigger('click');
            Previus=document.getElementById('PreviusA');
            Next=document.getElementById('NextA');
            SpanFecha = $('#SpanFechaA');
            Previus.setAttribute('onclick', "BuscarPorDia('" + FechaFin + "','" + FechaFin + "','" + Estatus + "','" + 'A' + "')");
            Next.setAttribute('onclick', "BuscarPorDia('" + FechaFin + "','" + FechaFin + "','" + Estatus + "','" + 'S' + "')");
            let partes = FechaFin.split("-");
            SpanFecha.html(`${partes[2]}/${partes[1]}/${partes[0]}`);
        }else{
            $('#inputFechaInicioC').val(FechaInicio);
            $('#inputFechaFinC').val(FechaFin);
            $('#buscarCerradas').trigger('click');
            Previus=document.getElementById('PreviusC');
            Next=document.getElementById('NextC');
            SpanFecha = $('#SpanFechaC');  
            Previus.setAttribute('onclick', "BuscarPorDia('" + FechaFin + "','" + FechaFin + "','" + Estatus + "','" + 'A' + "')");
            Next.setAttribute('onclick', "BuscarPorDia('" + FechaFin + "','" + FechaFin + "','" + Estatus + "','" + 'S' + "')");
            let partes = FechaFin.split("-");
            SpanFecha.html(`${partes[2]}/${partes[1]}/${partes[0]}`);
        }
    }
    function BuscarPorDia(FechaInicio,FechaFin,Estatus,Accion){
        //alert(Estatus);
        if(Estatus == 'Abierto'){
            Previus=document.getElementById('PreviusA');
            Next=document.getElementById('NextA');
            SpanFecha = $('#SpanFechaA');
        }else{
            Previus=document.getElementById('PreviusC');
            Next=document.getElementById('NextC');
            SpanFecha = $('#SpanFechaC');        
        }
        if(Accion == "A"){
            FechaInicio = RestarDia(FechaInicio);
            FechaFin = FechaInicio;
            Previus.onclick = null;
            Previus.setAttribute('onclick', "BuscarPorDia('" + FechaInicio + "','" + FechaFin + "','" + Estatus + "','" + Accion + "')");
            Next.onclick = null;
            Next.setAttribute('onclick', "BuscarPorDia('" + FechaInicio + "','" + FechaFin + "','" + Estatus + "','" + 'S' + "')");
        }else{
            FechaInicio = SumarDia(FechaInicio);
            FechaFin = FechaInicio;
            Previus.onclick = null;
            Previus.setAttribute('onclick', "BuscarPorDia('" + FechaInicio + "','" + FechaFin + "','" + Estatus + "','" + 'A' + "')");
            Next.onclick = null;
            Next.setAttribute('onclick', "BuscarPorDia('" + FechaInicio + "','" + FechaFin + "','" + Estatus + "','" + Accion + "')");
        }
        let partes = FechaFin.split("-");
        SpanFecha.html(`${partes[2]}/${partes[1]}/${partes[0]}`);
        BuscarFecha(FechaInicio,FechaFin,Estatus);
    }
    function MostrarMas(IdDiv,IdDivBtn){
        if ($('#' + IdDiv).hasClass('MostrarMenos')) {
            $('#'+IdDiv).removeClass('MostrarMenos');
            IdDivBtn.innerHTML = ('Ver menos');
        }else{
            $('#'+IdDiv).addClass('MostrarMenos');
            IdDivBtn.innerHTML = ('Ver más');
        }

    }
</script>
@endsection
