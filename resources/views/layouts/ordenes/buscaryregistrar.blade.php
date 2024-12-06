@extends('layouts.menu')

@section('content')
<div class="container mt-0">
    <h2 class="text-center mb-4 text-primary fw-bold">Generación y Escaneo de Códigos de Barras</h2>

    <!-- Sección para Generar Código de Barras -->
    <div class="card shadow-lg rounded mb-2">
        <div class="card-body bg-light">
            <h4 class="card-title text-success fw-bold mb-4"><i class="bi bi-barcode"></i> Generar Código de Barras</h4>
            <div class="row g-3 align-items-center mb-4">
                <div class="col-md-4">
                    <label for="orderSale" class="form-label"><strong>Orden de Venta:</strong></label>
                    <input type="text" class="form-control form-control-lg border-primary" id="orderSale" placeholder="Orden de Venta">
                </div>
                <div class="col-md-4">
                    <label for="orderManufacture" class="form-label"><strong>Orden de Fabricación:</strong></label>
                    <input type="text" class="form-control form-control-lg border-primary" id="orderManufacture" placeholder="Orden de Fabricación">
                </div>

                <div class="col-md-4">
                    <button type="button" class="btn btn-primary mt-4" onclick="generateBarcode()">Generar Código de Barras</button>
                </div>
            </div>

            <div id="barcodeContainer" class="text-center mt-4"></div>
        </div>
    </div>

    <!-- Sección para Escanear Código de Barras -->
    <div class="card shadow-lg rounded mb-2">
        <div class="card-body bg-light">
            <h4 class="card-title text-success fw-bold mb-4"><i class="bi bi-barcode"></i> Escaneo o Ingreso de Código de Barras</h4>

            <div class="row g-3 align-items-center mb-4">
                <div class="col-md-4">
                    <label for="scanBarcode" class="form-label"><strong>Código de Barras:</strong></label>
                    <input type="text" class="form-control form-control-lg border-primary" id="scanBarcode" placeholder="Escanea o ingresa el código de barras" oninput="searchBarcode()">
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla para mostrar los datos -->
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped shadow-sm">
                    <thead class="bg-primary text-white text-center">
                        <tr>
                            <th>Orden de Venta</th>
                            <th>Orden de Fabricación</th>
                            <th>Artículo</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTable" class="text-center">
                        <!-- Aquí se mostrarán los registros escaneados -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para funciones -->
<script>
// Función para generar código de barras
function generateBarcode() {
    const orderSale = document.getElementById('orderSale').value;
    const orderManufacture = document.getElementById('orderManufacture').value;

    if (orderSale && orderManufacture) {
        const barcodeValue = orderSale + orderManufacture;

        // Generar el código de barras con una API gratuita
        const barcodeImage = `https://barcode.tec-it.com/barcode.ashx?data=${barcodeValue}&code=Code128&dpi=96`;

        document.getElementById('barcodeContainer').innerHTML = `
            <p><strong>Código de Barras Generado:</strong></p>
            <img src="${barcodeImage}" alt="Código de Barras" />
            <p><strong>Valor: </strong>${barcodeValue}</p>
        `;
    } else {
        alert('Por favor, ingrese tanto la Orden de Venta como la Orden de Fabricación.');
    }
}

function searchBarcode() {
    const barcode = document.getElementById("scanBarcode").value;
    if (!barcode) {
        document.getElementById("recordsTable").innerHTML = ""; 
        return;
    }
    fetch(`/buscar-orden/${barcode}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                const tableRow = `
                    <tr>
                        <td>${data.order_sale}</td>
                        <td>${data.order_manufacture}</td>
                        <td>${data.article}</td>
                        <td>${data.quantity}</td>
                    </tr>
                `;
                document.getElementById("recordsTable").innerHTML = tableRow;
            } else {
                document.getElementById("recordsTable").innerHTML = `
                    <tr>
                        <td colspan="4" class="text-danger">No se encontraron resultados.</td>
                    </tr>
                `;
            }
        })
       /* .catch(error => {
            console.error("Error al buscar el código de barras:", error);
            document.getElementById("recordsTable").innerHTML = `
                <tr>
                    <td colspan="4" class="text-danger">Error al realizar la búsqueda.</td>
                </tr>
            `;
        });*/
}
</script>
@endsection
