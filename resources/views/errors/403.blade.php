@extends('layouts.menu2')
@section('title', 'Error 403')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
@section('content')
      <div class="px-0">
        <div class="row min-vh-50 flex-center">
          <div class="col-12 col-xl-10 col-xxl-8">
            <div class="row justify-content-center align-items-center">
              <div class="col-12 col-lg-6 text-center order-lg-1">
                <img class="img-fluid w-lg-100 d-dark-none" src="{{asset('menu2/assets/img/spot-illustrations/403-illustration.png')}}" alt="" width="400">
                <img class="img-fluid w-lg-100 d-light-none" src="{{asset('menu2/assets/img/spot-illustrations/dark_403-illustration.png')}}" alt="" width="400"></div>
              <div class="col-12 col-lg-6 text-center text-lg-start">
                <img class="img-fluid mb-3 w-50 w-lg-75 d-dark-none" src="{{asset('menu2/assets/img/spot-illustrations/403.png')}}" alt="">
                <img class="img-fluid mb-3 w-50 w-lg-75 d-light-none" src="{{asset('menu2/assets/img/spot-illustrations/dark_403.png')}}" alt="">
                <h2 class="text-800 fw-bolder mb-3">Acceso Prohibido!</h2>
                <p class="text-900 mb-2">¡Alto! Estás intentando acceder a un módulo para el cual no tienes permisos...</p>
                <p class="text-900 mb-5">Presiona el botón <span class="text-white bg-primary">Home</span> para regresar al sistema</p>
                <a class="btn btn-lg btn-primary" href="{{route('index.operador')}}">Home</a>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection
@section('scripts')
<script></script>
@endsection
