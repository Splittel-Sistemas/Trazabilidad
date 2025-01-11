@extends('layouts.menu2')
@section('title', 'Suministro')
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
        <h4 class="mb-2 text-1100">Suministro</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
              <div class="card shadow-sm">
                {{--<div class="card-header bg-info">
                    <strong>   </strong>
                </div>--}}
                <div class="card-body row" id="filtro">
                    <label for="CodigoEscaner" class="col-form-label col-sm-12 pt-0">Proceso <span class="text-muted"></span></label>
                    <div class="col-8">
                            <div class="form-check form-check-inline ">
                                <input class="form-check-input" type="radio" name="TipoProceso" id="Iniciar" checked>
                                <label class="form-check-label" for="Iniciar">
                                  Entrada
                                </label>
                            </div>
                            <div class="form-check form-check-inline ">
                                <input class="form-check-input" type="radio" name="TipoProceso" id="Finalizar">
                                <label class="form-check-label" for="Finalizar">
                                  Salida
                                </label>
                            </div>
                    </div>
                    <hr>
                    <div class="col-8" id="CodigoDiv">
                        <div class="">
                            <label for="CodigoEscaner">C&oacute;digo <span class="text-muted">&#40;Escanea o Ingresa manual&#41;</span></label>
                            <!--<a href=""><i class="fa fa-toggle-on"></i></a>-->
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" oninput="ListaCodigo(this.value,'CodigoEscanerSuministro')" id="CodigoEscaner" aria-describedby="CodigoEscanerHelp" placeholder="Escánea o ingresa manualmente.">
                            </div>
                            <div class=" mt-1 list-group-sm" id="CodigoEscanerSuministro">
                            </div>
                        </div>
                    </div>
                    <div class="col-4" id="CantidadDiv" style="display: none">
                        <div class="form-group">
                            <label for="Cantidad">Cantidad</label>
                                <input type="text" class="form-control form-control-sm" id="Cantidad" aria-describedby="Cantidad" value="1" placeholder="Ingresa cantidad recibida.">
                        </div>
                    </div>
                    <div class="col-12" id="IniciarBtn" style="display: none">
                        <button class="btn btn-primary btn-sm float-end" type="button" id="btnEscanear"><i class="fa fa-play"></i> Iniciar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="ContentTabla" class="col-12 mt-2" style="display: none">
            <div class="card">
                <div class="card-body">
                    <div id="tableExample2" class="table-list">
                        <div class="row justify-content-end g-0">
                            <div class="col-auto px-3"><select class="form-select form-select-sm mb-3" data-list-filter="data-list-filter">
                                <option selected="" value="">Todos</option>
                                <option value="Enproceso">En proceso</option>
                                <option value="Completado">Completado</option>
                            </select>
                            </div>
                        </div>
                        <div class="table-responsive scrollbar mb-3">
                        <table class="table table-striped table-sm fs--1 mb-0 overflow-hidden">
                            <thead>
                                <tr>
                                    <th class="sort border-top ps-3" data-sort="Num_Parte">Num. Parte</th>
                                    <th class="sort border-top" data-sort="Cantidad">Cantidad</th>
                                    <th class="sort border-top" data-sort="Inicio">Inicio</th>
                                    <th class="sort border-top" data-sort="Fin">Fin</th>
                                    <th class="sort border-top" data-sort="Estatus">Estatus</th>
                                    <th class="sort border-top" >Acci&oacute;nes</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="TablaBody">
                                <td class="align-middle ps-3" colspan="6">No existen Proesos iniciados </td>
                            </tbody>
                        </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                            <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                        </div>
                    </div>
                </div>
            </div>
            
            
            
            
            
            <!--<div class="card">
                <h5 class="mb-2">Partidas de la Orden de Fabricacion <span id="TituloPartidasOF"></span> <br><span id="CantidadPartidasOF"></span></h5>
                <table class="table table-sm table-bordered table-striped table-hover m-1">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Num. Parte</th>
                        <th>Cantidad</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Estatus</th>
                        <th>Acci&oacute;nes</th>
                    </tr>
                </thead>
                <tbody id="TablaBody">
                </tbody>
                </table>    
            </div>-->
        </div>
    </div>
    <div id="ContainerToastGuardado"></div>
@endsection
@section('scripts')
<script src="{{ asset('js/Suministro.js') }}"></script>
<script>
    function ListaCodigo(Codigo,Contenedor){
        //VerNumParte=VerNumParte($Codigo);
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
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
            },
            success: function(response) {
                $('#CantidadDiv').hide();
                $('#IniciarBtn').hide();
                if(response.status=="success"){
                    $('#TablaBody').html(response.tabla);
                    $('#CantidadPartidasOF').html('<span class="badge bg-light text-dark">Piezas procesadas '+response.CantidadCompletada+"/"+response.CantidadTotal+'</span>');
                    $('#TituloPartidasOF').html(response.OF);
                    if(response.Escaner==0){
                        $('#CantidadDiv').fadeOut();
                        $('#IniciarBtn').fadeOut();
                        if(response.EscanerExiste==0){
                            Mensaje='Codigo '+Codigo+' El codigo que intentas ingresar No existe!';
                            Color='bg-danger';
                            $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                            $('#ToastGuardadoBody').html(Mensaje);
                            $('#ToastGuardado').fadeIn();
                            setTimeout(function(){
                                $('#ToastGuardado').fadeOut();
                            }, 2000);
                        }else{
                            $('#ContentTabla').show();
                            $('#CantidadDiv').fadeIn();
                            $('#IniciarBtn').fadeIn();
                            return 0;
                        }
                    }else{
                        $('#ContentTabla').show();
                        Mensaje="";
                        if(response.Inicio==1){
                            switch (response.TipoEscanerrespuesta) {
                                case 1:
                                //$('#CodigoEscaner').val('');
                                    Mensaje='Codigo '+Codigo+' guardado correctamente!';
                                    Color='bg-success';
                                    break;
                                case 2:
                                    Mensaje='Codigo '+Codigo+' Ya se encuentra iniciado!';
                                    Color='bg-warning';
                                    break;
                                case 3:
                                    confirmacion('Retrabajo','¿Desea enviar codigo '+Codigo+' a Retrabajo? ','Confirmar','Retrabajo("'+Codigo+'")');
                                    return 0;
                                    break;
                                case 4:
                                    Mensaje='Codigo '+Codigo+' No existe!';
                                    Color='bg-danger';
                                    $('#ContentTabla').hide();
                                    $('#CantidadPartidasOF').html('');
                                    break;
                                case 5:
                                    Mensaje='Codigo '+Codigo+' Aún no termina el proceso anterior!';
                                    Color='bg-danger';
                                    $('#ContentTabla').hide();
                                    $('#CantidadPartidasOF').html('');
                                    break;
                                default:
                                    Mensaje='Codigo '+Codigo+' Ocurrio un error!';
                                    Color='bg-danger';
                                    break;
                            }

                        }
                        if(response.Finalizar==1){
                            switch (response.TipoEscanerrespuesta) {
                                case 1:
                                    Mensaje='Codigo '+Codigo+' Finalizado!';
                                    Color='bg-success';
                                    break;
                                case 2:
                                    Mensaje='Codigo '+Codigo+' Aún no ha sido inicializado!';
                                    Color='bg-danger';
                                    break;
                                case 3:
                                    Mensaje='Codigo '+Codigo+' No se encontro el Codigo!';
                                    Color='bg-danger';
                                    break;
                            
                                default:
                                    Mensaje='Codigo '+Codigo+' Ocurrio un error!';
                                    Color='bg-danger';
                                    break;
                                    break;
                            }

                        }
                        $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                        $('#ToastGuardadoBody').html(Mensaje);
                        $('#CantidadDiv').fadeOut();
                        $('#IniciarBtn').fadeOut();
                    }
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2500);
                }else if(response.status=="empty"){
                    //if(response.Escaner!=0){
                        $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                        $('#ToastGuardadoBody').html('El codigo No existe!  ');
                        $('#ToastGuardado').fadeIn();
                        setTimeout(function(){
                            $('#ToastGuardado').fadeOut();
                        }, 2000);
                    //}
                }
                /*document.getElementById('CodigoEscanerSuministro').style.display = "";
                if(response.Escaner==1){
                    $('#CantidadDiv').hide();
                    $('#IniciarBtn').hide();
                }else if(response.Escaner==0){
                    $('#CantidadDiv').show();
                    $('#IniciarBtn').show();
                    $('#CodigoEscanerSuministro').html(response.menu);
                }*/
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
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
            },
            success: function(response) {
                if(response.status=="success"){
                    //$('#CodigoEscaner').val('');
                    Mensaje='Codigo '+Codigo+' Se agrego a Retrabajo!';
                    Color='bg-warning';
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                    $('#ToastGuardadoBody').html(Mensaje);
                    $('#TablaBody').html(response.tabla);
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

</script>
@endsection
