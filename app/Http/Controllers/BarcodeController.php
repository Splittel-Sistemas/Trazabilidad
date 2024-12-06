<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\OrdenVenta;
use App\Models\OrdenFabricacion;
use Illuminate\Support\Facades\Log;

class BarcodeController extends Controller
{
    public function index()
    {
        return view('layouts.ordenes.buscaryregistrar'); // Asegúrate de que el nombre de la vista coincida con la ruta de la vista que creaste
    }
    public function searchOrder($barcode)
{
    \Log::info('Código de barras recibido:', ['barcode' => $barcode]);

    $barcodeParts = str_split($barcode, 3);
    $ordenVentaId = $barcodeParts[0] ?? null;
    $ordenFabricacionNumero = $barcodeParts[1] ?? null;

    \Log::info('Partes del código de barras:', [
        'ordenVentaId' => $ordenVentaId,
        'ordenFabricacionNumero' => $ordenFabricacionNumero,
    ]);

    if (!$ordenVentaId || !$ordenFabricacionNumero) {
        return response()->json([
            'status' => 'error',
            'message' => 'Formato de código de barras inválido',
        ]);
    }

    $ordenVenta = OrdenVenta::find($ordenVentaId);
    \Log::info('Resultado de OrdenVenta:', ['ordenVenta' => $ordenVenta]);

    if (!$ordenVenta) {
        return response()->json([
            'status' => 'error',
            'message' => 'Orden de venta no encontrada',
        ]);
    }

    $ordenFabricacion = OrdenFabricacion::where('numero_fabricacion', $ordenFabricacionNumero)
                                        ->where('orden_venta_id', $ordenVenta->id)
                                        ->first();
    \Log::info('Resultado de OrdenFabricacion:', ['ordenFabricacion' => $ordenFabricacion]);

    if (!$ordenFabricacion) {
        return response()->json([
            'status' => 'error',
            'message' => 'Orden de fabricación no encontrada',
        ]);
    }

    return response()->json([
        'status' => 'success',
        'order_sale' => $ordenVenta->orden_fab,
        'order_manufacture' => $ordenFabricacion->numero_fabricacion,
        'article' => $ordenVenta->articulo,
        'quantity' => $ordenVenta->cantidad_of,
    ]);
}

}
