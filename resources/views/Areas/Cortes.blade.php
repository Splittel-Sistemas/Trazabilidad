@extends('layouts.menu2')
@section('title', 'Cortes')
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
</style>
@endsection
@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs mb-3">
        <div class="row gy-3 mb-2 justify-content-between">
            <div class="col-md-9 col-auto">
                <h4 class="mb-2 text-1100">Cortes</h4>
            </div>
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
                <div class="tab-content mt-4 " id="myTabContent">
                    <!-- Tab Proceso -->
                    <div class="tab-pane fade show active" id="tab-proceso" role="tabpanel" aria-labelledby="proceso-tab">
                        <div id="ContentTabla" class="col-12 mt-2">
                            <div class="card" id="DivCointainerTableSuministro">
                            <div class="table-responsive">
                            <table id="procesoTable" class="table table-sm fs--1 mb-1" style="width: 100%;display: none;">
                                <thead>
                                    <tr class="bg-light">
                                        <th>Orden Fabricación</th>
                                        <th>Responsable Corte</th>
                                        <th>Artículo</th>
                                        <th>Descripción</th>
                                        <th>Piezas Cortadas Normal</th>
                                        <th>Piezas Cortadas Retrabajo</th>
                                        <th>Cantidad Total</th>
                                        <th>Fecha Planeada</th>
                                        <th>Estatus</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                        <div class="table-responsive card">
                            <table id="completadoTable" class="table table-sm fs--1 mb-1">
                                <thead>
                                    <tr class="bg-light">
                                        <th>Orden Fabricación</th>
                                        <th>Artículo</th>
                                        <th>Descripción</th>
                                        <th>Piezas Normales</th>
                                        <th>Piezas Retrabajo</th>
                                        <th>Cantidad Total OF</th>
                                        <th>Fecha inicio</th>
                                        <th>Fecha Cierre</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="completadoTableBody" class="list">
                                    {{--@foreach($OrdenesFabricacionCerradas as $orden1)
                                    <tr>
                                        <td>{{$orden1->OrdenFabricacion }}</td>
                                        <td>{{$orden1->Articulo }}</td>
                                        <td>{{$orden1->Descripcion }}</td>
                                        <td>{{$orden1->Piezascortadas}}</td>
                                        <td>{{$orden1->PiezascortadasR }}</td>
                                        <td>{{$orden1->CantidadTotal }}</td>
                                        <td>{{$orden1->FechaComienzo }}</td>
                                        <td>{{$orden1->FechaFinalizacion }}</td>
                                        <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Cerrada</span></div></td>
                                        <td><button class="btn btn-sm btn-outline-info px-3 py-2" onclick="Detalles('{{$orden1->idEncript}}')">Detalles</button></td>
                                    </tr>
                                @endforeach--}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!--MODAL PARA PLANEACION-->
    <div class="modal fade" id="ModalSuministro" tabindex="-1" data-bs-backdrop="static" aria-labelledby="ModalSuministroLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="height: 90%">
          <div class="modal-content" style="height: 100%">
            <div class="modal-header bg-info">
              <h5 class="modal-title text-white" id="ModalSuministroLabel">Cortes</h5><button class="btn" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-white"></span></button>
            </div>
            <div class="modal-body" id="ModalSuministroBody">
                <div class="" id="ModalSuministroBodyInfoOF">
                </div>
                <div class="row">
                    <form id="CortesForm" class="row needs-validation" novalidate="">
                        <div id="InputNormal" class="col-6">
                            <label class="form-label" for="Cantitadpiezas">Ingresa n&uacute;mero de piezas a cortar </label>
                            <div class="input-group">
                                <input class="form-control form-control-sm has-validation" id="Cantitadpiezas" type="number" oninput="RegexNumeros(this)" placeholder="Ingresa una cantidad" />
                                <button id="btnGrupoPiezasCorte" class="btn btn-success btn-sm float-end">Guardar</button>
                            </div>
                            <div class="invalid-feedback" id="error_cantidad"></div>
                            <div class="form-check mt-2 mb-2">
                                <input class="form-check-input" id="Retrabajo" type="checkbox" />
                                <label class="form-check-label" for="Retrabajo">Retrabajo</label>
                                <div class="invalid-feedback" id="error_retrabajo"></div>
                            </div>
                            <input type="hidden" id="CantitadpiezasIdOF">
                        </div>
                        <div id="InputEmision" class="col-6">
                            <div id="Emisiones" class="mb-2" style="display:none;">
                                <label class="form-label" for="Cantitadpiezas">Selecciona una Orden de Producci&oacute;n </label>
                                <select id="EmisionesOpciones" class="form-select form-select-sm" aria-label=".form-select-sm">
                                </select>
                                <div class="invalid-feedback" id="error_emision"></div>
                            </div>
                            <button id="btnGrupoPiezasCorte1" class="btn btn-success btn-sm float-end" style="display: none">Guardar</button>
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
@endsection
@section('scripts')
<script>
    //$(document).ready(function() {
    document.addEventListener('DOMContentLoaded', function () {
        DataTable('completadoTable',true);
        document.getElementById("Retrabajo").addEventListener("change", function() {
            if (this.checked) {
                TraerEmisiones();
                $('#Emisiones').fadeIn(600);
                $('#btnGrupoPiezasCorte1').fadeIn();
                $('#btnGrupoPiezasCorte').fadeOut();
                $('#Cantitadpiezas').prop('disabled', true);
                $('#Cantitadpiezas').val(0);
            }else{
                $('#EmisionesOpciones').html('');
                $('#Emisiones').fadeOut(600);
                $('#btnGrupoPiezasCorte').fadeIn();
                $('#btnGrupoPiezasCorte1').fadeOut();
                $('#Cantitadpiezas').prop('disabled', false);
                $('#Cantitadpiezas').val('');
            }
        });
        $('#btnGrupoPiezasCorte').click(function() {
            const btn = document.getElementById("btnGrupoPiezasCorte");
            event.preventDefault();
            Cantitadpiezas=$('#Cantitadpiezas');
            errorCantidad=$('#error_cantidad');
            CantitadpiezasIdOF=$('#CantitadpiezasIdOF').val();
            if(CantitadpiezasIdOF=='' || CantitadpiezasIdOF==null){
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
            btn.disabled = true; 
            $.ajax({
                url: "{{route('GuardarCorte')}}", 
                type: 'POST',
                data: {
                    id:CantitadpiezasIdOF,
                    retrabajo:'Normal',
                    Cantitadpiezas:Cantitadpiezas.val(),
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {

                },
                success: function(response) {
                    if(response.status=="success"){
                        success('Guardado correctamente!',response.message);
                        RecargarTabla();
                        RecargarTablaCerradas();
                        Planear(response.OF);
                    }else if(response.status=="error"){
                        error('Error',response.message);
                    }else if(response.status=="errorCantidada"){
                        error('Cantidad no disponible!',response.message);
                    }
                     btn.disabled = false;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    error('Error Server',jqXHR.responseJSON.message);
                     btn.disabled = false;
                }
            });
        });
        $('#btnGrupoPiezasCorte1').click(function() {
            const btn = document.getElementById("btnGrupoPiezasCorte1");
            event.preventDefault();
            Cantitadpiezas=$('#Cantitadpiezas');
            errorCantidad=$('#error_cantidad');
            EmisionesOpciones=$('#EmisionesOpciones');
            errorEmision=$('#error_emision');
            Retrabajo=$('#Retrabajo');
            errorRetrabajo=$('#error_retrabajo');
            CantitadpiezasIdOF=$('#CantitadpiezasIdOF').val();
            if(CantitadpiezasIdOF=='' || CantitadpiezasIdOF==null){
                error('Partida no guardada','No fue posible guardar los datos de la partida, todos los campos son requeridos');
                return 0;
            }
            if(Cantitadpiezas.val()=="" || Cantitadpiezas.val()==null || Cantitadpiezas.val()==0){
                Cantitadpiezas.addClass('is-invalid');
                errorCantidad.text('Por favor, selecciona una Orden de Producción valida.');
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
            if(!Retrabajo.is(':checked')){
                Retrabajo.addClass('is-invalid');
                errorRetrabajo.text('Campo requerido, Es necesario marcar la casilla.');
                errorRetrabajo.show();
                return 0; 
            }else{
                Retrabajo.removeClass('is-invalid');
                errorRetrabajo.text('');
                errorRetrabajo.hide(); 
            }
            btn.disabled = true; 
            $.ajax({
                url: "{{route('GuardarCorte')}}", 
                type: 'POST',
                data: {
                    id:CantitadpiezasIdOF,
                    emision:EmisionesOpciones.val(),
                    retrabajo:'Retrabajo',
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
                    btn.disabled = false; 
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    error('Error Server',jqXHR.responseJSON.message);
                    btn.disabled = false;
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
            // Mostrarlo en la consola o hacer algo con el valor
            $('#Cantitadpiezas').val(cantidad);
        });
        RecargarTabla();
        RecargarTablaCerradas();
        setInterval(RecargarTabla, 600000);//180000);
        $('#procesoTable').show();
    });
    function RecargarTabla(){
        $('#procesoTable').DataTable({
            destroy: true,
            //processing: true,
            //serverSide: true,
            ajax: {
                url: "{{ route('CorteRecargarTabla') }}",
                dataSrc: 'data',
                error: function(xhr, error, thrown) {
                    errorBD();
                }
            },
            columns: [
                { data: 'OrdenFabricacion' },
                { data: 'Responsable_Corte' },
                { data: 'Articulo' },
                { data: 'Descripcion' },
                { data: 'Piezas_Cortadas_Normal' },
                { data: 'Piezas_Cortadas_Retrabajo' },
                { data: 'Cantidad_Total' },
                { data: 'Fecha_Planeada' },
                { data: 'Estatus' },
                 { data: 'Accion' },
            ],
            columnDefs: [
                { targets: [5, 6, 7], orderable: false }, // Opcional: desactiva orden en acciones
                { targets: [5, 6, 7], searchable: false }, // Opcional: desactiva búsqueda
                { targets: [5, 6, 7], className: 'text-center' } // Centra los botones y checkbox
            ],
            language: {
                            "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                            "search":"Buscar",
                        },
            rowCallback: function(row, data, index) {
                if (data.Urgencia === 'U') {
                    $(row).css('background-color', '#8be0fc');
                }
            },
            lengthChange: false,
        });
        $('.dt-layout-start:first').append(
        '<button class="btn btn-info mb-0" data-bs-toggle="modal" data-bs-target="#ModalRetrabajo" onclick="LimpiarOF()"><i class="fas fa-plus"></i> Retrabajo</button>');
        /*$.ajax({
            url: "{{route('CorteRecargarTabla')}}", 
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status=="success"){
                    $('#procesoTable').DataTable().destroy();
                    $('#procesoTableBody').html(response.table);
                    DataTable('procesoTable',true);
                    $('.dt-layout-start:first').append(
                    '<button class="btn btn-info mb-0" data-bs-toggle="modal" data-bs-target="#ModalRetrabajo" onclick="LimpiarOF()"><i class="fas fa-plus"></i> Retrabajo</button>');
                }
            }
        });*/
    }
    function Planear(OrdenFabricacion){
        $('#Retrabajo').prop('checked', false);
        $('#Retrabajo').prop('disabled', false);
        $('#Cantitadpiezas').prop('disabled', false);
        $('#Cantitadpiezas').val('');
        $('#Emisiones').fadeOut(100);
        $('#ModalSuministro').modal('show');
        $('#ModalSuministroBodyInfoOF').html('');
        $('#ModalSuministroBodyPartidasOF').html('');
        $('#CantitadpiezasIdOF').val('');
        $('#btnGrupoPiezasCorte').fadeIn();
        $('#InputNormal').fadeOut();
        $('#btnGrupoPiezasCorte1').fadeOut();
        $('#Cantitadpiezas').prop('disabled', false);
        $('#EmisionesOpciones').prop('disabled', false);
        //$('#cantidadPiezas').prop('disabled', true);
        document.getElementById("Retrabajo").checked=false;
        $.ajax({
            url: "{{route('CortesDatosModal')}}", 
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
                    $('#CantitadpiezasIdOF').val(response.id);
                    $('#ModalSuministroBodyInfoOF').html(response.Ordenfabricacioninfo);
                    $('#ModalSuministroBodyPartidasOF').html(response.Ordenfabricacionpartidas);
                    $('#InputNormal').fadeIn();
                    //$('#TablePartidasModal').DataTable().destroy();
                    //DataTable('TablePartidasModal',false);
                }else if(response.status=='successnotcable'){
                    $('#ModalSuministroBodyInfoOF').html(response.Ordenfabricacioninfo);
                }
                if(response.statusOF == '0'){
                    $('#Cantitadpiezas').prop('disabled', true);
                    $('#EmisionesOpciones').prop('disabled', true);
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
        OrdenFabricacion=$('#CantitadpiezasIdOF').val();
        $('#EmisionesOpciones').html('');
        $.ajax({
            url: "{{route('TraerEmisiones')}}", 
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
    function Cancelar(id,Numpartida){
        confirmacion('Cancelar Partida','¿Desea cancelar la partida Número '+Numpartida+'?','Confirmar','CancelarAccion(\''+id+'\')');
    }
    function CancelarAccion(id){
        $.ajax({
            url: "{{route('CancelarCorte')}}", 
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
            url: "{{route('FinalizarCorte')}}", 
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
    function DataTable(tabla, busqueda){
        $('#'+tabla).DataTable({
                        stateSave: true,
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
    function etiquetaColor(id,ruta){
        document.getElementById('pdfIframe').src = "";
        if(ruta==1)
        var url = "{{ route('generarPDF')}}?id=_corteId_";
        else if(ruta == 2){
            var url = "{{ route('generarPDF45X25')}}?id=_corteId_";
        }else{
            var url = "{{ route('generarPDF110X20')}}?id=_corteId_";
        }
        Coloretiqueta=$('#Coloretiqueta').val();
        //var url = "{{ route('generar.pdf')}}?id=_corteId_";
        url = url.replace('_corteId_', id);
        // Asignar la URL al iframe para mostrar el PDF
        document.getElementById('pdfIframe').src = url;
        // Abrir el modal con el iframe
        $('#pdfModal').modal('show');
            // Abre la URL para descargar el PDF
            /*var ventana = window.open(url, '_blank');
            $('#ModalColor').modal('hide');*/
    }
    function Detalles(id){
        $('#ModalDetalle').modal('show');
        $('#ModalDetalleBodyInfoOF').html('');
        $('#ModalDetalleBodyPartidasOF').html('');
        document.getElementById("Retrabajo").checked=false;
        $.ajax({
            url: "{{route('CortesDatosModal')}}", 
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
                    $('#CantitadpiezasIdOF').val(response.id);
                    $('#ModalDetalleBodyInfoOF').html(response.Ordenfabricacioninfo);
                    $('#ModalDetalleBodyPartidasOF').html(response.Ordenfabricacionpartidas);
                    //$('#TablePartidasModal').DataTable().destroy();
                    //DataTable('TablePartidasModal',false);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#ModalDetalleBodyInfoOF').html('');
                $('#ModalDetalleBodyPartidasOF').html('');
                errorBD();
            }
        });
    }
    function LimpiarOF(){
        $('#RetrabajoOF').val('');
    }
    function RetrabajoMostrarOFBuscar(RetrabajoOF){
        $('#RetrabajoOFOpciones').html('');
        if(RetrabajoOF.value==""){
            return 0;
        }if((RetrabajoOF.value).length<5){
            return 0;
        }
        $.ajax({
            url: "{{route('BuscarCorte')}}", 
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
        TraerEmisiones();
    }
    function RecargarTablaCerradas(){
        fechainicio=$('#inputFechaInicio');
        fechafin=$('#inputFechaFin');
        if(fechafin.val()<fechainicio.val()){
                return 0; 
            }
        $.ajax({
            url: "{{route('CorteRecargarTablaCerrada')}}", 
            type: 'GET',
            data: {
                fechainicio:fechainicio.val(),
                fechafin:fechafin.val(),
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#completadoTableBody').html("<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'>"+
                        "<span class='visually-hidden'>Loading...</span></div></div></td></tr>");
            },
            success: function(response) {
                if(response.status=="success"){
                    $('#completadoTable').DataTable().destroy();
                    $('#completadoTableBody').html(response.table);
                    DataTable('completadoTable',true);
                }
            }
        });
    }
</script>
@endsection
