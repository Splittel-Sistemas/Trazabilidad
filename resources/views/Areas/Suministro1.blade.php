@extends('layouts.menu2')
@section('title', 'Suministro')
@section('styles')
<link rel="stylesheet" href="{{asset('css/Suministro.css')}}">
<style>
    #ToastGuardado {
        position: fixed; 
        top: 5rem; 
        right: 20px; 
        z-index: 1050; 
    }
</style>
@endsection
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
        <h4 class="mb-2 text-1100">Suministro</h4>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body row">
                    <div class="table-responsive">
                        <table id="OrdenFabricacion" class="table table-sm fs--1 mb-0">
                            <thead>
                                <tr class="bg-light">
                                    <th>&Oacute;rden Fabricaci&oacute;n </th>
                                    <th>Art&iacute;culo</th>
                                    <th>Descripcion</th>
                                    <th>Cantidad Total</th>
                                    <th>Fecha planeaci&oacute;n</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="OrdenFabricacionBody">
                                @foreach ($OrdenFabricacion as $OrdenFabricacionpartida)
                                    <tr>
                                        <td>{{ $OrdenFabricacionpartida ->OrdenFabricacion }}</td>
                                        <td>{{ $OrdenFabricacionpartida ->Articulo }}</td>
                                        <td>{{ $OrdenFabricacionpartida ->Descripcion }}</td>
                                        <td>{{ $OrdenFabricacionpartida ->CantidadTotal }}</td>
                                        <td>{{ $OrdenFabricacionpartida ->FechaEntrega }}</td>
                                        <td><button class="btn btn-sm btn-outline-primary" onclick="Planear('{{$OrdenFabricacionpartida->idEncript}}')">Planear</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--MODAL PARA PLANEACION-->
    <div class="modal fade" id="ModalSuministro" tabindex="-1" data-bs-backdrop="static" aria-labelledby="ModalSuministroLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header bg-info">
              <h5 class="modal-title text-white" id="ModalSuministroLabel">Emisiones</h5><button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-white"></span></button>
            </div>
            <div class="modal-body" id="ModalSuministroBody">
            </div>
            <div class="modal-footer" id="ModalSuministroFooter">
                <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
    <div  id="ContainerToastGuardado"></div>
@endsection
@section('scripts')
<script>
     $(document).ready(function() {
        $('#OrdenFabricacion').DataTable({
            "pageLength": 10,  // Paginación de 10 elementos por página
            "lengthChange": false, // Desactiva la opción de cambiar el número de elementos por página
            "paging": true, // Habilitar paginación
            "searching": true, // Habilitar búsqueda
            "ordering": true, // Habilitar ordenación de columnas
            "info": true, // Muestra información sobre el total de elementos
        });
        setInterval(RecargarTabla, 300000);
    });

    function RecargarTabla(){
        $.ajax({
            url: "{{route('SuministroRecargarTabla')}}", 
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'  
            },
            success: function(response) {
                if(response.status=="success"){
                    $('#OrdenFabricacion').DataTable().destroy();
                    $('#OrdenFabricacionBody').html(response.table);
                    $('#OrdenFabricacion').DataTable({
                        "pageLength": 10,  // Paginación de 10 elementos por página
                        "lengthChange": false, // Desactiva la opción de cambiar el número de elementos por página
                        "paging": true, // Habilitar paginación
                        "searching": true, // Habilitar búsqueda
                        "ordering": true, // Habilitar ordenación de columnas
                        "info": true, // Muestra información sobre el total de elementos
                        "language": {
                            "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                        }
                    });
                }
            }
        });
    }
    function Planear(OrdenFabricacionpartidaId){
        $('#ModalSuministro').modal('show');
        $.ajax({
            url: "{{route('SuministroEmision')}}", 
            type: 'POST',
            data: {
                id:OrdenFabricacionpartidaId,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#ModalSuministroBody').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'); 
            },
            success: function(response) {
                if(response.status == "success"){
                    $('#ModalSuministroBody').html(response.table);
                    $('#TableEmisiones').DataTable().destroy();
                    $('#TableEmisiones').DataTable({
                        "pageLength": 5,  // Paginación de 10 elementos por página
                        "lengthChange": false, // Desactiva la opción de cambiar el número de elementos por página
                        "paging": true, // Habilitar paginación
                        "searching": true, // Habilitar búsqueda
                        "ordering": true, // Habilitar ordenación de columnas
                        "info": false, // Muestra información sobre el total de elementos
                    });
                }
                else if(response.status == "empty"){
                    $('#ModalSuministroBody').html('<div class="alert alert-danger" role="alert">La Orden de Fabricacion no existe, o se cerro espontaneamente!</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#ModalSuministroBody').html('<div class="alert alert-danger" role="alert">Ocurrio un error, reviza tu conexión!</div>');
            }
        });
    }
    function GuardarEmision(NumEmision,Cantidad){
        confirmacion("Guardar","¿Deseas Guardar la partida de la Emisión?","Confirmar",'GuardarEmision()');
    }
</script>
{{--<script src="{{ asset('js/Suministro.js') }}"></script>
<script>
    function ListaCodigo(Codigo,Contenedor){
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
                Area:'{{$Area}}',
                _token: '{{csrf_token()}}'  
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
                                case 6:
                                    Mensaje='Codigo <strong>'+Codigo+'</strong> Aún no termina el proceso posterior!';
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
                        $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                        $('#ToastGuardadoBody').html('El codigo No existe!  ');
                        $('#ToastGuardado').fadeIn();
                        setTimeout(function(){
                            $('#ToastGuardado').fadeOut();
                        }, 2000);
                }
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
                    $('#ToastGuardadoBody').html('Aún no se ha completado el proceso de Corte!');
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
</script>--}}
@endsection