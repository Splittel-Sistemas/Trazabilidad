@extends('layouts.menu')

@section('title', 'Dashboard')

@section('styles')
<!-- Aquí puedes incluir estilos adicionales si son necesarios -->
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/panelprincipal.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <h1 class="my-4">Dashboard</h1>

    <!-- Row for summary cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="" class="text-decoration-none">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Fibra Producida</div>
                    <div class="card-body">
                        <h5 class="card-title"></h5>
                        <p class="card-text">Producción del mes</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="" class="text-decoration-none">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Pedidos Entregados</div>
                    <div class="card-body">
                        <h5 class="card-title"></h5>
                        <p class="card-text">Pedidos completados este mes</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="" class="text-decoration-none">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Alertas de Calidad</div>
                    <div class="card-body">
                        <h5 class="card-title">3</h5>
                        <p class="card-text">Defectos detectados en lotes recientes</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Monthly production and orders charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Producción Mensual de Fibra</div>
                <div class="card-body">
                    <canvas id="produccionMensualChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Pedidos por Estado</div>
                <div class="card-body">
                    <canvas id="pedidosEstadoChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Last orders table -->
    <div class="card mb-4">
        <div class="card-header">Últimos Pedidos</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th># Pedido</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí deberías insertar datos reales -->
                    <tr>
                        <td>1001</td>
                        <td>Telecom S.A.</td>
                        <td>Fibra G.652D</td>
                        <td>15/11/2024</td>
                        <td><span class="badge badge-success">Entregado</span></td>
                        <td><a href="#" class="btn btn-sm btn-primary">Ver</a></td>
                    </tr>
                    <tr>
                        <td>1002</td>
                        <td>Redes Globales</td>
                        <td>Conector LC</td>
                        <td>16/11/2024</td>
                        <td><span class="badge badge-warning">En Proceso</span></td>
                        <td><a href="#" class="btn btn-sm btn-primary">Ver</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
