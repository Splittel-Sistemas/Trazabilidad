@extends('layouts.menu2') 
@section('title', 'Crear Usuario')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4 text-center" style="color: #2d7007; text-shadow: 2px 2px 5px rgba(0,0,0,0.1);">Registrar Nuevo Usuario</h1>
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
            <div class="row mb-4">
                <!-- Apellido -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" name="apellido" id="apellido" class="form-control" placeholder="Ingrese su apellido" required>
                    </div>
                </div>
                <!-- Nombre -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Ingrese su nombre" required>
                    </div>
                </div>
            </div>
            <!-- Correo Electrónico -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su correo electrónico" required>
                    </div>
                </div>
            </div>
            <!-- Contraseña y Confirmar Contraseña -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su nueva contraseña">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirme su nueva contraseña">
                    </div>
                </div>
            </div>
            <!-- Roles -->
            <div class="form-group">
                <label class="font-weight-bold">Roles</label>
                <small class="form-text text-muted">Seleccione uno o más roles.</small>
        
                @foreach ($roles as $value)
                    <div class="form-check">
                        <input type="checkbox" name="roles[]" id="role_{{ $value->id }}" value="{{ $value->id }}" class="form-check-input" 
                            {{ (isset($registro) && $registro->roles->contains($value->id)) ? 'checked' : '' }}>
                        <label for="role_{{ $value->id }}" class="form-check-label">{{ $value->name }}</label>
                    </div>
                @endforeach
        
                @error('roles') 
                    <div class="text-danger">{{ $message }}</div> 
                @enderror
            </div>
            <!-- Botón de envío -->
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-success btn-lg rounded-pill shadow-lg transition-all hover:bg-success hover:text-white">Registrar</button>
            </div>   
        </form>
        
    </div>


    
@endsection

