@extends('layouts.menu2')
@section('title', 'Dashboard Operador')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .carousel-content-wrapper{ 
        max-width: 100%;
        overflow-y: auto; 
        padding-left: 8px;
        padding-right: 8px;
    }
    #carouselExampleIndicators{
        width: 100%;
    }
    body {
 
        font-family: 'Arial', sans-serif;
    }
    /*#clock {
        font-size: 16px;
        font-weight: bold;
        color: #c00000;
    }*/
    .carousel-inner {
        width: 100%;
        min-height: 20rem;
        position: relative;
        overflow: hidden; 
        border-radius: 15px;
    }
    .carousel-item {
        min-height: 20rem;
        color: white;
        position: relative;
        text-align: center;
        padding: 20px;
        transition: transform 0.8s ease-in-out;
    }
    .carousel-item div {
      display: grid;
      /*place-items: center;  */
      height: 100%; 
      width: 100%; 
      border-radius: 15px;
    }
    .carousel-control-prev,
    .carousel-control-next {
      z-index: 3;
      width: 8%;
    }
    .carousel-indicators li {
      background-color: white;
    }
    .custom-rounded {
        border-radius: 15px;
    }
    .carousel-title{
        color: #fdfdfd;
        margin-bottom: 0.5rem; 
    }
    .carousel-content{
        color: #ffffff;
        margin-top: 0; 
    }
    #ImgSinAvisos{
        margin: 1rem 1rem 2rem 1rem;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #ImgSinAvisos img{
        width: 60%;
    }
    .Div-centrar{
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sombra{
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
  </style>
@endsection
@section('content')
@php
    $colores = ['#FF5733', '#33B5E5', '#4CAF50', '#FFC107', '#9C27B0']; 
@endphp
@if(Auth::user()->hasPermission("Enviar Avisos"))
    <div class="d-flex justify-content-end mb-1">
        <button class="btn btn-outline-primary custom-rounded" type="submit" id="enviaraviso">Nuevo Aviso</button>
    </div>
@endif
<div class="d-flex justify-content-center m-1"> 
    <div class="col-12 col-sm-10 p-2 " > 
        @if($Avisos->count()==0)
            <img class="d-block mx-auto" src="{{asset('imagenes/Trazabilidad.png') }}" style="width: 5rem">
            <h3 class="text-center text-muted">¡Te damos la bienvenida! </h3>
            <h4 class="text-center text-dark">{{ ucfirst($user->name) }} {{ ucfirst($user->apellido) }}</h4>          
            <div id="clock" style="display:none;">
                <span id="date"></span>
                <span id="time"></span>
            </div> 
            <div id="ImgSinAvisos">
                <img class="" src="{{asset('imagenes/splittel.png') }}" alt="Splittel">
            </div>
        @else
            <h3 class="text-center text-muted">¡Te damos la bienvenida! </h3>
            <h4 class="text-center text-dark">{{ ucfirst($user->name) }} {{ ucfirst($user->apellido) }}</h4> 
            <img class="d-block mx-auto" src="{{asset('imagenes/Trazabilidad.png') }}" style="width: 3.5rem">
            <h3 class="text-center text-bold mx-2" style="color: #c00000">Avisos</h3>
            <div class="Div-centrar">
                <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="false">
                    <ol class="carousel-indicators">
                        @foreach($Avisos as $index => $aviso)
                            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner sombra">
                        @foreach($Avisos as $index => $Aviso)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" 
                                @if($Aviso->Html == 'no')style="background-color: {{ $colores[$index % count($colores)] }};"@else style="background-color:white;"@endif>
                                <div class="carousel-content-wrapper">
                                    @if($Aviso->Html == 'no')
                                    <h3 class="carousel-title">{{ ucwords($Aviso->titulo ?? 'Avisos') }}</h3>
                                    <p class="carousel-content">{{ $Aviso->Contenido }}</p>
                                    @else
                                    <h3 class="text-dark">{{ ucwords($Aviso->titulo ?? 'Avisos') }}</h3>
                                    <div class="text-dark">
                                        {!! $Aviso->Contenido !!}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
<div style="height: 30px;"></div>
<div class="modal fade" id="avisoModal" tabindex="-1" aria-labelledby="avisoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold text-white" id="avisoModalLabel">Aviso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-1">
                <form action="{{ route('guardarAviso') }}" method="POST">
                    @csrf
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="CodigoHtml" name="CodigoHtml" {{ old('CodigoHtml') ? 'checked' : '' }}>
                        <label class="form-check-label" for="flexSwitchCheckDefault"><i class="fas fa-code"></i>  Codigo HTML</label>
                    </div>
                    <div class="mb-1 px-4">
                        <label for="Titulo" class="form-label fw-semibold">Título (Opcional)</label>
                        <input type="text" class="form-control border-2 rounded-3" id="Titulo" name="Titulo" autocomplete="off" value="{{ old('Titulo') }}" placeholder="Escribe un título...">
                        @error('Titulo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="row px-4">
                        <div class="mb-1 col-6">
                            <label for="FechaInicio" class="form-label fw-semibold">Fecha Inicio</label>
                            <input type="datetime-local" class="form-control border-2 rounded-3" id="FechaInicio" value="{{ old('FechaInicio', date('Y-m-d\T00:00')) }}"  name="FechaInicio">
                            @error('FechaInicio')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-1 col-6">
                            <label for="FechaFin" class="form-label fw-semibold">Fecha Fin</label>
                            <input type="datetime-local" class="form-control border-2 rounded-3" id="FechaFin" value="{{ old('FechaFin', date('Y-m-d\T23:59')) }}" name="FechaFin">
                            @error('FechaFin')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-1 px-4">
                        <label for="Aviso" class="form-label fw-semibold">Aviso</label>
                        <textarea class="form-control border-2 rounded-3" id="Aviso" autocomplete="off" name="Aviso" rows="4" placeholder="Escribe tu aviso aquí..." style="resize: none;"></textarea>
                        @error('Aviso')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        @error('Aviso')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="height: 5px;"></div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded-3 shadow-sm">Guardar </button>
                    </div>
                    <div style="height: 5px;"></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myCarousel = new bootstrap.Carousel(document.getElementById('carouselExampleIndicators'), {
            interval: 4000,  
            ride: true,      
            wrap: true     
        });
    });
    /*function updateClock() {
        const now = new Date();
        const day = now.getDate();
        const year = now.getFullYear();
        const months = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
        const month = months[now.getMonth()];
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        document.getElementById('date').textContent = `${day} de ${month} del ${year}`;
        document.getElementById('time').textContent = `${hours}:${minutes}:${seconds}`;
    }*/
    //setInterval(updateClock, 1000);
    //updateClock();
    /*function updateMessage() {
        const now = new Date();
        const hour = now.getHours();
        let message = "¡Que tengas un gran día!";
        if (hour >= 5 && hour < 12) {
            message = "🌞 ¡Buenos días!";
        } else if (hour >= 12 && hour < 18) {
            message = "🌤️ ¡Buenas tardes!";
        } else if (hour >= 18 && hour < 22) {
            message = "🌆 ¡Buenas noches!";
        } else {
            message = "🌆 ¡Buenas noches!";
        }

        const messageElement = document.getElementById('message');
        messageElement.style.opacity = 0;
        setTimeout(() => {
            messageElement.textContent = message;
            messageElement.style.opacity = 1;
        }, 500);
    }*/
    //updateMessage();
    document.addEventListener("DOMContentLoaded", function() {
        const btnAbrirModal = document.getElementById('enviaraviso');
        const modal = new bootstrap.Modal(document.getElementById('avisoModal'));
        btnAbrirModal.addEventListener('click', function() {
            modal.show();
        });
    });
    /*document.addEventListener("DOMContentLoaded", function() {
        var fechaInput = document.getElementById('fecha');
        var today = new Date();
        var day = String(today.getDate()).padStart(2, '0'); 
        var month = String(today.getMonth() + 1).padStart(2, '0'); 
        var year = today.getFullYear();
        fechaInput.value = year + '-' + month + '-' + day;
    });*/
    setTimeout(function() {
        let alert = document.querySelector(".alert");
        if (alert) {
            alert.style.transition = "opacity 0.5s ease-out";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        }
    }, 3000); 
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            var avisoModal = new bootstrap.Modal(document.getElementById('avisoModal'));
            avisoModal.show();
        });
    @endif
    @if(session('success'))
        success('Guradado!', 'Aviso guardado correctamente');
    @endif
</script>
@endsection