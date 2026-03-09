@extends('layouts.menu2') 
@section('title', 'Crear Rol') 
<style>
</style>
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Nuevo Rol</h4>
        </div>
    </div>
    <nav style="--phoenix-breadcrumb-divider: '&gt;&gt;';" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{route('index.operador')}}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{route('RolesPermisos.index')}}">Roles & Permisos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Registro</li>
        </ol>
    </nav>
    <div class="row g-0">
        <div class='card p-4'>
            <form action="{{ route('RolesPermisos.store') }}" method="POST" class="">
                @csrf
                <div class="form-row mb-3">
                    <div class="col-6 col-sm-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Nombre De Rol</label>
                            <input type="text" name="nombre" oninput="RegexMayusculas(this)" id="nombre" value="{{old('nombre')}}" class="form-control form-control-sm border-2 border-success" placeholder="nombre" autocomplete="off">
                        </div>
                        @error('nombre')
                            <small class="text-danger d-block mt-1 animated fadeIn">{{ $message }}</small>
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
                                <small class="text-danger d-block mt-n1 animated fadeIn">{{ $message }}</small>
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
                                    </div>
                                </div>
                            </div>
                            @error('roles') 
                                <div class="text-danger mt-2">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-5">
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