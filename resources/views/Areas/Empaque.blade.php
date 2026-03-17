@extends('layouts.menu2')
@section('title', 'Empaque')
@section('styles')
<style>
    #ToastGuardado {
        position: fixed; /* Fixed position */
        top: 5rem; /* Distance from the top */
        right: 20px; /* Distance from the right */
        z-index: 1050; /* Ensure it's above other content */
    }
    #DivCointainerTableSuministro{
        height: 12rem;
        overflow-y: scroll;
    }
    #DivCointainerTableSuministro::-webkit-scrollbar {
        width: 5px;     /* Ancho para scroll vertical */
        height: 1px;    /* Alto para scroll horizontal */
    }
    #DivCointainerTableSuministro::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    #DivCointainerTableSuministro::-webkit-scrollbar-thumb {
        background: #dae1e2; /* Color info de Bootstrap */
        border-radius: 10px;
    }
    #DivCointainerTableSuministro::-webkit-scrollbar-thumb:hover {
        background: #9e9e9e;
    }
</style>
@endsection
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
        <h4 class="mb-2 text-1100">Empaque</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-8 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-header p-2 bg-info" id="filtroEntrada">
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
                        <div class="col-4" id="CantidadDivSalida">
                            <div class="form-group">
                                <label for="Cantidad">Cantidad</label>
                                <input type="text" class="form-control form-control-sm" id="CantidadSalida" aria-describedby="Cantidad" oninput="RegexNumeros(this);" placeholder="0">
                                <div class="invalid-feedback" id="error_CantidadSalida"></div>
                            </div>
                        </div>
                        <div class="col-12 mt-2" id="IniciarBtnSalida">
                            <button class="btn btn-primary btn-sm float-end" type="button" id="btnEscanearSalida"><i class="fa fa-play"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="ContentTabla" class="col-6">
            <div class="card" id="DivCointainerTableSuministro" style="display: none">
            </div>
        </div>
        <div id="ContentTablaPendientes" class="col-12 mt-2">
            <div class="card" id="DivCointainerTablePendientes">
                <h4 class="text-center mt-2 p-0">Ordenes de Fabricaci&oacute;n Pendientes</h4>
                {{--<div class="d-flex justify-content-start">
                    <div class="col-3 mx-2 Apuntarbox" id="Apuntarbox">
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text" id="inputGroup-sizing-sm">Núm. L&iacute;nea:</span>
                            <select class="form-select form-select-sm" aria-label="FiltroLinea" id="FiltroLinea">
                                <option selected="" disabled>Selecciona L&iacute;nea</option>
                                <option value="-1">Todas</option>
                                @foreach($Lineas as $Linea)
                                    <option value="{{$Linea->id}}">{{$Linea->Nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>--}}
                <div class="table-responsive">
                    <table id="TablaPreparadoPendientes" class="table table-sm fs--1 mb-1">
                        <thead>
                            <tr class="bg-light">
                                <th>Orden Fabricación</th>
                                <th>Cantidad Entrante</th>
                                <th>Cantidad Completada</th>
                                <th>Total Orden Fabricaci&oacute;n</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="TablaPreparadoPendientesBody" class="list">
                            <tr>
                                <td colspan='100%' align='center'>
                                    <div class='d-flex justify-content-center align-items-center'>
                                        <div class='spinner-grow text-primary' role='status'>
                                            <span class='visually-hidden'>Loading...</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
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
    //NUEVOS CODIGOS
    let timeout;
    function ListaCodigo(Codigo,Contenedor,TipoEntrada){
        DivCointainerTableSuministro = document.getElementById('DivCointainerTableSuministro');
        DivCointainerTableSuministro.style.display = 'none';
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
        OrdenFabricacion = Codigo.split("-")[0];
        FiltroLinea = 1;
        $('#Cantidad').val('');
        $('#CantidadSalida').val('');
        $.ajax({
            url: "{{route('PreparadoBuscar')}}", 
            type: 'POST',
            data: {
                Codigo: Codigo,
                Linea:FiltroLinea,
                Area:'{{$Area}}'
            },
            beforeSend: function() {
                //$('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
            },
            success: function(response) {
                $('#CantidadDiv').hide();
                $('#IniciarBtn').hide();
                $('#RetrabajoDiv').hide();
                if(response.status=="success"){
                    if(!(TipoEntrada == 'Mostrar' || TipoEntrada == 'Cancelar')){
                        $('#CantidadSalida').focus();
                    }
                    DivCointainerTableSuministro.style.display = '';
                    $('#DivCointainerTableSuministro').html(response.tabla);
                }else{
                    Toast_message(response.status,response.message)
                    $('#DivCointainerTableSuministro').html(response.tabla);
                }
                $('#ContentTabla').show();
                RecargarTablaPendientes();
            },
            error: function(xhr, status, error) {
                $('#CantidadDiv').hide();
                $('#IniciarBtn').hide();
                $('#CantidadDivSalida').hide();
                $('#IniciarBtnSalida').hide();
                $('#CodigoEscanerSalida').focus();
            }
        }); 
        }, 800);
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
        $('#btnEscanearSalida').click(function() {
            CodigoEscaner=$('#CodigoEscanerSalida').val();
            Cantidad=$('#CantidadSalida').val();
            error_CodigoEscanerSalida = $('#error_CodigoEscanerSalida');
            error_CantidadSalida = $('#error_CantidadSalida');
            error_CodigoEscanerSalida.html('');
            error_CantidadSalida.html('');

            if(CodigoEscaner.trim() === ''){
                error_CodigoEscanerSalida.html('* Campo Código es requerido.');
                $('#CodigoEscanerSalida').addClass('is-invalid');
                return 0;
            }else{
                $('#CodigoEscanerSalida').removeClass('is-invalid');
            }
            if(Cantidad<1){
                error_CantidadSalida.html('* Campo Cantidad tiene que ser mayor a 0.');
                $('#CantidadSalida').addClass('is-invalid');
                return;
            }else{
                $('#CantidadSalida').removeClass('is-invalid');
            }
            const btn = document.getElementById('btnEscanearSalida');
            btn.disabled = true;
            TipoNoEscaner(CodigoEscaner,Cantidad);
        });
        $('#FiltroLinea').on('change', function() {
            var val = $(this).val();
            if(!(val == "" || val == null)){
                if(val == -1) {
                    $('#Apuntarbox').addClass('Apuntarbox');
                    table.column(8).search('').draw();
                } else {
                    table.column(8).search(val).draw();
                    $('#Apuntarbox').removeClass('Apuntarbox');
                }
            }
        });
        setInterval(RecargarTablaPendientes,600000);//180000
        RecargarTablaPendientes();
    });
    function TipoNoEscaner(CodigoEscaner,Cantidad) {
        FiltroLinea = $('#FiltroLinea').val();
        /*if( FiltroLinea == null || FiltroLinea=="" || FiltroLinea<0 ){
            Mensaje='Para comenzar, en el campo Núm. Línea selecciona la línea en la que vas a trabajar!';
                    Color='bg-danger';
                    $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white '+Color+' border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
                    $('#ToastGuardadoBody').html(Mensaje);
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                    $('#ToastGuardado').fadeOut();
                    }, 3500);
            return 0;
        }*/
        // Realizar la petición AJAX
        $.ajax({
            url: "{{route('TipoNoEscaner')}}",
            type: 'POST',
            data: {
                Codigo: CodigoEscaner,
                Cantidad: Cantidad,
                Linea:1,//Se asigna la linea por default
                Area: '{{$Area}}'
            },
            beforeSend: function() {
                $('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
            },
            success: function(response) {
                $('#ToastGuardado').fadeOut();
                if(response.status == 'success'){
                    Toast_message(response.status,response.message);
                    $('#CodigoEscanerSalida').val('');
                    ListaCodigo(CodigoEscaner,'CodigoEscanerSuministro','Mostrar');
                    RecargarTablaPendientes();
                    if(response.CantidadTota==response.AreaActual){
                        FinalizarManual(response.id_OF);
                    }
                }else{
                    $('#CodigoEscanerSalida').val('');
                    $('#CantidadSalida').val('');
                    Toast_message(response.status,response.message);
                }
                $('#CodigoEscanerSalida').focus();
            },
            error: function(xhr, status, error) {
                $('#ContainerToastGuardado').html('<div id="ToastGuardado" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex justify-content-around"><div id="ToastGuardadoBody" class="toast-body"></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button></div></div>'); 
                    $('#ToastGuardadoBody').html('Error, Ocurrió un problema, revisa tu conexión, si el percance persiste contacta a TI!');
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 3000);
            },
            complete: function() {
                document.getElementById('btnEscanearSalida').removeAttribute('disabled');
                const btn = document.getElementById('btnEscanearSalida');
                btn.disabled = false;
            }
        });
    }
    function Toast_message(status,mensaje){
        if(status=='error'){
            status = 'danger';
        }
        $('#ContainerToastGuardado').html( `<div id="ToastGuardado" class="toast align-items-center text-white bg-${status} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                                            <div class="d-flex justify-content-around">
                                                <div id="ToastGuardadoBody" class="toast-body"></div>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                                            </div>
                                            </div> `); 
                    $('#ToastGuardadoBody').html(mensaje);
                    $('#ToastGuardado').fadeIn();
                    setTimeout(function(){
                        $('#ToastGuardado').fadeOut();
                    }, 3500); 
    }
    //END
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
    //url: "{{route('AreaTablaPendientes')}}",
    function RecargarTablaPendientes(){
        $.ajax({
            url: "{{route('tablaEmpacado')}}",
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
                    if(!(val == "" || val == null)){
                        if(val == -1) {
                            table.column(8).search('').draw();
                        } else {
                            table.column(8).search(val).draw();
                        }
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
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: confirmButtonText,
        }).then((result) => {
            console.log("Resultado de confirmación: ", result); // Verifica el resultado
            if (result.isConfirmed) {
                console.log("Usuario ha confirmado");
                funcion();  // Ejecuta la función pasada como argumento
                return true;
            } else {
                console.log("Usuario ha cancelado");
                return false;
            }
        });
    }
    async function CancelarPartida(id,Codigo) {
        const confirmacionRespuesta = await confirmacionesss(
        "¿Estás seguro de que deseas cancelar esta partida?", 
        "", 
        "Confirmar", 
        function() {
            $.ajax({
                url: '{{ route("regresar.proceso") }}',
                method: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status == "success") {
                        Toast_message('success',response.message);
                        $('#registro-' + id).remove();  
                        //ListaCodigo(response);  
                        ListaCodigo(Codigo,'CodigoEscanerSuministro',"Cancelar")
                        setTimeout(function() {
                            console.log("Recargando la tabla...");
                            RecargarTablaPendientes();  
                        }, 500);  
                    } else {
                        Toast_message('error',response.message);
                    }
                },
                error: function(xhr, status, error) {
                    Toast_message('error','Hubo un error al cancelar la partida.');
                }
            });
        }
        );
        if (!confirmacionRespuesta) {
            toastr.info('La acción fue cancelada', 'Información');
        }
    }
    function FinalizarManual(idOF){
        let id = idOF;
        confirmacionesss(
            "Finalizar Orden de Fabricación", 
            "¿Estás seguro de que deseas finalizar esta orden de Fabricación?", 
            "Confirmar", 
            function () {
                $.ajax({
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
                            RecargarTablaPendientes();
                        }, 500);
                    },
                    error: function (xhr) {
                        console.error("Error:", xhr);
                        alert("Error: " + (xhr.responseJSON?.error || "Ocurrió un problema"));
                    }
                });
            }
        );
    } 
</script>
@endsection