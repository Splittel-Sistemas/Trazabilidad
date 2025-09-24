<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partidasof_Areas;
use App\Models\PartidasOF;
use App\Models\OrdenFabricacion;
use App\Models\OrdenVenta;
use Carbon\Carbon;
use TCPDF;
use Illuminate\Support\Facades\Auth;
class EtiquetasController extends Controller
{
    protected $funcionesGenerales;
    protected $HuaweiInternacional;
    protected $Nokia;
    protected $Drai;
    public function __construct(FuncionesGeneralesController $funcionesGenerales){
        $this->funcionesGenerales = $funcionesGenerales;
        $this->HuaweiInternacional = "C0563";
        $this->Nokia = "C0675";
        $this->Drai = "C0003";
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
        $PorcentajeA = $request->PorcentajeA;
        $PorcentajeB = $request->PorcentajeB;
        $CodigoCliente = $request->CodigoCliente;
        $Insercion = $request->Insercion;
        $Retorno = $request->Retorno;
        $CantidadCajas = $request->CantidadCajas;
        switch($TipoEtiqueta){
            case 'ETIQ1':
                return $this->EtiquetaHUAWEI($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$CodigoCliente);
                break;
            case 'ETIQ2':
                return $this->EtiquetaBanderillaQR($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,1,$CodigoCliente);
                break;
            case 'ETIQ3':
                return $this->EtiquetaBanderillaQR($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,2,$CodigoCliente);
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
                return $this->EtiquetaTrazabilidadMPO($PaginaInicio,$PaginaFin,$Insercion,$Retorno,$PDFOrdenFabricacion,$CodigoCliente);
                break;
            case 'ETIQ7':
                return $this->EtiquetaInyeccion($CantidadEtiquetas,$PDFOrdenFabricacion);
                break;
            case 'ETIQ8':
                return $this->EtiquetaDivisor($CantidadEtiquetas,$PDFOrdenFabricacion,$PorcentajeA,$PorcentajeB);
                break;
            case 'ETIQ9':
                return $this->EtiquetaCajaHuawei($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$CantidadCajas,$CantidadBolsa,$CodigoCliente);
                break;
            case 'ETIQ10':
                return $this->EtiquetaCajaNokia($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$CantidadEtiquetas,$CantidadBolsa,$CodigoCliente);
                break;
            default:
                break;
        }return $request;
    }
    //Etiqueta HUAWEI
    public function EtiquetaHUAWEI($PaginaInicio,$PaginaFin,$OrdenFabricacion,$CodigoCliente){
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
                    if($CodigoCliente != $this->HuaweiInternacional){
                        return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es especial para el cliente HUAWEI INTERNATIONAL.']);
                    }
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
                $fecha = Carbon::parse($DatosSAP[0]["DocDate"]);
                $Year = $fecha->isoFormat('GG');
                $Year = substr($Year, -2);
                $NumeroSemana = $fecha->isoWeek(); 
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
    public function EtiquetaBanderillaQR($PaginaInicio,$PaginaFin,$OrdenFabricacion,$TipoEtiqBan,$CodigoCliente){
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
            $DatosSAP = [];
            if($TipoEtiqBan == 2){
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta);
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
                $AumentoLetra = 0;
                $pdf->SetFont('dejavusans', 'B', 4.75);
                $pdf->AddPage('L', array(80, 27.5));
                if($TipoEtiqBan == 2){
                    $Aumento = 1;
                    $Aumentox = 0;
                    $AumentoLetra = 0.5;
                }
                //Codigo 
                //Primer Codigo
                $x = 1;
                $y = 3.5+$Aumento+$AumentoLetra;
                $cx = $x + 17.75 / 2;
                $cy = $y + 4 / 2;
                $pdf->StartTransform();
                $pdf->Rotate(180, $cx, $cy);
                $pdf->SetXY($x, $y); // posición X=3 mm, Y=0 mm
                $pdf->Cell(17.5, 4,$OrdenFabricacion5P." ".str_pad(($NumSerie), 4, '0', STR_PAD_LEFT), 0, 0, 'C'); // ancho=3 mm, alto=4 mm 
                $pdf->StopTransform();

                $x = 16;
                $y = 18-$Aumento-$AumentoLetra ;
                $pdf->SetXY($x, $y);
                $pdf->Cell(19.5, 2, "  " . $OrdenFabricacion5P . " " . str_pad(($NumSerie), 4, '0', STR_PAD_LEFT), 0, 0, 'C');

                $x = 2;
                $y = 6+$Aumento;
                $cx = $x + 14/2;//$x + 8 / 2;
                $cy = $y + 14/2;
                $pdf->StartTransform();
                $pdf->Rotate(180, $cx, $cy);   // rotar 180° sobre el centro del QR
                $CodigoQR = 'https://optronics.com.mx/360/Inspeccion-Visual-1/'.$OrdenFabricacion->OrdenFabricacion.str_pad(($NumSerie), 4, '0', STR_PAD_LEFT).".html";
                $pdf->write2DBarcode($CodigoQR, 'QRCODE,M', $x, $y, 12, 12, null, 'N');
                $pdf->StopTransform();

                $x = 20;
                $y = 4.5-$Aumento;
                $pdf->write2DBarcode($CodigoQR,'QRCODE,M',$x,$y,12,12,null,'N');
                //END
                //Codigo 2
                if($i+1 < $PaginaFin OR (($i+1) == $PaginaFin AND $TotalPaginas%2 == 0)){
                    $x = 39.5;
                    $y = 3.5+$Aumento+$AumentoLetra ;
                    $cx = $x + 17.75 / 2;
                    $cy = $y + 4 / 2;
                    $pdf->StartTransform();
                    $pdf->Rotate(180, $cx, $cy);
                    $pdf->SetXY($x, $y); // posición X=3 mm, Y=0 mm
                    $pdf->Cell(17.5, 4,$OrdenFabricacion5P." ".str_pad(($NumSerie+1), 4, '0', STR_PAD_LEFT), 0, 0, 'C'); // ancho=3 mm, alto=4 mm 
                    $pdf->StopTransform();

                    $x = 54;
                    $y = 18-$Aumento-$AumentoLetra ;
                    $pdf->SetXY($x, $y);
                    $pdf->Cell(19.5, 2, "  " . $OrdenFabricacion5P . " " . str_pad(($NumSerie+1), 4, '0', STR_PAD_LEFT), 0, 0, 'C');

                    $x = 41;
                    $y = 6+$Aumento;
                    $cx = $x + 14/2;
                    $cy = $y + 14/2;
                    $pdf->StartTransform();
                    $pdf->Rotate(180, $cx, $cy);   // rotar 180° sobre el centro del QR
                    $CodigoQR = 'https://optronics.com.mx/360/Inspeccion-Visual-1/'.$OrdenFabricacion->OrdenFabricacion.str_pad(($NumSerie + 1), 4, '0', STR_PAD_LEFT).".html";
                    $pdf->write2DBarcode($CodigoQR, 'QRCODE,M', $x, $y, 12, 12, null, 'N');
                    $pdf->StopTransform();
                    $x = 58;
                    $y = 4.5-$Aumento;
                    $pdf->StartTransform();
                    $pdf->write2DBarcode($CodigoQR,'QRCODE,M',$x,$y,12,12,null,'N');
                    $LetraEspecial = 0;
                    $AumentoLetraEspecial = 0;
                    if($TipoEtiqBan == 2){
                        //Datos de SAP
                        $NumeroEspecial = "";
                        $NumeroRegistros = count($DatosSAP);
                        if($NumeroRegistros==0){
                            return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es solo para Cliente Especial.']);
                        }else{
                            if($DatosSAP[0]['SubCatNum']==""){
                                return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es solo para Cliente Especial.']);
                            }else{
                                if($CodigoCliente == $this->HuaweiInternacional){
                                    $NumeroEspecial =  $DatosSAP[0]['SubCatNum'];
                                }elseif($CodigoCliente == $this->Nokia){
                                    $NumeroEspecial =  $DatosSAP[0]['ItemCode'];
                                    $LetraEspecial = 1.5;
                                    $AumentoLetraEspecial =0.5;
                                }else{
                                    return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es solo para Cliente Especial.']);
                                }
                            }
                        }
                        $pdf->SetFont('dejavusans', 'B', 5.5-$LetraEspecial);
                        $pdf->SetXY(55-$AumentoLetraEspecial, 19);
                        $pdf->Cell(20, 2,$NumeroEspecial, 0, 0, 'C');
                        $x = 39.5+$AumentoLetraEspecial;
                        $y = 1.5;
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
                    $NumeroRegistros = count($DatosSAP);
                    $LetraEspecial = 0;
                    $AumentoLetraEspecial = 0;
                    if($NumeroRegistros==0){
                        return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es solo para Cliente Especial.']);
                        }else{
                        if($DatosSAP[0]['SubCatNum']==""){
                            return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es solo para Cliente Especial']);
                        }else{
                            if($CodigoCliente == $this->HuaweiInternacional){
                                $NumeroEspecial =  $DatosSAP[0]['SubCatNum'];
                            }elseif($CodigoCliente == $this->Nokia){
                                $NumeroEspecial =  $DatosSAP[0]['ItemCode'];
                                $LetraEspecial = 1.5;
                                $AumentoLetraEspecial = 0.5;
                            }else{
                                return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es solo para Cliente Especial.']);
                            }
                        }
                    }
                    $pdf->SetFont('dejavusans', 'B', 5.5-$LetraEspecial);
                    $pdf->SetXY(17-$AumentoLetraEspecial, 19);
                    $pdf->Cell(20, 2,$NumeroEspecial, 0, 0, 'C');
                    $x = 1+$AumentoLetraEspecial;
                    $y = 1.5;
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
    /*public function EtiquetaTrazabilidadMPO($CantidadEtiquetas,$OrdenFabricacion){
        try {
            // create new PDF document
            $pdf = new TCPDF();

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Nicola Asuni');
            $pdf->SetTitle('TCPDF Example 060');
            $pdf->SetSubject('TCPDF Tutorial');
            $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

            // set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 060', PDF_HEADER_STRING);

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $pdf->setLanguageArray($l);
            }

            // set font
            $pdf->SetFont('helvetica', '', 20);

            // ---------------------------------------------------------

            // set page format (read source code documentation for further information)
            $page_format = array(
                'MediaBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 210, 'ury' => 297),
                'CropBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 210, 'ury' => 297),
                'BleedBox' => array ('llx' => 5, 'lly' => 5, 'urx' => 205, 'ury' => 292),
                'TrimBox' => array ('llx' => 10, 'lly' => 10, 'urx' => 200, 'ury' => 287),
                'ArtBox' => array ('llx' => 15, 'lly' => 15, 'urx' => 195, 'ury' => 282),
                'Dur' => 3,
                'trans' => array(
                    'D' => 1.5,
                    'S' => 'Split',
                    'Dm' => 'V',
                    'M' => 'O'
                ),
                'Rotate' => 90,
                'PZ' => 1,
            );

            // Check the example n. 29 for viewer preferences

            // add first page ---
            $pdf->AddPage('P', $page_format, false, false);
            $pdf->Cell(0, 12, 'First Page', 1, 1, 'C');

            // add second page ---
            $page_format['Rotate'] =90;
            $pdf->AddPage('P', $page_format, false, false);
            $pdf->Cell(0, 12, 'Second Page', 1, 1, 'C');

            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaBolsaJumper_'.date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }*/
    public function EtiquetaTrazabilidadMPO($PaginaInicio,$PaginaFin,$Insercion,$Retorno,$OrdenFabricacion,$CodigoCliente){
        try {
            if ($Insercion != floor($Insercion)) {
                    $Insercion = number_format((float)$Insercion, 2);
            }else{
                $Insercion = $Insercion = number_format((float)$Insercion, 1);
            }
            if ($Retorno != floor($Retorno)) {
                    $Retorno = number_format((float)$Retorno, 2);
            }else{
                $Retorno = $Retorno = number_format((float)$Retorno, 1);
            }
            $NombreFabricante = "Optronics S.A. de C.V.";
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first(); 
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']); return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
            }
            if ($PaginaFin<$PaginaInicio) {
                return json_encode(["error" => 'Página de inicio tiene que ser menor a Página fin.']);
            }
            $NumeroParte = "";
            if($this->Nokia == $CodigoCliente OR $this->HuaweiInternacional == $CodigoCliente OR $this->Drai == $CodigoCliente){
                $NumOV = $OrdenFabricacion->OrdenVenta->OrdenVenta;
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($NumOV);
                if($this->HuaweiInternacional == $CodigoCliente){
                    $NumeroParte = $DatosSAP[0]["SubCatNum"];
                }elseif($this->Nokia == $CodigoCliente OR $this->Drai == $CodigoCliente){
                    $NumeroParte = $DatosSAP[0]["ItemCode"];
                }
            }else{
                $NumeroParte = $OrdenFabricacion->Articulo;
            }
            $Descripcion = $OrdenFabricacion->Descripcion;
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            $ValorMedicionA = "PERDIDA DE INSERCCION ≤ ".$Insercion." dB";
            $ValorMedicionB = "PERDIDA DE RETORNO   ≥ ".$Retorno." dB";
            $nombre = auth()->user()->name;
            $nombre = $nombre[0];
            if($OrdenVenta == ""){
                $OrdenVenta = "N/A";
            }else{
                $OrdenVenta = $OrdenVenta->OrdenVenta;
            }
            // Crear PDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // Ajustar márgenes
            $pdf->SetMargins(2, 2, 2); 
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->SetAutoPageBreak(TRUE, 0);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);

            // Contador para saber cuántas etiquetas se han colocado en la página
            $CompletoCantidadEtiquetas = $PaginaFin;
            $CantidadEtiquetas = $CompletoCantidadEtiquetas/2;
            $serial = $PaginaInicio;
            $PaginaInicio = ($PaginaInicio-1)/2;
            //return $PaginaInicio."     ".$CantidadEtiquetas;
            for ($i=$PaginaInicio; $i<$CantidadEtiquetas; $i++) {
                //Sirve para girar la pagina 270 grados
                //Si hay duda revisar documentacion Tcpdf Ejemplo 060: configuración de página avanzada
                $page_format = array(
                    'MediaBox' => array('llx' => 0, 'lly' => 0, 'urx' => 80, 'ury' => 27.5),
                    'CropBox' => array('llx' => 0, 'lly' => 0, 'urx' => 80, 'ury' => 27.5),
                    'BleedBox' => array('llx' => 2, 'lly' => 2, 'urx' => 78, 'ury' => 25.5),
                    'TrimBox' => array('llx' => 4, 'lly' => 4, 'urx' => 76, 'ury' => 23.5),
                    'ArtBox' => array('llx' => 6, 'lly' => 6, 'urx' => 74, 'ury' => 21.5),
                    'Dur' => 3,
                    'trans' => array(
                        'D' => 1.5,
                        'S' => 'Split',
                        'Dm' => 'V',
                        'M' => 'O'
                    ),
                    'Rotate' => 270,
                    'PZ' => 1,
                );
                $pdf->AddPage('P',$page_format,false, false); //Cada Etiqueta sera de 35X22
                $pdf->SetFont('dejavusans', '', 5);
                    $Resx = 40;
                    $pdf->SetXY(3,3); 
                    $pdf->MultiCell(23, 0, $NombreFabricante, 0, 'L', 0, 1);
                    $pdf->SetFont('dejavusans', 'B', 4.2);
                    $pdf->SetXY(3,6); 
                    $pdf->MultiCell(23, 0, $NumeroParte, 0, 'L', 0, 1);
                    $pdf->SetFont('dejavusans', '', 5);
                    $pdf->SetXY(3,8); 
                    $pdf->MultiCell(23, 0, $Descripcion, 0, 'L', 0, 1);
                    $pdf->SetFont('dejavusans', '', 4.5);
                    $pdf->SetXY(3,19); 
                    $pdf->MultiCell(23, 0, $ValorMedicionA, 0, 'L', 0, 1);
                    $pdf->SetXY(3,23); 
                    $pdf->MultiCell(23, 0, $ValorMedicionB, 0, 'L', 0, 1);
                    $pdf->SetXY(3,28); 
                    $pdf->MultiCell(23, 0, "ORDEN:", 0, 'L', 0, 1);
                    $pdf->SetFont('dejavusans', 'B', 5);
                    $pdf->SetXY(13,28); 
                    $pdf->MultiCell(23, 0, $nombre.$OrdenFabricacion->OrdenFabricacion, 0, 'L', 0, 1);
                    $pdf->SetFont('dejavusans', '', 5);
                    $pdf->SetXY(21,30); 
                    $pdf->MultiCell(23, 0, str_pad(($serial), 2, '0', STR_PAD_LEFT), 0, 'L', 0, 1);
                    $serial++;
                    if($serial<=$CompletoCantidadEtiquetas){
                        $pdf->SetFont('dejavusans', '', 5);
                        $pdf->SetXY(3,48); 
                        $pdf->MultiCell(23, 0, $NombreFabricante, 0, 'L', 0, 1);
                        $pdf->SetFont('dejavusans', 'B', 4.2);
                        $pdf->SetXY(3,51); 
                        $pdf->MultiCell(23, 0, $NumeroParte, 0, 'L', 0, 1);
                        $pdf->SetFont('dejavusans', '', 5);
                        $pdf->SetXY(3,53); 
                        $pdf->MultiCell(23, 0, $Descripcion, 0, 'L', 0, 1);
                        $pdf->SetFont('dejavusans', '', 4.5);
                        $pdf->SetXY(3,64); 
                        $pdf->MultiCell(23, 0, $ValorMedicionA, 0, 'L', 0, 1);
                        $pdf->SetXY(3,68); 
                        $pdf->MultiCell(23, 0, $ValorMedicionB, 0, 'L', 0, 1);
                        $pdf->SetXY(3,73); 
                        $pdf->MultiCell(23, 0, "ORDEN:", 0, 'L', 0, 1);
                        $pdf->SetFont('dejavusans', 'B', 5);
                        $pdf->SetXY(13,73); 
                        $pdf->MultiCell(23, 0, $nombre.$OrdenFabricacion->OrdenFabricacion, 0, 'L', 0, 1);
                        $pdf->SetFont('dejavusans', '', 5);
                        $pdf->SetXY(21,75); 
                        $pdf->MultiCell(23, 0, str_pad(($serial), 2, '0', STR_PAD_LEFT), 0, 'L', 0, 1);
                        $serial++;
                    }
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
            $ResiduoCantidadEtiquetas = $CantidadEtiquetas % 2;
            $CantidadEtiquetas = $CantidadEtiquetas / 2;
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                $pdf->AddPage('L', array(108,28));
                if(!file_exists(storage_path('app/Logos/Optronics.jpg'))){
                        throw new \Exception('No se encontraron el Logo requerido, por favor contactate con TI.');
                }else{
                    $imagePath = storage_path('app/Logos/Optronics.jpg');
                    $pdf->Image($imagePath, 20.5, 1, 15);
                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->setFontSpacing(0);                  // Sin espaciado adicional entre letras
                    $pdf->setCellPadding(0);
                    $pdf->SetXY(3, 6);
                    $pdf->MultiCell(51, 0, "FECHA:                    GRUPO:", 0, 'L', 0, 1);
                    $pdf->SetXY(3, 11);
                    $pdf->MultiCell(51, 0, "HORA DE INGRESO:", 0, 'L', 0, 1);
                    $pdf->SetXY(3, 16);
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->setFontSpacing(-0.3);
                    $pdf->MultiCell(51, 0, "CONECTORES INYECTADOS:", 0, 'L', 0, 1);
                    $pdf->SetXY(3, 20);
                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->MultiCell(51, 0, "HORA DE DESECHO:", 0, 'L', 0, 1);
                    if(($i+1 < $CantidadEtiquetas) OR ($ResiduoCantidadEtiquetas == 0)){
                        $pdf->Image($imagePath, 72.5, 1, 15);
                        $pdf->SetFont('helvetica', '', 9);
                        $pdf->setFontSpacing(0);                  // Sin espaciado adicional entre letras
                        $pdf->setCellPadding(0);
                        $pdf->SetXY(58, 6);
                        $pdf->MultiCell(51, 0, "FECHA:                    GRUPO:", 0, 'L', 0, 1);
                        $pdf->SetXY(58, 11);
                        $pdf->MultiCell(51, 0, "HORA DE INGRESO:", 0, 'L', 0, 1);
                        $pdf->SetXY(58, 16);
                        $pdf->SetFont('helvetica', '', 8);
                        $pdf->setFontSpacing(-0.3);
                        $pdf->MultiCell(51, 0, "CONECTORES INYECTADOS :", 0, 'L', 0, 1);
                        $pdf->SetXY(58, 20);
                        $pdf->SetFont('helvetica', '', 9);
                        $pdf->MultiCell(51, 0, "HORA DE DESECHO:", 0, 'L', 0, 1);
                    }
                }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaBolsaJumper_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function EtiquetaDivisor($CantidadEtiquetas,$OrdenFabricacion,$PorcentajeA,$PorcentajeB){
        try {
            if($PorcentajeA <1 OR $PorcentajeB<1){
                return response()->json(['error' => "Medida 1 y Medida 2 tienen que ser mayor a 0"], 500);
            }
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(1, 1, 1); 
            $pdf->SetFont('dejavusans', '', 10);  //dejavusans equivalente a Arial helvetica
            $pdf->SetAutoPageBreak(TRUE, 0.5);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);

            // Contador para saber cuántas etiquetas se han colocado en la página
            $ResiduoCantidadEtiquetas = $CantidadEtiquetas%3;
            $CantidadEtiquetas = intval($CantidadEtiquetas/3);
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                $pdf->SetFont('helvetica', 'B', 9.5);
                $pdf->AddPage('L', array(114, 25));
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                //Cuadro 1
                $pdf->Rect(3, 6, 8 , 5 );
                $pdf->Rect(11, 6, 8 , 5 );
                $pdf->Rect(19, 6, 8 , 5 );
                $pdf->Rect(3, 11, 8 , 5 );
                $pdf->Rect(11, 11, 8 , 5 );
                $pdf->Rect(19, 11, 8 , 5 );
                $pdf->SetXY(19, 6);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(3, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(10.7, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetXY(19, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetXY(10.3, 6);
                $pdf->MultiCell(10, 0, "OUT", 0, 'L', 0, 1);
                $pdf->SetXY(4, 6);
                $pdf->MultiCell(10, 0, "IN", 0, 'L', 0, 1);
                // Cuadro 2
                $pdf->SetFont('helvetica', 'B', 9.5);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->Rect(41, 6, 8 , 5 );
                $pdf->Rect(49, 6, 8 , 5 );
                $pdf->Rect(57, 6, 8 , 5 );
                $pdf->Rect(41, 11, 8 , 5 );
                $pdf->Rect(49, 11, 8 , 5 );
                $pdf->Rect(57, 11, 8 , 5 );
                $pdf->SetXY(57, 6);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(41, 12);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(49, 12);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetXY(57, 12);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);

                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetXY(48.3, 6);
                $pdf->MultiCell(10, 0, "OUT", 0, 'L', 0, 1);
                $pdf->SetXY(42, 6);
                $pdf->MultiCell(10, 0, "IN", 0, 'L', 0, 1);
                // Cuadro 3
                $pdf->SetFont('helvetica', 'B', 9.5);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->Rect(75, 6, 8 , 5 );
                $pdf->Rect(83, 6, 8 , 5 );
                $pdf->Rect(91, 6, 8 , 5 );
                $pdf->Rect(75, 11, 8 , 5 );
                $pdf->Rect(83, 11, 8 , 5 );
                $pdf->Rect(91, 11, 8 , 5 );
                $pdf->SetXY(91, 6);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(75, 12);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(83, 12);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetXY(91, 12);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);

                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetXY(82.3, 6);
                $pdf->MultiCell(10, 0, "OUT", 0, 'L', 0, 1);
                $pdf->SetXY(76, 6);
                $pdf->MultiCell(10, 0, "IN", 0, 'L', 0, 1);
            }
            if($ResiduoCantidadEtiquetas >= 1){
                $pdf->SetFont('helvetica', 'B', 9.5);
                $pdf->AddPage('L', array(114, 25));
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                //Cuadro 1
                $pdf->Rect(3, 6, 8 , 5 );
                $pdf->Rect(11, 6, 8 , 5 );
                $pdf->Rect(19, 6, 8 , 5 );
                $pdf->Rect(3, 11, 8 , 5 );
                $pdf->Rect(11, 11, 8 , 5 );
                $pdf->Rect(19, 11, 8 , 5 );
                $pdf->SetXY(19, 6);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(3, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(10.7, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetXY(19, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetXY(10.3, 6);
                $pdf->MultiCell(10, 0, "OUT", 0, 'L', 0, 1);
                $pdf->SetXY(4, 6);
                $pdf->MultiCell(10, 0, "IN", 0, 'L', 0, 1);
            }
            if($ResiduoCantidadEtiquetas >= 2){
               $pdf->SetFont('helvetica', 'B', 9.5);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->Rect(41, 6, 8 , 5 );
                $pdf->Rect(49, 6, 8 , 5 );
                $pdf->Rect(57, 6, 8 , 5 );
                $pdf->Rect(41, 11, 8 , 5 );
                $pdf->Rect(49, 11, 8 , 5 );
                $pdf->Rect(57, 11, 8 , 5 );
                $pdf->SetXY(57, 6);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(41, 12);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(49, 12);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetXY(57, 12);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);

                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetXY(48.3, 6);
                $pdf->MultiCell(10, 0, "OUT", 0, 'L', 0, 1);
                $pdf->SetXY(42, 6);
                $pdf->MultiCell(10, 0, "IN", 0, 'L', 0, 1);
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaNumeroPiezas_'.$OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function EtiquetaCajaNokia($PaginaInicio,$PaginaFin,$OrdenFabricacion,$CantidadEtiquetas,$CantidadBolsa,$CodigoCliente){
        if($CodigoCliente == $this->Nokia){
            try {
                $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
                if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                    return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
                }
                if ($PaginaFin<$PaginaInicio) {
                    return json_encode(["error" => 'Página de inicio tiene que ser menor a Página fin.']);
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
                // Crear PDF
                $pdf = new TCPDF();
                // Ajustar márgenes
                $pdf->SetMargins(1, 1, 1); 
                $pdf->SetFont('dejavusans', '', 9);  //dejavusans equivalente a Arial helvetica
                $pdf->SetAutoPageBreak(TRUE, 0.5);   
                $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
                $pdf->SetPrintHeader(false);

                // Contador para saber cuántas etiquetas se han colocado en la página
                $PaginaInicio = ($PaginaInicio<1)?1:$PaginaInicio;
                $NumOV = $OrdenFabricacion->OrdenVenta->OrdenVenta;
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($NumOV);
                for ($i=($PaginaInicio-1); $i<$PaginaFin; $i++) {
                    $page_format = array(
                        'MediaBox' => array('llx' => 0, 'lly' => 0, 'urx' => 101, 'ury' => 66),
                        'CropBox' => array('llx' => 0, 'lly' => 0, 'urx' => 101, 'ury' => 66),
                        'BleedBox' => array('llx' => 2, 'lly' => 2, 'urx' => 99, 'ury' => 64),
                        'TrimBox' => array('llx' => 4, 'lly' => 4, 'urx' => 97, 'ury' => 63),
                        'ArtBox' => array('llx' => 6, 'lly' => 6, 'urx' => 94, 'ury' => 61),
                        'Dur' => 3,
                        'trans' => array(
                            'D' => 1.5,
                            'S' => 'Split',
                            'Dm' => 'V',
                            'M' => 'O'
                        ),
                        'Rotate' => 270,
                        'PZ' => 1,
                    );
                    $pdf->AddPage('P', $page_format,false,false);
                    $pdf->SetFont('times', 'B', 10);
                    $pdf->setFontSpacing(-0.2);
                    $pdf->SetXY(3, 3);
                    $pdf->Cell(63, 6, "Descripción:", 0, 1, 'L', 0);
                    $pdf->SetXY(9, 9);
                    $pdf->MultiCell(56, 0, $OrdenFabricacion->Descripcion, 0, 'L', 0, 1);
                    $pdf->SetXY(3, 27);
                    $pdf->Cell(63, 6, "No. departe:", 0, 1, 'L', 0);
                    $x = 10;
                    $y = 34;
                    $w = 38;
                    $h = 5.5;
                    $NoParte = $OrdenFabricacion->Articulo;
                    $pdf->write1DBarcode($NoParte,'C128', $x, $y, $w, $h, 0.4, null, 'L');
                    $textWidth = $pdf->GetStringWidth($NoParte);
                    $textX = $x + ($w - $textWidth) / 2;
                    $textY = $y + $h ;
                    $pdf->SetXY($textX, $textY);
                    $pdf->Cell($textWidth, 4, $NoParte, 0, 1, 'C');
                    $pdf->SetXY(3, 45);
                    $pdf->Cell(63, 6, "Codigo especial:", 0, 1, 'L', 0);
                    $CodigoEspecial = $DatosSAP[0]["ItemCode"];
                    $x = 10;
                    $y = 52;
                    $w = 45;
                    $h = 5.5;
                    $pdf->write1DBarcode($CodigoEspecial,'C128', $x, $y, $w, $h, 0.4, null, 'L');
                    $textWidth = $pdf->GetStringWidth($CodigoEspecial);
                    $textX = $x + ($w - $textWidth) / 2;
                    $textY = $y + $h ;
                    $pdf->SetXY($textX, $textY);
                    $pdf->Cell($textWidth, 4, $CodigoEspecial , 0, 1, 'C');
                    $pdf->SetXY(3, 63);
                    $pdf->Cell(63, 6, "(O.V.)         ".$NumOV, 0, 1, 'L', 0);
                    $pdf->SetXY(3, 68);
                    $pdf->Cell(63, 6, "Caja            ".($i+1)." / ".$CantidadEtiquetas, 0, 1, 'L', 0);
                    $pdf->SetXY(3, 80);
                    $pdf->Cell(20, 6, "Cantidad", 0, 1, 'L', 0);
                    $pdf->SetXY(50, 80);
                    $pdf->Cell(10, 6, "PCS", 0, 1, 'L', 0);
                    $x = 25;
                    $y = 80;
                    $w = 20;
                    $h = 5.5;
                    $pdf->write1DBarcode($CantidadBolsa,'C128', $x, $y, $w, $h, 0.4, null, 'L');
                    $textWidth = $pdf->GetStringWidth($CantidadBolsa);
                    $textX = $x + ($w - $textWidth) / 2;
                    $textY = $y + $h ;
                    $pdf->SetXY($textX, $textY);
                    $pdf->Cell($textWidth, 4, $CantidadBolsa , 0, 1, 'C');
                }
                ob_end_clean();
                // Generar el archivo PDF y devolverlo al navegador
                return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaHUAWEI_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }else{
            return response()->json(['error' => "Etiqueta permitida solo para el cliente NOKIA."], 500);
        }
    }
    public function EtiquetaCajaHuawei($PaginaInicio,$PaginaFin,$OrdenFabricacion,$CantidadCajas,$CantidadBolsa,$CodigoCliente){
        if($CodigoCliente == $this->HuaweiInternacional){
            try {
                $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
                if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                    return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
                }
                if ($PaginaFin<$PaginaInicio) {
                    return json_encode(["error" => 'Página de inicio tiene que ser menor a Página fin.']);
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
                // Crear PDF
                $pdf = new TCPDF();
                // Ajustar márgenes
                $pdf->SetMargins(1, 1, 1); 
                $pdf->SetAutoPageBreak(TRUE, 0.5);   
                $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
                $pdf->SetPrintHeader(false);

                // Contador para saber cuántas etiquetas se han colocado en la página
                $PaginaFin = ($PaginaFin>$CantidadCajas)?$CantidadCajas:$PaginaFin;
                $PaginaInicio = ($PaginaInicio<1)?1:$PaginaInicio;
                $NumOV = $OrdenFabricacion->OrdenVenta->OrdenVenta;
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($NumOV);
                for ($i=($PaginaInicio-1); $i<$PaginaFin; $i++) {
                    $page_format = array(
                        'MediaBox' => array('llx' => 0, 'lly' => 0, 'urx' => 101, 'ury' => 66),
                        'CropBox' => array('llx' => 0, 'lly' => 0, 'urx' => 101, 'ury' => 66),
                        'BleedBox' => array('llx' => 2, 'lly' => 2, 'urx' => 99, 'ury' => 64),
                        'TrimBox' => array('llx' => 4, 'lly' => 4, 'urx' => 97, 'ury' => 63),
                        'ArtBox' => array('llx' => 6, 'lly' => 6, 'urx' => 94, 'ury' => 61),
                        'Dur' => 3,
                        'trans' => array(
                            'D' => 1.5,
                            'S' => 'Split',
                            'Dm' => 'V',
                            'M' => 'O'
                        ),
                        'Rotate' => 270,
                        'PZ' => 1,
                    );
                    $pdf->AddPage('P',$page_format,false,false);
                    $pdf->setFontSpacing(-0.2);
                    $pdf->SetFont('helvetica', 'B', 12);
                    $pdf->SetXY(23, 2);
                    $pdf->Cell(10, 6, $DatosSAP[0]["SubCatNum"], 0, 1, 'L', 0);
                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->write1DBarcode($DatosSAP[0]["SubCatNum"],'C128', 21, 8, 24, 4, 0.4, null, 'L');

                    $pdf->SetXY(3, 7);
                    $pdf->Cell(10, 6, "(ITEM)", 0, 1, 'L', 0);
                    $pdf->SetXY(3, 14);
                    $pdf->Cell(10, 6, "(DESC)", 0, 1, 'L', 0);
                    $pdf->SetXY(16, 13);
                    $pdf->MultiCell(48, 0, $DatosSAP[0]["U_BPItmDsc"], 0, 'L', 0, 1);
                    
                    $pdf->SetXY(3, 25);
                    $pdf->Cell(10, 6, "(MODEL)", 0, 1, 'L', 0);
                    $pdf->SetXY(16, 22);
                    $pdf->MultiCell(48, 0, $OrdenFabricacion->Descripcion, 0, 'L', 0, 1);

                    $pdf->SetXY(3, 34);
                    $pdf->Cell(10, 6, "(C.O.)", 0, 1, 'L', 0);
                    $pdf->SetFont('helvetica', 'B', 9);
                    $pdf->setFontSpacing(0);
                    $pdf->SetXY(16, 34);
                    $pdf->Cell(10, 6, "Queretaro / Mexico", 0, 1, 'L', 0);
                    $pdf->setFontSpacing(-0.2);
                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->SetXY(3, 40);
                    $pdf->Cell(10, 6, "(O.V.)", 0, 1, 'L', 0);
                    $pdf->write1DBarcode($NumOV,'C128', 20, 41, 20, 4, 0.4, null, 'L');

                    $pdf->SetXY(3, 48);
                    $pdf->Cell(10, 6, "(QTY)", 0, 1, 'L', 0);
                    $x = 18;
                    $y = 52;
                    $w = 15;
                    $h = 4;
                    $pdf->write1DBarcode($CantidadBolsa,'C128', $x, $y, $w, $h, 0.4, null, 'L');
                    $textWidth = $pdf->GetStringWidth($CantidadBolsa);
                    $textX = $x + ($w - $textWidth) / 2;
                    $textY = $y + $h ;
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->SetXY($textX, $textY-9);
                    $pdf->Cell($textWidth, 4, $CantidadBolsa, 0, 1, 'C');
                    $pdf->SetXY($textX+10, $textY-9);
                    $pdf->Cell(10, 4, "PCS", 0, 1, 'C');
                    $pdf->SetXY(3, 45);

                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->SetXY(3, 58);
                    $pdf->Cell(10, 6, "(SN/TN)", 0, 1, 'L', 0);
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->SetXY(24, 58);
                    $pdf->Cell(18, 6, ($i+1)." / ".$CantidadCajas, 0, 1, 'L', 0);

                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->SetXY(3, 63);
                    $pdf->Cell(10, 6, "(CODE)", 0, 1, 'L', 0);
                    $pdf->SetXY(16, 63);
                    $pdf->Cell(10, 6, $OrdenFabricacion->Articulo, 0, 1, 'L', 0);

                    $pdf->SetXY(3, 68);
                    $pdf->Cell(10, 6, "(PO No)", 0, 1, 'L', 0);
                    $pdf->SetXY(16, 68);
                    $pdf->Cell(10, 6, $DatosSAP[0]["NumAtCard"], 0, 1, 'L', 0);

                    $pdf->SetXY(3, 73);
                    $pdf->Cell(10, 6, "(LOT No)", 0, 1, 'L', 0);
                    $pdf->SetXY(16, 73);
                    $pdf->Cell(10, 6, $NumOV, 0, 1, 'L', 0);

                    $pdf->SetXY(3, 78);
                    $pdf->Cell(10, 6, "(DATE)", 0, 1, 'L', 0);
                    $fecha = Carbon::parse($DatosSAP[0]["DocDate"]);
                    $Year = $fecha->isoFormat('GG');
                    $Year = substr($Year, -2);
                    $Week = $fecha->isoWeek(); 
                    $pdf->SetXY(16, 78);
                    $pdf->Cell(10, 6,  $Year.$Week, 0, 1, 'L', 0);

                    $pdf->SetXY(3, 83);
                    $pdf->Cell(10, 6, "(REMARK)", 0, 1, 'L', 0);
                    $CodigoBarras = "19".$DatosSAP[0]["SubCatNum"]."/4U1003".$Year.$Week;
                    $x = 14;
                    $y = 89;
                    $w = 40;
                    $h = 4;
                    $pdf->write1DBarcode($CodigoBarras,'C128', $x, $y, $w, $h, 0.4, null, 'L');
                    $textWidth = $pdf->GetStringWidth($CodigoBarras);
                    $textX = $x + ($w - $textWidth) / 2;
                    $textY = $y + $h ;
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->SetXY($textX, $textY);
                    $pdf->Cell($textWidth, 4, $CodigoBarras, 0, 1, 'C');
                }
                ob_end_clean();
                // Generar el archivo PDF y devolverlo al navegador
                return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaHUAWEI_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }else{
            return response()->json(['error' => "Etiqueta permitida solo para el cliente Huawei Internacional."], 500);
        }
    }
}
