<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\FuncionesGeneralesController;
use Illuminate\Support\Facades\Log;

class PlaneacionController extends Controller
{
    protected $funcionesGenerales;

    public function __construct(FuncionesGeneralesController $funcionesGenerales)
    {
        $this->funcionesGenerales = $funcionesGenerales;
    }

    public function OrdenesVActual(Request $request)
    {
        $query = $request->input('query'); 
        $fecha = $request->input('date');  
        $fechaHoy = date('Ymd');
        $fechaAyer = date('Ymd', strtotime('-1 day'));
        $fechaConsulta = $fecha ? $fecha : $fechaHoy;
        $schema = 'HN_OPTRONICS';
        $sql = 'SELECT T0."DocNum" AS "OV", T0."CardName" AS "Cliente", T0."DocDate" AS "Fecha", 
                T0."DocStatus" AS "Estado", T0."DocTotal" AS "Total" FROM ' . $schema . '.ORDR T0 
                WHERE T0."DocDate" BETWEEN \'' . $fechaAyer . '\' AND \'' . $fechaHoy . '\'';
                $params = [
                    'query' => '%' . $query . '%',  
                    'fechaAyer' => $fechaAyer,     
                    'fechaHoy' => $fechaHoy,   
                ];
        try {
            $ordenesVenta = $this->funcionesGenerales->ejecutarConsulta($sql);
            //return($ordenesVenta);

            if (empty($ordenesVenta)) {
                return view('layouts.ordenes.ordenesv', compact('ordenesVenta', 'fechaHoy', 'fechaAyer'));
                Log::info('No se encontraron órdenes para las fechas: ' . $fechaAyer . ' a ' . $fechaHoy);
                return back()->with('warning', 'No se encontraron órdenes para estas fechas.');
            }
            Log::info('Ordenes Venta:', ['ordenes' => $ordenesVenta]);
        } catch (\Exception $e) {
            Log::error('Error al obtener órdenes: ' . $e->getMessage());
            return back()->with('error', 'Error al obtener órdenes. Intenta nuevamente.');
        }
        $fechaHoy = date('d-m-Y');
        $fechaAyer = date('d-m-Y', strtotime('-1 day'));
        return view('layouts.ordenes.ordenesv', compact('ordenesVenta', 'fechaHoy', 'fechaAyer'));
    }

    public function DatosDePartida(Request $request)
    {
        $schema = 'HN_OPTRONICS';
        $ordenventa = $request->input('docNum');  
        if (empty($ordenventa)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El número de orden no fue proporcionado.'
            ]);
        }

        $ordenventa = addslashes($ordenventa); 
        $sql = "SELECT T1.\"ItemCode\" AS \"Articulo\", 
                    T1.\"Dscription\" AS \"Descripcion\", 
                    ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", 
                    T2.\"DueDate\" AS \"Fecha entrega OF\", 
                    T1.\"PoTrgNum\" AS \"Orden de F.\" 
                FROM {$schema}.\"ORDR\" T0
                INNER JOIN {$schema}.\"RDR1\" T1 ON T0.\"DocEntry\" = T1.\"DocEntry\"
                LEFT JOIN {$schema}.\"OWOR\" T2 ON T1.\"PoTrgNum\" = T2.\"DocNum\"
                WHERE T0.\"DocNum\" = '{$ordenventa}'  
                ORDER BY T1.\"VisOrder\"";

        try {
            $partidas = $this->funcionesGenerales->ejecutarConsulta($sql);
            if (empty($partidas)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontraron partidas para esta orden.'
                ]);
            }
            $html = '<div class="table-responsive table-partidas">';
            $html .= '<table class=" table-sm" id="table-source">';
            $html .= '<thead>
                        <tr>
                            <th>Orden Fab.</th>
                            <th>Artículo</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Fecha entrega</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($partidas as $index => $partida) {
                $cantidadOF = is_numeric($partida['Cantidad OF']) ? 
                            number_format($partida['Cantidad OF'], 0, '.', '') : 
                            'No disponible';
                $html .= '<tr id="row-' . $index . '" draggable="true" ondragstart="drag(event)">
                            <td>' . ($partida['Orden de F.'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Articulo'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Descripcion'] ?? 'No disponible') . '</td>
                            <td>' . $cantidadOF . '</td>
                            <td>' . (!empty($partida['Fecha entrega OF']) ? \Carbon\Carbon::parse($partida['Fecha entrega OF'])->format('d-m-Y') : 'No disponible') . '</td>
                        </tr>';
            }
            $html .= '</tbody></table></div>';
            return response()->json([
                'status' => 'success',
                'message' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener las partidas: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener las partidas. Por favor, intente más tarde.'
            ]);
        }
    }
    public function filtros(Request $request)
    {
        $fechaHoy = date('Y-m-d');
        $fechaAyer = date('Y-m-d', strtotime('-1 day'));
        $startDate = $request->input('startDate', $fechaAyer);
        $endDate = $request->input('endDate', $fechaHoy);
        $query = $request->input('query', '');

        if (strtotime($startDate) > strtotime($endDate)) {
            return response()->json([
                'status' => 'error',
                'message' => 'La fecha de inicio no puede ser posterior a la fecha de fin.'
            ]);
        }
        $schema = 'HN_OPTRONICS';
        $startDateFormatted = date('Y-m-d', strtotime($startDate));
        $endDateFormatted = date('Y-m-d', strtotime($endDate));

        $sql = 'SELECT T0."DocNum" AS "OV", T0."CardName" AS "Cliente", T0."DocDate" AS "Fecha", 
                T0."DocStatus" AS "Estado", T0."DocTotal" AS "Total" 
                FROM ' . $schema . '.ORDR T0 
                WHERE T0."DocDate" BETWEEN \'' . $startDateFormatted . '\' AND \'' . $endDateFormatted . '\'';

        if (!empty($query)) {
            $sql .= ' AND T0."DocNum" LIKE \'%' . $query . '%\'';
        }

        try {
            $ordenesVenta = $this->funcionesGenerales->ejecutarConsulta($sql);
            if (empty($ordenesVenta)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontraron órdenes para estas fechas.'
                ]);
            }
            $tablaOrdenes = '';
            foreach ($ordenesVenta as $index => $orden) {
                $tablaOrdenes .= '<tr class="table-light" id="details' . $index . 'cerrar" style="cursor: pointer;" draggable="true" ondragstart="drag(event)" data-bs-toggle="collapse" data-bs-target="#details' . $index . '" aria-expanded="false" aria-controls="details' . $index . '">
                                    <td onclick="loadContent(\'details' . $index . '\', ' . $orden['OV'] . ')">
                                        ' . $orden['OV'] . " - " . $orden['Cliente'] . '
                                    </td>
                                </tr>
                                <tr id="details' . $index . '" class="collapse">
                                    <td class="table-border" id="details' . $index . 'llenar">
                                        <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                    </td>
                                </tr>';
            }
            return response()->json([
                'status' => 'success',
                'data' => $tablaOrdenes,
                'fechaHoy' => $fechaHoy,
                'fechaAyer' => $fechaAyer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener órdenes. Detalles: ' . $e->getMessage()
            ]);
        }
    }
    public function filtro(Request $request)
    {
        $query = $request->input('query', ''); // Capturar el valor del filtro por orden de venta
    
        $schema = 'HN_OPTRONICS';
    
        $sql = 'SELECT T0."DocNum" AS "OV", T0."CardName" AS "Cliente", T0."DocDate" AS "Fecha", 
                T0."DocStatus" AS "Estado", T0."DocTotal" AS "Total" 
                FROM ' . $schema . '.ORDR T0';
    
        if (!empty($query)) {
            $sql .= ' WHERE T0."DocNum" LIKE \'%' . $query . '%\'';
        }
    
        try {
            $ordenesVenta = $this->funcionesGenerales->ejecutarConsulta($sql);
    
            if (empty($ordenesVenta)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontraron órdenes con ese número.'
                ]);
            }
    
            // Construir la tabla en HTML
            $tablaOrdenes = '';
            foreach ($ordenesVenta as $index => $orden) {
                $tablaOrdenes .= '<tr class="table-light" id="details' . $index . 'cerrar" style="cursor: pointer;" draggable="true" ondragstart="drag(event)" data-bs-toggle="collapse" data-bs-target="#details' . $index . '" aria-expanded="false" aria-controls="details' . $index . '">
                                    <td onclick="loadContent(\'details' . $index . '\', ' . $orden['OV'] . ')">
                                        ' . $orden['OV'] . " - " . $orden['Cliente'] . '
                                    </td>
                                  </tr>
                                  <tr id="details' . $index . '" class="collapse">
                                    <td class="table-border" id="details' . $index . 'llenar">
                                        <!-- Aquí se llenarán los detalles de la orden cuando el usuario haga clic -->
                                    </td>
                                  </tr>';
            }
    
            return response()->json([
                'status' => 'success',
                'data' => $tablaOrdenes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener órdenes. Detalles: ' . $e->getMessage()
            ]);
        }
    }
}    