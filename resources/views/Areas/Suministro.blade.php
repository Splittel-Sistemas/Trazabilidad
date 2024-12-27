@extends('layouts.menu')
@section('title', 'Suministro')
@section('styles')
<link rel="stylesheet" href="{{asset('css/Suministro.css')}}">
@endsection
@section('content')
    <div class="row mb-2">
        <div class="breadcrumbs col-12">
            <div class="breadcrumbs-inner">
                <div class="row m-0">
                    <div class="col-sm-4">
                        <div class="page-header float-left">
                            <div class="page-title">
                                <h1>Suministro</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="page-header float-right">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="{{ route("Home") }}">Dashboard</a></li>
                                    <li><a href="#">Áreas</a></li>
                                    <li class="active">Suministro</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
              <div class="card">
                <div class="card-header">
                    <strong>   </strong>
                </div>
                <div class="card-body" id="filtro">
                    <div class="form-group">
                        <!--<label for="CodigoEscaner">C&oacute;digo</label>
                        <select class="form-control" id="CodigoEscaner">
                            <option value="" disabled selected>Ingresa el C&oacute;digo...</option>
                        </select>-->
                        <label for="CodigoEscaner">C&oacute;digo</label>
                        <input type="text" class="form-control form-control-sm" oninput="ListaCodigo(this.value,'CodigoEscanerSuministro')" id="CodigoEscaner" aria-describedby="CodigoEscanerHelp" placeholder="Ingresa el codigo">
                        <div class="border p-2 mt-2 list-group-sm" id="CodigoEscanerSuministro" style="display: none">
                            
                        </div>
                        <small id="CodigoEscanerHelp" class="form-text text-muted">Esc&aacute;nea o ingresa manualmente.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="{{ asset('js/Suministro.js') }}"></script>
<script>
    //$(document).ready(function() {});
    function ListaCodigo(Codigo,Contenedor){
        let CodigoPartes = Codigo.split('-');
        let CodigoPrimero = CodigoPartes[0];
        var CodigoSegundo = CodigoPartes[1] || "";
        document.getElementById('CodigoEscanerSuministro').style.display = "none";
        $.ajax({
            url: "{{route('SuministroBuscar')}}", 
            type: 'GET',
            data: {
                CodigoPrimero: CodigoPrimero,
                CodigoSegundo: CodigoSegundo,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
            },
            success: function(response) {
                document.getElementById('CodigoEscanerSuministro').style.display = "";
                $('#CodigoEscanerSuministro').html(response);
            },
            error: function(xhr, status, error) {
                /*Cuerpo.html('<p class="text-center">No existen información para esta Orden de Fabricación</p>');
                $('#ModalOrdenesFabricacion').modal('hide'); // Muestra el modal*/
            }
        }); 
    }
</script>
@endsection
