@extends('layouts.menu2')

@section('title', 'Busquedas')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--estilos-->
    <style>
        /*estilo de boton detalles*/
    </style>
@endsection

@section('content')
    <!-- Breadcrumbs -->
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Busquedas</h4>
        </div>
    </div>
    <div class="container my-4">
        <div class="echart-gauge-progress-chart-example" style="min-height:300px"></div>
    </div>
@endsection

@section('scripts')
    <!-- Scripts -->
    <script src="/js/charts/echarts/examples/gauge-progress-chart.js"></script>
    <script src="../../vendors/popper/popper.min.js"></script>
    <script src="../../vendors/bootstrap/bootstrap.min.js"></script>
    <script src="../../vendors/anchorjs/anchor.min.js"></script>
    <script src="../../vendors/is/is.min.js"></script>
    <script src="../../vendors/fontawesome/all.min.js"></script>
    <script src="../../vendors/lodash/lodash.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="../../vendors/list.js/list.min.js"></script>
    <script src="../../vendors/feather-icons/feather.min.js"></script>
    <script src="../../vendors/dayjs/dayjs.min.js"></script>
    <script src="../../vendors/echarts/echarts.min.js"></script>
    <script src="../../vendors/prism/prism.js"></script>
    <script src="../../assets/js/phoenix.js"></script>
    <script src="../../assets/js/echarts-example.js"></script>
@endsection
