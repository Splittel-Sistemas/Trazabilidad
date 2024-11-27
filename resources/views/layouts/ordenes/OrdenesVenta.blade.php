@extends('layouts.menu')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4 text-primary">Gestión de Órdenes de Venta</h2>

    
    <div class="card shadow-lg mb-5">
        <div class="card-body">
            <h4 class="card-title text-primary"><strong>Órdenes de Venta</strong></h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>OV Número</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                   
                        <tr>
                            <td>1</td>
                            <td>OV-001</td>
                            <td>Cliente A</td>
                            <td>2024-11-21</td>
                            <td>Pendiente</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="showOVDetails('OV-001')">Ver Detalles</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>OV-002</td>
                            <td>Cliente B</td>
                            <td>2024-11-20</td>
                            <td>Completada</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="showOVDetails('OV-002')">Ver Detalles</button>
                            </td>
                        </tr>
                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="card shadow-lg">
        <div class="card-body">
            <h4 class="card-title text-primary"><strong>Detalles de la Orden de Venta</strong></h4>

            
            <div id="ovDetails" style="display: none;">
                <p><strong>Número OV:</strong> <span id="ovNumero"></span></p>
                <p><strong>Cliente:</strong> <span id="ovCliente"></span></p>
                <p><strong>Fecha:</strong> <span id="ovFecha"></span></p>

               
                <h5 class="mt-4 text-primary"><strong>Vincular Orden de Fabricación</strong></h5>
                <div class="form-group mb-3">
                    <select class="form-select" id="ofSelector" onchange="showOFDetails(this.value)">
                        <option selected disabled>Seleccione una Orden de Fabricación</option>
                        <option value="OF-001">OF-001 (Cliente A)</option>
                        <option value="OF-002">OF-002 (Cliente B)</option>
                    </select>
                </div>

                
                <div id="ofDetails" style="display: none;">
                    <h5 class="text-secondary"><strong>Detalles de la Orden de Fabricación</strong></h5>
                    <p><strong>Número:</strong> <span id="ofNumero"></span></p>
                    <p><strong>Cliente:</strong> <span id="ofCliente"></span></p>
                    <p><strong>Fecha:</strong> <span id="ofFecha"></span></p>

                  
                    <h6 class="mt-3 text-primary"><strong>Partidas</strong></h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Artículo</th>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="partidasTable">
                                
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="mt-3">
                        <button class="btn btn-success btn-sm" onclick="addPartida()">Agregar Partida</button>
                    </div>
                </div>
            </div>

            
            <p id="noOVSelected" class="text-danger">Seleccione una Orden de Venta para ver los detalles.</p>
        </div>
    </div>
</div>

<script>
    const ordenesVenta = {
        "OV-001": {
            cliente: "Cliente A",
            fecha: "2024-11-21",
        },
        "OV-002": {
            cliente: "Cliente B",
            fecha: "2024-11-20",
        },
    };

    const ordenesFabricacion = {
        "OF-001": {
            cliente: "Cliente A",
            fecha: "2024-11-21",
            partidas: [
                { articulo: "A001", descripcion: "Producto 1", cantidad: 10 },
                { articulo: "A002", descripcion: "Producto 2", cantidad: 5 },
            ],
        },
        "OF-002": {
            cliente: "Cliente B",
            fecha: "2024-11-20",
            partidas: [
                { articulo: "B001", descripcion: "Producto 3", cantidad: 7 },
            ],
        },
    };

    function showOVDetails(ovNumero) {
        const ov = ordenesVenta[ovNumero];

       
        document.getElementById("ovNumero").textContent = ovNumero;
        document.getElementById("ovCliente").textContent = ov.cliente;
        document.getElementById("ovFecha").textContent = ov.fecha;

        document.getElementById("ovDetails").style.display = "block";
        document.getElementById("noOVSelected").style.display = "none";
    }

    function showOFDetails(ofNumero) {
        const of = ordenesFabricacion[ofNumero];

        
        document.getElementById("ofNumero").textContent = ofNumero;
        document.getElementById("ofCliente").textContent = of.cliente;
        document.getElementById("ofFecha").textContent = of.fecha;

        
        const partidasTable = document.getElementById("partidasTable");
        partidasTable.innerHTML = "";
        of.partidas.forEach((partida, index) => {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${partida.articulo}</td>
                    <td>${partida.descripcion}</td>
                    <td>${partida.cantidad}</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="deletePartida('${ofNumero}', ${index})">Eliminar</button>
                    </td>
                </tr>
            `;
            partidasTable.insertAdjacentHTML("beforeend", row);
        });

        document.getElementById("ofDetails").style.display = "block";
    }

    function addPartida() {
        alert("Funcionalidad de agregar partida pendiente.");
    }

    function deletePartida(ofNumero, index) {
        alert(`Eliminar la partida ${index + 1} de la Orden ${ofNumero}`);
    }
</script>
@endsection
