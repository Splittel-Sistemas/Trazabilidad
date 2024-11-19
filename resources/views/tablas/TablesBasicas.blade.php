@extends('layouts.menu')

@section('title', 'Tablas Básicas')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .table-container, .form-container {
            margin-top: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="form-container">
        <h1>Formulario de Suministros de Fibra Óptica</h1>
        <form id="form-fibra-optica" action="{{ route('suministros.enviar') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="distribuidor">Distribuidor:</label>
                <input type="text" id="distribuidor" name="distribuidor" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="producto">Producto:</label>
                <input type="text" id="producto" name="producto" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="ordenFabricacion">Orden de Fabricación:</label>
                <input type="text" id="ordenFabricacion" name="ordenFabricacion" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="ordenParte">Orden de Parte:</label>
                <input type="text" id="ordenParte" name="ordenParte" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" class="form-control" required min="1">
            </div>
            <button type="submit" class="btn btn-primary mt-3">Enviar</button>
        </form>
    </div>

    <div class="table-container">
        <h1>Tablas Básicas</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->nombre }}</td>
                        <td>{{ $usuario->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
