@extends('layouts.menu2')

@section('title', 'Traslados de Almacenes')

@section('content')
<a href="{{route('traslados')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-left" aria-hidden="true"></i> Atras</a>
<div class="container mt-5">
    <h1 class="mb-4 text-center">Detalle Traslado</h1>

    <div class="row">
        <div class="col-md-12">
            <dl class="row">
                <dt class="col-sm-2">Codigo:</dt>
                <dd class="col-sm-10">T{{str_pad($traslado->id, 6, "0", STR_PAD_LEFT)}}</dd>
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
                    @case('Generado')
                    <span class="badge text-bg-primary">{{ $traslado->estado }}</span>
                    @break

                    @case('Parcial')
                    <span class="badge text-bg-info">{{ $traslado->estado }}</span>
                    @break

                    @case('Recibido')
                    <span class="badge text-bg-success">{{ $traslado->estado }}</span>
                    @break

                    @case('Cancelado')
                    <span class="badge text-bg-danger">{{ $traslado->estado }}</span>
                    @break
                    @endswitch
                </dd>
            </dl>
        </div>
        <div class="col-md-12">
            <div class="table-responsive mb-4">
                <table class="table table-sm table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Documento</th>
                            <th>Linea</th>
                            <th>Producto</th>
                            <th>Cantidad Enviada</th>
                            <th>Cantidad Recibida</th>
                            <th>Lote</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($traslado->trasladoDetalles as $indice => $detalle)
                        <tr>
                            <td>{{ $indice + 1 }}</td>
                            <td>{{ $detalle->docnum }}</td>
                            <td>{{ $detalle->linenum }}</td>
                            <td>{{ $detalle->itemcode }}</td>
                            <td>{{ $detalle->quantity_transfer }}</td>
                            <td>{{ $detalle->quantity_receive }}</td>
                            <td>{{ $detalle->batchnum }}</td>
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
    console.info('traslado show');

    window.addEventListener('DOMContentLoaded', function () {
        $('.table').DataTable();
    });
</script>
@endsection