@extends('layouts.menu')

@section('title', 'Generar Código de Barras')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/Planecion.css') }}">
    <style>
        #barcode {
            text-align: center;
            margin-top: 20px;
        }
        #barcode canvas {
            max-width: 100%;
            margin: 0 auto;
            display: block;
        }
        .form-container {
            padding: 20px;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
        .barcode-input-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .barcode-input-group input {
            max-width: 80%;
            margin-right: 10px;
        }
        .barcode-input-group button {
            max-width: 18%;
        }
    </style>
@endsection

@section('content')
<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Generar Código de Barras</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Dashboard</a></li>
                            <li><a href="#">Código de Barras</a></li>
                            <li class="active">Generador de Código de Barras</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="card form-container">
        <div class="card-header text-center">
            <strong>Generar Código de Barras</strong>
        </div>
        <div class="card-body">
            <div class="barcode-input-group mb-3">
                <input type="text" id="barcodeInput" class="form-control" placeholder="Ingresa el valor del código" />
                <button onclick="generateBarcode()" class="btn btn-primary">Generar</button>
            </div>
            <div id="barcode">
                <!-- El código de barras generado se mostrará aquí -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function generateBarcode() {
        // Obtener el valor del campo de entrada
        const barcodeValue = document.getElementById('barcodeInput').value;

        // Validar si el campo no está vacío
        if (!barcodeValue) {
            alert('Por favor ingresa un valor válido para el código de barras.');
            return;
        }

        // Crear un canvas para generar el código de barras
        const canvas = document.createElement('canvas');
        
        // Usamos la librería JsBarcode para generar el código de barras
        JsBarcode(canvas, barcodeValue, {
            format: 'CODE128',  // Cambié el formato a CODE128, que es más flexible y soporta cualquier longitud
            lineColor: "#0aa", // Color de la línea del código de barras
            width: 4,          // Ancho de las barras
            height: 40,        // Altura del código de barras
            displayValue: true // Mostrar el valor del código de barras debajo
        });

        // Limpiar cualquier código de barras previo
        document.getElementById('barcode').innerHTML = ''; 

        // Mostrar el nuevo código de barras generado
        document.getElementById('barcode').appendChild(canvas);
    }
</script>

<!-- Asegúrate de incluir JsBarcode al final de tu página -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

@endsection
