@extends('layouts.menu') 
@section('title', 'Registro Usuario') 
@section('styles') 
    {{--<link rel="stylesheet" href="{{asset('css/style.css')}}">--}}
@endsection

@section('content') 
  <h1 class="h5 text-danger text-center mb-4">Proporcione datos correctos</h1>
  <form action="">  {{--URL a la que se envían los datos del formulario. --}}
      <div class="row mb-3">
          <div class="col-md-6">
              <label for="apellido">Apellidos:</label>
              <input type="text" class="form-control" placeholder="Apellidos" id="apellido" required>
          </div>
          <div class="col-md-6">
              <label for="nombre">Nombre:</label>
              <input type="text" class="form-control" placeholder="Nombre" id="nombre" required>
          </div>
      </div>
      <div class="row mb-3">
          <div class="col-md-6">
              <label for="email">Email address:</label>
              <input type="email" class="form-control" placeholder="Enter email" id="email" required>
          </div>
          <div class="col-md-6">
              <label for="pwd">Password:</label>
              <input type="password" class="form-control" placeholder="Enter password" id="pwd" required>
          </div>
      </div>
      <div class="mb-3">
          <label class="form-label">Actividad:</label>
          <div class="input-group">
              <div class="input-group-prepend">
                  <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Selecciona actividad</button>
                  <div class="dropdown-menu">
                      <a class="dropdown-item" href="#">Limpieza de la Fibra Óptica</a>
                      <a class="dropdown-item" href="#">Preparación de la Fibra</a>
                      <a class="dropdown-item" href="#">Corte de la Fibra</a>
                      <a class="dropdown-item" href="#">Fusión de la Fibra</a>
                      <a class="dropdown-item" href="#">Protección de la Junta de Fusión</a>
                      <a class="dropdown-item" href="#">Pruebas de Conectividad</a>
                      <a class="dropdown-item" href="#">Documentación y Mantenimiento</a>
                  </div>
              </div>
          </div>
      </div>
      <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="rememberMe">
          <label class="form-check-label" for="rememberMe">Remember me</label>
      </div>
      <div class="text-center">
          <button type="submit" class="btn btn-primary" style="padding: 8px 15px; font-size: 0.9rem; border-radius: 4px;">Submit</button>
      </div>
  </form>
</div>

    <p></p>

@endsection
@section('scripts') 
    {{--<script src="{{asset('js/main.js')}}"></script>--}}
@endsection
