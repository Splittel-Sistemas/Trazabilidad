@extends('layouts.menu2')
@section('title', 'Asignación')
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
    #Container_dropzone{
        position: fixed;
        z-index: 1;
        /*pointer-events: none !important;*/
        top: 5rem;
    }
    .dropzone{
        border: 2px dashed #007bff;
        padding: 20px; 
        text-align: center; 
        min-height: 4rem;
        transition: 1s transform;
        background: #ffffffce;
    }
    .dropzone:hover{
        transform: scale(1.1);
    }
    .dropzone svg{
        height: 2rem;
        animation: pulso-infinito 1.5s ease-in-out infinite;
        position: absolute;
        padding: 0;
        margin: 0;
        margin-left: -1rem; 
    }
    .Tabledrop{
        transition: 0.5s transform;
    }
    .Tabledrop:hover{
        transform: scale(1.01);
    }
    @keyframes pulso-infinito {
        0% {
            margin-top: 1rem;
        }
        50% {
            margin-top: 0.5;           /* Se hace más brillante */
        }
        100% {
            margin-top:0;
            opacity: 0.5;
        }
    }
</style>
@endsection
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Asignaci&oacute;n</h4>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-6 mb-2 ">
            <div class="col-sm-12">
                <div class="card border border-light ">
                    <div class="card-body p-2">
                        <div class="accordion" id="accordionFiltroOV">
                            <div class="accordion-item border-top border-300 p-0">
                                <h4 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroOV" aria-expanded="true" aria-controls="collapseFiltroOV">
                                        Porcentaje de Asignaci&oacute;n &nbsp;<span id="Fecha_Grafica"> {{\Carbon\Carbon::parse($FechaFin)->translatedFormat('d \d\e F \d\e Y')}}</span>
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
                                                <div class="col-6 mt-1">
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
        <div class="col-sm-6" id="Container_dropzone" style="display: none;">
            <div class="col-12 mt-1">
                <div ondrop="drop(event,0)" ondragover="allowDrop(event)" class="dropzone mt-2 border-dashed rounded-2 min-h-0 mb-2">
                    <h5><i class="fas fa-angle-double-up"></i><br><br><br>Inicio de la fila</h5>
                    <p class="text-muted">Suelta aqui la Orden</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="mx-1">
                <a class="btn btn-soft-primary mt-2" data-bs-toggle="collapse" href="#CodigoColores" role="button" aria-expanded="false" aria-controls="CodigoColores">L&iacute;neas</a>
            </p>
        <div class="collapse mx-5" id="CodigoColores">
            <div class="row">
                <div class="col-3 border">Nomal</div>
                <div class="col-3 border text-center" style="background-color: rgb(139, 224, 252);">Urgente</div>
                <div class="col-3 border text-center" style="background-color: rgb(252, 248, 139);">Detenida</div>
                <div class="col-3 border text-center" style="background-color: rgb(255, 128, 44);color:white;">Prioridad</div>
            </div>
        </div>
            <div class="tab-content mt-1" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-proceso" role="tabpanel" aria-labelledby="proceso-tab">
                    <div id="ContentTabla" class="col-12 mt-2">
                        <div class="card" id="DivCointainerTableSuministro">
                            <div class="table-responsive">
                                <table id="TablaAsignacionAbiertas" class="table table-sm fs--1 mb-1">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>Orden Fabricación</th>
                                            <th>Artículo</th>
                                            <th>Descripción</th>
                                            <th>Cantidad Pendiente de asignar</th>
                                            <th>Cantidad Total Orden de Fabricacion</th>
                                            <th class="text-center" colspan="2">Acciones</th>
                                            <th>Urgencia</th>
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
                <h5 class="modal-title text-white" id="ModalDetalleLabel">Asignar por L&iacute;neas</h5><button class="btn" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-white"></span></button>
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
    <!--Modal Detener-->
    <div class="modal fade" id="DetenerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white" id="DetenerModalLabel">Detener Orden de Fabricaci&oacute;n<strong><span id="OFTextoDetener"></span></strong></h5><button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label class="form-label" for="DetenerComentario">Comentario ( ¿Por qué se detiene? )</label>
                        <textarea class="form-control" id="DetenerComentario" rows="3"> </textarea>
                        <small class="text-danger" id="Error_DetenerComentario"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" id="DetenerGuardar">Guardar</button>
                    <button class="btn btn-soft-primary" type="button" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    const contenedor = document.getElementById('ContentTabla');

    $(document).ready(function(){
        RecargarTabla();
        //setInterval(RecargarTabla,600000);//180000
    });
    function RecargarTabla(){
        $('#TablaAsignacionAbiertas').DataTable({
            destroy: true,
            paging: false,
            ajax: {
                url: "{{ route('AsignacionRecargarTabla') }}",
                dataSrc: function(json) {
                    return Object.values(json.data);
                },
                error: function(xhr, error, thrown) {
                    errorBD();
                }
            },
            order: [],
            columns: [
                { data: 'OrdenFabricacion' },
                { data: 'Articulo' },
                { data: 'Descripcion' },
                { data: 'CantidadPendiente' },
                { data: 'CantidadTotal' },
                { data: 'Asignar' },
                { data: 'Detener' },
                { data:'Urgencia'}
            ],
            columnDefs: [
                { targets: [0,1,2,3,4,5, 6], orderable: false }, // Opcional: desactiva orden en acciones
                { targets: [5, 6], searchable: false }, // Opcional: desactiva búsqueda
                { targets: [5, 6], className: 'text-center' }, // Centra los botones y checkbox
                { targets: [7], visible: false, searchable: false }
            ],
            language: {
                            //"info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                            "info": "Total Ordenes de Fabricación: _TOTAL_",
                            "search":"Buscar",
                        },
                        "initComplete": function(settings, json) {
                            //$('#'+tabla).css('font-size', '0.7rem');
                        },
            rowCallback: function(row, data, index) {
                if (data.Urgencia === 'U') {
                    $(row).css('background-color', '#8be0fc');
                    $(row).addClass('Urgente');
                }
                if(data.prioridad == 1){
                    $(row).css('background-color', 'rgb(255, 128, 44)');
                    $(row).css('color', 'white');
                }
                if(data.Status == 0){
                    $(row).css('background-color', '#fcf88b');
                    $(row).css('color', '');
                    $(row).addClass('Detenida');
                }
                $(row).attr('draggable', 'true');
                $(row).attr('id', data.Id);
                //if(data.prioridad != 1){
                    $(row).attr('ondrop','drop(event,this)');
                    $(row).attr('ondragover','allowDrop(event)');
                    $(row).addClass('class','Tabledrop');
                //}
            },
            lengthChange: false,
            initComplete: function(settings, json) {
                //$('#'+tabla).css('font-size', '0.7rem');
            }
        });
    }
    /*function RecargarTabla(){
        $('#TablaAsignacionAbiertas').DataTable({
            destroy: true,
            ajax: {
                url: "{{ route('AsignacionRecargarTabla') }}",
                dataSrc: 'data',
                error: function(xhr, error, thrown) {
                    errorBD();
                }
            },
            order: [[7, 'desc']],
            columns: [
                { data: 'OrdenFabricacion' },
                { data: 'Articulo' },
                { data: 'Descripcion' },
                { data: 'CantidadPendiente' },
                { data: 'CantidadTotal' },
                { data: 'Asignar' },
                { data: 'Detener' },
                { data:'Urgencia'}
            ],
            columnDefs: [
                { targets: [5, 6], orderable: false }, // Opcional: desactiva orden en acciones
                { targets: [5, 6], searchable: false }, // Opcional: desactiva búsqueda
                { targets: [5, 6], className: 'text-center' }, // Centra los botones y checkbox
                { targets: [7], visible: false, searchable: false }
            ],
            language: {
                            "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                            "search":"Buscar",
                        },
                        "initComplete": function(settings, json) {
                            //$('#'+tabla).css('font-size', '0.7rem');
                        },
            rowCallback: function(row, data, index) {
                if (data.Urgencia === 'U') {
                    $(row).css('background-color', '#8be0fc');
                }
                if(data.Status == 0){
                    $(row).css('background-color', '#fcf88b');
                }
                $(row).attr('draggable', 'true');
                $(row).attr('id', data.Id);
                $(row).attr('ondrop','drop(event,"'+data.Id+'")');
                $(row).attr('ondragover','allowDrop(event)');
            },
            lengthChange: false,
            initComplete: function(settings, json) {
                //$('#'+tabla).css('font-size', '0.7rem');
            }
        });
    }*/
    function AsignarLinea(id){
        $('#ModalDetalle').modal('show');
        $.ajax({
            url: "{{route('AsignacionInfoModal')}}", 
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
            BotonGuardar.disabled = false;
            return 0;
        }
        BotonGuardar.disabled = true;
        $.ajax({
            url: "{{route('AsignacionAsignar')}}", 
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
            },
            complete:function(){
                BotonGuardar.disabled = false;
            }
        });
    }
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
                url: "{{route('AsignacionBusqueda')}}", 
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
    function DetenerOrdenFabricacion(idOF,status){
        DetenerGuardar = document.getElementById('DetenerGuardar');
        DetenerGuardar.dataset.id = "";
        DetenerGuardar.dataset.id = idOF;
        DetenerGuardar.dataset.status = "";
        DetenerGuardar.dataset.status = status;
        if(status === 'D'){
            document.getElementById('Error_DetenerComentario').innerText = "";
            document.getElementById('DetenerComentario').value = "";
            $('#DetenerModal').modal('show');
        }else{
            DetenerGuardar.click();
        }
    }
    document.getElementById('DetenerGuardar').addEventListener('click', function() {
        id = this.dataset.id;
        status = this.dataset.status;
        elemento = this;
        message = ' "Continuar" ';
        $color = 'success';
        if(status === 'D'){
            DetenerComentario = document.getElementById('DetenerComentario');
            if((DetenerComentario.value).trim()==""){
                document.getElementById('Error_DetenerComentario').innerText = "* Campo obligatorio";
                return 0;
            }else{
                document.getElementById('Error_DetenerComentario').innerText = "";
            }
            if(id.trim() == ""){
                error('Ocurrio un error', "los datos no pudieron ser procesados correctamente.");
                $('#DetenerModal').modal('hide');
                return 0;
            }
            this.disabled = true;
            message = ' "Detenida" ';
            $color = 'warning';
        }
        $.ajax({
            url: "{{route('DetenerAsignacion')}}", 
            type: 'POST',
            data: {
                idOF:id,
                comentario:DetenerComentario.value,
                status:status
            },
            beforeSend: function() {
                //$('#ModalDetalleBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            },
            success: function(response) {
                if(response.status=='success'){
                    if($color == 'warning'){
                        warning('Guardado Correctamente','La Orden de Fabricación '+response.Ordenfabricacion+' cambio a estatus '+message+'correctamente!');
                    }else{
                        success('Guardado Correctamente','La Orden de Fabricación '+response.Ordenfabricacion+' cambio a estatus '+message+'correctamente!');
                    }
                    document.getElementById('Error_DetenerComentario').innerText = "";
                    document.getElementById('DetenerComentario').value = "";
                }else{
                    warning('Error al Detener la orden de fabricación '+response.Ordenfabricacion+'!', 'Los datos no pudieron ser procesados correctamente, si persiste el error contacta a TI');
                }
                RecargarTabla();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            },
            complete:function(){
                elemento.disabled = false;
                $('#DetenerModal').modal('hide');
            }
        });
    });
    
    //Permitir que el elemento sea soltado
    function allowDrop(event) {
        event.preventDefault();
    }
    // Al soltar los elementos en el dropzone
    function drop(event,posicion) {
        event.preventDefault();
        const data = event.dataTransfer.getData("text");
        let PosicionDestino = 0;
        let fila = document.getElementById(data);
        if(posicion){
            PosicionDestino = posicion.sectionRowIndex;
        }
        if(fila){
            if(fila.sectionRowIndex == PosicionDestino)return;
        }
        $.ajax({
            url: "{{route('PrioridadAsignacion')}}", 
            type: 'POST',
            data: {
                idOF:data,
                Posicion: PosicionDestino,
            },
            beforeSend: function() {
                //$('#ModalDetalleBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            },
            success: function(response) {
                if(response.status=='success'){
                    tablaBody = document.querySelector("#TablaAsignacionAbiertas tbody");
                    if(PosicionDestino == 0){
                        PrimeraFila = tablaBody.rows[0];
                        /*if (PrimeraFila.classList.contains("Urgente")){
                            PrimeraFila.style.background = "#8be0fc";
                            PrimeraFila.style.color = "";
                        }else if (PrimeraFila.classList.contains("Detenida")){
                            PrimeraFila.style.background = "#fcf88b";
                            PrimeraFila.style.color = "";
                        }else{
                            PrimeraFila.style.background = "";
                            PrimeraFila.style.color = "";
                        }*/
                        var filadestino = tablaBody.children[PosicionDestino];
                        fila = document.getElementById(data);
                        fila.style.background = "rgb(255, 128, 44)";
                        fila.style.color = "white";
                        tablaBody.prepend(fila);
                    }else{
                        fila = document.getElementById(data);
                        var filadestino = tablaBody.children[PosicionDestino];
                        if(fila){
                            tablaBody.insertBefore(fila, filadestino);
                        }
                    }
                    success('Guardado Correctamente',response.message);
                    document.getElementById('Error_DetenerComentario').innerText = "";
                    document.getElementById('DetenerComentario').value = "";
                }else{
                    error('Error al dar Prioridad a la orden de fabricación '+response.Ordenfabricacion+'!', 'Los datos no pudieron ser procesados correctamente, si persiste el error contacta a TI');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            },
            complete:function(){
            }
        });
    }
    // Detectar cuando inicia el arrastre
    contenedor.addEventListener('dragstart', function(e) {
        // Solo cuando se intente arrastrar un <tr>
        const fila = e.target.closest('tr');
        if (fila && fila.getAttribute('draggable') === 'true') {
            document.getElementById('Container_dropzone').style.display= "";
            let selectedRows = document.querySelectorAll("tr[id]"); 
            const idSeleccionado = fila.id;
             //let rowIds = [];
            /*selectedRows.forEach(row => {
                if(row.id) rowIds.push(row.id);
            });*/
            e.dataTransfer.setData("text", idSeleccionado);
        }
    });
    //ocultar el dropzone cuando se suelta (o se cancela)
    contenedor.addEventListener('dragend', function(e) {
        const fila = e.target.closest('tr');
        document.getElementById('Container_dropzone').style.display= "none";
    });
    //Urgencia en la linea
    function PrioridadLinea(elemento, id_partida){
        status = elemento.checked;
         $.ajax({
            url: "{{route('UrgenciaAsignacion')}}", 
            type: 'POST',
            data: {
                id_partida: id_partida,
                status: status,
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status=='success'){
                    success('Guardado Correctamente!', response.message)
                }else{
                    error('Error al dar Prioridad', response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            },
            complete:function(){
            }
        });
    }
</script>
@endsection