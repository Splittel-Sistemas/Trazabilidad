@extends('layouts.menu2')
@section('title', 'Dashboard')
@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .progress-box-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
            width: 100%; /* Ensures full width */
            max-width: 1200px; /* Increases max width */
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin: 0 auto; /* Centers the container */
        }
        .progress-bar-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .progress-bar-container span {
            font-size: 16px;
            width: 150px;
        }
        .progress-bar {
            width: 70%;  /* Maintains 70% of the container's width */
            height: 20px; /* Increases the height for better visibility */
            background-color: #ddd;
            border-radius: 5px;
            transition: width 0.5s ease;
        }
        .progress-bar-container:hover {
            transform: translateY(-3px);
            cursor: pointer;
        }
        .progress-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .progress-label {
            width: 100px; /* Ajusta el ancho según el espacio disponible */
            font-weight: bold;
            margin-right: 10px;
            text-align: right; /* Alinea el texto a la derecha */
        }
        .progress {
            flex-grow: 1;
            height: 22px;
            border-radius: 10px;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 90%;
            margin-left: 5%;
        }
        .Nav-fixed{
            position: fixed;
            top: 2rem;
            z-index: 1;
            width: 94%;
        }
        .Nav-Contend{
            position: relative;
            top: 10.5rem;
        }
    </style>
@endsection
@section('content')
        <div class="row gy-3 justify-content-between bg-soft " id="Nav-fixed">
            <div class="col-xxl-6">
                <h2 class="mb-3 text-1100">Dashboard</h2>
                <div class="row g-3 justify-content-between mb-4 ">
                    <div class="col-auto">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-phoenix-primary" onclick="DashboardPrincipal('D');"><i class="fas fa-calendar-day"></i> D&iacute;a</button>
                            <button class="btn btn-phoenix-primary" onclick="DashboardPrincipal('S');"><i class="fas fa-calendar-week"></i> Semana</button>
                            <button class="btn btn-phoenix-primary" onclick="DashboardPrincipal('M');"><i class="fas fa-calendar-alt"></i> Mes</button>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        </div>
        <div class="row gy-3 mb-4 justify-content-between" id="Nav-Contend">
            <h4 class="text-700 fw-semi-bold mb-2">Ordenes de Fabricaci&oacute;n</h4>
            <div class="col-xxl-12">
                 <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-md-4 col-xl-3 col-xxl-3">
                        <div class="card h-75">
                            <div class="card-body">
                                <div class="d-flex d-sm-block justify-content-between">
                                    <div class="border-bottom-sm mb-sm-2">
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex align-items-center icon-wrapper-sm shadow-primary-100" style="transform: rotate(-7.45deg);">
                                                <span class="far fas fa-calendar-check text-primary fs-1 z-index-1 ms-2"></span>
                                            </div>
                                            <p class="text-700 fs--1 mb-0 ms-2 mt-3">Ordene de Fabricaci&oacute;n</p>
                                        </div>
                                        <p class="text-success mt-2 fs-2 fw-bold mb-0 mb-sm-4" ><span id="OFCantidadAbierta">0</span><span class="fs-0 text-900 lh-lg"> Abiertas</span></p>
                                    </div>
                                    <!--<div class="d-flex flex-column justify-content-center flex-between-end d-sm-block text-end text-sm-start">
                                        <span class="badge badge-phoenix badge-phoenix-info fs--2 mb-2" >
                                            <span id="OFPorcentajeAbierta"></span>%
                                        </span>
                                        <span class="mb-0 fs--1 text-700">que el d&iacute;a de ayer</span>
                                    </div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl-3 col-xxl-2">
                        <div class="card h-75">
                            <div class="card-body">
                                <div class="d-flex d-sm-block justify-content-between">
                                    <div class="border-bottom-sm mb-sm-2">
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex align-items-center icon-wrapper-sm shadow-info-100" style="transform: rotate(-7.45deg);">
                                                <span class="fas fa-calendar-minus text-info fs-1 z-index-1 ms-2"></span>
                                            </div>
                                            <p class="text-700 fs--1 mb-0 ms-2 mt-3">Ordene de Fabricaci&oacute;n</p>
                                        </div>
                                        <p class="text-warning mt-2 fs-2 fw-bold mb-0 mb-sm-4"><span id="OFCantidadCerrada">0</span> <span class="fs-0 text-900 lh-lg">Cerradas</span></p>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center flex-between-end d-sm-block text-end text-sm-start"><span class="badge badge-phoenix badge-phoenix-info fs--2 mb-2"><span id="OFPorcentajeCerrada">0</span>%</span>
                                        <span id="OFCantidadCerrada_text" class="mb-0 fs--1 text-700"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl-6 col-xxl-4 gy-2 gy-md-1">
                        <div class="">
                            <h5 class="pb-4 border-bottom">Grafica Ordenes de Fabricaci&oacute;n</h5>
                            <div id="OFGraficoAbiertasCerradas" style="width: 100%; height: 200px;"></div>
                        </div>
                    </div>
                    <hr>
                    <h4 class="text-700 fw-semi-bold my-1">Estaci&oacute;nes</h4>
                    <div class="row gy-3 justify-content-between">
                        {{--<div class="col-xl-12 col-xxl-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="border-bottom">
                                        <h5 class="pb-4 border-bottom">Tiempo promedio por Pieza</h5>
                                        <p class="text-center">% de tiempo</p>
                                        <div id="OFGraficoEstacionesTiempos" style="width: 100%; height: 200px;"></div>
                                    </div>
                                    <div class="border-top">
                                        <div class="table-responsive scrollbar mx-n1 px-1">
                                            <table id="TableEstaciones" class="table table-sm  fs--1 leads-table" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Estación</th>
                                                        <th class="text-center">Programadas</th>
                                                        <th class="text-center">Pendientes</th>
                                                        <th class="text-center">En proceso</th>
                                                        <th class="text-center">Terminadas</th>
                                                        <th class="text-center">Promedio tiempo productivo(pieza)</th>
                                                        <th class="text-center">Promedio tiempo Muerto(pieza)</th>
                                                        <th class="text-center">Promedio tiempo Total(pieza)</th>
                                                        <th class="text-center">Promedio piezas/h</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list" id="BodyEstaciones">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>--}}
                        {{--<div class="col-xl-12 col-xxl-12">
                            <div class="border-top">
                                <div class="table-responsive scrollbar mx-n1 px-1">
                                    <table id="TableEstaciones" class="table table-sm  fs--1 leads-table" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Estación</th>
                                                <th class="text-center">Programadas</th>
                                                <th class="text-center">Pendientes</th>
                                                <th class="text-center">En proceso</th>
                                                <th class="text-center">Terminadas</th>
                                                <th class="text-center">Promedio por pieza(t)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list" id="BodyEstaciones">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>--}}
                    </div>
                    {{--<h4 class="text-700 fw-semi-bold my-1">L&iacute;neas</h4>
                    <div class="row gy-3 justify-content-between">
                    </div>--}}
                </div>
            </div>
        </div>
@endsection
@section('scripts')
<script>
    window.onload = function (){
        DashboardPrincipal('D');
    }
    window.addEventListener("scroll", function () {
        const cuadro = document.getElementById("Nav-fixed");
        const NavContend = document.getElementById("Nav-Contend");
        const scrollY = window.scrollY;
        if (scrollY > 92) {
            cuadro.classList.add("Nav-fixed");
            NavContend.classList.add("Nav-Contend");
        } else {
            cuadro.classList.remove("Nav-fixed");
            NavContend.classList.remove("Nav-Contend");
        }
    });
    //Dashboard/Principal
    function DashboardPrincipal(LapsoTiempo){
        OFCantidadAbierta = $('#OFCantidadAbierta');
        OFCantidadCerrada_text = $('#OFCantidadCerrada_text');
        OFCantidadCerrada_text.html('');
        //OFPorcentajeAbierta = $('#OFPorcentajeAbierta');
        OFCantidadCerrada = $('#OFCantidadCerrada'); 
        OFPorcentajeCerrada = $('#OFPorcentajeCerrada');
        //BodyEstaciones = document.getElementById('BodyEstaciones');
        OFGraficoAbiertasCerradas = document.getElementById('OFGraficoAbiertasCerradas');
        OFGraficoEstacionesTiempos = document.getElementById('OFGraficoEstacionesTiempos');
        OFCantidadAbierta.html('');
        //OFPorcentajeAbierta.html('');
        OFCantidadCerrada.html(''); 
        OFPorcentajeCerrada.html('');
        //BodyEstaciones.innerHTML = "";
        /*if (echarts.getInstanceByDom(OFGraficoAbiertasCerradas)) {
            echarts.dispose(OFGraficoAbiertasCerradas); // Destruye la instancia previa
        }
        if (echarts.getInstanceByDom(OFGraficoEstacionesTiempos)) {
            echarts.dispose(OFGraficoEstacionesTiempos); // Destruye la instancia previa
        }*/
        $.ajax({
            url: "{{route('DashboardPrincipal')}}", 
            type: 'POST',
            data: {
                LapsoTiempo:LapsoTiempo,
            },
            beforeSend: function() {
                //BodyEstaciones.innerHTML = "<tr><td colspan='100%' align='center'><div class='d-flex justify-content-center align-items-center'><div class='spinner-grow text-primary' role='status'><span class='visually-hidden'>Loading...</span></div></div></td></tr>";
            },
            success: function(response) {
                //BodyEstaciones.innerHTML = "";
                OFCantidadAbierta.html(response.OFAbiertaCant);
                if(LapsoTiempo === "D"){
                    OFCantidadCerrada_text.html('que el d&iacute;a de ayer');
                }else if(LapsoTiempo === "S"){
                    OFCantidadCerrada_text.html('que la semana pasada');
                }else{
                    OFCantidadCerrada_text.html('que el mes pasado');
                }
                //OFPorcentajeAbierta.html(response.PorcentajeAvanceA);
                OFCantidadCerrada.html(response.OFCerradaCant); 
                OFPorcentajeCerrada.html(response.PorcentajeAvanceC);
                var ChartOFGraficoAbiertasCerradas = echarts.init(OFGraficoAbiertasCerradas);
                var option = {
                    title: {
                        text: 'Ordenes Totales: '+(response.OFCerradaCant+response.OFAbiertaCant),
                        left: 'center',
                        top: 0,
                        textStyle: {
                            fontSize: 12,
                            fontWeight: 'bold'
                        }
                    },
                    color: ['#007bff', '#17a2b8'],
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c} ({d}%)'
                    },
                    legend: {
                        orient: 'vertical',
                        top: '1%',
                        left: 'left',
                        formatter: function (name) {
                            const series = option.series[0];
                            const item = series.data.find(i => i.name === name);
                            if (!item) return name;

                            const total = series.data.reduce((sum, i) => sum + i.value, 0);
                            if (total === 0) {
                                    return `${name}: 0%`;
                            }
                            const percent = ((item.value / total) * 100).toFixed(1);
                            return `${name}: ${percent}%`;
                        }
                    },
                    series: [
                        {
                            name: 'Información',
                            type: 'pie',
                            radius: ['40%', '70%'],
                            avoidLabelOverlap: false,
                            itemStyle: {
                            borderRadius: 1,
                            borderColor: '#fff',
                            borderWidth: 2
                            },
                            label: {
                            show: false,
                            position: 'center'
                            },
                            emphasis: {
                            label: {
                                show: true,
                                fontSize: 10,
                                fontWeight: 'bold'
                            }
                            },
                            labelLine: {
                            show: false
                            },
                            data: [
                                { value: response.OFAbiertaCant, name: 'Abiertas' },
                                { value: response.OFCerradaCant, name: 'Cerradas' },
                            ]
                        }
                    ]
                };
                ChartOFGraficoAbiertasCerradas.setOption(option);
                var DataSourceEstacionesTiempos = [
                    ['product', 'Tiempo Total', 'Tiempo Productivo', 'Tiempo Muerto']
                ];
                /*if ($.fn.DataTable.isDataTable('#TableEstaciones')) {
                    $('#TableEstaciones').DataTable().clear().destroy();
                }
                (response.Estaciones).forEach(element => {
                    if(element.id != 18){
                        DataSourceEstacionesTiempos.push([
                            element.nombre, // o como se llame el campo del nombre
                            //element.PorcentajeTiempoTotal, // duración de la orden
                            element.PorcentajeTiempoProductivo, // tiempo productivo
                            element.PorcentajeTiempoMuerto, // tiempo muerto
                            //element.TiempoTotal,
                            element.TiempoProductivo,
                            element.TiempoMuerto,
                        ]);
                    }
                    BodyEstaciones.innerHTML += `
                        <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                            <td class="fw-semi-bold text-1000 ps-0 py-0">
                                <a class="fw-bold text-primary" href="#!">`+element.nombre+`</a>
                            </td>
                            <td class="fw-semi-bold text-900 py-0 text-center">`+element.Programadas+`</td>
                            <td class="w-semi-bold text-900 py-0 text-center">`+element.Pendientes+`</td>
                            <td class="fw-semi-bold text-900 py-0 text-center">`+element.EnProceso+`</td>
                            <td class="fw-bold text-900 py-0 text-center">
                                <div class="d-flex align-items-center gap-3" title="`+element.PorcentajeTerminadas+`%">
                                    <div style="--phoenix-circle-progress-bar:`+element.PorcentajeTerminadas+`">
                                        <svg class="circle-progress-svg" width="40" height="40" viewBox="0 0 170 170">
                                            <circle class="progress-bar-rail" cx="60" cy="60" r="54" fill="none" stroke-linecap="round" stroke-width="14"></circle>
                                            <circle class="progress-bar-top" cx="60" cy="60" r="54" fill="none" stroke-linecap="round" stroke="#3874FF" stroke-width="14"></circle>
                                        </svg>
                                    </div>
                                    <h6 class="mb-0 text-900">`+element.Terminadas+`</h6>
                                </div>
                            </td>
                            <td class="fw-semi-bold text-900 py-0 text-center">`+element.TiempoPromedioPieza+`</td>
                            <td class="fw-semi-bold text-900 py-0 text-center">`+element.TiempoMuerto+`</td>
                             <td class="fw-semi-bold text-900 py-0 text-center">`+element.TiempoTotal+`</td>
                            <td class="fw-semi-bold text-900 py-0 text-center">`+element.CantidadPiezasHora+`</td>
                        </tr>`;
                });*/
                //var ChartOFGraficoEstacionesTiempos = echarts.init(OFGraficoEstacionesTiempos);
                OptionEstaciones = {
                    legend: {},
                    tooltip: {
                    },
                    dataset: {
                        source: DataSourceEstacionesTiempos
                    },
                    grid: {
                        left: '5%',
                        right: '5%',
                        bottom: 60,   // más espacio si se rotan etiquetas
                        top: '15%'
                    },
                    xAxis: {
                        type: 'category',
                        axisLabel: {
                            fontSize: 10,
                            rotate: 40,
                            overflow: 'truncate',
                            ellipsis: '...'
                        }
                    },
                    yAxis: {
                        axisLabel: {
                            fontSize: 10          // opcional, también reducir
                        }
                    },
                    series: [
                        //{ name: 'Total',type: 'bar', barWidth: '20%',stack: 'total', itemStyle: { color: '#1f77b4' } },  // azul fuerte
                        { name: 'Productivo',type: 'bar', barWidth: '20%',stack: 'total', itemStyle: { color: '#5DADE2' } },  // azul más claro
                        { name: 'Muerto',type: 'bar', barWidth: '20%',stack: 'total', itemStyle: { color: '#EC7063' } }   // azul muy claro (opcional)
                    ]
                };
                //ChartOFGraficoEstacionesTiempos.setOption(OptionEstaciones);
                /*$('#TableEstaciones').DataTable({
                    pageLength: 10,
                    lengthChange: false,
                    ordering: false,
                    searching: false,
                    language: {
                        info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                        infoEmpty: "Mostrando 0 a 0 de 0 entradas",
                        infoFiltered: "(filtrado de _MAX_ entradas totales)",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "Siguiente",
                            previous: "Anterior"
                        }
                    }
                });*/
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //$('#RetrabajoOFOpciones').html('');
            }
        });
    }
</script>
@endsection
