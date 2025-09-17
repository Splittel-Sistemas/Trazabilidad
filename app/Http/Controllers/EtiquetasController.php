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
            case 'ETIQ4CEDIS':
                return $this->EtiquetaBolsaJumperCEDIS($CantidadEtiquetas,$PDFOrdenFabricacion);
                break;
            case 'ETIQ5':
                return $this->EtiquetaNumeroPiezas($Sociedad,$CantidadEtiquetas,$CantidadBolsa,$PDFOrdenFabricacion);
                break;
            case 'ETIQ6':
                return $this->EtiquetaTrazabilidadMPO($CantidadEtiquetas,$PDFOrdenFabricacion);
                break;
            case 'ETIQ7':
                return $this->EtiquetaInyeccion($CantidadEtiquetas,$PDFOrdenFabricacion);
                break;
            case 'ETIQ8':
                return $this->EtiquetaDivisor($CantidadEtiquetas,$PDFOrdenFabricacion);
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
                $pdf->SetFont('dejavusans', '', 9);
                $pdf->setFontSpacing(-0.2);
                // Color de fondo y texto en la parte superior de la etiqueta
                $posX = 0;
    
                //Agregar la imagen
                if(!file_exists(storage_path('app/Logos/Optronics.jpg'))){
                    return json_encode(["error" => 'No se encontraron el Logo requerido, por favor contactate con TI.']);
                }else{
                    $imagePath = storage_path('app/Logos/Optronics.jpg');
                    $pdf->Image($imagePath, 4, 4, 35);
                }
                //Se agrega el margen a la pagina
                $margen = 2;
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.1);
                $pdf->Rect(4, 13, 92 , 0 );

                $ParteNo = 'Part No:  '.$NumeroHuawei;
                $pdf->SetXY($posX+12, 16); 
                $pdf->MultiCell(90, 0, $ParteNo, 0, 'L', 0, 1);
                $ParteNo = '  Desc:  ';
                $pdf->SetXY($posX+12, 22);
                $pdf->MultiCell(90, 0, $ParteNo, 0, 'L', 0, 1);
                $ParteNo = 'Qty: 1 PCS';
                $pdf->SetXY($posX+70, 16);
                $pdf->MultiCell(40, 0, $ParteNo, 0, 'L', 0, 1);
                $pdf->SetXY($posX+24, 22); 
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
    //Productivo
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
    //Prueba
    public function EtiquetaBolsaJumperCEDIS($CantidadEtiquetas,$OrdenFabricacion){
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
                    $pdf->Image($imagePath, 3.5, 4, 35);
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
                $pdf->RoundedRect(2,3, 97, 45, 1, '1111', 'D', $border_style, array());
                $pdf->SetDrawColor(0, 0, 0);
                /*$pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.5);
                $pdf->Rect(3, 3, 95 , 45 );*/
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->Rect(2, 13, 97 , 0 );

                $ParteNo = 'Denomination:  '."\n\n\n\n".
                            'Specification:  ';
                $pdf->SetXY($posX+3, 16); 
                $pdf->MultiCell(90, 0, $ParteNo, 0, 'L', 0, 1);
                $pdf->SetFont('dejavusans', '', 10);
                $Descripcion= $OrdenFabricacion->Descripcion;
                $pdf->SetXY($posX+27, 16); 
                $pdf->MultiCell(66, 0, $Descripcion, 0, 'L', 0, 1);
                //Codigo de barras
                $CodigoBarras = $OrdenFabricacion->Articulo;
                //$pdf->SetXY($posX + 30, 1);
                $pdf->write1DBarcode($CodigoBarras, 'C128',18, 39, 65, 4, 0.4, array(), 'N');
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->setFontSpacing(0);
                $pdf->SetXY($posX+25, 32);
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
            $TotalPaginas = $PaginaFin;
            $PaginaFin = $PaginaFin/2;
            $NumSerie = 1;
            for ($i=($PaginaInicio-1); $i<$PaginaFin; $i++) {
                $Aumento = 0;
                $Aumentox  = 0;
                $pdf->SetFont('dejavusans', '', 6);
                $pdf->AddPage('L', array(80, 27.5));
                if($TipoEtiqBan == 2){
                    $Aumento = 2;
                    $Aumentox = 0;
                }
                //Codigo 
                //Primer Codigo
                $x = 1;
                $y = 3.5+$Aumento;
                $cx = $x + 17.75 / 2;
                $cy = $y + 4 / 2;
                $pdf->StartTransform();
                $pdf->Rotate(180, $cx, $cy);
                $pdf->SetXY($x, $y); // posición X=3 mm, Y=0 mm
                $pdf->Cell(17.5+$Aumentox, 4,$OrdenFabricacion5P." ".str_pad(($NumSerie), 4, '0', STR_PAD_LEFT), 0, 0, 'C'); // ancho=3 mm, alto=4 mm 
                $pdf->StopTransform();

                $x = 17;
                $y = 20.5-$Aumento;
                $pdf->SetXY($x, $y);
                $pdf->Cell(19.5, 2, "  " . $OrdenFabricacion5P . " " . str_pad(($NumSerie), 4, '0', STR_PAD_LEFT), 0, 0, 'C');

                $x = 3;
                $y = 9;
                $cx = $x + 14/2;//$x + 8 / 2;
                $cy = $y + 14/2;
                $pdf->StartTransform();
                $pdf->Rotate(180, $cx, $cy);   // rotar 180° sobre el centro del QR
                $CodigoQR = 'https://optronics.com.mx/360/Inspeccion-Visual-1/'.$OrdenFabricacion->OrdenFabricacion.str_pad(($NumSerie), 4, '0', STR_PAD_LEFT).".html";
                $pdf->write2DBarcode($CodigoQR, 'QRCODE,H', $x, $y, 14, 14, null, 'N');
                $pdf->StopTransform();

                $x = 20;
                $y = 4.5;
                $pdf->StartTransform();
                $pdf->write2DBarcode($CodigoQR,'QRCODE,H',$x,$y,14,14,null,'N');
                //END
                //Codigo 2
                if($i+1 < $PaginaFin OR (($i+1) == $PaginaFin AND $TotalPaginas%2 == 0)){
                    $x = 40.5;
                    $y = 3.5+$Aumento;
                    $cx = $x + 17.75 / 2;
                    $cy = $y + 4 / 2;
                    $pdf->StartTransform();
                    $pdf->Rotate(180, $cx, $cy);
                    $pdf->SetXY($x, $y); // posición X=3 mm, Y=0 mm
                    $pdf->Cell(17.5+$Aumentox, 4,$OrdenFabricacion5P." ".str_pad(($NumSerie+1), 4, '0', STR_PAD_LEFT), 0, 0, 'C'); // ancho=3 mm, alto=4 mm 
                    $pdf->StopTransform();

                    $x = 57;
                    $y = 20.5-$Aumento;
                    $pdf->SetXY($x, $y);
                    $pdf->Cell(19.5, 2, "  " . $OrdenFabricacion5P . " " . str_pad(($NumSerie+1), 4, '0', STR_PAD_LEFT), 0, 0, 'C');

                    $x = 43;
                    $y = 9;
                    $cx = $x + 14/2;
                    $cy = $y + 14/2;
                    $pdf->StartTransform();
                    $pdf->Rotate(180, $cx, $cy);   // rotar 180° sobre el centro del QR
                    $CodigoQR = 'https://optronics.com.mx/360/Inspeccion-Visual-1/'.$OrdenFabricacion->OrdenFabricacion.str_pad(($NumSerie + 1), 4, '0', STR_PAD_LEFT).".html";
                    $pdf->write2DBarcode($CodigoQR, 'QRCODE,H', $x, $y, 14, 14, null, 'N');
                    $pdf->StopTransform();

                    $x = 60;
                    $y = 4.5;
                    $pdf->StartTransform();
                    $pdf->write2DBarcode($CodigoQR,'QRCODE,H',$x,$y,14,14,null,'N');
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
                        $pdf->SetFont('dejavusans', '', 6);
                        $pdf->SetXY(59, 21);
                        $pdf->Cell(20, 2,$NumeroEspecial, 0, 0, 'C');
                        $x = 40;
                        $y = 0.5+$Aumento;
                        $cx = $x + 17 / 2;
                        $cy = $y + 4 / 2;
                        $pdf->StartTransform();
                        $pdf->Rotate(180, $cx, $cy);
                        $pdf->SetXY($x, $y);
                        $pdf->Cell(17.5, 2,$NumeroEspecial, 0, 0, 'C');
                        $pdf->StopTransform();
                    }
                }
                $NumSerie += 2;
                //END
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
                    $pdf->SetFont('dejavusans', '', 6);
                    $pdf->SetXY(19, 21);
                    $pdf->Cell(20, 2,$NumeroEspecial, 0, 0, 'C');
                    $x = 0.5;
                    $y = 0.5+$Aumento;
                    $cx = $x + 17 / 2;
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
                $pdf->AddPage('L', array(101, 51));
                // Color de fondo y texto en la parte superior de la etiqueta
                $posX = 0;
                //Agregar la imagen
                if($Sociedad == 'OPT'){
                    if(!file_exists(storage_path('app/Logos/Optronics.jpg'))){
                        throw new \Exception('No se encontraron el Logo requerido, por favor contactate con TI.');
                    }else{
                        $imagePath = storage_path('app/Logos/Optronics.jpg');
                        $pdf->Image($imagePath, 31, 4, 40);
                        $margen = 1;
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.4);
                        $border_style = array(
                            'width' => 0.4,
                            'cap' => 'butt',
                            'join' => 'miter',
                            'dash' => 0,
                            'phase' => 0,
                            'color' => array(0, 0, 0) // RGB negro
                        );
                        $pdf->RoundedRect(3, 16, 95, 30, 1, '1111', 'D', $border_style, array());
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.4);
                        $pdf->Rect(34.5, 16, 0, 30);
                        
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.4);
                        $pdf->Rect(67.5, 16, 0, 30);
                    
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.4);
                        $pdf->Rect(3, 26, 95, 0);
                        $ParteNo = '         O.V                  O.F            CANTIDAD';
                        $pdf->SetFont('dejavusans', '', 12);
                        $pdf->SetXY(3, 19);
                        $pdf->MultiCell(95, 0, $ParteNo, 0, 'L', 0, 1);

                        $ParteNo = $OrdenVenta;
                        $pdf->SetFont('dejavusans', 'B', 20);
                        $pdf->SetXY(5+$AumentoX, 32);
                        $pdf->MultiCell(31, 0, $ParteNo, 0, 'L', 0, 1);
                        $ParteNo = $OrdenFabricacion->OrdenFabricacion;
                        $pdf->SetFont('dejavusans', 'B', 18);
                        $pdf->SetXY(37, 33);
                        $pdf->MultiCell(31, 0, $ParteNo, 0, 'L', 0, 1);
                        $ParteNo = $CantidadBolsa;
                        $pdf->SetFont('dejavusans', 'B', 28);
                        $XRestanteCantidad=0;
                        if(strlen($ParteNo)==2){
                            $XRestanteCantidad = 3;
                        }else if(strlen($ParteNo)==3){
                            $XRestanteCantidad = 6;
                        }
                        $pdf->SetXY(78-$XRestanteCantidad, 30);
                        $pdf->MultiCell(31, 0, $ParteNo, 0, 'L', 0, 1);
                    }
                }else{
                    if(!file_exists(storage_path('app/Logos/Fibremex.png'))){
                        throw new \Exception('No se encontraron el Logo requerido, por favor contactate con TI.');
                    }else{
                        $imagePath = storage_path('app/Logos/Fibremex.png');
                        $pdf->Image($imagePath, 28.5, 6, 45);
                        $margen = 1;
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.4);
                        $border_style = array(
                            'width' => 0.4,
                            'cap' => 'butt',
                            'join' => 'miter',
                            'dash' => 0,
                            'phase' => 0,
                            'color' => array(0, 0, 0) // RGB negro
                        );
                        $pdf->RoundedRect(3, 16, 95, 30, 1, '1111', 'D', $border_style, array());
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.4);
                        $pdf->Rect(26.5, 16, 0, 30);
                        
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.4);
                        $pdf->Rect(50.5, 16, 0, 30);
                        
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.4);
                        $pdf->Rect(74.5, 16, 0, 30);

                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.4);
                        $pdf->Rect(3, 26, 95, 0);
                        $ParteNo = 'No.PEDIDO        O.V              O.F        CANTIDAD';
                        $pdf->SetFont('dejavusans', '', 11);
                        $pdf->SetXY(3, 19);
                        $pdf->MultiCell(95, 0, $ParteNo, 0, 'L', 0, 1);

                        $ParteNo = $NoPEDIDO;
                        $pdf->SetFont('dejavusans', 'B', 8);
                        $pdf->SetXY(5, 32);
                        $pdf->MultiCell(23, 0, $ParteNo, 0, 'L', 0, 1);
                        $ParteNo = $OrdenVenta;
                        $pdf->SetFont('dejavusans', 'B', 16);
                        $pdf->SetXY(28, 32);
                        $pdf->MultiCell(23, 0, $ParteNo, 0, 'L', 0, 1);
                        $ParteNo = $OrdenFabricacion->OrdenFabricacion;
                        $pdf->SetFont('dejavusans', 'B', 14);
                        $pdf->SetXY(51, 33);
                        $pdf->MultiCell(23, 0, $ParteNo, 0, 'L', 0, 1);
                        $ParteNo = $CantidadBolsa;
                        $pdf->SetFont('dejavusans', 'B', 28);
                        $XRestanteCantidad=0;
                        if(strlen($ParteNo)==2){
                            $XRestanteCantidad = 4;
                        }else if(strlen($ParteNo)==3){
                            $XRestanteCantidad = 7;
                        }
                        $pdf->SetXY(82-$XRestanteCantidad, 29);
                        $pdf->MultiCell(23, 0, $ParteNo, 0, 'L', 0, 1);
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
    public function EtiquetaTrazabilidadMPO($CantidadEtiquetas,$OrdenFabricacion){
         try {
            $NombreFabricacante = "Optronics S.A. de C.V.";
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first(); 
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
            }
            $NumeroParte = $OrdenFabricacion->Articulo;
            $Descripcion = $OrdenFabricacion->Descripcion;
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            $ValorMedicionA = "PERDIDA DE INSERCCION ≤ 0.50 dB";
            $ValorMedicionB = "PERDIDA DE RETORNO ≥ 20.0 dB";
            $nombre = auth()->user()->name;
            $nombre = $nombre[0];
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
                $pdf->AddPage('P', array(35,22));
                $pdf->SetFont('dejavusans', '', 5);
                $pdf->SetXY(1, 3); 
                $pdf->MultiCell(22, 0, $NombreFabricacante, 0, 'L', 0, 1);
                $pdf->SetFont('dejavusans', 'B', 5);
                $pdf->SetXY(1,6); 
                $pdf->MultiCell(22, 0, $NumeroParte, 0, 'L', 0, 1);
                $pdf->SetFont('dejavusans', '', 5);
                $pdf->SetXY(1,10); 
                $pdf->MultiCell(22, 0, $Descripcion, 0, 'L', 0, 1);
                $pdf->SetXY(1,19); 
                $pdf->MultiCell(22, 0, $ValorMedicionA, 0, 'L', 0, 1);
                $pdf->SetXY(1,25); 
                $pdf->MultiCell(22, 0, $ValorMedicionB, 0, 'L', 0, 1);
                $pdf->SetXY(1,30); 
                $pdf->MultiCell(22, 0, "ORDEN:", 0, 'L', 0, 1);
                $pdf->SetFont('dejavusans', 'B', 5);
                $pdf->SetXY(10,30); 
                $pdf->MultiCell(22, 0, $nombre.$OrdenFabricacion->OrdenFabricacion, 0, 'L', 0, 1);
                $pdf->SetFont('dejavusans', '', 5);
                $pdf->SetXY(18,32); 
                $pdf->MultiCell(22, 0, str_pad(($i+1), 2, '0', STR_PAD_LEFT), 0, 'L', 0, 1);
                /*$pdf->setFontSpacing(-0.2);

                $ParteNo = 'Denomination:  '."\n\n\n\n".
                            'Specification:  ';
                $pdf->SetXY(1.5, 17); 
                $pdf->MultiCell(90, 0, $ParteNo, 0, 'L', 0, 1);
                $pdf->SetFont('dejavusans', '', 10);
                $Descripcion= $OrdenFabricacion->Descripcion;
                $pdf->SetXY(25.5, 17); 
                $pdf->MultiCell(66, 0, $Descripcion, 0, 'L', 0, 1);
                //Codigo de barras
                $CodigoBarras = $OrdenFabricacion->Articulo;
                //$pdf->SetXY($posX + 30, 1);
                $pdf->write1DBarcode($CodigoBarras, 'C128',18, 39, 65, 4, 0.4, array(), 'N');
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->setFontSpacing(0);
                $pdf->SetXY(23.5, 33);
                $pdf->MultiCell(65, 0, $CodigoBarras, 0, 'L', 0, 1);*/
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaBolsaJumper_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function EtiquetaInyeccion($CantidadEtiquetas,$OrdenFabricacion){
         try {
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first(); 
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
            }
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(2, 2, 2); 
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);
            $pdf->SetAutoPageBreak(TRUE, 0);   

            // Contador para saber cuántas etiquetas se han colocado en la página
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                $pdf->AddPage('L', array(51,24));
                if(!file_exists(storage_path('app/Logos/Optronics.jpg'))){
                        throw new \Exception('No se encontraron el Logo requerido, por favor contactate con TI.');
                }else{
                    $imagePath = storage_path('app/Logos/Optronics.jpg');
                    $pdf->Image($imagePath, 18, 1, 15);
                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->SetXY(0, 6);
                    $pdf->MultiCell(51, 0, "FECHA:         GRUPO:", 0, 'L', 0, 1);
                    $pdf->SetXY(0, 10);
                    $pdf->MultiCell(51, 0, "HORA DE INGRESO:", 0, 'L', 0, 1);
                    $pdf->SetXY(0, 14);
                    $pdf->MultiCell(51, 0, "CONECTORES INYECTADOS:", 0, 'L', 0, 1);
                    $pdf->SetXY(0, 18);
                    $pdf->MultiCell(51, 0, "HORA DE DESECHO:", 0, 'L', 0, 1);
                }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaBolsaJumper_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function EtiquetaDivisor($CantidadEtiquetas,$OrdenFabricacion){
        try {
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(1, 1, 1); 
            $pdf->SetAutoPageBreak(TRUE, 0.5);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);

            // Contador para saber cuántas etiquetas se han colocado en la página
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                 $pdf->SetFont('helvetica', '', 10);
                $pdf->AddPage('L', array(35, 22));
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->Rect(3, 4, 8 , 5 );
                $pdf->Rect(13, 4, 8 , 5 );
                $pdf->Rect(23, 4, 8 , 5 );
                $pdf->Rect(3, 13, 8 , 5 );
                $pdf->Rect(13, 13, 8 , 5 );
                $pdf->Rect(23, 13, 8 , 5 );

                $pdf->SetXY(12.5, 4);
                $pdf->MultiCell(51, 0, "OUT", 0, 'L', 0, 1);
                $pdf->SetXY(22.5, 4);
                $pdf->MultiCell(51, 0, "50%", 0, 'L', 0, 1);

                $pdf->SetXY(3, 13);
                $pdf->MultiCell(51, 0, "50%", 0, 'L', 0, 1);
                $pdf->SetXY(12.5, 13);
                $pdf->MultiCell(51, 0, "50%", 0, 'L', 0, 1);
                $pdf->SetXY(22.5, 13);
                $pdf->MultiCell(51, 0, "50%", 0, 'L', 0, 1);

                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetXY(4.5, 4);
                $pdf->MultiCell(51, 0, "IN", 0, 'L', 0, 1);
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaNumeroPiezas_'.$OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
