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
                                <input class="form-control" autocomplete="off" onclick="" oninput="BuscarEtiquetaFiltro(this);" type="text" id="Etiquetaitems" placeholder="buscar" style="display:none;">
                                <select id="TipoEtiqueta" onchange="Etiqueta(this);" class="form-select" style="width: 100%;">
                                </select>
                                <div id="ListaOpciones" class="list-group" style="display:none;">
                                </div>
                            </div>
                            <div class="col-8">
                                <div id="DatosEtiquetas" class="row mt-1">
                                    <div class="col-12">
                                        <div class="alert alert-danger p-2" role="alert" id="PdfAlerta" style="display: none">
                                        </div>
                                        <h5 class="text-center" id="TituloEtiqueta"></h5>
                                    </div>
                                    <div class="row" id="CamposEtiquetas">

                                    </div>
                                    <input type="hidden" id="CodigoCliente">
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
        CamposEtiquetas = document.getElementById('CamposEtiquetas');
        CamposEtiquetas.innerHTML = "";
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

        document.getElementById('pdfIframe').src = "";
        document.getElementById('pdfIframe').style.display = "none";
        TextoDetallesOV = document.getElementById("TextoDetallesOV");
        TextoDetallesCliente = document.getElementById("TextoDetallesCliente");
        TextoDetallesOV.innerHTML = "";
        TextoDetallesCliente.innerHTML = "";
        CodigoCliente = document.getElementById('CodigoCliente');
        CodigoCliente.value = "";
        TituloEtiqueta = document.getElementById("TituloEtiqueta");
        TituloEtiqueta.innerHTML = "";
        URLcontroller = '{{ route("Etiquetas.show", ":OF")}}'.replace(":OF", OF);
        fetch(URLcontroller)
        .then(response => response.json())
        .then(data => {
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
        CamposEtiquetas = document.getElementById('CamposEtiquetas');
        CamposEtiquetas.innerHTML = "";

        InputPDFOrdenFabricacion = document.getElementById("PDFOrdenFabricacion");
        TituloEtiqueta = document.getElementById("TituloEtiqueta");
        TituloEtiqueta.innerHTML = "";
        $('#PdfAlerta').hide();
        $('#Etiquetaitems').hide();
        document.getElementById('TipoEtiqueta').blur();
        document.getElementById('pdfIframe').src = "";
        document.getElementById('pdfIframe').style.display = "none";
        InputCodigoCliente = document.getElementById("CodigoCliente").value;
        const payload = {
            TipoEtiqueta: TIPOETIQUETA.value,
            OrdenFabricacion: InputPDFOrdenFabricacion.value,
            CodigoCliente: InputCodigoCliente,
        };
        URLcontroller = '{{ route("Etiquetas.Campos") }}';
        fetch(URLcontroller, {
            method: 'POST',
            credentials: 'same-origin', // importante para enviar cookies de sesión
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.CSRF_TOKEN
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            TituloEtiqueta.innerHTML = data.Titulo;
            CamposEtiquetas.innerHTML = data.Menu;
            BtnGenerar = document.getElementById('BtnGenerar');
            BtnGenerar.onclick = function() {GenerarEtiquetas(InputPDFOrdenFabricacion.value,TIPOETIQUETA);};

        })
        .catch(err => {
            //console.error('Error en la petición:', err);
        });
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
        InputTipoDistribuidor = document.getElementById("TipoDistribuidor");
        InputMenuDistribuidor = document.getElementById("MenuDistribuidor");
        PdfAlerta = document.getElementById("PdfAlerta");
        InputTipoEtiqueta = document.getElementById("TipoEtiqueta").value;
        document.getElementById('pdfIframe').src = "";
        //Errores
        ErrorPaginaFin = document.getElementById('ErrorPaginaFin');
        if (ErrorPaginaFin) {
            ErrorPaginaFin.innerHTML = "";
        }
        ErrorPaginaInicio = document.getElementById('ErrorPaginaInicio');
        if (ErrorPaginaInicio) {
            ErrorPaginaInicio.innerHTML = "";
        }
        ErrorCantidadBolsa = document.getElementById('ErrorCantidadBolsa');
        if (ErrorCantidadBolsa) {
            ErrorCantidadBolsa.innerHTML = "";
        }
        ErrorCantidadEtiquetas = document.getElementById('ErrorCantidadEtiquetas');
        if (ErrorCantidadEtiquetas) {
            ErrorCantidadEtiquetas.innerHTML = "";
        }
        ErrorInsercion = document.getElementById('ErrorInsercion');
        if (ErrorInsercion) {
            ErrorInsercion.innerHTML = "";
        }
        ErrorRetorno = document.getElementById('ErrorRetorno');
        if (ErrorRetorno) {
            ErrorRetorno.innerHTML = "";
        }
        ErrorCantidadCajas = document.getElementById("ErrorCantidadCajas");
        if (ErrorCantidadCajas) {
            ErrorCantidadCajas.innerHTML = "";
        }
        ErrorTipoDistribuidor = document.getElementById("ErrorTipoDistribuidor");
        if (ErrorTipoDistribuidor) {
            ErrorTipoDistribuidor.innerHTML = "";
        }
        ErrorMenuDistribuidor = document.getElementById("ErrorMenuDistribuidor");
        if (ErrorMenuDistribuidor) {
            ErrorMenuDistribuidor.innerHTML = "";
        }
        if(InputTipoEtiqueta == "ETIQ1" || InputTipoEtiqueta == "ETIQ3"){
            if(InputPaginaInicio.value == 0 || InputPaginaInicio.value == ""){
                ErrorPaginaInicio.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputPaginaFin.value == 0 || InputPaginaFin.value == ""){
                ErrorPaginaFin.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputPaginaFin.value<InputPaginaInicio.value){
                ErrorPaginaInicio.innerHTML = "El valor inicio no puede ser mayor a valor fin.";
                return 0;
            }
        }else if(InputTipoEtiqueta == "ETIQ2" || InputTipoEtiqueta == "ETIQ15"){
            if(InputPaginaInicio.value == 0 || InputPaginaInicio.value == ""){
                ErrorPaginaInicio.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputPaginaFin.value == 0 || InputPaginaFin.value == ""){
                ErrorPaginaFin.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputPaginaFin.value<InputPaginaInicio.value){
                ErrorPaginaInicio.innerHTML = "El valor inicio no puede ser mayor a valor fin.";
                return 0;
            }

        }else if(InputTipoEtiqueta == "ETIQ5"){
            if(CantidadEtiquetas.value == 0 || CantidadEtiquetas.value == ""){
                ErrorCantidadEtiquetas.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputCantidadBolsa.value == 0 || InputCantidadBolsa.value == ""){
                ErrorCantidadBolsa.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
        }else if(InputTipoEtiqueta == "ETIQ6" || InputTipoEtiqueta == "ETIQ13"){
            if(InputInsercion.value == ""){
                ErrorInsercion.innerHTML = "Selecciona una opción valida";
                return 0;
            }
        }else if(InputTipoEtiqueta == "ETIQ7" || InputTipoEtiqueta == "ETIQ8" || InputTipoEtiqueta == "ETIQ4" || InputTipoEtiqueta == "ETIQ4CEDIS"){
             if(CantidadEtiquetas.value == 0 || CantidadEtiquetas.value == ""){
                ErrorCantidadEtiquetas.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
        }else if(InputTipoEtiqueta == "ETIQ9"){
             if(InputPaginaInicio.value == 0 || InputPaginaInicio.value == ""){
                ErrorPaginaInicio.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputPaginaFin.value == 0 || InputPaginaFin.value == ""){
                ErrorPaginaFin.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputPaginaFin.value<InputPaginaInicio.value){
                ErrorPaginaInicio.innerHTML = "El valor inicio no puede ser mayor a valor fin.";
                return 0;
            }
            if(CantidadCajas.value == 0 || CantidadCajas.value == ""){
                ErrorCantidadCajas.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
        }else if(InputTipoEtiqueta == "ETIQ10"){
             if(InputPaginaInicio.value == 0 || InputPaginaInicio.value == ""){
                ErrorPaginaInicio.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputPaginaFin.value == 0 || InputPaginaFin.value == ""){
                ErrorPaginaFin.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputPaginaFin.value<InputPaginaInicio.value){
                ErrorPaginaInicio.innerHTML = "El valor inicio no puede ser mayor a valor fin.";
                return 0;
            }
            if(CantidadCajas.value == 0 || CantidadCajas.value == ""){
                ErrorCantidadCajas.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }

        }else if(InputTipoEtiqueta == "ETIQ11" || InputTipoEtiqueta == "ETIQ12"){
            if(CantidadEtiquetas.value == 0 || CantidadEtiquetas.value == ""){
                ErrorCantidadEtiquetas.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(CantidadBolsa.value == 0 || CantidadBolsa.value == ""){
                ErrorCantidadBolsa.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
        }else if(InputTipoEtiqueta == "ETIQ14"){
            if(InputPaginaInicio.value == 0 || InputPaginaInicio.value == ""){
                ErrorPaginaInicio.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputPaginaFin.value == 0 || InputPaginaFin.value == ""){
                ErrorPaginaFin.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
            if(InputPaginaFin.value<InputPaginaInicio.value){
                ErrorPaginaInicio.innerHTML = "El valor inicio no puede ser mayor a valor fin.";
                return 0;
            }
            if(InputTipoDistribuidor.value == null || InputTipoDistribuidor.value == ""){
                ErrorTipoDistribuidor.innerHTML = "Campo requerido";
                return 0;
            }
        }else if(InputTipoEtiqueta == "ETIQ16"){
            if(CantidadEtiquetas.value == 0 || CantidadEtiquetas.value == ""){
                ErrorCantidadEtiquetas.innerHTML = "Ingresa un valor mayor a 0.";
                return 0;
            }
        }else if(InputTipoEtiqueta == "ETIQ17" || InputTipoEtiqueta == "ETIQ18" || InputTipoEtiqueta == "ETIQ19" || InputTipoEtiqueta == "ETIQ20" || InputTipoEtiqueta == "ETIQ21"){
            if(InputMenuDistribuidor.value == null || InputMenuDistribuidor.value == ""){
                ErrorMenuDistribuidor.innerHTML = "Campo requerido.";
                return 0;
            }
        }
        const URLcontroller = '{{ route("Etiquetas.Generar") }}';
        const payload = {
            PDFOrdenFabricacion: InputPDFOrdenFabricacion?.value || 0,
            CantidadBolsa: InputCantidadBolsa?.value || 0,
            PaginaFin: InputPaginaFin?.value || 0,
            PaginaInicio: InputPaginaInicio?.value || 0,
            Sociedad: InputSociedad?.value || 0,
            CantidadEtiquetas: CantidadEtiquetas?.value || 0,
            OF: OF, // ya es una variable, no hace falta validarla aquí
            TipoEtiqueta: InputTipoEtiqueta || 0,
            PorcentajeA: InputPorcentajeA?.value || 0,
            PorcentajeB: InputPorcentajeB?.value || 0,
            PorcentajeC: InputPorcentajeC?.value || 0,
            PorcentajeD: InputPorcentajeD?.value || 0,
            CodigoCliente: InputCodigoCliente?.value || 0,
            Insercion: InputInsercion?.value || 0,
            Retorno: InputRetorno?.value || 0,
            CantidadCajas: InputCantidadCajas?.value || 0,
            TipoDistribuidor:InputTipoDistribuidor?.value || 0,
        };
        SpinnerInsert('PdfEspinner');
        fetch(URLcontroller, {
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
                base64Data = data.pdf;
                const byteCharacters = atob(base64Data);
                const byteNumbers = new Array(byteCharacters.length);
                for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], { type: 'application/pdf' });

                // Crear URL temporal y mostrar en el iframe
                const blobUrl = window.URL.createObjectURL(blob);
                document.getElementById('pdfIframe').src = blobUrl;
                document.getElementById('pdfIframe').style.display = "";
            }
        })
        .catch(err => {
            document.getElementById('PdfEspinner').innerHTML = "";
            //$('#ModalDetalle').modal('hide');
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
    function CantidadFinal() {
        const valor = document.getElementById("CantidadCajas").value;
        document.getElementById("PaginaFin").value = valor;
    }
    function mostrarSeleccion(select) {
        // Obtener el texto de la opción seleccionada
        Titulo = document.getElementById("TituloEtiqueta");
        Tituloinicio = document.getElementById("TipoEtiqueta");
        const texto = select.options[select.selectedIndex].text;
        const texto1 = Tituloinicio.options[Tituloinicio.selectedIndex].text;
        Titulo.innerHTML = texto1+" "+ texto;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endsection
