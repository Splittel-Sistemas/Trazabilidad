@extends('layouts.menu2')
@section('title', 'Montaje')
@section('styles')
<link rel="stylesheet" href="{{asset('css/Suministro.css')}}">
<style>
    /* Positioning the toast in the top-right corner */
    #ToastGuardado {
        position: fixed; /* Fixed position */
        top: 5rem; /* Distance from the top */
        right: 20px; /* Distance from the right */
        z-index: 1050; /* Ensure it's above other content */
    }
</style>
@endsection
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
        <h4 class="mb-2 text-1100">Montaje</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
              <div class="card shadow-sm">
                <div class="card-header p-2" id="filtroEntrada" style="background: #01914f">
                    <h3 for="CodigoEscaner" class="col-sm-12 p-0 text-white">Entrada <i class="fas fa-arrow-down"></i></h3>
                </div>
                <div class="card-body row" id="filtroEntrada">
                    <form id="filtroForm" method="post" class="form-horizontal row mt-0 needs-validation" novalidate="">
                        <div class="col-8" id="CodigoDiv">
                            <div class="">
                                <label for="CodigoEscaner">C&oacute;digo <span class="text-muted">&#40;Escanea o Ingresa manual&#41;</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" oninput="ListaCodigo(this.value,'CodigoEscanerSuministro','Entrada')" id="CodigoEscanerEntrada" aria-describedby="CodigoEscanerHelp" placeholder="Escánea o ingresa manualmente.">
                                    <div class="invalid-feedback" id="error_CodigoEscaner"></div>
                                </div>
                                <div class=" mt-1 list-group-sm" id="CodigoEscanerSuministro">
                                </div>
                            </div>
                        </div>
                        <div class="col-4" id="CantidadDiv" style="display: none">
                            <div class="form-group">
                                <label for="Cantidad">Cantidad</label>
                                <input type="text" class="form-control form-control-sm" id="Cantidad" aria-describedby="Cantidad" placeholder="0">
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
        <div class="col-6">
            <div class="card shadow-sm">
                <div class="card-header p-2" id="filtroEntrada" style="background:#dd4b39;">
                    <h3 for="CodigoEscaner" class="col-sm-12 p-0 text-white">Salida <i class="fas fa-arrow-up"></i></h3>
                </div>
                <div class="card-body row" id="filtroSalida">
                    <form id="filtroForm" method="post" class="form-horizontal row mt-0 needs-validation" novalidate="">
                        <div class="col-8" id="CodigoDiv">
                            <div class="">
                                <label for="CodigoEscaner">C&oacute;digo <span class="text-muted">&#40;Escanea o Ingresa manual&#41;</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" oninput="ListaCodigo(this.value,'CodigoEscanerSuministro','Salida')" id="CodigoEscanerSalida" aria-describedby="CodigoEscanerHelp" placeholder="Escánea o ingresa manualmente.">
                                    <div class="invalid-feedback" id="error_CodigoEscanerSalida"></div>
                                </div>
                                <div class=" mt-1 list-group-sm" id="CodigoEscanerSuministro">
                                </div>
                            </div>
                        </div>
                        <div class="col-4" id="CantidadDivSalida" style="display: none">
                            <div class="form-group">
                                <label for="Cantidad">Cantidad</label>
                                <input type="text" class="form-control form-control-sm" id="CantidadSalida" aria-describedby="Cantidad" oninput="RegexNumeros(this);" placeholder="0">
                                <div class="invalid-feedback" id="error_CantidadSalida"></div>
                            </div>
                        </div>
                        <div class="col-12 mt-2" id="IniciarBtnSalida" style="display: none">
                            <button class="btn btn-primary btn-sm float-end" type="button" id="btnEscanearSalida"><i class="fa fa-play"></i> Cerrar</button>
                        </div>
                    </form>
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
                    <div class="col-3 mx-2">
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text" id="inputGroup-sizing-sm">Núm. L&iacute;nea:</span>
                            <select class="form-select form-select-sm" aria-label="FiltroLinea" id="FiltroLinea">
                                <option selected="" disabled>Selecciona L&iacute;nea</option>
                                <option value="-1">Todas</option>
                                @foreach($Lineas as $Linea)
                                    <option value="{{$Linea->NumeroLinea}}">{{$Linea->Nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="TablaPreparadoPendientes" class="table table-sm fs--1 mb-1">
                        <thead>
                            <tr class="bg-light">
                                <th>Orden Fabricación</th>
                                <th>Artículo</th>
                                <th>Descripción</th>
                                <th>Cantidad Completada</th>
                                <th>Cantidad Faltante</th>
                                <th>Cantidad Entrante</th>
                                <th>Total Orden Fabricaci&oacute;n</th>
                                <th>Estatus</th>
                                <th>L&iacute;nea</th>
                            </tr>
                        </thead>
                        <tbody id="TablaPreparadoPendientesBody" class="list">
                            @foreach($Registros as $partida)
                                @foreach($partida->PartidasOFFaltantes as $PartidaArea)
                                    <tr>
                                        <td class="text-center">{{$partida->OrdenFabricacion }}</td>
                                        <td>{{$partida->Articulo }}</td>
                                        <td>{{$partida->Descripcion }}</td>
                                        <td class="text-center">{{$PartidaArea->Actual}}</td>
                                        <td class="text-center">{{$PartidaArea->Anterior-$PartidaArea->Actual}}</td>
                                        <td class="text-center">{{$PartidaArea->Anterior }}</td>
                                        <td class="text-center">{{$partida->CantidadTotal }}</td>
                                        <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>
                                        <td><h5 class="text-light text-center p-0" style="background: {{$PartidaArea->ColorLinea}};">{{$PartidaArea->Linea}}</h5></td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div  id="ContainerToastGuardado"></div>
@endsection
@section('scripts')
<script src="{{ asset('js/Suministro.js') }}"></script>
<script>
    let timeout;
    function ListaCodigo(Codigo,Contenedor,TipoEntrada){
        clearTimeout(timeout);
        timeout = setTimeout(() => {
        document.getElementById('CodigoEscanerSuministro').style.display = "none";
        if (CadenaVacia(Codigo)) {
            return 0;
        }
        $('#ContentTabla').hide();
        if(Codigo.length<6){
            return 0;
        }
        //InicioInput=document.getElementById('Iniciar');
        if(TipoEntrada=="Entrada"){
            Inicio=1;
            Finalizar=0;
        }
        //FinalizarInput=document.getElementById('Finalizar');
        if(TipoEntrada=="Salida"){
            Inicio=0;
            Finalizar=1;
            $('#CodigoEscanerEntrada').val('');
        }
        if(Inicio==1){
            $('#CodigoEscanerEntrada').focus();
            $('#CodigoEscanerSalida').val('');
        }else if(Inicio==0){
            $('#CodigoEscanerSalida').focus();
            $('#CodigoEscanerEntrada').val('');
        }
        regexCodigo = /^\d+-\d+-\d+$/;
        regexCodigoOF = /^\d+-\d+$/;
        if(!(regexCodigo.test(Codigo) || regexCodigoOF.test(Codigo))) {
            return 0;
        }
        $('#Cantidad').val('');
        $('#CantidadSalida').val('');
        FiltroLinea = $('#FiltroLinea').val();
        if(FiltroLinea == null || FiltroLinea=="" || FiltroLinea<0){
            Mensaje='Para comenzar, en el campo Núm. Línea selecciona la línea en la que vas a trabajar!';
                            Color='bg-danger';
                            $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                            $('#ToastGuardadoBody').html(Mensaje);
                            $('#ToastGuardado').fadeIn();
                            setTimeout(function(){
                                $('#ToastGuardado').fadeOut();
                            }, 3500);
            return 0;
        }
        $.ajax({
            url: "{{route('PreparadoBuscar')}}", 
            type: 'POST',
            data: {
                Codigo: Codigo,
                Retrabajo: 'no',
                Inicio:Inicio,
                Linea:FiltroLinea,
                Finalizar:Finalizar,
                Area:'{{$Area}}'
            },
            beforeSend: function() {
                //$('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
            },
            success: function(response) {
                $('#CantidadDiv').hide();
                $('#IniciarBtn').hide();
                $('#RetrabajoDiv').hide();
                $('#CantidadDivSalida').hide();
                $('#IniciarBtnSalida').hide();
                document.getElementById('Retrabajo').checked = false;
                if(response.status=="success"){
                    if(Inicio==1){
                        $('#CodigoEscanerEntrada').focus();
                    }else if(Inicio==0){
                        $('#CodigoEscanerSalida').focus();
                    }
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
                        $('#CantidadDivSalida').fadeOut();
                        $('#IniciarBtnSalida').fadeOut();
                        if(response.EscanerExiste==0){
                            Mensaje='Codigo '+Codigo+' El codigo que intentas ingresar No existe!';
                            Color='bg-danger';
                            $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                            $('#ToastGuardadoBody').html(Mensaje);
                            $('#ToastGuardado').fadeIn();
                            setTimeout(function(){
                                $('#ToastGuardado').fadeOut();
                            }, 3500);
                        }else{
                            if(Inicio==1){
                                const Retrabajo = document.getElementById('Retrabajo');
                                Retrabajo.disabled = false;
                            }else{
                                const Retrabajo = document.getElementById('Retrabajo');
                                Retrabajo.disabled = true;
                            }
                                $('#ContentTabla').show();
                                $('#CantidadDiv').fadeIn();
                                $('#IniciarBtn').fadeIn();
                                $('#RetrabajoDiv').fadeIn();
                                $('#ContentTabla').show();
                                $('#CantidadDivSalida').fadeIn();
                                $('#IniciarBtnSalida').fadeIn();
                                //$('#CodigoEscanerEntrada').val(Codigo);
                                //$('#CodigoEscanerSalida').val(Codigo);
                                if(Inicio==1){
                                    $('#Cantidad').focus();
                                }else if(Inicio==0){
                                    $('#CantidadSalida').focus();
                                }
                            return 0;
                        }
                        if(Inicio==1){
                            $('#Cantidad').focus();
                        }else if(Inicio==0){
                            $('#CantidadSalida').focus();
                        }
                    }else{
                        $('#ContentTabla').show();
                        Mensaje="";
                        if(response.Inicio==1){
                            if(Inicio==1){
                                $('#CodigoEscanerEntrada').focus();
                            }else if(Inicio==0){
                                $('#CodigoEscanerSalida').focus();
                            }
                            switch (response.TipoEscanerrespuesta) {
                                case 1:
                                //$('#CodigoEscaner').val('');
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> guardado correctamente!';
                                    CoincidenciasCodigo = Codigo.match(/-/g);
                                    if(CoincidenciasCodigo.length==2){
                                        $('#CodigoEscanerSalida').val('');
                                        $('#CodigoEscanerEntrada').val('');
                                    }
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
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Ya se encuentra iniciada en una Estación posterior!';
                                    Color='bg-danger';
                                    //$('#ContentTabla').hide();
                                    //$('#CantidadPartidasOF').html('');
                                    break;
                                case 7:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> No existe!';
                                    Color='bg-danger';
                                    break;
                                default:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Ocurrio un error!';
                                    Color='bg-danger';
                                    break;
                            }

                        }
                        if(response.Finalizar==1){
                            if(Inicio==1){
                                $('#CodigoEscanerEntrada').focus();
                            }else if(Inicio==0){
                                $('#CodigoEscanerSalida').focus();
                            }
                            switch (response.TipoEscanerrespuesta) {
                                case 1:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Finalizado!';
                                    Color='bg-success';
                                    CoincidenciasCodigo = Codigo.match(/-/g);
                                    if(CoincidenciasCodigo.length==2){
                                        $('#CodigoEscanerSalida').val('');
                                        $('#CodigoEscanerEntrada').val('');
                                    }
                                    break;
                                case 2:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> El codigo Aún no ha sido inicializado!';
                                    Color='bg-danger';
                                    break;
                                case 3:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> no encontrado!';
                                    Color='bg-danger';
                                    break;
                                case 7:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> No existe!';
                                    Color='bg-danger';
                                    break;
                                default:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Ocurrio un error!';
                                    Color='bg-danger';
                                    break;
                            }
                            BanderaFinalizar=response.CantidadTotal-response.CantidadCompletada;
                            //alert(BanderaFinalizar);
                            if(BanderaFinalizar==0 && response.TipoEscanerrespuesta==1){
                                confirmacionesss(
                                    "¡Orden de Fabricación finalizada!", 
                                    "La orden de Fabricación se encuentra completada", 
                                    "Aceptar", 
                                    function () {
                                        console.log('Orde de Fabricacion Cerrada');
                                        /*$.ajax({
                                            url: '{{ route("finProceso.empacado") }}',
                                            type: "GET",
                                            data: {
                                                id: id,
                                                _token: '{{ csrf_token() }}'
                                            },
                                            success: function (response) {
                                                if (response.codigo=='Success') {
                                                    success('Orden de Fabricacion Finalizada',response.message);
                                                }else{
                                                    error('Ocurrio un error',response.message);
                                                }
                                                setTimeout(function() {
                                                    console.log("Recargando la tabla...");
                                                    cargarTablaEmpacado();
                                                }, 500);
                                            },
                                            error: function (xhr) {
                                                console.error("Error:", xhr);
                                                alert("Error: " + (xhr.responseJSON?.error || "Ocurrió un problema"));
                                            }
                                        });*/

                                    }
                                );
                            }
                        }
                            $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                            $('#ToastGuardadoBody').html(Mensaje);
                            $('#CantidadDiv').fadeOut();
                            $('#IniciarBtn').fadeOut();
                            $('#CantidadDivSalida').fadeOut();
                            $('#IniciarBtnSalida').fadeOut();
                    }
                    if(response.TipoEscanerrespuesta!=7){
                        $('#ToastGuardado').fadeIn();
                        setTimeout(function(){
                            $('#ToastGuardado').fadeOut();
                        }, 2500);
                    }
                }else if(response.status=="empty"){
                        if(Inicio==1){
                            $('#CodigoEscanerEntrada').focus();
                        }else if(Inicio==0){
                            $('#CodigoEscanerSalida').focus();
                        }
                    //if(response.Escaner!=0){
                        $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                        $('#ToastGuardadoBody').html('El codigo No existe!  ');
                        $('#ToastGuardado').fadeIn();
                        setTimeout(function(){
                            $('#ToastGuardado').fadeOut();
                        }, 4000);
                    //}
                }else if(response.status=="NoExiste"){
                        if(Inicio==1){
                            $('#CodigoEscanerEntrada').focus();
                        }else if(Inicio==0){
                            $('#CodigoEscanerSalida').focus();
                        }
                        $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                        $('#ToastGuardadoBody').html('El codigo No existe!  ');
                        $('#ToastGuardado').fadeIn();
                        setTimeout(function(){
                            $('#ToastGuardado').fadeOut();
                        }, 4000);
                }
                RecargarTablaPendientes();
            },
            error: function(xhr, status, error) {
                $('#CantidadDiv').hide();
                $('#IniciarBtn').hide();
                $('#CantidadDivSalida').hide();
                $('#IniciarBtnSalida').hide();
                if(Inicio==1){
                    $('#CodigoEscanerEntrada').focus();
                }else if(Inicio==0){
                    $('#CodigoEscanerSalida').focus();
                }
            }
        }); 
    }, 800);
    }
    function TraerDatos(id,OF){
        $('#CodigoEscaner').val(OF+"-"+id);
        $('#CodigoEscanerSuministro').html('');
    }
    function Retrabajo(Codigo){
        $('#CodigoEscanerEntrada').focus();
        FiltroLinea = $('#FiltroLinea').val();
        if(FiltroLinea == null || FiltroLinea=="" || FiltroLinea<0){
            Mensaje='Para comenzar, en el campo Núm. Línea selecciona la línea en la que vas a trabajar!';
                            Color='bg-danger';
                            $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                            $('#ToastGuardadoBody').html(Mensaje);
                            $('#ToastGuardado').fadeIn();
                            setTimeout(function(){
                                $('#ToastGuardado').fadeOut();
                            }, 3500);
            return 0;
        }
        $.ajax({
            url: "{{route('PreparadoBuscar')}}", 
            type: 'POST',
            data: {
                Codigo: Codigo,
                Inicio:1,
                Linea:FiltroLinea,
                Finalizar:0,
                Retrabajo: 'si',
                Area:'{{$Area}}' 
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
                    $('#CodigoEscanerSalida').val('');
                    $('#CodigoEscanerEntrada').val('');
                }
                RecargarTablaPendientes();
            }
        });
    }
    function TablaList(TableName){
        var options = {
            valueNames: ['NumParte', 'Cantidad', 'Inicio', 'Fin', 'Estatus'],
                //page: 5,  
                //pagination: false,
                filter: {
                            key: 'Estatus' 
                    }
        };
        userList = new List(TableName, options);
        //userList.sort('Inicio', { order: 'desc' });
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
        document.addEventListener('keydown', function(event) {
            if (event.key === 'ArrowLeft') {
                $('#CodigoEscanerEntrada').focus();
            }
            if (event.key === 'ArrowRight') {
                $('#CodigoEscanerSalida').focus();
            }
        });
        $('#Cantidad').on('input', function() {
            RegexNumeros(document.getElementById('Cantidad'));
        });
        $('#CodigoEscanerEntrada').on('input', function() {
            RegexNumerosGuiones(document.getElementById('CodigoEscanerEntrada'));
        });
        $('#CodigoEscanerSalida').on('input', function() {
            RegexNumerosGuiones(document.getElementById('CodigoEscanerSalida'));
        });
        $('#btnEscanear').click(function() {
            CodigoEscaner=$('#CodigoEscanerEntrada').val();
            Cantidad=$('#Cantidad').val();
            Retrabajo=document.getElementById('Retrabajo').checked;
            if(Retrabajo){
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
                        TipoNoEscaner('Entrada');
                    } else {
                        return 0;
                    }
                })
            }else{
                TipoNoEscaner('Entrada');
            }
        });
        $('#btnEscanearSalida').click(function() {
            CodigoEscaner=$('#CodigoEscanerSalida').val();
            Cantidad=$('#CantidadSalida').val();
            TipoNoEscaner('Salida');
        });
        var table = $('#TablaPreparadoPendientes').DataTable({
            "language": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sInfo":           "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando de 0 a 0 de 0 registros",
                "sInfoFiltered":   "(filtrado de _MAX_ registros en total)",
                "sSearch":         "Buscar:",
                "sUrl":            "",
            }
        });
        $('#FiltroLinea').on('change', function() {
                var val = $(this).val();
                if(val == -1) {
                    table.column(8).search('').draw();
                } else {
                    table.column(8).search(val).draw();
                }
        });
        setInterval(RecargarTablaPendientes,180000);//180000
    });
    function TipoNoEscaner(TipoEntrada) {
        CodigoEscaner=$('#CodigoEscanerEntrada').val();
        Cantidad=$('#Cantidad').val();
        Retrabajo=document.getElementById('Retrabajo').checked;
        FiltroLinea = $('#FiltroLinea').val();
        if(FiltroLinea == null || FiltroLinea=="" || FiltroLinea<0){
            Mensaje='Para comenzar, en el campo Núm. Línea selecciona la línea en la que vas a trabajar!';
                            Color='bg-danger';
                            $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                            $('#ToastGuardadoBody').html(Mensaje);
                            $('#ToastGuardado').fadeIn();
                            setTimeout(function(){
                                $('#ToastGuardado').fadeOut();
                            }, 3500);
            return 0;
        }
        if (TipoEntrada=="Entrada") {
            Inicio = 1;
            Fin = 0;
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
                $('#CodigoEscanerEntrada').addClass('is-invalid');
                $('#error_CodigoEscaner').html('Solo se aceptan N&uacute;meros y -');
                return 0;
            } else {
                if ($('#CodigoEscanerEntrada').hasClass('is-invalid')) { $('#CodigoEscanerEntrada').removeClass('is-invalid'); }
                $('#error_CodigoEscaner').html('');
            }
        }
        if (TipoEntrada=="Salida") {
            Inicio = 0;
            Fin = 1;
            CodigoEscaner=$('#CodigoEscanerSalida').val();
            Cantidad=$('#CantidadSalida').val();
            Retrabajo="false";
            // Validación Solo números para Cantidad y mayor a 0
            if (Cantidad <= 0) {
                $('#CantidadSalida').addClass('is-invalid');
                $('#error_CantidadSalida').html('Campo cantidad no puede ser 0');
                return 0;
            } else {
                if ($('#CantidadSalida').hasClass('is-invalid')) { $('#CantidadSalida').removeClass('is-invalid'); }
                $('#error_CantidadSalida').html('');
            }
            if (!/^\d+$/.test(Cantidad)) {
                $('#CantidadSalida').addClass('is-invalid');
                $('#error_CantidadSalida').html('Solo se aceptan N&uacute;meros');
                return 0;
            } else {
                if ($('#CantidadSalida').hasClass('is-invalid')) { $('#CantidadSalida').removeClass('is-invalid'); }
                $('#error_CantidadSalida').html('');
            }
            // Validación Solo Números y -
            if (!/^[-\d]+$/.test(CodigoEscaner)) {
                $('#CodigoEscanerSalida').addClass('is-invalid');
                $('#error_CodigoEscanerSalida').html('Solo se aceptan N&uacute;meros y -');
                return 0;
            } else {
                if ($('#CodigoEscanerSalida').hasClass('is-invalid')) { $('#CodigoEscanerSalida').removeClass('is-invalid'); }
                $('#error_CodigoEscanerSalida').html('');
            }
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
                Linea:FiltroLinea,
                Retrabajo: Retrabajo,
                Area: '{{$Area}}'
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
                    }, 3500);
                }else if(response.status=='PasBackerror'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('La cantidad solicitada aún no puede ser procesada!, Aún no han pasado las piezas del paso anterior');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 3500);
                }else if(response.status=='error'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Ocurrio un error no fue posible guardar la información para codigo'+CodigoEscaner+'!');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 3500);
                }else if(response.status=='success'){
                    if(response.Inicio==1){
                        mensaje='Nueva Entrada del Codigo '+CodigoEscaner+' Guardada!';
                        $('#CodigoEscanerEntrada').focus();
                    }else if(response.Inicio==0){
                        mensaje='Salida del Codigo '+CodigoEscaner+' Guardada!';
                        $('#CodigoEscanerSalida').focus();
                    }
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html(mensaje);
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 3500); 
                    $('#CodigoEscanerEntrada').val('');
                    $('#CodigoEscanerSalida').val('');
                    if(Inicio==1){
                        $('#Cantidad').focus();
                    }else{
                        $('#CantidadSalida').focus();
                    }
                    $('#Cantidad').val(1);
                    $('#CantidadSalida').val(1);
                    if(response.BanderaFinalizar==0){
                        id=response.OF;
                        confirmacionesss(
                                    "¡Orden de Fabricación finalizada!", 
                                    "La orden de Fabricación se encuentra completada", 
                                    "Aceptar", 
                                    function () {
                                        console.log('Orde de Fabricacion Cerrada');
                                        /*$.ajax({
                                            url: '{{ route("finProceso.empacado") }}',
                                            type: "GET",
                                            data: {
                                                id: id,
                                                _token: '{{ csrf_token() }}'
                                            },
                                            success: function (response) {
                                                if (response.codigo=='Success') {
                                                    success('Orden de Fabricacion Finalizada',response.message);
                                                }else{
                                                    error('Ocurrio un error',response.message);
                                                }
                                                setTimeout(function() {
                                                    console.log("Recargando la tabla...");
                                                    cargarTablaEmpacado();
                                                }, 500);
                                            },
                                            error: function (xhr) {
                                                console.error("Error:", xhr);
                                                alert("Error: " + (xhr.responseJSON?.error || "Ocurrió un problema"));
                                            }
                                        });*/

                                    }
                                );
                    }
                }else if(response.status=='SurplusFin'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Error no guardado, la cantidad de salidas supera el total de la cantidad las partidas registradas!');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 3500);
                }else if(response.status=='SurplusRetrabajo'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Error no guardado, Para mandar a retrabajo las ordenes tienen que estar en Estatus "Finalizado" !');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 3500);
                }else if(response.status=='SurplusInicio'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Error no guardado, La cantidad de entradas no puede superar la cantidad Total de la Partida de la Orden de Fabricación !');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 3500);
                }else if(response.status=='SurplusInicioAnt'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Error no guardado, La cantidad de entradas, Aún faltan de procesarse piezas en la area Anterior !');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 3500);
                }
                ListaCodigo(CodigoEscaner,'CodigoEscanerSuministro','Entrada');
                RecargarTablaPendientes();
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
    }
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
                    {"language": {
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
                    if(val == -1) {
                        table.column(8).search('').draw();
                    } else {
                        table.column(8).search(val).draw();
                    }
                });
                $('#FiltroLinea').trigger('change');
            },
            error: function(xhr, status, error) {
               console.log('Ocurrio un error al traer las Ordenes Pendientes' ); 
            }
        });
    }
    function confirmacionesss(titulo, mensaje, confirmButtonText, funcion) {
        return Swal.fire({
            title: titulo,
            text: mensaje,
            icon: "success",
            /*showCancelButton: true,
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",*/
            confirmButtonColor: "#3085d6",
            confirmButtonText: confirmButtonText,
        }).then((result) => {
            return true;
            /*console.log("Resultado de confirmación: ", result); // Verifica el resultado
            if (result.isConfirmed) {
                console.log("Usuario ha confirmado");
                funcion();  // Ejecuta la función pasada como argumento
                return true;
            } else {
                console.log("Usuario ha cancelado");
                return false;
            }*/
        });
    }
</script>
@endsection