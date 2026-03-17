<!DOCTYPE html>
<html lang="en-US" dir="ltr" class="chrome windows fontawesome-i2svg-active fontawesome-i2svg-complete">
    <head>
        <link rel="shortcut icon" type="image/x-icon" href="{{asset('imagenes/Trazabilidad.png') }}" sizes="10x1">
        <link rel="manifest" href="{{asset('menu2/assets/img/favicons/manifest.json')}}">
        <meta name="msapplication-TileImage" content="{{asset('menu2/assets/img/favicons/mstile-150x150.png')}}">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Error 500</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

  <body>
        <main class="main" id="top">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-6 text-center order-lg-1 d-flex flex-column align-items-center justify-content-center vh-100 text-center">
                        <img class="img-fluid w-lg-100 d-light-none" src="https://cdni.iconscout.com/illustration/premium/thumb/error-500-10492191-8488195.png" alt="Error 500">
                    </div>
                    <div class="col-12 col-lg-6 text-center text-lg-start d-flex flex-column align-items-center justify-content-center vh-100 text-center">
                        <img class="img-fluid mb-6 w-50 w-lg-75 d-dark-none" src="{{asset('menu2/ssets/img/spot-illustrations/500.png')}}" alt="">
                        <img class="img-fluid mb-6 w-50 w-lg-75 d-light-none" src="{{asset('menu2/assets/img/spot-illustrations/dark_500.png')}}" alt="">
                        <h2 class="text-800 fw-bolder mb-2">Error desconocido!</h2>
                        <p class="text-900 mb-4 text-center "><span class="fw-bolder">¡Pero relájate!</span> <br> En este momento estamos trabajando para solucionar el problema.</p>
                        <a class="btn btn-lg btn-primary" href="{{route('index.operador')}}">Home</a>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>