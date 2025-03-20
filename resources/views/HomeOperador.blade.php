@extends('layouts.menu2')
@section('title', 'Dashboard Operador')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body {
 
        font-family: 'Arial', sans-serif;
    }
    .welcome-container {
        text-align: center;
        animation: fadeIn 1s ease-in-out;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }
    .welcome-title {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }
    .user-name {
        font-size: 22px;
        color: #c00000;
        font-weight: bold;
    }
    .welcome-message {
        font-size: 16px;
        color: #555;
        margin-top: 15px;
    }
    .usuario-icono {
        background: #c00000;
        color: white;
        padding: 20px;
        font-size: 50px;
        border-radius: 50%;
        margin-bottom: 15px;
    }
    #clock {
        font-size: 16px;
        margin-top: 10px;
        font-weight: bold;
        color: #c00000;
    }
    .messages-container {
        padding: 20px;

        border-radius: 15px;
        max-width: 800px;
        margin: 20px auto;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    .message {

        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }
    .message h3 {
        color: #007BFF;
    }
    .message p {
        color: #555;
    }
    .btn-primary {
        background: #007BFF;
        border: none;
        transition: 0.3s;
    }
    .btn-primary:hover {
        background: #0056b3;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
<style>
    .carousel-inner {
      position: relative;
      overflow: hidden; 
      border-radius: 15px;
    }
    .carousel-item {
      height: 200px;
      width: 100%;
      color: white;
      position: relative;
      text-align: center;
      padding: 20px;
      transition: transform 0.8s ease-in-out;
     
      
    
    }
    .carousel-item div {
      display: grid;
      place-items: center;  
      height: 100%; 
      width: 100%; 
      border-radius: 15px;
    }
    .carousel-control-prev,
    .carousel-control-next {
      z-index: 3;
      width: 5%;
    }
    .carousel-indicators li {
      background-color: white;
    }
    .carousel-title {
    margin-bottom: 0.5rem; 
  }

  .carousel-content {
    margin-top: 0; 
  }
  .custom-rounded {
    border-radius: 15px;
   }
  </style>
@endsection
@section('content')
@php
    $colores = ['#FF5733', '#33B5E5', '#4CAF50', '#FFC107', '#9C27B0']; 
@endphp
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(Auth::user()->hasPermission("Enviar Avisos"))
    <div class="d-flex justify-content-end">
        <button class="btn btn-outline-info custom-rounded" type="submit" id="enviaraviso">Nuevo Aviso</button>
    </div>
@endif    
<div style="height: 5px;"></div>
<div class="welcome-container"> 
   <!-- <i class="far fa-user usuario-icono"></i>-->
    <h1 class="welcome-title">Bienvenido</h1>
    <h2 class="user-name">{{ ucfirst($user->name) }} {{ ucfirst($user->apellido) }}</h2>          
    <p class="welcome-message" id="message"></p>
    <div id="clock">
        <div id="date"></div>
        <div id="time"></div>
    </div> 
    <div style="height: 5px;"></div>
    
    <div class="card">
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="false">
            <ol class="carousel-indicators">
            @foreach($avisos as $index => $aviso)
                <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></li>
            @endforeach
            </ol>
            <div class="carousel-inner">
                @foreach($avisos as $index => $aviso)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" 
                    style="background-color: {{ $colores[$index % count($colores)] }};">
                    <div class="carousel-content-wrapper" style="max-height: 200px; overflow-y: auto; padding: 10px;">
                        <h3 class="carousel-title">{{ ucwords($aviso->titulo ?? 'Avisos') }}</h3>
                        <p class="carousel-content">{{ $aviso->contenido }}</p>
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
</div>
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
                    <div class="mb-1">
                        <label for="titulo" class="form-label fw-semibold">Título (Opcional)</label>
                        <input type="text" class="form-control border-2 rounded-3" id="titulo" name="titulo" placeholder="Escribe un título...">
                    </div>
                    <div class="mb-1">
                        <label for="fecha" class="form-label fw-semibold">Fecha</label>
                        <input type="date" class="form-control border-2 rounded-3" id="fecha" name="fecha">
                    </div>
                    <div class="mb-1">
                        <label for="aviso" class="form-label fw-semibold">Aviso</label>
                        <textarea class="form-control border-2 rounded-3" id="aviso" name="aviso" rows="4" placeholder="Escribe tu aviso aquí..." style="resize: none;"></textarea>
                    </div>
                    <div style="height: 5px;"></div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded-3 shadow-sm">Enviar </button>
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
    function updateClock() {
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
    }
    setInterval(updateClock, 1000);
    updateClock();
    function updateMessage() {
        const now = new Date();
        const hour = now.getHours();
        let message = "¡Que tengas un gran día!";
        if (hour >= 5 && hour < 12) {
            message = "🌞 ¡Buenos días! ¡A comenzar con energía!";
        } else if (hour >= 12 && hour < 18) {
            message = "🌤️ ¡Buenas tardes! Sigue con el gran trabajo.";
        } else if (hour >= 18 && hour < 22) {
            message = "🌆 ¡Buenas noches! Mantén el ritmo, casi terminas.";
        } else {
            message = "🌙 ¡Turno nocturno en marcha! Mucho éxito y concentración.";
        }

        const messageElement = document.getElementById('message');
        messageElement.style.opacity = 0;
        setTimeout(() => {
            messageElement.textContent = message;
            messageElement.style.opacity = 1;
        }, 500);
    }
    updateMessage();
    document.addEventListener("DOMContentLoaded", function() {
        const btnAbrirModal = document.getElementById('enviaraviso');
        const modal = new bootstrap.Modal(document.getElementById('avisoModal'));
        btnAbrirModal.addEventListener('click', function() {
            modal.show();
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        var fechaInput = document.getElementById('fecha');
        var today = new Date();
        var day = String(today.getDate()).padStart(2, '0'); 
        var month = String(today.getMonth() + 1).padStart(2, '0'); 
        var year = today.getFullYear();
        fechaInput.value = year + '-' + month + '-' + day;
    });
    setTimeout(function() {
        let alert = document.querySelector(".alert");
        if (alert) {
            alert.style.transition = "opacity 0.5s ease-out";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        }
    }, 3000); 
</script>
@endsection