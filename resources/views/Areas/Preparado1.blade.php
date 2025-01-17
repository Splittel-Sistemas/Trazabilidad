@extends('layouts.menu1')
@section('title', 'Preparado')
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
                                <h1>Preparado</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="page-header float-right">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="#">Dashboard</a></li>
                                    <li><a href="#">Áreas</a></li>
                                    <li class="active"> Preparado</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Formulario para registrar material recibido -->
    <form action="" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="material" class="form-label">Tipo de Material</label>
                <select class="form-select" name="material" id="material" required>
                    <option value="">Seleccione...</option>
                    <option value="cable">Cable</option>
                    <option value="botitas">Botitas</option>
                    <option value="termos">Termos</option>
                    <option value="ojillo">Ojillo</option>
                </select>
            </div>

            <div class="col-md-6">
                <label for="cantidad" class="form-label">Cantidad</label>
                <input type="number" class="form-control" name="cantidad" id="cantidad" min="1" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="proveedor" class="form-label">Proveedor</label>
                <input type="text" class="form-control" name="proveedor" id="proveedor" required>
            </div>

            <div class="col-md-6">
                <label for="fecha" class="form-label">Fecha de Recepción</label>
                <input type="date" class="form-control" name="fecha" id="fecha" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Registrar Recepción</button>
    </form>

    <hr>

    <!-- Tabla para mostrar materiales recibidos -->
    <h2 class="mb-3">Materiales Recibidos</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Tipo de Material</th>
                <th>Cantidad</th>
                <th>Proveedor</th>
                <th>Fecha de Recepción</th>
            </tr>
        </thead>
        <tbody>
           
        </tbody>
    </table>
</div>
@endsection
