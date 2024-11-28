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

        $sql = "SELECT 
                    T1.\"ItemCode\" AS \"Articulo\", 
                    T1.\"Dscription\" AS \"Descripcion\", 
                    T2.\"PlannedQty\" AS \"Cantidad OF\",  
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

            $html = '<table class="table table-striped table-bordered">';
            $html .= '<thead>
                        <tr>
                            <th>Articulo</th>
                            <th>Descripcion</th>
                            <th>Cantidad OF</th>
                            <th>Fecha entrega OF</th>
                            <th>Orden de F.</th>
                        </tr>
                    </thead><tbody>';

            foreach ($partidas as $partida) {
                $html .= '<tr>
                            <td>' . ($partida['Articulo'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Descripcion'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Cantidad OF'] ?? 'No disponible') . '</td>
                            <td>' . (\Carbon\Carbon::parse($partida['Fecha entrega OF'])->format('d-m-Y')) . '</td>
                            <td>' . ($partida['Orden de F.'] ?? 'No disponible') . '</td>
                        </tr>';
            }
            $html .= '</tbody></table>';

            return response()->json([
                'status' => 'success',
                'message' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener las partidas: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener las partidas: ' . $e->getMessage()
            ]);
        }
    }


}

