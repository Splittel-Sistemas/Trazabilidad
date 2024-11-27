<?php

namespace App\Services;

use TCPDF;

class TCPDFService
{
    public function generatePdf($data, $filePath)
    {
        // Crear una nueva instancia de TCPDF
        $pdf = new TCPDF();

        // Establecer las propiedades del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Mi Empresa');
        $pdf->SetTitle('Mi Documento PDF');
        $pdf->SetSubject('Generación de PDF en Laravel');

        // Establecer las fuentes
        $pdf->SetFont('helvetica', '', 12);

        // Añadir una página
        $pdf->AddPage();

        // Agregar contenido al PDF
        $pdf->Write(0, $data);

        // Generar el archivo PDF y guardarlo en el sistema de archivos
        $pdf->Output($filePath, 'F'); // 'F' para guardar el archivo en el sistema

        // Puedes agregar una validación para verificar si el archivo se ha generado correctamente
        if (file_exists($filePath)) {
            return true;
        }

        return false; // Si el archivo no existe, devolver false
    }
}