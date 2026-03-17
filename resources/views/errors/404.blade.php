<!DOCTYPE html>
<html lang="en-US" dir="ltr" class="chrome windows fontawesome-i2svg-active fontawesome-i2svg-complete">
    <head>
        <link rel="shortcut icon" type="image/x-icon" href="{{asset('imagenes/Trazabilidad.png') }}" sizes="10x1">
        <link rel="manifest" href="{{asset('menu2/assets/img/favicons/manifest.json')}}">
        <meta name="msapplication-TileImage" content="{{asset('menu2/assets/img/favicons/mstile-150x150.png')}}">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Error 404</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <style>

    </style>
    <body>
        <main class="main" id="top">
      <div class="px-3">
        <div class="row min-vh-100 flex-center p-1">
            <div class="col-12 col-sm-6 d-flex flex-column align-items-center justify-content-center vh-100 text-center">
                <img class="img-fluid mb-6 w-50 w-lg-75 d-dark-none" src="{{asset('menu2/assets/img/spot-illustrations/404.png')}}" alt=""><img class="img-fluid mb-6 w-50 w-lg-75 d-light-none" src="../../assets/img/spot-illustrations/dark_404.png" alt="">
                <img class="img-fluid mb-6 w-50 w-lg-75 d-light-none" src="../../assets/img/spot-illustrations/dark_404.png" alt="">
                <h2 class="text-800 fw-bolder mb-3">Página no encontrada!</h2>
                <p class="text-900 mb-5"> La ruta a la que deseas acceder no existe.<br class="d-none d-sm-block">Presiona el botón <span class="text-white bg-primary">Home</span> para regresar al sistema. </p>
                <a class="btn btn-lg btn-primary" href="{{route('index.operador')}}">Home</a>
            </div>
            <div class="col-12 col-sm-6 d-flex flex-column align-items-center justify-content-center vh-100 text-center">
                <img class="img-fluid w-lg-100 d-dark-none" src="{{asset('menu2/assets/img/spot-illustrations/404-illustration.png')}}" alt="" width="400">
            </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>