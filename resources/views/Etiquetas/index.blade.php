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
                <input type="text" oninput="RegexNumeros(this)" class="form-control form-control-sm" autocomplete="off" id="NumeroOrden" placeholder="Número de Orden de Fabricación">
                <button class="btn btn-outline-primary btn-sm" id="Btn-BuscarOrden" onclick="BuscarOF()">Buscar</button>
            </div>
            <div class="list-group lista-busqueda" id="ListaBusquedas" style="max-height: 20rem;overflow-y: auto;display: none;">
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
                                </select>
                                <div id="ListaOpciones" class="list-group" style="display:none;">
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
                                            <label class="form-label" for="PaginaInicio">Etiqueta inicio </label>
                                            <input class="form-control" id="PaginaInicio" type="number" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorPaginaFin">
                                        <div class="mb-3">
                                            <label class="form-label" for="PaginaFin">Etiqueta fin  </label>
                                            <input class="form-control" id="PaginaFin" type="number" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorCantidadCajas">
                                        <div class="mb-3">
                                            <label class="form-label" for="CantidadCajas">Cantidad de Cajas  </label>
                                            <input class="form-control" id="CantidadCajas" type="number" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorCantidadBolsa">
                                        <div class="mb-3">
                                            <label class="form-label" for="CantidadBolsa">Cantidad por bolsa o caja  </label>
                                            <input class="form-control" id="CantidadBolsa" type="number" placeholder="0" value="1" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorPorcentajeA" style="display: none">
                                        <div class="mb-3">
                                            <label class="form-label" for="PorcentajeA">Medida 1  </label>
                                            <input class="form-control" id="PorcentajeA" type="number" oninput="ValorDivisor(this, 'A')" placeholder="0" min="1" max="100" value="50" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorPorcentajeB" style="display: none">
                                        <div class="mb-3">
                                            <label class="form-label" for="PorcentajeB">Medida 2  </label>
                                            <input class="form-control" id="PorcentajeB" type="number" oninput="ValorDivisor(this, 'B')" placeholder="0" min="1" max="100" value="50" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorPorcentajeC" style="display: none">
                                        <div class="mb-3">
                                            <label class="form-label" for="PorcentajeC">Medida 3  </label>
                                            <input class="form-control" id="PorcentajeC" type="number" oninput="ValorDivisor(this, 'C')" placeholder="0" min="1" max="100" value="50" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorPorcentajeD" style="display: none">
                                        <div class="mb-3">
                                            <label class="form-label" for="PorcentajeD">Medida 4  </label>
                                            <input class="form-control" id="PorcentajeD" type="number" oninput="ValorDivisor(this, 'D')" placeholder="0" min="1" max="100" value="50" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorInsercion" style="display: none">
                                        <div class="mb-3">
                                            <label class="form-label" for="Insercion">Insercion  </label>
                                            <input class="form-control" id="Insercion" type="number" placeholder="0" value="0.50" />
                                        </div>
                                    </div>
                                    <div class="col-3" id="ContenedorRetorno" style="display: none">
                                        <div class="mb-3">
                                            <label class="form-label" for="Retorno">Retorno  </label>
                                            <input class="form-control" id="Retorno" type="number" placeholder="0" value="20.0" />
                                        </div>
                                    </div>
                                    <input type="hidden" id="CodigoCliente">
                                    <div class="col-3" id="ContenedorBoton">
                                        <div class="mt-3">
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
        $('#TipoEtiqueta').html("");
        $('#ListaOpciones').html("");
        InputPaginaFin = document.getElementById("PaginaFin");
        InputPaginaInicio = document.getElementById("PaginaInicio");
        InputCantidadEtiquetas = document.getElementById("CantidadEtiquetas");
        InputRetorno = document.getElementById("Retorno");
        InputInsercion = document.getElementById("Insercion");
        InputCantidadCajas = document.getElementById("CantidadCajas");
        ContenedorInsercion = document.getElementById("ContenedorInsercion");
        ContenedorRetorno = document.getElementById("ContenedorRetorno");
        ContenedorCantidadBolsa = document.getElementById("ContenedorCantidadBolsa");
        ContenedorCantidadCajas = document.getElementById("ContenedorCantidadCajas");
        ContenedorPaginaFin = document.getElementById("ContenedorPaginaFin");
        ContenedorPaginaInicio = document.getElementById("ContenedorPaginaInicio");
        ContenedorSociedad = document.getElementById("ContenedorSociedad");
        ContenedorPorcentajeA = document.getElementById("ContenedorPorcentajeA");
        ContenedorPorcentajeB = document.getElementById("ContenedorPorcentajeB");
        ContenedorPorcentajeC = document.getElementById("ContenedorPorcentajeC");
        ContenedorPorcentajeD = document.getElementById("ContenedorPorcentajeD");
        ContenedorBoton = document.getElementById("ContenedorBoton");
        ContenedorCantidadEtiquetas = document.getElementById("ContenedorCantidadEtiquetas");

        InputRetorno = 20.0;
        InputInsercion = 0.50;
        ContenedorInsercion.style.display = "none";
        ContenedorRetorno.style.display = "none";
        ContenedorCantidadBolsa.style.display = "none";
        ContenedorPaginaFin.style.display = "none";
        ContenedorPaginaInicio.style.display = "none";
        ContenedorSociedad.style.display = "none";
        ContenedorPorcentajeA.style.display = "none";
        ContenedorPorcentajeB.style.display = "none";
        ContenedorPorcentajeC.style.display = "none";
        ContenedorPorcentajeD.style.display = "none";
        ContenedorBoton.style.display = "none";
        ContenedorCantidadCajas.style.display = "none";
        ContenedorCantidadEtiquetas.style.display = "none";
        document.getElementById('pdfIframe').src = "";
        document.getElementById('pdfIframe').style.display = "none";
        TextoDetallesOV = document.getElementById("TextoDetallesOV");
        TextoDetallesCliente = document.getElementById("TextoDetallesCliente");
        TextoDetallesOV.innerHTML = "";
        TextoDetallesCliente.innerHTML = "";
        CodigoCliente = document.getElementById('CodigoCliente');
        CodigoCliente.value = "";
        URL = '{{ route("Etiquetas.show", ":OF")}}'.replace(":OF", OF);
        fetch(URL)
        .then(response => response.json())
        .then(data => {
            InputPaginaInicio.value = 1;
            InputPaginaFin.value = data.CantidadTotal;
            InputCantidadCajas.value = data.CantidadTotal;
            CantidadEtiquetas.value = data.CantidadTotal;
            TextoDetallesOV.innerHTML = "Orden de Venta: "+data.OrdenVenta+ "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+"Orden Fabricación: "+data.OrdenFabricacion;
            TextoDetallesCliente.innerHTML = data.Cliente+"   ("+data.CodigoCliente+")";
            CodigoCliente.value = data.CodigoCliente;
            $('#TipoEtiqueta').html(data.MenuOption);
            $('#ListaOpciones').html(data.MenuBtn);
            
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
        InputPorcentajeA = document.getElementById("PorcentajeA");
        InputPorcentajeB = document.getElementById("PorcentajeB");
        InputPorcentajeC = document.getElementById("PorcentajeC");
        InputPorcentajeD = document.getElementById("PorcentajeD");
        InputRetorno = document.getElementById("Retorno");
        InputInsercion = document.getElementById("Insercion");
        InputCantidadCajas = document.getElementById("CantidadCajas");

        InputCantidadBolsa.value = 1;
        InputPorcentajeA.value = 50;
        InputPorcentajeB.value = 50;
        InputPorcentajeC.value = 50;
        InputPorcentajeD.value = 50;
        InputRetorno.value = 20.0;
        InputInsercion.value = 0.50;
        InputCantidadCajas.value = 1;

        ContenedorInsercion = document.getElementById("ContenedorInsercion");
        ContenedorRetorno = document.getElementById("ContenedorRetorno");
        ContenedorCantidadBolsa = document.getElementById("ContenedorCantidadBolsa");
        ContenedorPaginaFin = document.getElementById("ContenedorPaginaFin");
        ContenedorPaginaInicio = document.getElementById("ContenedorPaginaInicio");
        ContenedorSociedad = document.getElementById("ContenedorSociedad");
        ContenedorPorcentajeA = document.getElementById("ContenedorPorcentajeA");
        ContenedorPorcentajeB = document.getElementById("ContenedorPorcentajeB");
        ContenedorPorcentajeC = document.getElementById("ContenedorPorcentajeC");
        ContenedorPorcentajeD = document.getElementById("ContenedorPorcentajeD");
        ContenedorBoton = document.getElementById("ContenedorBoton");
        ContenedorCantidadEtiquetas = document.getElementById("ContenedorCantidadEtiquetas");
        ContenedorCantidadCajas = document.getElementById("ContenedorCantidadCajas");

        TituloEtiqueta = document.getElementById("TituloEtiqueta");
        ContenedorInsercion.style.display = "none";
        ContenedorRetorno.style.display = "none";
        ContenedorPaginaFin.style.display = "none";
        ContenedorPaginaInicio.style.display = "none";
        ContenedorBoton.style.display = "none";
        ContenedorSociedad.style.display = "none";
        ContenedorCantidadEtiquetas.style.display = "none";
        ContenedorCantidadBolsa.style.display = "none";
        ContenedorPorcentajeA.style.display = "none";
        ContenedorPorcentajeB.style.display = "none";
        ContenedorPorcentajeC.style.display = "none";
        ContenedorPorcentajeD.style.display = "none";
        ContenedorCantidadCajas.style.display = "none";
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
            case 'ETIQ6':
                ContenedorPaginaInicio.style.display = "";
                ContenedorPaginaFin.style.display = "";
                ContenedorBoton.style.display = "";
                ContenedorInsercion.style.display = "";
                ContenedorRetorno.style.display = "";
                TituloEtiqueta.innerHTML = "ETIQUETA DE TRAZABILIDAD MPO";
                break;
            case 'ETIQ7':
                ContenedorCantidadEtiquetas.style.display = "";
                ContenedorBoton.style.display = "";
                TituloEtiqueta.innerHTML = "ETIQUETA DE INYECCIÓN";
                break;
            case 'ETIQ8':
                ContenedorCantidadEtiquetas.style.display = "";
                ContenedorBoton.style.display = "";
                ContenedorPorcentajeA.style.display = "";
                ContenedorPorcentajeB.style.display = "";
                ContenedorPorcentajeC.style.display = "";
                ContenedorPorcentajeD.style.display = "";
                TituloEtiqueta.innerHTML = "ETIQUETA DE DIVISOR";
                break;
            case 'ETIQ9':
                ContenedorPaginaFin.style.display = "";
                ContenedorPaginaInicio.style.display = "";
                ContenedorCantidadBolsa.style.display = "";
                ContenedorCantidadCajas.style.display = "";
                ContenedorBoton.style.display = "";
                TituloEtiqueta.innerHTML = "ETIQUETA DE CAJA HUAWEI";
                break;
            case 'ETIQ10':
                ContenedorPaginaFin.style.display = "";
                ContenedorPaginaInicio.style.display = "";
                ContenedorCantidadBolsa.style.display = "";
                ContenedorCantidadCajas.style.display = "";
                ContenedorBoton.style.display = "";
                TituloEtiqueta.innerHTML = "ETIQUETA DE CAJA NOKIA";
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
        InputPorcentajeA = document.getElementById("PorcentajeA");
        InputPorcentajeB = document.getElementById("PorcentajeB");
        InputPorcentajeC = document.getElementById("PorcentajeC");
        InputPorcentajeD = document.getElementById("PorcentajeD");
        InputCodigoCliente = document.getElementById("CodigoCliente");
        InputCantidadCajas = document.getElementById("CantidadCajas");
        CantidadEtiquetas = document.getElementById("CantidadEtiquetas");
        InputRetorno = document.getElementById("Retorno");
        InputInsercion = document.getElementById("Insercion");
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
            TipoEtiqueta:InputTipoEtiqueta,
            PorcentajeA:InputPorcentajeA.value,
            PorcentajeB:InputPorcentajeB.value,
            PorcentajeC:InputPorcentajeC.value,
            PorcentajeD:InputPorcentajeD.value,
            CodigoCliente:InputCodigoCliente.value,
            Insercion:InputInsercion.value,
            Retorno:InputRetorno.value,
            CantidadCajas:InputCantidadCajas.value,

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
    function ValorDivisor(elemento, id){
        if(elemento.value > 100){
            elemento.value = 100;
        }
        if(elemento.value < 0){
            elemento.value = 0;
        }
        PorcentajeA = document.getElementById('PorcentajeA');
        PorcentajeB = document.getElementById('PorcentajeB');
        PorcentajeA = document.getElementById('PorcentajeA');
        PorcentajeB = document.getElementById('PorcentajeB');
        if(id=='A'){
            PorcentajeB.value = (100-elemento.value);
        }else{
            //PorcentajeA.value = (100-elemento.value).toFixed(2);
        }
        if(id=='C'){
            PorcentajeD.value = (100-elemento.value);
        }else{
            //PorcentajeA.value = (100-elemento.value).toFixed(2);
        }
    }
    document.addEventListener("keydown", function(event) {
      if (event.key === "Enter") {
        document.getElementById("Btn-BuscarOrden").click();
      }
    });
</script>
@endsection
