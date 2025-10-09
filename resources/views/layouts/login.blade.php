<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=0.8, user-scalable=no">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('imagenes/splittel.png') }}">
    <title>Login</title>
    <script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Source Sans Pro', sans-serif;
        }
        body {
            /*background: linear-gradient(to right, #21598a, #b31b00);*/
            /*background:linear-gradient(135deg, #ff0000 50%, #0000ff 50%);*/
            /*background: linear-gradient(to right, #b40018,#747474);/*, #fdfdfd);*/
            overflow: hidden;
            background:linear-gradient(135deg, #00458f 50%,#00254d 50%);
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        .container {
            background: rgba(255, 255, 255, 255);
            padding: 12px;
            border-radius: 2.5rem;
            box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.493);
            text-align: center;
            max-width: 40rem;
            width: 40rem;
        }
        .container h1 {
            color: #272727;
            font-family:Arial, Helvetica, sans-serif;
            font:bold;
            margin-bottom: 1.1rem;
            font-size:x-large;
        }
        .btn-select{
            margin: 0;
            padding: 0.5rem;
            border-radius: 1rem;
            width: 12rem;
            border: #007bff;
            border-width: 2px;
            transition: background 0.4s, color 0.3s;
        }
        .btn-select:hover{
            background:#0056b3;
            color: #fff;
        }
        .bg-select{
            width: 90%;
            margin: 0 auto;
            padding-bottom: 2rem;
        }
        .active{
            background: #003d82;
            color: white;
        }
        .img{
            margin-top: 0.5rem;
        }
        .input-field {
            transition: border-color 0.3s, outline 0.3s;
            width: 90%;
            padding:0.6rem;
            margin: 1rem 0;
            border: 2px solid #7e0404;
            border-radius: 90px;
            font-size: 16px;
        }
        .input-field:focus {
            transition: border-color 0.4s, color 0.3s;
            border-color: #007bff;
            outline: none;
        }
        #login-button {
            width: 50%;
            padding: 10px;
            border: none;
            border-radius: 90px;
            background-color: #0056b3;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        #login-button:hover {
            background-color:#003d82;
            transform: scale(1.1);
        }
        #login-operador {
            width: 50%;
            margin-top: 1rem;
            padding: 10px;
            border: none;
            border-radius: 90px;
            background-color:#0056b3;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        #login-operador:hover {
            background-color: #003d82;
            transform: scale(1.1);
        }
        .bg-bubbles{
            z-index: -1;
        }
        .bg-bubbles li {
            position: absolute;
            list-style: none;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.603);
            bottom: -160px;
            animation: square 20s infinite linear; /* Animación de las burbujas */
        }
        .li-errores{
                list-style-type: none;
        }
        @keyframes square {
            0% {
                transform: translateY(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            100% {
                /*transform: translateY(-90vh); /* Sube fuera de la pantalla */
                opacity: 1;
                transform: translateY(-90vh) rotate(600deg);
            }
        }
        /* Posiciones y tamaños de las burbujas */
        .bg-bubbles li:nth-child(1) { top: 10%; left: 10%; }
        .bg-bubbles li:nth-child(2) { top: 15%; left: 40%; width: 60px; height: 60px; }/*width: 80px; height: 80px; }*/
        .bg-bubbles li:nth-child(3) { top: 20%; left: 70%; width: 60px; height: 60px; }
        .bg-bubbles li:nth-child(4) { top: 40%; left: 20%; width: 60px; height: 60px; }
        .bg-bubbles li:nth-child(5) { top: 50%; left: 80%; width: 60px; height: 60px; }
        .bg-bubbles li:nth-child(6) { top: 60%; left: 65%; width: 120px; height: 120px; }
        .bg-bubbles li:nth-child(7) { top: 70%; left: 30%; width: 140px; height: 140px; }
        .bg-bubbles li:nth-child(8) { top: 80%; left: 80%; width: 40px; height: 40px; }
        .bg-bubbles li:nth-child(9) { top: 15%; left: 90%; width: 30px; height: 30px; }
        .bg-bubbles li:nth-child(10) { top: 90%; left: 10%; width: 120px; height: 120px; }
        .bg-bubbles li:nth-child(11) { top: 100%; left: 85%; width: 130px; height: 130px; }
    </style> 
</head>
<body>
        <div class="wrapper">
            <div class="container">
                <img class="img" src="{{asset('imagenes/splittel.png') }}" alt="Splittel" width="100" height="40">
                <h1 class="m-0 p-0">Trazabilidad</h1>
                <div class="text-center mb-1 bg-select">
                    <button class="btn-select active" id="toggleAdministrativo">Administrativo</button>
                    <button class="btn-select " id="toggleOperadores">Operadores</button>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger p-0 mb-0">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">x</button>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="li-errores">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!-- Formulario para Administrativos -->
                <form method="POST" action="{{ route('login_post') }}" class="form" id="formAdministrativo">
                    @csrf
                    <div id="administrativoFields">
                        <input name="email" id="email" type="email" placeholder="Correo Electrónico" value="{{ old('email') }}" required class="input-field">
                        <input name="password" type="password" placeholder="Contraseña" required class="input-field" autocomplete="off">
                    </div>
                    <button type="submit" id="login-button">Ingresar</button>
                </form>
                <!-- Formulario para Operadores (Oculto por defecto) -->
                <form method="POST" action="{{ route('operador.login') }}" class="form" id="formOperador" style="display: none;">
                    @csrf
                    <div id="administrativoFields mt-4">
                        <input name="clave" id="clave" type="text" placeholder="Clave" required class="input-field" autocomplete="off">
                        <button type="submit" class="mt-4" id="login-operador">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
        <ul class="bg-bubbles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
</body>
<script>
    document.getElementById('toggleAdministrativo').addEventListener('click', function() {
        // Activar el botón de Administrativos
        this.classList.add('active');
        document.getElementById('toggleOperadores').classList.remove('active');
        // Mostrar solo el formulario de Administrativos
        document.getElementById('formAdministrativo').style.display = 'block';
        document.getElementById('formOperador').style.display = 'none';
        document.getElementById('email').focus();
    });
    document.getElementById('toggleOperadores').addEventListener('click', function() {
        // Activar el botón de Operadores
        this.classList.add('active');
        document.getElementById('toggleAdministrativo').classList.remove('active');
        // Mostrar solo el formulario de Operadores
        document.getElementById('formAdministrativo').style.display = 'none';
        document.getElementById('formOperador').style.display = 'block';
        document.getElementById('clave').focus();
    });
    $(document).ready(function () {
        document.getElementById('email').focus();
        $('#formAdministrativo').on('submit', function (event) {
            event.preventDefault();
            refreshCsrfToken("Admin");
            $('#login-button').prop('disabled', true).text('Enviando...');
            setTimeout(() => {
                alert("El envío del formulario está tardando demasiado. Intenta nuevamente.");
            }, 120000);
        });
        $('#formOperador').on('submit', function (event) {
            event.preventDefault();
            refreshCsrfToken("Oper");
            $('#login-operador').prop('disabled', true).text('Enviando...');
            setTimeout(() => {
                alert("El envío del formulario está tardando demasiado. Intenta nuevamente.");
            }, 120000);
        });
        //Actualiza la pagina al abrir
        window.onpageshow = function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
        };
        @if (session('Vista'))
            document.getElementById('toggleOperadores').click();
        @endif
    });
    function refreshCsrfToken(Formulario) {
        $.get("{{ route('UpdateToken') }}", function (data) {
            const newToken = data.token;
            // 1. Actualizar <meta name="csrf-token">
            $('meta[name="csrf-token"]').attr('content', newToken);
            // 2. Actualizar todos los formularios con el nuevo token
            $('input[name="_token"]').val(newToken);
            if(Formulario == "Oper"){
                $('#formOperador').off('submit').submit();
            }else{
                $('#formAdministrativo').off('submit').submit();
            }
        });
    }
</script>
</html>
