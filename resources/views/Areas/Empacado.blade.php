@extends('layouts.menu2')
@section('title', 'Empaquetado')
@section('styles')
<link rel="stylesheet" href="{{asset('css/Suministro.css')}}">
<style>
    #ToastGuardado {
        position: fixed; 
        top: 5rem;
        right: 20px; 
        z-index: 1050; 
    }
    #DivCointainerTableSuministro {
        width: 100%;
        display: block;
        height: 11.5rem;
        overflow-y: scroll;
    }
    #DivCointainerTableSuministro::-webkit-scrollbar {
        width: 8px; 
    }
    #DivCointainerTableSuministro::-webkit-scrollbar-track {
        background-color: #f1f1f1;
    }
    #DivCointainerTableSuministro::-webkit-scrollbar-thumb {
        background-color: #888; 
        border-radius: 10px; 
    }
    #DivCointainerTableSuministro::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }

</style>
@endsection
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
        <h4 class="mb-2 text-1100">Empaquetad&oacute;</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
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
                            <div class="form-check form-check-inline " style="display: none;">
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
        </div>
        <div id="ContentTabla" class="col-6" style="display: none">
            <div class="card" id="DivCointainerTableSuministro">
            </div>
        </div>
        <div class="col-12">
            <div style="height: 30px;"></div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive card">
                        <table id="EmpacadoTable" class="table table-sm">
                            <thead>
                                <tr class="bg-light">
                                    <th  class="text-center">Orden de Venta</th>
                                    <th  class="text-center">Orden Fabricación</th>
                                    <th  class="text-center">Total de Piezas</th>
                                    <th  class="text-center">Cantidad Registrada</th>
                                    <th  class="text-center">Fecha Entrega</th>
                                    <th  class="text-center">Acción</th>

                                </tr>
                            </thead>
                            <tbody id="EmpacadoTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <div  id="ContainerToastGuardado"></div>
@endsection
@section('scripts')
<script>
    var puedeFinalizar = @json(Auth::user()->hasPermission("Finalizar Trazabilidad"));
</script>
<script src="{{ asset('js/Suministro.js') }}"></script>
<script>
    function ListaCodigo(Codigo,Contenedor){
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
            url: "{{route('EmpaquetadoBuscar')}}", 
            type: 'POST',
            data: {
                Codigo: Codigo,
                Retrabajo: 'no',
                Inicio:Inicio,
                Finalizar:Finalizar,
                Area:'{{$Area}}',
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                //$('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
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
                            (DivCointainerTableSuministro);
                        }
                    }
                    $('#CantidadPartidasOF').html('<span class="badge bg-light text-dark">Piezas procesadas '+response.CantidadCompletada+"/"+response.CantidadTotal+'</span>');
                    $('#TituloPartidasOF').html(response.OF);
                    if(response.Escaner==0){
                        if((response.tabla).includes('<td')){
                            (DivCointainerTableSuministro);
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
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Ya Registrado!';
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
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> El codigo Aún no ha sido inicializado!';
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
                    //if(response.Escaner!=0){
                        $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                        $('#ToastGuardadoBody').html('El codigo No existe!  ');
                        $('#ToastGuardado').fadeIn();
                        setTimeout(function(){
                            $('#ToastGuardado').fadeOut();
                        }, 2000);
                    //}
                }else if(response.status=="NoExiste"){
                        $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                        $('#ToastGuardadoBody').html('El codigo No existe!  ');
                        $('#ToastGuardado').fadeIn();
                        setTimeout(function(){
                            $('#ToastGuardado').fadeOut();
                        }, 2000);
                }
                //cargarTablaEmpacado();
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
            url: "{{route('EmpaquetadoBuscar')}}", 
            type: 'POST',
            data: {
                Codigo: Codigo,
                Inicio:1,
                Finalizar:0,
                Retrabajo: 'si',
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
                    
                    $('#DivCointainerTableSuministro').html(response.tabla);
                    if(response.Escaner==1){
                        ('ContainerTableSuministros');
                    }
                    $('#CantidadPartidasOF').html('<span class="badge bg-light text-dark">Piezas procesadas '+response.CantidadCompletada+"/"+response.CantidadTotal+'</span>');
                    $('#TituloPartidasOF').html(response.OF);
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 2500);
                }
                //cargarTablaEmpacado();
            }
        });
    }
    /*
    function TablaList(TableName){
        var options = {
            valueNames: ['NumParte', 'Cantidad'],
                page: 10,  
                pagination: true,
               
        };
        userList = new List(TableName, options);
        userList.sort('Inicio', { order: 'desc' });
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
    }*/
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
                    $('#ToastGuardadoBody').html('La cantidad solicitada aún no puede ser procesada!, Aún no han pasado las piezas del paso anterior');
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
                }else if(response.status=='SurplusInicioAnt'){
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Error no guardado, La cantidad de entradas, Aún faltan de procesarse piezas en la area Anterior !');
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
    }
</script>


<script>

$(document).ready(function () {
    cargarTablaEmpacado();

    setInterval(cargarTablaEmpacado, 30000);

    function cargarTablaEmpacado() {
        $.ajax({
            url: '{{ route("tabla.principal") }}',
            type: "GET",
            dataType: "json",
            success: function (data) {
                let tabla = $('#EmpacadoTable');

                if ($.fn.DataTable.isDataTable(tabla)) {
                    tabla.DataTable().destroy();
                }

                let tbody = $("#EmpacadoTableBody");
                tbody.empty();

                data.forEach((item) => {
                    let cantidad = item.Areas_id == 9 ? item.Cantidad : "0";

                    let botonFinalizar = (puedeFinalizar) 
                        ? `<button class="btn btn-sm btn-danger finalizar-btn" data-id="${item.OrdenFabricacion}">Finalizar</button>`
                        : '';

                    let fila = `
                        <tr>
                            <td class="text-center">${item.OrdenVenta}</td>
                            <td class="text-center">${item.OrdenFabricacion}</td>
                            <td class="text-center">${item.CantidadTotal}</td>
                            <td class="text-center">${cantidad}</td>
                            <td class="text-center">${item.FechaEntrega}</td>
                            <td class="text-center">${botonFinalizar}</td>
                        </tr>
                    `;
                    tbody.append(fila);
                });

                DataTable('EmpacadoTable', true);


                $(".finalizar-btn").off("click").on("click", function () {
                    let id = $(this).data("id");

                    console.log("ID de la orden a finalizar:", id);

                    $.ajax({
                        url: '{{ route("finProceso.empacado") }}',
                        type: "GET",
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            alert(response.message); 
                            location.reload(); 
                        },
                        error: function (xhr) {
                            alert("Error: " + (xhr.responseJSON?.error || "Ocurrió un problema"));
                        }
                    });
                });
            },
            error: function () {
                console.log("Error al cargar los datos de la tabla.");
            },
        });
    }






    function DataTable(tabla, busqueda) {
        $('#' + tabla).DataTable({
            "pageLength": 10,
            "lengthChange": false,
            "paging": true,
            "searching": busqueda,
            "ordering": true,
            "info": true,
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                "search": "Buscar",
                
            },
            "initComplete": function(settings, json) {
                $('#' + tabla).css('font-size', '0.7rem');
            }
        });
    }
});


</script>
@endsection










