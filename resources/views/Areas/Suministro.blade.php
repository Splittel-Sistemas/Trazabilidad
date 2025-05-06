@extends('layouts.menu2')
@section('title', 'Suministro')
@section('styles')
<link rel="stylesheet" href="{{asset('css/Suministro.css')}}">
<style>
    #ToastGuardado {
        position: fixed; /* Fixed position */
        top: 5rem; /* Distance from the top */
        right: 20px; /* Distance from the right */
        z-index: 1050; /* Ensure it's above other content */
    }
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
</style>
@endsection
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Suministro</h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <!-- Módulos sin corte y completados -->
            <ul class="nav nav-underline" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="proceso-tab" data-bs-toggle="tab" href="#tab-proceso" role="tab" aria-controls="tab-proceso" aria-selected="false" tabindex="-1">
                        Abiertos
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="completado-tab" data-bs-toggle="tab" href="#tab-completado" role="tab" aria-controls="tab-completado" aria-selected="false" tabindex="-1">
                        Cerrados
                    </a>
                </li>
            </ul>
            <hr id="hr-menu">
            <div class="tab-content mt-4" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-proceso" role="tabpanel" aria-labelledby="proceso-tab">
                    {{--<div class="col-6">
                        <div class="card shadow-sm">
                            <div class="card-body row" id="filtro">
                                <label for="CodigoEscaner" class="col-form-label col-sm-12 pt-0">Proceso <span class="text-muted"></span></label>
                                <div class="col-8">
                                        <div class="form-check form-check-inline ">
                                            <input class="form-check-input" type="radio" name="TipoProceso" id="Iniciar" checked onclick="MostrarRetrabajo('Entrada')">
                                            <label class="form-check-label" for="Iniciar">
                                            Entrada
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline ">
                                            <input class="form-check-input" type="radio" name="TipoProceso" id="Finalizar" onclick="MostrarRetrabajo('Salida')">
                                            <label class="form-check-label" for="Finalizar">
                                            Salida
                                            </label>
                                        </div>
                                </div>
                                <hr>
                                <form id="filtroForm" method="post" class="form-horizontal row mt-0 needs-validation" novalidate="">
                                    <div class="col-8" id="CodigoDiv">
                                        <div class="">
                                            <label for="CodigoEscaner">C&oacute;digo <span class="text-muted">&#40;Escanea o Ingresa manual&#41;</span></label>
                                            <!--<a href=""><i class="fa fa-toggle-on"></i></a>-->
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm" oninput="ListaCodigo(this.value,'CodigoEscanerSuministro')" id="CodigoEscaner" aria-describedby="CodigoEscanerHelp" placeholder="Escánea o ingresa manualmente.">
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
                    </div>--}}
                    <div id="ContentTabla" class="col-12 mt-2">
                        <div class="card" id="DivCointainerTableSuministro">
                            <div class="table-responsive">
                                <table id="TablaSuministroAbiertas" class="table table-sm fs--1 mb-1">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>Orden Fabricación</th>
                                            <th>Artículo</th>
                                            <th>Descripción</th>
                                            <th>Emisiones Pendientes</th>
                                            <th>Suministro Normal</th>
                                            <th>Suministro Retrabajo</th>
                                            <th>Cantidad Cortes</th>
                                            <th>Estatus</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="TablaSuministroAbiertasBody" class="list">
                                        @foreach($PartidasOFA as $partida)
                                            <tr style="@if($partida->Urgencia=='U'){{'background:#FFDCDB';}}@endif">
                                                <td class="text-center">{{$partida->OrdenFabricacion }}</td>
                                                <td>{{$partida->Articulo }}</td>
                                                <td>{{$partida->Descripcion }}</td>
                                                <td>{{$partida->OrdenFaltantes}}</td>
                                                <td>{{$partida->Normal }}</td>
                                                <td>{{$partida->Retrabajo }}</td>
                                                <td class="text-center">{{$partida->TotalPartida }}</td>
                                                <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>
                                                <td><button class="btn btn-sm btn-outline-info px-3 py-2" onclick="Planear('{{$partida->idEncript}}')">Detalles</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-completado" role="tabpanel" aria-labelledby="completado-tab">
                    <div class="col-6 mt-2  mb-4 ">
                        <div class="accordion ml-3" id="accordionFiltroUnico">
                            <div class="accordion-item shadow-sm card border border-light">
                                <h4 class="accordion-header" id="headingFiltroUnico">
                                    <button class="accordion-button btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroUnico" aria-expanded="true" aria-controls="collapseFiltroUnico">
                                        <strong>Filtro Fecha</strong>
                                    </button>
                                </h4>
                                <div class="accordion-collapse collapse collapse" id="collapseFiltroUnico" aria-labelledby="headingFiltroUnico" data-bs-parent="#accordionFiltroUnico">
                                    <div class="accordion-body pt-2">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="inputFechaUnica" class="form-label"><strong>Fecha Inicio</strong></label>
                                                    <div class="input-group">
                                                        <input type="date" name="fecha" id="inputFechaInicio" value="{{$fechaAtras}}" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="invalid-feedback" id="error_inputFechaInicio"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="inputFechaUnica" class="form-label"><strong>Fecha Fin</strong></label>
                                                    <div class="input-group">
                                                        <input type="date" name="fecha" id="inputFechaFin" value="{{$fecha}}" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="invalid-feedback" id="error_inputFechaFin"></div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <button id="buscarUnico" class="btn btn-primary btn-sm float-end">
                                                        <i class="fa fa-search"></i> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="ContentTabla" class="col-12 mt-2">
                        <div class="card" id="DivCointainerTableSuministro">
                            <div class="table-responsive">
                                <table id="TablaSuministroCerradas" class="table table-sm fs--1 mb-1">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>Orden Fabricación</th>
                                            <th>N&uacute;mero Partida</th>
                                            <th>Artículo</th>
                                            <th>Descripción</th>
                                            <th>Normal</th>
                                            <th>Retrabajo</th>
                                            <th>Cortes Suministrados</th>
                                            <th>Fecha Finalizacion</th>
                                            <th>Estatus</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="TablaSuministroCerradasBody" class="list">
                                        @foreach($PartidasOFC as $partida)
                                        <tr>
                                            <td class="text-center">{{$partida->OrdenFabricacion }}</td>
                                            <td class="text-center">{{$partida->NumeroPartida }}</td>
                                            <td>{{$partida->Articulo }}</td>
                                            <td>{{$partida->Descripcion }}</td>
                                            <td>{{$partida->Normal }}</td>
                                            <td>{{$partida->Retrabajo }}</td>
                                            <td class="text-center">{{$partida->TotalPartida }}</td>
                                            <td class="text-center">{{$partida->FechaTermina }}</td>
                                            <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Cerrada</span></div></td>
                                            <td><button class="btn btn-sm btn-outline-info px-3 py-2" onclick="Detalles('{{$partida->idEncript}}')">Detalles</button></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--MODAL DETALLE-->
    <div class="modal fade" id="ModalDetalle" tabindex="-1" data-bs-backdrop="static" aria-labelledby="ModalDetalleLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="height: 90%">
          <div class="modal-content" style="height: 100%">
            <div class="modal-header bg-info">
              <h5 class="modal-title text-white" id="ModalDetalleLabel">Detalles Partidas de Orden de Fabricaci&oacute;n</h5><button class="btn" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-white"></span></button>
            </div>
            <div class="modal-body" id="ModalDetalleBody">
                <div class="" id="ModalDetalleBodyInfoOF">
                </div>
                <div class="mt-0" id="ModalDetalleBodyPartidasOF">
                </div>
            </div>
            <div class="modal-footer" id="ModalDetalleFooter">
                <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
    </div>
    <!--MODAL PARA PLANEACION-->
    <div class="modal fade" id="ModalSuministro" tabindex="-1" data-bs-backdrop="static" aria-labelledby="ModalSuministroLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="height: 90%">
          <div class="modal-content" style="height: 100%">
            <div class="modal-header bg-info">
              <h5 class="modal-title text-white" id="ModalSuministroLabel">Suministro</h5><button class="btn" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-white"></span></button>
            </div>
            <div class="modal-body" id="ModalSuministroBody">
                <div class="" id="ModalSuministroBodyInfoOF">
                </div>
                <div class="row">
                    <form id="CortesForm" class="row g-3 needs-validation" novalidate="">
                        <div class="col-6">
                            <label class="form-label" for="Cantitadpiezas">Ingresa n&uacute;mero de Unidades a Suministrar </label>
                                <input class="form-control form-control-sm has-validation" id="Cantitadpiezas" type="number" oninput="RegexNumeros(this)" placeholder="Ingresa una cantidad" disabled />
                            <div class="invalid-feedback" id="error_cantidad"></div>
                            <div class="form-check mt-2 mb-2">
                                <input class="form-check-input" id="Retrabajo" type="checkbox" />
                                <label class="form-check-label" for="Retrabajo">Retrabajo</label>
                                <div class="invalid-feedback" id="error_retrabajo"></div>
                            </div>
                            <input type="hidden" id="CantitadpiezasIdPartidasOF">
                        </div>
                        <div class="col-6 mt-2">
                            <div id="Emisiones" class="mt-2 mb-2" style="display:none;">
                                <label class="form-label" for="Cantitadpiezas">Selecciona una Orden de Producci&oacute;n </label>
                                <select id="EmisionesOpciones" class="form-select form-select-sm" aria-label=".form-select-sm">
                                </select>
                                <div class="invalid-feedback" id="error_emision"></div>
                            </div>
                            <button id="btnGrupoUnidadaesSuministro" class="btn btn-success btn-sm float-end">Guardar</button>
                        </div>
                    </form>
                </div>
                <div class="mt-0" id="ModalSuministroBodyPartidasOF">
                </div>
            </div>
            <div class="modal-footer" id="ModalSuministroFooter">
                <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
    </div>
    <!--MODAL RETRABAJO-->
    <div class="modal fade" id="ModalRetrabajo" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog ">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="ModalRetrabajoLabel">Orden de Fabricaci&oacute;n a Retrabajo</h5><button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
            </div>
            <div class="modal-body">
                    <div class="col-8">
                        <label class="form-label" for="RetrabajoOF">Orden de Fabricación</label>
                            <input class="form-control search-input form-control-sm" id="RetrabajoOF" type="text"  required="" oninput="RegexNumeros(this); RetrabajoMostrarOFBuscar(this)" placeholder="Ingresa Número de Orden de Fabricación"/>
                        <div id="RetrabajoOFOpciones" class="list-group" style="position:;max-height: 7rem;overflow-y: auto;">

                        </div>
                        <div class="invalid-feedback" id="error_RetrabajoOF"></div>
                    </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cancelar</button></div>
          </div>
        </div>
    </div>
    <!--MODAL PDF-->
    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h6 class="modal-title text-white" id="ModalDetalleLabel">Imprimir PDF</h6><button class="btn" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-white"></span></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí se cargará el PDF en un iframe -->
                    <iframe id="pdfIframe" src="" width="100%" height="300px"></iframe>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div  id="ContainerToastGuardado"></div>
@endsection
@section('scripts')
<script src="{{ asset('js/Suministro.js') }}"></script>
<script>
    $(document).ready(function() {
        DataTable('TablaSuministroAbiertas',true);
        $('.dt-layout-start:first').append(
        '<button class="btn btn-info mb-0" data-bs-toggle="modal" data-bs-target="#ModalRetrabajo" onclick="LimpiarOF()"><i class="fas fa-plus"></i> Retrabajo</button>');
        DataTable('TablaSuministroCerradas',true);
        $('#btnGrupoUnidadaesSuministro').click(function() {
            event.preventDefault();
            Cantitadpiezas=$('#Cantitadpiezas');
            errorCantidad=$('#error_cantidad');
            EmisionesOpciones=$('#EmisionesOpciones');
            errorEmision=$('#error_emision');
            Retrabajo=$('#Retrabajo');
            errorRetrabajo=$('#error_retrabajo');
            CantitadpiezasIdPartidasOF=$('#CantitadpiezasIdPartidasOF').val();
            if(CantitadpiezasIdPartidasOF=='' || CantitadpiezasIdPartidasOF==null){
                error('Partida no guardada','No fue posible guardar los datos de la partida, todos los campos son requeridos');
                return 0;
            }
            if(Cantitadpiezas.val()=="" || Cantitadpiezas.val()==null || Cantitadpiezas.val()==0){
                Cantitadpiezas.addClass('is-invalid');
                errorCantidad.text('Por favor, ingresa un número valido, mayor a 0.');
                errorCantidad.show();
                return 0; 
            }else{
                Cantitadpiezas.removeClass('is-invalid');
                errorCantidad.text('');
                errorCantidad.hide(); 
            }
            if(EmisionesOpciones.val()=='' || EmisionesOpciones.val()==null){
                EmisionesOpciones.addClass('is-invalid');
                errorEmision.text('Campo requerido, selecciona una Emisión de producción.');
                errorEmision.show();
                return 0; 
            }else{
                EmisionesOpciones.removeClass('is-invalid');
                errorEmision.text('');
                errorEmision.hide(); 
            }
            
            $.ajax({
                url: "{{route('SuministroGuardar')}}", 
                type: 'POST',
                data: {
                    id:CantitadpiezasIdPartidasOF,
                    emision:EmisionesOpciones.val(),
                    retrabajo:Retrabajo.prop('checked'),
                    Cantitadpiezas:Cantitadpiezas.val(),
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                },
                success: function(response) {
                    if(response.status=="success"){
                        success('Guardado correctamente!',response.message);
                        RecargarTablaCerradas();
                        RecargarTabla();
                        Planear(response.OF);
                    }else if(response.status=="error"){
                        error('Error',response.message);
                    }else if(response.status=="errorCantidada"){
                        error('Cantidad no disponible!',response.message);
                    }else if(response.status=="errorEmision"){
                        error('Orden de emisión requerida!',response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    error('Error Server',jqXHR.responseJSON.message);
                }
            });
        });
        $('#buscarUnico').click(function() {
            RecargarTablaCerradas();
        });
        $('#inputFechaFin').change(function() {
            fechainicio=$('#inputFechaInicio');
            fechafin=$('#inputFechaFin');
            $('#error_inputFechaInicio').hide();
            fechainicio.removeClass('is-invalid');
            errorinputFechaFin=$('#error_inputFechaFin');
            if(fechafin.val()<fechainicio.val()){
                fechafin.addClass('is-invalid');
                errorinputFechaFin.text('El campo Fecha Fin tiene que se mayor a Fecha Inicio.');
                errorinputFechaFin.show();
                return 0; 
            }else{
                fechafin.removeClass('is-invalid');
                errorinputFechaFin.text('');
                errorinputFechaFin.hide(); 
            }
        });
        $('#inputFechaInicio').change(function() {
            fechainicio=$('#inputFechaInicio');
            fechafin=$('#inputFechaFin');
            fechafin.removeClass('is-invalid');
            $('#error_inputFechaFin').hide();
            errorinputFechaInicio=$('#error_inputFechaInicio');
            if(fechafin.val()<fechainicio.val()){
                fechainicio.addClass('is-invalid');
                errorinputFechaInicio.text('El campo Fecha Inicio tiene que ser menor a Fecha Fin.');
                errorinputFechaInicio.show();
                return 0; 
            }else{
                fechainicio.removeClass('is-invalid');
                errorinputFechaInicio.text('');
                errorinputFechaInicio.hide(); 
            }
        });
        $('#EmisionesOpciones').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            // Obtener el valor de data-cantidad
            var cantidad = selectedOption.data('cantidad');
            $('#Cantitadpiezas').val(cantidad);
        });
        setInterval(RecargarTabla, 10000);
    });
    function Detalles(id){
        $('#ModalDetalle').modal('show');
        $('#ModalDetalleBodyInfoOF').html('');
        $('#ModalDetalleBodyPartidasOF').html('');
        //document.getElementById("Retrabajo").checked=false;
        $.ajax({
            url: "{{route('SuministroDatosModal')}}", 
            type: 'POST',
            data: {
                id:id,
                detalles:"detalles",
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#ModalDetalleBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                $('#ModalDetalleBodyPartidasOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                },
            success: function(response) {
                if(response.status=="success"){
                    $('#CantitadpiezasIdPartidasOF').val(response.idPartidaOF);
                    $('#ModalDetalleBodyInfoOF').html(response.Ordenfabricacioninfo);
                    $('#ModalDetalleBodyPartidasOF').html(response.Ordenfabricacionpartidas);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#ModalDetalleBodyInfoOF').html('');
                $('#ModalDetalleBodyPartidasOF').html('');
                errorBD();
            }
        });
    }
    function Planear(OrdenFabricacion){
        $('#Retrabajo').prop('checked', false);
        $('#Retrabajo').prop('disabled', false);
        $('#Cantitadpiezas').val('');
        $('#Emisiones').fadeIn(100);
        $('#ModalSuministro').modal('show');
        $('#ModalSuministroBodyInfoOF').html('');
        $('#ModalSuministroBodyPartidasOF').html('');
        $('#CantitadpiezasIdPartidasOF').val('');
        $('#btnGrupoPiezasCorte1').fadeIn();
        $('#ModalRetrabajo').modal('hide');
        document.getElementById("Retrabajo").checked=false;
        //Regresa todo normal
        Cantitadpiezas=$('#Cantitadpiezas');
        errorCantidad=$('#error_cantidad');
        EmisionesOpciones=$('#EmisionesOpciones');
        errorEmision=$('#error_emision');
        CantitadpiezasIdPartidasOF=$('#CantitadpiezasIdPartidasOF').val();
        Cantitadpiezas=$('#Cantitadpiezas');
        Cantitadpiezas.removeClass('is-invalid');
                errorCantidad.text('');
                errorCantidad.hide();
        EmisionesOpciones.removeClass('is-invalid');
                errorEmision.text('');
                errorEmision.hide();
        $.ajax({
            url: "{{route('SuministroDatosModal')}}", 
            type: 'POST',
            data: {
                id:OrdenFabricacion,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#ModalSuministroBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                $('#ModalSuministroBodyPartidasOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                },
            success: function(response) {
                if(response.status=="success"){
                    $('#CantitadpiezasIdPartidasOF').val(response.idPartidaOF);
                    $('#ModalSuministroBodyInfoOF').html(response.Ordenfabricacioninfo);
                    $('#ModalSuministroBodyPartidasOF').html(response.Ordenfabricacionpartidas);
                    TraerEmisiones();
                    //$('#TablePartidasModal').DataTable().destroy();
                    //DataTable('TablePartidasModal',false);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#ModalSuministroBodyInfoOF').html('');
                $('#ModalSuministroBodyPartidasOF').html('');
                errorBD();
            }
        });
    }
    function TraerEmisiones(){
        OrdenFabricacion=$('#CantitadpiezasIdPartidasOF').val();
        $('#EmisionesOpciones').html('');
        $.ajax({
            url: "{{route('SuministroEmision')}}", 
            type: 'POST',
            data: {
                id:OrdenFabricacion,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {

            },
            success: function(response) {
                $('#EmisionesOpciones').html(response.opciones);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            }
        });
    }
    function Cancelar(id){
        confirmacion('Cancelar Partida','¿Desea cancelar la la partida?','Confirmar','CancelarAccion(\''+id+'\')');
    }
    function CancelarAccion(id){
        $.ajax({
            url: "{{route('SuministroCancelar')}}", 
            type: 'POST',
            data: {
                id:id,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {

            },
            success: function(response) {
                if(response.status=='success'){
                    Planear(response.OF);
                    success("Cancelada Correctamente!",response.message);
                    RecargarTabla();
                    RecargarTablaCerradas();
                }else if(response.status=='errornoexiste'){
                    error("Error!",response.message);
                }else if(response.status=='erroriniciada'){
                    error("Error!",response.message);
                }else if(response.status=='errorfinalizada'){
                    error("Error!",response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            }
        });
    }
    function Finalizar(id){
        $.ajax({
            url: "{{route('SuministroFinalizar')}}", 
            type: 'POST',
            data: {
                id:id,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status=='success'){
                    Planear(response.OF);
                    success("Finalizada Correctamente!",response.message);
                    RecargarTabla();
                    RecargarTablaCerradas();
                }else if(response.status=='errornoexiste'){
                    error("Error!",response.message);
                }else if(response.status=='errorfinalizada'){
                    error("Error!",response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            }
        });
    }
    function RecargarTabla(){
        $.ajax({
            url: "{{route('SuministroRecargarTabla')}}", 
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status=="success"){
                    $('#TablaSuministroAbiertas').DataTable().destroy();
                    $('#TablaSuministroAbiertasBody').html(response.table);
                    DataTable('TablaSuministroAbiertas',true);
                    $('.dt-layout-start:first').append(
                    '<button class="btn btn-info mb-0" data-bs-toggle="modal" data-bs-target="#ModalRetrabajo" onclick="LimpiarOF()"><i class="fas fa-plus"></i> Retrabajo</button>');
                }
            }
        });
    }
    function RecargarTablaCerradas(){
        fechainicio=$('#inputFechaInicio');
        fechafin=$('#inputFechaFin');
        if(fechafin.val()<fechainicio.val()){
                return 0; 
        }
        $.ajax({
            url: "{{route('SuministroRecargarTablaCerrada')}}", 
            type: 'GET',
            data: {
                fechainicio:fechainicio.val(),
                fechafin:fechafin.val(),
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status=="success"){
                    $('#TablaSuministroCerradas').DataTable().destroy();
                    $('#TablaSuministroCerradasBody').html(response.table);
                    DataTable('TablaSuministroCerradas',true);
                }
            }
        });
    }
    function LimpiarOF(){
        $('#RetrabajoOF').val('');
        $('#RetrabajoOFOpciones').html('');
    }
    function RetrabajoMostrarOFBuscar(RetrabajoOF){
        $('#RetrabajoOFOpciones').html('');
        if(RetrabajoOF.value==""){
            return 0;
        }if((RetrabajoOF.value).length<5){
            return 0;
        }
        $.ajax({
            url: "{{route('BuscarSuministro')}}", 
            type: 'POST',
            data: {
                OF:RetrabajoOF.value,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {

            },
            success: function(response) {
                $('#RetrabajoOFOpciones').html(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#RetrabajoOFOpciones').html('');
            }
        });
    }
    function RetrabajoMostrarOFBuscarModal(id){
        $('#ModalRetrabajo').modal('hide');
        $('#RetrabajoOFOpciones').html('');
        Planear(id);
        $('#Retrabajo').prop('checked', true);
        $('#Retrabajo').prop('disabled', true);
        $('#Emisiones').fadeIn(100);
        $('#btnGrupoPiezasCorte').fadeOut(100);
        $('#btnGrupoPiezasCorte1').fadeIn(100);
        $('#CantitadpiezasIdOF').val(id)
    }
    function etiquetaColor(id){
        Coloretiqueta=$('#Coloretiqueta').val();
        var url = "{{ route('generarPDFSuministro')}}?id=_corteId_";
        url = url.replace('_corteId_', id);
        // Asignar la URL al iframe para mostrar el PDF
    document.getElementById('pdfIframe').src = url;
    
    // Abrir el modal con el iframe
    $('#pdfModal').modal('show');
        // Abre la URL para descargar el PDF
        /*var ventana = window.open(url, '_blank');
        $('#ModalColor').modal('hide');*/
    }


    /*function ListaCodigo(Codigo,Contenedor){
        $('#ToastGuardado').fadeOut();
        document.getElementById('CodigoEscanerSuministro').style.display = "none";
        if (CadenaVacia(Codigo)) {
            return 0;
        }
        $('#ContentTabla').hide();
        if(Codigo.length<6){
            return 0;
        }
        InicioInput=document.getElementById('Iniciar');
        if(InicioInput.checked){
            Inicio=1;
            Finalizar=0;
        }
        FinalizarInput=document.getElementById('Finalizar');
        if(FinalizarInput.checked){
            Inicio=0;
            Finalizar=1;
        }
        $.ajax({
            url: "{{route('SuministroBuscar')}}", 
            type: 'GET',
            data: {
                Codigo: Codigo,
                Inicio:Inicio,
                Finalizar:Finalizar,
                Area:'{{$Area}}',
                _token: '{{csrf_token()}}'  
            },
            beforeSend: function() {
                $('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
            },
            success: function(response) {
                $('#CantidadDiv').hide();
                $('#IniciarBtn').hide();
                $('#RetrabajoDiv').hide();
                document.getElementById('Retrabajo').checked = false;
                if(response.status=="success"){
                    $('#DivCointainerTableSuministro').html(response.tabla);
                    if(response.Escaner==1){
                        if((response.tabla).includes('<td')){
                        TablaList(DivCointainerTableSuministro);
                        }
                    }
                    $('#CantidadPartidasOF').html('<span class="badge bg-light text-dark">Piezas procesadas '+response.CantidadCompletada+"/"+response.CantidadTotal+'</span>');
                    $('#TituloPartidasOF').html(response.OF);
                    if(response.Escaner==0){
                        if((response.tabla).includes('<td')){
                            TablaList(DivCointainerTableSuministro);
                        }
                        $('#CantidadDiv').fadeOut();
                        $('#IniciarBtn').fadeOut();
                        $('#RetrabajoDiv').fadeOut();
                        if(response.EscanerExiste==0){
                            Mensaje='Codigo '+Codigo+' El codigo que intentas ingresar No existe!';
                            Color='bg-danger';
                            $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                            $('#ToastGuardadoBody').html(Mensaje);
                            $('#ToastGuardado').fadeIn();
                            setTimeout(function(){
                                $('#ToastGuardado').fadeOut();
                            }, 2000);
                        }else{
                            $('#ContentTabla').show();
                            $('#CantidadDiv').fadeIn();
                            $('#IniciarBtn').fadeIn();
                            $('#RetrabajoDiv').fadeIn();
                            if(Inicio==1){
                                const Retrabajo = document.getElementById('Retrabajo');
                                Retrabajo.disabled = false;
                            }else{
                                const Retrabajo = document.getElementById('Retrabajo');
                            Retrabajo.disabled = true;
                            }
                            return 0;
                        }
                    }else{
                        $('#ContentTabla').show();
                        Mensaje="";
                        if(response.Inicio==1){
                            switch (response.TipoEscanerrespuesta) {
                                case 1:
                                //$('#CodigoEscaner').val('');
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> guardado correctamente!';
                                    Color='bg-success';
                                    break;
                                case 2:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Ya se encuentra iniciado!';
                                    Color='bg-warning';
                                    break;
                                case 3:
                                    confirmacion('Retrabajo','¿Desea enviar codigo'+Codigo+' a Retrabajo? ','Confirmar','Retrabajo("'+Codigo+'")');
                                    return 0;
                                    break;
                                case 4:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> No existe!';
                                    Color='bg-danger';
                                    $('#ContentTabla').hide();
                                    $('#CantidadPartidasOF').html('');
                                    break;
                                case 5:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Aún no termina el proceso anterior!';
                                    Color='bg-danger';
                                    $('#ContentTabla').hide();
                                    $('#CantidadPartidasOF').html('');
                                    break;
                                case 6:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Aún no termina el proceso posterior!';
                                    Color='bg-danger';
                                    $('#ContentTabla').hide();
                                    $('#CantidadPartidasOF').html('');
                                    break;
                                default:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Ocurrio un error!';
                                    Color='bg-danger';
                                    break;
                            }

                        }
                        if(response.Finalizar==1){
                            switch (response.TipoEscanerrespuesta) {
                                case 1:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Finalizado!';
                                    Color='bg-success';
                                    break;
                                case 2:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Aún no ha sido inicializado!';
                                    Color='bg-danger';
                                    break;
                                case 3:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> no encontrado!';
                                    Color='bg-danger';
                                    break;
                            
                                default:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Ocurrio un error!';
                                    Color='bg-danger';
                                    break;
                                    break;
                            }

                        }
                        $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                        $('#ToastGuardadoBody').html(Mensaje);
                        $('#CantidadDiv').fadeOut();
                        $('#IniciarBtn').fadeOut();
                    }
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2500);
                }else if(response.status=="empty"){
                        $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                        $('#ToastGuardadoBody').html('El codigo No existe!  ');
                        $('#ToastGuardado').fadeIn();
                        setTimeout(function(){
                            $('#ToastGuardado').fadeOut();
                        }, 2000);
                }
            },
            error: function(xhr, status, error) {
                $('#CantidadDiv').hide();
                $('#IniciarBtn').hide();
            }
        }); 
    }
    function TraerDatos(id,OF){
        $('#CodigoEscaner').val(OF+"-"+id);
        $('#CodigoEscanerSuministro').html('');
    }
    function Retrabajo(Codigo){
        $.ajax({
            url: "{{route('SuministroBuscar')}}", 
            type: 'GET',
            data: {
                Codigo: Codigo,
                Inicio:1,
                Finalizar:0,
                Confirmacion:1,
                Area:'{{$Area}}',
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
            },
            success: function(response) {
                if(response.status=="success"){
                    //$('#CodigoEscaner').val('');
                    Mensaje='Codigo <strong>'+Codigo+'</strong> Se agrego a Retrabajo!';
                    Color='bg-warning';
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                    $('#ToastGuardadoBody').html(Mensaje);
                    $('#DivCointainerTableSuministro').html(response.tabla);
                    var options = {
                        valueNames: ['NumParte', 'Cantidad', 'Inicio', 'Fin', 'Estatus'],
                        page: 5,  // Número de elementos por página
                        pagination: true,  // Habilita la paginación
                        filter: {
                            key: 'Estatus'  // Establece el filtro para la columna "Estatus"
                        }
                    };
                    $('#DivCointainerTableSuministro').html(response.tabla);
                    if(response.Escaner==1){
                        TablaList('ContainerTableSuministros');
                    }
                    $('#CantidadPartidasOF').html('<span class="badge bg-light text-dark">Piezas procesadas '+response.CantidadCompletada+"/"+response.CantidadTotal+'</span>');
                    $('#TituloPartidasOF').html(response.OF);
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2500);
                }
            }
        });
    }
    function TablaList(TableName){
        var options = {
            valueNames: ['NumParte', 'Cantidad', 'Inicio', 'Fin', 'Estatus'],
                page: 10,  
                pagination: true,
                filter: {
                            key: 'Estatus' 
                    }
        };
        userList = new List(TableName, options);
        document.querySelector('[data-list-filter="data-list-filter"]').addEventListener('change', function() {
            var filterValue = this.value; // Obtener el valor seleccionado
            if (filterValue === "") {
                userList.filter();  // Si no hay filtro seleccionado, muestra todos los elementos
            } else {
                userList.filter(function(item) {
                    return item.values().Estatus.toLowerCase().includes(filterValue.toLowerCase());
                });
            }
        });
    }
    $(document).ready(function() {
        $('#Cantidad').on('input', function() {
            RegexNumeros(document.getElementById('Cantidad'));
        });
        $('#CodigoEscaner').on('input', function() {
            RegexNumerosGuiones(document.getElementById('CodigoEscaner'));
        });
        $('#btnEscanear').click(function() {
            CodigoEscaner=$('#CodigoEscaner').val();
            Cantidad=$('#Cantidad').val();
            Retrabajo=document.getElementById('Retrabajo').checked;
            InicioInput = document.getElementById('Iniciar');
            if(Retrabajo && InicioInput.checked){
                Swal.fire({
                    title: 'Retrabajo',
                    text: `¿Desea enviar ${Cantidad} piezas con código ${CodigoEscaner} a Retrabajo?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    // Verificar si el usuario presionó "Confirmar"
                    if (result.isConfirmed) {
                        TipoNoEscaner();
                    } else {
                        return 0;
                    }
                })
            }else{
                TipoNoEscaner();
            }
        });
    })
    function TipoNoEscaner() {
        CodigoEscaner=$('#CodigoEscaner').val();
        Cantidad=$('#Cantidad').val();
        Retrabajo=document.getElementById('Retrabajo').checked;
        InicioInput = document.getElementById('Iniciar');
        if (InicioInput.checked) {
            Inicio = 1;
            Fin = 0;
        }
        FinalizarInput = document.getElementById('Finalizar');
        if (FinalizarInput.checked) {
            Inicio = 0;
            Fin = 1;
        }

        // Validación Solo números para Cantidad y mayor a 0
        if (Cantidad <= 0) {
            $('#Cantidad').addClass('is-invalid');
            $('#error_Cantidad').html('Campo cantidad no puede ser 0');
            return 0;
        } else {
            if ($('#Cantidad').hasClass('is-invalid')) { $('#Cantidad').removeClass('is-invalid'); }
            $('#error_Cantidad').html('');
        }

        if (!/^\d+$/.test(Cantidad)) {
            $('#Cantidad').addClass('is-invalid');
            $('#error_Cantidad').html('Solo se aceptan N&uacute;meros');
            return 0;
        } else {
            if ($('#Cantidad').hasClass('is-invalid')) { $('#Cantidad').removeClass('is-invalid'); }
            $('#error_Cantidad').html('');
        }

        // Validación Solo Números y -
        if (!/^[-\d]+$/.test(CodigoEscaner)) {
            $('#CodigoEscaner').addClass('is-invalid');
            $('#error_CodigoEscaner').html('Solo se aceptan N&uacute;meros y -');
            return 0;
        } else {
            if ($('#CodigoEscaner').hasClass('is-invalid')) { $('#CodigoEscaner').removeClass('is-invalid'); }
            $('#error_CodigoEscaner').html('');
        }
        // Realizar la petición AJAX
        $.ajax({
            url: "{{route('TipoNoEscaner')}}",
            type: 'POST',
            data: {
                Codigo: CodigoEscaner,
                Cantidad: Cantidad,
                Inicio: Inicio,
                Fin: Fin,
                Retrabajo: Retrabajo,
                Area: '{{$Area}}',
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
            },
            success: function(response) {
                $('#ToastGuardado').fadeOut();
                if(response.status=="dontexist" || response.status=="empty"){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('El Codigo '+ CodigoEscaner + ' que intentas ingresar No existe!');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2000);
                }else if(response.status=='PasBackerror'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Aún no se ha completado el proceso de Corte!');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2000);
                }else if(response.status=='error'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Ocurrio un error no fue posible guardar la información para codigo'+CodigoEscaner+'!');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2000);
                }else if(response.status=='success'){
                    if(response.Inicio==1){
                        mensaje='Nueva Entrada del Codigo '+CodigoEscaner+' Guardada!';
                    }else if(response.Inicio==0){
                        mensaje='Salida del Codigo '+CodigoEscaner+' Guardada!';
                    }
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html(mensaje);
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2000);
                }else if(response.status=='SurplusFin'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Error no guardado, la cantidad de salidas supera los Entradas!');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2000);
                }else if(response.status=='SurplusRetrabajo'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Error no guardado, Para mandar a retrabajo las ordenes tienen que estar en Estatus "Finalizado" !');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2000);
                }else if(response.status=='SurplusInicio'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Error no guardado, La cantidad de entradas no puede superar la cantidad Total de la Partida de la Orden de Fabricación !');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2000);
                }
                ListaCodigo(CodigoEscaner,'CodigoEscanerSuministro')
            },
            error: function(xhr, status, error) {
                $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Error, Ocurrió un problema, revisa tu conexión, si el percance persiste contacta a TI!');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 3000);
            }
        });
    }
    function MostrarRetrabajo(tipo) {
        const Retrabajo = document.getElementById('Retrabajo');
        Retrabajo.checked = false;
        if (tipo === 'Entrada') {
            // Mostrar el input cuando 'Entrada' esté seleccionado
            Retrabajo.disabled = false;
        } else {
            // Ocultar el input cuando 'Salida' esté seleccionado
            Retrabajo.disabled = true;
        }
    }*/
    function DataTable(tabla, busqueda){
        $('#'+tabla).DataTable({
                        "pageLength": 10,  // Paginación de 10 elementos por página
                        "lengthChange": false, // Desactiva la opción de cambiar el número de elementos por página
                        "paging": true, // Habilitar paginación
                        "searching": busqueda, // Habilitar búsqueda
                        "ordering": true, // Habilitar ordenación de columnas
                        "info": true, // Muestra información sobre el total de elementos
                        "language": {
                            "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                            "search":"Buscar",
                        },
                        "initComplete": function(settings, json) {
                            $('#'+tabla).css('font-size', '0.7rem');
                        }
        });
    }
</script>
@endsection