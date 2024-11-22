@extends('layouts.menu')

@section('content')
<div class="container mt-0">
    
    <h2 class="text-center mb-4 text-primary fw-bold">Gestión De Produccion</h2>

    <div class="card shadow-lg rounded mb-2">
        <div class="card-body bg-light">
            <h4 class="card-title text-success fw-bold mb-4"><i class="bi bi-barcode"></i> Escaneo y Registro</h4>

            <div class="row g-3 align-items-center mb-4">
                <div class="col-md-4">
                    <label for="searchBarcode" class="form-label"><strong>Código de Barras:</strong></label>
                    <input type="text" class="form-control form-control-lg border-primary" id="searchBarcode" placeholder="Escanea o ingresa el código de barras" oninput="searchBarcode()">
                </div>

                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check form-switch ms-3">
                        <input class="form-check-input" type="checkbox" id="barcodeSwitch" onclick="toggleBarcodeScanner()">
                        <label class="form-check-label text-success fw-bold" for="barcodeSwitch"><i class="bi bi-play-circle-fill"></i> Activar Escáner</label>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-1">
                <div class="col-md-2">
                    <label for="quantity" class="form-label"><strong>Cantidad:</strong></label>
                    <input type="number" class="form-control form-control-lg border-info" id="quantity" value="1" min="1">
                </div>

                <div class="col-md-2">
                    <label for="stock" class="form-label"><strong>Stock:</strong></label>
                    <input type="number" class="form-control form-control-lg border-info" id="stock" value="0" disabled>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped shadow-sm">
                    <thead class="bg-primary text-white text-center">
                        <tr>
                            <th>#</th>
                            <th>Artículo</th>
                            <th>Cantidad</th>
                            <th>Precio Venta</th>
                            <th>Sub Total</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTable" class="text-center">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-md-6">
            <div class="card shadow-lg rounded mb-4">
                <div class="card-body bg-light">
                    <h4 class="card-title text-primary fw-bold"><i class="bi bi-receipt"></i> Datos de Venta</h4>
                    <div class="form-group">
                        <label for="client" class="form-label"><strong>Cliente:</strong></label>
                        <div class="col">
                        <input type="text" class="form-control form-control-lg border-primary" id="client" placeholder="Nombre del cliente">
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label for="folio" class="form-label"><strong>Folio:</strong></label>
                        <div class="col">
                        <input type="text" class="form-control form-control-lg border-primary" id="folio" placeholder="Número de folio">
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-lg rounded mb-4">
                <div class="card-body">
                    <h4 class="card-title fw-bold"><i class="bi bi-box"></i> Estadísticas de Piezas</h4>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="alert alert-info shadow-sm text-center" role="alert">
                                <h4><i class="bi bi-box"></i> Total: <span id="totalPieces">0</span></h4>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="alert alert-danger shadow-sm text-center" role="alert">
                                <h4><i class="bi bi-exclamation-circle"></i> Faltantes: <span id="missingPieces">0</span></h4>
                            </div>
                        </div>
                    </div>
                        <button type="button" class="btn btn-outline-success">Aceptar</button>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endsection
