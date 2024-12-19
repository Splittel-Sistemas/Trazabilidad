@extends('layouts.menu') 
@section('title', 'Crear Usuario')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Registrar Nuevo Usuario</h1>

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
                <input type="text" name="name" id="name" class="form-control" placeholder="Ingrese su nombre" required>
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
            <div class="form-row mb-4">
                <div class="form-group col-md-6">
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
            
                <div class="form-group col-md-6">
                    <label class="font-weight-bold">Permisos</label>
                    <small class="form-text text-muted">Seleccione uno o más permisos.</small>
                    
                    @foreach ($permissions as $value)
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" id="permission_{{ $value->id }}" value="{{ $value->id }}" class="form-check-input" 
                                {{ (isset($registro) && $registro->permissions->contains($value->id)) ? 'checked' : '' }}>
                            <label for="permission_{{ $value->id }}" class="form-check-label">{{ $value->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            

                @foreach ($permissions as $permission)
                    <div class="form-check">
                        <input  type="checkbox" name="permissions[]"  id="permission_{{ $permission->id }}"  value="{{ $permission->id }}" class="form-check-input" 
                            {{ (isset($registro) && $registro->permissions->contains($permission->id)) ? 'checked' : '' }}>
                        <label for="permission_{{ $value }}" class="form-check-label">{{ $permission->name }}</label>
                    </div>
                @endforeach

                @error('permissions') 
                    <div class="text-danger">{{ $message }}</div> 
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Registrar</button>
        </form>
    </div>
@endsection

