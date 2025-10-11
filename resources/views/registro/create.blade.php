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
        <div class="row gy-3 mb-2 justify-content-between">
            <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Registrar Usuario </h4>
            </div>
        </div>
        {{--@if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif--}}
        <div class="card shadow-sm border-light col-4 p-3 mb-1">
            <div >
                <p class="m-0">Selecciona el Tipo de Claves</p>
            </div>
            <div class="form-check mt-1">
                <input class="form-check-input" id="administrador" type="radio" name="usuario_tipo" checked onchange="toggleForm()">
                <label class="form-check-label" for="administrador">Asignar claves de accesso &#40;Administrativo&#41;</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="operador" type="radio" name="usuario_tipo" onchange="toggleForm()">
                <label class="form-check-label" for="operador">Generar clave &#40;Operador&#41;</label>
            </div>
        </div>
        <div style="height: 30px;"></div>
        <div class='card'>
            <form action="{{ route('registro.store') }}" method="POST" class=" p-3 rounded bg-white" id="form_administrador">
                @csrf
                <input type="hidden" name="TipoUser" value="Administrador">
                <div class="row mx-4 mb-1">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" name="name" @if(old('TipoUser')=='Administrador')value="{{ old('name') }}"@endif id="name" class="form-control form-control-sm" placeholder="Ingrese su nombre" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido">Apellidos (s)</label>
                            <input type="text" name="apellido" @if(old('TipoUser')=='Administrador')value="{{ old('apellido') }}"@endif id="apellido" class="form-control form-control-sm" placeholder="Ingrese su apellido" required>
                        </div>
                    </div>
                </div>
                <div class="row mx-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control form-control-sm" placeholder="Ingrese su correo electrónico" required>
                        </div>
                        @error('email')
                        @if($message=='validation.unique')
                            <small class="text-danger">*El correo que estas ingresando ya existe, ingresa otro correo.</small> 
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
                            <input type="password" name="password" id="password" value="{{ old('password') }}" class="form-control form-control-sm" placeholder="Ingrese su nueva contraseña">
                        </div>
                        @error('password')
                        @if($message=='validation.min.string')
                            <small class="text-danger">*La contraseña tiene que tener una longitud mínima de 8 caracteres.</small> 
                        @elseif($message=='validation.confirmed')
                            <small class="text-danger">*Las contraseñas no coinciden, asegurate de escribir la misma contraseña.</small> 
                        @else
                            <small class="text-danger">{{ $message }}</small> 
                        @endif
                    @enderror
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirme su nueva contraseña">
                        </div>
                    </div>
                </div>
                <div class="form-group mx-6">
                    <label class="font-weight-bold mt-2">Roles</label>
                    <small class="form-text text-muted">Seleccione un rol.</small>
                    <div class="roles-container d-flex flex-wrap w-75 mx-2 p-2 border">
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
                            @if($message=='validation.required')
                                <small class="text-danger">*Campo requerido, selecciona un rol.</small> 
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
        </div>
        
        <!-- Formulario Operador (oculto por defecto) -->
        <div class='card'>
            <form action="{{ route('operador.store') }}" method="POST" class="shadow p-4 rounded bg-white" id="form_operador" style="display:none;">
                @csrf
                <input type="hidden" name="TipoUser" value="Operador">
                <div class="row mx-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name_operador">Nombre</label>
                            <input type="text" name="name" @if(old('TipoUser')=='Operador')value="{{old('name')}}"@endif id="name_operador" class="form-control form-control-sm" placeholder="Ingrese su nombre" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido_operador">Apellidos (s)</label>
                            <input type="text" name="apellido" @if(old('TipoUser')=='Operador') value="{{old('apellido')}}"@endif id="apellido_operador" class="form-control form-control-sm" placeholder="Ingrese su apellido" required>
                        </div>
                    </div>
                </div>
                <div class="form-group mx-6">
                    <label class="font-weight-bold mt-3">Roles</label>
                    <small class="form-text text-muted">Seleccione un rol.</small>
                    <div class="roles-container d-flex flex-wrap mx-2 w-75 border p-2">
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
                            @if($message=='validation.required')
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
    function toggleForm() {
        document.getElementById("form_operador").style.display = "none";
        document.getElementById("form_administrador").style.display = "none";
        if (document.getElementById("administrador").checked) {
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
