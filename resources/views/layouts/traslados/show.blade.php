@extends('layouts.menu2')

@section('title', 'Traslados de Almacenes')

@section('content')
<a href="{{route('traslados')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-left" aria-hidden="true"></i> Atras</a>
<div class="container mt-5">
    <h1 class="mb-4 text-center">Detalle Traslado</h1>

    <div class="row">
        <div class="col-md-12">
            <dl class="row">
                <dt class="col-sm-2">Recibo de Producción:</dt>
                <dd class="col-sm-10">{{$traslado->referencia_sap}}</dd>
            </dl>

            <dl class="row">
                <dt class="col-sm-2">Genero Traslado:</dt>
                <dd class="col-sm-10">{{$traslado->usuarioTraslado->name}}</dd>
            </dl>

            <dl class="row">
                <dt class="col-sm-2">Recepciono Traslado:</dt>
                <dd class="col-sm-10">{{$traslado->usuarioRecibe?->name}}</dd>
            </dl>

            <dl class="row">
                <dt class="col-sm-2">Estado:</dt>
                <dd class="col-sm-10">
                    @switch($traslado->estado)
                    @case('generado')
                    <span class="badge text-bg-primary">{{ $traslado->estado }}</span>
                    @break

                    @case('traslado')
                    <span class="badge text-bg-info">{{ $traslado->estado }}</span>
                    @break

                    @case('recibido')
                    <span class="badge text-bg-success">{{ $traslado->estado }}</span>
                    @break

                    @case('cancelado')
                    <span class="badge text-bg-danger">{{ $traslado->estado }}</span>
                    @break
                    @endswitch
                </dd>
            </dl>
        </div>
        <div class="col-md-12">
            <div class="table-responsive mb-4">
                <table class="table table-sm table-striped table-bordered text-center" id="table-source">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Cantidad Enviada</th>
                            <th>Cantidad Recibida</th>
                            <th>Lote</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($traslado->trasladoDetalles as $detalle)
                        <tr id="row-{{ $traslado->id }}">
                            <td>{{ $detalle->id }}</td>
                            <td>{{ $detalle->codigo_producto }}</td>
                            <td>{{ $detalle->cantidad_traslado }}</td>
                            <td>{{ $detalle->cantidad_recepcion }}</td>
                            <td>{{ $detalle->lote }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($traslado->serviceLayer?->response)
        <div class="com-md-12">
            <div class="highlight">
                <pre class="language-html">
                        <code class="language-html">{{ trim(json_encode(json_decode($traslado->serviceLayer?->response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</code>
                    </pre>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
    console.info('traslado view');
</script>
@endsection