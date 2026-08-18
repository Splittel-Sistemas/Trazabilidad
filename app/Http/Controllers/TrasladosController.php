<?php

namespace App\Http\Controllers;

use App\Models\TrasladoDetalle;
use App\Models\Traslados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\VarDumper;

class TrasladosController extends Controller
{

    protected $funcionesGenerales;

    public function __construct()
    {
        $this->funcionesGenerales = new FuncionesGeneralesController();
    }

    public function index()
    {
        $traslados = Traslados::with('trasladoDetalles')->get();
        return view('layouts.traslados.index', compact('traslados'));
    }

    public function create()
    {
        $trasladosRegistrados = TrasladoDetalle::select('docnum')->groupBy('docnum')->get();

        $filtro = "";

        if (!empty($trasladosRegistrados) && count($trasladosRegistrados) > 0) {
            $filtro = ' AND T00."DocNum" NOT IN (';
            foreach ($trasladosRegistrados as $value) {
                $filtro .= "'" . $value->docnum . "',";
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

            $filtro = 'T00."DocNum" IN (';
            foreach ($listaDocumentos as $value) {
                $filtro .= "'" . $value . "',";
            }
            $filtro = substr($filtro, 0, -1);
            $filtro .= ")";

            $detalleTraslado = $this->funcionesGenerales->DetalleTraslado($filtro);

            $traslado = Traslados::create([
                'estado' => 'Generado',
                'usuario_traslado_id' => Auth::id(),
                'usuario_recive_id' => null,
            ]);

            foreach ($detalleTraslado as $detalle) {
                DB::table('traslado_detalles')->insert([
                    'traslado_id' => $traslado->id,
                    'docnum' => (int)$detalle['DocNum'],
                    'linenum' => $detalle['LineNum'],
                    'itemcode' => $detalle['ItemCode'],
                    'quantity_transfer' => (int)$detalle['Quantity'],
                    'quantity_receive' => 0,
                    'batchnum' => $detalle['BatchNum']
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
        $serviceLayer = Traslados::with('serviceLayer')->where('id', $id)->first();
        return view('layouts.traslados.show', compact('traslado', 'serviceLayer'));
    }
}
