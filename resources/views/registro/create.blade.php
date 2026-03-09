@extends('layouts.menu2') 
@section('title', 'Crear Usuario')

<style>
    .roles-container {
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
    <div class="breadcrumbs mb-4">
        <div class="row gy-3 mb-1 justify-content-between">
            <div class="col-md-9 col-auto">
                <h4 class="mb-2 text-1100">Registrar Usuario </h4>
            </div>
        </div>
        <nav style="--phoenix-breadcrumb-divider: '&gt;&gt;';" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{route('index.operador')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('registro.index')}}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro</li>
            </ol>
        </nav>
        <div class='card p-n5 col-10 mx-auto mt-3'>
            <div class="col-12   p-3 mt-1 mb-2">
                <ul class="nav nav-pills" id="myTab" role="tablist">
                <li class="nav-item"><a class="nav-link @if(old('TipoUser')!='Operador') active @endif" id="Administrativo-tab" data-bs-toggle="tab" href="#tab-Administrativo" role="tab" aria-controls="tab-Administrativo" aria-selected="true" onclick="toggleForm('A')">Administrativo</a></li>
                <li class="nav-item"><a class="nav-link @if(old('TipoUser')=='Operador') active @endif" id="Operador-tab" data-bs-toggle="tab" href="#tab-Operador" role="tab" aria-controls="tab-Operador" aria-selected="false" onclick="toggleForm('O')">Operador</a></li>
            </ul>
            <hr class="m-0 p-0">
            </div>
            <form action="{{ route('registro.store') }}" method="POST" class=" px-3 rounded" id="form_administrador" style="@if(old('TipoUser')=='Operador') display:none; @endif">
                @csrf
                <h4 class="text-center mb-2 text-muted">Administrativo</h4>
                <input type="hidden" name="TipoUser" value="Administrador">
                <div class="row mx-4 mb-1">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" name="name" autocomplete="off" @if(old('TipoUser')=='Administrador')value="{{ old('name') }}"@endif id="name" class="form-control form-control-sm" placeholder="Ingrese su nombre" autocomplete="off">
                            @error('name')
                                    <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="apellido">Apellidos (s)</label>
                            <input type="text" name="apellido" autocomplete="off" @if(old('TipoUser')=='Administrador')value="{{ old('apellido') }}"@endif id="apellido" class="form-control form-control-sm" placeholder="Ingrese su apellido" autocomplete="off">
                            @error('apellido')
                                    <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" autocomplete="off" id="email" value="{{ old('email') }}" class="form-control form-control-sm" placeholder="Ingrese su correo electrónico" autocomplete="off">
                            <small class="text-danger" id="error_email"></small>
                        </div>
                        @error('email')
                        @if($message=='validation.unique')
                            <small class="text-danger">{{ $message }}</small> 
                        @else
                            <small class="text-danger">{{ $message }}</small> 
                        @endif
                        @enderror
                    </div>
                </div>
                <div class="row mx-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" value="{{ old('password') }}" class="form-control form-control-sm" placeholder="Ingrese su nueva contraseña" autocomplete="off">
                            <small class="text-danger" id="error_password"></small>
                        </div>
                        @error('password')
                        @if($message=='validation.min.string')
                            <small class="text-danger">{{ $message }}</small> 
                        @elseif($message=='validation.confirmed')
                            <small class="text-danger">{{ $message }}</small> 
                        @else
                            <small class="text-danger">{{ $message }}</small> 
                        @endif
                    @enderror
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirme su nueva contraseña" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="form-group mx-6">
                    <label class="font-weight-bold mt-2">Roles</label>
                    <small class="form-text text-muted">Seleccione un rol.</small>
                    <div class="roles-container d-flex flex-wrap w-75 mx-2 p-2 border rounded-pill">
                        @foreach ($roles as $value)
                            <div class="form-check mr-3 col-3">
                                <input type="radio" name="roles[]" id="role_{{ $value->id }}" value="{{ $value->id }}" class="form-check-input"
                                    {{ (isset($registro) && $registro->roles->contains($value->id)) ? 'checked' : '' }}>
                                <label for="role_{{ $value->id }}" class="form-check-label">{{ $value->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('roles') 
                        @if(old('TipoUser')=='Administrador')
                            @if($message=='validation.')
                                <small class="text-danger">{{ $message }}</small> 
                            @else
                                <small class="text-danger">{{ $message }}</small> 
                            @endif
                        @endif
                    @enderror
                </div>
                <div class="d-flex justify-content-center mt-2">
                    <button type="submit" class="btn btn-primary rounded-pill transition-all hover:bg-success hover:text-white">Guardar</button>
                </div>   
            </form>
            <form action="{{ route('operador.store') }}" method="POST" class=" px-3 rounded" id="form_operador" style="@if(old('TipoUser')!='Operador') display:none; @endif">
                @csrf
                <h4 class="text-center mb-2 text-muted">Operador</h4>
                <input type="hidden" name="TipoUser" value="Operador">
                <div class="row mx-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name_operador">Nombre</label>
                            <input type="text" name="Oname" @if(old('TipoUser')=='Operador')value="{{old('Oname')}}"@endif id="name_operador" class="form-control form-control-sm" placeholder="Ingrese su nombre" autocomplete="off">
                             @error('Oname')
                                    <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido_operador">Apellidos (s)</label>
                            <input type="text" name="Oapellido" @if(old('TipoUser')=='Operador') value="{{old('Oapellido')}}"@endif id="apellido_operador" class="form-control form-control-sm" placeholder="Ingrese su apellido" autocomplete="off">
                             @error('Oapellido')
                                    <small class="text-danger">{{ $message }}</small> 
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group mx-6">
                    <label class="font-weight-bold mt-3">Roles</label>
                    <small class="form-text text-muted">Seleccione un rol.</small>
                    <div class="roles-container d-flex flex-wrap mx-2 w-75 border p-2 rounded-pill">
                        @foreach ($roles as $value)
                            <div class="form-check mr-3 col-3">
                                <input type="radio" name="roles[]" id="role_{{ $value->id }}" value="{{ $value->id }}" class="form-check-input"
                                    {{ (isset($registro) && $registro->roles->contains($value->id)) ? 'checked' : '' }}>
                                <label for="role_{{ $value->id }}" class="form-check-label">{{ $value->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')
                        @if(old('TipoUser')=='Operador')
                            @if($message=='validation.')
                                <small class="text-danger">*Campo requerido, selecciona un rol.</small> 
                            @else
                                <small class="text-danger">{{ $message }}</small> 
                            @endif
                        @endif
                    @enderror
                </div>
                <div class="d-flex justify-content-center mt-2">
                    <button type="submit"class="btn btn-primary rounded-pill transition-all hover:bg-success hover:text-white">Guardar</button>
                </div>   
            </form>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        {{--@if ($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('userModal'));
            myModal.show();
            $('#ModalBodyEditarUsuarioCargar').hide();
            error('Datos no validos!','Completa correctamente los datos');
        @endif
        @if(request()->has('Email') OR request()->has('Clave'))
            $('#ModalUsuarioAgregado').modal('show');
        @endif
        @if (session('success')) 
                success('Guaradado correctamente!','{{ session('success') }}')
        @endif--}}
        //Validar y enviar los formularios
        $(document).on('submit', '#form_administrador', function(event) {
            event.preventDefault();
            this.submit();
        });
        $(document).on('submit', '#form_operador', function(event) {
            event.preventDefault();
            this.submit();
        });
    });
    function toggleForm(formulario) {
        document.getElementById("form_operador").style.display = "none";
        document.getElementById("form_administrador").style.display = "none";
        if (formulario == 'A') {
            document.getElementById("form_administrador").style.display = "block";
        } else {
            document.getElementById("form_operador").style.display = "block";
        }
    }
    @if(old('TipoUser')=='Operador')
    $(function () {
        $('#operador').prop('checked', true).trigger('change');
    })
    @endif
</script>
@endsection
