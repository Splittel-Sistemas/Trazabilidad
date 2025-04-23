@extends('layouts.menu2') 
@section('title', 'Crear Rol') 
<style>
    /*.permissions-container {
    display: flex;
    flex-wrap: wrap; 
    gap: 10px;      
    }
    .form-check {
        display: flex;
        align-items: center;
    }
    .sub-permissions {
        display: none;
        padding-left: 1px;
        margin-top: 1px;
        font-size: 0.9rem;
        transition: all 0.3s ease-in-out; 
    }
    .sub-permissions .form-check {
        margin-left: 1px;
    }
    .card {
        border: 1px solid #ddd;
        margin-top: 1px;
    }
*/
</style>
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Nuevo Rol</h4>
        </div>
    </div>
    <div class="row g-0">
        {{--@if ($errors->any())
            <div class="position-relative mb-4" aria-live="polite" aria-atomic="true" style="min-height: 130px;">
                <div class="toast show position-absolute top-0 end-0">
                    <div class="toast-header">
                        <strong class="me-auto">Bootstrap</strong>
                        <small class="text-800">11 mins ago</small>
                        <button class="btn ms-2 p-0" type="button" data-bs-dismiss="toast" aria-label="Close"><span class="uil uil-times fs-1"></span></button>
                    </div>
                    <div class="toast-body">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                      </div>
                    </div>
                </div>
        @endif--}}
        <div class='card p-4'>
            <form action="{{ route('RolesPermisos.store') }}" method="POST" class="">
                @csrf
                <div class="form-row mb-3">
                    <div class="col-6 col-sm-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Nombre De Rol</label>
                            <input type="text" name="nombre" oninput="RegexMayusculas(this)" id="nombre" value="{{old('nombre')}}" class="form-control form-control-sm border-2 border-success" placeholder="nombre" required>
                        </div>
                        @error('nombre')
                            <small class="text-danger">*El nombre del rol que intentas crear ya existe</small>
                        @enderror
                    </div>
                </div>
                <hr>
                <div class="form-row mb-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Permisos</label>
                            <small class="form-text text-muted">Seleccione uno o más permisos.</small>
                            @error('permissions')
                                <small class="text-danger">*Se requiere seleccionar minimo 1 permiso</small>
                            @enderror
                            <br>
                            <!-- Checkbox "Marcar Todo" -->
                            <div class="form-check mb-3">
                                <input type="checkbox" id="MarcarTodoCheck" class="form-check-input">
                                <label for="MarcarTodoCheck" class="form-check-label font-weight-bold">Marcar todo</label>
                            </div>
                            <div class="permissions-container mt-4">
                                <div class="container">
                                    <div class="row" id="PermisosCheck">
                                        @foreach($permissions as $permiso)
                                            <div class="col-3">
                                                @if($permiso->count()>1)
                                                    <input onchange="MostrarInput(this,'collapse{{$permiso[0]->id}}')" type="checkbox" name="permissions[]" id="permission_{{ $permiso[0]->id }}" value="{{ $permiso[0]->id }}" class="form-check-input sub-permission" data-parent="permission_{{ $permiso[0]->id }}">
                                                    <label for="permission_{{ $permiso[0]->id }}" class="form-check-label">
                                                        {{ $permiso[0]->name }}
                                                    </label>
                                                    <div class="collapse collapse-permiso" id="collapse{{$permiso[0]->id}}">
                                                        <hr class="m-0 p-0">
                                                        @foreach($permiso as $key => $unico)
                                                            @if($key>0)
                                                            <div class="col-12 mx-3">
                                                                <input type="checkbox" name="permissions[]" id="permission_{{ $unico->id }}" value="{{ $unico->id }}" class="form-check-input sub-permission" data-parent="permission_{{ $unico->id }}">
                                                                <label for="permission_{{ $unico->id }}" class="form-check-label">
                                                                    {{ $unico->name }}
                                                                </label>
                                                            </div>
                                                            @endif
                                                        @endforeach
                                                        <hr class="m-1 p-0">
                                                    </div>
                                                @else
                                                    <input type="checkbox" data-bs-toggle="collapse" href="#collapse{{$permiso[0]->id}}" name="permissions[]" id="permission_{{ $permiso[0]->id }}" value="{{ $permiso[0]->id }}" class="form-check-input sub-permission" data-parent="permission_{{ $permiso[0]->id }}">
                                                    <label for="permission_{{ $permiso[0]->id }}" class="form-check-label">
                                                        {{ $permiso[0]->name }}
                                                    </label>
                                                @endif
                                            </div>
                                        @endforeach
                                        {{--@php
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
                                        @endforeach--}}
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
                    <button type="submit" class="btn btn-primary  btn-block rounded-pill">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("MarcarTodoCheck").addEventListener("change", function () {
            let allCheckboxes = document.querySelectorAll("input[name='permissions[]']");
            var items = document.querySelectorAll('.collapse-permiso');
                        items.forEach(item => {
                            $(item).addClass('show');
                            $(item).slideDown();  
                            // Solo abrir si está cerrado
                                //new bootstrap.Collapse(item, {toggle: false}).show();
                        });
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
    function MostrarInput(checkbox,idcollapse){
        var collapseElement = $('#'+idcollapse);
        if(checkbox.checked){
            collapseElement.show();
        }else{
            collapseElement.hide();
            collapseElement.find('input[type="checkbox"]').prop('checked', false);
        }
    }
</script>
@endsection