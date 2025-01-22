<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PartidasOF;
use App\Models\OrdenFabricacion;
use App\Models\Partidas;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use TCPDF;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class CorteController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
    
        // Verificar si el usuario tiene el permiso 'Vista Cortes'
        if ($user->hasPermission('Vista Cortes')) {
            // Obtener las órdenes de fabricación con sus datos relacionados
            $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
                ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
                ->select(
                    'OrdenFabricacion.id',
                    'OrdenFabricacion.OrdenFabricacion',
                    'OrdenFabricacion.Articulo',
                    'OrdenFabricacion.Descripcion',
                    'OrdenFabricacion.CantidadTotal',
                    'OrdenFabricacion.FechaEntregaSAP',
                    'OrdenFabricacion.FechaEntrega',
                    DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
                )
                ->groupBy(
                    'OrdenFabricacion.id',
                    'OrdenFabricacion.OrdenFabricacion',
                    'OrdenFabricacion.Articulo',
                    'OrdenFabricacion.Descripcion',
                    'OrdenFabricacion.CantidadTotal',
                    'OrdenFabricacion.FechaEntregaSAP',
                    'OrdenFabricacion.FechaEntrega'
                )
                ->get();
    
            // Asignar estatus a las órdenes
            $ordenesFabricacion->transform(function ($item) {
                $item->estatus = $item->suma_cantidad_partida < $item->CantidadTotal ? 'abierto' : 'cerrado';
                return $item;
            });
    
            // Filtrar órdenes con estatus "abierto"
            $ordenesAbiertas = $ordenesFabricacion->filter(function ($orden) {
                return strtolower($orden->estatus) === 'abierto';
            });
    
            // Si es una solicitud AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json($ordenesAbiertas->values());
            }
    
            // Devolver la vista con los datos procesados
            return view('Areas.Cortes', compact('ordenesAbiertas', 'ordenesFabricacion'));
        }
    
        // Redirigir a la URL externa si no tiene el permiso
        return redirect()->away('https://assets-blog.hostgator.mx/wp-content/uploads/2018/10/paginas-de-error-hostgator.webp');
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
    public function guardarPartidasOF(Request $request)
    {
        // Validar que los datos recibidos sean correctos
        $request->validate([
            'datos_partidas' => 'required|array', // 'datos_partidas' debe ser un array
            'datos_partidas.*.orden_fabricacion_id' => 'required|exists:OrdenFabricacion,id', 
            'datos_partidas.*.cantidad_partida' => 'required|integer|min:1', 
            'datos_partidas.*.fecha_fabricacion' => 'required|date_format:Y-m-d H:i:s',

        ]);
    
        // Validar y guardar las partidas
        foreach ($request->datos_partidas as $partida) {
            // Buscar la orden de fabricación correspondiente
            $ordenFabricacion = OrdenFabricacion::find($partida['orden_fabricacion_id']);
    
            // Obtener la suma actual de las partidas registradas para esta OrdenFabricacion_id
            $sumaActual = PartidasOF::where('OrdenFabricacion_id', $partida['orden_fabricacion_id'])
                ->sum('cantidad_partida');
    
            // Calcular la nueva suma incluyendo la cantidad que se desea ingresar
            $nuevaSuma = $sumaActual + $partida['cantidad_partida'];
    
            // Verificar si la nueva suma excede la cantidad total permitida
            if ($nuevaSuma > $ordenFabricacion->CantidadTotal) {
                return response("La cantidad total acumulada ({$nuevaSuma}) excede la cantidad total permitida ({$ordenFabricacion->CantidadTotal}) para la Orden.", 422);
            }
    
            // Crear la partida si pasa la validación
            PartidasOF::create([
                'OrdenFabricacion_id' => $partida['orden_fabricacion_id'],
                'cantidad_partida' => $partida['cantidad_partida'],
                'fecha_fabricacion' => $partida['fecha_fabricacion'],
            ]);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Las partidas se guardaron correctamente',
        ]);
    }
    public function getCortesInfo($id)
    {
        try {
            // Sumar los cortes registrados de la tabla `partidas_of`
            $sumaCortes = DB::table('partidasof')
                ->where('OrdenFabricacion_id', $id) 
                ->sum('cantidad_partida');

            // Obtener información de la orden de fabricación
            $ordenFabricacion = DB::table('ordenfabricacion')
                ->where('id', $id)
                ->first();

            if (!$ordenFabricacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orden de fabricación no encontrada.',
                ]);
            }

            return response()->json([
                'success' => true,
                'CantidadTotal' => $ordenFabricacion->CantidadTotal, 
                'cortes_registrados' => $sumaCortes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la información de los cortes.',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function getCortes(Request $request)
    {
        $ordenFabricacionId = $request->id;
    
        // Obtiene las partidas relacionadas con la orden de fabricación
        $partidas = PartidasOF::where('OrdenFabricacion_id', $ordenFabricacionId)
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Verifica si el usuario tiene el permiso 'CorteEdit'
        $userCanEdit = Auth::user()->hasPermission('CompletadosEdit');
    
        // Formatea los datos antes de enviarlos
        $data = $partidas->map(function ($partida) {
            return [
                'id' => $partida->id,
                'cantidad_partida' => $partida->cantidad_partida,
                'fecha_fabricacion' => $partida->fecha_fabricacion
                    ? Carbon::parse($partida->fecha_fabricacion)->format('d-m-Y H:i')
                    : null,
                'FechaFinalizacion' => $partida->FechaFinalizacion
                    ? Carbon::parse($partida->FechaFinalizacion)->format('d-m-Y H:i')
                    : null,
            ];
        });
    
        return response()->json([
            'success' => true,
            'data' => $data,
            'userCanEdit' => $userCanEdit, // Se agrega la variable para el permiso
        ]);
    }
    public function finalizarCorte(Request $request)
    {
        // Validar que el ID exista y que la fecha sea válida
        $request->validate([
            'id' => 'required|exists:partidasof,id', 
            'fecha_finalizacion' => 'required|date', 
        ]);

        // Buscar el registro basado en el ID
        $corte = PartidasOF::find($request->id);

        // Actualizar el campo FechaFinalizacion
        $corte->FechaFinalizacion = $request->fecha_finalizacion;
        $corte->save();

        // Retornar una respuesta de éxito
        return response()->json([
            'success' => true,
            
        ]);
        
    }
    public function generarPDF(Request $request)
    {
        try {
            $partidaId = $request->input('id');
            if (!$partidaId) {
                throw new \Exception('ID no recibido');
            }

            // Buscar la partida por ID
            $partida = PartidasOF::with('ordenFabricacion')->find($partidaId);

            if (is_null($partida) || is_null($partida->ordenFabricacion)) {
                throw new \Exception('No se encontraron datos para este ID.');
            }

            // Obtener la orden de fabricación relacionada
            $ordenFabricacion = $partida->ordenFabricacion;

            // Calcular el inicio del contador para esta OrdenFabricacion_id
            $numeroInicial = PartidasOF::where('OrdenFabricacion_id', $partida->OrdenFabricacion_id)
                ->where('id', '<', $partidaId)
                ->sum('cantidad_partida') + 1;

            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            $contador = $numeroInicial; 

            for ($i = 1; $i <= $partida->cantidad_partida; $i++) {
                $partidasData[] = [
                    'cantidad' => $contador, 
                    'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
                    'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                ];
                $contador++; // Incrementar el contador
            }

            // Invertir el array de partidas
            $partidasData = array_reverse($partidasData);

            // Crear PDF
            $pdf = new TCPDF();
            $pdf->SetMargins(5, 5, 5);
            $pdf->AddPage();

            // Título del PDF
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 10, 'Orden de Fabricación: ' . strip_tags($ordenFabricacion->OrdenFabricacion), 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 5, 'Descripción: ' . strip_tags($ordenFabricacion->Descripcion), 0, 1, 'C');
            $pdf->Ln(5);

            // Generar contenido para cada partida
            foreach ($partidasData as $partida) {
                $content = 
                    'No: ' . strip_tags($partida['cantidad']) . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['orden_fabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";

                $startY = $pdf->GetY();
                $rectWidth = 80;
                $rectHeight = 15;
                $pdf->Rect(10, $startY, $rectWidth, $rectHeight, 'D');
                $pdf->SetXY(12, $startY + 1);
                $pdf->SetFont('helvetica', '', 6);
                $pdf->MultiCell($rectWidth - 4, 5, strip_tags($content), 0, 'L', 0, 1);
                $pdf->SetFont('helvetica', '', 8);
            }

            ob_end_clean();

            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_' . $partidaId . '.pdf', 'D');
        } catch (\Exception $e) {
            Log::error('Error al generar PDF: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function PDFCondicion(Request $request)
    {
        try {
            //dd($request->all());
            
            $request->validate([
                'desde_no' => 'required|integer|min:1',
                'hasta_no' => 'required|integer|min:1|gte:desde_no',
            ]);

            
            $desde = $request->input('desde_no');
            $hasta = $request->input('hasta_no');

            // Obtener la orden de fabricación (solo si necesitas los datos de la orden de fabricación)
            $ordenFabricacion = OrdenFabricacion::find($request->input('id'));

            if (!$ordenFabricacion) {
                throw new \Exception('No se encontró la orden de fabricación');
            }

            // Crear las etiquetas con el rango especificado
            $partidasData = [];
            $contadorPartida = $desde; 

            // Generar tantas etiquetas como el rango lo indique
            for ($i = $desde; $i <= $hasta; $i++) {
                $partidasData[] = [
                    'no' => $contadorPartida,
                    'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
                    'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion,
                ];
                $contadorPartida++;
            }

            // Crear el PDF usando TCPDF o domPDF
            $pdf = new TCPDF();
            $pdf->SetMargins(5, 5, 5); 
            $pdf->AddPage();

            // Título de la orden de fabricación
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 10, 'Orden de Fabricación: ' . strip_tags($ordenFabricacion->OrdenFabricacion), 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 5, 'Descripción: ' . strip_tags($ordenFabricacion->Descripcion), 0, 1, 'C');
            $pdf->Ln(5);  // Salto de línea

            // Datos de las partidas generadas
            foreach ($partidasData as $partida) {
                $content = 
                    'No: ' . strip_tags($partida['no']) . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['orden_fabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";

                // Obtener la posición inicial y calcular la altura de la celda
                $startY = $pdf->GetY();

                // Ajustar el tamaño del rectángulo
                $rectWidth = 80;  // Ancho del rectángulo
                $rectHeight = 15;  // Altura del rectángulo ajustada
                $pdf->Rect(10, $startY, $rectWidth, $rectHeight, 'D');  // Dibujar el rectángulo

                // Colocar el contenido dentro del cuadro con fuente más pequeña
                $pdf->SetXY(12, $startY + 1);  
                $pdf->SetFont('helvetica', '', 6);  
                $pdf->MultiCell($rectWidth - 4, 5, strip_tags($content), 0, 'L', 0, 1);  
            }

            // Limpiar cualquier salida previa
            ob_end_clean();

            // Generar el PDF y forzar la descarga
            return $pdf->Output('etiquetas_' . $ordenFabricacion->OrdenFabricacion . '.pdf', 'D');
        } catch (\Exception $e) {
            // Registrar el error y devolver mensaje de error
            Log::error('Error al generar PDF: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function MostarInformacion(Request $request)
    {
        $partidaId = $request->input('id');
    
        // Buscar la partida por ID
        $partida = PartidasOF::with('ordenFabricacion')->find($partidaId);
    
        if (is_null($partida) || is_null($partida->ordenFabricacion)) {
            return response()->json(['error' => 'No se encontraron datos para este ID.']);
        }
    
        // Obtener la orden de fabricación relacionada
        $ordenFabricacion = $partida->ordenFabricacion;
    
        // Calcular el inicio del contador para esta OrdenFabricacion_id
        $numeroInicial = PartidasOF::where('OrdenFabricacion_id', $partida->OrdenFabricacion_id)
            ->where('id', '<', $partidaId)
            ->sum('cantidad_partida') + 1;
    
        // Preparar las partidas relacionadas solo para la partida seleccionada
        $partidasData = [];
        $contador = $numeroInicial; 
    
        for ($i = 1; $i <= $partida->cantidad_partida; $i++) {
            $partidasData[] = [
                'cantidad' => $contador, 
                'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
                'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
            ];
            $contador++; // Incrementar el contador
        }
    
        // Preparar la respuesta con la información de la partida seleccionada
        $response = [
            'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
            'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
            'cantidad_partida' => $partida->cantidad_partida ?? 0,
            'partidas' => $partidasData, 
        ];
    
        return response()->json($response);
    }
    public function filtrarPorFecha(Request $request)
    {
        
        $fecha = $request->input('fecha');
        
        if (!$fecha) {
            return response()->json(['error' => 'La fecha es requerida'], 400);
        }

        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
            )
            ->whereDate('OrdenFabricacion.FechaEntrega', $fecha)
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->get();
            $ordenesFabricacion->transform(function ($item) {
                $cantidadTotal = $item->CantidadTotal;
                $sumaCantidadPartida = $item->suma_cantidad_partida;
        
                if ($sumaCantidadPartida == 0) {
                    $item->estatus = 'Abierta';
                } elseif ($sumaCantidadPartida < $cantidadTotal) {
                    $item->estatus = 'Abierta';
                } else {
                    $item->estatus = 'Cerrada';
                }
        
                return $item;
            });

        return response()->json($ordenesFabricacion);
    }
    public function Completado(Request $request)
    {
        $today = date('Y-m-d');
        $semna = date('Y-m-d', strtotime('-1 week'));
    
        // Consulta de órdenes de fabricación filtradas por la fecha actual
        $ordenesFabricacion = $this->filtroOvFecha($today, $semna);
        
        // Agregar estatus calculado
        $ordenesFabricacion->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;
    
            if ($sumaCantidadPartida == 0) {
                $item->estatus = 'Abierta';
            } elseif ($sumaCantidadPartida < $cantidadTotal) {
                $item->estatus = 'Abierta';
            } else {
                $item->estatus = 'Cerrada';
            }
    
            return $item;
        });
    
        // Filtrar los registros según los estatus
        $ordenesCompletadas = $ordenesFabricacion->filter(function ($orden) {
            return strtolower($orden->estatus) === 'cerrada';
        });
        
        // Convertir la colección a un arreglo y devolverlo
        return response()->json($ordenesCompletadas->values()->toArray());
    }
    
    public function filtroOvFecha($today, $semna)
    {
        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                
                
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
            )

            ->where('OrdenFabricacion.FechaEntrega','<=', $today) 
            ->where('OrdenFabricacion.FechaEntrega','>=', $semna)
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->get();
            return $ordenesFabricacion;
    }
   public function cerradas($today, $semana)
   {
   }
   public function filtrarPorFechaC(Request $request)
    {
        $fecha = $request->input('fecha');
        
        if (!$fecha) {
            return response()->json(['error' => 'La fecha es requerida'], 400);
        }

        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida'),
                DB::raw('CASE 
                    WHEN IFNULL(SUM(partidasof.cantidad_partida), 0) < OrdenFabricacion.CantidadTotal THEN "Abierto"
                    ELSE "Cerrado"
                END as estatus')
            )
            ->whereDate('OrdenFabricacion.FechaEntrega', $fecha)
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->having('estatus', 'Cerrado') // Filtrar directamente en la consulta
            ->get();

        return response()->json($ordenesFabricacion);
    }
   public function eliminarCorte(Request $request)
    {
        $corteId = $request->id;

        if (!$corteId) {
            return response()->json([
                'success' => false,
                'message' => 'ID no proporcionado.',
            ]);
        }

        try {
            // Elimina el registro de la tabla 'partidasof' donde el id coincida con el proporcionado
            $corte = DB::table('partidasof')->where('id', $corteId)->first();

            if (!$corte) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el corte.',
                ]);
            }

            DB::table('partidasof')->where('id', $corteId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Corte eliminado correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el corte: ' . $e->getMessage(),
            ]);
        }
  }
public function eliminarCorte1(Request $request)
 {
        $corteId = $request->id;

        if (!$corteId) {
            return response()->json([
                'success' => false,
                'message' => 'ID no proporcionado.',
            ]);
        }

        try {
            // Elimina el registro de la tabla 'partidasof' donde el id coincida con el proporcionado
            $corte = DB::table('partidasof')->where('id', $corteId)->first();

            if (!$corte) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el corte.',
                ]);
            }

            DB::table('partidasof')->where('id', $corteId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Corte eliminado correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el corte: ' . $e->getMessage(),
            ]);
        }
 }
 public function buscar(Request $request)
{
    $query = Producto::query();

    // Filtrar por el campo de búsqueda
    if ($request->has('buscar') && $request->input('buscar') != '') {
        $busqueda = $request->input('buscar');
        
        // Realizar la búsqueda en las columnas específicas
        $query->where(function($q) use ($busqueda) {
            $q->where('orden_fabricacion', 'like', '%' . $busqueda . '%')
              ->orWhere('articulo', 'like', '%' . $busqueda . '%')
              ->orWhere('descripcion', 'like', '%' . $busqueda . '%')
              ->orWhere('cantidad_total', 'like', '%' . $busqueda . '%')
              ->orWhere('fecha', 'like', '%' . $busqueda . '%')
              ->orWhere('estatus', 'like', '%' . $busqueda . '%');
        });
    }

    $resultados = $query->get();

    return view('productos.index', compact('resultados'));
}

    




    /*
    public function index()
    {
        // Fecha actual
        //$today = date('Y-m-d');
        //$semna = date('Y-m-d', strtotime('-1 week'));
        // Consulta de órdenes de fabricación filtradas por la fecha actual
        $ordenesFabricacion=$this->filtroOvFechaTodas();
        // Agregar estatus calculado
        $ordenesFabricacion->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;
    
            if ($sumaCantidadPartida == 0) {
                $item->estatus = 'Sin cortes';
            } elseif ($sumaCantidadPartida < $cantidadTotal) {
                $item->estatus = 'En proceso';
            } else {
                $item->estatus = 'Completado';
            }
    
            return $item;
        });

         // Filtrar los registros según los estatus
        $ordenesSinCorteYEnProceso = $ordenesFabricacion->filter(function ($orden) {
            return in_array($orden->estatus, ['Sin Corte', 'En Proceso']);
        });
        
    
        return view('Areas.Cortes', compact('ordenesFabricacion'));
    }

    public function SinCortesProceso(Request $request)
    {
        //$today = date('Y-m-d');
        //$semna = date('Y-m-d', strtotime('-1 week'));
    
        // Consulta de órdenes de fabricación filtradas por la fecha actual
        $ordenesFabricacion = $this->filtroOvFechaTodas();
    
        // Agregar estatus calculado
        $ordenesFabricacion->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;
    
            if ($sumaCantidadPartida == 0) {
                $item->estatus = 'Sin cortes';
            } elseif ($sumaCantidadPartida < $cantidadTotal) {
                $item->estatus = 'En proceso';
            } else {
                $item->estatus = 'Completado';
            }
    
            return $item;
        });
    
        // Filtrar los registros según los estatus
        $ordenesSinCorteYEnProceso = $ordenesFabricacion->filter(function ($orden) {
            return in_array($orden->estatus, ['Sin cortes', 'En proceso']);
        });
    
        // Convertir la colección a un arreglo
        return response()->json($ordenesSinCorteYEnProceso->values()->toArray());
    }
    
    public function Completado(Request $request)
    {
       ;
        $today = date('Y-m-d');
        $semna = date('Y-m-d', strtotime('-1 week'));
    
        // Consulta de órdenes de fabricación filtradas por la fecha actual
        $ordenesFabricacion = $this->filtroOvFecha($today, $semna);
        
    
        // Agregar estatus calculado
        $ordenesFabricacion->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;
    
            if ($sumaCantidadPartida == 0) {
                $item->estatus = 'Sin cortes';
            } elseif ($sumaCantidadPartida < $cantidadTotal) {
                $item->estatus = 'En proceso';
            } else {
                $item->estatus = 'Completado';
            }
    
            return $item;
        });
    
        // Filtrar los registros según los estatus
        $ordenesSinCorteYEnProceso = $ordenesFabricacion->filter(function ($orden) {
            return in_array($orden->estatus, ['Completado']);
        });
        
    
        // Convertir la colección a un arreglo
        return response()->json($ordenesSinCorteYEnProceso->values()->toArray());
    }
    
    public function filtroOvFechaTodas()
    {
        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
            )
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->get();

        return $ordenesFabricacion;
    }

    public function filtroOvFecha($today, $semna)
    {
        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                
                
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
            )

            ->where('OrdenFabricacion.FechaEntrega','<=', $today) 
            ->where('OrdenFabricacion.FechaEntrega','>=', $semna)
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->get();
            return $ordenesFabricacion;
    }
    public function filtrarPorFecha(Request $request)
    {
        
        $fecha = $request->input('fecha');
        
        if (!$fecha) {
            return response()->json(['error' => 'La fecha es requerida'], 400);
        }

        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
            )
            ->whereDate('OrdenFabricacion.FechaEntrega', $fecha)
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->get();
            $ordenesFabricacion->transform(function ($item) {
                $cantidadTotal = $item->CantidadTotal;
                $sumaCantidadPartida = $item->suma_cantidad_partida;
        
                if ($sumaCantidadPartida == 0) {
                    $item->estatus = 'Sin cortes';
                } elseif ($sumaCantidadPartida < $cantidadTotal) {
                    $item->estatus = 'En proceso';
                } else {
                    $item->estatus = 'Completado';
                }
        
                return $item;
            });

        return response()->json($ordenesFabricacion);
    }
    public function fechaCompletado(Request $request){
         
        $fecha = $request->input('fecha');
        
        if (!$fecha) {
            return response()->json(['error' => 'La fecha es requerida'], 400);
        }

        $ordenesFabricacion = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->select(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida')
            )
            ->whereDate('OrdenFabricacion.FechaEntrega', $fecha)
            ->groupBy(
                'OrdenFabricacion.id',
                'OrdenFabricacion.OrdenFabricacion',
                'OrdenFabricacion.Articulo',
                'OrdenFabricacion.Descripcion',
                'OrdenFabricacion.CantidadTotal',
                'OrdenFabricacion.FechaEntregaSAP',
                'OrdenFabricacion.FechaEntrega'
            )
            ->get();
            $ordenesFabricacion->transform(function ($item) {
                $cantidadTotal = $item->CantidadTotal;
                $sumaCantidadPartida = $item->suma_cantidad_partida;
        
                if ($sumaCantidadPartida == 0) {
                    $item->estatus = 'Sin cortes';
                } elseif ($sumaCantidadPartida < $cantidadTotal) {
                    $item->estatus = 'En proceso';
                } else {
                    $item->estatus = 'Completado';
                }
        
                return $item;
            });

        return response()->json($ordenesFabricacion);
    }
 
    public function getData(Request $request)
    {
        $limit = $request->input('length', 10); // Número de registros por página
        $start = $request->input('start', 0);  // Índice del primer registro
        $searchValue = $request->input('search.value', ''); // Valor del filtro de búsqueda

        // Consulta para obtener los datos de OrdenFabricacion
        $query = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
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
                'OrdenFabricacion.updated_at',
                DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida') // Suma de las partidas
            )
            ->groupBy(
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
            );

        // Total de registros sin filtrar
        $totalRecords = $query->count();

        // Aplicar filtro de búsqueda
        if (!empty($searchValue)) {
            $query->where('OrdenFabricacion.Articulo', 'like', '%' . $searchValue . '%');
        }

        // Total de registros filtrados
        $totalFiltered = $query->count();

        // Obtener los registros paginados
        $data = $query->skip($start)->take($limit)->get();

        // Transformar los datos para agregar el estatus
        $data->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;

            // Obtener las partidas relacionadas
            $partidas = DB::table('partidasof')
                ->where('OrdenFabricacion_id', $item->id)
                ->get();

            // Verificar si alguna partida tiene 'FechaFinalizar' en null
            $pendientesFecha = $partidas->firstWhere('FechaFinalizar', null);
            if ($pendientesFecha) {
                $estatus = 'En proceso';
            } elseif ($sumaCantidadPartida >= $cantidadTotal) {
                $estatus = 'Completado';
            } else {
                $estatus = 'En proceso';
            }

            $item->estatus = $estatus; // Asignar el estatus al elemento
            return $item;
        });

        // Formato de respuesta para DataTables
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
        
    }
    public function cambiarEstatus(Request $request)
    {
        $id = $request->input('id');
        $nuevoEstatus = $request->input('estatus');
        
        // Depuración: log del estatus recibido
        Log::debug('Estatus recibido:', ['estatus' => $nuevoEstatus]);

        $estatusValidos = ['Completado', 'En proceso', 'Sin cortes'];
        if (!in_array($nuevoEstatus, $estatusValidos)) {
            return response()->json(['error' => 'Estatus inválido'], 400);
        }

        // Buscar la orden de fabricación
        $orden = OrdenFabricacion::find($id);
        if (!$orden) {
            return response()->json(['error' => 'Orden no encontrada'], 404);
        }

        // Transformación del estatus según la lógica
        $cantidadTotal = $orden->CantidadTotal;
        $sumaCantidadPartida = $orden->suma_cantidad_partida;

        if ($sumaCantidadPartida == 0) {
            $orden->estatus = 'Sin cortes';
        } elseif ($sumaCantidadPartida < $cantidadTotal) {
            $orden->estatus = 'En proceso';
        } else {
            $orden->estatus = 'Completado';
        }

        // Regresar el nuevo estatus actualizado a la vista
        return response()->json([
            'message' => 'Estatus actualizado correctamente',
            'estatus' => $orden->estatus, // Devuelve el estatus calculado
        ]);
    }
    public function actualizarTablasecundaria()
    {
        $ordenesFabricacion = $this->index()->ordenesFabricacion; // Reutilizar lógica de index
        return response()->json($ordenesFabricacion);
    }
    public function actualizarTabla()
    {
        $ordenesFabricacion = $this->index()->ordenesFabricacion; // Reutilizar lógica de index
        return response()->json($ordenesFabricacion);
    }
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
    public function buscarOrdenVenta(Request $request)
    {
        $query = $request->input('query');
        $fechaHaceUnaSemana = $request->input('fechaHaceUnaSemana'); // Obtiene la fecha de hace una semana si está presente
    
        $queryBuilder = DB::table('OrdenFabricacion')
            ->select('OrdenFabricacion.id', 
                    'OrdenFabricacion.OrdenVenta_id', 
                    'OrdenFabricacion.OrdenFabricacion', 
                    'OrdenFabricacion.Articulo', 
                    'OrdenFabricacion.Descripcion', 
                    'OrdenFabricacion.CantidadTotal', 
                    'OrdenFabricacion.FechaEntregaSAP', 
                    'OrdenFabricacion.FechaEntrega', 
                    'OrdenFabricacion.created_at', 
                    'OrdenFabricacion.updated_at',
                    DB::raw('IFNULL(SUM(partidasof.cantidad_partida), 0) as suma_cantidad_partida'))
            ->leftJoin('partidasof', 'OrdenFabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->groupBy('OrdenFabricacion.id', 
                    'OrdenFabricacion.OrdenVenta_id', 
                    'OrdenFabricacion.OrdenFabricacion', 
                    'OrdenFabricacion.Articulo', 
                    'OrdenFabricacion.Descripcion', 
                    'OrdenFabricacion.CantidadTotal', 
                    'OrdenFabricacion.FechaEntregaSAP', 
                    'OrdenFabricacion.FechaEntrega', 
                    'OrdenFabricacion.created_at', 
                    'OrdenFabricacion.updated_at');
    
        // Si hay una consulta de búsqueda, aplica el filtro
        if ($query) {
            $queryBuilder->where('OrdenFabricacion.OrdenFabricacion', 'like', "%$query%");
        }
    
        // Si fechaHaceUnaSemana está presente, filtra las órdenes desde hace una semana
        if ($fechaHaceUnaSemana) {
            $queryBuilder->whereDate('OrdenFabricacion.created_at', '>=', $fechaHaceUnaSemana);
        }
    
        $resultados = $queryBuilder->get();
       
        $resultados->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;
    
            // Lógica para determinar el estatus
            if ($sumaCantidadPartida == 0) {
                $item->estatus = 'Sin cortes';
            } elseif ($sumaCantidadPartida < $cantidadTotal) {
                $item->estatus = 'En proceso';
            } else {
                $item->estatus = 'Completado';
            }
    
            return $item;
        });
    
        // Devolver los resultados con estatus calculado
        return response()->json($resultados);
    }
    public function guardarPartidasOF(Request $request)
    {
        // Validar que los datos recibidos sean correctos
        $request->validate([
            'datos_partidas' => 'required|array', // 'datos_partidas' debe ser un array
            'datos_partidas.*.orden_fabricacion_id' => 'required|exists:OrdenFabricacion,id', 
            'datos_partidas.*.cantidad_partida' => 'required|integer|min:1', 
            'datos_partidas.*.fecha_fabricacion' => 'required|date', 
        ]);
    
        // Validar y guardar las partidas
        foreach ($request->datos_partidas as $partida) {
            // Buscar la orden de fabricación correspondiente
            $ordenFabricacion = OrdenFabricacion::find($partida['orden_fabricacion_id']);
    
            // Obtener la suma actual de las partidas registradas para esta OrdenFabricacion_id
            $sumaActual = PartidasOF::where('OrdenFabricacion_id', $partida['orden_fabricacion_id'])
                ->sum('cantidad_partida');
    
            // Calcular la nueva suma incluyendo la cantidad que se desea ingresar
            $nuevaSuma = $sumaActual + $partida['cantidad_partida'];
    
            // Verificar si la nueva suma excede la cantidad total permitida
            if ($nuevaSuma > $ordenFabricacion->CantidadTotal) {
                return response("La cantidad total acumulada ({$nuevaSuma}) excede la cantidad total permitida ({$ordenFabricacion->CantidadTotal}) para la Orden.", 422);
            }
    
            // Crear la partida si pasa la validación
            PartidasOF::create([
                'OrdenFabricacion_id' => $partida['orden_fabricacion_id'],
                'cantidad_partida' => $partida['cantidad_partida'],
                'fecha_fabricacion' => $partida['fecha_fabricacion'],
            ]);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Las partidas se guardaron correctamente',
        ]);
    }
    public function DetallesCompletado(Request $request)
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
        $partidas = PartidasOF::where('OrdenFabricacion_id', $ordenFabricacionId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Formatea los datos antes de enviarlos
        $data = $partidas->map(function ($partida) {
            return [
                'id' => $partida->id,
                'cantidad_partida' => $partida->cantidad_partida,
                'fecha_fabricacion' => Carbon::parse($partida->fecha_fabricacion)->format('d-m-Y'), 
                'FechaFinalizacion' => $partida->FechaFinalizacion 
                    ? Carbon::parse($partida->FechaFinalizacion)->format('d-m-Y') 
                    : null, 
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    } 
    public function finalizarCorte(Request $request)
    {
        // Validar que el ID exista y que la fecha sea válida
        $request->validate([
            'id' => 'required|exists:partidasof,id', 
            'fecha_finalizacion' => 'required|date', 
        ]);

        // Buscar el registro basado en el ID
        $corte = PartidasOF::find($request->id);

        // Actualizar el campo FechaFinalizacion
        $corte->FechaFinalizacion = $request->fecha_finalizacion;
        $corte->save();

        // Retornar una respuesta de éxito
        return response()->json([
            'success' => true,
            
        ]);
        
    }
    public function getEstatus(Request $request)
    {
        $ordenFabricacion = OrdenFabricacion::findOrFail($request->id);
        return response()->json([
            'success' => true,
            'estatus' => $ordenFabricacion->estatus,
            'badgeClass' => match ($ordenFabricacion->estatus) {
                'Completado' => 'badge-success',
                'En proceso' => 'badge-warning',
                default => 'badge-danger',
            },
        ]);
    }
    public function getCantidadTotal($id)
    {
        $ordenFabricacion = OrdenFabricacion::find($id);

        if ($ordenFabricacion) {
            return response()->json(['success' => true, 'CantidadTotal' => $ordenFabricacion->CantidadTotal]);
        }

        return response()->json(['success' => false, 'message' => 'Orden de fabricación no encontrada.']);
    }
    public function getCortesInfo($id)
    {
        try {
            // Sumar los cortes registrados de la tabla `partidas_of`
            $sumaCortes = DB::table('partidasof')
                ->where('OrdenFabricacion_id', $id) 
                ->sum('cantidad_partida');

            // Obtener información de la orden de fabricación
            $ordenFabricacion = DB::table('ordenfabricacion')
                ->where('id', $id)
                ->first();

            if (!$ordenFabricacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orden de fabricación no encontrada.',
                ]);
            }

            return response()->json([
                'success' => true,
                'CantidadTotal' => $ordenFabricacion->CantidadTotal, 
                'cortes_registrados' => $sumaCortes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la información de los cortes.',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function MostarInformacion(Request $request)
    {
        $partidaId = $request->input('id');
    
        // Buscar la partida por ID
        $partida = PartidasOF::with('ordenFabricacion')->find($partidaId);
    
        if (is_null($partida) || is_null($partida->ordenFabricacion)) {
            return response()->json(['error' => 'No se encontraron datos para este ID.']);
        }
    
        // Obtener la orden de fabricación relacionada
        $ordenFabricacion = $partida->ordenFabricacion;
    
        // Calcular el inicio del contador para esta OrdenFabricacion_id
        $numeroInicial = PartidasOF::where('OrdenFabricacion_id', $partida->OrdenFabricacion_id)
            ->where('id', '<', $partidaId)
            ->sum('cantidad_partida') + 1;
    
        // Preparar las partidas relacionadas solo para la partida seleccionada
        $partidasData = [];
        $contador = $numeroInicial; 
    
        for ($i = 1; $i <= $partida->cantidad_partida; $i++) {
            $partidasData[] = [
                'cantidad' => $contador, 
                'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
                'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
            ];
            $contador++; // Incrementar el contador
        }
    
        // Preparar la respuesta con la información de la partida seleccionada
        $response = [
            'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
            'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
            'cantidad_partida' => $partida->cantidad_partida ?? 0,
            'partidas' => $partidasData, 
        ];
    
        return response()->json($response);
    }
    public function generarPDF(Request $request)
    {
        try {
            $partidaId = $request->input('id');
            if (!$partidaId) {
                throw new \Exception('ID no recibido');
            }

            // Buscar la partida por ID
            $partida = PartidasOF::with('ordenFabricacion')->find($partidaId);

            if (is_null($partida) || is_null($partida->ordenFabricacion)) {
                throw new \Exception('No se encontraron datos para este ID.');
            }

            // Obtener la orden de fabricación relacionada
            $ordenFabricacion = $partida->ordenFabricacion;

            // Calcular el inicio del contador para esta OrdenFabricacion_id
            $numeroInicial = PartidasOF::where('OrdenFabricacion_id', $partida->OrdenFabricacion_id)
                ->where('id', '<', $partidaId)
                ->sum('cantidad_partida') + 1;

            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            $contador = $numeroInicial; 

            for ($i = 1; $i <= $partida->cantidad_partida; $i++) {
                $partidasData[] = [
                    'cantidad' => $contador, 
                    'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
                    'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                ];
                $contador++; // Incrementar el contador
            }

            // Invertir el array de partidas
            $partidasData = array_reverse($partidasData);

            // Crear PDF
            $pdf = new TCPDF();
            $pdf->SetMargins(5, 5, 5);
            $pdf->AddPage();

            // Título del PDF
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 10, 'Orden de Fabricación: ' . strip_tags($ordenFabricacion->OrdenFabricacion), 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 5, 'Descripción: ' . strip_tags($ordenFabricacion->Descripcion), 0, 1, 'C');
            $pdf->Ln(5);

            // Generar contenido para cada partida
            foreach ($partidasData as $partida) {
                $content = 
                    'No: ' . strip_tags($partida['cantidad']) . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['orden_fabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";

                $startY = $pdf->GetY();
                $rectWidth = 80;
                $rectHeight = 15;
                $pdf->Rect(10, $startY, $rectWidth, $rectHeight, 'D');
                $pdf->SetXY(12, $startY + 1);
                $pdf->SetFont('helvetica', '', 6);
                $pdf->MultiCell($rectWidth - 4, 5, strip_tags($content), 0, 'L', 0, 1);
                $pdf->SetFont('helvetica', '', 8);
            }

            ob_end_clean();

            // Generar el archivo PDF y devolverlo al navegador
            return $pdf->Output('orden_fabricacion_' . $partidaId . '.pdf', 'D');
        } catch (\Exception $e) {
            Log::error('Error al generar PDF: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function PDFCondicion(Request $request)
    {
        try {
            //dd($request->all());
            
            $request->validate([
                'desde_no' => 'required|integer|min:1',
                'hasta_no' => 'required|integer|min:1|gte:desde_no',
            ]);

            
            $desde = $request->input('desde_no');
            $hasta = $request->input('hasta_no');

            // Obtener la orden de fabricación (solo si necesitas los datos de la orden de fabricación)
            $ordenFabricacion = OrdenFabricacion::find($request->input('id'));

            if (!$ordenFabricacion) {
                throw new \Exception('No se encontró la orden de fabricación');
            }

            // Crear las etiquetas con el rango especificado
            $partidasData = [];
            $contadorPartida = $desde; 

            // Generar tantas etiquetas como el rango lo indique
            for ($i = $desde; $i <= $hasta; $i++) {
                $partidasData[] = [
                    'no' => $contadorPartida,
                    'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
                    'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion,
                ];
                $contadorPartida++;
            }

            // Crear el PDF usando TCPDF o domPDF
            $pdf = new TCPDF();
            $pdf->SetMargins(5, 5, 5); 
            $pdf->AddPage();

            // Título de la orden de fabricación
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 10, 'Orden de Fabricación: ' . strip_tags($ordenFabricacion->OrdenFabricacion), 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 5, 'Descripción: ' . strip_tags($ordenFabricacion->Descripcion), 0, 1, 'C');
            $pdf->Ln(5);  // Salto de línea

            // Datos de las partidas generadas
            foreach ($partidasData as $partida) {
                $content = 
                    'No: ' . strip_tags($partida['no']) . "\n" .
                    'Orden de Fabricación: ' . strip_tags($partida['orden_fabricacion']) . "\n" .
                    'Descripción: ' . strip_tags($partida['descripcion']) . "\n";

                // Obtener la posición inicial y calcular la altura de la celda
                $startY = $pdf->GetY();

                // Ajustar el tamaño del rectángulo
                $rectWidth = 80;  // Ancho del rectángulo
                $rectHeight = 15;  // Altura del rectángulo ajustada
                $pdf->Rect(10, $startY, $rectWidth, $rectHeight, 'D');  // Dibujar el rectángulo

                // Colocar el contenido dentro del cuadro con fuente más pequeña
                $pdf->SetXY(12, $startY + 1);  
                $pdf->SetFont('helvetica', '', 6);  
                $pdf->MultiCell($rectWidth - 4, 5, strip_tags($content), 0, 'L', 0, 1);  
            }

            // Limpiar cualquier salida previa
            ob_end_clean();

            // Generar el PDF y forzar la descarga
            return $pdf->Output('etiquetas_' . $ordenFabricacion->OrdenFabricacion . '.pdf', 'D');
        } catch (\Exception $e) {
            // Registrar el error y devolver mensaje de error
            Log::error('Error al generar PDF: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
        */
}






