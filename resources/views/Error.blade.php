@extends('layouts.menu2')
@section('title', 'Error 403 - Acceso Prohibido')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body, html {
        height: 100%;
        margin: 0;
        font-family: 'Arial', sans-serif;
        overflow: hidden;
    }

    .error-container {
        display: flex;
        justify-content: space-between; /* Alinea los elementos horizontalmente */
        align-items: flex-start; /* Alinea los elementos verticalmente al inicio */
        height: 100vh; /* Asegura que el contenedor tenga toda la altura de la ventana */
       
        text-align: left;
        padding: 20px;
        box-sizing: border-box;
    }

    .error-icon {
        flex-shrink: 0;
        max-width: 400px;
        height: auto;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 100px; /* Alinea la imagen correctamente */
    }

    .error-icon img {
        width: 100%;
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }

    .error-text {
        max-width: 600px;
        flex-grow: 80;
        padding-right: 20px; /* Asegura que haya espacio entre el texto y la imagen */
        margin-top: 20px; /* Mueve el texto hacia arriba para alinearlo con la imagen */
    }

    .error-code {
        font-size: 80px;
        font-weight: bold;
        color: #ff6f61;
        margin: 0px 0;
    }

    .error-message {
        font-size: 28px;
        color: #333;
        margin: 0px 0;
    }

    .home-link {
        font-size: 25px;
        color: #1976d2;
        text-decoration: none;
        padding: 10px 20px;
        border: 2px solid #1976d2;
        border-radius: 9px;
        transition: background-color 0.3s, color 0.3s;
        margin-top: 30px; /* Aumenta el margen superior para separar más el botón */
    }

    .home-link:hover {
        background-color: #1976d2;
        color: #fff;
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .error-container {
            flex-direction: column; /* Apila los elementos verticalmente en pantallas pequeñas */
            align-items: center;
            justify-content: center;
        }

        .error-icon {
            margin-bottom: 20px;
        }

        .error-text {
            max-width: 90%;
            text-align: center;
            margin-top: 5%; /* Ajusta para que el contenido esté más arriba en pantallas pequeñas */
        }

        .error-code {
            font-size: 60px;
        }

        .error-message {
            font-size: 18px;
        }

        .home-link {
            font-size: 16px;
            margin-top: 30px; /* Mantiene el margen superior para el botón */
        }
    }
</style>
@endsection
@section('content')
<div class="error-container">
    <div class="error-text">
        <div class="error-code">403</div>
        <div class="error-message">
            <strong>Acceso Denegado.</strong> Lo sentimos, no tienes los permisos necesarios para ver esta página.
            <br><br>
            Si crees que esto es un error, por favor contacta al administrador.
        </div>
         <div style="height: 15px;"></div>
        <a href="{{ url('/') }}" class="home-link">Volver al inicio</a>
    </div>
    
    <!-- Agregar un espaciado aquí entre los textos y el ícono -->
    <div class="error-icon" > <!-- Ajusta el margen según sea necesario -->
        <img src="{{ asset('sinpermiso.png') }}" alt="Error Icon" />
    </div>
    
</div>
@endsection
@section('scripts')
<script></script>
@endsection
