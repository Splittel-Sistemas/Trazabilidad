@extends('layouts.menu2')

@section('title', 'Traslados de Almacenes')

@section('content')
<a href="{{route('traslados')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-left" aria-hidden="true"></i> Atras</a>
<div class="container mt-5">
    <h1 class="mb-4 text-center">Recibos de Producción Sin Traslado</h1>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive mb-4">
                <table class="table table-sm table-striped table-bordered text-center" id="tableContenido">
                    <thead>
                        <tr>
                            <th><input type="checkbox" onchange="SeleccionarTodo()" id="btnSelecionarTodo"> Check</th>
                            <th>Documento</th>
                            <th>Linea</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Lote</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{$anterior = '';}}
                        @foreach ($recibosProduccionPorTrasladar as $docnum => $detalle)
                        @foreach ($detalle as $item)
                        <tr>
                            @if($docnum != $anterior)
                            <td><input type="checkbox" name="docnum" value="{{$docnum}}"></td>
                            @else
                            <td></td>
                            @endif
                            <td>{{ $item['DocNum']}}</td>
                            <td>{{ $item['LineNum']}}</td>
                            <td>{{ $item['ItemCode']}}</td>
                            <td>{{ $item['Quantity']}}</td>
                            <td>{{ $item['BatchNum']}}</td>
                        </tr>
                        @endforeach
                        @endforeach

                        @if(count($recibosProduccionPorTrasladar) == 0)
                        <tr>
                            <td colspan="6">Sin datos que mostrar</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <button class="btn btn-success" onclick="GenerarTraslado()" id="btnGenerarTraslado">Generar Traslado</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    console.info('traslado create');

    const btnSelecionarTodo = $('#btnSelecionarTodo');
    const tableContenido = $('#tableContenido tbody');
    const btnGenerarTraslado = $('#btnGenerarTraslado');

    function SeleccionarTodo() {
        if (btnSelecionarTodo.is(':checked')) {
            tableContenido.find('tr').each(function(indiceTabla, fila) {
                let primerColumna = $(fila).find("td:first");
                let checkbox = primerColumna.find("input[type='checkbox']");
                if (checkbox.length > 0) {
                    checkbox.prop('checked', true);
                }
            });
        } else {
            tableContenido.find('tr').each(function(indiceTabla, fila) {
                let primerColumna = $(fila).find("td:first");
                let checkbox = primerColumna.find("input[type='checkbox']");
                if (checkbox.length > 0) {
                    checkbox.prop('checked', false);
                }
            });
        }
    }

    function GenerarTraslado() {
        btnGenerarTraslado.prop('disabled', true);
        let arrayDocNum = [];
        Swal.fire({
            title: "¿Generar Traslado?",
            text: "Esta seguro de generar el traslado, solo se tomaran en cuenta los documentos con el check activo (Tambien se contemplan las lineas con el mismo número de documento)",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#30d683",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si",
            cancelButtonText: "No"
        }).then((result) => {
            if (result.isConfirmed) {
                let numeroDocumentosCheck = $("input[name='docnum']:checked");
                if (numeroDocumentosCheck.length > 0) {
                    numeroDocumentosCheck.each(function(indice, elemento) {
                        arrayDocNum.push(elemento.value);
                    });

                    $.ajax({
                        url: '/Traslados/generate',
                        method: 'POST',
                        data: {
                            'listaDocumentos': arrayDocNum
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 'success') {
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                                Swal.fire({
                                    title: "success",
                                    text: "Traslado creado exitosamente.",
                                    icon: "success"
                                });
                            } else {
                                Swal.fire({
                                    title: "warning",
                                    text: "Ocurrio un error la generacion del traslado.",
                                    icon: "warning"
                                });
                                btnGenerarTraslado.prop('disabled', false);
                            }
                        },
                        error: function(error) {
                            Swal.fire({
                                title: "Error",
                                text: "Ocurrio un error en la cosulta AJAX, por favor copntecte con TI.",
                                icon: "error"
                            });
                            btnGenerarTraslado.prop('disabled', false);
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Advertencia",
                        text: "No se puede crear un traslado vacio.",
                        icon: "warning"
                    });
                    btnGenerarTraslado.prop('disabled', false);
                }
            } else {
                btnGenerarTraslado.prop('disabled', false);
            }
        });
    }
</script>
@endsection