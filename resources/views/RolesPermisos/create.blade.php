@extends('layouts.menu2') 
@section('title', 'Crear Rol') 
<style>
    .permissions-container {
    display: flex;
    flex-wrap: wrap; /* Permite que los elementos pasen a una nueva línea si no caben */
    gap: 10px;       /* Espaciado entre elementos */
    }

    .form-check {
        display: flex;
        align-items: center; /* Alinea el checkbox con el texto */
    }

</style>
@section('content')
<div class="breadcrumbs mb-2">
    <div class="row g-0">
            <div class="row gy-3 mb-2 justify-content-between">
                <div class="col-md-9 col-auto">
                <h4 class="mb-2 text-1100">Crear Nuevo Rol</h4>
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
        <div class='card'>
            <form action="{{ route('RolesPermisos.store') }}" method="POST" class="shadow-lg p-4 rounded-lg bg-white">
                @csrf
                <div class="form-row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Nombre De Rol</label>
                            <input type="text" name="nombre" id="nombre" class="form-control form-control-sm border-2 border-success" placeholder="nombre" required>
                        </div>
                    </div>
                </div>
                <div class="form-row mb-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Permisos</label>
                            <br>
                            <div class="form-check mr-3 mb-2 col-6">
                                <input type="checkbox" id="MarcarTodoCheck" class="form-check-input">
                                <label for="MarcarTodo" class="form-check-label">Marcar todo</label>
                            </div>
                            <small class="form-text text-muted">Seleccione uno o más Permisos.</small>
                            <div class="permissions-container d-flex flex-wrap mt-4">
                                <div class="container">
                                    <div class="row" id="PermisosCheck">
                                        @foreach ($permissions as $value)
                                        <div class="form-check mr-3 mb-2 col-3 col-sm-2 col-sm-2">
                                            <input type="checkbox" name="permissions[]" id="permission_{{ $value->id }}" value="{{ $value->id }}" class="form-check-input" 
                                                {{ (isset($registro) && $registro->permissions->contains($value->id)) ? 'checked' : '' }}>
                                            <label for="permission_{{ $value->id }}" class="form-check-label">{{ $value->name }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @error('roles') 
                                <div class="text-danger mt-2">{{ $message }}</div> 
                            @enderror
                        </div>
                        
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-md btn-block rounded-pill float-end">Guardar</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('MarcarTodoCheck').addEventListener('change', function(){
            var estaMarcado = $('#MarcarTodoCheck').prop('checked');
            var permisos = document.querySelectorAll('#PermisosCheck .form-check-input');
            if (estaMarcado) {
                permisos.forEach(checkbox => {
                    checkbox.checked=true;
                });
            }else{
                permisos.forEach(checkbox => {
                    checkbox.checked=false;
                });
            }
        });
    });
</script>
@endsection