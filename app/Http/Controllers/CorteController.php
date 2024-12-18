<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PartidasOF;
use App\Models\OrdenFabricacion;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class CorteController extends Controller
{
    //vista
    public function index()
    {
        return view('Estaciones.cortes');
    }
    //carga de la tabla
    public function getData()
    {
        $data = DB::table('OrdenFabricacion')  
                ->join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')  
                ->select(
                    'OrdenFabricacion.id', 
                    'OrdenFabricacion.OrdenVenta_id', 
                    'OrdenFabricacion.OrdenFabricacion', 
                    'OrdenFabricacion.Articulo', 
                    'OrdenFabricacion.Descripcion', 
                    'OrdenFabricacion.CantidadTotal', 
                    'OrdenFabricacion.FechaEntregaSAP', 
                    'OrdenFabricacion.FechaEntrega', 
                    'OrdenFabricacion.created_at', 
                    'OrdenFabricacion.updated_at'
                )
                ->get();
        
       // var_dump($data);
    
        return response()->json(['data' => $data]);
    }
    //modal
    public function verDetalles($id)
    {
        $ordenFabricacion = DB::table('OrdenFabricacion')
                            ->select('OrdenFabricacion.OrdenFabricacion', 
                                        'OrdenFabricacion.FechaEntrega', 
                                        'OrdenFabricacion.Articulo', 
                                        'OrdenFabricacion.Estado')
                            ->where('OrdenFabricacion.id', $id) 
                            ->first();
                            

        if ($ordenFabricacion) {
            return response()->json(['orden_fabricacion' => $ordenFabricacion]);
        } else {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
    }
    //buscar orden de fabricacion
    public function buscarOrdenVenta(Request $request)
    {
        $query = $request->input('query'); 
        if ($query) {
            // Asegúrate de que no haya duplicados por 'OrdenFabricacion'
            $resultados = DB::table('OrdenFabricacion')
                ->select('OrdenFabricacion.id', 
                        'OrdenFabricacion.OrdenVenta_id', 
                        'OrdenFabricacion.OrdenFabricacion', 
                        'OrdenFabricacion.Articulo', 
                        'OrdenFabricacion.Descripcion', 
                        'OrdenFabricacion.CantidadTotal', 
                        'OrdenFabricacion.FechaEntregaSAP', 
                        'OrdenFabricacion.FechaEntrega', 
                        'OrdenFabricacion.created_at', 
                        'OrdenFabricacion.updated_at'
                        )
                ->where('OrdenFabricacion', 'like', "%$query%")
                //->distinct()  // Evita duplicados por la columna 'OrdenFabricacion'
                ->get();
        } else {
            $resultados = []; 
        }

        return response()->json($resultados);
    }
    public function guardarPartidasOF(Request $request)
    {
        // Validación para asegurarnos de que 'datos_partidas' es un array y contiene los campos correctos
        $request->validate([
            'datos_partidas' => 'required|array', // 'datos_partidas' debe ser un array
            'datos_partidas.*.orden_fabricacion_id' => 'required|exists:OrdenFabricacion,id', // Validar que 'orden_fabricacion_id' exista en la tabla 'OrdenFabricacion'
            'datos_partidas.*.cantidad_partida' => 'required|integer', // Asegurarse de que 'cantidad_partida' sea un número entero
            'datos_partidas.*.fecha_fabricacion' => 'required|date', // Asegurarse de que 'fecha_fabricacion' sea una fecha válida
        ]);
    
        // Guardar las partidas
        foreach ($request->datos_partidas as $partida) {
            // Crear una nueva partida en la base de datos
            PartidasOF::create([
                'orden_fabricacion_id' => $partida['orden_fabricacion_id'],
                'cantidad_partida' => $partida['cantidad_partida'],
                'fecha_fabricacion' => $partida['fecha_fabricacion'],
            ]);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Las partidas se guardaron correctamente',
        ]);
    }
    public function getDetalleOrden(Request $request)
    {
        $ordenId = $request->id;

        if (!$ordenId) {
            return response()->json([
                'success' => false,
                'message' => 'ID no proporcionado.',
            ]);
        }

        try {
            // Busca los datos exactos con el modelo configurado
            $detalle = DB::table('OrdenFabricacion')->where('id', $ordenId)->first();

            if (!$detalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró la orden de fabricación.',
                ]);
            }

            // Devuelve los datos correctamente formateados
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $detalle->id,
                    'OrdenFabricacion' => $detalle->OrdenFabricacion,
                    'Articulo' => $detalle->Articulo,
                    'Descripcion' => $detalle->Descripcion,
                    'CantidadTotal' => $detalle->CantidadTotal,
                    'FechaEntregaSAP' => $detalle->FechaEntregaSAP,
                    'FechaEntrega' => $detalle->FechaEntrega,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los detalles: ' . $e->getMessage(),
            ]);
        }
    }
    public function getCortes(Request $request)
    {
        $ordenFabricacionId = $request->id;

        // Obtiene las partidas relacionadas con la orden de fabricación
        $partidas = PartidasOF::where('orden_fabricacion_id', $ordenFabricacionId)
                            ->orderBy('created_at', 'desc') // Puedes ordenar si lo deseas
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $partidas
        ]);
    }
    public function finalizarCorte(Request $request)
    {
        // Validar que el ID exista y que la fecha sea válida
        $request->validate([
            'id' => 'required|exists:partidas_of,id', // Validar que el ID exista en la tabla
            'fecha_finalizacion' => 'required|date', // Validar que sea una fecha válida
        ]);

        // Buscar el registro basado en el ID
        $corte = PartidasOF::find($request->id);

        // Actualizar el campo FechaFinalizacion
        $corte->FechaFinalizacion = $request->fecha_finalizacion;
        $corte->save();

        // Retornar una respuesta de éxito
        return response()->json([
            'success' => true,
            'message' => 'Corte finalizado correctamente.'
        ]);
    }
    // OrdenFabricacionController.php
    public function getCantidadTotal($id)
    {
        $ordenFabricacion = OrdenFabricacion::find($id);

        if ($ordenFabricacion) {
            return response()->json(['success' => true, 'cantidad_total' => $ordenFabricacion->CantidadTotal]);
        }

        return response()->json(['success' => false, 'message' => 'Orden de fabricación no encontrada.']);
    }




    
}




