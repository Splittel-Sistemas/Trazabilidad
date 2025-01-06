@extends('layouts.menu') 
@section('title', 'Crear Rol')
@section('content')
    <div class="container mt-5">
        <h1 class="mb-4 text-center" style="color: #2d7007; text-shadow: 2px 2px 5px rgba(0,0,0,0.1);">Registrar Nuevo Rol</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('RolesPermisos.store') }}" method="POST" class="shadow p-4 rounded bg-white">
            @csrf
            <div class="form-row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Nombre De Rol</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="nombre" required>
                    </div>
                </div>
            </div>
            <div class="form-row mb-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Permisos</label>
                        <small class="form-text text-muted">Seleccione uno o más roles.</small>
                        @foreach ($permissions as $value)
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" id="permission_{{ $value->id }}" value="{{ $value->id }}" class="form-check-input" 
                                {{ (isset($registro) && $registro->permissions->contains($value->id)) ? 'checked' : '' }}>
                            <label for="permission_{{ $value->id }}" class="form-check-label">{{ $value->name }}</label>
                        </div>
                    @endforeach
                        @error('roles') 
                            <div class="text-danger">{{ $message }}</div> 
                        @enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-lg btn-block rounded-pill shadow-lg transition-all hover:bg-success hover:text-white">Registrar</button>
        </form>
    </div>
@endsection
