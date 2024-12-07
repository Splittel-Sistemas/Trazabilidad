<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\FuncionesGeneralesController;
use Illuminate\Support\Facades\Log;
use App\Models\OrdenVenta;
use App\Models\OrdenFabricacion;

class PlaneacionController extends Controller
{
    protected $funcionesGenerales;

    public function __construct(FuncionesGeneralesController $funcionesGenerales)
    {
        $this->funcionesGenerales = $funcionesGenerales;
    }
    public function index(){
        $FechaFin=date('Ymd', strtotime('-1 day'));
        $FechaInicio=date('Ymd');
        $NumOV="";
        $message="";
        $datos=$this->OrdenesVenta($FechaInicio,$FechaFin,$NumOV);
        if($datos!=0){
            if(empty($datos)){
                $status="empty";
            }else{
                $status="success";
            }
        }else{
            $status="error";
        }
        $status="empty";
        $FechaFin=date('Y-m-d', strtotime('-1 day'));
        $FechaInicio=date('Y-m-d');
        return view('Planeacion.Planeacion', compact('datos', 'FechaInicio', 'FechaFin','status'));
    }
    public function PartidasOF(Request $request){
        //datos para la consulta
        $schema = 'HN_OPTRONICS';
        $ordenventa = $request->input('docNum');
        if (empty($ordenventa)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El número de orden no fue proporcionado.'
            ]);
        }
        //Consulta a SAP para traer las partidas de una OV
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
        //Ejecucion de la consulta
        $partidas = $this->funcionesGenerales->ejecutarConsulta($sql);
        if ($partidas === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al ejecutar la consulta. Verifique los parámetros.'
            ]);
        }
        if (empty($partidas)) {
            //Log::warning("No se encontraron partidas para la orden: $ordenventa");
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron partidas para esta orden.'
            ]);
        }
        $html = '<div class="table-responsive table-partidas" style="width:100%;">';
        $html .= '<table class="table-sm" id="table_OF" style="width:100%;">';
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
            //Valida que la Orden de Fabricacion no se encuentre registrada
            $respuesta=$this->comprobar_existe_partida($ordenventa,$partida['Orden de F.']);
            $bandera_tabla_mostrar=0;
            if($respuesta==0){
                $bandera_tabla_mostrar=1;
                $ordenFab = trim($partida['Orden de F.']); 
                $cantidadOF = is_numeric($partida['Cantidad OF']) 
                    ? number_format($partida['Cantidad OF'], 0, '.', '') 
                    : 'No disponible'; 
        
                $fechaEntrega = !empty($partida['Fecha entrega OF']) 
                    ? \Carbon\Carbon::parse($partida['Fecha entrega OF'])->format('d-m-Y') 
                    : 'No disponible'; 
                $html .= '<tr id="row-' . $index . '" draggable="true" ondragstart="drag(event)" data-orden-fab="' . trim($partida['Orden de F.']) . '" data-articulo="' . $partida['Articulo'] . '" data-descripcion="' . $partida['Descripcion'] . '" data-cantidad="' . $cantidadOF . '" data-fecha-entrega="' . $fechaEntrega . '">
                            <td>' . ($partida['Orden de F.'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Articulo'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Descripcion'] ?? 'No disponible') . '</td>
                            <td>' . ($cantidadOF ?: 'No disponible') . '</td>
                            <td>' . ($fechaEntrega ?: 'No disponible') . '</td>
                        </tr>';
            }
        }
        if($bandera_tabla_mostrar==0){
            return response()->json([
                'status' => 'success',
                'message' => '<p class="text-center" style="font-size:12px;">Todas las &Oacute;rdenes de fabricaci&oacute;n ya se encuentran asignadas</p>'
            ]);    
        }
            $html .= '</tbody></table></div>';
            return response()->json([
                'status' => 'success',
                'message' => $html
            ]);
    }
    //Funcion para ver las Ordenes de venta de  fecha inicio a fecha fin y por numero de OV
    public function OrdenesVenta($FechaInicio,$FechaFin,$NumOV){
        $schema = 'HN_OPTRONICS';
        $where="";
        $datos="";
        if($NumOV==""){
            $where='T0."DocDate" BETWEEN \'' . $FechaFin . '\' AND \'' . $FechaInicio . '\'';
        }else{
            $where='T0."DocNum" = \'' .$NumOV. '\'';
        }
        $sql = 'SELECT T0."DocNum" AS "OV", T0."CardName" AS "Cliente", T0."DocDate" AS "Fecha", 
                T0."DocStatus" AS "Estado", T0."DocTotal" AS "Total" FROM ' . $schema . '.ORDR T0 
                WHERE '.$where;
        try {
            $datos = $this->funcionesGenerales->ejecutarConsulta($sql);
        } catch (\Exception $e) {
            return $datos=0;
        }
        return $datos;
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
                //Log::info('No se encontraron órdenes para las fechas: ' . $fechaAyer . ' a ' . $fechaHoy);
                return back()->with('warning', 'No se encontraron órdenes para estas fechas.');
            }
            //Log::info('Ordenes Venta:', ['ordenes' => $ordenesVenta]);
        } catch (\Exception $e) {
            //Log::error('Error al obtener órdenes: ' . $e->getMessage());
            return back()->with('error', 'Error al obtener órdenes. Intenta nuevamente.');
        }
        $fechaHoy = date('d-m-Y');
        $fechaAyer = date('d-m-Y', strtotime('-1 day'));
        return view('layouts.ordenes.ordenesv', compact('ordenesVenta', 'fechaHoy', 'fechaAyer'));
    }
    public function DatosDePartida(Request $request)
    {
        //datos para la consulta
        $schema = 'HN_OPTRONICS';
        $ordenventa = $request->input('docNum');
        if (empty($ordenventa)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El número de orden no fue proporcionado.'
            ]);
        }
        //Consulta a SAP para traer las partidas de una OV
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
        //Ejecucion de la consulta
        $partidas = $this->funcionesGenerales->ejecutarConsulta($sql);
        if ($partidas === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al ejecutar la consulta. Verifique los parámetros.'
            ]);
        }
        if (empty($partidas)) {
            //Log::warning("No se encontraron partidas para la orden: $ordenventa");
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron partidas para esta orden.'
            ]);
        }
        $html = '<div class="table-responsive table-partidas" style="width:100%;">';
        $html .= '<table class="table-sm" id="table_OF" style="width:100%;">';
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
            //Valida que la Orden de Fabricacion no se encuentre registrada
            $respuesta=$this->comprobar_existe_partida($ordenventa,$partida['Orden de F.']);
            $bandera_tabla_mostrar=0;
            if($respuesta==0){
                $bandera_tabla_mostrar=1;
                $ordenFab = trim($partida['Orden de F.']); 
                $cantidadOF = is_numeric($partida['Cantidad OF']) 
                    ? number_format($partida['Cantidad OF'], 0, '.', '') 
                    : 'No disponible'; 
        
                $fechaEntrega = !empty($partida['Fecha entrega OF']) 
                    ? \Carbon\Carbon::parse($partida['Fecha entrega OF'])->format('d-m-Y') 
                    : 'No disponible'; 
                $html .= '<tr id="row-' . $index . '" draggable="true" ondragstart="drag(event)" data-orden-fab="' . trim($partida['Orden de F.']) . '" data-articulo="' . $partida['Articulo'] . '" data-descripcion="' . $partida['Descripcion'] . '" data-cantidad="' . $cantidadOF . '" data-fecha-entrega="' . $fechaEntrega . '">
                            <td>' . ($partida['Orden de F.'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Articulo'] ?? 'No disponible') . '</td>
                            <td>' . ($partida['Descripcion'] ?? 'No disponible') . '</td>
                            <td>' . ($cantidadOF ?: 'No disponible') . '</td>
                            <td>' . ($fechaEntrega ?: 'No disponible') . '</td>
                        </tr>';
            }
        }
        if($bandera_tabla_mostrar==0){
            return response()->json([
                'status' => 'success',
                'message' => '<p class="text-center" style="font-size:12px;">Todas las &Oacute;rdenes de fabricaci&oacute;n ya se encuentran asignadas</p>'
            ]);    
        }
            $html .= '</tbody></table></div>';
            return response()->json([
                'status' => 'success',
                'message' => $html
            ]);
    }
    public function guardarDatos(Request $request)
    {
        //Log::info('Datos recibidos para guardar fila:', $request->all());
        $cantidadOf = $request->cantidad_of;
        if ($cantidadOf && !is_numeric($cantidadOf)) {
            //Log::error('La cantidad no es un número válido:', ['cantidad' => $cantidadOf]);
            return response()->json([
                'status' => 'error',
                'message' => 'La cantidad debe ser un número válido.',
            ]);
        }
    
        $fechaEntrega = $request->fecha_entrega;
        if (!$fechaEntrega || !\Carbon\Carbon::parse($fechaEntrega)->isValid()) {
            //Log::info('Fecha de entrega no proporcionada o inválida, se asigna la fecha actual');
            $fechaEntrega = \Carbon\Carbon::today()->format('Y-m-d'); 
        } else {
            $fechaEntrega = \Carbon\Carbon::parse($fechaEntrega)->format('Y-m-d');
        }
        try {
            $exists = OrdenVenta::where('orden_fab', $request->orden_fab)
                ->where('articulo', $request->articulo)
                ->where('descripcion', $request->descripcion)
                ->where('cantidad_of', $request->cantidad_of)
                ->where('fecha_entrega', $fechaEntrega)
                ->exists();
            if (!$exists) {
                $ordenVenta = OrdenVenta::create([
                    'orden_fab' => $request->orden_fab,
                    'articulo' => $request->articulo,
                    'descripcion' => $request->descripcion,
                    'cantidad_of' => $cantidadOf,
                    'fecha_entrega' => $fechaEntrega,
                ]);
                //Log::info('Fila guardada correctamente en orden_venta:', ['orden_venta' => $ordenVenta]);
                $ordenFabricacionExists = OrdenFabricacion::where('orden_venta_id', $ordenVenta->id)
                    ->where('numero_fabricacion', $request->orden_fab) 
                    ->exists();
                if (!$ordenFabricacionExists) {
                    $ordenFabricacion = OrdenFabricacion::create([
                        'orden_venta_id' => $ordenVenta->id,  
                        'numero_fabricacion' => $request->orden_fab,  
                        'fecha_fabricacion' => $fechaEntrega,  
                        'estado' => 'Pendiente',  
                    ]);
                    //Log::info('Fila guardada correctamente en orden_fabricacion:', ['orden_fabricacion' => $ordenFabricacion]);
                } else {
                    //Log::info('La fila de orden_fabricacion ya existe en la base de datos');
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Fila guardada correctamente',
                    'data' => $ordenVenta,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Los datos ya existen en la base de datos.',
                ]);
            }
        } catch (\Exception $e) {
            //Log::error('Error al guardar la fila:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Hubo un problema al guardar los datos. Verifique los parámetros.',
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
                                    <td style="display:none"> ' . $request->cliente. '</td>
                                    <td style="display:none"> ' . $request->docNum. '</td>
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
        $query = $request->input('query', ''); 
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
                                    <td style="display:none"> ' . $request->cliente. '</td>
                                    <td style="display:none"> ' . $request->docNum. '</td>
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
    public function guardarConsulta(Request $request)
    {   
        $validatedData = $request->validate([
            'orden_fab' => 'required|string',
            'articulo' => 'required|string',
            'descripcion' => 'required|string',
            'cantidad_of' => 'required|integer',
            'fecha_entrega' => 'required|date',
        ]);
        try {
            $ordenVenta = OrdenVenta::create([
                'orden_fab' => $validatedData['orden_fab'],
                'articulo' => $validatedData['articulo'],
                'descripcion' => $validatedData['descripcion'],
                'cantidad_of' => $validatedData['cantidad_of'],
                'fecha_entrega' => $validatedData['fecha_entrega'],
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Consulta guardada exitosamente.',
                'data' => $ordenVenta,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al guardar la consulta. Intente más tarde.',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function eliminarRegistro(Request $request)
    {
        try {
            $ordenFab = $request->input('orden_fab');
            
            // Verificar si existe el registro
            $registro = OrdenVenta::where('orden_fab', $ordenFab)->first();

            if ($registro) {
                $registro->delete(); // Eliminar el registro
                return response()->json([
                    'status' => 'success',
                    'message' => 'Registro eliminado correctamente.'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontró el registro a eliminar.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hubo un error al intentar eliminar el registro.',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function comprobar_existe_partida($OrdenVenta, $Ordenfabricacion){
            $datos=OrdenVenta:: where('OrdenVenta','=',$OrdenVenta)->first();
            if($datos){
                $datos=OrdenFabricacion::where('OrdenVenta_id','=',$datos->id)
                ->where('OrdenFabricacion','=',$Ordenfabricacion)                        
                ->count();
            }else{
                $datos=0;
            }
        return $datos;
    }


}    