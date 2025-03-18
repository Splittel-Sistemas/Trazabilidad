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
        box-shadow: 0 6px 20px rgba(247, 247, 247, 0.1);
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

    .form-label {
        font-size: 1rem;
        color: #f7f0f0;
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
    .usuario-icono{
        background: #c00000;
        color: white;
        padding: 1.3rem;
       font-size: 5rem;
       border-radius: 100px;
    }



        #clock {
        text-align: center;
        font-family: Arial, sans-serif;
        font-size: 24px;
    }

    #date {
        font-size: 20px;
        font-weight: bold;
        color: #c00000;
    }

    #time {
        font-size: 25px;
        font-weight: bold;
        color: #c00000;
    }

</style>
@endsection

@section('content')

    <div class="card" style="background-color: rgba(0, 0, 0, 0.1);">
        <div class="welcome-container">
            <div class="profile-image col-12">
                <i class="far fa-user usuario-icono"></i>
            </div>
            <div class="welcome-container">
                <h1 class="welcome-title">Bienvenido</h1>
                <h2 class="user-name">{{ ucfirst($user->name) }} {{ ucfirst($user->apellido) }}</h2>          
                <p class="welcome-message" id="message"></p>
                <div id="clock">
                    <div id="date"></div>
                    <div id="time"></div>
                </div>
                
            </div>  
        </div>
    </div>
@endsection

@section('scripts')
<script>
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
            message = "🌙 ¡Turno nocturno en marcha! Mucha exito y concentración.";
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
