@extends('layouts.menu2') 
@section('title', 'Crear Rol') 

@section('content')
<div class="breadcrumbs mb-4">
    <div class="row g-0">
     
            <div class="row gy-3 mb-2 justify-content-between">
                <div class="col-md-9 col-auto">
                <h4 class="mb-2 text-1100">Registrar Nuevo Rol</h4>
                </div>
            </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('RolesPermisos.store') }}" method="POST" class="shadow-lg p-4 rounded-lg bg-white">
            @csrf
            <div class="form-row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nombre De Rol</label>
                        <input type="text" name="nombre" id="nombre" class="form-control form-control-lg border-2 border-success" placeholder="nombre" required>
                    </div>
                </div>
            </div>
            <div class="form-row mb-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Permisos</label>
                        <small class="form-text text-muted">Seleccione uno o más Permisos.</small>
                        @foreach ($permissions as $value)
                            <div class="form-check">
                                <input type="checkbox" name="permissions[]" id="permission_{{ $value->id }}" value="{{ $value->id }}" class="form-check-input" 
                                    {{ (isset($registro) && $registro->permissions->contains($value->id)) ? 'checked' : '' }}>
                                <label for="permission_{{ $value->id }}" class="form-check-label">{{ $value->name }}</label>
                            </div>
                        @endforeach
                        @error('roles') 
                            <div class="text-danger mt-2">{{ $message }}</div> 
                        @enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-lg btn-block rounded-pill shadow-lg transition-all hover:bg-success hover:text-white">Registrar</button>
        </form>
    </div>
@endsection
