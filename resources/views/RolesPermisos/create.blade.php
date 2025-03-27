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
    .sub-permissions {
        display: none; /* Inicialmente oculto */
        padding-left: 1px;
        margin-top: 1px;
        font-size: 0.9rem;
        transition: all 0.3s ease-in-out; /* Efecto suave */
    }

    .sub-permissions .form-check {
        margin-left: 1px;
    }

    .card {
        border: 1px solid #ddd;
        margin-top: 1px;
    }

</style>
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
        <h4 class="mb-2 text-1100">Nuevo Rol</h4>
        </div>
    </div>
    <div class="row g-0">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class='card p-4'>
            <form action="{{ route('RolesPermisos.store') }}" method="POST" class="">
                @csrf
                <div class="form-row mb-3">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Nombre De Rol</label>
                            <input type="text" name="nombre" oninput="RegexMayusculas(this)" id="nombre" class="form-control form-control-sm border-2 border-success" placeholder="nombre" required>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-row mb-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Permisos</label>
                            <small class="form-text text-muted">Seleccione uno o más permisos.</small>
                            <br>
                            <!-- Checkbox "Marcar Todo" -->
                            <div class="form-check mb-3">
                                <input type="checkbox" id="MarcarTodoCheck" class="form-check-input">
                                <label for="MarcarTodoCheck" class="form-check-label font-weight-bold">Marcar todo</label>
                            </div>
                            <div class="permissions-container mt-4">
                                <div class="container">
                                    <div class="row" id="PermisosCheck">
                                        @php
                                            $subPermissions = [
                                                'Vista Lineas' => ['Crear Linea', 'Editar Linea', 'Activar/Desactivar Linea'],
                                                'Vista Usuarios' => ['Crear Usuario', 'Editar Usuario', 'Activar/Desactivar Usuario'],
                                                'Vista Roles y permisos' => ['Crear Rol', 'Editar Rol'],
                                            ];
                
                                            $permisosPrincipales = $permissions->reject(function ($permiso) use ($subPermissions) {
                                                foreach ($subPermissions as $subs) {
                                                    if (in_array($permiso->name, $subs)) {
                                                        return true; // Excluir si es un sub-permiso
                                                    }
                                                }
                                                return false;
                                            });
                                        @endphp
                                        @foreach ($permisosPrincipales as $value)
                                            @php
                                                $hasSubPermissions = array_key_exists($value->name, $subPermissions);
                                                $targetID = str_replace(' ', '_', strtolower($value->name));
                                            @endphp
                                            <div class="form-check col-md-2 mb-4">
                                                <input type="checkbox" name="permissions[]" id="permission_{{ $value->id }}" 
                                                    value="{{ $value->id }}" class="form-check-input toggle-sub"
                                                    data-target="{{ $targetID }}"
                                                    {{ (isset($registro) && $registro->permissions->contains($value->id)) ? 'checked' : '' }}>
                                                <label for="permission_{{ $value->id }}" class="form-check-label font-weight-bold">
                                                    {{ $value->name }}
                                                </label>
                                            </div>
                                            @if ($hasSubPermissions)
                                            <div id="{{ $targetID }}" class="sub-permissions col-md-3 ml-1 pl-1" style="display: none;">
                                                <div class="card p-4">
                                                    <div class="row"> 
                                                        @foreach ($subPermissions[$value->name] as $subPermiso)
                                                            @php
                                                                $subValue = $permissions->firstWhere('name', $subPermiso);
                                                            @endphp
                                                            @if ($subValue)
                                                                <div class="form-check col-md-4"> 
                                                                    <input type="checkbox" name="permissions[]" id="permission_{{ $subValue->id }}" 
                                                                        value="{{ $subValue->id }}" class="form-check-input sub-permission"
                                                                        data-parent="permission_{{ $value->id }}">
                                                                    <label for="permission_{{ $subValue->id }}" class="form-check-label">
                                                                        {{ $subPermiso }}
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div> <!-- Cierra el row -->
                                                </div>
                                            </div>
                                        @endif

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
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary btn-lg btn-block rounded-pill">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".toggle-sub").forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
            let target = document.getElementById(this.dataset.target);
            if (target) {
                if (this.checked) {
                    
                    document.querySelectorAll(".sub-permissions").forEach(function (subPerm) {
                        if (subPerm !== target) {
                            subPerm.style.display = "none";
                            subPerm.previousElementSibling.querySelector("input").checked = false; 
                        }
                    });

                    
                    target.style.display = "block";
                    setTimeout(function () {
                        target.style.height = "auto"; 
                    }, 300);
                } else {
                    target.style.display = "none";  
                }
            }
        });
        let target = document.getElementById(checkbox.dataset.target);
        if (target && checkbox.checked) {
            target.style.display = "block";
        }
    });
    document.getElementById("MarcarTodoCheck").addEventListener("change", function () {
        let allCheckboxes = document.querySelectorAll("input[name='permissions[]']");
        
        allCheckboxes.forEach(chk => {
            chk.checked = this.checked;
            let target = document.getElementById(chk.dataset.target);
            if (target) {
                target.style.display = this.checked ? "block" : "none";
                if (!this.checked) {
                    target.querySelectorAll(".sub-permission").forEach(sub => {
                        sub.checked = false; 
                    });
                }
            }
        });
    });
});
</script>
<script>
    /*
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
    });*/
</script>
@endsection