@extends('layouts.menu2')
@section('title', 'Etiquetas')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .borde {
          border: 1px solid black;
        }
</style>
@endsection
@section('content')
    <div class="breadcrumbs mb-3">
        <div class="row gy-3 mb-2 justify-content-between">
            <div class="col-md-9 col-auto">
                <h4 class="mb-2 text-1100">Etiquetas</h4>
            </div>
        </div>
        <!--<div class="alert alert-outline-info d-flex align-items-center" role="alert">
            <span class="fas fa-info-circle text-info fs-1 me-3"></span>
            <p class="mb-0 flex-1"> Las etiquetas, solo se pueden generar de acuerdo a parametros establecidos!</p>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>-->
    </div>
    <div class="row">
        <div class="mb-2 col-6">
            <label for="NumeroOrden" class="form-label">Ingresa orden de Fabricación</label>
            <div class="input-group">
                <input type="text" oninput="RegexNumeros(this)" class="form-control form-control-sm" id="NumeroOrden" placeholder="Número de Orden de Fabricación">
                <button class="btn btn-outline-primary btn-sm" id="Btn-BuscarOrden" onclick="BuscarOF()">Buscar</button>
            </div>
            <div class="list-group lista-busqueda" id="ListaBusquedas" style="display: none;">
            </div>
        </div>
    </div>
    <div class="modal fade" id="ModalDetalle" tabindex="-1" data-bs-backdrop="static" aria-labelledby="ModalDetalleLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="height: 90%">
          <div class="modal-content" style="height: 100%">
            <div class="modal-header bg-info">
              <h5 class="modal-title text-white" id="ModalDetalleLabel">Etiquetas</h5><button class="btn" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-white"></span></button>
            </div>
            <div class="modal-body" id="ModalDetalleBody">
                <div class="alert alert-outline-info d-flex align-items-center py-1" role="alert">
                    <span class="fas fa-info-circle text-info fs-3 me-1"></span>
                    <p class="mb-0 flex-1">Selecciona el tipo de etiqueta que requieres generar</p>
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <h5 class="text-center text-muted" id="TextoDetallesOV"></h5>
                <h5 class="text-center text-muted" id="TextoDetallesCliente"></h5>
                <div class="mt-2" id="ModalDetalleBody">
                    <div class="p-1">
                        <div class="row border border-light">
                            <div class="col-4 border border-light">
                                <label class="form-label mt-2" for="Sociedad">Tipo de Etiqueta</label>
                                <input class="form-control" onclick="" oninput="BuscarEtiquetaFiltro(this);" type="text" id="Etiquetaitems" placeholder="buscar" style="display:none;">
                                <select id="TipoEtiqueta" onchange="Etiqueta(this);" class="form-select" style="width: 100%;">
                                    <option value="" disabled>Selecciona una Opci&oacute;n</option>
                                    <option value="ETIQ1">ETIQUETA ESPECIAL HUAWEI</option>
                                    <option value="ETIQ2">ETIQUETA DE BANDERILLA QR GENERAL</option>
                                    <option value="ETIQ3">ETIQUETA DE BANDERILLA QR NÚMERO ESPECIAL</option>
                                    <option value="ETIQ4">ETIQUETA DE BOLSA JUMPER</option>
                                    <option value="ETIQ5">ETIQUETA DE NÚMERO DE PIEZAS</option>
                                </select>
                                <div id="ListaOpciones" class="list-group" style="display:none;">
                                    <a onclick="SeleccionarCampo('ETIQ1')" class="list-group-item list-group-item-action active" style="font-size: 12px; padding: 4px 8px;">ETIQUETA ESPECIAL HUAWEI</a>
                                    <a onclick="SeleccionarCampo('ETIQ2')" class="list-group-item list-group-item-action" style="font-size: 12px; padding: 4px 8px;">ETIQUETA DE BANDERILLA QR GENERAL</a>
                                    <a onclick="SeleccionarCampo('ETIQ3')" class="list-group-item list-group-item-action" style="font-size: 12px; padding: 4px 8px;">ETIQUETA DE BANDERILLA QR GENERAL ESPECIAL</a>
                                    <a onclick="SeleccionarCampo('ETIQ4')" class="list-group-item list-group-item-action" style="font-size: 12px; padding: 4px 8px;">ETIQUETA DE BOLSA JUMPER</a>
                                    <a onclick="SeleccionarCampo('ETIQ5')" class="list-group-item list-group-item-action" style="font-size: 12px; padding: 4px 8px;">ETIQUETA DE NÚMERO DE PIEZAS</a>
                                </div>
                            </div>
                            <div class="col-8">
                                <div id="DatosEtiquetas" class="row mt-2">
                                    <div class="col-12">
                                        <div class="alert alert-danger p-2" role="alert" id="PdfAlerta" style="display: none">
                                        </div>
                                        <h5 class="text-center" id="TituloEtiqueta"></h5>
                                    </div>
                                    <div class="col-3" id="ContenedorSociedad">
                                        <div class="mb-3">
                                            <label class="form-label" for="Sociedad">Logo</label>
                                                <select class="form-select" id="Sociedad" data-choices="data-choices" size="1" required="required" name="organizerSingle" data-options='{"removeItemButton":true,"placeholder":true}'>
                                                    <option value="" disabled>Selecciona una Opci&oacute;n</option>
                                                    <option value="FMX">Fibremex</option>
                                                    <option value="OPT">Optronics</option>
                                                </select>
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorCantidadEtiquetas">
                                        <div class="mb-3">
                                            <label class="form-label" for="CantidadEtiquetas">Cantidad de Etiquetas  </label>
                                            <input class="form-control" id="CantidadEtiquetas" type="number" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorPaginaInicio">
                                        <div class="mb-3">
                                            <label class="form-label" for="PaginaInicio">P&aacute;gina inicio </label>
                                            <input class="form-control" id="PaginaInicio" type="number" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorPaginaFin">
                                        <div class="mb-3">
                                            <label class="form-label" for="PaginaFin">P&aacute;gina fin  </label>
                                            <input class="form-control" id="PaginaFin" type="number" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorCantidadBolsa">
                                        <div class="mb-3">
                                            <label class="form-label" for="CantidadBolsa">Cantidad por bolsa  </label>
                                            <input class="form-control" id="CantidadBolsa" type="number" placeholder="0" value="1" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorBoton">
                                        <div class="mt-4">
                                            <button type="button" id="BtnGenerar" class="btn btn-phoenix-primary me-1 mb-1">Generar</button>
                                        </div>
                                    </div>
                                </div>
                                <!--PDF Etiquetas-->
                                <div class="modal-header bg-dark p-2">
                                    <h6 class="modal-title text-white" id="ModalDetalleLabel">PDF Etiquetas</h6>
                                </div>
                                <!-- Aquí se cargará el PDF en un iframe -->
                                <iframe id="pdfIframe" src="" width="100%" height="300px" style="display: none"></iframe>
                                <p id="TextoSelecciona" class="text-center my-4"> <i class="far fa-file-pdf"></i> Selecciona una opci&oacute;n</p>
                                <input type="hidden" id="PDFOrdenFabricacion">
                                <input type="hidden" id="PDFEtiqueta">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="ModalDetalleFooter">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    window.CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
</script>
<script>
    AjaxOrden = null;
    $('#NumeroOrden').on('input', function() {
        NumeroOrden = $('#NumeroOrden').val();
         if (AjaxOrden && typeof AjaxOrden.abort === 'function') {
            AjaxOrden.abort();
        }
        if (AjaxOrden && AjaxOrden.readyState !== 4) {
                AjaxOrden.abort();
                console.log("Petición anterior cancelada");
            }
        if(NumeroOrden.length<3){
            $('#ListaBusquedas').html('');
            $('#ListaBusquedas').hide();
            return 0;
        }
        TipoOrden = "OF";
        AjaxOrden = $.ajax({
            url: '{{ route("TipoOrden") }}',
            type: 'POST',
            data: { 
                NumeroOrden:NumeroOrden,
                TipoOrden:TipoOrden,
            },
            success: function (response) {
                $('#ListaBusquedas').html(response);
                if(response!=""){
                    $('#ListaBusquedas').show();
                }
            },
            error: function () {
                //alert('Error al obtener los datos de la venta.');
            }
        });
    });
    function BuscarOF(){
        $('#NumeroOrden').trigger('input');
    }
    function SeleccionarNumOrden(OF){
        $('#ListaBusquedas').hide();
        $('#ModalDetalle').modal('show');
        $('#ModalDetalleLabel').html('Etiquetas Orden de Fabricación '+OF);
        $('#PDFOrdenFabricacion').val(OF);
        $('#TextoSelecciona').show();
        $('#PdfAlerta').hide();
        $('#TipoEtiqueta').val("");
        $('#Etiquetaitems').hide();
        InputPaginaFin = document.getElementById("PaginaFin");
        InputPaginaInicio = document.getElementById("PaginaInicio");
        InputCantidadEtiquetas = document.getElementById("CantidadEtiquetas");
        ContenedorCantidadBolsa = document.getElementById("ContenedorCantidadBolsa");
        ContenedorPaginaFin = document.getElementById("ContenedorPaginaFin");
        ContenedorPaginaInicio = document.getElementById("ContenedorPaginaInicio");
        ContenedorSociedad = document.getElementById("ContenedorSociedad");
        ContenedorBoton = document.getElementById("ContenedorBoton");
        ContenedorCantidadEtiquetas = document.getElementById("ContenedorCantidadEtiquetas");
        ContenedorCantidadBolsa.style.display = "none";
        ContenedorPaginaFin.style.display = "none";
        ContenedorPaginaInicio.style.display = "none";
        ContenedorSociedad.style.display = "none";
        ContenedorBoton.style.display = "none";
        ContenedorCantidadEtiquetas.style.display = "none";
        document.getElementById('pdfIframe').src = "";
        document.getElementById('pdfIframe').style.display = "none";
        TextoDetallesOV = document.getElementById("TextoDetallesOV");
        TextoDetallesCliente = document.getElementById("TextoDetallesCliente");
        TextoDetallesOV.innerHTML = "";
        TextoDetallesCliente.innerHTML = "";
        URL = '{{ route("Etiquetas.show", ":OF")}}'.replace(":OF", OF);
        fetch(URL)
        .then(response => response.json())
        .then(data => {
            InputPaginaInicio.value = 1;
            InputPaginaFin.value = data.CantidadTotal;
            CantidadEtiquetas.value = data.CantidadTotal;
            TextoDetallesOV.innerHTML = "Orden de Venta: "+data.OrdenVenta+ "    "+"Orden Fabricación: "+data.OrdenFabricacion;
            TextoDetallesCliente.innerHTML = "Cliente:"+data.Cliente;
        })
        .catch(error1 => {
            $('#ModalDetalle').modal('hide');    
            error('Ocurrio un error',error1);
        });
    }
    function Etiqueta(TIPOETIQUETA){
        InputPDFOrdenFabricacion = document.getElementById("PDFOrdenFabricacion");
        InputCantidadBolsa = document.getElementById("CantidadBolsa");
        InputPaginaFin = document.getElementById("PaginaFin");
        InputPaginaInicio = document.getElementById("PaginaInicio");
        InputSociedad = document.getElementById("Sociedad");
        ContenedorCantidadBolsa = document.getElementById("ContenedorCantidadBolsa");
        ContenedorPaginaFin = document.getElementById("ContenedorPaginaFin");
        ContenedorPaginaInicio = document.getElementById("ContenedorPaginaInicio");
        ContenedorSociedad = document.getElementById("ContenedorSociedad");
        ContenedorBoton = document.getElementById("ContenedorBoton");
        ContenedorCantidadEtiquetas = document.getElementById("ContenedorCantidadEtiquetas");
        TituloEtiqueta = document.getElementById("TituloEtiqueta");
        ContenedorPaginaFin.style.display = "none";
        ContenedorPaginaInicio.style.display = "none";
        ContenedorBoton.style.display = "none";
        ContenedorSociedad.style.display = "none";
        ContenedorCantidadEtiquetas.style.display = "none";
        ContenedorCantidadBolsa.style.display = "none";
        BtnGenerar = document.getElementById("BtnGenerar");
        BtnGenerar.onclick = "";
        $('#PdfAlerta').hide();
        $('#Etiquetaitems').hide();
        document.getElementById('TipoEtiqueta').blur();
        BtnGenerar.onclick = function() {GenerarEtiquetas(InputPDFOrdenFabricacion.value,TIPOETIQUETA);};
        document.getElementById('pdfIframe').src = "";
        document.getElementById('pdfIframe').style.display = "none";
        switch(TIPOETIQUETA.value){
            case 'ETIQ1':
                ContenedorPaginaFin.style.display = "";
                ContenedorPaginaInicio.style.display = "";
                ContenedorBoton.style.display = "";
                TituloEtiqueta.innerHTML = "ETIQUETA ESPECIAL HUAWEI";
                break;
            case 'ETIQ2':
                ContenedorPaginaFin.style.display = "";
                ContenedorPaginaInicio.style.display = "";
                ContenedorBoton.style.display = "";
                TituloEtiqueta.innerHTML = "ETIQUETA DE BANDERILLA QR GENERAL";
                break;
            case 'ETIQ3':
                ContenedorPaginaFin.style.display = "";
                ContenedorPaginaInicio.style.display = "";
                ContenedorBoton.style.display = "";
                TituloEtiqueta.innerHTML = "ETIQUETA DE BANDERILLA QR NÚMERO ESPECIAL";
                break;
            case 'ETIQ4':
                ContenedorCantidadEtiquetas.style.display = "";
                ContenedorBoton.style.display = "";
                TituloEtiqueta.innerHTML = "ETIQUETA DE BOLSA JUMPER";
                break;
            case 'ETIQ5':
                ContenedorSociedad.style.display = "";
                ContenedorCantidadEtiquetas.style.display = "";
                ContenedorCantidadBolsa.style.display = "";   ContenedorSociedad.style.display = "";
                ContenedorCantidadEtiquetas.style.display = "";
                ContenedorCantidadBolsa.style.display = "";
                ContenedorBoton.style.display = "";
                TituloEtiqueta.innerHTML = "ETIQUETA DE NÚMERO DE PIEZAS";
                break;
            default:
                break;
        }
    }
    function GenerarEtiquetas(OF,TipoEtiqueta){
        InputPDFOrdenFabricacion = document.getElementById("PDFOrdenFabricacion");
        InputCantidadBolsa = document.getElementById("CantidadBolsa");
        InputPaginaFin = document.getElementById("PaginaFin");
        InputPaginaInicio = document.getElementById("PaginaInicio");
        InputSociedad = document.getElementById("Sociedad");
        CantidadEtiquetas = document.getElementById("CantidadEtiquetas");
        PdfAlerta = document.getElementById("PdfAlerta");
        InputTipoEtiqueta = document.getElementById("TipoEtiqueta").value;
        const URL = '{{ route("Etiquetas.Generar") }}';
        const payload = {
            PDFOrdenFabricacion: InputPDFOrdenFabricacion.value,
            CantidadBolsa: InputCantidadBolsa.value,
            PaginaFin: InputPaginaFin.value,
            PaginaInicio: InputPaginaInicio.value,
            Sociedad: InputSociedad.value,
            CantidadEtiquetas: CantidadEtiquetas.value,
            OF:OF,
            TipoEtiqueta:InputTipoEtiqueta

        };
        fetch(URL, {
            method: 'POST',
            credentials: 'same-origin', // importante para enviar cookies de sesión
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CSRF_TOKEN
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if('error' in data){
                PdfAlerta.style.display = "";
                PdfAlerta.innerHTML = '<i class ="far fa-times-circle"></i>   Ocurrio un error, '+data.error;
                document.getElementById('pdfIframe').style.display = "none";
                $('#TextoSelecciona').show();
            }else{
                PdfAlerta.style.display = "none";
                $('#TextoSelecciona').hide();
                url = "data:application/pdf;base64," + data.pdf;
                document.getElementById('pdfIframe').src = url;
                document.getElementById('pdfIframe').style.display = "";
            }
        })
        .catch(err => {
            $('#ModalDetalle').modal('hide');
            error('Ocurrió un error', err);
            document.getElementById('pdfIframe').style.display = "none";
            $('#TextoSelecciona').show();
        });
    }
    function BuscarEtiqueta(BuscarEtiqueta){
        document.getElementById('TipoEtiqueta').click();
    }
    document.getElementById('TipoEtiqueta').addEventListener('focus', () => {
        $('#Etiquetaitems').show();
        $('#ListaOpciones').hide();
    });
    document.getElementById('Etiquetaitems').addEventListener('focus', () => {
        $('#ListaOpciones').show();
    });
    function BuscarEtiquetaFiltro(input) {
        const filtro = input.value.toLowerCase();
        const lista = document.getElementById('ListaOpciones');
        const opciones = lista.querySelectorAll('.list-group-item');
        if (filtro.trim() === '') {
            opciones.forEach(opcion => {
                opcion.style.display = 'block';
            });
            lista.style.display = 'block';
            return;
        }
        let hayCoincidencias = false;
        opciones.forEach(opcion => {
            const texto = opcion.textContent.toLowerCase();
            if (texto.includes(filtro)) {
                opcion.style.display = 'block';
                hayCoincidencias = true;
            }else {
                opcion.style.display = 'none';
            }
        });
        // Mostrar u ocultar la lista según si hay coincidencias o si hay texto
        if (filtro !== '' && hayCoincidencias) {
            lista.style.display = 'block';
        } else {
            lista.style.display = 'none';
        }
    }
    function SeleccionarCampo(valor){
        $('#TipoEtiqueta').val(valor).change();
        $('#ListaOpciones').hide();
    }
</script>

{{--<script>
    $(document).ready(function() {
        //$('#procesoTableBody').html('{{$OrdenesFabricacionCerradas}}');
        DataTable('procesoTable',true);
        $('.dt-layout-start:first').append(
        '<button class="btn btn-info mb-0" data-bs-toggle="modal" data-bs-target="#ModalRetrabajo" onclick="LimpiarOF()"><i class="fas fa-plus"></i> Retrabajo</button>');
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
                errorCantidad.text('Por favor, selecciona una Orden de fabricación valida.');
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
        setInterval(RecargarTabla, 300000);//180000);
        $('#procesoTable').show();
    });
    function RecargarTabla(){
        $.ajax({
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
        });
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
                        }/*,
                        "stateSave": true, // Mantener el estado (página, búsqueda, etc.) entre recargas
                        "stateSaveCallback": function(settings, data) {
                            // Esto guarda el estado actual en localStorage (opcional si no usas un backend)
                            localStorage.setItem('datatableState_' + tabla, JSON.stringify(data));
                        },
                        "stateLoadCallback": function(settings) {
                            // Esto carga el estado desde localStorage (opcional si no usas un backend)
                            var state = localStorage.getItem('datatableState_' + tabla);
                            return state ? JSON.parse(state) : null;
                        }*/
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
        /*$('#Cantitadpiezas').val('');
        $('#Emisiones').fadeOut(100);
        $('#ModalSuministro').modal('show');*/
        $('#ModalDetalle').modal('show');
        $('#ModalDetalleBodyInfoOF').html('');
        $('#ModalDetalleBodyPartidasOF').html('');
        /*$('#CantitadpiezasIdOF').val('');
        $('#btnGrupoPiezasCorte').fadeIn();
        $('#btnGrupoPiezasCorte1').fadeOut();*/
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
</script>--}}
@endsection
