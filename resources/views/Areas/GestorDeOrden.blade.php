@extends('layouts.menu2')
@section('title', 'Gestor de Ordenes')
@section('styles')
<style>
    #ToastGuardado {
        position: fixed; /* Fixed position */
        top: 5rem; /* Distance from the top */
        right: 20px; /* Distance from the right */
        z-index: 1050; /* Ensure it's above other content */
    }
    #myTab li a{
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        padding: 0.5rem 1rem 0.5rem 1rem;
    }
    #myTab li a:hover{
        background: #f1f1f1;
        border: solid 1px #e7e7e7;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    #myTab li .active{
        border: solid 1px #e7e7e7;
        border-bottom: solid white;
    }
    #myTab li .active:hover{
        border: solid 1px #e7e7e7;
        border-bottom: solid white;
    }
    #hr-menu{
        padding: 0;
        margin: 0;
    }
    #FiltroOrdenFabricacionContent{
        max-height: 6rem; 
        overflow: auto;
    }
    #Container_dropzone{
        position: fixed;
        z-index: 1;
        /*pointer-events: none !important;*/
        top: 5rem;
    }
    .dropzone{
        border: 2px dashed #007bff;
        padding: 20px; 
        text-align: center; 
        min-height: 4rem;
        transition: 1s transform;
        background: #ffffffce;
    }
    .dropzone:hover{
        transform: scale(1.1);
    }
    .dropzone svg{
        height: 2rem;
        animation: pulso-infinito 1.5s ease-in-out infinite;
        position: absolute;
        padding: 0;
        margin: 0;
        margin-left: -1rem; 
    }
    .Tabledrop{
        transition: 0.5s transform;
    }
    .Tabledrop:hover{
        transform: scale(1.01);
    }
    @keyframes pulso-infinito {
        0% {
            margin-top: 1rem;
        }
        50% {
            margin-top: 0.5;           /* Se hace más brillante */
        }
        100% {
            margin-top:0;
            opacity: 0.5;
        }
    }
</style>
@endsection
@section('content')
    <div class="row gy-3 mb-2 justify-content-between">
        <div class="col-md-9 col-auto">
            <h4 class="mb-2 text-1100">Gestor de Ordenes</h4>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-6" id="Container_dropzone" style="display: none;">
            <div class="col-12 mt-1">
                <div ondrop="drop(event,0)" ondragover="allowDrop(event)" class="dropzone mt-2 border-dashed rounded-2 min-h-0 mb-2">
                    <h5><i class="fas fa-angle-double-up"></i><br><br><br>Inicio de la fila</h5>
                    <p class="text-muted">Suelta aqui la Orden</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-3 border">Nomal</div>
                <div class="col-3 border text-center" style="background-color: rgb(139, 224, 252);">Urgente</div>
                <div class="col-3 border text-center" style="background-color: rgb(252, 248, 139);">Detenida</div>
                <div class="col-3 border text-center" style="background-color: rgb(255, 128, 44);color:white;">Prioridad</div>
            </div>
            <div class="tab-content mt-1" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-proceso" role="tabpanel" aria-labelledby="proceso-tab">
                    <div id="ContentTabla" class="col-12 mt-2">
                        <div class="card" id="DivCointainerTableSuministro">
                            <div class="table-responsive">
                                <table id="TablaAsignacionAbiertas" class="table table-sm fs--1 mb-1">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>Orden Fabricación</th>
                                            <th>Artículo</th>
                                            <th>Descripción</th>
                                            <th>Cantidad Pendiente de asignar</th>
                                            <th>Cantidad Total Orden de Fabricacion</th>
                                            <th class="text-center" colspan="2">Acciones</th>
                                            <th>Urgencia</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--MODAL DETALLE-->
    <div class="modal fade" id="ModalDetalle" data-bs-backdrop="static" aria-labelledby="ModalDetalleLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="height: 90%">
          <div class="modal-content" style="height: 100%">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="ModalDetalleLabel">Asignar por L&iacute;neas</h5><button class="btn" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-white"></span></button>
            </div>
            <div class="modal-body" id="ModalDetalleBody">
                <div class="" id="ModalDetalleBodyInfoOF">
                </div>
            </div>
            <div class="modal-footer" id="ModalDetalleFooter">
                <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
    </div>
    <!--Modal Parametros-->
    <div class="modal fade" id="ParametrosPorcentaje" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="ParametrosPorcentajeLabel">Modificar Par&aacute;metros L&iacute;nea <span id="NumeroLinea1">0</span></h5><button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="mb-1 col-6">
                        <label class="form-label" for="CantidadPersona">Cantidad de personas:</label>
                        <input class="form-control" id="CantidadPersona" oninput="RegexNumeros(this)" type="text" placeholder="Ingresa una cantidad" />
                        <div class="invalid-feedback" id="error_CantidadPersona"></div>
                    </div>
                    <div class="mb-1 col-6">
                        <label class="form-label" for="Piezaspersona">Piezas por persona:</label>
                        <input class="form-control" id="Piezaspersona" oninput="RegexNumeros(this)" type="text" placeholder="Ingresa una cantidad" />
                        <div class="invalid-feedback" id="error_Piezaspersona"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary" onclick="GuardarParametrosPorcentajes()">Guardar</button><button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cancelar</button></div>
          </div>
        </div>
    </div>
    <!--Modal Detener-->
    <div class="modal fade" id="DetenerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white" id="DetenerModalLabel">Detener Orden de Fabricaci&oacute;n<strong><span id="OFTextoDetener"></span></strong></h5><button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1"></span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label class="form-label" for="DetenerComentario">Comentario ( ¿Por qué se detiene? )</label>
                        <textarea class="form-control" id="DetenerComentario" rows="3"> </textarea>
                        <small class="text-danger" id="Error_DetenerComentario"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" id="DetenerGuardar">Guardar</button>
                    <button class="btn btn-soft-primary" type="button" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    const contenedor = document.getElementById('ContentTabla');
    $(document).ready(function(){
        RecargarTabla();
        setInterval(RecargarTabla, 180000);
        //setInterval(RecargarTabla,600000);//180000
    });
    function RecargarTabla(){
        $('#TablaAsignacionAbiertas').DataTable({
            destroy: true,
            paging: false,
            ajax: {
                url: "{{ route('GestorRecargarTabla') }}",
                dataSrc: function(json) {
                    return Object.values(json.data);
                },
                error: function(xhr, error, thrown) {
                    errorBD();
                }
            },
            order: [],
            columns: [
                { data: 'OrdenFabricacion' },
                { data: 'Articulo' },
                { data: 'Descripcion' },
                { data: 'CantidadPendiente' },
                { data: 'CantidadTotal' },
                { data: 'Asignar' },
                { data: 'Detener' },
                { data:'Urgencia'}
            ],
            columnDefs: [
                { targets: [0,1,2,3,4,5, 6], orderable: false }, // Opcional: desactiva orden en acciones
                { targets: [5, 6], searchable: false }, // Opcional: desactiva búsqueda
                { targets: [3,4], className: 'text-center' }, // Centra los botones y checkbox
                { targets: [5,6,7], visible: false, searchable: false }
            ],
            language: {
                            //"info": "Mostrando _START_ a _END_ de _TOTAL_ entrada(s)",
                            "info": "Total Ordenes de Fabricación: _TOTAL_",
                            "search":"Buscar",
                        },
                        "initComplete": function(settings, json) {
                            //$('#'+tabla).css('font-size', '0.7rem');
                        },
            rowCallback: function(row, data, index) {
                if (data.Urgencia === 'U') {
                    $(row).css('background-color', '#8be0fc');
                    $(row).addClass('Urgente');
                }
                if(data.prioridad == 1){
                    $(row).css('background-color', 'rgb(255, 128, 44)');
                    $(row).css('color', 'white');
                }
                if(data.Status == 0){
                    $(row).css('background-color', '#fcf88b');
                    $(row).css('color', '');
                    $(row).addClass('Detenida');
                }
                $(row).attr('draggable', 'true');
                $(row).attr('id', data.Id);
                //if(data.prioridad != 1){
                    $(row).attr('ondrop','drop(event,this)');
                    $(row).attr('ondragover','allowDrop(event)');
                    $(row).addClass('class','Tabledrop');
                //}
            },
            lengthChange: false,
            initComplete: function(settings, json) {
                //$('#'+tabla).css('font-size', '0.7rem');
            }
        });
    }
    
    //Permitir que el elemento sea soltado
    function allowDrop(event) {
        event.preventDefault();
    }
    // Al soltar los elementos en el dropzone
    function drop(event,posicion) {
        event.preventDefault();
        const data = event.dataTransfer.getData("text");
        let PosicionDestino = 0;
        let fila = document.getElementById(data);
        if(posicion){
            PosicionDestino = posicion.sectionRowIndex;
        }
        if(fila){
            if(fila.sectionRowIndex == PosicionDestino)return;
        }
        $.ajax({
            url: "{{route('PrioridadAsignacion')}}", 
            type: 'POST',
            data: {
                idOF:data,
                Posicion: PosicionDestino,
            },
            beforeSend: function() {
                //$('#ModalDetalleBodyInfoOF').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-grow text-info text-center" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            },
            success: function(response) {
                if(response.status=='success'){
                    tablaBody = document.querySelector("#TablaAsignacionAbiertas tbody");
                    if(PosicionDestino == 0){
                        PrimeraFila = tablaBody.rows[0];
                        /*if (PrimeraFila.classList.contains("Urgente")){
                            PrimeraFila.style.background = "#8be0fc";
                            PrimeraFila.style.color = "";
                        }else if (PrimeraFila.classList.contains("Detenida")){
                            PrimeraFila.style.background = "#fcf88b";
                            PrimeraFila.style.color = "";
                        }else{
                            PrimeraFila.style.background = "";
                            PrimeraFila.style.color = "";
                        }*/
                        var filadestino = tablaBody.children[PosicionDestino];
                        fila = document.getElementById(data);
                        fila.style.background = "rgb(255, 128, 44)";
                        fila.style.color = "white";
                        tablaBody.prepend(fila);
                    }else{
                        fila = document.getElementById(data);
                        var filadestino = tablaBody.children[PosicionDestino];
                        if(fila){
                            tablaBody.insertBefore(fila, filadestino);
                        }
                    }
                    success('Guardado Correctamente',response.message);
                    document.getElementById('Error_DetenerComentario').innerText = "";
                    document.getElementById('DetenerComentario').value = "";
                }else{
                    error('Error al dar Prioridad a la orden de fabricación '+response.Ordenfabricacion+'!', 'Los datos no pudieron ser procesados correctamente, si persiste el error contacta a TI');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                errorBD();
            },
            complete:function(){
            }
        });
    }
    // Detectar cuando inicia el arrastre
    contenedor.addEventListener('dragstart', function(e) {
        // Solo cuando se intente arrastrar un <tr>
        const fila = e.target.closest('tr');
        if (fila && fila.getAttribute('draggable') === 'true') {
            document.getElementById('Container_dropzone').style.display= "";
            let selectedRows = document.querySelectorAll("tr[id]"); 
            const idSeleccionado = fila.id;
             //let rowIds = [];
            /*selectedRows.forEach(row => {
                if(row.id) rowIds.push(row.id);
            });*/
            e.dataTransfer.setData("text", idSeleccionado);
        }
    });
    //ocultar el dropzone cuando se suelta (o se cancela)
    contenedor.addEventListener('dragend', function(e) {
        const fila = e.target.closest('tr');
        document.getElementById('Container_dropzone').style.display= "none";
    });
</script>
@endsection