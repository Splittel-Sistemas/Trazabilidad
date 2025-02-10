@extends('layouts.menu2')

@section('title', 'Dashboard')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

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
            color: white;
            border-top: 4px solid #218838;
        }

        .open-orders:hover {
            background-color: #c82333;
            color: white;
            border-top: 4px solid #c82333;
        }

        /* Progress Box Container */
        .progress-box-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        #progress-2 {
            background-color: #28a745;
        }

        #progress-3 {
            background-color: #007bff;
        }

        #progress-4 {
            background-color: #ff9800;
        }

        #progress-5 {
            background-color: #dc3545;
        }

        #progress-6 {
            background-color: #9c27b0;
        }

        #progress-7 {
            background-color: #3f51b5;
        }

        #progress-8 {
            background-color: #009688;
        }

        #progress-9 {
            background-color: #ff5722;
        }

        #step-Corte {
            background-color: #28a745;
        }

        #step-Suministro {
            background-color: #007bff;
        }

        #step-Preparado {
            background-color: #ff9800;
        }

        #step-Ensamble {
            background-color: #dc3545;
        }

        #step-Pulido {
            background-color: #9c27b0;
        }

        #step-Medicion{
            background-color: #3f51b5;
        }

        #step-Visualizacion {
            background-color: #009688;
        }

        #step-Abierto{
            background-color: #ff5722;
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
            background: white;
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
                width: 100%;
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



        
        


    </style>
@endsection

@section('content')
    <div class="summary-container">
        <div class="summary-box closed-orders">
            <i class="fas fa-check-circle"></i>
            <h3>Órdenes Fab. Cerradas</h3>
            <p id="closedOrders">0%</p>
        </div>
        <div class="summary-box open-orders">
            <i class="fas fa-exclamation-circle"></i>
            <h3>Órdenes Fab. Abiertas</h3>
            <p id="openOrders">0%</p>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="chart-container">
            <canvas id="monthlyChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="chart-container full-width">
            <canvas id="dayChart"></canvas>
        </div>
    </div>

    <!-- Contenedor de las barras de progreso -->
    <div class="progress-box"> 
        <h2 class="progress-title">Progreso De Producción</h2>
        <div class="progress-box-container">
            <div class="progress-item">
                <span class="progress-label">Cortes</span>
                <div class="progress">
                    <div id="progress-2" class="progress-bar text-white fw-bold progress-animated"
                        role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Suministro</span>
                <div class="progress">
                    <div id="progress-3" class="progress-bar text-white fw-bold progress-animated"
                        role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Preparado</span>
                <div class="progress">
                    <div id="progress-4" class="progress-bar text-white fw-bold progress-animated"
                        role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Ensamble</span>
                <div class="progress">
                    <div id="progress-5" class="progress-bar text-white fw-bold progress-animated"
                        role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Pulido</span>
                <div class="progress">
                    <div id="progress-6" class="progress-bar text-white fw-bold progress-animated"
                        role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Medicion</span>
                <div class="progress">
                    <div id="progress-7" class="progress-bar text-white fw-bold progress-animated"
                        role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Visualizacion</span>
                <div class="progress">
                    <div id="progress-8" class="progress-bar text-white fw-bold progress-animated"
                        role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Empaque</span>
                <div class="progress">
                    <div id="progress-9" class="progress-bar text-white fw-bold progress-animated"
                        role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
            </div>
        </div>   
    </div>
    <hr>
    
    <div class="progress-box">
        <h2 class="progress-title">Progreso De Producción Por Orden Fabricacion</h2>
        <div class="progress-box-container">
            <div class="progress-item">
                <span class="progress-label">Cortes</span>
                <div class="progress">
                    <div id="step-Corte" class="progress-bar text-white fw-bold progress-animated"
                            role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;">
                        0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Suministro</span>
                <div class="progress">
                    <div id="step-Suministro"  class="progress-bar text-white fw-bold progress-animated"
                    role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;">
                    0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Preparado</span>
                <div class="progress">
                    <div id="step-Preparado" class="progress-bar text-white fw-bold progress-animated"
                    role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;">
                        0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Ensamble</span>
                <div class="progress">
                    <div id="step-Ensamble"  class="progress-bar text-white fw-bold progress-animated"
                    role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;">
                    0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Pulido</span>
                <div class="progress">
                    <div id="step-Pulido"  class="progress-bar text-white fw-bold progress-animated"
                    role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;">
                    0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Medición</span>
                <div class="progress">
                    <div id="step-Medicion"  class="progress-bar text-white fw-bold progress-animated"
                    role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;">
                    0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Visualización</span>
                <div class="progress">
                    <div id="step-Visualizacion" class="progress-bar text-white fw-bold progress-animated"
                    role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;">
                    0%
                    </div>
                </div>
            </div>
    
            <div class="progress-item">
                <span class="progress-label">Empaque</span>
                <div class="progress">
                    <div id="step-Abierto"  class="progress-bar text-white fw-bold progress-animated"
                    role="progressbar" style="width: 0%; transition: width 0.5s ease-in-out; font-size: 14px;">
                    0%
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--modal cerradas-->
    <div class="modal fade" id="example2Modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-info" id="exampleModalLabel">
                        Órdenes Cerradas:
                        <span id="ordenFabricacionNumero" class="ms-3 text-muted"></span>
                    </h5>
                    <button type="button" class="btn p-1" data-bs-dismiss="modal" aria-label="Close">
                        <svg class="svg-inline--fa fa-xmark fs--1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                            <path fill="currentColor" d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"></path>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="retrabajo1" class="mb-3"></div>
                    <table class="table table-striped table-sm fs--1 mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="sort border-top">Orden Fabricación</th>
                                <th class="sort border-top ps-3">Artículo</th>
                                <th class="sort border-top">Descripción</th>
                                <th class="sort border-top">Cantidad Total</th>
                                <th class="sort border-top">Cortes</th>
                               
                                <th class="border-top">Detalles</th>
                            </tr>
                        </thead>
                        <tbody id="orden-list"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>    

    <div class="modal fade" id="example3Modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-info" id="example3ModalLabel">
                        Órdenes Abiertas:
                        
                    </h5>
                    
                    <button type="button" class="btn p-1" data-bs-dismiss="modal" aria-label="Close">
                        <svg class="svg-inline--fa fa-xmark fs--1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
                            <path fill="currentColor" d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"></path>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="retrabajo" class="mb-3"></div> <!-- Aquí se mostrará el porcentaje de retrabajo -->
                    <table class="table table-striped table-sm fs--1 mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="sort border-top" data-sort="Orden">Orden Fabricacion</th>
                                <th class="sort border-top ps-3" data-sort="Articulo">Articulo</th>
                                <th class="sort border-top" data-sort="Descripcion">Descripcion</th>
                                <th class="sort border-top" data-sort="CantidadTotal">Cantidad Total</th>
                                <th class="sort border-top" data-sort="Partidas">Cortes</th>
                                
                            </tr>
                        </thead>
                        <tbody id="modalContent"></tbody>
                    </table>
                </div>
                
                <div class="modal-footer">
                    <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    

@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", async function () {
            await fetchOrders();
            await loadCharts();
        });

        async function fetchOrders() {
            try {
                // Obtener órdenes cerradas
                const closedResponse = await fetch("{{ route('ordenes.cerredas') }}");
                const closedData = await closedResponse.json();
                console.log("Órdenes Cerradas:", closedData); 
                const closedValue = closedData.retrabajo ?? "0/0"; // Manejo de casos donde no haya datos
                document.getElementById('closedOrders').innerText = closedValue;


                // Obtener órdenes abiertas
                const openResponse = await fetch("{{ route('ordenes.abiertas') }}");
                const openData = await openResponse.json();
                console.log("Órdenes Abiertas:", openData);

                // Usamos el total de órdenes y las órdenes abiertas para mostrar el formato 1/10
                const totalOrdenes = openData.totalOrdenes ?? 0;
                const ordenesAbiertasCount = openData.ordenesAbiertasCount ?? 0;
                document.getElementById('openOrders').innerText = `${ordenesAbiertasCount}/${totalOrdenes}`;


            } catch (error) {
                console.error('Error obteniendo los datos de órdenes:', error);
                document.getElementById('closedOrders').innerText = "0%";
                document.getElementById('openOrders').innerText = "0%";
            }
        }

        //modal abiertas
        document.querySelector('.summary-box.open-orders').addEventListener('click', async () => {
            const modal = new bootstrap.Modal(document.getElementById('example3Modal'));  
            modal.show();

            try {
                const response = await fetch("{{ route('ordenes.abiertas') }}");
                const data = await response.json();

                console.log("Órdenes abiertas:", data);

                const tableBody = document.getElementById('modalContent');
                tableBody.innerHTML = '';

                data.ordenes.forEach(orden => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${orden.OrdenFabricacion}</td>
                        <td>${orden.Articulo}</td>
                        <td>${orden.Descripcion}</td>
                        <td>${orden.CantidadTotal}</td>
                        <td>${orden.cantidad_partida}</td>
                        
                    `;
                    tableBody.appendChild(row);
                });

                // Mostrar porcentaje de retrabajo si el elemento existe
                const retrabajoElement = document.getElementById('retrabajo');
                if (retrabajoElement) {
                    retrabajoElement.textContent = `Porcentaje de retrabajo: ${data.retrabajo}%`;
                }
            } catch (error) {
                console.error('Error al obtener las órdenes abiertas:', error);
            }
        });


        //modal cerradas
        document.querySelector('.summary-box.closed-orders').addEventListener('click', async () => {
            const modal = new bootstrap.Modal(document.getElementById('example2Modal'));  
            modal.show();  

            try {
                const response = await fetch("{{ route('ordenes.cerredas') }}");
                const data = await response.json();

                console.log("Órdenes cerradas:", data);

                const tableBody = document.getElementById('orden-list');
                tableBody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevas órdenes

                data.ordenes.forEach((orden, index) => {
                const collapseId = `collapseOrden${index}`;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${orden.OrdenFabricacion}</td>
                    <td>${orden.Articulo}</td>
                    <td>${orden.Descripcion}</td>
                    <td>${orden.CantidadTotal}</td>
                    <td>${orden.cantidad_partida}</td>
                    
                    <td>
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                            Ver más
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);

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
                tableBody.appendChild(detailRow);
            });


                const retrabajoElement = document.getElementById('retrabajo1');
                if (retrabajoElement) {
                    retrabajoElement.textContent = `Porcentaje de retrabajo: ${data.retrabajo}%`;
                }

            } catch (error) {
                console.error('Error al obtener las órdenes:', error);
            }
        });

        // Función para crear las tarjetas de las estaciones de trabajo
        function createEstacionCard(estacion, tiempoInicio, tiempoFin) {
            return `
                <div style="flex: 1 1 45%; min-width: 200px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.3s ease-in-out;">
                    <strong>Estación de ${estacion}:</strong>
                    <div style="display: flex; gap: 10px;">
                        <span style="color: green; font-weight: bold;">Inicio: ${tiempoInicio ?? 'N/A'}</span> 
                        <span style="color: red; font-weight: bold;">Fin: ${tiempoFin ?? 'N/A'}</span>
                    </div>
                </div>
            `;
        }
        async function loadCharts() 
        {
            try {
                const response = await fetch("{{ route('graficas.dashboard') }}");
                const data = await response.json();

                // Datos por día
                const dias = data.ordenesPorDia.map(item => item.dia);
                const totalesDia = data.ordenesPorDia.map(item => item.total);

                // Datos por semana
                const semanas = data.ordenesPorSemana.map(item => item.semana);
                const totalesSemana = data.ordenesPorSemana.map(item => item.total);

                // Datos por mes
                const meses = data.ordenesPorMes.map(item => item.mes);
                const totalesMes = data.ordenesPorMes.map(item => item.total);

                // Gráfico de órdenes por día
                new Chart(document.getElementById("dayChart"), {
                    type: 'line',
                    data: {
                        labels: dias,
                        datasets: [{
                            label: 'Órdenes por Día',
                            data: totalesDia,
                            borderColor: 'rgba(75, 192, 192, 0.8)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                // Gráfico de órdenes por semana
                new Chart(document.getElementById("weeklyChart"), {
                    type: 'bar',
                    data: {
                        labels: semanas,
                        datasets: [{
                            label: 'Órdenes por Semana',
                            data: totalesSemana,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 0.8)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                // Gráfico de órdenes por mes
                new Chart(document.getElementById("monthlyChart"), {
                    type: 'line',
                    data: {
                        labels: meses,
                        datasets: [{
                            label: 'Órdenes por Mes',
                            data: totalesMes,
                            borderColor: 'rgba(255, 99, 132, 0.8)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            } catch (error) {
                console.error("Error obteniendo los datos de los gráficos:", error);
            }
        }
        window.onload = function() {
            fetchProgresoOF();
    fetchProgresoDash();
   
};


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


// Actualizar las barras de progreso por orden de fabricación


// Animar las barras de progreso
function animateProgressBar(progressBar, percentage) {
    progressBar.style.width = `${percentage}%`;
    progressBar.setAttribute('aria-valuenow', percentage);
    progressBar.textContent = `${percentage}%`;
}

// Llamadas iniciales a las funciones de progreso
fetchProgresoDash(); // Para obtener el progreso general
fetchProgresoOF(); // Para obtener el progreso de las órdenes de fabricación

</script>


@endsection


