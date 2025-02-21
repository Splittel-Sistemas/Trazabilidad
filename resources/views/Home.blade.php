@extends('layouts.menu2')
@section('title', 'Dashboard')
@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dashboard Layout */
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            padding: 30px;
            justify-items: center;
        }

        /* Summary Section */
        .summary-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .summary-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            min-width: 250px;
            font-size: 1.3em;
            font-weight: bold;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        /* Hover effect for summary boxes */
        .summary-box:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        /* Icon Styling */
        .summary-box i {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        /* Order Status Styling */
        .closed-orders {
            border-top: 4px solid #28a745;
            color: #28a745;
        }

        .open-orders {
            border-top: 4px solid #dc3545;
            color: #dc3545;
        }

        /* Specific hover effects for each type of status */
        .closed-orders:hover {
            background-color: #218838;
            
            border-top: 4px solid #218838;
        }

        .open-orders:hover {
            background-color: #c82333;
          
            border-top: 4px solid #c82333;
        }

        /* Progress Box Container */
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

        /* Progress Bar Container */
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

        /* Progress Bar Styling */
        .progress-bar {
            width: 70%;  /* Maintains 70% of the container's width */
            height: 20px; /* Increases the height for better visibility */
            background-color: #ddd;
            border-radius: 5px;
            transition: width 0.5s ease;
        }

        /* Hover effect for progress bars */
        .progress-bar-container:hover {
            transform: translateY(-3px);
            cursor: pointer;
        }

        /* Chart Section */
        .chart-container {
            width: 100%;
            max-width: 800px;
           
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Hover effect for chart boxes */
        .chart-container:hover {
            transform: translateY(-12px);
            cursor: pointer;
        }

        /* Full width chart (Day Chart) */
        .chart-container.full-width {
            width: 100%;
            max-width: 100%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .summary-container {
                flex-direction: column;
                align-items: center;
            }

            .chart-container {
                width: 10%;
            }
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
        .progress-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        .fondo-rojo {
            background-color: red;
        }


        /*contendor de progreso */
      
       
        .grid-container {
            display: grid;
            grid-template-columns: repeat(4, 0fr);
            grid-template-rows: repeat(2, auto);
            gap: 30px;
            padding: 1px;
            max-width: 4500px;
            margin: auto;
        }
        .grid-item {
        border: 2px solid #ddd;  /* Borde alrededor del contenedor */
        padding: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;  /* Efecto suave en la transformación */
        border-radius: 10px;
        text-align: center;
        }

        .grid-item:hover {
        transform: translateY(-10px);  /* Movimiento hacia arriba cuando se pasa el cursor */
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);  /* Sombra para resaltar el contenedor */
        }

        .small-title {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .chart-container {
                width: 75vw;
                max-width:1400px;
                height: 400px;
                margin: 20px auto;
            }

            /*estilo de boton*/
            .btn-outline-info {
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease-in-out;
        }

        .btn-outline-info:hover {
            background-color: #17a2b8;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Acordeón con bordes suaves */
        .card {
            margin-bottom: 1rem;
        }

        .collapse {
            transition: all 0.3s ease;
        }

        .card-body {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Mejorar la apariencia del contenedor del gráfico */
        .chart-container {
           
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #chart-day, #chart-month, #chart-hour {
        width: 900px;
        height: 350px;
        }
    </style>
@endsection
@section('content')
    <hr style="border-top: 9px solid red; width: 100%; margin: 1px auto;">
    <div class="card text-center">
        <h1 class="progress-title mt-3 mb-4">Indicadores de Producción del Mes</h1>
        <div class="row justify-content-center">
            <!-- Órdenes Cerradas (Completadas) -->
            <div class="col-12 col-md-4 mb-4">
                <div class="d-flex align-items-center justify-content-center">
                    <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                        <i class="fas fa-check-circle" style="font-size: 30px; color: #28a745;"></i>
                    </span>
                    <div class="ms-3">
                        <h4 id="ordenesCompletadas" class="mb-0">Órdenes Cerradas</h4>
                        <p class="text-muted fs--1 mb-0">Órdenes fabricación del mes cerradas</p>
                    </div>
                </div>
            </div>

            <!-- Órdenes Abiertas (En Proceso) -->
            <div class="col-12 col-md-4 mb-4">
                <div class="d-flex align-items-center justify-content-center">
                    <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                        <i class="fas fa-sync-alt" style="font-size: 30px; color: #ffc107;"></i>
                    </span>
                    <div class="ms-3">
                        <h4 id="ordenesAbiertas" class="mb-0">Órdenes Abiertas</h4>
                        <p class="text-muted fs--1 mb-0">Órdenes fabricación del mes abiertas</p>
                    </div>
                </div>
            </div>

            <!-- Total de Órdenes -->
            <div class="col-12 col-md-4 mb-4">
                <div class="d-flex align-items-center justify-content-center">
                    <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                        <i class="fas fa-boxes" style="font-size: 30px; color: #007bff;"></i>
                    </span>
                    <div class="ms-3">
                        <h4 id="totalOrdenes" class="mb-0">Total De Órdenes</h4>
                        <p class="text-muted fs--1 mb-0">Total de todas las órdenes del mes</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="height: 10px;"></div>

        <div class="container">
            <div class="row mb-4">
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="grid-item">
                        <h1 class="small-title"></h1>
                        <canvas id="plemasCorte" width="150" height="150"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="grid-item">
                        <h1 class="small-title"></h1>
                        <canvas id="plemasSuministro" width="150" height="150"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="grid-item">
                        <h1 class="small-title"></h1>
                        <canvas id="plemasPreparado" width="150" height="150"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="grid-item">
                        <h1 class="small-title"></h1>
                        <canvas id="plemasEnsamble" width="150" height="150"></canvas>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="grid-item">
                        <h1 class="small-title"></h1>
                        <canvas id="plemasPulido" width="150" height="150"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="grid-item">
                        <h1 class="small-title"></h1>
                        <canvas id="plemasMedicion" width="150" height="150"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="grid-item">
                        <h1 class="small-title"></h1>
                        <canvas id="plemasVisualizacion" width="150" height="150"></canvas>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="grid-item">
                        <h1 class="small-title"></h1>
                        <canvas id="plemasEmpaque" width="150" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr style="border-top: 9px solid red; width: 100%; margin: 1px auto;">
    <div style="height: 10px;"></div>
    <div class="card">
        <h1 class="progress-title">Ordenes Fabricación</h1>
        <div class="grid-container" style="display: flex; justify-content: center;">
            <div class="grid-item">
                <h1 class="small-title"></h1>
                <canvas id="plemasordenes" width="700" height="300"></canvas>
            </div>     
        </div>
    </div>

    <hr style="border-top: 9px solid red; width: 100%; margin: 1px auto;">
    <div style="height: 10px;"></div>
    <div class="card">
        <div class="col-10 col-md-18 col-lg-12 mx-auto">
            <h1 class="progress-title">Progreso del Día</h1>
            <p id="chart-hour-fecha" style="font-size: 14px; color: gray;"></p> 
            <div id="chart-hour" class="chart-container"></div>
        </div>
    </div>

    <hr style="border-top: 9px solid red; width: 100%; margin: 1px auto;">
    <div style="height: 10px;"></div>
    <div class="card">
        <h1 class="progress-title">Progreso de la Semana</h1>
        <p id="chart-day-rango" style="font-size: 14px; color: gray;"></p>  
        <div id="chart-day" class="chart-container"></div>
    </div>

    <hr style="border-top: 9px solid red; width: 100%; margin: 1px auto;">
    <div style="height: 10px;"></div>
    <div class="card">
        <h1 class="progress-title">Progreso del Mes</h1>
        <p id="chart-month-mes" style="font-size: 14px; color: gray;"></p> <!-- Aquí se mostrará la fecha -->
        <div id="chart-month" class="chart-container"></div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

// Obtener progreso general del dashboard
function fetchProgresoDash() {
    fetch("{{ route('progreso.dash') }}")
        .then(response => response.json())
        .then(data => {
            console.log("Progreso General:", data.progreso);
            if (data && data.progreso) {
                updateProgressBars(data.progreso); // Actualizar las barras con los datos generales
            } else {
                console.error("No se recibieron datos de progreso válidos.");
            }
        })
        .catch(error => {
            console.error('Error obteniendo datos de progreso:', error);
        });
}
// Actualizar las barras de progreso del dashboard
function updateProgressBars(progreso) {
    Object.keys(progreso).forEach(area => {
        const porcentaje = progreso[area];
        let progressBarGeneral = document.getElementById(`progress-${area}`);
        if (progressBarGeneral) {
            animateProgressBar(progressBarGeneral, porcentaje);
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    fetch("{{ route('indicadores.CE') }}")
        .then(response => response.json())
        .then(data => {
            const estaciones = [
                "plemasCorte", "plemasSuministro", "plemasPreparado", "plemasEnsamble",
                "plemasPulido", "plemasMedicion", "plemasVisualizacion", "plemasEmpaque"
            ];

            estaciones.forEach(id => {
                let canvas = document.getElementById(id);
                if (!canvas) return;

                // Obtener el contenedor del canvas
                let container = canvas.parentElement;
                container.style.display = "flex";
                container.style.alignItems = "center";
                container.style.gap = "1px"; 

                let ctx = canvas.getContext("2d");
                canvas.style.width = "95px";
                canvas.style.height = "97px";

                let completado = data[id] ? data[id].completado : 0;
                let pendiente = data[id] ? data[id].pendiente : 0;
                let totalOrdenes = data[id] ? data[id].totalOrdenes : 0; 


                let porcentajeCompletado = totalOrdenes > 0 ? ((completado / totalOrdenes) * 100).toFixed(2) : 0;
                let porcentajePendiente = totalOrdenes > 0 ? (((totalOrdenes - completado) / totalOrdenes) * 100).toFixed(2) : 0;

                let infoDiv = container.querySelector(".info-grafico");
                if (!infoDiv) {
                    infoDiv = document.createElement("div");
                    infoDiv.classList.add("info-grafico");
                    container.appendChild(infoDiv);
                }

                infoDiv.innerHTML = `
                    <strong>${id.replace("plemas", "")}</strong><br>
                    Cerradas: <span style="color: #28a745;">${completado}/${totalOrdenes} (${porcentajeCompletado}%)</span><br>
                    Abiertas: <span style="color: #FFC107;">${totalOrdenes - completado}/${totalOrdenes} (${porcentajePendiente}%)</span>
                `;

                infoDiv.style.fontSize = "14px";

                new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: ["Cerradas", "Abiertas"],
                        datasets: [{
                            data: (totalOrdenes === completado) ? [completado, 0] : [completado, totalOrdenes - completado],
                            backgroundColor: (totalOrdenes === completado) ? ["#28a745"] : ["#28a745", "#FFC107"],
                            cutout: "70%"
                        }]
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                align: 'center'
                            },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false
                            }
                        },
                        elements: {
                            arc: {
                                borderWidth: 0
                            }
                        }
                    }
                });

            });
        })
        .catch(error => console.error('Error al cargar los datos:', error));    
    });
    fetch("{{ route('orden.cerredas') }}")
    .then(response => response.json())
    .then(data => {
        const id = "plemasordenes"; 
        let canvas = document.getElementById(id);
        if (!canvas) return;

        let container = canvas.parentElement;
        container.style.display = "flex";
        container.style.alignItems = "center";
        container.style.gap = "10px";

        let myChart = echarts.init(canvas);

        // Obtener datos del backend
        let completadas = data.ordenesCompletadas.length > 0 ? data.ordenesCompletadas.length : 0;
        let abiertas = data.ordenesAbiertas.length > 0 ? data.ordenesAbiertas.length : 0;
        let totalOrdenes = data.totalOrdenes > 0 ? data.totalOrdenes : (completadas + abiertas);

        // Calcular porcentajes
        let porcentajeCompletadas = totalOrdenes > 0 ? ((completadas / totalOrdenes) * 100).toFixed(2) : 0;
        let porcentajeAbiertas = totalOrdenes > 0 ? (((totalOrdenes - completadas) / totalOrdenes) * 100).toFixed(2) : 0;

        // Mostrar los datos en el indicador
        let infoDiv = container.querySelector(".info-grafico");
        if (!infoDiv) {
            infoDiv = document.createElement("div");
            infoDiv.classList.add("info-grafico");
            container.appendChild(infoDiv);
        }

        infoDiv.innerHTML = `
            <strong>Órdenes</strong><br>
            Cerradas: <span style="color: #28a745;">${completadas}/${totalOrdenes} (${porcentajeCompletadas}%)</span><br>
            Abiertas: <span style="color: #ffc107;">${totalOrdenes - completadas}/${totalOrdenes} (${porcentajeAbiertas}%)</span><br>
            Órdenes de Fabricacion: <span style="color: #dc3545; white-space: nowrap;">${totalOrdenes}</span>

        `;


        // Definir opciones del gráfico
        let option = {
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left'
            },
            series: [
                {
                    name: 'Órdenes',
                    type: 'pie',
                    radius: '80%',
                    data: [
                        { value: completadas, name: 'Cerradas', itemStyle: { color: '#28a745' } }, // Verde para Completadas
                        { value: abiertas, name: 'Abiertas', itemStyle: { color: '#ffc107' } },  // Amarillo para Abiertas
                        
                    ],
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        // Renderizar gráfico
        myChart.setOption(option);
    })
    .catch(error => console.error('Error al cargar los datos:', error));

function cargarOrdenesCerradas() {
    $.ajax({
        url: "{{ route('tabla.abiertas') }}",
        method: 'GET',
        success: function (data) {
            // Mostrar el porcentaje de órdenes cerradas
            $('#ordenFabricacionNumero').text(data.ordenesAbiertasCount); 

            // Llenar la tabla con los datos
            var tabla = $('#orden-list');
            tabla.empty(); 

            // Iterar sobre los datos y agregar filas a la tabla
            data.ordenes.forEach(function (orden) {
                var fila = `<tr>
                    <td>${orden.OrdenFabricacion}</td>
                    <td>${orden.Articulo}</td>
                    <td>${orden.Descripcion}</td>
                    <td>${orden.CantidadTotal}</td>
                    <td>${orden.SumaTotalcantidad_partida}</td>
                    
                </tr>`;
                tabla.append(fila);
            });
        },
        error: function (error) {
            console.log("Error al cargar los datos: ", error);
        }
    });
}

function cargarOrdenesCompletas() {
    $.ajax({
        url: "{{ route('tabla.completas') }}",
        method: 'GET',
        success: function (data) {
            // Mostrar el porcentaje de órdenes cerradas
            $('#ordenesCompletadasNumero').text(data.retrabajo); 

            // Llenar la tabla con los datos
            var tabla = $('#ordenes-completadas-list');
            tabla.empty(); 

            data.ordenes.forEach(function (orden) {
                const collapseId = `collapse-${orden.OrdenFabricacion}`;

                // Crear fila principal
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${orden.OrdenFabricacion}</td>
                    <td>${orden.Articulo}</td>
                    <td>${orden.Descripcion}</td>
                    <td>${orden.CantidadTotal}</td>
                    <td>${orden.cantidad_partida}</td>
                    <td>
                        <button class="btn btn-primary btn-sm btn-ver-mas" type="button"
                                data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                            Ver más
                        </button>
                    </td>
                `;

                // Crear fila de detalles con acordeón Bootstrap
                const detailRow = document.createElement('tr');
                detailRow.innerHTML = `
                    <td colspan="6">
                        <div class="collapse" id="${collapseId}">
                            <div class="card">
                                <div class="card-body">
                                    <strong>Tiempos de Etapas</strong><br>
                                    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                                        ${createEstacionCard('Corte', orden.TiempoCorte, orden.FinCorte)}
                                        ${createEstacionCard('Suministro', orden.TiempoSuministro, orden.FinSuministro)}
                                        ${createEstacionCard('Preparado', orden.TiempoPreparado, orden.FinPreparado)}
                                        ${createEstacionCard('Ensamble', orden.TiempoEnsamble, orden.FinEnsamble)}
                                        ${createEstacionCard('Pulido', orden.TiempoPulido, orden.FinPulido)}
                                        ${createEstacionCard('Medición', orden.TiempoMedicion, orden.FinMedicion)}
                                        ${createEstacionCard('Visualización', orden.TiempoVisualizacion, orden.FinVisualizacion)}
                                        ${createEstacionCard('Empaque', orden.TiempoAbierto, orden.FinAbierto)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                `;

                // Agregar filas a la tabla
                tabla.append(fila);
                tabla.append(detailRow);
            });
        },
        error: function (error) {
            console.log("Error al cargar los datos: ", error);
        }
    });
}

// Obtener progreso de órdenes de fabricación
function fetchProgresoOF() {
    fetch("{{ route('of.progreso') }}")
        .then(response => response.json())
        .then(data => {
            console.log("Progreso Órdenes de Fabricación:", data.progreso);
            if (data.progreso) {
                updateOFProgressBars(data.progreso); 
            } else {
                console.error("No se recibieron datos de progreso de orden de fabricación.");
            }
        })
        .catch(error => {
            console.error('Error obteniendo datos de progreso de orden de fabricación:', error);
        });
}

function updateOFProgressBars(progreso) {
    Object.keys(progreso).forEach(orden => {
        const progresoOrden = progreso[orden].detalle;
        Object.keys(progresoOrden).forEach(areaName => {
            const porcentaje = progresoOrden[areaName];
            let progressBarGeneral = document.getElementById(`step-${areaName}`);
            if (progressBarGeneral) {
                animateProgressBar(progressBarGeneral, porcentaje);
            }
        });
    });
}

function animateProgressBar(bar, porcentaje) {
    bar.style.width = `${porcentaje}%`;
    bar.innerHTML = `${porcentaje}%`;
}

// Animar las barras de progreso
function animateProgressBar(progressBar, percentage) {
    progressBar.style.width = `${percentage}%`;
    progressBar.setAttribute('aria-valuenow', percentage);
    progressBar.textContent = `${percentage}%`;
}


// Llamadas iniciales a las funciones de progreso
fetchProgresoDash(); 
fetchProgresoOF(); 
$(document).ready(function () {
    cargarOrdenesCerradas();
    cargarOrdenesCompletas();
});
function generarGrafico(url, containerId, itemName) {
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const datasetSource = [[itemName, ...data.labels]];

            data.series.forEach((serie) => {
                datasetSource.push([serie.name, ...serie.data]);
            });

            // Asignar valores a los elementos del HTML
            const fechaContainer = document.getElementById(`${containerId}-fecha`);
            const rangoContainer = document.getElementById(`${containerId}-rango`);
            const mesContainer = document.getElementById(`${containerId}-mes`);
            
            if(mesContainer){
                mesContainer.textContent = ` ${data.mes}`;
            }

            if (fechaContainer) {
                fechaContainer.textContent = ` ${data.fecha}`;
            }

            if (rangoContainer) {
                rangoContainer.textContent = ` ${data.rangoSemana}`;
            }
            const option = {
            tooltip: { trigger: 'axis' },
            legend: { left: '5%' },
            dataset: { source: datasetSource },
            xAxis: { type: 'category' },
            yAxis: { gridIndex: 0 },
            grid: {
                left: containerId === 'chart-month' ? '5%' : '50%',
                right: containerId === 'chart-month' ? '50%' : '5%',
                bottom: '10%',
                containLabel: true
            },
            series: data.series.map(() => ({
                type: 'line',
                smooth: true,
                seriesLayoutBy: 'row',
                emphasis: { focus: 'series' }
            })).concat([
                {
                    type: 'pie',
                    id: 'pie',
                    radius: '35%',
                    center: containerId === 'chart-month' ? ['75%', '50%'] : ['20%', '50%'],
                    emphasis: { focus: 'self' },
                    label: {
                        formatter: `{b}: {@[${data.labels[0]}]} ({d}%)`
                    },
                    encode: {
                        itemName: itemName,
                        value: data.labels[0],
                        tooltip: data.labels[0]
                    }
                }
            ]),
            toolbox: {
                feature: {
                    saveAsImage: {
                        name: `${data.fecha || ''}${data.rangoSemana || ''}${data.mes || ''}` // Verifica que los valores no sean undefined
                    }
                }
            }
        };

            const chart = echarts.init(document.getElementById(containerId));

            chart.on('updateAxisPointer', function (event) {
                const xAxisInfo = event.axesInfo[0];
                if (xAxisInfo) {
                    const dimension = xAxisInfo.value + 1;
                    chart.setOption({
                        series: [{
                            id: 'pie',
                            label: { formatter: `{b}: {@[${dimension}]} ({d}%)` },
                            encode: { value: dimension, tooltip: dimension }
                        }]
                    });
                }
            });

            chart.setOption(option);
        })
        .catch(error => {
            console.error(`Error al cargar los datos del gráfico (${itemName}):`, error);
        });
}
// Llamadas a la función para generar gráficos
generarGrafico("{{ route('tablas.semana') }}", "chart-day", "Día");
generarGrafico("{{ route('tablas.mes') }}", "chart-month", "Semana");
generarGrafico("{{ route('tablas.hora') }}", "chart-hour", "Mes");

$(document).ready(function() {
        $.ajax({
            url: "{{ route('wizarp.dashboard') }}", 
            type: 'GET',
            dataType: 'json',
            success: function(data) {

                $('#ordenesCompletadas').text('Órdenes Cerradas: ' + data.ordenesCompletadas);
                $('#ordenesAbiertas').text('Órdenes Abiertas: ' + data.ordenesAbiertas);
                $('#totalOrdenes').text('Total de Órdenes: ' + data.totalOrdenes);
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
            }
        });
    });

</script>
@endsection