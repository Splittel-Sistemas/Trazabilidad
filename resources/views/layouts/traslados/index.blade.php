@extends('layouts.menu2')

@section('title', 'Traslados de Almacenes')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Traslados de Almacenes</h1>

    <div class="row">
        <div class="col-md-12 mb-3">
            <a href="{{ route('traslados.create') }}" class="btn btn-sm btn-primary">Crear</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive mb-4">
                <h4 class="text-center">Tabla de Traslados</h4>
                <table class="table table-striped table-bordered table-sm text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Codigo</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Alta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($traslados as $indice => $traslado)
                        <tr>
                            <td>{{ $indice + 1 }}</td>
                            <td>T{{ str_pad($traslado->id, 6, "0", STR_PAD_LEFT) }}</td>
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

                        @if(count($traslados) == 0)
                        <tr>
                            <td colspan="6">Sin datos que mostrar</td>
                        </tr>
                        @endif
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

    window.addEventListener('DOMContentLoaded', function () {
        $('.table').DataTable();
    });
</script>
@endsection
