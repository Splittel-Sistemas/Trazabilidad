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
                <div class="mt-2" id="ModalDetalleBody">
                    <div class="p-1">
                        <div class="row border border-light">
                            <div class="col-12">
                                <h5 class="text-center pt-1" id="TextoDetallesCliente"></h5>
                                <h6 class="text-center text-muted" id="TextoDetallesOV"></h6>
                            </div>
                            <div class="col-4 border border-light">
                                <label class="form-label mt-2" for="Sociedad">Tipo de Etiqueta</label>
                                <input class="form-control" onclick="" oninput="BuscarEtiquetaFiltro(this);" type="text" id="Etiquetaitems" placeholder="buscar" style="display:none;">
                                <select id="TipoEtiqueta" onchange="Etiqueta(this);" class="form-select" style="width: 100%;">
                                    <option value="" disabled>Selecciona una Opci&oacute;n</option>
                                    <option value="ETIQ1">ETIQUETA ESPECIAL HUAWEI</option>
                                    <option value="ETIQ2">ETIQUETA DE BANDERILLA QR GENERAL</option>
                                    <option value="ETIQ3">ETIQUETA DE BANDERILLA QR NÚMERO ESPECIAL</option>
                                    <option value="ETIQ4">ETIQUETA DE BOLSA JUMPER</option>
                                    <option value="ETIQ4CEDIS">ETIQUETA DE BOLSA JUMPER CEDIS</option>
                                    <option value="ETIQ5">ETIQUETA DE NÚMERO DE PIEZAS</option>
                                </select>
                                <div id="ListaOpciones" class="list-group" style="display:none;">
                                    <a onclick="SeleccionarCampo('ETIQ1')" class="list-group-item list-group-item-action active" style="font-size: 12px; padding: 4px 8px;">ETIQUETA ESPECIAL HUAWEI</a>
                                    <a onclick="SeleccionarCampo('ETIQ2')" class="list-group-item list-group-item-action" style="font-size: 12px; padding: 4px 8px;">ETIQUETA DE BANDERILLA QR GENERAL</a>
                                    <a onclick="SeleccionarCampo('ETIQ3')" class="list-group-item list-group-item-action" style="font-size: 12px; padding: 4px 8px;">ETIQUETA DE BANDERILLA QR GENERAL ESPECIAL</a>
                                    <a onclick="SeleccionarCampo('ETIQ4')" class="list-group-item list-group-item-action" style="font-size: 12px; padding: 4px 8px;">ETIQUETA DE BOLSA JUMPER</a>
                                    <a onclick="SeleccionarCampo('ETIQ4CEDIS')" class="list-group-item list-group-item-action" style="font-size: 12px; padding: 4px 8px;">ETIQUETA DE BOLSA JUMPER CEDIS</a>
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
                                    <h6 class="modal-title text-white" id="ModalDetalleLabel">PDF Etiquetas <span id="PdfEspinner"></span></h6>
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
            TextoDetallesOV.innerHTML = "Orden de Venta: "+data.OrdenVenta+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+"Orden Fabricación: "+data.OrdenFabricacion;
            TextoDetallesCliente.innerHTML = data.Cliente+"   ("+data.CodigoCliente+")";
        })
        .catch(error1 => {
            $('#ModalDetalle').modal('hide');    
            error('Error: ',error1);
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
            case 'ETIQ4CEDIS':
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
        SpinnerInsert('PdfEspinner');
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
            document.getElementById('PdfEspinner').innerHTML = "";
            if('error' in data){
                PdfAlerta.style.display = "";
                PdfAlerta.innerHTML = '<i class ="far fa-times-circle"></i>   Error: '+data.error;
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
            document.getElementById('PdfEspinner').innerHTML = "";
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
@endsection
