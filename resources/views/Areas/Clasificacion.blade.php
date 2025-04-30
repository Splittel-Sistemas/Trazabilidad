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
            <hr id="hr-menu">
            <div class="tab-content mt-4" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-proceso" role="tabpanel" aria-labelledby="proceso-tab">
                    <div id="ContentTabla" class="col-12 mt-2">
                        <div class="card" id="DivCointainerTableSuministro">
                            <div class="table-responsive">
                                <table id="TablaSuministroAbiertas" class="table table-sm fs--1 mb-1">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>Orden Fabricación</th>
                                            <th>Artículo</th>
                                            <th>Descripción</th>
                                            <th>Cantidad</th>
                                            <th>Asignar l&iacute;nea</th>
                                        </tr>
                                    </thead>
                                    <tbody id="TablaSuministroAbiertasBody" class="list">
                                        @foreach($OrdenFabricacion as $partida)
                                            <tr style="@if($partida->Urgencia=='U'){{'background:#FFDCDB';}}@endif" id="Fila_$partida->OrdenFabricacion">
                                                <td class="text-center">{{$partida->OrdenFabricacion }}</td>
                                                <td>{{$partida->Articulo }}</td>
                                                <td>{{$partida->Descripcion }}</td>
                                                <td class="text-center">{{$partida->CantidadTotal }}</td>
                                                <td>
                                                    <select onclick="" class="form-select form-select-sm" aria-label="">
                                                        <option selected="">Selecciona una L&iacute;nea</option>
                                                            @foreach($Lineas as $L)
                                                                <option value="{{$L->id}}">{{$L->Nombre}}</option>
                                                            @endforeach
                                                      </select>
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
@endsection
@section('scripts')
<script src="{{ asset('js/Suministro.js') }}"></script>
<script>
    $(document).ready(function() {
        DataTable('TablaSuministroAbiertas',true);
    });
    {{--function Finalizar(id){
        $.ajax({
            url: "{{route('SuministroFinalizar')}}", 
            type: 'POST',
            data: {
                id:id,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status=='success'){
                    Planear(response.OF);
                    success("Finalizada Correctamente!",response.message);
                    RecargarTabla();
                    RecargarTablaCerradas();
                }else if(response.status=='errornoexiste'){
                    error("Error!",response.message);
                }else if(response.status=='errorfinalizada'){
                    error("Error!",response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            }
        });
    }
    function RecargarTabla(){
        $.ajax({
            url: "{{route('SuministroRecargarTabla')}}", 
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status=="success"){
                    $('#TablaSuministroAbiertas').DataTable().destroy();
                    $('#TablaSuministroAbiertasBody').html(response.table);
                    DataTable('TablaSuministroAbiertas',true);
                    $('.dt-layout-start:first').append(
                    '<button class="btn btn-info mb-0" data-bs-toggle="modal" data-bs-target="#ModalRetrabajo" onclick="LimpiarOF()"><i class="fas fa-plus"></i> Retrabajo</button>');
                }
            }
        });
    }
    function RetrabajoMostrarOFBuscar(RetrabajoOF){
        $('#RetrabajoOFOpciones').html('');
        if(RetrabajoOF.value==""){
            return 0;
        }if((RetrabajoOF.value).length<5){
            return 0;
        }
        $.ajax({
            url: "{{route('BuscarSuministro')}}", 
            type: 'POST',
            data: {
                OF:RetrabajoOF.value,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {

            },
            success: function(response) {
                $('#RetrabajoOFOpciones').html(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#RetrabajoOFOpciones').html('');
            }
        });
    }--}}
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