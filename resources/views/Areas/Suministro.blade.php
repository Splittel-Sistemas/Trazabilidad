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
<script>
</script>
@endsection
