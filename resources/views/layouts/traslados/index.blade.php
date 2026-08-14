@extends('layouts.menu2')

@section('title', 'Traslados de Almacenes')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Traslados de Almacenes</h1>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive mb-4">
                <h4 class="text-center">Tabla de Traslados</h4>
                <table class="table table-striped table-bordered table-sm text-center" id="table-source">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Recibo Producción</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Alta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($traslados as $traslado)
                        <tr id="row-{{ $traslado->id }}">
                            <td>{{ $traslado->id }}</td>
                            <td>{{ $traslado->referencia_sap }}</td>
                            <td>
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
                            </td>
                            <td>{{ $traslado->usuarioTraslado->name }}</td>
                            <td>{{ $traslado->created_at }}</td>
                            <td>
                                <a href="{{ route('traslados.show', $traslado->id) }}" class="btn btn-sm btn-primary">Detalles</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    console.info('traslado view');
</script>
@endsection
