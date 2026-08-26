<?php

namespace App\Http\Controllers;

use App\Models\ServiceLayer;
use App\Models\TrasladoDetalle;
use App\Models\Traslados;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrasladosController extends Controller
{

    protected $funcionesGenerales;

    public function __construct()
    {
        $this->funcionesGenerales = new FuncionesGeneralesController();
    }

    public function index()
    {
        $trasladosCompletos = Traslados::with('trasladoDetalles')->where('estado', '=', 'Recibido')->orWhere('estado', '=', 'Cancelado')->limit(50)->get();
        $trasladosPendientes = Traslados::with('trasladoDetalles')->whereNotIn('estado', ['Recibido', 'Cancelado'])->get();
        return view('layouts.traslados.index', compact('trasladosCompletos', 'trasladosPendientes'));
    }

    public function create()
    {
        $trasladosRegistrados = TrasladoDetalle::select('rp')->groupBy('rp')->get();

        $filtro = "";

        if (!empty($trasladosRegistrados) && count($trasladosRegistrados) > 0) {
            $filtro = ' AND T00."DocNum" NOT IN (';
            foreach ($trasladosRegistrados as $value) {
                $filtro .= "'" . $value->rp . "',";
            }
            $filtro = substr($filtro, 0, -1);
            $filtro .= ")";
        }

        $recibosProduccionPorTrasladar = $this->funcionesGenerales->RecibosProduccionParaTraslados($filtro);
        return view('layouts.traslados.create', compact('recibosProduccionPorTrasladar'));
    }

    public function generate(Request $request)
    {
        DB::beginTransaction();
        try {
            $listaDocumentos = $request->listaDocumentos;

            $filtro = 'AND T00."DocNum" IN (';
            foreach ($listaDocumentos as $value) {
                $filtro .= "'" . $value . "',";
            }
            $filtro = substr($filtro, 0, -1);
            $filtro .= ")";

            $detalleTraslado = $this->funcionesGenerales->DetalleTraslado($filtro);

            $traslado = Traslados::create([
                'estado' => 'Generado',
                'usuario_traslado_id' => Auth::id(),
                'alta' => Carbon::now()
            ]);

            foreach ($detalleTraslado as $detalle) {
                DB::table('traslado_detalles')->insert([
                    'traslado_id' => $traslado->id,
                    'of' => (int)$detalle['ORDEN FABRICACION'],
                    'ov' => (int)$detalle['ORDEN VENTA'],
                    'rp' => (int)$detalle['RECIBO PRODUCCION'],
                    'cardcode' => $detalle['CardCode'],
                    'cardname' => $detalle['CardName'],
                    'linenum' => $detalle['LineNum'],
                    'itemcode' => $detalle['ItemCode'],
                    'dscription' => $detalle['Dscription'],
                    'quantity_transfer' => (int)$detalle['Quantity'],
                    'quantity_receive' => 0,
                    'batchnum' => $detalle['BatchNum'],
                ]);
            }

            if (count($detalleTraslado) == 0) {
                DB::rollBack();
                return response()->json(['status' => 'warning', 'message' => 'No se encontraron detalles de los documentos proporcionados.']);
            } else {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Traslado generado']);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function show(int $id)
    {
        $traslado = Traslados::with('trasladoDetalles')->where('id', $id)->first();
        $serviceLayer = ServiceLayer::where('traslado_id', $id)->orderBy('movimiento', 'asc')->get();
        $movimientos = array();;
        foreach ($serviceLayer as $services) {
            $movimientos[$services->movimiento] = $services->movimiento;
        }
        return view('layouts.traslados.show', compact('traslado', 'serviceLayer', 'movimientos'));
    }

    public function showprint(int $id)
    {
        $traslado = Traslados::with('trasladoDetalles')->where('id', $id)->first();
        $serviceLayer = ServiceLayer::where('traslado_id', $id)->orderBy('movimiento', 'asc')->get();
        $movimientos = array();;
        foreach ($serviceLayer as $services) {
            $movimientos[$services->movimiento] = $services->movimiento;
        }
        return view('layouts.traslados.showprint', compact('traslado', 'serviceLayer', 'movimientos'));
    }

    public function print(int $idTraslado, int $idMovimiento)
    {
        $serviceLayer = ServiceLayer::where('traslado_id', $idTraslado)->where('movimiento', $idMovimiento)->get();
        $listaMovimientos = [];
        $usuario = '';
        $fecha = '';
        $docnum = '';
        foreach ($serviceLayer as $services) {
            $listaMovimientos[$services->ov] = $services;
            $usuario = $services->usuarioRecibe->name;
            $fecha = $services->alta;
            $docnum = $services->docnum;
        }
        return view('layouts.traslados.print', compact('idTraslado', 'idMovimiento', 'usuario', 'fecha', 'listaMovimientos', 'docnum'));
    }

    public function dateFilter(Request $request)
    {
        $request->validate([
            'fechaInicio' => ['required', 'date'],
            'fechaFin' => ['required', 'date'],
        ]);

        $fechaInicio = $request->fechaInicio;
        $fechaFin = $request->fechaFin . ' 23:59:59';

        $datos = [];

        $trasladosCompletos = Traslados::from('traslados as t')
            ->leftJoin('users as u', 'u.id', '=', 't.usuario_traslado_id')
            ->select([
                't.id',
                't.estado',
                'u.name as usuario',
                't.alta',
            ])
            ->whereIn('t.estado', ['Recibido', 'Cancelado'])
            ->whereBetween('t.alta', [$fechaInicio, $fechaFin])
            ->get();

        foreach ($trasladosCompletos as $indice => $traslado) {
            $estado = '';
            switch ($traslado['estado']) {
                case 'Generado':
                    $estado = '<span class="badge text-bg-primary">Generado</span>';
                    break;

                case 'Parcial':
                    $estado = '<span class="badge text-bg-warning">Parcial</span>';
                    break;

                case 'Recibido':
                    $estado = '<span class="badge text-bg-success">Recibido</span>';
                    break;

                case 'Cancelado':
                    $estado = '<span class="badge text-bg-danger">Cancelado</span>';
                    break;
            }

            $botonDetalle = '<a href="/Traslados/' . $traslado['id'] . '" class="btn btn-sm btn-primary">Detalles</a>';
            $datos[] = [$indice, 'T' . str_pad($traslado['id'], 6, "0", STR_PAD_LEFT), $estado, $traslado['usuario'], $traslado['alta'], $botonDetalle];
        }


        return response()->json($datos);
    }
}
