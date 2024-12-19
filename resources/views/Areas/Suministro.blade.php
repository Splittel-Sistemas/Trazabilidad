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
                                    <li><a href="#">Dashboard</a></li>
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
                <div class="card-body card-block collapsed show" id="filtro">
                    <div class="form-group">
                        <label for="CodigoEscaner">C&oacute;digo</label>
                        <input type="text" class="form-control form-control-sm" oninput="ListaCodigo(this.value,'CodigoEscanerSuministro')" id="CodigoEscaner" aria-describedby="CodigoEscanerHelp" placeholder="Ingresa el codigo">
                        <div class="border p-2 mt-2 list-group-sm" id="CodigoEscanerSuministro">
                            <a class="list-group-item list-group-item-action active" data-toggle="list" href="#home" role="tab">Home</a>
                            <a class="list-group-item list-group-item-action" data-toggle="list" href="#home" role="tab">Home</a>
                            <a class="list-group-item list-group-item-action" data-toggle="list" href="#home" role="tab">Home</a>
                            <a class="list-group-item list-group-item-action" data-toggle="list" href="#home" role="tab">Home</a>
                            <a class="list-group-item list-group-item-action" data-toggle="list" href="#home" role="tab">Home</a>
                        </div>
                        <small id="CodigoEscanerHelp" class="form-text text-muted">Esc&aacute;nea o ingresa manualmente.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/Suministro.js') }}"></script>

@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Estilos de Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (necesario para Select2) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Script de Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- Script de Bootstrap (opcional si lo necesitas para otros elementos de la interfaz) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    function ListaCodigo(codigo,tabla){
        $.ajax({
            url: "{{route('AreaPartidas')}}", 
            type: 'GET',
            data: {
                codigo: codigo,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                /*if (modal.is(':visible')) {
                        $('#table-2-content_vencidos').html("<tr><td colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></td></tr>");
                    }else{
                        $('#table-2-content').html("<tr><td colspan='100%' align='center'><img src='{{ asset('storage/ImagenesGenerales/ajax-loader.gif') }}' /></td></tr>");
                    }*/
                // You can display a loading spinner here
            },
            success: function(response) {
                /*if (modal.is(':visible')) {
                    tabla=(response.tabla);
                    //tabla=tabla.replace(/onclick="DetallesOrdenFabricacion/g, " disabled ");
                    $('#table-2-content_vencidos').html(tabla);

                }else{
                    $('#table-2-content').html(response.tabla);
                }*/
            },
            error: function(xhr, status, error) {
                //errorBD();
            }
        }); 
        alert(codigo);
    }
   /* $('#mySelect2').select2({
  ajax: {
    url: '/example/api',
    processResults: function (data) {
      // Transforms the top-level key of the response object from 'items' to 'results'
      return {
        results: data.items
      };
    }
  }
});*/
</script>
@endsection
