@extends('layouts.menu2')
@section('title', 'Planeación')
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .btn-link{
        transform: scale(1.5);
    }
    .btn-link:hover {
    transform: scale(1.8);
    transition: transform 0.3s ease; /* Añade una transición suave */
}
</style>
{{--<style>
    .table-bordered {
        border: 1px solid #ddd;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #ddd;
    }

    .table thead.thead-dark {
        background-color: #4a90e2; /* Cambiar por un color que combine con tu proyecto */
        color: white;
        font-weight: bold;
    }

    .table tbody tr:nth-child(odd) {
        background-color: #f9f9f9; /* Fila alterna */
    }

    .table tbody tr:hover {
        background-color: #e6f7ff; /* Color al pasar el ratón */
        cursor: pointer;
    }

    .badge-success {
        background-color: #28a745;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-danger {
        background-color: #dc3545;
    }

    .btn-outline-info {
        color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-outline-info:hover {
        background-color: #17a2b8;
        color: white;
    }

    .search-box {
        margin-left: 0; /* Alinea al lado izquierdo */
    }
    .dataTables_info {
        color: #0d6efd; /* Azul estilo primary */
        font-weight: bold;
    }
    /* Cambiar color de las letras de los botones de paginación */
    .paginate_button {
        color: white !important; 
    }

    /* Estilo del botón activo */
    .paginate_button.current {
        color: white !important; 
        background-color: #0d6efd !important; 
    }

    /* Estilo al pasar el ratón por los botones */
    .paginate_button:hover {
        color: white !important; /* Letras blancas al pasar el ratón */
    }

</style>--}}
@endsection
@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs mb-3">
        <div class="row gy-3 mb-2 justify-content-between">
            <div class="col-md-9 col-auto">
                <h4 class="mb-2 text-1100">Cortes</h4>
            </div>
        </div>
    </div>
    <div>
                {{--<div class="col-5 mt-1 mb-4">
                    <div class="accordion" id="accordionFiltroOV">
                        <div class="card shadow-sm">
                            <div class="accordion-item border-top border-300 p-1">
                                <h4 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroOV" aria-expanded="true" aria-controls="collapseFiltroOV">
                                        <strong>Filtro Por Fecha</strong>
                                    </button>
                                </h4>
                                <div class="accordion-collapse collapse show" id="collapseFiltroOV" aria-labelledby="headingOne" data-bs-parent="#accordionFiltroOV">
                                    <div class="accordion-body pt-2">
                                        <form id="filtroForm" method="post" class="form-horizontal">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-10 mb-3">
                                                    <div class="input-group">
                                                        <input type="date" name="fecha" id="fecha" class="form-control form-control-sm" value="{{$fecha}}">
                                                        <button id="buscarFecha" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-search"></i>Buscar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>--}}
    <div class="card">
        <div class="card-body">
            <!-- Módulos sin corte y completados -->
            <ul class="nav nav-underline" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="proceso-tab" data-bs-toggle="tab" href="#tab-proceso" role="tab" aria-controls="tab-proceso" aria-selected="false" tabindex="-1">
                        Abiertos
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="completado-tab" data-bs-toggle="tab" href="#tab-completado" role="tab" aria-controls="tab-completado" aria-selected="false" tabindex="-1">
                        Cerrados
                    </a>
                </li>
            </ul>
            <hr>
            <div class="tab-content mt-4" id="myTabContent">
                <!-- Tab Proceso -->
                <div class="tab-pane fade show active" id="tab-proceso" role="tabpanel" aria-labelledby="proceso-tab">
                    <div class="table-responsive">
                        <table id="procesoTable" class="table table-sm">
                            <thead>
                                <tr class="bg-light">
                                    <th>Orden Fabricación</th>
                                    <th>Artículo</th>
                                    <th>Descripción</th>
                                    <th>Piezas Cortadas</th>
                                    <th>Cantidad Total</th>
                                    <th>Fecha Planeada</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="procesoTableBody" class="list">
                            @foreach($OrdenesFabricacionAbiertas as $orden)
                                <tr>
                                    <td>{{ $orden->OrdenFabricacion }}</td>
                                    <td>{{ $orden->Articulo }}</td>
                                    <td>{{ $orden->Descripcion }}</td>
                                    <td>{{ $orden->Piezascortadas }}</td>
                                    <td>{{ $orden->CantidadTotal }}</td>
                                    <td>{{ $orden->FechaEntrega }}</td>
                                    <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="fw-bold">Abierta</span></div></td>
                                    <td><button class="btn btn-sm btn-outline-primary px-3 py-1" onclick="Planear('{{$orden->idEncript}}')">Cortes</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-completado" role="tabpanel" aria-labelledby="completado-tab">
                    <div class="col-6 mt-1 mb-4">
                        <div class="accordion" id="accordionFiltroUnico">
                            <div class="accordion-item shadow-sm">
                                <h4 class="accordion-header" id="headingFiltroUnico">
                                    <button class="accordion-button btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroUnico" aria-expanded="true" aria-controls="collapseFiltroUnico">
                                        <strong>Filtro Fecha</strong>
                                    </button>
                                </h4>
                                <div class="accordion-collapse collapse collapse" id="collapseFiltroUnico" aria-labelledby="headingFiltroUnico" data-bs-parent="#accordionFiltroUnico">
                                    <div class="accordion-body pt-2">
                                        <form id="formFiltroUnico" method="post" class="form-horizontal">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="inputFechaUnica" class="form-label"><strong>Fecha Inicio</strong></label>
                                                    <div class="input-group">
                                                        <input type="date" name="fecha" id="inputFechaUnica" value="{{$fechaAtras}}" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <label for="inputFechaUnica" class="form-label"><strong>Fecha Fin</strong></label>
                                                    <div class="input-group">
                                                        <input type="date" name="fecha" id="inputFechaUnica" value="{{$fecha}}" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <button id="buscarUnico" class="btn btn-primary btn-sm float-end">
                                                        <i class="fa fa-search"></i> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="completadoTable" class="table table-sm fs--1 mb-1">
                            <thead>
                                <tr class="bg-light">
                                    <th>Orden Fabricación</th>
                                    <th>N&uacute;mero Partida</th>
                                    <th>Artículo</th>
                                    <th>Descripción</th>
                                    <th>Piezas Cortadas</th>
                                    <th>Cantidad Total</th>
                                    <th>Fecha Cierre</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="completadoTableBody" class="list">
                                @foreach($OrdenesFabricacionCerradas as $orden1)
                                <tr>
                                    <td>{{ $orden1->OrdenFabricacion }}</td>
                                    <td>{{ $orden1->NumeroPartida}}</td>
                                    <td>{{ $orden1->Articulo }}</td>
                                    <td>{{ $orden1->Descripcion }}</td>
                                    <td>{{ $orden1->Piezascortadas }}</td>
                                    <td>{{ $orden1->CantidadTotal }}</td>
                                    <td>{{ $orden1->FechaFinalizacion }}</td>
                                    <td class="text-center"><div class="badge badge-phoenix fs--2 badge-phoenix-danger"><span class="fw-bold">Cerrada</span></div></td>
                                    <td><button class="btn btn-sm btn-outline-primary px-3 py-1" onclick="Planear('{{$orden1->idEncript}}')">Cortes</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <!-- Modal de Detalles de la Orden -->
            <div class="modal fade" id="modalDetalleOrden" tabindex="-1"  role="dialog" aria-labelledby="modalDetalleOrdenLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        
                        <div class="modal-header p-2 bg">
                            <h5 class="modal-title" id="modalDetalleOrdenLabel" style="color: white;">Detalles de la Orden de Fabricacion</h5>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Sección de detalles -->
                            <div class="mb-4">
                                <h5 class="text-secondary"><i class="bi bi-info-circle"></i></h5>
                                <table class="table table-striped table-bordered table-sm">
                                    <tbody id="modalBodyContent">
                                        <!-- Aquí se insertarán los datos dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- Apartado de cortes del día -->
                            <div class="mt-4 p-3 bg-light rounded">
                                <h5 class="text-secondary"><i class="bi bi-scissors"></i> </h5>
                                <form id="formCortesDia" class="needs-validation d-flex align-items-center" novalidate>
                                    <div class="mb-2 d-flex align-items-center">
                                        <label for="numCortes" class="form-label ms-2 mb-0">Registrar Cantidad:</label> <!-- Eliminar margen inferior con mb-0 -->
                                        <input type="number" class="form-control form-control-sm ms-2" id="numCortes" name="numCortes" min="0" placeholder="Ingresa el número" required>
                                        

                                        @if(Auth::user()->hasPermission("CorteEdit"))
                                        <button type="button" id="confirmar" class="btn btn-outline-success btn-sm ms-2" >Confirmar</button>
                                        @endif
                                    </div>
                                    
                                </form>
                            
                                <div id="cortesGuardados" class="mt-3 text-success fw-bold">
                                    <div class="table-responsive">
                                        <table id="tablaCortes" class="table table-bordered table-striped">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Cortes De Piezas</th>
                                                    <th>Fecha De Registro</th>
                                                    <th>Fecha De Finalizacion</th>
                                                    <th>
                                                        @if(Auth::user()->hasPermission("CorteEdit"))
                                                         <button class="btn btn-outline-primary  btn-sm ms-2" id="pdfRangos" data-id="">Generar PDF de Rangos</button>
                                                        @endif

                                                     </th>
                                                    
                                                </tr>
                                            </thead>
                                            <div class="modal-footer d-flex justify-content-between">
                                            </div>
                                            <tbody>
                                                <!-- Cortes de la tabla PartidasOF se reflejan aquí -->
                                            </tbody>
                                        </table> 
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-outline-Danger" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tab Completado -->
            {{--<div class="tab-pane fade" id="tab-completado" role="tabpanel" aria-labelledby="completado-tab">
                    <div class="col-6 mt-2">
                        <div class="accordion" id="accordionFiltroUnico">
                            <div class="card shadow-sm">
                                <div class="accordion-item border-top border-300">
                                    <h4 class="accordion-header" id="headingFiltroUnico">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltroUnico" aria-expanded="true" aria-controls="collapseFiltroUnico">
                                            <strong>Filtros</strong>
                                        </button>
                                    </h4>
                                    <div class="accordion-collapse collapse show" id="collapseFiltroUnico" aria-labelledby="headingFiltroUnico" data-bs-parent="#accordionFiltroUnico">
                                        <div class="accordion-body pt-2">
                                            <form id="formFiltroUnico" method="post" class="form-horizontal">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="inputFechaUnica" class="form-label"><strong>Filtro por Fecha</strong></label>
                                                        <div class="input-group">
                                                            <input type="date" name="fecha" id="inputFechaUnica" class="form-control form-control-sm rounded-3">
                                                            <button id="buscarUnico" class="btn btn-outline-primary btn-sm ms-2 rounded-3">
                                                                <i class="fa fa-search"></i> Buscar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <div class="table-responsive">
                        <table id="completadoTable" class="table table-striped table-sm fs--1 mb-1">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th class="sort border-top ps-3" data-sort="orden">Or. Fabricación</th>
                                        <th class="sort border-top" data-sort="articulo">Artículo</th>
                                        <th class="sort border-top" data-sort="descripcion">Descripción</th>
                                        <th class="sort border-top" data-sort="cantidad">Cantidad Total</th>
                                        <th class="sort border-top" data-sort="fechaSAP">Fecha SAP</th>
                                        <th class="sort border-top" data-sort="fechaEstimada">Fecha Estimada</th>
                                        <th class="sort border-top" data-sort="estatus">Estatus</th>
                                        <th class="sort text-end align-middle pe-0 border-top">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <!-- Las filas serán llenadas por AJAX -->
                                </tbody>
                        </table>
                    </div>
                </div>
            --}}<!-- Modal para mostrar la información de la orden -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header p-2" style="background-color: #84c3ec; --bs-bg-opacity: .8;">
                            <h5 class="modal-title" id="myModalLabel">Información de la Orden de Fabricación</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="color: red; font-size: 1.25rem; background: none; border: none; padding: 3; line-height: 2;">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <!-- Contenedor con desplazamiento dinámico -->
                                <div id="partidas-lista" style="max-height: 400px; overflow-y: auto;">
                                
                                        
                                    <!-- Aquí se llenarán las partidas dinámicamente -->
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" id="btn-descargar-pdf" class="btn btn-primary" data-id="">Descargar PDF</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <!--<div class="modal fade" id="myModalRangos" tabindex="-1" data-bs-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header p-2 bg-primary">
                            <h5 class="modal-title" id="exampleModalLabel">Selecciona los Rangos para el PDF</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="color: red; font-size: 1.25rem; background: none; border: none; padding: 3; line-height: 2;">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="formRangoPDF" method="POST" action="{{ route('pdfcondicion') }}">
                                @csrf
                                <input type="hidden" id="orden_fabricacion_id" name="id">
                                <div class="mb-3">
                                    <label for="desde_no" class="form-label">Desde No:</label>
                                    <input type="number" name="desde_no" id="desde_no">
                                </div>
                                <div class="mb-3">
                                    <label for="hasta_no" class="form-label">Hasta No:</label>
                                    <input type="number" name="hasta_no" id="hasta_no">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    @if(Auth::user()->hasPermission("UsuriosEdit"))
                                    <button type="submit" id="btn-pdf-descarga" class="btn btn-primary" data-id="">Generar PDF</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>-->
            <input type="hidden" id="ordenFabricacionId" value="">
        </div>
    </div>
    <!--MODAL PARA PLANEACION-->
    <div class="modal fade" id="ModalSuministro" tabindex="-1" data-bs-backdrop="static" aria-labelledby="ModalSuministroLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="height: 90%">
          <div class="modal-content" style="height: 100%">
            <div class="modal-header bg-info">
              <h5 class="modal-title text-white" id="ModalSuministroLabel">Cortes</h5><button class="btn" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-white"></span></button>
            </div>
            <div class="modal-body" id="ModalSuministroBody">
                <div class="" id="ModalSuministroBodyInfoOF">
                </div>
                <div class="row">
                    <form id="CortesForm" class="row g-3 needs-validation" novalidate="">
                        <div class="col-6">
                            <label class="form-label" for="Cantitadpiezas">Ingresa n&uacute;mero de piezas a cortar </label>
                            <div class="input-group">
                                <input class="form-control form-control-sm has-validation" id="Cantitadpiezas" type="number" oninput="RegexNumeros(this)" placeholder="Ingresa una cantidad" />
                                <button id="btnGrupoPiezasCorte" class="btn btn-success btn-sm float-end">Guardar</button>
                            </div>
                            <div class="invalid-feedback" id="error_cantidad"></div>
                            <div class="form-check mt-2 mb-2">
                                <input class="form-check-input" id="Retrabajo" type="checkbox" />
                                <label class="form-check-label" for="Retrabajo">Retrabajo</label>
                                <div class="invalid-feedback" id="error_retrabajo"></div>
                                <button id="btnGrupoPiezasCorte1" class="btn btn-success btn-sm float-end" style="display: none">Guardar</button>
                            </div>
                            <input type="hidden" id="CantitadpiezasIdOF">
                        </div>
                        <div class="col-6 mt-2">
                            <div id="Emisiones" class="mt-2 mb-2" style="display:none;">
                                <label class="form-label" for="Cantitadpiezas">Selecciona una Orden de Producci&oacute;n </label>
                                <select id="EmisionesOpciones" class="form-select form-select-sm" aria-label=".form-select-sm">
                                </select>
                                <div class="invalid-feedback" id="error_emision"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="mt-0" id="ModalSuministroBodyPartidasOF">
                </div>
            </div>
            <div class="modal-footer" id="ModalSuministroFooter">
                <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
    </div>
    <!--MODAL SELECCIONAR COLOR-->
    <div class="modal fade" id="ModalColor" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog ">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Color de Etiquetas</h5><button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
            </div>
            <div class="modal-body">
                <select class="form-select" id="Coloretiqueta">
                    <option value="1" style="background-color: blue; color: white;">Azul</option>
                    <option value="2" style="background-color: red; color: white;">Rojo</option>
                    <option value="3" style="background-color: #d5006d;color: white;">Rosa Mexicano</option>
                    <option value="4" style="background-color: #6b3e26;color: white;">Café</option>
                    <option value="5" style="background-color: #006747;color: white;">Verde Bandera</option>
                    <option value="6" style="background-color: yellow;">Amarillo</option>
                    <option value="7" style="background-color: #d4e157;">Verde Limón</option>
                    <option value="8" style="background-color: pink;">Rosa</option>
                    <option value="9" style="background-color: orange;">Naranja</option>
                </select></div>
            <div class="modal-footer"><button class="btn btn-outline-primary" id="DescargarEtiquetas" type="button">Descargar</button><button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cancelar</button></div>
          </div>
        </div>
      </div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        //$('#procesoTableBody').html('{{$OrdenesFabricacionCerradas}}');
        DataTable('procesoTable',true);
        DataTable('completadoTable',true);
        document.getElementById("Retrabajo").addEventListener("change", function() {
            if (this.checked) {
                TraerEmisiones();
                $('#Emisiones').fadeIn(600);
                $('#btnGrupoPiezasCorte1').fadeIn();
                $('#btnGrupoPiezasCorte').fadeOut();
            }else{
                $('#EmisionesOpciones').html('');
                $('#Emisiones').fadeOut(600);
                $('#btnGrupoPiezasCorte').fadeIn();
                $('#btnGrupoPiezasCorte1').fadeOut();
            }
        });
        $('#btnGrupoPiezasCorte').click(function() {
            event.preventDefault();
            Cantitadpiezas=$('#Cantitadpiezas');
            errorCantidad=$('#error_cantidad');
            CantitadpiezasIdOF=$('#CantitadpiezasIdOF').val();
            if(CantitadpiezasIdOF=='' || CantitadpiezasIdOF==null){
                error('Partida no guardada','No fue posible guardar los datos de la partida, todos los campos son requeridos');
                return 0;
            }
            if(Cantitadpiezas.val()=="" || Cantitadpiezas.val()==null || Cantitadpiezas.val()==0){
                Cantitadpiezas.addClass('is-invalid');
                errorCantidad.text('Por favor, ingresa un número valido, mayor a 0.');
                errorCantidad.show();
                return 0; 
            }else{
                Cantitadpiezas.removeClass('is-invalid');
                errorCantidad.text('');
                errorCantidad.hide(); 
            }
            $.ajax({
                url: "{{route('GuardarCorte')}}", 
                type: 'POST',
                data: {
                    id:CantitadpiezasIdOF,
                    retrabajo:'Normal',
                    Cantitadpiezas:Cantitadpiezas.val(),
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {

                },
                success: function(response) {
                    if(response.status=="success"){
                        success('Guardado correctamente!',response.message);
                        RecargarTabla();
                        Planear(response.OF);
                    }else if(response.status=="error"){
                        error('Error',response.message);
                    }else if(response.status=="errorCantidada"){
                        error('Cantidad no disponible!',response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    error('Error Server',jqXHR.responseJSON.message);
                }
            });
        });
        $('#btnGrupoPiezasCorte1').click(function() {
            event.preventDefault();
            Cantitadpiezas=$('#Cantitadpiezas');
            errorCantidad=$('#error_cantidad');
            EmisionesOpciones=$('#EmisionesOpciones');
            errorEmision=$('#error_emision');
            Retrabajo=$('#Retrabajo');
            errorRetrabajo=$('#error_retrabajo');
            CantitadpiezasIdOF=$('#CantitadpiezasIdOF').val();
            if(CantitadpiezasIdOF=='' || CantitadpiezasIdOF==null){
                error('Partida no guardada','No fue posible guardar los datos de la partida, todos los campos son requeridos');
                return 0;
            }
            if(Cantitadpiezas.val()=="" || Cantitadpiezas.val()==null || Cantitadpiezas.val()==0){
                Cantitadpiezas.addClass('is-invalid');
                errorCantidad.text('Por favor, ingresa un número valido, mayor a 0.');
                errorCantidad.show();
                return 0; 
            }else{
                Cantitadpiezas.removeClass('is-invalid');
                errorCantidad.text('');
                errorCantidad.hide(); 
            }
            if(EmisionesOpciones.val()=='' || EmisionesOpciones.val()==null){
                EmisionesOpciones.addClass('is-invalid');
                errorEmision.text('Campo requerido, selecciona una Emisión de producción.');
                errorEmision.show();
                return 0; 
            }else{
                EmisionesOpciones.removeClass('is-invalid');
                errorEmision.text('');
                errorEmision.hide(); 
            }
            if(!Retrabajo.is(':checked')){
                Retrabajo.addClass('is-invalid');
                errorRetrabajo.text('Campo requerido, Es necesario marcar la casilla.');
                errorRetrabajo.show();
                return 0; 
            }else{
                Retrabajo.removeClass('is-invalid');
                errorRetrabajo.text('');
                errorRetrabajo.hide(); 
            }
            $.ajax({
                url: "{{route('GuardarCorte')}}", 
                type: 'POST',
                data: {
                    id:CantitadpiezasIdOF,
                    retrabajo:'Retrabajo',
                    Cantitadpiezas:Cantitadpiezas.val(),
                    _token: '{{ csrf_token() }}'  
                },
                beforeSend: function() {

                },
                success: function(response) {
                    if(response.status=="success"){
                        success('Guardado correctamente!',response.message);
                        RecargarTabla();
                        Planear(response.OF);
                    }else if(response.status=="error"){
                        error('Error',response.message);
                    }else if(response.status=="errorCantidada"){
                        error('Cantidad no disponible!',response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    error('Error Server',jqXHR.responseJSON.message);
                }
            });
        });
        setInterval(RecargarTabla, 300000);
    });
    function RecargarTabla(){
        $.ajax({
            url: "{{route('CorteRecargarTabla')}}", 
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status=="success"){
                    $('#procesoTable').DataTable().destroy();
                    $('#procesoTableBody').html(response.table);
                    DataTable('procesoTable',true);
                }
            }
        });
    }
    function Planear(OrdenFabricacion){
        $('#Cantitadpiezas').val('');
        $('#Emisiones').fadeOut(100);
        $('#ModalSuministro').modal('show');
        $('#ModalSuministroBodyInfoOF').html('');
        $('#ModalSuministroBodyPartidasOF').html('');
        $('#CantitadpiezasIdOF').val('');
        $('#btnGrupoPiezasCorte').fadeIn();
        $('#btnGrupoPiezasCorte1').fadeOut();
        document.getElementById("Retrabajo").checked=false;
        $.ajax({
            url: "{{route('CortesDatosModal')}}", 
            type: 'POST',
            data: {
                id:OrdenFabricacion,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
                $('#ModalSuministroBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                $('#ModalSuministroBodyPartidasOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                },
            success: function(response) {
                if(response.status=="success"){
                    $('#CantitadpiezasIdOF').val(response.id);
                    $('#ModalSuministroBodyInfoOF').html(response.Ordenfabricacioninfo);
                    $('#ModalSuministroBodyPartidasOF').html(response.Ordenfabricacionpartidas);
                    //$('#TablePartidasModal').DataTable().destroy();
                    //DataTable('TablePartidasModal',false);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#ModalSuministroBodyInfoOF').html('');
                $('#ModalSuministroBodyPartidasOF').html('');
                errorBD();
            }
        });
    }
    function TraerEmisiones(){
        OrdenFabricacion=$('#CantitadpiezasIdOF').val();
        $('#EmisionesOpciones').html('');
        $.ajax({
            url: "{{route('TraerEmisiones')}}", 
            type: 'POST',
            data: {
                id:OrdenFabricacion,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {

            },
            success: function(response) {
                $('#EmisionesOpciones').html(response.opciones);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            }
        });
    }
    function Cancelar(id,Numpartida){
        confirmacion('Cancelar Partida','¿Desea cancelar la partida Número '+Numpartida+'?','Confirmar','CancelarAccion(\''+id+'\')');
    }
    function CancelarAccion(id){
        $.ajax({
            url: "{{route('CancelarCorte')}}", 
            type: 'POST',
            data: {
                id:id,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {

            },
            success: function(response) {
                if(response.status=='success'){
                    Planear(response.OF);
                    success("Cancelada Correctamente!",response.message);
                    RecargarTabla()
                }else if(response.status=='errornoexiste'){
                    error("Error!",response.message);
                }else if(response.status=='erroriniciada'){
                    error("Error!",response.message);
                }else if(response.status=='errorfinalizada'){
                    error("Error!",response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            }
        });
    }
    function Finalizar(id){
        $.ajax({
            url: "{{route('FinalizarCorte')}}", 
            type: 'POST',
            data: {
                id:id,
                _token: '{{ csrf_token() }}'  
            },
            beforeSend: function() {
            },
            success: function(response) {
                if(response.status=='success'){
                    Planear(response.OF);
                    success("Finalizada Correctamente!",response.message);
                    RecargarTabla();
                }else if(response.status=='errornoexiste'){
                    error("Error!",response.message);
                }else if(response.status=='errorfinalizada'){
                    error("Error!",response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            }
        });
    }
    function DataTable(tabla, busqueda){
        $('#'+tabla).DataTable({
                        "pageLength": 10,  // Paginación de 10 elementos por página
                        "lengthChange": false, // Desactiva la opción de cambiar el número de elementos por página
                        "paging": true, // Habilitar paginación
                        "searching": busqueda, // Habilitar búsqueda
                        "ordering": true, // Habilitar ordenación de columnas
                        "info": true, // Muestra información sobre el total de elementos
                        "language": {
                            "info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                        },
                        "initComplete": function(settings, json) {
                            $('#procesoTable').css('font-size', '0.7rem'); // Aplica el tamaño de fuente a la tabla
                        }
        });

    }
    function Etiquetas(id){
        $('#ModalColor').modal('show');
        $('#DescargarEtiquetas').attr('onclick', 'etiquetaColor("'+id+'");');
        // Generar la URL usando Laravel route()
    }
    function etiquetaColor(id){
        Coloretiqueta=$('#Coloretiqueta').val();
        var url = "{{ route('generar.pdf')}}?id=_corteId_&Coloretiqueta =_Coloretiqueta_";
        url = url.replace('_corteId_', id);
        url = url.replace('_Coloretiqueta_', Coloretiqueta);
        // Abre la URL para descargar el PDF
        window.open(url, '_blank');
        $('#ModalColor').modal('hide');
    }
</script>

<!-- Script de inicialización -->
{{--<script>
    $(document).ready(function () {
        $('#completadoTable').DataTable({
            // Configuraciones opcionales
            responsive: true, // Hace la tabla responsive
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json" // Traducción al español
            },
            ajax: {
                url: 'URL_A_TU_API_O_ARCHIVO_JSON', // Ruta al JSON con los datos
                dataSrc: '' // Ajustar si el JSON no tiene una estructura plana
            },
            columns: [
                { data: 'orden' },
                { data: 'articulo' },
                { data: 'descripcion' },
                { data: 'cantidad' },
                { data: 'fechaSAP' },
                { data: 'fechaEstimada' },
                { data: 'estatus' },
                {
                    data: null,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `<button class="btn btn-sm btn-primary">Acción</button>`;
                    }
                }
            ]
        });
    });

</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        var tab = new bootstrap.Tab(document.querySelector('#proceso-tab'));
        tab.show(); // Muestra la pestaña "Sin Corte y En Proceso"
    ////////////////////////////////////////////////////////////////////////////////////////

    
    $('#procesoTable').on('click', '.ver-detalles', function() {
        var ordenFabricacionId = $(this).data('id');

        // Asignar el ID de la orden de fabricación a los botones correspondientes
        $('#pdfRangos').attr('data-id', ordenFabricacionId);
        $('#btn-pdf-descarga').attr('data-id', ordenFabricacionId);

        // Obtener los detalles de la orden de fabricación
        $.ajax({
            url: '{{ route("corte.getDetalleOrden") }}',
            type: 'GET',
            data: { id: ordenFabricacionId },
            success: function(response) {
                if (response.success) {
                    // Mostrar los detalles de la orden en el modal
                    $('#modalBodyContent').html(`
                        <div class="table-responsive">
                            <table id="ordenFabricacionTable" class="table table-striped table-sm fs--1 mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th class="sort border-top ps-3" data-sort="orden">Or. Fabricación</th>
                                        <th class="sort border-top" data-sort="articulo">Artículo</th>
                                        <th class="sort border-top" data-sort="descripcion">Descripción</th>
                                        <th class="sort border-top" data-sort="cantidad">Cantidad Total</th>
                                        <th class="sort border-top" data-sort="fechaSAP">Fecha SAP</th>
                                        <th class="sort border-top" data-sort="fechaEstimada">Fecha Estimada</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <tr>
                                        <td class="align-middle ps-3 orden">${response.data.OrdenFabricacion}</td>
                                        <td class="align-middle articulo">${response.data.Articulo}</td>
                                        <td class="align-middle descripcion">${response.data.Descripcion}</td>
                                        <td class="align-middle cantidad">${response.data.CantidadTotal}</td>
                                        <td class="align-middle fechaSAP">${response.data.FechaEntregaSAP}</td>
                                        <td class="align-middle fechaEstimada">${response.data.FechaEntrega}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `);

                    // Asignar el ID de la orden a un campo oculto (si es necesario)
                    $('#ordenFabricacionId').val(response.data.id);

                    // Llamar a la función que obtiene los cortes de la orden
                    obtenerCortes(ordenFabricacionId);
                    

                    // Mostrar el modal con los detalles de la orden
                    $('#modalDetalleOrden').modal('show');
                } else {
                    alert('Error: ' + response.message); // Muestra el mensaje si no se pudo obtener la orden
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error al obtener los detalles de la orden.');
            }
        });
    });
    $('#confirmar').click(function () {
        const numCortes = parseInt($('#numCortes').val().trim());
        const ordenFabricacionId = $('#ordenFabricacionId').val();

        if (!numCortes || numCortes <= 0 || isNaN(numCortes)) {
            alert('Por favor, ingrese un número válido de cortes.');
            return;
        }

        if (!ordenFabricacionId) {
            alert('No se ha seleccionado una orden de fabricación.');
            return;
        }
        var url = "{{ route('orden-fabricacion.cortes-info', ['ordenFabricacionId' => '__ordenFabricacionId__']) }}".replace('__ordenFabricacionId__', ordenFabricacionId);


        // Validar y guardar cortes
        $.ajax({
            url: url,
            type: 'GET',
            success: function (infoResponse) {
                if (!infoResponse.success) {
                    alert('Error al obtener la información de la orden de fabricación: ' + infoResponse.message);
                    return;
                }

                const cantidadTotal = parseInt(infoResponse.CantidadTotal);
                const cortesRegistrados = parseInt(infoResponse.cortes_registrados);

                if (cortesRegistrados + numCortes > cantidadTotal) {
                    alert('El número total de cortes excede la cantidad total de la orden.');
                    return;
                }

                // Preparar datos para guardar
                const datosPartidas = [{
                    cantidad_partida: numCortes,
                    fecha_fabricacion: new Date().toISOString().replace('T', ' ').split('.')[0],

                    orden_fabricacion_id: ordenFabricacionId
                }];

                $.ajax({
                    url: '{{ route("guardar.partida") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        datos_partidas: datosPartidas
                    },
                    success: function (saveResponse) {
                        if (saveResponse.status === 'success') {
                            alert('Partidas guardadas correctamente.');

                            // Actualizar la tabla de cortes
                            obtenerCortes(ordenFabricacionId);
                            
                                $.ajax({
                                    url: "{{ route('ordenes.cerradas') }}",
                                    method: "GET",
                                    headers: {
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                    },
                                    success: function (data) {
                                        const tableBody = $('#procesoTable tbody');
                                        tableBody.empty(); // Limpia la tabla antes de agregar nuevos datos

                                        // Asegúrate de que 'data' sea un arreglo, en caso de que sea un objeto
                                        const dataArray = Array.isArray(data) ? data : Object.values(data);

                                        // Iterar sobre los datos y construir las filas de la tabla
                                        dataArray.forEach(orden => {
                                            tableBody.append(`
                                                <tr>
                                                    <td class="align-middle ps-3 orden">${orden.OrdenFabricacion}</td>
                                                    <td class="align-middle articulo">${orden.Articulo}</td>
                                                    <td class="align-middle descripcion">${orden.Descripcion}</td>
                                                    <td class="align-middle cantidad">${orden.CantidadTotal}</td>
                                                    <td class="align-middle fechaSAP">${orden.FechaEntregaSAP}</td>
                                                    <td class="align-middle fechaEstimada">${orden.FechaEntrega}</td>
                                                    <td class="align-middle estatus">
                                                        <span class="${getBadgeClass(orden.estatus)}">
                                                            ${orden.estatus}
                                                            <i class="${getIconClass(orden.estatus)}"></i>
                                                        </span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" data-id="${orden.id}">
                                                            <i class="bi bi-eye"></i> Detalles
                                                        </a>
                                                    </td>
                                                </tr>
                                            `);
                                        });
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error en la solicitud AJAX:', error);
                                        alert('Ocurrió un error al cargar los datos.');
                                    }
                                });
                                $.ajax({
                                    url: "{{ route('ordenes.completadas') }}",
                                    method: "GET",
                                    headers: {
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                    },
                                    success: function (data) {
                                        console.log(data); // Imprime la respuesta en la consola

                                        const tableBody = $('#completadoTable tbody');
                                        tableBody.empty(); // Limpia la tabla

                                        // Asegúrate de que 'data' sea un arreglo, en caso de que sea un objeto
                                        const dataArray = Array.isArray(data) ? data : Object.values(data);

                                        // Recorrer los datos y agregar las filas a la tabla
                                        dataArray.forEach(orden => {
                                            tableBody.append(`
                                                <tr>
                                                    <td class="align-middle ps-3 orden">${orden.OrdenFabricacion}</td>
                                                    <td class="align-middle articulo">${orden.Articulo}</td>
                                                    <td class="align-middle descripcion">${orden.Descripcion}</td>
                                                    <td class="align-middle cantidad">${orden.CantidadTotal}</td>
                                                    <td class="align-middle fechaSAP">${orden.FechaEntregaSAP}</td>
                                                    <td class="align-middle fechaEstimada">${orden.FechaEntrega}</td>
                                                    <td class="align-middle estatus">
                                                        <span class="${getBadgeClass(orden.estatus)}">
                                                            ${orden.estatus}
                                                            <i class="${getIconClass(orden.estatus)}"></i>
                                                        </span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                    <a href="#" class="btn btn-outline-info btn-xs ver-regresar" data-id="${orden.id}">
                                                            <i class="bi bi-eye"></i>Detalles
                                                    </a>
                                                    </td>
                                                </tr>
                                            `);
                                        });
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error en la solicitud AJAX:', error);
                                        alert('Ocurrió un error al cargar los datos.');
                                    }
                                });
                                $.ajax({
                                    url: '{{ route("corte.getDetalles") }}',
                                    type: 'GET',
                                    data: { id: ordenFabricacionId },
                                    success: function(response) {
                                        if (response.success) {
                                            // Mostrar los detalles de la orden en el modal
                                            $('#modalBodyContent').html(`
                                                <div class="table-responsive">
                                                    <table id="ordenFabricacionTable" class="table table-striped table-sm fs--1 mb-0">
                                                        <thead class="bg-primary text-white">
                                                            <tr>
                                                                <th class="sort border-top ps-3" data-sort="orden">Or. Fabricación</th>
                                                                <th class="sort border-top" data-sort="articulo">Artículo</th>
                                                                <th class="sort border-top" data-sort="descripcion">Descripción</th>
                                                                <th class="sort border-top" data-sort="cantidad">Cantidad Total</th>
                                                                <th class="sort border-top" data-sort="fechaSAP">Fecha SAP</th>
                                                                <th class="sort border-top" data-sort="fechaEstimada">Fecha Estimada</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="list">
                                                            <tr>
                                                                <td class="align-middle ps-3 orden">${response.data.OrdenFabricacion}</td>
                                                                <td class="align-middle articulo">${response.data.Articulo}</td>
                                                                <td class="align-middle descripcion">${response.data.Descripcion}</td>
                                                                <td class="align-middle cantidad">${response.data.CantidadTotal}</td>
                                                                <td class="align-middle fechaSAP">${response.data.FechaEntregaSAP}</td>
                                                                <td class="align-middle fechaEstimada">${response.data.FechaEntrega}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            `);

                                            // Asignar el ID de la orden a un campo oculto (si es necesario)
                                            $('#ordenFabricacionId').val(response.data.id);

                                            // Llamar a la función que obtiene los cortes de la orden
                                        
                                        

                                            // Mostrar el modal con los detalles de la orden
                                            $('#modalDetalleOrden').modal('show');
                                        } else {
                                            alert('Error: ' + response.message); // Muestra el mensaje si no se pudo obtener la orden
                                        }
                                    },
                                    error: function(xhr) {
                                        console.error(xhr.responseText);
                                        alert('Error al obtener los detalles de la orden.');
                                    }
                                });
                        
                        } else {
                            alert('Errores: ' + saveResponse.errores.join(', '));
                        }
                    },
                    error: function (xhr) {
                        console.error('Error al guardar partidas:', xhr.responseText);
                        alert('Error al guardar las partidas: ' + xhr.responseText);
                    }
                });
            },
            error: function (xhr) {
                console.error('Error al obtener información de cortes:', xhr.responseText);
                alert('Error al obtener información de cortes: ' + xhr.responseText);
            }
        });
    });
    
    function obtenerCortescompletado(ordenFabricacionId) {
        $.ajax({
            url: '{{ route("corte.getCortes") }}',
            type: 'GET',
            data: { id: ordenFabricacionId },
            success: function (cortesResponse) {
                if (cortesResponse.success) {
                    const cortesHtml = cortesResponse.data.reverse().map((corte, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${corte.cantidad_partida}</td>
                            <td>${corte.fecha_fabricacion}</td>
                            <td>${corte.FechaFinalizacion || ''}</td>
                            <td>
                                <button type="button" class="btn btn-outline-primary btn-generar-etiquetas" data-id="${corte.id}">Generar Etiquetas</button>
                            </td>
                        
                            
                        </tr>
                    `).join('');
                    // Asignar el ID de la orden a un campo oculto (si es necesario)
                    $('#ordenFabricacionId').val(response.data.id);

                // Llamar a la función que obtiene los cortes de la orden
                obtenerCortes(ordenFabricacionId);
                obtenerCortesregresar(ordenFabricacionId);

                    $('#tablaCortes tbody').html(cortesHtml);
                } else {
                    $('#tablaCortes tbody').html('<tr><td colspan="6" class="text-center">No se encontraron cortes.</td></tr>');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Error al obtener los cortes.');
            }
        });
    }
    // Función para obtener y mostrar los cortes registrados
    function obtenerCortes(ordenFabricacionId)
    {
        $.ajax({
            url: '{{ route("corte.getCortes") }}',
            type: 'GET',
            data: { id: ordenFabricacionId },
            success: function (cortesResponse) {
                if (cortesResponse.success) {
                    const cortesHtml = cortesResponse.data.reverse().map((corte, index) => `
                        <tr id="corte-${corte.id}">
                            <td>${index + 1}</td>
                            <td>${corte.cantidad_partida}</td>
                            <td>${corte.fecha_fabricacion || ''}</td>
                            <td>${corte.FechaFinalizacion || ''}</td>
                            <td>
                                <button type="button" class="btn btn-outline-primary btn-generar-etiquetas" data-id="${corte.id}">Generar Etiquetas</button>
                            </td>
                            <td>
                                
                                <button type="button" class="btn btn-outline-warning btn-regresar" data-id="${corte.id}">Eliminar</button>
                                
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-danger btn-finalizar" data-id="${corte.id}">Finalizar</button>
                            </td>
                        </tr>
                    `).join('');
                    $('#tablaCortes tbody').html(cortesHtml);

                    // Agregar evento click para el botón 'Eliminar'
                    $('.btn-regresar').on('click', function() {
                        const corteId = $(this).data('id');  // Obtener el ID del corte

                        // Confirmar la acción de eliminación
                        if (confirm('¿Estás seguro de que deseas eliminar este corte?')) {
                            // Realizar la eliminación del corte
                            $.ajax({
                                url: '{{ route("corte.eliminarCorte") }}',
                                type: 'POST',  // Usamos POST en lugar de DELETE
                                data: {
                                    _method: 'DELETE',  // Simulamos una solicitud DELETE en Laravel
                                    id: corteId,
                                    _token: $('meta[name="csrf-token"]').attr('content')  // Agregamos el token CSRF
                                },
                                success: function(response) {
                                    $.ajax({
                        url: "{{ route('ordenes.completadas') }}",
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function (data) {
                            console.log(data); // Imprime la respuesta en la consola

                            const tableBody = $('#completadoTable tbody');
                            tableBody.empty(); // Limpia la tabla

                            // Asegúrate de que 'data' sea un arreglo, en caso de que sea un objeto
                            const dataArray = Array.isArray(data) ? data : Object.values(data);

                            // Recorrer los datos y agregar las filas a la tabla
                            dataArray.forEach(orden => {
                                tableBody.append(`
                                    <tr>
                                        <td class="align-middle ps-3 orden">${orden.OrdenFabricacion}</td>
                                        <td class="align-middle articulo">${orden.Articulo}</td>
                                        <td class="align-middle descripcion">${orden.Descripcion}</td>
                                        <td class="align-middle cantidad">${orden.CantidadTotal}</td>
                                        <td class="align-middle fechaSAP">${orden.FechaEntregaSAP}</td>
                                        <td class="align-middle fechaEstimada">${orden.FechaEntrega}</td>
                                        <td class="align-middle estatus">
                                            <span class="${getBadgeClass(orden.estatus)}">
                                                ${orden.estatus}
                                                <i class="${getIconClass(orden.estatus)}"></i>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                        <a href="#" class="btn btn-outline-info btn-xs ver-regresar" data-id="${orden.id}">
                                                <i class="bi bi-eye"></i>Detalles
                                        </a>
                                        </td>
                                    </tr>
                                `);
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error('Error en la solicitud AJAX:', error);
                            alert('Ocurrió un error al cargar los datos.');
                        }
                    });
                    $.ajax({
                        url: "{{ route('ordenes.cerradas') }}",
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        success: function (data) {
                            const tableBody = $('#procesoTable tbody');
                            tableBody.empty(); // Limpia la tabla antes de agregar nuevos datos

                            // Asegúrate de que 'data' sea un arreglo, en caso de que sea un objeto
                            const dataArray = Array.isArray(data) ? data : Object.values(data);

                            // Iterar sobre los datos y construir las filas de la tabla
                            dataArray.forEach(orden => {
                                tableBody.append(`
                                    <tr>
                                        <td class="align-middle ps-3 orden">${orden.OrdenFabricacion}</td>
                                        <td class="align-middle articulo">${orden.Articulo}</td>
                                        <td class="align-middle descripcion">${orden.Descripcion}</td>
                                        <td class="align-middle cantidad">${orden.CantidadTotal}</td>
                                        <td class="align-middle fechaSAP">${orden.FechaEntregaSAP}</td>
                                        <td class="align-middle fechaEstimada">${orden.FechaEntrega}</td>
                                        <td class="align-middle estatus">
                                            <span class="${getBadgeClass(orden.estatus)}">
                                                ${orden.estatus}
                                                <i class="${getIconClass(orden.estatus)}"></i>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" data-id="${orden.id}">
                                                <i class="bi bi-eye"></i> Detalles
                                            </a>
                                        </td>
                                    </tr>
                                `);
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error('Error en la solicitud AJAX:', error);
                            alert('Ocurrió un error al cargar los datos.');
                        }
                    });

                                    if (response.success) {
                                        // Eliminar la fila de la tabla
                                        $(`#corte-${corteId}`).remove();
                                        alert('Corte eliminado exitosamente.');
                                    } else {
                                        alert('Error al eliminar el corte.');
                                    }

                                },
                                error: function(xhr) {
                                    console.error(xhr.responseText);
                                    alert('Error al eliminar el corte.');
                                }
                            });
                        }
                    });
                } else {
                    $('#tablaCortes tbody').html('<tr><td colspan="6" class="text-center">No se encontraron cortes.</td></tr>');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Error al obtener los cortes.');
            }
        });
    }

    // Al hacer clic en el botón "Finalizar"
    $(document).on('click', '.btn-finalizar', function() {
        var corteId = $(this).data('id');
        var fechaHoraActual = new Date().toISOString().slice(0, 19).replace('T', ' ');

        $.ajax({
            url: '{{ route("corte.finalizarCorte") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: corteId,
                fecha_finalizacion: fechaHoraActual
            },
            success: function(response)
            
            {


                if (response.success) {
                    // Recargar la tabla de cortes
                    
                    obtenerCortes($('#ordenFabricacionId').val());
                } else {
                    alert('Error al finalizar el corte: ' + response.message);
                }
                obtenerCortes(ordenFabricacionId);
                $.ajax({
                    url: "{{ route('orden-fabricacion.update-status') }}",
                                method: "POST",
                                data: {
                                    id: ordenFabricacionId,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    if (response.success) {
                                        // Actualizar el badge de estatus en la tabla
                                        const row = $('tr[data-id="'+ ordenFabricacionId +'"]');
                                        const badge = row.find('.estatus .badge');

                                        let badgeClass;
                                        switch (response.estatus) {
                                            case 'Completado':
                                                badgeClass = 'badge-success';
                                                break;
                                            case 'En proceso':
                                                badgeClass = 'badge-warning';
                                                break;
                                            default:
                                                badgeClass = 'badge-danger';
                                        }

                                        badge.attr('class', `badge ${badgeClass}`).text(response.estatus);
                                    } else {
                                        alert(response.message);
                                    }
                                },
                                error: function (xhr) {
                                    alert('Error al actualizar el estatus');
                                }
                            });
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error al finalizar el corte.');
            }
        });
        
    });
    // Abrir modal para rangos PDF
    $('#pdfRangos').on('click', function() {
        var ordenFabricacionId = $(this).attr('data-id');
        $('#orden_fabricacion_id').val(ordenFabricacionId);
        $('#myModalRangos').modal('show');
    });
    // Evento para mostrar la información de la orden cuando se hace clic en el botón
    $(document).on('click', '.btn-generar-etiquetas', function() {
        var corteId = $(this).data('id');

        $.ajax({
            url: '{{ route("mostrar.etiqueta") }}',
            data: { id: corteId },
            type: 'GET',
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                } else {
                    $('#partidas-lista').html('');

                    var partidasHtml = '';
                    response.partidas.forEach(function(partida) {
                        partidasHtml += `
                            <div>
                                <p><strong>No:</strong> ${partida.cantidad}</p>
                                <p><strong>Orden de Fabricación:</strong> ${partida.orden_fabricacion}</p>
                                <p><strong>Descripción:</strong> ${partida.descripcion}</p>
                            </div>
                            <hr>`;
                    });

                    $('#partidas-lista').html(partidasHtml);
                    $('#myModal').modal('show');

                    $('#btn-descargar-pdf').data('id', corteId);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                console.log('Detalles:', xhr.responseText);
                alert('Error al cargar los datos.');
            }
        });
    });
    // Evento para descargar el PDF cuando se hace clic en el botón de descargar
    $(document).on('click', '#btn-descargar-pdf', function() {
        var corteId = $(this).data('id');
        if (!corteId) {
            alert('No se encontró el ID');
            return;
        }

        // Generar la URL usando Laravel route()
        var url = "{{ route('generar.pdf', ['id' => '__corteId__']) }}".replace('__corteId__', corteId);

        // Abre la URL para descargar el PDF
        window.open(url, '_blank');
    });
    //buscar por fecha abierto
    document.getElementById('buscarFecha').addEventListener('click', function (e) {
            e.preventDefault();
            
            const fecha = document.getElementById('fecha').value;
            
            if (!fecha) {
                alert('Por favor, selecciona una fecha.');
                return;
            }

            // Realiza la solicitud AJAX
            fetch('{{ route("Fitrar.Fecha") }}', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ fecha })
            })
            .then(response => {
                if (!response.ok) throw new Error('Error al filtrar los datos.');
                return response.json();
            })
            .then(data => {
                const tableBody = document.querySelector('#procesoTable tbody');
                tableBody.innerHTML = ''; // Limpiar la tabla

                // Filtrar los datos para excluir los completados
                const datosFiltrados = data.filter(item => item.estatus !== 'Cerrado');

                // Agrega las filas a la tabla
                datosFiltrados.forEach(item => {
                    // Determina las clases e íconos del estatus
                    let badgeClass = '';
                    let badgeIcon = '';
                    switch (item.estatus) {
                        case 'Abierto':
                            badgeClass = 'badge badge-phoenix fs--2 badge-phoenix-warning';
                            badgeIcon = 'ms-1 fas fa-spinner';
                            break;
                        case 'Abierto':
                            badgeClass = 'badge badge-phoenix fs--2 badge-phoenix-secondary';
                            badgeIcon = 'ms-1 fas fa-times';
                            break;
                        default:
                            badgeClass = 'badge-danger';
                            badgeIcon = 'fas fa-times';
                    }
                    
                    // Crea una fila de la tabla
                    var row = `
                        <tr>
                            <td>${item.OrdenFabricacion}</td>
                            <td>${item.Articulo}</td>
                            <td>${item.Descripcion}</td>
                            <td>${item.CantidadTotal}</td>
                            <td>${item.FechaEntregaSAP}</td>
                            <td>${item.FechaEntrega}</td>
                            <td><span class="badge ${badgeClass} d-block mt-2" style="font-size: 12px;">
                                <span class="fw-bold">${item.estatus}</span>
                                <span class="ms-1 ${badgeIcon}"></span>
                            </span></td>
                            <td><a href="#" class="btn btn-outline-warning btn-xs ver-detalles d-flex align-items-center justify-content-center" 
                                style="padding: 2px 6px; font-size: 12px; border-radius: 4px;" data-id="${item.id}">
                                Detalles
                            </a></td>
                        </tr>
                    `;
                    $('#procesoTable tbody').append(row);
                });
            })
            .catch(error => {
                console.error('Error:', error.message);
                alert('Error al procesar la solicitud: ' + error.message);
            });
    });
    document.getElementById('buscarUnico').addEventListener('click', function (e) {
            e.preventDefault();
        
            const fecha = document.getElementById('inputFechaUnica').value;
            
            if (!fecha) {
                alert('Por favor, selecciona una fecha.');
                return;
            }

            // Realiza la solicitud AJAX
            fetch('{{ route("Fitrar.Fechacerrado") }}', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ fecha })
            })
            .then(response => {
                if (!response.ok) throw new Error('Error al filtrar los datos.');
                return response.json();
            })
            .then(data => {
                const tableBody = document.querySelector('#completadoTable tbody');
                tableBody.innerHTML = ''; // Limpiar la tabla

                // Filtrar los datos para excluir los completados
                const datosFiltrados = data.filter(item => item.estatus !== 'Abiertos');

                // Agrega las filas a la tabla
                datosFiltrados.forEach(item => {
                    // Determina las clases e íconos del estatus
                    let badgeClass = '';
                    let badgeIcon = '';
                    switch (item.estatus) {
                        case 'Cerrados':
                            badgeClass = 'badge badge-phoenix fs--2 badge-phoenix-warning';
                            badgeIcon = 'ms-1 fas fa-spinner';
                            break;
                        case 'Abierto':
                            badgeClass = 'badge badge-phoenix fs--2 badge-phoenix-secondary';
                            badgeIcon = 'ms-1 fas fa-times';
                            break;
                        default:
                            badgeClass = 'badge-danger';
                            badgeIcon = 'fas fa-times';
                    }
                    
                    // Crea una fila de la tabla
                    var row = `
                        <tr>
                            <td>${item.OrdenFabricacion}</td>
                            <td>${item.Articulo}</td>
                            <td>${item.Descripcion}</td>
                            <td>${item.CantidadTotal}</td>
                            <td>${item.FechaEntregaSAP}</td>
                            <td>${item.FechaEntrega}</td>
                            <td><span class="badge ${badgeClass} d-block mt-2" style="font-size: 12px;">
                                <span class="fw-bold">${item.estatus}</span>
                                <span class="ms-1 ${badgeIcon}"></span>
                            </span></td>
                            <td>  
                                
                                <a href="#" class="btn btn-outline-info btn-xs ver-regresar" data-id="${item.id}">
                                <i class="bi bi-eye"></i>Detalles
                                
                                </a>
                            </td>
                        </tr>
                    `;
                    $('#completadoTable tbody').append(row);
                });
            })
            .catch(error => {
                console.error('Error:', error.message);
                alert('Error al procesar la solicitud: ' + error.message);
            });
    });
        // Llamada AJAX para obtener las ordenes cerradas
    
        $.ajax({
            url: "{{ route('ordenes.cerradas') }}",
            method: "GET",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
            },
            success: function (data) {
                const tableBody = $('#procesoTable tbody');
                tableBody.empty(); // Limpia la tabla antes de agregar nuevos datos

                // Asegúrate de que 'data' sea un arreglo, en caso de que sea un objeto
                const dataArray = Array.isArray(data) ? data : Object.values(data);

                // Iterar sobre los datos y construir las filas de la tabla
                dataArray.forEach(orden => {
                    tableBody.append(`
                        <tr>
                            <td class="align-middle ps-3 orden">${orden.OrdenFabricacion}</td>
                            <td class="align-middle articulo">${orden.Articulo}</td>
                            <td class="align-middle descripcion">${orden.Descripcion}</td>
                            <td class="align-middle cantidad">${orden.CantidadTotal}</td>
                            <td class="align-middle fechaSAP">${orden.FechaEntregaSAP}</td>
                            <td class="align-middle fechaEstimada">${orden.FechaEntrega}</td>
                            <td class="align-middle estatus">
                                <span class="${getBadgeClass(orden.estatus)}">
                                    ${orden.estatus}
                                    <i class="${getIconClass(orden.estatus)}"></i>
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" data-id="${orden.id}">
                                    <i class="bi bi-eye"></i> Detalles
                                </a>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function (xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                alert('Ocurrió un error al cargar los datos.');
            }
        });
        // Llamada AJAX para obtener las ordenes completadas
        $.ajax({
            url: "{{ route('ordenes.completadas') }}",
            method: "GET",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (data) {
                console.log(data); // Imprime la respuesta en la consola

                const tableBody = $('#completadoTable tbody');
                tableBody.empty(); // Limpia la tabla

                // Asegúrate de que 'data' sea un arreglo, en caso de que sea un objeto
                const dataArray = Array.isArray(data) ? data : Object.values(data);

                // Recorrer los datos y agregar las filas a la tabla
                dataArray.forEach(orden => {
                    tableBody.append(`
                        <tr>
                            <td class="align-middle ps-3 orden">${orden.OrdenFabricacion}</td>
                            <td class="align-middle articulo">${orden.Articulo}</td>
                            <td class="align-middle descripcion">${orden.Descripcion}</td>
                            <td class="align-middle cantidad">${orden.CantidadTotal}</td>
                            <td class="align-middle fechaSAP">${orden.FechaEntregaSAP}</td>
                            <td class="align-middle fechaEstimada">${orden.FechaEntrega}</td>
                            <td class="align-middle estatus">
                                <span class="${getBadgeClass(orden.estatus)}">
                                    ${orden.estatus}
                                    <i class="${getIconClass(orden.estatus)}"></i>
                                </span>
                            </td>
                            <td class="text-center align-middle">
                            <a href="#" class="btn btn-outline-info btn-xs ver-regresar" data-id="${orden.id}">
                                    <i class="bi bi-eye"></i>Detalles
                            </a>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function (xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                alert('Ocurrió un error al cargar los datos.');
            }
        });

        $('#completadoTable').on('click', '.ver-regresar', function() {
        var ordenFabricacionId = $(this).data('id');

        // Asignar el ID de la orden de fabricación a los botones correspondientes
        $('#pdfRangos').attr('data-id', ordenFabricacionId);
        $('#btn-pdf-descarga').attr('data-id', ordenFabricacionId);

        // Obtener los detalles de la orden de fabricación
        $.ajax({
            url: '{{ route("corte.getDetalles") }}',
            type: 'GET',
            data: { id: ordenFabricacionId },
            success: function(response) {
                if (response.success) {
                    // Mostrar los detalles de la orden en el modal
                    $('#modalBodyContent').html(`
                        <div class="table-responsive">
                            <table id="ordenFabricacionTable" class="table table-striped table-sm fs--1 mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th class="sort border-top ps-3" data-sort="orden">Or. Fabricación</th>
                                        <th class="sort border-top" data-sort="articulo">Artículo</th>
                                        <th class="sort border-top" data-sort="descripcion">Descripción</th>
                                        <th class="sort border-top" data-sort="cantidad">Cantidad Total</th>
                                        <th class="sort border-top" data-sort="fechaSAP">Fecha SAP</th>
                                        <th class="sort border-top" data-sort="fechaEstimada">Fecha Estimada</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <tr>
                                        <td class="align-middle ps-3 orden">${response.data.OrdenFabricacion}</td>
                                        <td class="align-middle articulo">${response.data.Articulo}</td>
                                        <td class="align-middle descripcion">${response.data.Descripcion}</td>
                                        <td class="align-middle cantidad">${response.data.CantidadTotal}</td>
                                        <td class="align-middle fechaSAP">${response.data.FechaEntregaSAP}</td>
                                        <td class="align-middle fechaEstimada">${response.data.FechaEntrega}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `);

                    // Asignar el ID de la orden a un campo oculto (si es necesario)
                    $('#ordenFabricacionId').val(response.data.id);

                    // Llamar a la función que obtiene los cortes de la orden
                    obtenerCortesregresar(ordenFabricacionId);

                    // Mostrar el modal con los detalles de la orden
                    $('#modalDetalleOrden').modal('show');
                } else {
                    alert('Error: ' + response.message); // Muestra el mensaje si no se pudo obtener la orden
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error al obtener los detalles de la orden.');
            }
        });
        });
    // Función para obtener y mostrar los cortes registrados
    function obtenerCortesregresar(ordenFabricacionId) {
        $.ajax({
            url: '{{ route("corte.getCortes") }}',
            type: 'GET',
            data: { id: ordenFabricacionId },
            success: function (cortesResponse) {
                if (cortesResponse.success) {
                    const userCanEdit = cortesResponse.userCanEdit;
                    const cortesHtml = cortesResponse.data.reverse().map((corte, index) => `
                        <tr id="corte-${corte.id}">
                            <td>${index + 1}</td>
                            <td>${corte.cantidad_partida}</td>
                            <td>${corte.fecha_fabricacion}</td>
                            <td>${corte.FechaFinalizacion || ''}</td>
                            <td>
                                ${userCanEdit ? `
                                    <button type="button" class="btn btn-outline-primary btn-generar-etiquetas" data-id="${corte.id}">Generar Etiquetas</button>
                                ` : ''}
                            </td>
                            <td>
                                ${userCanEdit ? `
                                    <button type="button" class="btn btn-outline-danger btn-regresar" data-id="${corte.id}">regresar</button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('');
                    $('#tablaCortes tbody').html(cortesHtml);

                    // Asignar evento 'click' para el botón 'regresar'
                    $('.btn-regresar').on('click', function() {
                        const corteId = $(this).data('id');
                        // Realizar la eliminación del corte
                        $.ajax({
                            url: '{{ route("corte.eliminarCorte") }}',
                            type: 'POST',  // Usamos POST en lugar de DELETE
                            data: {
                                _method: 'DELETE',  // Esto simula una solicitud DELETE en Laravel
                                id: corteId,
                                _token: $('meta[name="csrf-token"]').attr('content')  // Agregamos el token CSRF
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Eliminar la fila de la tabla
                                    $(`#corte-${corteId}`).remove();
                                } else {
                                    alert('Error al eliminar el corte.');
                                }
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                alert('Error al eliminar el corte.');
                            }
                        });
                    });
                    $.ajax({
                        url: "{{ route('ordenes.completadas') }}",
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function (data) {
                            console.log(data); // Imprime la respuesta en la consola

                            const tableBody = $('#completadoTable tbody');
                            tableBody.empty(); // Limpia la tabla

                            // Asegúrate de que 'data' sea un arreglo, en caso de que sea un objeto
                            const dataArray = Array.isArray(data) ? data : Object.values(data);

                            // Recorrer los datos y agregar las filas a la tabla
                            dataArray.forEach(orden => {
                                tableBody.append(`
                                    <tr>
                                        <td class="align-middle ps-3 orden">${orden.OrdenFabricacion}</td>
                                        <td class="align-middle articulo">${orden.Articulo}</td>
                                        <td class="align-middle descripcion">${orden.Descripcion}</td>
                                        <td class="align-middle cantidad">${orden.CantidadTotal}</td>
                                        <td class="align-middle fechaSAP">${orden.FechaEntregaSAP}</td>
                                        <td class="align-middle fechaEstimada">${orden.FechaEntrega}</td>
                                        <td class="align-middle estatus">
                                            <span class="${getBadgeClass(orden.estatus)}">
                                                ${orden.estatus}
                                                <i class="${getIconClass(orden.estatus)}"></i>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                        <a href="#" class="btn btn-outline-info btn-xs ver-regresar" data-id="${orden.id}">
                                                <i class="bi bi-eye"></i>Detalles
                                        </a>
                                        </td>
                                    </tr>
                                `);
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error('Error en la solicitud AJAX:', error);
                            alert('Ocurrió un error al cargar los datos.');
                        }
                    });
                    $.ajax({
                        url: "{{ route('ordenes.cerradas') }}",
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        success: function (data) {
                            const tableBody = $('#procesoTable tbody');
                            tableBody.empty(); // Limpia la tabla antes de agregar nuevos datos

                            // Asegúrate de que 'data' sea un arreglo, en caso de que sea un objeto
                            const dataArray = Array.isArray(data) ? data : Object.values(data);

                            // Iterar sobre los datos y construir las filas de la tabla
                            dataArray.forEach(orden => {
                                tableBody.append(`
                                    <tr>
                                        <td class="align-middle ps-3 orden">${orden.OrdenFabricacion}</td>
                                        <td class="align-middle articulo">${orden.Articulo}</td>
                                        <td class="align-middle descripcion">${orden.Descripcion}</td>
                                        <td class="align-middle cantidad">${orden.CantidadTotal}</td>
                                        <td class="align-middle fechaSAP">${orden.FechaEntregaSAP}</td>
                                        <td class="align-middle fechaEstimada">${orden.FechaEntrega}</td>
                                        <td class="align-middle estatus">
                                            <span class="${getBadgeClass(orden.estatus)}">
                                                ${orden.estatus}
                                                <i class="${getIconClass(orden.estatus)}"></i>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="#" class="btn btn-outline-warning btn-xs ver-detalles" data-id="${orden.id}">
                                                <i class="bi bi-eye"></i> Detalles
                                            </a>
                                        </td>
                                    </tr>
                                `);
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error('Error en la solicitud AJAX:', error);
                            alert('Ocurrió un error al cargar los datos.');
                        }
                    });

                } else {
                    $('#tablaCortes tbody').html('<tr><td colspan="6" class="text-center">No se encontraron cortes.</td></tr>');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Sin acceso a Tabla de cortes.');
            }
        });
    }
    function getBadgeClass(estatus) {
        return estatus.toLowerCase() === 'abierta' ? 'badge bg-success' : 'badge bg-danger';
    }
    function getIconClass(estatus) {
            return estatus.toLowerCase() === 'abierta' ? 'bi bi-check-circle' : 'bi bi-x-circle';
    }
    });

</script>--}}
@endsection
