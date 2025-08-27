<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partidasof_Areas;
use App\Models\PartidasOF;
use App\Models\OrdenFabricacion;
use App\Models\OrdenVenta;
use TCPDF;
use Illuminate\Support\Facades\Auth;
class EtiquetasController extends Controller
{
    protected $funcionesGenerales;
    public function __construct(FuncionesGeneralesController $funcionesGenerales){
        $this->funcionesGenerales = $funcionesGenerales;
    }
    public function index(){
         $user = Auth::user();
        // Verificar el permiso 'Vista Planeacion'
        if ($user->hasPermission('Vista Etiquetas')) {
            return view('Etiquetas.index');
        } else {
            return redirect()->route('error.');
        }
    }
    public function show( $OrdenFabricacion){
        $OrdenFabricacion = OrdenFabricacion::select('CantidadTotal', 'OrdenFabricacion')->where('OrdenFabricacion',$OrdenFabricacion)->first();
        $OrdenFabricacion1 = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion->OrdenFabricacion)->first();
        $OrdenVenta = $OrdenFabricacion1->OrdenVenta;
        if($OrdenVenta->OrdenVenta == 00000){
            $OrdenFabricacion->OrdenVenta = 'S/N';
            $OrdenFabricacion->Cliente = 'S/N';
        }else{
            $Cliente = ($this->funcionesGenerales->OrdeneVenta($OrdenVenta->OrdenVenta));
            $OrdenFabricacion->OrdenVenta = $OrdenVenta->OrdenVenta;
            $OrdenFabricacion->Cliente = $OrdenVenta->NombreCliente;
            $OrdenFabricacion->CodigoCliente = $Cliente[0]['CardCode'];
        }
        return $OrdenFabricacion;
    }
    public function Generar(Request $request){
        $PDFOrdenFabricacion = $request->PDFOrdenFabricacion;
        $CantidadBolsa = $request->CantidadBolsa;
        $PaginaFin = $request->PaginaFin;
        $PaginaInicio = $request->PaginaInicio;
        $Sociedad = $request->Sociedad;
        $CantidadEtiquetas = $request->CantidadEtiquetas;
        $OF = $request->OF;
        $TipoEtiqueta = $request->TipoEtiqueta;
        switch($TipoEtiqueta){
            case 'ETIQ1':
                return $this->EtiquetaHUAWEI($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion);
                break;
            case 'ETIQ2':
                return $this->EtiquetaBanderillaQR($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,1);
                break;
            case 'ETIQ3':
                return $this->EtiquetaBanderillaQR($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,2);
                break;
            case 'ETIQ4':
                return $this->EtiquetaBolsaJumper($CantidadEtiquetas,$PDFOrdenFabricacion);
                break;
            case 'ETIQ5':
                return $this->EtiquetaNumeroPiezas($Sociedad,$CantidadEtiquetas,$CantidadBolsa,$PDFOrdenFabricacion);
                break;
            default:
                break;
        }return $request;
    }
    //Etiqueta HUAWEI
    public function EtiquetaHUAWEI($PaginaInicio,$PaginaFin,$OrdenFabricacion){
        try {
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
            }
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            if($OrdenVenta == ""){
                $OrdenVenta = "N/A";
            }else{
                $OrdenVenta = $OrdenVenta->OrdenVenta;
            }
            //Datos de SAP
            $NumeroHuawei = "";
            $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta);
            $NumeroRegistros = count($DatosSAP);
            if($NumeroRegistros==0){
                return json_encode(["error" => "Cliente no encontrado, esta etiqueta es especial para el cliente HUAWEI INTERNATIONAL, verifica que la orden de fabricacion pertenesaca al cliente HUAWEI INTERNATIONAL."]);
            }else{
                if($DatosSAP[0]['SubCatNum']==""){
                    return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es especial para el cliente HUAWEI INTERNATIONAL.']);
                }else{
                    $NumeroHuawei =  $DatosSAP[0]['SubCatNum'];
                }
            }
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(1, 1, 1); 
            $pdf->SetFont('dejavusans', '', 9);  //dejavusans equivalente a Arial helvetica
            $pdf->SetAutoPageBreak(TRUE, 0.5);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);


            // Contador para saber cuántas etiquetas se han colocado en la página
            for ($i=($PaginaInicio-1); $i<$PaginaFin; $i++) {
                $pdf->AddPage('L', array(101, 51));
                
                // Color de fondo y texto en la parte superior de la etiqueta
                $posX = 0;
    
                //Agregar la imagen
                if(!file_exists(storage_path('app/Logos/Optronics.jpg'))){
                    return json_encode(["error" => 'No se encontraron el Logo requerido, por favor contactate con TI.']);
                }else{
                    $imagePath = storage_path('app/Logos/Optronics.jpg');
                    $pdf->Image($imagePath, 7, 5, 30);
                }
                //Se agrega el margen a la pagina
                $margen = 2;
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.5);
                $pdf->Rect(2, 3, 97 , 45 );
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.2);
                $pdf->Rect(6, 13, 90 , 0 );

                $ParteNo = 'Part No:  '.$NumeroHuawei."\n".
                            '  Desc:  ';
                $pdf->SetXY($posX+12, 16); 
                $pdf->MultiCell(90, 0, $ParteNo, 0, 'L', 0, 1);
                $ParteNo = 'Qty: 1 PCS';
                $pdf->SetXY($posX+70, 16);
                $pdf->MultiCell(40, 0, $ParteNo, 0, 'L', 0, 1);
                $pdf->SetXY($posX+24, 20); 
                $pdf->MultiCell(69, 0, $OrdenFabricacion->Descripcion, 0, 'L', 0, 1);
                $pdf->SetXY($posX+4, 31); 
                $pdf->MultiCell(60, 0, "Specification:  ", 0, 'L', 0, 1);
                //Codigo de barras
                $NumeroProveedorOpt = '4U1003';
                $Year = $fecha = date('Y');
                $Year = substr($Year, -2);
                $NumeroSemana = date('W');
                $CantidadBolsa = 'Q0001';
                $UltimosDigOF = 'S'.substr($OrdenFabricacion->OrdenFabricacion,-2);
                $CodigoBarras = '19'.$NumeroHuawei."/".$NumeroProveedorOpt.$Year.str_pad($NumeroSemana, 2, '0', STR_PAD_LEFT).$CantidadBolsa.$UltimosDigOF.str_pad(($i+1), 4, '0', STR_PAD_LEFT);
                $pdf->SetXY($posX + 30, 2);
                $pdf->write1DBarcode($CodigoBarras, 'C128',14, 36, 69, 5.5, 0.4, array(), 'N');
                 $pdf->SetXY($posX+15.5, 42.5); 
                $pdf->MultiCell(69, 0, $CodigoBarras, 0, 'L', 0, 1);
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaHUAWEI_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Etiqueta Bolsa de Jumper
    public function EtiquetaBolsaJumper($CantidadEtiquetas,$OrdenFabricacion){
        try {
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
            }
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            if($OrdenVenta == ""){
                $OrdenVenta = "N/A";
            }else{
                $OrdenVenta = $OrdenVenta->OrdenVenta;
            }
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(2, 2, 2); 
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->SetAutoPageBreak(TRUE, 0);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);

            // Contador para saber cuántas etiquetas se han colocado en la página
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                $pdf->AddPage('L', array(101, 51));
                $pdf->SetFont('dejavusans', '', 9);
                $pdf->setFontSpacing(-0.2);
                // Color de fondo y texto en la parte superior de la etiqueta
                $posX = 0;
                //Agregar la imagen
                if(!file_exists(storage_path('app/Logos/Optronics.jpg'))){
                    throw new \Exception('No se encontraron el Logo requerido, por favor contactate con TI.');
                }else{
                    $imagePath = storage_path('app/Logos/Optronics.jpg');
                    $pdf->Image($imagePath, 3.5, 6, 35);
                }
                //Se agrega el margen a la pagina
                $margen = 1;
                $border_style = array(
                            'width' => 0.3,
                            'cap' => 'butt',
                            'join' => 'miter',
                            'dash' => 0,
                            'phase' => 0,
                            'color' => array(0, 0, 0) // RGB negro
                        );
                $pdf->RoundedRect(0.5,5, 97, 45, 1, '1111', 'D', $border_style, array());
                $pdf->SetDrawColor(0, 0, 0);
                /*$pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.5);
                $pdf->Rect(3, 3, 95 , 45 );*/
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->Rect(0.5, 15, 97 , 0 );

                $ParteNo = 'Denomination:  '."\n\n\n\n".
                            'Specification:  ';
                $pdf->SetXY($posX+1.5, 17); 
                $pdf->MultiCell(90, 0, $ParteNo, 0, 'L', 0, 1);
                $pdf->SetFont('dejavusans', '', 10);
                $Descripcion= $OrdenFabricacion->Descripcion;
                $pdf->SetXY($posX+25.5, 17); 
                $pdf->MultiCell(66, 0, $Descripcion, 0, 'L', 0, 1);
                //Codigo de barras
                $CodigoBarras = $OrdenFabricacion->Articulo;
                //$pdf->SetXY($posX + 30, 1);
                $pdf->write1DBarcode($CodigoBarras, 'C128',18, 39, 65, 4, 0.4, array(), 'N');
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->setFontSpacing(0);
                $pdf->SetXY($posX+23.5, 33);
                $pdf->MultiCell(65, 0, $CodigoBarras, 0, 'L', 0, 1);
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaBolsaJumper_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Etiqueta de Banderilla QR General
    public function EtiquetaBanderillaQR($PaginaInicio,$PaginaFin,$OrdenFabricacion,$TipoEtiqBan){
        try {
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
            }
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            if($OrdenVenta == ""){
                $OrdenVenta = "N/A";
            }else{
                $OrdenVenta = $OrdenVenta->OrdenVenta;
            }
            // Crear PDF
            $pdf = new TCPDF();
            $pdf->SetMargins(1, 1, 1); 
            $pdf->SetFont('dejavusans', '', 7);
            $pdf->SetAutoPageBreak(TRUE, 0.5);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);
            $OrdenFabricacion5P = substr($OrdenFabricacion->OrdenFabricacion,0,6);
            // Contador para saber cuántas etiquetas se han colocado en la página
            //$x = 0.25;//papelv en blanco x
            //$y = 0.25;//papel en blanco y
            $PaginaFin = $PaginaFin/2;
            for ($i=($PaginaInicio-1); $i<$PaginaFin; $i++) {
                $Aumento = 0;
                $Aumentox  = 0;
                if($TipoEtiqBan == 2){
                    $Aumento = 1.4;
                    $Aumentox = 2; 
                }
                $pdf->SetFont('dejavusans', '', 5);
                $pdf->AddPage('L', array(125, 25));
                // Estilo de línea punteada
                $pdf->SetLineStyle(array(
                    'width' => 0.2,
                    'dash' => '1,1', // patrón punteado: 1 mm línea, 1 mm espacio
                    'color' => array(0, 0, 0) // negro
                ));
                $anchoPagina = 37.5;
                $altoPagina = 225;
                $x = 37.5 + ($anchoPagina / 2);
                $pdf->Line($x, 0, $x, $altoPagina);
                $pdf->SetLineStyle(array(
                    'width' => 0.2,
                    'dash' => '1,1', // patrón punteado: 1 mm línea, 1 mm espacio
                    'color' => array(0, 0, 0) // negro
                ));
                $anchoPagina = 37.5;
                $altoPagina = 225;
                $x = $anchoPagina / 2;
                $pdf->Line($x, 0, $x, $altoPagina);
                //END
                //Codigo 
                //1
                $x = 0;
                $y = 2.7+$Aumento;
                $cx = $x + 17.75 / 2;
                $cy = $y + 4 / 2;
                $pdf->StartTransform();
                $pdf->Rotate(180, $cx, $cy);
                $pdf->SetXY($x, $y); // posición X=3 mm, Y=0 mm
                $pdf->Cell(17.5+$Aumentox, 4,$OrdenFabricacion5P."  ".str_pad(($i+1), 4, '0', STR_PAD_LEFT), 0, 0, 'C'); // ancho=3 mm, alto=4 mm 
                $pdf->StopTransform();

                //2
                $x = 17.75;
                $y = 16.5-$Aumento;
                $pdf->SetXY($x, $y);
                $pdf->Cell(19.5, 2, "  " . $OrdenFabricacion5P . "  " . str_pad(($i + 1), 4, '0', STR_PAD_LEFT), 0, 0, 'C');

                $x = 7;
                $y = 11+$Aumento;
                $cx = $x + 0 / 2;
                $cy = $y + 2 / 2;
                $pdf->StartTransform();
                $pdf->Rotate(90, $cx, $cy);   // rotar 180° sobre el centro del QR
                $CodigoQR = 'https://optronics.com.mx/360/Inspeccion-Visual-1/'.$OrdenFabricacion->OrdenFabricacion.str_pad(($i + 1), 4, '0', STR_PAD_LEFT).".html";
                $pdf->write2DBarcode($CodigoQR, 'QRCODE,H', 3, 7, 12.5, 12.5, null, 'N');
                $pdf->StopTransform();

                $x = 21.5;
                $y = 7.5-$Aumento;
                $cx = $x + 15.5 / 2;
                $cy = $y + 10 / 2;
                $pdf->StartTransform();
                $pdf->Rotate(-90, $cx, $cy);   // rotar 180° sobre el centro del QR
                $pdf->write2DBarcode($CodigoQR,'QRCODE,H',$x,$y,12.5,12.5,null,'N');
                $pdf->StopTransform();
                if($TipoEtiqBan == 2){
                    //Datos de SAP
                    $NumeroEspecial = "";
                    $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta);
                    $NumeroRegistros = count($DatosSAP);
                    if($NumeroRegistros==0){
                        return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es especial para el cliente HUAWEI INTERNATIONAL.']);
                    }else{
                        if($DatosSAP[0]['SubCatNum']==""){
                            return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es especial para el cliente HUAWEI INTERNATIONAL.']);
                        }else{
                            $NumeroEspecial =  $DatosSAP[0]['SubCatNum'];
                        }
                    }
                    $pdf->SetFont('dejavusans', '', 5.5);
                    $pdf->SetXY(18, 18.5-$Aumento);
                    $pdf->Cell(19.5, 2,$NumeroEspecial, 0, 0, 'C');
                    $x = 0;
                    $y = 0+$Aumento;
                    $cx = $x + 16.5 / 2;
                    $cy = $y + 4 / 2;
                    $pdf->StartTransform();
                    $pdf->Rotate(180, $cx, $cy);
                    $pdf->SetXY($x, $y);
                    $pdf->Cell(17.5, 2,$NumeroEspecial, 0, 0, 'C');
                    $pdf->StopTransform();
                }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaVisualQR_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /*public function EtiquetaBanderillaQR($PaginaInicio,$PaginaFin,$OrdenFabricacion,$TipoEtiqBan){
        try {
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
            }
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            if($OrdenVenta == ""){
                $OrdenVenta = "N/A";
            }else{
                $OrdenVenta = $OrdenVenta->OrdenVenta;
            }
            // Crear PDF
            $pdf = new TCPDF();
            $pdf->SetMargins(1, 1, 1); 
            $pdf->SetFont('dejavusans', '', 5.5);
            $pdf->SetAutoPageBreak(TRUE, 0.5);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);
            $OrdenFabricacion5P = substr($OrdenFabricacion->OrdenFabricacion,0,5);
            // Contador para saber cuántas etiquetas se han colocado en la página
            for ($i=($PaginaInicio-1); $i<$PaginaFin; $i++) {
                $Aumento = 0;
                $Aumentox  = 0;
                if($TipoEtiqBan == 2){
                    $Aumento = 1.4;
                    $Aumentox = 2; 
                }
                $pdf->SetFont('dejavusans', '', 4.5);
                $pdf->AddPage('L', array(35, 22));
                // Estilo de línea punteada
                $pdf->SetLineStyle(array(
                    'width' => 0.2,
                    'dash' => '1,1', // patrón punteado: 1 mm línea, 1 mm espacio
                    'color' => array(0, 0, 0) // negro
                ));
                // Coordenadas: dibujar línea horizontal a la mitad de la altura
                $anchoPagina = 35;
                $altoPagina = 22;
                $x = $anchoPagina / 2;
                $pdf->Line($x, 0, $x, $altoPagina);
                //Codigo 
                $x = 0;
                $y = 2.7+$Aumento;
                $cx = $x + 17.5 / 2;
                $cy = $y + 4 / 2;
                $pdf->StartTransform();
                $pdf->Rotate(180, $cx, $cy);
                $pdf->SetXY($x, $y); // posición X=3 mm, Y=0 mm
                $pdf->Cell(17.5+$Aumentox, 4,$OrdenFabricacion5P."  ".str_pad(($i+1), 4, '0', STR_PAD_LEFT), 0, 0, 'C'); // ancho=3 mm, alto=4 mm 
                $pdf->StopTransform();

                $x = 17.5;
                $y = 16.5-$Aumento;
                $pdf->SetXY($x, $y);
                $pdf->Cell(19.5, 2, "  " . $OrdenFabricacion5P . "  " . str_pad(($i + 1), 4, '0', STR_PAD_LEFT), 0, 0, 'C');

                $x = 9;
                $y = 11+$Aumento;
                $cx = $x + 0 / 2;
                $cy = $y + 2 / 2;
                $pdf->StartTransform();
                $pdf->Rotate(90, $cx, $cy);   // rotar 180° sobre el centro del QR
                $CodigoQR = 'https://optronics.com.mx/360/Inspeccion-Visual-1/'.$OrdenFabricacion->OrdenFabricacion.str_pad(($i + 1), 4, '0', STR_PAD_LEFT).".html";
                $pdf->write2DBarcode($CodigoQR, 'QRCODE,H', 3, 7, 12, 12, null, 'N');
                $pdf->StopTransform();

                $x = 20;
                $y = 7-$Aumento;
                $cx = $x + 15.5 / 2;
                $cy = $y + 10 / 2;
                $pdf->StartTransform();
                $pdf->Rotate(-90, $cx, $cy);   // rotar 180° sobre el centro del QR
                $pdf->write2DBarcode($CodigoQR,'QRCODE,H',$x,$y,12,12,null,'N');
                $pdf->StopTransform();
                if($TipoEtiqBan == 2){
                    //Datos de SAP
                    $NumeroEspecial = "";
                    $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta);
                    $NumeroRegistros = count($DatosSAP);
                    if($NumeroRegistros==0){
                        return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es especial para el cliente HUAWEI INTERNATIONAL.']);
                    }else{
                        if($DatosSAP[0]['SubCatNum']==""){
                            return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es especial para el cliente HUAWEI INTERNATIONAL.']);
                        }else{
                            $NumeroEspecial =  $DatosSAP[0]['SubCatNum'];
                        }
                    }
                    $pdf->SetFont('dejavusans', '', 5.5);
                    $pdf->SetXY(18, 18.5-$Aumento);
                    $pdf->Cell(19.5, 2,$NumeroEspecial, 0, 0, 'C');
                    $x = 0;
                    $y = 0+$Aumento;
                    $cx = $x + 16.5 / 2;
                    $cy = $y + 4 / 2;
                    $pdf->StartTransform();
                    $pdf->Rotate(180, $cx, $cy);
                    $pdf->SetXY($x, $y);
                    $pdf->Cell(17.5, 2,$NumeroEspecial, 0, 0, 'C');
                    $pdf->StopTransform();
                }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaVisualQR_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }*/
    //Etiqueta de Número de piezas
    public function EtiquetaNumeroPiezas($Sociedad,$CantidadEtiquetas,$CantidadBolsa,$OrdenFabricacion){
        try {
            $Sociedad = $Sociedad; //Sociedad
            $CantidadBolsa = $CantidadBolsa;
            $AumentoX = 0;
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
            }
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            if($OrdenVenta == ""){
                $OrdenVenta = "N/A";
            }else{
                $OrdenVenta = $OrdenVenta->OrdenVenta;
            }
            //Datos de SAP
            $NoPEDIDO = "";
            $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAPFMX($OrdenVenta);
            $NumeroRegistros = count($DatosSAP);
            if($OrdenVenta != '00000'){
                if($Sociedad == "FMX"){
                    if($NumeroRegistros==0){
                        throw new \Exception('Etiqueta permitida solo para Intercompañias.');
                    }else{
                        if($DatosSAP[0]['NumAtCard']==""){
                            throw new \Exception('No.PEDIDO no encontrado.');
                        }else{
                            $NoPEDIDO =  $DatosSAP[0]['NumAtCard'];
                        }
                    }
                }
            }else{
                $AumentoX = 2;
                $OrdenVenta = "S/N";
                //$NoPEDIDO = "S/N";
            }
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(1, 1, 1); 
            $pdf->SetFont('dejavusans', '', 9);  //dejavusans equivalente a Arial helvetica
            $pdf->SetAutoPageBreak(TRUE, 0.5);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);

            // Contador para saber cuántas etiquetas se han colocado en la página
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                $pdf->AddPage('L', array(51, 24));
                // Color de fondo y texto en la parte superior de la etiqueta
                $posX = 0;
                //Agregar la imagen
                if($Sociedad == 'OPT'){
                    if(!file_exists(storage_path('app/Logos/Optronics.jpg'))){
                        throw new \Exception('No se encontraron el Logo requerido, por favor contactate con TI.');
                    }else{
                        $imagePath = storage_path('app/Logos/Optronics.jpg');
                        $pdf->Image($imagePath, 16, 2, 21);
                        $margen = 1;
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.3);
                        $border_style = array(
                            'width' => 0.1,
                            'cap' => 'butt',
                            'join' => 'miter',
                            'dash' => 0,
                            'phase' => 0,
                            'color' => array(0, 0, 0) // RGB negro
                        );
                        $pdf->RoundedRect(1, 8, 49, 15, 1, '1111', 'D', $border_style, array());
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.2);
                        $pdf->Rect(16.3, 8, 0, 15);
                        
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.2);
                        $pdf->Rect(32.6, 8, 0, 15);
                    
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.2);
                        $pdf->Rect(1, 13.5, 49, 0);
                        $ParteNo = '      OV             OF        CANTIDAD';
                        $pdf->SetFont('dejavusans', '', 8);
                        $pdf->SetXY(0.3, 9);
                        $pdf->MultiCell(51, 0, $ParteNo, 0, 'L', 0, 1);

                        $ParteNo = $OrdenVenta;
                        $pdf->SetFont('dejavusans', 'B', 8);
                        $pdf->SetXY(3.3+$AumentoX, 16);
                        $pdf->MultiCell(13.3, 0, $ParteNo, 0, 'L', 0, 1);
                        $ParteNo = $OrdenFabricacion->OrdenFabricacion;
                        $pdf->SetFont('dejavusans', 'B', 8);
                        $pdf->SetXY(17.6, 16);
                        $pdf->MultiCell(14.3, 0, $ParteNo, 0, 'L', 0, 1);
                        $ParteNo = $CantidadBolsa;
                        $pdf->SetFont('dejavusans', 'B', 8);
                        $pdf->SetXY(38.9, 16);
                        $pdf->MultiCell(13.3, 0, $ParteNo, 0, 'L', 0, 1);
                    }
                }else{
                    if(!file_exists(storage_path('app/Logos/Fibremex.png'))){
                        throw new \Exception('No se encontraron el Logo requerido, por favor contactate con TI.');
                    }else{
                        $imagePath = storage_path('app/Logos/Fibremex.png');
                        $pdf->Image($imagePath, 16, 2, 21);
                        $margen = 1;
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.3);
                        $border_style = array(
                            'width' => 0.1,
                            'cap' => 'butt',
                            'join' => 'miter',
                            'dash' => 0,
                            'phase' => 0,
                            'color' => array(0, 0, 0) // RGB negro
                        );

                        $pdf->RoundedRect(1, 8, 49, 15, 1, '1111', 'D', $border_style, array());
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.2);
                        $pdf->Rect(13.2, 8, 0, 15);
                        
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.2);
                        $pdf->Rect(25.4, 8, 0, 15);
                        
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.2);
                        $pdf->Rect(37.6, 8, 0, 15);

                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.2);
                        $pdf->Rect(1, 13.5, 49, 0);
                        $ParteNo = 'No.PEDIDO        OV             OF       CANTIDAD';
                        $pdf->SetFont('dejavusans', '', 6);
                        $pdf->SetXY(0.3, 10);
                        $pdf->MultiCell(51, 0, $ParteNo, 0, 'L', 0, 1);

                        $ParteNo = $NoPEDIDO;
                        $pdf->SetFont('dejavusans', 'B', 4);
                        $pdf->SetXY(1, 16);
                        $pdf->MultiCell(13.1+$AumentoX, 0, $ParteNo, 0, 'L', 0, 1);
                        $ParteNo = $OrdenVenta;
                        $pdf->SetFont('dejavusans', 'B', 8);
                        $pdf->SetXY(13.3+$AumentoX, 16);
                        $pdf->MultiCell(13.3, 0, $ParteNo, 0, 'L', 0, 1);
                        $ParteNo = $OrdenFabricacion->OrdenFabricacion;
                        $pdf->SetFont('dejavusans', 'B', 8);
                        $pdf->SetXY(24.7, 16);
                        $pdf->MultiCell(14.3, 0, $ParteNo, 0, 'L', 0, 1);
                        $ParteNo = $CantidadBolsa;
                        $pdf->SetFont('dejavusans', 'B', 8);
                        $pdf->SetXY(41.9, 16);
                        $pdf->MultiCell(13.3, 0, $ParteNo, 0, 'L', 0, 1);
                    }
                }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaNumeroPiezas_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
