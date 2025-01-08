<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PartidasOF;
use App\Models\OrdenFabricacion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use TCPDF;
use App\Models\User;



class CorteController extends Controller
{
    //vista
    public function index()
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

    return view('Areas.Cortes', compact('ordenesFabricacion'));
}

    
    //carga de la tabla
    public function getData(Request $request) {
 
        $limit = $request->input('length'); // Número de registros por página
        $start = $request->input('start');  // Índice del primer registro
    
        // Consulta para obtener los datos de OrdenFabricacion sin filtrar por fecha
        $datos = OrdenFabricacion::join('OrdenVenta', 'OrdenFabricacion.OrdenVenta_id', '=', 'OrdenVenta.id')
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
        $totalRecords = $datos->count();
    
        // Aplicar filtros de búsqueda, si los hay
        if ($request->has('search') && $request->input('search.value') != '') {
            $searchValue = $request->input('search.value');
            $datos->where('OrdenFabricacion.Articulo', 'like', '%' . $searchValue . '%');
        }
    
        // Obtener los registros paginados
        $data = $datos->skip($start)->take($limit)->get();
    
        // Calcular el estatus en el backend
        $data->transform(function ($item) {
            $cantidadTotal = $item->CantidadTotal;
            $sumaCantidadPartida = $item->suma_cantidad_partida;
            $pendientesFecha = isset($item->FechaFinalizar) ? $item->FechaFinalizar : 0; // Asegúrate de que exista
    
            if ($sumaCantidadPartida == 0) {
                $estatus = 'Sin cortes';
            } elseif ($pendientesFecha > 0 || $sumaCantidadPartida < $cantidadTotal) {
                $estatus = 'En proceso';
            } else {
                $estatus = 'Completado';
            }
    
            $item->estatus = $estatus; // Añadir el estatus al resultado
            return $item;
        });
    
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, // Cambiar si agregas más filtros
            'data' => $data,
        ]);
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
            'datos_partidas.*.OrdenFabricacion_id' => 'required|exists:OrdenFabricacion,id', // Validar que 'orden_fabricacion_id' exista en la tabla 'OrdenFabricacion'
            'datos_partidas.*.cantidad_partida' => 'required|integer', // Asegurarse de que 'cantidad_partida' sea un número entero
            'datos_partidas.*.fecha_fabricacion' => 'required|date', // Asegurarse de que 'fecha_fabricacion' sea una fecha válida
        ]);
    
        // Guardar las partidas
        foreach ($request->datos_partidas as $partida) {
            // Crear una nueva partida en la base de datos
            PartidasOF::create([
                'OrdenFabricacion_id' => $partida['OrdenFabricacion_id'],
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
        $partidas = PartidasOF::where('OrdenFabricacion_id', $ordenFabricacionId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Formatea los datos antes de enviarlos
        $data = $partidas->map(function ($partida) {
            return [
                'id' => $partida->id,
                'cantidad_partida' => $partida->cantidad_partida,
                'fecha_fabricacion' => Carbon::parse($partida->fecha_fabricacion)->format('d-m-Y'), // Cambia el formato
                'FechaFinalizacion' => $partida->FechaFinalizacion 
                    ? Carbon::parse($partida->FechaFinalizacion)->format('d-m-Y') 
                    : null, // Evita errores si está vacío
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
            'id' => 'required|exists:partidasof,id', // Validar que el ID exista en la tabla
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
            
        ]);
    }
    // OrdenFabricacionController.php
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
            // Sumar los cortes registrados de la tabla `partidasof`
            $sumaCortes = DB::table('partidasof')
                ->where('OrdenFabricacion_id', $id) // Usa el nombre correcto
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
                'CantidadTotal' => $ordenFabricacion->CantidadTotal, // Columna esperada en la tabla `orden_fabricacion`
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
    
        // Buscar todas las partidas anteriores de la misma orden_fabricacion_id
        $partidasPrevias = PartidasOF::where('OrdenFabricacion_id', $partida->OrdenFabricacion_id)
            ->where('id', '<', $partidaId) // Asegurarse de obtener solo las partidas anteriores
            ->orderBy('cantidad_partida', 'asc') // Ordenar por cantidad_partida para mantener el orden correcto
            ->get();
    
        // Obtener el último "No" de las partidas previas
        $ultimoNumero = 0;
        if ($partidasPrevias->isNotEmpty()) {
            // Si existen partidas anteriores, obtener el último "No" generado
            $ultimoNumero = $partidasPrevias->last()->cantidad_partida;  // última cantidad_partida
        }
    
        // Preparar las partidas relacionadas solo para la partida seleccionada
        $partidasData = [];
        $contador = $ultimoNumero + 1;  // Comenzar la numeración desde el último número + 1
    
        // Generar las entradas basadas en la cantidad de la partida seleccionada
        for ($i = 1; $i <= $partida->cantidad_partida; $i++) {
            $partidasData[] = [
                'cantidad' => $contador,  // Esto será No: 1, 2, 3, etc. para cada unidad de la partida
                'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
                'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
            ];
            $contador++; // Incrementar el número de la partida
        }
    
        // Preparar la respuesta con la información de la partida seleccionada
        $response = [
            'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
            'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
            'cantidad_partida' => $partida->cantidad_partida ?? 0,
            'partidas' => $partidasData,  // Mostrar solo las partidas generadas para la selección
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

            // Preparar las partidas relacionadas solo para la partida seleccionada
            $partidasData = [];
            for ($i = 1; $i <= $partida->cantidad_partida; $i++) {
                $partidasData[] = [
                    'cantidad' => $i,
                    'descripcion' => $ordenFabricacion->Descripcion ?? 'Sin Descripción',
                    'orden_fabricacion' => $ordenFabricacion->OrdenFabricacion ?? 'Sin Orden de Fabricación',
                ];
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
            $contadorPartida = $desde; // Iniciar desde el valor 'Desde No'

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
                $pdf->SetXY(12, $startY + 1);  // Colocar el contenido dentro del cuadro (margen)
                $pdf->SetFont('helvetica', '', 6);  // Cambiar a una fuente más pequeña para los datos dentro del cuadro
                $pdf->MultiCell($rectWidth - 4, 5, strip_tags($content), 0, 'L', 0, 1);  // Usar MultiCell para asegurar que el texto se ajuste dentro del cuadro
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
}





