@extends('layouts.menu') 
@section('styles') 
@section('title', 'Crear Usuario')
    {{--<link rel="stylesheet" href="{{ asset('css/style.css') }}">--}}
@endsection


@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Registrar Nuevo Usuario</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('registro.store') }}" method="POST" class="shadow p-4 rounded bg-white">
            @csrf

            <div class="form-group mb-3">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" class="form-control" placeholder="Ingrese su apellido" required>
            </div>

            <div class="form-group mb-3">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ingrese su nombre" required>
            </div>

            <div class="form-group mb-3">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su correo electrónico" required>
            </div>

            <div class="form-group mb-4">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su contraseña" required>
            </div>

            <div class="form-group mb-4">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirme su contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Registrar</button>
        </form>
    </div>
@endsection

