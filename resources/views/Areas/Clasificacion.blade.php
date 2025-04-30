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
    <div class="card">
        <div class="card-body">
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
                                            <th>Cantidad Entrante</th>
                                            <th>Cantidad Total Orden de Fabricacion</th>
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
                                                <td class="text-center">{{$partida->CantidadTotal }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info px-3 py-2" onclick="AsignarLinea('{{$partida->idEncriptOF}}')">Asignar</button>
                                                    {{--<select onclick="" class="form-select form-select-sm" aria-label="">
                                                        <option selected="">Selecciona una L&iacute;nea</option>
                                                            @foreach($Lineas as $L)
                                                                <option value="{{$L->id}}">{{$L->Nombre}}</option>
                                                            @endforeach
                                                      </select>--}}
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
</script>
@endsection