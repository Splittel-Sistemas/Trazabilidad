<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <style>
        @import url(https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300);
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">
        <h1>Bienvenido</h1>
        <form method="POST" action="{{route('login_post')}}" class="form">
            
            @csrf
            <input name="email" type="text" placeholder="Username" required>
            <input name="password" type="password" placeholder="Password"required>
            <button type="submit" id="login-button">Ingresar</button>
        </form>
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
    </ul>
</div>

<script>
    $("#login-button").click(function(event) {
        //event.preventDefault();
        //$('form').fadeOut(500);
        //$('.wrapper').addClass('form-success');
    });
</script>
</body>
</html>
