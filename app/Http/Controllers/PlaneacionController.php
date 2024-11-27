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
                T0."DocStatus" AS "Estado", T0."DocTotal" AS "Total" 
                FROM ' . $schema . '.ORDR T0 
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
        $query = $request->input('query'); 
        $fecha = $request->input('date');  
        $fechaHoy = date('Ymd');
        $fechaAyer = date('Ymd', strtotime('-1 day'));
        $schema = 'HN_OPTRONICS';
        $ordenventa = $request->input('docNum');  

        if (empty($ordenventa)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El número de orden no fue proporcionado.'
            ]);
        }

        $sql = 'SELECT 
            T0."DocNum" AS "OV", 
            T1."ItemCode" AS "No. Parte", 
            T1."Dscription" AS "Descripción", 
            CASE WHEN T2."U_TcktCssftion" = \'01\' THEN \'Fabricacion\'
            WHEN T2."U_TcktCssftion" = \'02\' THEN \'Internacional\' 
            WHEN T2."U_TcktCssftion" = \'03\' THEN \'Nacional\' END AS "Clasificacion Ticket",
            T0."DocDate" AS "Fecha",
            T0."CardName" AS "Cliente"  
            FROM ' . $schema . '.ORDR T0 
            INNER JOIN ' . $schema . '.RDR1 T1 ON T1."DocEntry" = T0."DocEntry" 
            INNER JOIN ' . $schema . '.OITM T2 ON T2."ItemCode" = T1."ItemCode" 
            WHERE T0."DocNum" = \'' . $ordenventa . '\'
            ORDER BY T0."DocNum" LIMIT 1';

        try {
            // Ejecutar la consulta
            $partidas = $this->funcionesGenerales->ejecutarConsulta($sql, ['docNum' => $ordenventa]);

            // Verificar que se hayan encontrado partidas
            if (empty($partidas)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontraron partidas para esta orden.'
                ]);
            }

            // Si hay partidas, formateamos los datos
            $partidas = collect($partidas)->map(function($item) {
                return [
                    'NoParte' => $item['No. Parte'] ?? 'No disponible',
                    'Descripcion' => $item['Descripción'] ?? 'No disponible',
                    'ClasificacionTicket' => $item['Clasificacion Ticket'] ?? 'No disponible',
                    'Fecha' => $item['Fecha'] ?? 'No disponible',
                    'Cliente' => $item['Cliente'] ?? 'No disponible'
                ];
            });

            
            return response()->json([
                'status' => 'success',
                'html' => $partidas
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

