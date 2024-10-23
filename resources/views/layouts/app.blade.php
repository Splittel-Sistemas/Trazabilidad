<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Asegúrate de que el CSS esté disponible -->
</head>
<body>
    <div class="container">
        @include('registro.index') <!-- Puedes incluir una barra de navegación aquí -->
        @yield('content')
    </div>
    <script src="{{ asset('js/app.js') }}"></script> <!-- Asegúrate de que el JS esté disponible -->
</body>
</html>
