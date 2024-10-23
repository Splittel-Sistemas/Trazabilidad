@extends('layouts.menu') 
@section('title', 'Registrar Usuario')
@section('styles') 
    {{--<link rel="stylesheet" href="{{ asset('css/style.css') }}">--}}
@endsection


@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Registrar Nuevo Usuario</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('registro.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" class="form-control" required placeholder="Ingrese su apellido">
            </div>
    
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Ingrese su nombre">
            </div>
    
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="Ingrese su correo electrónico">
            </div>
    
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Ingrese su contraseña">
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Confirme su contraseña">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Registrar</button>
        </form>
    </div>
@endsection
