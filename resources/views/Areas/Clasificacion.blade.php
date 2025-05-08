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
                                                            <label for="Filtrofecha_table2">Fecha de asignaci&oacute;n:</label>
                                                            <div class="input-group">
                                                                <input type="date" name="FiltroOF_Fecha_table2"  id="FiltroOF_Fecha_table2" class="form-control form-control-sm   w-autoborder-primary col-12" placeholder="Ingresa Orden de fabricación" value="{{\Carbon\Carbon::parse($FechaFin)->translatedFormat('Y-m-d')}}">
                                                                <button id="buscarOV_vencidas" class="btn btn-primary btn-sm">
                                                                    Mostrar
                                                                </button>
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
                                            <th>Asignar l&iacute;nea</th>
                                        </tr>
                                    </thead>
                                    <tbody id="TablaCalsificacionAbiertasBody" class="list">
                                        @foreach($OrdenFabricacion as $partida)
                                            <tr style="@if($partida->Urgencia=='U'){{'background:#FFDCDB';}}@endif" id="Fila_$partida->OrdenFabricacion">
                                                <td class="text-center">{{$partida->OrdenFabricacion }}</td>
                                                <td>{{$partida->Articulo }}</td>
                                                <td>{{$partida->Descripcion }}</td>
                                                <td class="text-center">{{$partida->CantidadSuministro }}</td>
                                                <td class="text-center">{{$partida->CantidadTotal }} </td>
                                                <td class="text-center"><input type="checkbox" @if ($partida->EscanerDisabled == 0 ) onchange="Escaner(this,'{{$partida->idEncriptOF}}')" @else disabled @endif class="Corte67869" @if($partida->Escaner == 1) checked @endif></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info px-3 py-2" onclick="AsignarLinea('{{$partida->idEncriptOF}}')">Asignar</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--MODAL DETALLE-->
    <div class="modal fade" id="ModalDetalle" tabindex="-1" data-bs-backdrop="static" aria-labelledby="ModalDetalleLabel" aria-hidden="true">
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
        DataTable('TablaClasificacionAbiertas',true);
        setInterval(RecargarTabla,180000);//180000
    });
    function RecargarTabla(){
        $.ajax({
            url: "{{route('ClasificacionRecargarTabla')}}", 
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status=="success"){
                    $('#TablaClasificacionAbiertas').DataTable().destroy();
                    $('#TablaCalsificacionAbiertasBody').html(response.table);
                    DataTable('TablaClasificacionAbiertas',true);
                }
            }
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
    function GuardarAsignacion(id){
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
                errorBD();
                RecargarTabla();
            }
        });
    }
    function DataTable(tabla, busqueda){
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
</script>
@endsection