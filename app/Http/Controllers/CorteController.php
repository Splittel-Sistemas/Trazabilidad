<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenFabricacion;
use Illuminate\Support\Facades\DB;
use App\Models\PartidasOF;

class CorteController extends Controller
{
    //vista
    public function index()
    {
        return view('Estaciones.cortes');
    }

    public function getData()
    {
        $data = DB::table('orden_fabricacion') 
                ->join('orden_venta', 'orden_fabricacion.orden_venta_id', '=', 'orden_venta.id')  
                ->select('orden_fabricacion.id', 
                         'orden_fabricacion.orden_venta_id', 
                         'orden_fabricacion.orden_fabricacion', 
                         'orden_fabricacion.fecha_entrega', 
                         'orden_fabricacion.articulo', 
                         'orden_venta.articulo as OrdenVentaArticulo')  
                ->get();
        return response()->json(['data' => $data]);  
    }
    // Método para filtrar por Orden de Venta
    public function filtroOrdenVentaData(Request $request)
    {
        $query = $request->input('query', '');  
        $startDate = $request->input('startDate', '');  
        $queryBuilder = DB::table('orden_fabricacion')
            ->join('orden_venta', 'orden_fabricacion.orden_venta_id', '=', 'orden_venta.id')
            ->select('orden_fabricacion.id', 'orden_fabricacion.orden_venta_id', 'orden_fabricacion.orden_fabricacion', 
                     'orden_fabricacion.fecha_entrega', 'orden_fabricacion.articulo', 
                     'orden_venta.articulo as OrdenVentaArticulo');            
        if (!empty($query)) {
            $queryBuilder->where('orden_fabricacion.orden_fabricacion', 'LIKE', '%' . $query . '%');
        }
        if (!empty($startDate)) {
            $queryBuilder->where('orden_fabricacion.fecha_entrega', '>=', $startDate);
        }
        $data = $queryBuilder->get();
        return response()->json(['data' => $data]);
    }
    // Método para filtrar por una fecha específica
    public function filtroFechaData(Request $request)
    {
        $fecha = $request->input('fecha', '');      
        $queryBuilder = DB::table('orden_fabricacion')
            ->join('orden_venta', 'orden_fabricacion.orden_venta_id', '=', 'orden_venta.id')
            ->select('orden_fabricacion.id', 'orden_fabricacion.orden_venta_id', 'orden_fabricacion.orden_fabricacion', 
                     'orden_fabricacion.fecha_entrega', 'orden_fabricacion.articulo', 
                     'orden_venta.articulo as OrdenVentaArticulo');         
        if (!empty($fecha)) {
            $queryBuilder->whereDate('orden_fabricacion.fecha_entrega', '=', $fecha);
        }
        $data = $queryBuilder->get();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'No hay datos disponibles para la fecha seleccionada.']);
        }
        return response()->json(['data' => $data]);
    }
    //modal
    public function verDetalles($id)
    {
        $ordenFabricacion = DB::table('orden_fabricacion')
            ->join('orden_venta', 'orden_fabricacion.orden_venta_id', '=', 'orden_venta.id')
            ->select('orden_fabricacion.*', 'orden_venta.articulo as orden_venta_articulo')
            ->where('orden_fabricacion.id', $id)
            ->first();
        if (!$ordenFabricacion) {
            return response()->json(['message' => 'Orden de fabricación no encontrada'], 404);
        }
        return response()->json([
            'orden_fabricacion' => $ordenFabricacion
        ]);
    }

    public function guardar(Request $request)
    {
        $validatedData = $request->validate([
            'ordenFabricacion' => 'required|string|max:255',
            'fechaEntrega' => 'required|date',
            'articulo' => 'required|string|max:255',
            'ordenVentaArticulo' => 'nullable|string|max:255',
            'estado' => 'required|string|max:255',
        ]);

        PartidasOF::create([
            'orden_fabricacion' => $validatedData['ordenFabricacion'],
            'fecha_entrega' => $validatedData['fechaEntrega'],
            'articulo' => $validatedData['articulo'],
            'orden_venta_articulo' => $validatedData['ordenVentaArticulo'],
            'estado' => $validatedData['estado'],
        ]);

        return response()->json(['message' => 'Datos guardados con éxito'], 200);
    }
    public function guardarCortes(Request $request)
    {
        $request->validate([
            'numCortes' => 'required|integer|min:0',
        ]);

        
        PartidasOF::create([
            'numero_cortes' => $request->numCortes,
            'fecha' => now(), 
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Cortes guardados exitosamente',
        ]);
    }

    
}




    
