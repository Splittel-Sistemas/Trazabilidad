@extends('layouts.menu') 
@section('title', 'Registro Usuario') 
@section('styles') 
    {{--<link rel="stylesheet" href="{{ asset('css/style.css') }}">--}}
@endsection

@section('content')
    <div class="container">
        <h1>Lista de Usuarios</h1>
        <a href="{{ route('registro.create') }}" class="btn btn-primary">Agregar Usuario</a>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($personal as $registro)
                    <tr>
                        <td>{{ $registro->apellido }}</td>
                        <td>{{ $registro->nombre }}</td>
                        <td>{{ $registro->email }}</td>
                        <td>
                            <a href="{{ route('registro.edit', $registro->id) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('registro.destroy', $registro->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('scripts') 
    {{-- <script src="{{ asset('js/main.js') }}"></script> --}}
@endsection
