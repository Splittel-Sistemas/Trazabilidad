@extends('layouts.menu2')

@section('title', 'Traslados de Almacenes')

@section('content')
<a href="{{route('traslados')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-left" aria-hidden="true"></i> Atras</a>
<div class="mt-5 bg-white p-5">
    <h1 class="mb-4 text-center">Detalle Traslado</h1>

    <div class="row">
        <div class="col-md-12">
            <dl class="row" style="margin-bottom: .25rem;">
                <dt class="col-sm-2">Codigo:</dt>
                <dd class="col-sm-10">T{{str_pad($traslado->id, 6, "0", STR_PAD_LEFT)}}</dd>
            </dl>

            <dl class="row" style="margin-bottom: .25rem;">
                <dt class="col-sm-2">Genero:</dt>
                <dd class="col-sm-10">{{$traslado->usuarioTraslado->name}}</dd>
            </dl>

            <dl class="row" style="margin-bottom: .25rem;">
                <dt class="col-sm-2">Estado:</dt>
                <dd class="col-sm-10">
                    @switch($traslado->estado)
                    @case('Generado')
                    <span class="badge text-bg-primary">{{ $traslado->estado }}</span>
                    @break

                    @case('Parcial')
                    <span class="badge text-bg-warning">{{ $traslado->estado }}</span>
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

            <dl class="row" style="margin-bottom: .25rem;">
                <dt class="col-sm-2">Partidas Completas:</dt>
                <dd class="col-sm-10">
                    @php
                    $contador = 0;

                    foreach ($traslado->trasladoDetalles as $detalle) {
                    if ($detalle->quantity_transfer == $detalle->quantity_receive) {
                    $contador++;
                    }
                    }

                    echo $contador . ' / ' . count($traslado->trasladoDetalles);
                    @endphp
                </dd>
            </dl>
        </div>
        <div class="col-md-12">
            <div class="table-responsive mb-4">
                <table class="table table-sm table-striped table-bordered text-center" id="tablaPrincipal">
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
                            <th>Cantidad Enviada</th>
                            <th>Cantidad Recibida</th>
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
                            <td>{{ $detalle->quantity_receive }}</td>
                            <td>{{ $detalle->batchnum }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-12">
            <a href="{{route('traslados.showprint', $traslado->id)}}" class="btn btn-info" target="_blank">Imprimir</a>
            @if(count($traslado->serviceLayer) > 0)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                Ver Movimientos
            </button>

            <div class="modal fade" id="staticBackdrop" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Movimientos Realizados</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-bordered text-center">
                                    <tbody>
                                        @php
                                        $anterior = 0;
                                        @endphp
                                        @foreach ($traslado->serviceLayer as $indice => $service)
                                        @if($service->movimiento != $anterior)
                                        <tr class="table-primary">
                                            <th colspan="3">Movimiento: {{$service->movimiento}}</th>
                                            <th colspan="3">Realizado por: {{$service->usuarioRecibe->name}}</th>
                                            <th colspan="3">Fecha: {{$service->alta}}</th>
                                        </tr>
                                        <tr class="table-info">
                                            <th>Orden Venta</th>
                                            <th>Orden Fabricacion</th>
                                            <th>Recibo Producción</th>
                                            <th>Cod. Cliente</th>
                                            <th>Cliente</th>
                                            <th>Cod. Producto</th>
                                            <th>Producto</th>
                                            <th>Cantidad Recibida</th>
                                            <th>Lote</th>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>{{ $service->ov }}</td>
                                            <td>{{ $service->of }}</td>
                                            <td>{{ $service->rp }}</td>
                                            <td>{{ $service->cardcode }}</td>
                                            <td>{{ $service->cardname }}</td>
                                            <td>{{ $service->itemcode }}</td>
                                            <td>{{ $service->dscription }}</td>
                                            <td>{{ $service->quantity_transfer }}</td>
                                            <td>{{ $service->batchnum }}</td>
                                        </tr>
                                        @php
                                        $anterior = $service->movimiento;
                                        @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info" onclick="SeleccionarMovimiento()">Imprimir</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    window.addEventListener('DOMContentLoaded', function() {
        $('#tablaPrincipal').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
        });
    });

    async function SeleccionarMovimiento() {
        const {
            value: movimiento
        } = await Swal.fire({
            title: "Movimiento a imprimir",
            input: "select",
            inputOptions: @json(array_combine($movimientos, $movimientos)),
            inputPlaceholder: "Selecciona un movimiento",
            showCancelButton: true,
            inputValidator: (value) => {
                return new Promise((resolve) => {
                    if (value) {
                        resolve();
                    } else {
                        resolve("No se a seleccionado algun movimiento.");
                    }
                });
            }
        });
        if (movimiento) {
            window.open("/Traslados/print/{{$traslado->id}}/" + movimiento, "_blank");
        }
    }
</script>
@endsection