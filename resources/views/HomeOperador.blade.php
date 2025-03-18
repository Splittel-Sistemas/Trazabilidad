@extends('layouts.menu2')

@section('title', 'Dashboard Operador')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body {
       
    }
    .welcome-container {
        text-align: center;
        animation: fadeIn 1s ease-in-out;
        margin-bottom: 20px;
        padding: 20px;
       
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    .icono-user{
        padding: 2rem;
        background: #ff0101;
        border-radius: 100px;

    }
    .profile-image {
        font-size: 50px;
        color: #000000;
    }

    .welcome-title {
        font-size: 30px;
        color: #333;
        font-weight: 600;
        margin: 0;
    }
    .welcome-message {
        font-size: 22px;
        color: #555;
        margin-top: 15px;
        font-weight: 500;
    }
    .clock {
        font-size: 40px;
        color: #4CAF50;
        font-weight: bold;
        margin-top: 25px;
        transition: color 0.3s ease;
        letter-spacing: 2px;
    }
    .form-label {
        font-size: 1rem;
        color: #333;
        font-weight: bold;
    }
    .form-select {
        font-size: 1rem;
        padding: 12px;
        width: auto;
        max-width: 300px;
        border-radius: 30px;
        box-shadow: 0 4px 8px rgba(119, 119, 119, 0.1);
       
        margin-top: 15px;
        transition: all 0.3s ease;
    }
    .form-select:focus {
        border-color: #4CAF50;
        outline: none;
        box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
    }
    .card {
       
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
    }
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }
</style>
@endsection

@section('content')
    <div class="card">
        <div class="welcome-container">
            
            <div class="profile-image col-12">
                <i class="fas fa-user icono-user"></i>
            </div>
            <h1 class="welcome-title">Bienvenido, {{ ucfirst($user->name) }} {{ ucfirst($user->apellido) }}</h1>
            <p class="welcome-message" id="message"></p>
            <div class="clock" id="clock">00:00:00</div>
        </div>
        <div class="container">
            <label for="lineSelector" class="form-label">Seleccione una línea</label>
            <select id="lineSelector" class="form-select">
                <option value="">Selecciona una línea</option>
                @foreach($linea as $l)
                    <option value="{{ $l->id }}" {{ $l->NumeroLinea == 1 ? 'selected' : '' }}>
                        {{ $l->NumeroLinea }}-{{ $l->Nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function updateClock() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
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
        } else {
            message = "🌙 ¡Buenas noches! Disfruta tu descanso.";
        }

        const messageElement = document.getElementById('message');
        messageElement.style.opacity = 0;
        setTimeout(() => {
            messageElement.textContent = message;
            messageElement.style.opacity = 1;
        }, 500);
    }
    updateMessage();
</script>
@endsection
