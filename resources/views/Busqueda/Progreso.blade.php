@extends('layouts.menu2')
@section('title', 'Progreso')
@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--estilos-->
    <style>
        .lista-busqueda{
            height:6rem;
            overflow-y: auto;
        }
        .lista-busqueda::-webkit-scrollbar {
            width: 8px; /* Ancho del scroll */
        }
        .lista-busqueda::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 10px;
            border: 2px solid #fff;
        }
        .lista-busqueda::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }
        .lista-busqueda::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            border-radius: 10px;
        }
        .progress-Porcentaje{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 100%;
            width: 4.8rem;
            height: 4.8rem;
            display: flex;
            justify-content: center;
        }
        .progress-circle {
            position: relative;
            left: 35%;
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1),
            inset 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .progress-Porcentaje h5{
            font-size: 1.3rem; /* Ajusta según sea necesario */
            font-weight: bold;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .Estacion_Hover {
            transition: transform 0.3s ease; /* Duración y tipo de transición */
        }
        .Estacion_Hover:hover{
            transform: scale(1.04);
        }
        #GraficaPorcentajeTiempos {
        width: 100%;
        height: 350px;
        }
    </style>
@endsection
@section('content')
    <!-- Breadcrumbs -->
    <div class="row gy-3 mb-1 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Busquedas</h4>
        </div>
    </div>
    <div class="container mt-1">
        <!--Filtro-->
        <div class="row">
            <div class="card shadow-sm border-light col-8 col-sm-6 p-2 mb-1">
                <div class="accordion" id="FiltroOrden">
                    <div class="accordion-item border-top border-300">
                        <h2 class="accordion-header" id="headingOne">
                            <button id="AccordeFiltroOrdenBtn" class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Busqueda por N&uacute;mero de Orden
                            </button>
                        </h2>
                        <div class="accordion-collapse collapse show" id="collapseOne" aria-labelledby="headingOne" data-bs-parent="#FiltroOrden">
                            <div class="accordion-body pt-0">
                                <div class="btn-group my-0" role="group" aria-label="Tipo de Orden">
                                    <input type="radio" class="btn-check" name="TipoOrden" id="TipoOrden1" autocomplete="off" checked>
                                    <label class="btn btn-sm btn-outline-primary" for="TipoOrden1">
                                        <i class="fas fa-shopping-cart me-1"></i> Orden de venta
                                    </label>
                                    <input type="radio" class="btn-check" name="TipoOrden" id="TipoOrden2" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-primary" for="TipoOrden2">
                                        <i class="fas fa-industry me-1"></i> Orden de fabricaci&oacute;n
                                    </label>
                                </div>
                                <hr class="my-1 p-0">
                                <div class="mb-2 col-10">
                                    <label for="NumeroOrden" class="form-label">N&uacute;mero de Orden</label>
                                    <div class="input-group">
                                        <input type="text" oninput="RegexNumeros(this)" class="form-control form-control-sm" id="NumeroOrden" placeholder="Ingresa número de Orden" autocomplete="off">
                                        <button class="btn btn-outline-primary btn-sm" id="Btn-BuscarOrden">
                                            Buscar
                                        </button>
                                    </div>
                                    <div class="list-group lista-busqueda" id="ListaBusquedas" style="display: none;height: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Detalles-->
        <div class="row ">
            <!--Orden Fabricacion-->
            <div class="col-12 pt-4 card" id="DetallesOrdenFabricacion" style="display: none">
                <h4 class="mb-3" id="exampleModalLabel">
                    Orden de Fabricaci&oacute;n:
                    <span id="ordenFabricacionNumero" class="text-muted"></span>
                    <span id="EstatusFabricacion"class="" style="position: absolute;right:4rem;">Estatus</span> 
                </h4>
                <hr>
                 <!-- Barra de progreso -->
                        <h4 class="text-center mb-3 text-muted">Orden de Venta: <span id="OrdenVenta"></span> &nbsp;&nbsp;&nbsp;&nbsp; Cliente: <span id="NombreCliente"></span></h4>
                        <h5 class="text-center mb-2">Progreso Total de piezas completadas</h5>
                        <div class="progress" style="height: 22px; border-radius: 5px; box-shadow: 0px 3px 6px rgba(0,0,0,0.2); overflow: hidden; width: 100%;">
                            <div id="plemasProgressBar" class="progress-bar text-white fw-bold progress-animated" role="progressbar" 
                                style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;" 
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0%
                            </div>
                            <h6 class="mx-2 mt-2" id="Bloque0porciento">0%</h6>
                        </div>
                        <div class="row justify-content-center mt-3">
                            <!-- Primera fila (4 elementos) -->
                            <h4 class="text-center mb-0 mt-2">Tiempos</h4>
                            <div class="row mt-3">
                                <div class="mx-0 col-12 col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header m-0 py-3 bg-info">

                                        </div>
                                        <div class="card-body">
                                            <div class="border-0 p-2 text-center">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="fa-stack fa-1x">
                                                        <i class="fas fa-circle fa-stack-2x text-success"></i>
                                                        <i class="fas fa-cog fa-stack-1x text-white "></i>
                                                    </span>
                                                </div>
                                                <h5 class="mt-2">Duración Total de Fabricación</h5>
                                                <p id="TiempoDuracion" class="text-muted fs--1 mb-0"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mx-0 col-12 col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header m-0 py-3 bg-info">

                                        </div>
                                        <div class="card-body">
                                            <div class="border-0 p-2 text-center">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="fa-stack fa-1x">
                                                        <i class="fas fa-circle fa-stack-2x text-success"></i>
                                                        <i class="fas fa-cogs fa-stack-1x text-white "></i>
                                                    </span>
                                                </div>
                                                <h5 class="mt-2">Duración Promedio por Pieza</h5>
                                                <p id="TiempoPromedio" class="text-muted fs--1 mb-0"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card shadow-sm border-0 text-center" style="background-color: #d4edda;">
                                        <div class="card-header m-0 py-2 bg-success">

                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="fa-stack fa-1x">
                                                    <i class="fas fa-circle fa-stack-2x text-success"></i>
                                                    <i class="fas fa-stopwatch fa-stack-1x text-white "></i>
                                                </span>
                                            </div>
                                            <h5 class="mt-2">Tiempo Productivo</h5>
                                            <p id="Produccion"  class="text-muted fs--1 mb-0">Tiempo Promedio</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card shadow-sm border-0 text-center" style="background-color: #f8d7da;">
                                        <div class="card-header m-0 py-2 bg-danger">

                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="fa-stack fa-1x">
                                                    <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                                    <i class="fas fa-hourglass-empty fa-stack-1x text-white"></i>
                                                </span>
                                            </div>
                                            <h5 class="mt-2">Tiempo Muerto</h5>
                                            <p id="Muerto"  class="text-muted fs--1 mb-0">Tiempo Promedio</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card shadow-sm border-0 text-center" style="background-color: #cce5ff;">
                                        <div class="card-header m-0 py-2 bg-primary">

                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="fa-stack fa-1x">
                                                    <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                                    <i class="fas fa-clock fa-stack-1x text-white"></i>
                                                </span>
                                            </div>
                                            <h5 class="mt-2">Tiempo Total Trabajado</h5>
                                            <p id="TiempoTotal"  class="text-muted fs--1 mb-0">Tiempo Total de la Orden</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <!---->
                            <!--<div class="card p-3">-->
                            <h4 class="text-center mb-2 mt-2">Estaci&oacute;nes</h4>
                            <div  class="row mb-3" id="plemasCanvases">
                            </div>
                            <hr>
                            <h4 class="text-center mb-0 mt-2">Estad&iacute;sticas</h4>
                            <div class="col-12 col-sm-8 col-md-8 Estacion_Hover my-1" id="ContainerGraficaPorcentajeTiempos">
                                <div class="card rounded border-0 p-2">
                                    <div id="GraficaPorcentajeTiempos"></div>
                                </div>
                            </div>
                            {{--<div class="col-12 col-sm-4 col-md-4 Estacion_Hover my-3" id="ContainerGraficaPorcentajeRetrabajos">
                                <div class="card rounded border-0 p-2">
                                    <div id="GraficaPorcentajeTiempos1">1111111</div>
                                </div>
                            </div>--}}
                        </div>
            </div>
            <!--Orden Venta-->
            <div class="col-12 pt-4 rounded border-0" id="DetallesOrdenVenta" style="display: none">
                <div class="py-4 px-2 ">
                    <h4 class="mb-3">
                        Orden de Venta 
                        <span id="OVNumero"></span>
                        <span id="OVEstatus"class="" style="position: absolute;right:4rem;">Estatus</span> 
                    </h4>
                    <hr>
                    <!-- Barra de progreso -->
                    <div class="card bg-white py-4 px-4 rounded">
                        <h4 class="text-start mb-2 text-muted">Cliente: <span id="OVNombreCliente"></span></h4>
                        <h5 class="text-center mb-2">Progreso de piezas completadas Orden de Venta</h5>
                        <div class="progress" style="height: 22px; border-radius: 6px; box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.438); overflow: hidden; width: 100%;">
                            <div id="OVBarrraProgreso" class="progress-bar text-white fw-bold progress-animated" role="progressbar" 
                                style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;" 
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                0%
                            </div>
                            <h6 class="mx-2 mt-2" id="OVBloque0porciento">0%</h6>
                        </div>
                    </div>
                    <!-- Avance Orden Fabricacion-->
                    <div class="my-3 col-xl-6 col-xxl-6 bg-white px-4 py-3 rounded">
                        <div class="border-top">
                            <div id="purchasersSellersTable">
                                <div class="table-responsive scrollbar mx-n1 px-1">
                                    <table class="table table-sm fs--1 leads-table">
                                        <thead>
                                            <tr>
                                                <th class="ps-0 pe-5 text-uppercase text-nowrap" style="width: 20%;">Orden de fabricaci&oacute;n</th>
                                                <th class="ps-4 pe-5 text-uppercase text-center" style="width: 80%;"> Progreso</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list" id="OrdenesCompletadas-body">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Avance de la Orden-->
                    <div class="card theme-wizard my-3" data-theme-wizard="data-theme-wizard">
                        <div class="card-header bg-100 pt-3 pb-2 border-bottom-0">
                            <ul id="MenuTrazabilidad" class="nav justify-content-between nav-wizard" role="tablist">

                            </ul>
                        </div>
                        <div id="MenuTrazabilidadBody" class="card-body d-flex justify-content-between pt-4 pb-0">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>   
    //cargar los datos de fabricacion
    let currentStageOpen = null; // <- para rastrear la etapa activa
    function cambiarEstatus(estado) {
        const estatusElement = document.getElementById('Estatus');
  
        if (estado === 'abierto') {
            estatusElement.classList.add('open');
            estatusElement.classList.remove('closed');
            estatusElement.textContent = 'Abierto';
        } else {
            estatusElement.classList.add('closed');
            estatusElement.classList.remove('open');
            estatusElement.textContent = 'Cerrado';
        }
    }
</script>
<script>
    //Scripts utiles
    $('#Btn-BuscarOrden').on('click', function () {
        var NumeroOrden = $('#NumeroOrden').val().trim();
        if (NumeroOrden === '') {
            return;  
        }
        $('#NumeroOrden').val(NumeroOrden).trigger('input');
    });
    AjaxOrden = null;
    $('#TipoOrden1').on('change', function() {
        $('#ListaBusquedas').html('');
        $('#ListaBusquedas').hide();
    });
    $('#TipoOrden2').on('change', function() {
        $('#ListaBusquedas').html('');
        $('#ListaBusquedas').hide();
    });
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
        OrdenVenta = $('#TipoOrden1').is(':checked');
        OrdenFabricacion = $('#TipoOrden2').is(':checked');
        TipoOrden = "OF";
        if(OrdenVenta == true){
            TipoOrden = "OV";
        }
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
    function SeleccionarNumOrden(NumeroOrden){
        $('#NumeroOrden').val(NumeroOrden);
        $('#ListaBusquedas').hide();
        $('.ver-fabricacion').trigger('click');
        $('#DetallesOrdenVenta').hide();
        $('#DetallesOrdenFabricacion').hide();
        OrdenVenta = $('#TipoOrden1').is(':checked');
        OrdenFabricacion = $('#TipoOrden2').is(':checked');
        if(OrdenVenta == true){
            OVNumero = document.getElementById('OVNumero');
            OVEstatus  = document.getElementById('OVEstatus');
            OVNombreCliente = document.getElementById('OVNombreCliente');
            OVBloque0porciento = document.getElementById('OVBloque0porciento');
            OVBarrraProgreso = document.getElementById('OVBarrraProgreso');
            OrdenesCompletadasbody = document.getElementById('OrdenesCompletadas-body');
            OrdenesCompletadasbody.innerHTML = "";
            OVNumero.innerHTML = NumeroOrden;
            $.ajax({
                url: '{{ route("Detalles.OrdenVenta") }}',
                type: 'GET',
                data: { id: NumeroOrden },
                success: function (response) {
                    if(response.Estatus != "error"){
                        OVNombreCliente.innerHTML = response.Cliente;
                        OVNumero.innerHTML = response.OV;
                        if (response.OVEstatus != "") {
                            var estadoFabricacion = response.OVEstatus || 'Desconocido';
                            var $estatusElem = $('#OVEstatus');
                            var icono = '';
                            $estatusElem.removeClass('bg-success bg-danger bg-secondary').addClass('badge');
                            if (estadoFabricacion === 'Abierta') {
                                $estatusElem.removeClass('bg-danger bg-secondary').addClass('bg-success');
                                icono = '<i class="fas fa-lock-open"></i>';  // Ícono de "Abierta"
                                $estatusElem.html(icono + ' Abierta');
                            } else if (estadoFabricacion === 'Cerrada') {
                                $estatusElem.removeClass('bg-success bg-secondary').addClass('bg-danger');
                                icono = '<i class="fas fa-lock"></i>';  // Ícono de "Cerrada"
                                $estatusElem.html(icono + ' Cerrada');
                                console.log('Estado: Cerrada, Clases: bg-danger');
                            } else {
                                $estatusElem.removeClass('bg-success bg-danger').addClass('bg-secondary');
                                icono = '<i class="fas fa-question-circle"></i>';  // Ícono de "Desconocido"
                                $estatusElem.html(icono + ' Estado desconocido');
                            }
                        }
                        var progreso = response.progreso;
                        if(progreso == 0){
                            $('#OVBloque0porciento').show();
                        }else{
                            $('#OVBloque0porciento').hide();
                        }
                        var OVprogressBar = $('#OVBarrraProgreso');
                        OVprogressBar.css('width', progreso + '%').text(progreso + '%');
                        OVprogressBar.removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                        // Asignar color según el porcentaje
                        if (progreso >= 0 && progreso < 20) {
                            OVprogressBar.addClass('bg-danger');  // Rojo
                        } else if (progreso >= 20 && progreso < 40) {
                            OVprogressBar.addClass('bg-warning');  // Naranja
                        } else if (progreso >= 40 && progreso < 70) {
                            OVprogressBar.addClass('bg-primary');  // Azul
                        } else if (progreso >= 70 && progreso < 90) {
                            OVprogressBar.addClass('bg-info');  // Celeste
                        } else {
                            OVprogressBar.addClass('bg-success');  // Verde
                        }
                        $('#DetallesOrdenVenta').fadeIn();
                        InfoEstaciones = "";
                        Trazabilidad = "";
                        AcomodarLista = [];
                        ListaCompleta = [];
                        ListaAreas = [];
                        Colores = [];
                        MenuTrazabilidad = "";
                        $NumeroOrdenes = 0;
                        response.AreasDatos.forEach(([OrdenFabricacion,progreso, Areas]) => {
                            clase = (progreso < 50) ? 'bg-warning' : 'bg-info';
                            clase = (progreso>=50 && progreso <= 75) ? 'bg-info' : 'bg-success';
                            InfoEstaciones += '<tr class="">'
                                                +'<td class="text-start px-2"><span class=" fw-bold" style="font-size:1rem;">'+OrdenFabricacion+'</span></td>'
                                                +'<td class="text-star">'
                                                    +'<div class="progress" style="height: 22px; border-radius: 3px; overflow: hidden; width: 100%;">'
                                                    +'<div id="OVBarrraProgreso" class="progress-bar text-white fw-bold progress-animated '+clase+'" role="progressbar" style="width: '+progreso+'%; transition: width 0.5s ease-in-out; font-size: 14px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">'+progreso+'%</div>'
                                                +'</div></td>';
                                            +'</tr>';
                            AcomodarLista.push(...Areas.map(item => item.AP));
                            ListaCompleta.push(Areas);
                            ListaAreas.push(...Areas.map(item => item));
                            $NumeroOrdenes++;
                            Colores.push(ColorAleatorio());
                        });
                        AcomodarLista = [...new Set(AcomodarLista)];
                        PorcentajeArea = "";
                        AcomodarLista.forEach((IdArea,index) => {
                            //PorcentajeArea = "";
                            filtrado = ListaAreas.filter(item => item.AP === IdArea);
                            color = "#01a524"
                            icono = "fas fa-check";
                            AcomodarLista.forEach((elemento,index) => {
                                if(elemento.PorcentajeActual != 100){
                                    color = "#ffc107"
                                    icono = "fas fa-cogs";
                                }
                            });
                            PorcentajeArea += '<div class="p-2 d-flex flex-column justify-content-around">';
                            ListaCompleta.forEach((registro) =>{
                                RegistroBan = registro.filter(item => item.AP === IdArea);
                                if(RegistroBan.length>0){
                                    PorcentajeArea +='<div class="mb-2"><div class="progress" style="height: 22px; border-radius: 3px; overflow: hidden; width: 90%;"><div id="OVBarrraProgreso" class="progress-bar text-white fw-bold progress-animated bg-success" role="progressbar" style="width: '+RegistroBan[0].PorcentajeActual+'%; transition: width 0.5s ease-in-out; font-size: 14px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">'+RegistroBan[0].PorcentajeActual+'%</div>'
                                                            +'</div></div>'
                                }
                                else{
                                    PorcentajeArea +='<div class="mb-2"><div class="progress" style="height: 22px; border-radius: 3px; overflow: hidden; width: 90%;"><div id="OVBarrraProgreso" class="progress-bar text-dark fw-bold progress-animated bg-white" role="progressbar" style="width: 100%; transition: width 0.5s ease-in-out; font-size: 14px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">No asignada</div>'
                                                            +'</div></div>'
                                }
                            });
                            PorcentajeArea += '</div>';
                            MenuTrazabilidad += '<li class="nav-item" role="presentation">'
                                                    +'<a class="nav-link fw-semi-bold" data-bs-toggle="tab" data-wizard-step="'+index+'">'
                                                        +'<div class="text-center d-inline-block">'
                                                            +'<span class="nav-item-circle-parent">'
                                                                +'<span class="nav-item-circle" style="color: '+color+'; border-color:'+color+';">'
                                                                    +'<span class="'+icono+'"></span>'
                                                                +'</span>'
                                                            +'</span>'
                                                            +'<span class="d-none d-md-block mt-1 fs--1" style="color:'+color+'">'+filtrado[0].NombreArea+'</span>'
                                                        +'</div>'
                                                    +'</a>'
                                                +'</li>';
                        });
                        /*AcomodarLista.forEach((item, index) => {
                            response.AreasDatos.forEach(([OrdenFabricacion, progreso, Areas]) => {
                            
                        });*/
                        //AcomodarLista.sort((a, b) => a - b); 
                        /*
                        alert(AcomodarLista);
                            Areas.forEach((item, index) => {
                                Trazabilidad = '';
                                console.log(`AP: ${item.AP}`);
                                console.log(`Porcentaje: ${item.PorcentajeActual}`);
                                console.log(`Tiempo: ${item.TiempoOrdenes}`);
                                console.log('----------------------');
                            });
                        */
                        document.getElementById('MenuTrazabilidadBody').innerHTML = PorcentajeArea;
                        document.getElementById('MenuTrazabilidad').innerHTML = MenuTrazabilidad;
                        OrdenesCompletadasbody.innerHTML = InfoEstaciones;
                    }else{
                        error("Error Orden de Venta "+NumeroOrden, response.message);
                    }
                },
                error: function () {
                    OVNumero.innerHTML = "";
                    OVEstatus.innerHTML = "";
                    $('#OVBarrraProgreso').removeClass('bg-success bg-danger bg-secondary').text('Sin datos');
                    /*var progressBar = $('#plemasProgressBar');
                    TiempoDuracion.html("");
                    progressBar.css('width', '0%').text('0%').removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                    $('#ordenFabricacionNumero').removeClass('text-info').addClass('text-muted').text(ordenfabricacion);
                    $('#EstatusFabricacion').removeClass('bg-success bg-danger bg-secondary').text('Sin datos');*/
                    errorBD();
                }
            }); 
        }else{
            var ordenfabricacion = $('#NumeroOrden').val();
            var Bloque0porciento = $('#Bloque0porciento'); 
            var TiempoDuracion = $('#TiempoDuracion');
            var Produccion = $('#Produccion');
            var TiempoTotal = $('#TiempoTotal');
            var Muerto = $('#Muerto')
            var plemasCanvases = $('#plemasCanvases');
            var TiempoPromedio = $('#TiempoPromedio');
            var OrdenVenta = $('#OrdenVenta');
            var NombreCliente = $('#NombreCliente');
            OrdenVenta.html('');
            NombreCliente.html('');
            plemasCanvases.html('');
            TiempoTotal.html('Tiempo Total')
            Produccion.html('Tiempo Total');
            Muerto.html('Tiempo Total');
            CadenaTiempo="Aún no ha comenzado el proceso";
            $.ajax({
                url: '{{ route("Detalles.Fabricacion") }}',
                type: 'GET',
                data: { id: ordenfabricacion },
                success: function (response) {
                    var progressBar = $('#plemasProgressBar');
                    if (response.Estatus !== 'Error') {
                        var progreso = response.progreso;
                        if(progreso==0){
                            Bloque0porciento.show();
                        }else{
                            Bloque0porciento.hide();
                        }
                        if(response.TiempoProductivo != 0){
                            Produccion.html('Tiempo total<br>'+response.TiempoProductivo);
                        }else{
                            Produccion.html('El dato se verá reflejado cuando la orden sea finalizada');
                        }
                        if(response.TiempoTotal != 0){
                            TiempoTotal.html('Tiempo total<br>'+response.TiempoTotal);
                        }else{
                            TiempoTotal.html('El dato se verá reflejado cuando la orden sea finalizada');
                        }
                        if(response.TiempoMuerto != 0){
                            Muerto.html('Tiempo total<br>'+response.TiempoMuerto);
                        }else{
                            Muerto.html('El dato se verá reflejado cuando la orden sea finalizada');
                        }
                        if(!response.TiempoDuracion==0){
                            CadenaTiempo="";
                            if(response.TiempoDuracion.y!=0){CadenaTiempo+=response.TiempoDuracion.y+" Años "}
                            if(response.TiempoDuracion.m!=0){CadenaTiempo+=response.TiempoDuracion.m+" Meses "}
                            if(response.TiempoDuracion.d!=0){CadenaTiempo+=response.TiempoDuracion.d+" Días "}
                            if(response.TiempoDuracion.h!=0){CadenaTiempo+=response.TiempoDuracion.h+" Horas "}
                            if(response.TiempoDuracion.i!=0){CadenaTiempo+=response.TiempoDuracion.i+" Minutos "}
                            if(response.TiempoDuracion.s!=0){CadenaTiempo+=response.TiempoDuracion.s+" Segundos"}
                        }else{
                            CadenaTiempo="El dato se verá reflejado cuando la orden sea finalizada";
                        }
                        if(!response.TiempoPromedioSeg==""){
                            TiempoPromedio.html(response.TiempoPromedioSeg);
                        }else{
                            TiempoPromedio.html("El dato se verá reflejado cuando la orden sea finalizada");
                        }
                        //Orden Venta y Nombre Cliente
                        OrdenVenta.html(response.OV);
                        NombreCliente.html(response.Cliente);
                        TiempoDuracion.html(CadenaTiempo);
                        // Actualizar la barra de progreso con animación
                        progressBar.css('width', progreso + '%').text(progreso + '%');
                        progressBar.removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                        // Asignar color según el porcentaje
                        if (progreso >= 0 && progreso < 20) {
                            progressBar.addClass('bg-danger');  // Rojo
                        } else if (progreso >= 20 && progreso < 40) {
                            progressBar.addClass('bg-warning');  // Naranja
                        } else if (progreso >= 40 && progreso < 70) {
                            progressBar.addClass('bg-primary');  // Azul
                        } else if (progreso >= 70 && progreso < 90) {
                            progressBar.addClass('bg-info');  // Celeste
                        } else {
                            progressBar.addClass('bg-success');  // Verde
                        }
                        $('#ordenFabricacionNumero').removeClass('text-muted').text(ordenfabricacion);
                        if (response.Estatus != "") {
                            var estadoFabricacion = response.Estatus || 'Desconocido';
                            var $estatusElem = $('#EstatusFabricacion');
                            var icono = '';
                            $estatusElem.removeClass('bg-success bg-danger bg-secondary').addClass('badge');
                            if (estadoFabricacion === 'Abierta') {
                                $estatusElem.removeClass('bg-danger bg-secondary').addClass('bg-success');
                                icono = '<i class="fas fa-lock-open"></i>';  // Ícono de "Abierta"
                                $estatusElem.html(icono + ' Abierta');
                            } else if (estadoFabricacion === 'Cerrada') {
                                $estatusElem.removeClass('bg-success bg-secondary').addClass('bg-danger');
                                icono = '<i class="fas fa-lock"></i>';  // Ícono de "Cerrada"
                                $estatusElem.html(icono + ' Cerrada');
                                console.log('Estado: Cerrada, Clases: bg-danger');
                            } else {
                                $estatusElem.removeClass('bg-success bg-danger').addClass('bg-secondary');
                                icono = '<i class="fas fa-question-circle"></i>';  // Ícono de "Desconocido"
                                $estatusElem.html(icono + ' Estado desconocido');
                            }
                        }
                        EstacionesGraficas ='';
                        var ArrayPorcentajeGrafica = [];
                        var ArrayNombrePorcentajeGrafica = [];
                        if((response.Estaciones).length!=0){
                            (response.Estaciones).forEach(area => {
                                ColorProgress="";
                                if(area.PorcentajeActual<25){
                                    ColorProgress = ' #D32F2F ';
                                }
                                if(area.PorcentajeActual>=25 && area.PorcentajeActual<50){
                                    ColorProgress = ' #FF7043 '; 
                                }
                                if(area.PorcentajeActual>=50 && area.PorcentajeActual<75){
                                    ColorProgress = ' #FFEB3B ';
                                }
                                if(area.PorcentajeActual>=75 && area.PorcentajeActual<=100){
                                    ColorProgress = ' #38f41a ';
                                }
                                if(area.AP != 1){
                                    EstacionesGraficas+='<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-4  col-xxl-3 my-2 Estacion_Hover">'+
                                                            '<div class="card rounded border-0 p-2" style="box-shadow: 3px 3px 3px 2px rgba(0.1, 0.1, 0.1, 0.2);">'+
                                                                '<div class="card-header py-1" style="background:'+ColorProgress+';"><h5 class="text-center text-white">'+area.NombreArea+'</h5></div>'
                                                                +'<div class="progress-container">'+
                                                                    '<div class="progress-circle" style="background: conic-gradient('+ColorProgress+' 0% '+area.PorcentajeActual+'%, #e0e0e0 '+area.PorcentajeActual+'% 100%);">'+
                                                                        '<div class="progress-Porcentaje"><h5>'+area.PorcentajeActual+'%</h5></div>'+   
                                                                    '</div>'+
                                                                '</div>';
                                    if(response.RequiereCorte == 0 && area.NombreArea=='Corte'){
                                        EstacionesGraficas+='<span class="badge bg-warning">No requiere Corte</span>';
                                    }
                                    EstacionesGraficas+='<small class="float-start"><span class="float-start">Piezas Normales:'+area.Normales+'</span><span class="float-end"> Piezas Retrabajo:'+area.Retrabajo+'</span></small><h6 class="text-center mt-2">Tiempos</h6><small>Duración: '+area.TiempoOrdenes+'</small><small>Productivo: '+area.TiempoProductivoEstacion+'</small>'+
                                                            '</div>'+
                                                        '</div>';
                                    ArrayPorcentajeGrafica.push({ value: area.TiempoEstacionSegundos, name: area.NombreArea});
                                    ArrayNombrePorcentajeGrafica.push(area.NombreArea);
                                }
                            });
                        }else{
                            EstacionesGraficas = '<h5 class="text-center">Aún no se asigna una Línea</h5>';
                        }
                        plemasCanvases.html(EstacionesGraficas);
                    } else {
                        TiempoDuracion.html("");
                        error('Error de la Orden de Fabricación',response.Message);
                        Bloque0porciento.show();
                        progressBar.css('width', '0%').text('0%').removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                        $('#ordenFabricacionNumero').removeClass('text-info').addClass('text-muted').text(ordenfabricacion);
                        $('#EstatusFabricacion').removeClass('bg-success bg-danger bg-secondary').text('Sin datos');
                    }
                    // Mostrar el modal
                    $('#DetallesOrdenFabricacion').fadeIn();
                    $('#AccordeFiltroOrdenBtn').trigger('click');
                    //if((response.Estaciones).length!=0){
                        var chart = echarts.init(document.getElementById('GraficaPorcentajeTiempos'));
                        var option = {
                            title: {
                                text: 'Porcentaje de Tiempo por Estación',
                                subtext: '% Tiempo en segundos',
                                left: 'center'
                            },
                            tooltip: {
                                trigger: 'item',
                                formatter: function (params) {
                                    var totalSeconds = params.value;
                                    var hours = Math.floor(totalSeconds / 3600);
                                    var minutes = Math.floor((totalSeconds % 3600) / 60);
                                    var seconds = totalSeconds % 60;
                                    var timeParts = [];
                                    if (hours > 0) {
                                        timeParts.push(`${hours}h`);
                                        timeParts.push(`${minutes}m`);
                                        timeParts.push(`${seconds}s`);
                                    }
                                    else if (minutes > 0){
                                        timeParts.push(`${minutes}m`);
                                        timeParts.push(`${seconds}s`);
                                    }
                                    else if (seconds > 0 || timeParts.length === 0) timeParts.push(`${seconds}s`);
                                    return `${params.name}: (${timeParts.join(' ')})`;
                                }
                                /*formatter: '{b}: ({c} segundos)'*/
                            },
                            legend: {
                                type: 'scroll',
                                orient: 'vertical',
                                left: '5%',
                                top: 'middle',
                                itemGap: 5,            // Más distancia entre entradas
                                //bottom: 20,
                                data: ArrayNombrePorcentajeGrafica
                            },
                            series: [
                                {
                                name: 'Tiempo en horas',
                                type: 'pie',
                                radius: ['25%', '40%'], 
                                center: ['50%', '50%'],
                                //avoidLabelOverlap: true,  // Previene superposición de etiquetas
                                itemStyle: {
                                    borderWidth: 1,         // Línea entre segmentos para separación visual
                                    borderColor: '#fff'
                                },
                                label: {
                                    //position: 'outside',    // Etiquetas afuera para mayor espacio
                                    //alignTo: 'labelLine',
                                    formatter: '{b}: {d}%', 
                                    //distance: 1            // Distancia desde el gráfico
                                },
                                labelLine: {
                                    smooth: false,
                                    length: 20              // Longitud de línea guía
                                },
                                data: ArrayPorcentajeGrafica,
                                    emphasis: {
                                        itemStyle: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                                        }
                                    }
                                }
                            ]
                        };
                        if((response.Estaciones).length==0){
                            chart.setOption(option);
                            $('#ContainerGraficaPorcentajeTiempos').hide();
                        }else{
                            $('#ContainerGraficaPorcentajeTiempos').show();
                        }
                    // Establecer la opción y renderizar el gráfico
                    chart.setOption(option);
                    //$('#example2Modal').on('shown.bs.modal', function () {
                    chart.resize();
                    //});
                    // Hacer que el gráfico sea responsivo al cambiar el tamaño de la ventana
                    window.addEventListener('resize', function() {
                    chart.resize();
                    });//--}}
                },
                error: function () {
                    var progressBar = $('#plemasProgressBar');
                    TiempoDuracion.html("");
                    progressBar.css('width', '0%').text('0%').removeClass('bg-danger bg-warning bg-info bg-success bg-primary');
                    $('#ordenFabricacionNumero').removeClass('text-info').addClass('text-muted').text(ordenfabricacion);
                    $('#EstatusFabricacion').removeClass('bg-success bg-danger bg-secondary').text('Sin datos');
                    errorBD();
                }
            });  
        }
    }
    function ColorAleatorio() {
        const letras = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letras[Math.floor(Math.random() * 16)];
        }
        return color;
    }
</script>
@endsection