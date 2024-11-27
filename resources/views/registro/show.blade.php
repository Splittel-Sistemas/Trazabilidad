@extends('layouts.menu') 
@section('title', 'Detalles del Usuario')
@section('styles') 
    {{--<link rel="stylesheet" href="{{ asset('css/style.css') }}">--}}
@endsection

@section('content')
    <div class="container">
        <h1>Detalles de Usuario: {{ $registro->nombre }}</h1>
        <p><strong>Apellido:</strong> {{ $registro->apellido }}</p>
        <p><strong>Email:</strong> {{ $registro->email }}</p>
        <a href="{{ route('registro.edit', $registro->id) }}" class="btn btn-warning">Editar</a>
        <form action="{{ route('registro.destroy', $registro->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Eliminar</button>
        </form>
        <a href="{{ route('registro.index') }}" class="btn btn-secondary">Volver</a>
    </div>
@endsection
