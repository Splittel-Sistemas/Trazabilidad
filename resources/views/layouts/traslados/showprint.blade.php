<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="{{asset('menu2/vendors/simplebar/simplebar.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">

    <link href="{{asset('menu2/assets/css/theme-rtl.min.css')}}" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="{{asset('menu2/assets/css/theme.min.css')}}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{asset('menu2/assets/css/user-rtl.min.css')}}" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="{{asset('menu2/assets/css/user.min.css')}}" type="text/css" rel="stylesheet" id="user-style-default">

    <title>Detalle</title>

    <style>
        html {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-sm table-borderless">
                <tbody>
                    <tr>
                        <td rowspan="4"><img src="/imagenes/optronics.jpg" alt="logo" width="200px"></td>
                        <td>Codigo:</td>
                        <td>T{{str_pad($traslado->id, 6, "0", STR_PAD_LEFT)}}</td>
                    </tr>

                    <tr>
                        <td>Genero Traslado:</td>
                        <td>{{$traslado->usuarioTraslado->name}}</td>
                    </tr>

                    <tr>
                        <td>Estado:</td>
                        <td>
                            {{ $traslado->estado }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0rem;">Fecha</td>
                        <td style="padding: 0rem;">{{$traslado->alta}}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <table class="table table-sm table-bordered text-center">
        <thead>
            <tr>
                <th>#</th>
                <th>Orden Venta</th>
                <th>Orden Fabricacion</th>
                <th>Recibo Producción</th>
                <th>Cod. Cliente</th>
                <th>Cliente</th>
                <th>Cod. Producto</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Lote</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($traslado->trasladoDetalles as $indice => $detalle)
            <tr>
                <td>{{ $indice + 1 }}</td>
                <td>{{ $detalle->ov }}</td>
                <td>{{ $detalle->of }}</td>
                <td>{{ $detalle->rp }}</td>
                <td>{{ $detalle->cardcode }}</td>
                <td>{{ $detalle->cardname }}</td>
                <td>{{ $detalle->itemcode }}</td>
                <td>{{ $detalle->dscription }}</td>
                <td>{{ $detalle->quantity_transfer }}</td>
                <td>{{ $detalle->batchnum }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="mt-3 table table-sm table-borderless">
        <tbody>
            <tr>
                <td style="border-bottom: 1px solid #000;">Envia:</td>
                <td style="border-bottom: 1px solid #000;"></td>
                <td style="border-bottom: 1px solid #000;"></td>
                <td></td>
                <td style="border-bottom: 1px solid #000;">Recibe:</td>
                <td style="border-bottom: 1px solid #000;"></td>
                <td style="border-bottom: 1px solid #000;"></td>
            </tr>
        </tbody>
    </table>

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            window.print();
        });
    </script>
</body>

</html>