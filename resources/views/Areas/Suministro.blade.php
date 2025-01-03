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
                <div class="card-body row" id="filtro">
                    <div class="col-8" id="CodigoDiv">
                        <div class="">
                            <label for="CodigoEscaner">C&oacute;digo <span class="text-muted">&#40;Escanea para iniciar&#41;</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" oninput="ListaCodigo(this.value,'CodigoEscanerSuministro')" id="CodigoEscaner" aria-describedby="CodigoEscanerHelp" placeholder="Escánea o ingresa manualmente.">
                            </div>
                            <div class=" mt-1 list-group-sm" id="CodigoEscanerSuministro">
                            </div>
                        </div>
                    </div>
                    <div class="col-4" id="CantidadDiv" style="display: none">
                        <div class="form-group">
                            <label for="Cantidad">Cantidad</label>
                                <input type="text" class="form-control form-control-sm" id="Cantidad" aria-describedby="Cantidad" value="1" placeholder="Ingresa cantidad recibida.">
                        </div>
                    </div>
                    <div class="col-12" id="IniciarBtn" style="display: none">
                        <button class="btn btn-primary btn-sm float-end" type="button" id="btnEscanear"><i class="fa fa-play"></i> Iniciar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="ContentTabla" class="col-12 mt-2" style="display: none">
            <h4 class="mb-2">Partidas de la Orden de Fabricacion <span id="TituloPartidasOF"></span> <br><span id="CantidadPartidasOF"></span></h4>
            <table class="table table-sm table-bordered table-striped table-hover m-1">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Num. Parte</th>
                    <th>Cantidad</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Estatus</th>
                    <th>Acci&oacute;nes</th>
                </tr>
            </thead>
            <tbody id="TablaBody">
            </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
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
        $.ajax({
            url: "{{route('SuministroBuscar')}}", 
            type: 'GET',
            data: {
                Codigo: Codigo,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#CodigoEscanerSuministro').html("<p colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /><br>Cargando</p>");
            },
            success: function(response) {
                $('#CantidadDiv').hide();
                $('#IniciarBtn').hide();
                if(response.status=="success"){
                    $('#ContentTabla').show();
                    $('#TablaBody').html(response.tabla);
                    $('#CantidadPartidasOF').html('<span class="badge bg-light text-dark">'+response.CantidadCompletada+"/"+response.CantidadTotal+'</span>');
                    $('#TituloPartidasOF').html(response.OF);
                    if(response.Escaner==0){
                        $('#CantidadDiv').show();
                        $('#IniciarBtn').show();
                    }else{
                        $('#CantidadDiv').hide();
                        $('#IniciarBtn').hide();
                    }
                }else if(response.status=="empty"){
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

</script>
@endsection
