@extends('layouts.menu2')
@section('title', 'Clasificación')
@section('styles')
<style>
    #ToastGuardado {
        position: fixed; /* Fixed position */
        top: 5rem; /* Distance from the top */
        right: 20px; /* Distance from the right */
        z-index: 1050; /* Ensure it's above other content */
    }
    #myTab li a{
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        padding: 0.5rem 1rem 0.5rem 1rem;
    }
    #myTab li a:hover{
        background: #f1f1f1;
        border: solid 1px #e7e7e7;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    #myTab li .active{
        border: solid 1px #e7e7e7;
        border-bottom: solid white;
    }
    #myTab li .active:hover{
        border: solid 1px #e7e7e7;
        border-bottom: solid white;
    }
    #hr-menu{
        padding: 0;
        margin: 0;
    }
    #FiltroOrdenFabricacionContent{
        max-height: 6rem; 
        overflow: auto;
    }
</style>
@endsection
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Clasificaci&oacute;n</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-6 mb-2 ">
            <div class="col-sm-12">
                <div class="card border border-light ">
                    <div class="card-body p-2">
                        <div class="accordion" id="accordionFiltroOV">
                            <div class="accordion-item border-top border-300 p-0">
                                <h4 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroOV" aria-expanded="true" aria-controls="collapseFiltroOV">
                                        Porcentaje de planeaci&oacute;n &nbsp;<span id="Fecha_Grafica"> {{\Carbon\Carbon::parse($FechaFin)->translatedFormat('d \d\e F \d\e Y')}}</span>
                                    </button>
                                </h4>
                                <div class="accordion-collapse collapse show" id="collapseFiltroOV" aria-labelledby="headingOne" data-bs-parent="#accordionFiltroOV">
                                    <div class="accordion-body pt-0">
                                        <div class="card-body p-1">
                                            <div class="d-flex justify-content-between">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="text-700">Cantidad personas: <span id="Cantidadpersonas">0</span></h6>
                                                        <h6 class="text-700">Estimado de piezas por d&iacute;a: <span id="Estimadopiezas">0</span></h6>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="text-700 ">Linea <span id="NumeroLinea">0</span></h6> 
                                                        <h6 class="text-700">Piezas Asignadas: <span id="Piezasplaneadas">0</span></h6>
                                                        <h6 class="text-700">Piezas faltantes: <span id="Piezasfaltantes">0</span></h6>
                                                    </div>
                                                    <div class="col-12">
                                                        <button class="btn btn-link mx-5 p-0" type="button" data-bs-toggle="modal" onclick="LlenarModalPorcentajes()" data-bs-target="#ParametrosPorcentaje">
                                                            <i class="far fa-edit"></i> Capacidad productiva
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class=" d-flex justify-content-center aling-items-center">
                                                   <div class="p-0" id="PrcentajePlaneacion" style="width: 9rem;height:9rem"></div>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="bullet-item bg-primary me-2"></div>
                                                    <h6 class="text-900 fw-semi-bold flex-1 mb-0">Porcentaje Planeadas</h6>
                                                    <h6 class="text-900 fw-semi-bold mb-0"><span id="Porcentajeplaneada">0</span>%</h6>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="bullet-item bg-primary-200 me-2"></div>
                                                    <h6 class="text-900 fw-semi-bold flex-1 mb-0">Porcentaje Faltantes</h6>
                                                    <h6 class="text-900 fw-semi-bold mb-0"><span id="Porcentajefaltante">0</span>%</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 mb-2 ">
            <div class="col-sm-12">
                <div class="card border border-light ">
                    <div class="card-body p-2">
                        <div class="accordion" id="accordionFiltroOV">
                            <div class="accordion-item border-top border-300 p-0">
                                <h4 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroOV" aria-expanded="true" aria-controls="collapseFiltroOV">
                                    <span>Parametros</span>
                                    </button>
                                </h4>
                                <div class="accordion-collapse collapse show" id="collapseFiltroOV" aria-labelledby="headingOne" data-bs-parent="#accordionFiltroOV">
                                    <div class="accordion-body pt-0">
                                        <div class="card-body p-1">
                                            <div class="row">
                                                <div class="col-6 mb-0 pt-0">
                                                    <label for="lineaModal" >Línea</label>
                                                    <select name="lineaModal" id="lineaModal" class="form-select form-select-sm border-primary w-100">
                                                        @foreach($Lineas as $L)
                                                            <option value="{{$L->id}}">{{$L->Nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6 m-0">
                                                    <div class="form-row">
                                                        <div class="col-12 mb-1">
                                                            <label for="Filtrofecha_table2">Fecha de asignaci&oacute;n</label>
                                                            <div class="input-group">
                                                                <input type="date" name="FiltroOF_Fecha_table2"  id="FiltroOF_Fecha_table2" class="form-control form-control-sm   w-autoborder-primary col-12" placeholder="Ingresa Orden de fabricación" value="{{\Carbon\Carbon::parse($FechaFin)->translatedFormat('Y-m-d')}}">
                                                                <button id="buscarOV_vencidas" class="btn btn-primary btn-sm">
                                                                    Mostrar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 mt-4">
                                                     <div class="form-group form-group-sm">
                                                        <label for="FiltroOrdenFabricacion">Buscar:</label>
                                                        <input type="text" oninput="BuscarOrden(this);RegexNumeros(this);" class="form-control" id="FiltroOrdenFabricacion" aria-describedby="FiltroOrdenFabricacionHelp" placeholder="Ingresa Orden de Fabricación">
                                                        <div id="FiltroOrdenFabricacionContent">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            {{--<div class="row">
                <div class="col-4 mb-0 pt-0 ms-auto">
                    <label for="lineaModal" >Selecciona línea</label>
                    <select name="lineaModal" id="lineaModal" class="form-select form-select-sm border-primary w-100">
                        @foreach($Lineas as $L)
                            <option value="{{$L->id}}">{{$L->Nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 m-0">
                    <div class="form-row">
                        <div class="col-12 mb-1">
                            <label for="Filtrofecha_table2">Selecciona la fecha de planeaci&oacute;n:</label>
                            <div class="input-group">
                                <input type="date" name="FiltroOF_Fecha_table2"  id="FiltroOF_Fecha_table2" class="form-control form-control-sm   w-autoborder-primary col-12" placeholder="Ingresa Orden de fabricación" value="{{\Carbon\Carbon::parse($FechaFin)->translatedFormat('Y-m-d')}}">
                                <button id="buscarOV_vencidas" class="btn btn-primary btn-sm">
                                    Mostrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>--}}
            <div class="tab-content mt-4" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-proceso" role="tabpanel" aria-labelledby="proceso-tab">
                    <div id="ContentTabla" class="col-12 mt-2">
                        <div class="card" id="DivCointainerTableSuministro">
                            <div class="table-responsive">
                                <table id="TablaClasificacionAbiertas" class="table table-sm fs--1 mb-1">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>Orden Fabricación</th>
                                            <th>Artículo</th>
                                            <th>Descripción</th>
                                            <th>Cantidad Pendiente de asignar</th>
                                            <th>Cantidad Total Orden de Fabricacion</th>
                                            <th>Esc&aacute;ner</th>
                                            <th class="text-center" colspan="2">Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--MODAL DETALLE-->
    <div class="modal fade" id="ModalDetalle" data-bs-backdrop="static" aria-labelledby="ModalDetalleLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="height: 90%">
          <div class="modal-content" style="height: 100%">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="ModalDetalleLabel">Clasificar por L&iacute;neas</h5><button class="btn" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-white"></span></button>
            </div>
            <div class="modal-body" id="ModalDetalleBody">
                <div class="" id="ModalDetalleBodyInfoOF">
                </div>
            </div>
            <div class="modal-footer" id="ModalDetalleFooter">
                <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
    </div>
    <!--Modal Parametros-->
    <div class="modal fade" id="ParametrosPorcentaje" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="ParametrosPorcentajeLabel">Modificar Par&aacute;metros L&iacute;nea <span id="NumeroLinea1">0</span></h5><button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!--<div class="mb-3 col-8">
                        <label class="form-label" for="CantidadPersona">Grupo:</label>
                        <select class="form-control" id="GrupoPersona">
                            <option value="">Selecciona un color</option>
                            <option value="Rojo">Rojo</option>
                            <option value="Verde">Verde</option>
                            <option value="Gris">Gris</option>
                        </select>
                        <div class="invalid-feedback" id="error_CantidadPersona"></div>
                    </div>-->
                    <div class="mb-1 col-6">
                        <label class="form-label" for="CantidadPersona">Cantidad de personas:</label>
                        <input class="form-control" id="CantidadPersona" oninput="RegexNumeros(this)" type="text" placeholder="Ingresa una cantidad" />
                        <div class="invalid-feedback" id="error_CantidadPersona"></div>
                    </div>
                    <div class="mb-1 col-6">
                        <label class="form-label" for="Piezaspersona">Piezas por persona:</label>
                        <input class="form-control" id="Piezaspersona" oninput="RegexNumeros(this)" type="text" placeholder="Ingresa una cantidad" />
                        <div class="invalid-feedback" id="error_Piezaspersona"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary" onclick="GuardarParametrosPorcentajes()">Guardar</button><button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cancelar</button></div>
          </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        RecargarTabla();
        setInterval(RecargarTabla,180000);//180000
    });
    function RecargarTabla(){
        $('#TablaClasificacionAbiertas').DataTable({
            destroy: true,
            ajax: {
                url: "{{ route('ClasificacionRecargarTabla') }}",
                dataSrc: 'data'
            },
            columns: [
                { data: 'OrdenFabricacion' },
                { data: 'Articulo' },
                { data: 'Descripcion' },
                { data: 'CantidadPendiente' },
                { data: 'CantidadTotal' },
                { data: 'Escaner' },
                { data: 'Asignar' },
                { data: 'Finalizar' }
            ],
            columnDefs: [
                { targets: [5, 6, 7], orderable: false }, // Opcional: desactiva orden en acciones
                { targets: [5, 6, 7], searchable: false }, // Opcional: desactiva búsqueda
                { targets: [5, 6, 7], className: 'text-center' } // Centra los botones y checkbox
            ],
            language: {
                            "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                            "search":"Buscar",
                        },
                        "initComplete": function(settings, json) {
                            $('#'+tabla).css('font-size', '0.7rem');
                        },
            rowCallback: function(row, data, index) {
                if (data.Urgencia === 'U') {
                    $(row).css('background-color', '#8be0fc');
                }
            },
            lengthChange: false
        });
    }
    function AsignarLinea(id){
        $('#ModalDetalle').modal('show');
        $.ajax({
            url: "{{route('ClasificacionInfoModal')}}", 
            type: 'POST',
            data: {
                id:id,
            },
            beforeSend: function() {
                $('#ModalDetalleBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            },
            success: function(response) {
                if(response.status=='success'){
                    $('#ModalDetalleBodyInfoOF').html(response.Ordenfabricacionpartidas);
                }else{
                    error('Error al cargar los datos!', 'Los datos no pudieron ser procesados correctamente, si persiste el error contacta a TI');
                    $('#ModalDetalleBodyInfoOF').html('');
                    $('#ModalDetalle').modal('hide');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#ModalDetalleBodyInfoOF').html('');
                $('#ModalDetalle').modal('hide');
                errorBD();
            }
        });
    }
    function GuardarAsignacion(id, BotonGuardar){
        BotonGuardar.disabled = true;
        CantidadModal = $('#CantidadModal').val();
        LineaModal = $('#LineaModal').val();
        ErrorCantidadModal = $('#ErrorCantidadModal');
        ErrorLineaModal = $('#ErrorLineaModal');
        $BanderaRegistros=0;
        if(CantidadModal=="" || CantidadModal==0 || CantidadModal==null){
            ErrorCantidadModal.html('*El campo cantidad es requerido, y su valor tiene que ser mayor a 0.');
            $BanderaRegistros=1;
        }else{ErrorCantidadModal.html('');}
        if(LineaModal=="" || LineaModal==null){
            ErrorLineaModal.html('*El campo Línea es requerido, selecciona una opcion valida.');
            $BanderaRegistros=1;
        }else{ErrorLineaModal.html('');}
        if($BanderaRegistros==1){
            return 0;
        }
        $.ajax({
            url: "{{route('ClasificacionAsignar')}}", 
            type: 'POST',
            data: {
                id:id,
                CantidadModal:CantidadModal,
                LineaModal:LineaModal,
            },
            beforeSend: function() {
                //$('#ModalDetalleBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            },
            success: function(response) {
                if(response.status=='success'){
                    success('Guardado correctamente!','Asignación de linea generada correctamente.');
                    AsignarLinea(response.idOF);
                    PorcentajeLlenadas();
                }else{
                    error('Error al cargar los datos!', response.message);
                }
                RecargarTabla();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                BotonGuardar.disabled = false;
                errorBD();
                RecargarTabla();
            }
        });
    }
    /*function DataTable(tabla, busqueda){
        $('#'+tabla).DataTable({
            "pageLength": 10,  // Paginación de 10 elementos por página
            "lengthChange": false, // Desactiva la opción de cambiar el número de elementos por página
            "paging": true, // Habilitar paginación
            "searching": busqueda, // Habilitar búsqueda
            "ordering": true, // Habilitar ordenación de columnas
            "info": true, // Muestra información sobre el total de elementos
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                "search":"Buscar",
            },
            "initComplete": function(settings, json) {
                $('#'+tabla).css('font-size', '0.7rem');
            }
        });
    }*/
    $(document).ready(function() {
        PorcentajeLlenadas(); 
        $('#lineaModal').change(function() {
            PorcentajeLlenadas(); 
        });
        $('#FiltroOF_Fecha_table2').change(function() {
            PorcentajeLlenadas(); 
        });
        setInterval(RecargarTabla, 180000);
    });
    //fin
    function PorcentajeLlenadas() {
        let fecha = $('#FiltroOF_Fecha_table2').val();
        //chris
        let lineaSeleccionada = $('#lineaModal').val();

        $.ajax({
            url: "{{ route('PorcentajesPlaneacion') }}",
            type: 'GET',
            data: {
                fecha: fecha,
                Linea_id: lineaSeleccionada, // chris
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function () {},
            success: function (response) {
                let color = "#007BFF";
                let PorcentajeFaltante = 0;

                if (response.PorcentajePlaneada > 80) color = '#FFFF00';
                if (response.PorcentajePlaneada > 90) color = '#FFA500';
                if (response.PorcentajePlaneada > 100) color = '#FF0000';
                if (response.PorcentajeFaltante > 0) PorcentajeFaltante = response.PorcentajeFaltante;

                // Actualizar la interfaz con los datos de la respuesta
                $("#Cantidadpersonas").html(response.NumeroPersonas);
                $("#Estimadopiezas").html(response.CantidadEstimadaDia);
                $("#Piezasplaneadas").html(response.PlaneadoPorDia);
                $("#Porcentajefaltante").html(PorcentajeFaltante);
                $("#Porcentajeplaneada").html(response.PorcentajePlaneada);
                $('#Fecha_Grafica').html(response.Fecha_Grafica);
                $('#Piezasfaltantes').html(response.Piezasfaltantes);

                //chis
                if (response.Linea_id) {
                    let lineaTexto = $(`#lineaModal option[value="${response.Linea_id}"]`).text(); 
                    let lineaNumero = lineaTexto.split('-')[0].trim(); 
                    NumeroLinea = response.Linea_id;
                    $('#NumeroLinea').html(response.Linea);
                    $('#NumeroLinea1').html(response.Linea);//
                }

                var myChart = echarts.init(document.getElementById('PrcentajePlaneacion'));
                var option = {
                    tooltip: { trigger: 'item' },
                    legend: { show: false },
                    series: [
                        {
                            name: 'Planeación',
                            type: 'pie',
                            radius: ['60%', '70%'],
                            avoidLabelOverlap: false,
                            itemStyle: {
                                borderRadius: 10,
                                borderColor: '#fff',
                                borderWidth: 2
                            },
                            label: {
                                show: true,
                                position: 'center',
                                formatter: response.PorcentajePlaneada + '%',
                                fontSize: 20,
                                fontWeight: 'bold'
                            },
                            labelLine: { show: false },
                            data: [
                                { value: response.PorcentajePlaneada, name: 'Total Planeado', itemStyle: { color: color } },
                                { value: PorcentajeFaltante, name: 'Total faltante estimado', itemStyle: { color: '#D3D3D3' } }
                            ]
                        }
                    ]
                };
                myChart.setOption(option);
            }
        });
    }
    function GuardarParametrosPorcentajes(){
        CantidadPersona=$('#CantidadPersona').val();
        Piezaspersona=$('#Piezaspersona').val();
        Fecha=$('#FiltroOF_Fecha_table2').val();
        Linea = $('#lineaModal').val();
        errorCantidadPersona=$('#error_CantidadPersona');
        errorPiezaspersona=$('#error_Piezaspersona');
        if(CantidadPersona==0 || CantidadPersona==""){
            errorCantidadPersona.text('Por favor, ingresa un número valido, mayor a 0.');
            errorCantidadPersona.show();
            return 0; 
        }else{
            errorCantidadPersona.text('');
            errorCantidadPersona.hide(); 
        }
        if(Piezaspersona==0 || Piezaspersona==""){
            errorPiezaspersona.text('Por favor, ingresa un número valido, mayor a 0.');
            errorPiezaspersona.show();
            return 0; 
        }else{
            errorPiezaspersona.text('');
            errorPiezaspersona.hide(); 
        }
        $.ajax({
                url: "{{route('GuardarParametrosPorcentajes')}}", 
                type: 'POST',
                data: {
                    CantidadPersona:CantidadPersona,
                    Piezaspersona: Piezaspersona,
                    Fecha:Fecha,
                    Linea: Linea, //chris
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                },
                success: function(response) {
                    if(response==0){
                        error('Error con la Línea','El número de linea no existe o esta desactivada');
                        return 0;
                    }else{
                        success('Guardado correctamente!','Capacidad productiva guardado.');
                    }
                    PorcentajeLlenadas();
                },
                error: function(xhr, status, error) {
                    errorBD();
                }
        }); 
        $('#ParametrosPorcentaje').modal('hide');
    }
    function LlenarModalPorcentajes(){
        Cantidadpersonas=$('#Cantidadpersonas').html();
        Estimadopiezas=$('#Estimadopiezas').html();
        Linea_id=$('#lineaModal').html();
        if(Cantidadpersonas==0 || Estimadopiezas==0){
            Cantidadpersonas=0;
            Estimadopiezas=0;
        }else{
            Estimadopiezas=Estimadopiezas/Cantidadpersonas;
        }
        $('#CantidadPersona').val(Cantidadpersonas);
        $('#Piezaspersona').val(Estimadopiezas);
        $('#NumeroLinea1').val( Linea_id);//chris

        errorCantidadPersona=$('#error_CantidadPersona');
        errorPiezaspersona=$('#error_Piezaspersona');
        errorCantidadPersona.hide(); 
        errorPiezaspersona.hide(); 
    }
    function Escaner(Escanear, id){
        Escaneado=Escanear.checked;
        $.ajax({
            url: "{{route('CambiarEstatusEscaner')}}", 
            type: 'POST',
            data: {
                Escanear: Escaneado,
                Id: id,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                //RecargarTabla();
            },
            error: function(xhr, status, error) {
                RecargarTabla();
                error('Ocurrio un erro!', 'El Tipo Escáner no se pudo actualizar')
            }
        }); 
    }
    function BuscarOrden(Elemento){
        $('#FiltroOrdenFabricacionContent').html('');
        OF=Elemento.value
        if(OF.length>4){
            $.ajax({
                url: "{{route('ClasificacionBusqueda')}}", 
                type: 'POST',
                data: {
                    OrdenFabricacion:OF,
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {
                },
                success: function(response) {
                   $('#FiltroOrdenFabricacionContent').html(response);
                },
                error: function(xhr, status, error) {
                    console.log('ocurrio un Error al traer los datos para:'+OF);
                }
        }); 
        }
    }
    function BorrarContenedor(){
        $('#FiltroOrdenFabricacionContent').html('');
    }
    function FinalizarOrdenFabricacion(IdOrdenFabricación){
        const modalEl = document.getElementById('ModalDetalle');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance && modalInstance._focustrap) {
            modalInstance._focustrap.deactivate();
        }
        Swal.fire({
            title: 'Finalizar Orden de Fabricación',
            text: '¿Deseas finalizar la Orden de Fabricación?',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText:"Cancelar",
            confirmButtonText: 'Finalizar',
            input: 'textarea',
            inputPlaceholder: 'Ingrese el motivo para finalizar la Orde de Fabricación',  
            inputAttributes: {
                id: 'Motivo'
            },
            inputValidator: () => null,
            didOpen: () => {
                const textarea = Swal.getInput(); // obtiene el <textarea>
                const errorText = document.createElement('p');
                errorText.id = 'errorMotivo';
                errorText.className = 'text-danger';
                errorText.style.marginTop = '2px';
                errorText.style.marginLeft = '40px';
                errorText.style.display = 'none'; // oculto por defecto
                errorText.innerText = '*El motivo es obligatorio';
                textarea.insertAdjacentElement('afterend', errorText);
            },
            preConfirm: () => {
                const textarea = Swal.getInput();
                if(!CadenaVacia(textarea.value)){
                    $('#errorMotivo').hide();
                }else{
                    $('#errorMotivo').show();
                    return false;
                }
            }
        }).then((result) => {
        if (result.isConfirmed) {
            var Motivo = result.value;
            $.ajax({
                url: "{{route('FinalizarOrdenFabricacion')}}", 
                type: 'POST',
                data: {
                    idOF:IdOrdenFabricación,
                    Motivo:Motivo,
                },
                beforeSend: function() {
                    //$('#ModalDetalleBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                },
                success: function(response) {
                    if(response==1){
                        AsignarLinea(IdOrdenFabricación);
                        success('Guardado Correctamente','La Orden de Fabricación ha sido finalizada correctamente!');
                    }else{
                        error('Error al Finalizar la orden de fabricación!', 'Los datos no pudieron ser procesados correctamente, si persiste el error contacta a TI');
                    }
                    RecargarTabla();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    errorBD();
                }
            });
        }
      });
    }
    function EliminarPartida(IdOrdenFabricación,NumPartida,OrdenFabricacion){
         Swal.fire({
            title: 'Eliminar partida asignada',
            text: '¿Deseas eliminar la partida '+NumPartida+'?',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText:"Cancelar",
            confirmButtonText: 'Aceptar', 
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{route('EliminarAsignacion')}}", 
                    type: 'POST',
                    data: {
                        idOF:IdOrdenFabricación,
                    },
                    beforeSend: function() {
                        //$('#ModalDetalleBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                    },
                    success: function(response) {
                        if(response==1){
                            AsignarLinea(OrdenFabricacion);
                            success('Guardado Correctamente','Partida '+NumPartida+' eliminada correctamente!');
                        }else{
                            error('Error al Eliminar Partida!', 'Los datos no pudieron ser procesados correctamente, si persiste el error contacta a TI');
                        }
                        RecargarTabla();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        errorBD();
                    }
                });
            }
            /*    const Motivo = result.value;
                fetch("{{ route('FinalizarOrdenFabricacion') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        idOF: IdOrdenFabricación,
                        Motivo: Motivo,
                    }),
                })
                .then(response => response.json())
                .then(response => {
                    if (response === 1) {
                        AsignarLinea(IdOrdenFabricación);
                        success('Guardado Correctamente','La Orden de Fabricación ha sido finalizada correctamente!');
                    } else {
                        error('Error al Finalizar la orden de fabricación!', 'Los datos no pudieron ser procesados correctamente, si persiste el error contacta a TI');
                    }
                    RecargarTabla();
                })
                .catch(() => {
                    errorBD();
                });
            }*/
        }); 
    }
</script>
@endsection