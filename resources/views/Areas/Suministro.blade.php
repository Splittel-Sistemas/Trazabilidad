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
        </div>
        <div id="ContentTabla" class="col-12 mt-2" style="display: none">
            <div class="card" id="DivCointainerTableSuministro">
            </div>
        </div>
        </div>
    </div>
    <div  id="ContainerToastGuardado"></div>
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
                $('#RetrabajoDiv').hide();
                document.getElementById('Retrabajo').checked = false;
                if(response.status=="success"){
                    $('#DivCointainerTableSuministro').html(response.tabla);
                    if(response.Escaner==1){
                        TablaList(DivCointainerTableSuministro);
                    }
                    $('#CantidadPartidasOF').html('<span class="badge bg-light text-dark">Piezas procesadas '+response.CantidadCompletada+"/"+response.CantidadTotal+'</span>');
                    $('#TituloPartidasOF').html(response.OF);
                    if(response.Escaner==0){
                        TablaList(DivCointainerTableSuministro);
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
                    //if(response.Escaner!=0){
                        $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
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
                // Manejar la respuesta del servidor si es necesario
            },
            error: function(xhr, status, error) {
                /*Swal.fire({
                    icon: "error",
                    title: 'Error: datos no válidos',
                    text: xhr.responseJSON.message,
                    showConfirmButton: false,
                    timer: 2500
                });*/
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
@endsection