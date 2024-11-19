<?php
namespace App\Http\Controllers;

use App\Services\TCPDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        // Datos que se incluirán en el PDF
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
    }
}
