<?php
namespace App\Http\Controllers;

use App\Services\TCPDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\OrdenVenta;
use App\Models\OrdenFabricacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;




class PDFController extends Controller
{
    protected $tcpdfService;

    // Inyectamos el servicio TCPDF
    public function __construct(TCPDFService $tcpdfService)
    {
        $this->tcpdfService = $tcpdfService;
    }
    

    public function generatePdf()
    {
        //return view('GenerardorPDF.GernerarPdf'); 
        
        /*// Datos que se incluirán en el PDF
        $data = "Este es un ejemplo de PDF generado con TCPDF en Laravel.";

        // Ruta para guardar el PDF (en storage/app/public)
        $filePath = storage_path('app/public/mi_pdf_generado.pdf');

        // Llamar al servicio para generar el PDF
        $generated = $this->tcpdfService->generatePdf($data, $filePath);

        // Verificar si el PDF fue generado con éxito
        if ($generated) {
            return response()->json([
                'message' => 'PDF generado con éxito',
                'pdf_path' => asset('storage/mi_pdf_generado.pdf') // Proporcionar la URL pública
            ]);
        } else {
            return response()->json([
                'message' => 'Hubo un problema al generar el PDF'
            ], 500); // Devolver error 500 si el archivo no se generó
        }
    
      }*/
    }
    public function index()
    {
        return view('GeneradorPDF.GenerarPdf');
    }
    public function searchOrder($barcode)
{
    $barcodeParts = str_split($barcode, 3); 
    $ordenVentaId = $barcodeParts[0] ?? null;
    $ordenFabricacionNumero = $barcodeParts[1] ?? null;

    if (!$ordenVentaId || !$ordenFabricacionNumero) {
        return response()->json([
            'status' => 'error',
            'message' => 'Formato de código de barras inválido',
        ]);
    }
    $ordenVenta = OrdenVenta::where('id', $ordenVentaId)->first();
    if (!$ordenVenta) {
        return response()->json([
            'status' => 'error',
            'message' => 'Orden de venta no encontrada',
        ]);
    }
    $ordenFabricacion = OrdenFabricacion::where('numero_fabricacion', $ordenFabricacionNumero)
                                        ->where('orden_venta_id', $ordenVenta->id)
                                        ->first();
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
