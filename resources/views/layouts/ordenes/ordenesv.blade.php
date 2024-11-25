@extends('layouts.menu')

@section('content')
<div class="container mt-4">
    <h1 class="text-primary mb-4 text-center">Gestión de Órdenes de Venta</h1>

    <!-- Buscador -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="searchForm" action="#" method="GET" class="d-flex align-items-center">
                <div class="input-group">
                    <input type="text" name="query" id="ordenSearch" class="form-control" placeholder="Buscar órdenes..." required>
                    <input type="date" size="15" maxlength="5" id="datePicker" class="form-control form-control-sm text-center w-auto mx-3 shadow-sm border-primary">
                    <button type="submit" class="btn btn-primary" id="searchBtn">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Navegación de Fechas -->
    <div class="d-flex justify-content-center align-items-center mb-4">
        <a href="#" id="prevDayBtn" class="btn btn-outline-secondary me-3">
            <i class="bi bi-chevron-left"></i> Día Anterior
        </a>

        <a href="#" id="todayBtn" class="btn btn-outline-primary ms-3">
            Día de Hoy <i class="bi bi-house"></i>
        </a>
    </div>

    <!-- Acordeón de resultados -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Órdenes de Venta</h5>
        </div>
        <div class="card-body">
            <div class="accordion" id="ordersAccordion">
                <!-- Orden 1 -->
                <div class="accordion-item order-row" data-date="2024-11-21">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Orden #1 - Cliente A - $100.00
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#ordersAccordion">
                        <div class="accordion-body">
                            <table class="table table-bordered table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Cliente A</td>
                                        <td>2024-11-21</td>
                                        <td>$100.00</td>
                                    </tr>
                                </tbody>
                            </table>
                            <a href="#" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Ver Más
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Orden 2 -->
                <div class="accordion-item order-row" data-date="2024-11-21">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Orden #2 - Cliente B - $200.00
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#ordersAccordion">
                        <div class="accordion-body">
                            <table class="table table-bordered table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>2</td>
                                        <td>Cliente B</td>
                                        <td>2024-11-21</td>
                                        <td>$200.00</td>
                                    </tr>
                                </tbody>
                            </table>
                            <a href="#" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Ver Más
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Orden 3 -->
                <div class="accordion-item order-row" data-date="2024-11-20">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Orden #3 - Cliente C - $150.00
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#ordersAccordion">
                        <div class="accordion-body">
                            <table class="table table-bordered table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>3</td>
                                        <td>Cliente C</td>
                                        <td>2024-11-20</td>
                                        <td>$150.00</td>
                                    </tr>
                                </tbody>
                            </table>
                            <a href="#" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Ver Más
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            

            <!-- Mensaje de no hay órdenes -->
            <div id="noOrdersRow" class="text-center text-muted d-none mt-4">
                <p>No se encontraron órdenes para la fecha seleccionada.</p>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- datatables -->
<link href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        const datePicker = $('#datePicker');
        const ordersAccordion = $('#ordersAccordion');
        const noOrdersRow = $('#noOrdersRow');
        let currentDate = moment();

        // Inicializar fecha actual
        datePicker.val(currentDate.format('YYYY-MM-DD'));
        filterOrdersByDate(currentDate.format('YYYY-MM-DD'));

        function filterOrdersByDate(date) {
            let foundAnyOrder = false;

            ordersAccordion.find('.order-row').each(function () {
                const rowDate = $(this).data('date');
                if (rowDate === date) {
                    $(this).show();
                    foundAnyOrder = true;
                } else {
                    $(this).hide();
                }
            });

            noOrdersRow.toggleClass('d-none', foundAnyOrder);
        }

        datePicker.on('change', function () {
            filterOrdersByDate($(this).val());
        });

        $('#prevDayBtn').on('click', function (e) {
            e.preventDefault();
            currentDate.subtract(1, 'days');
            const newDate = currentDate.format('YYYY-MM-DD');
            datePicker.val(newDate);
            filterOrdersByDate(newDate);
        });

        $('#todayBtn').on('click', function (e) {
            e.preventDefault();
            currentDate = moment();
            const newDate = currentDate.format('YYYY-MM-DD');
            datePicker.val(newDate);
            filterOrdersByDate(newDate);
        });
    });
    $(document).ready(function () {
        $('.datatable').DataTable({
            paging: true,
            searching: false,
            info: false,
            lengthChange: false,
            pageLength: 5,
            language: {
                paginate: {
                    previous: 'Anterior',
                    next: 'Siguiente'
                },
                emptyTable: 'No hay datos disponibles en la tabla',
            }
        });
    });
</script>
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

@endsection
@endsection
