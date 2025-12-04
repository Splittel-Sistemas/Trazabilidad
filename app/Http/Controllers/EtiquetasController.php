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
    protected $RadioMovil;
    protected $Broadata;
    protected $RM;
    protected $Fibremex;
    protected $Etiquetas;
    protected $OptronicsLLC;
    public function __construct(FuncionesGeneralesController $funcionesGenerales){
        $this->funcionesGenerales = $funcionesGenerales;
        //📝Son clientes a los cuales se les generan etiquetas especiales 
        $this->HuaweiInternacional = "C0563";
        $this->Nokia = "C0675";
        $this->Drai = "C0003";
        $this->RadioMovil = "C0101";
        $this->Fibremex = "C0004";
        $this->RM = "C0875";
        $this->Broadata = "P45689";
        $this->OptronicsLLC = "C0912";
        //📝Etiquetas fabricadas hasta el momento
        //📌Si se requiere agregar una nueva etiqueta, agregarla  a este arreglo y en los metodos Menu y Campos revizar ya que 
            //allí es donde se crea el Menu para cada cliente y los campos que se deben completar para generar la etiqueta
            //Las validaciones la mayoria estan en el FRONT-END y ALGUNOS TIENE VALIDACION EN EL BACKEND
        $this->Etiquetas = [
            [1,"ETIQ4","ETIQUETA DE BOLSA JUMPER"],
            [2,"ETIQ1","BOLSA ESPECIAL HUAWEI"],
            [3,"ETIQ4","BOLSA ESPECIAL NOKIA"],
            [4,"ETIQ4","BOLSA ESPECIAL DRAI"],
            [5,"ETIQ2","ETIQUETA DE BANDERILLA QR GENERAL"],
            [6,"ETIQ3","BANDERILLA QR NÚMERO ESPECIAL"],
            [7,"ETIQ4CEDIS","ETIQUETA DE BOLSA JUMPER CEDIS"],
            [8,"ETIQ5","ETIQUETA DE NÚMERO DE PIEZAS"],
            [9,"ETIQ6","ETIQUETA DE TRAZABILIDAD MPO (PRUEBA)"],
            [10,"ETIQ7","ETIQUETA DE INYECCIÓN (PRUEBA)"],
            [11,"ETIQ8","ETIQUETA DE DIVISOR (PRUEBA)"],
            [12,"ETIQ4","ETIQUETA DE BOLSA PATCH CORD GENERAL"],
            [13,"ETIQ9","CAJA HUAWEI"],
            [14,"ETIQ10","CAJA NOKIA"],
            [15,"ETIQ11","CAJA CABLE DE SERVICIO"],
            [16,"ETIQ12","ETIQUETA PARA MARCADORES DE FIBRA OPTICA (PRUEBA)"],
            [17,"ETIQ13","CERTIFICADO DE MEDICIÓN OPTRONICS"],
            [18,"ETIQ14","DISTRIBUIDOR ETIQUETA DE TRAZABILIDAD (PRUEBA)"],
            [19,"ETIQ15","DISTRIBUIDOR ETIQUETA DE TUBO (PRUEBA)"],
            [20,"ETIQ16","ETIQUETA DE IDENTIFICACIÓN DE CHAROLAS TRANSTELCO"],
            [21,"ETIQ17","DISTRIBUIDOR: 1UR"],
            [22,"ETIQ18","DISTRIBUIDOR: 2UR"],
            [23,"ETIQ19","DISTRIBUIDOR: 4UR"],
            [24,"ETIQ20","DISTRIBUIDOR DE PARED"],
            [25,"ETIQ21","DISTRIBUIDOR DE PARED EXTERIOR"],
            [26,"ETIQ22","DISTRIBUIDOR RIEL DIN"],
            //Etiquetas Broadata
            [27,"ETIQ23","BROADATA CH"],
            [28,"ETIQ24","BROADATA CABLE"],
            [29,"ETIQ25","BROADATA BOLSA"],
            [30,"ETIQ26","BROADATA CAJA"],
            [31,"ETIQ27","BROADATA CERTIFICADO"],
        ];
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
            $OrdenFabricacion->CodigoCliente = "S/N";
        }else{
            $Cliente = ($this->funcionesGenerales->OrdeneVenta($OrdenVenta->OrdenVenta));
            $OrdenFabricacion->OrdenVenta = $OrdenVenta->OrdenVenta;
            $OrdenFabricacion->Cliente = $OrdenVenta->NombreCliente;
            $OrdenFabricacion->CodigoCliente = $Cliente[0]['CardCode'];
        }
        $Menu = $this->Menu($OrdenFabricacion->CodigoCliente);
        $MenuOption = '<option value="" disabled selected>Selecciona una Opci&oacute;n</option>';
        $MenuBtn = "";
        foreach($Menu as $Registro){
            $MenuOption .= '<option value="'.$Registro[1].'">'.$Registro[2].'</option>'; 
            $MenuBtn .= '<a onclick="SeleccionarCampo(\''.$Registro[1].'\')" class="list-group-item list-group-item-action" style="font-size: 12px; padding: 4px 8px;">'.$Registro[2].'</a>'; 
        }
        $OrdenFabricacion['MenuOption'] = $MenuOption;
        $OrdenFabricacion['MenuBtn'] = $MenuBtn;
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
        $PorcentajeC = $request->PorcentajeC;
        $PorcentajeD = $request->PorcentajeD;
        $CodigoCliente = $request->CodigoCliente;
        $Insercion = $request->Insercion;
        $Retorno = $request->Retorno;
        $CantidadCajas = $request->CantidadCajas;
        $Etiquetas = $this->Etiquetas;
        $TipoDistribuidor = $request->TipoDistribuidor;
        $MenuDistribuidor = $request->MenuDistribuidor;
        $ColorCable = $request->ColorCable;
        $TituloEtiqueta = "";
        foreach ($Etiquetas as $etiqueta) {
            if ($etiqueta[1] === $TipoEtiqueta) {
                $TituloEtiqueta = $etiqueta[2];
                break;
            }
        }
        $this->funcionesGenerales->Logs("Etiquetas","imprimir Etiqueta","Etiqueta ".$TituloEtiqueta);
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
                return $this->EtiquetaBolsaJumper($CantidadEtiquetas,$PDFOrdenFabricacion,$CodigoCliente);
                break;
            case 'ETIQ4CEDIS':
                return $this->EtiquetaBolsaJumperCEDIS($CantidadEtiquetas,$PDFOrdenFabricacion);
                break;
            case 'ETIQ5':
                return $this->EtiquetaNumeroPiezas($Sociedad,$CantidadEtiquetas,$CantidadBolsa,$PDFOrdenFabricacion,$CodigoCliente);
                break;
            case 'ETIQ6':
                return $this->EtiquetaTrazabilidadMPO($PaginaInicio,$PaginaFin,$Insercion,$Retorno,$PDFOrdenFabricacion,$CodigoCliente);
                break;
            case 'ETIQ7':
                return $this->EtiquetaInyeccion($CantidadEtiquetas,$PDFOrdenFabricacion);
                break;
            case 'ETIQ8':
                return $this->EtiquetaDivisor($CantidadEtiquetas,$PDFOrdenFabricacion,$PorcentajeA,$PorcentajeB,$PorcentajeC,$PorcentajeD);
                break;
            case 'ETIQ9':
                return $this->EtiquetaCajaHuawei($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$CantidadCajas,$CantidadBolsa,$CodigoCliente);
                break;
            case 'ETIQ10':
                return $this->EtiquetaCajaNokia($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$CantidadCajas,$CantidadBolsa,$CodigoCliente);
                break;
            case 'ETIQ11':
                return $this->EtiquetaCajaCableServicio_MarcadoresFO($CantidadEtiquetas,$PDFOrdenFabricacion,$CantidadBolsa,$CodigoCliente,$TipoEtiqueta);
                break;
            case 'ETIQ12':
                return $this->EtiquetaCajaCableServicio_MarcadoresFO($CantidadEtiquetas,$PDFOrdenFabricacion,$CantidadBolsa,$CodigoCliente,$TipoEtiqueta);
                break;
            case 'ETIQ13':
                return $this->EtiquetaCertificadoMedicion($PaginaInicio,$PaginaFin,$Insercion,$Retorno,$PDFOrdenFabricacion,$CodigoCliente);
                break;
            case 'ETIQ14':
                return $this->DistribuidorEtiquetaTrazabilidad($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$TipoDistribuidor,$CodigoCliente);
                break;
            case 'ETIQ15':
                return $this->DistribuidorEtiquetaTubo($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$CodigoCliente);
                break;
            case 'ETIQ16':
                return $this->EtiquetaIdentificacionCharolasTranstelco($CantidadEtiquetas,$CodigoCliente);
                break;
            case 'ETIQ17':
            case 'ETIQ18':
            case 'ETIQ19':
            case 'ETIQ20':
            case 'ETIQ21':
            case 'ETIQ22':
                return $this->EtiquetaDistribuidores($CantidadEtiquetas,$MenuDistribuidor);
                break;
            case 'ETIQ23':
                return $this->BroadataCH($CantidadEtiquetas,$ColorCable);
                break;
            case 'ETIQ24':
                return $this->BroadataCable($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$CodigoCliente);
                break;
            case 'ETIQ25':
                return $this->BroadataBolsa($CantidadEtiquetas,$PDFOrdenFabricacion,$CodigoCliente);
                break;
            case 'ETIQ26':
                return $this->BroadataCaja($CantidadEtiquetas,$PDFOrdenFabricacion,$CantidadBolsa,$CodigoCliente);
                break;
            case 'ETIQ27':
                return $this->BroadataCertificado($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$CodigoCliente);
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
            $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
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
                $pdf->MultiCell(69, 0, html_entity_decode($OrdenFabricacion->Descripcion, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 0, 'L', 0, 1);
                $pdf->SetXY($posX+4, 31); 
                $pdf->MultiCell(60, 0, "Specification:  ", 0, 'L', 0, 1);
                //Codigo de barras
                $NumeroProveedorOpt = '4U1003';

                $Fecha = $this->SemanaFecha($DatosSAP[0]["DocDate"]);
                $CantidadBolsa = 'Q0001';
                $UltimosDigOF = 'S'.substr($OrdenFabricacion->OrdenFabricacion,-2);
                $CodigoBarras = '19'.$NumeroHuawei."/".$NumeroProveedorOpt.$Fecha['Year'].str_pad($Fecha['Week'], 2, '0', STR_PAD_LEFT).$CantidadBolsa.$UltimosDigOF.str_pad(($i+1), 4, '0', STR_PAD_LEFT);
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
    public function EtiquetaBolsaJumper($CantidadEtiquetas,$OrdenFabricacion,$CodigoCliente){
        try {
            if($CodigoCliente == $this->HuaweiInternacional){
                return json_encode(["error" => 'Esta etiqueta no corresponde al cliente HUAWEI INTERNATIONAL.']);
            }
            if($CodigoCliente == $this->RadioMovil){
                return json_encode(["error" => 'Esta etiqueta es para cliente especial, y aún esta en proceso.']);
            }
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
            $Descripcion = html_entity_decode($OrdenFabricacion->Descripcion, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $Articulo = "";
                if($CodigoCliente == $this->Drai){
                    $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
                    $Articulo = $DatosSAP[0]["SubCatNum"];
                }elseif($CodigoCliente == $this->Nokia){
                    $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
                    $Articulo = $DatosSAP[0]["ItemCode"];
                }
                else{
                    $Articulo = $OrdenFabricacion->Articulo;
                }
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
                $pdf->RoundedRect(2,5, 97, 45, 1, '1111', 'D', $border_style, array());
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->Rect(2, 15, 97 , 0 );

                $ParteNo = 'Denomination:  '."\n\n\n\n".
                            'Specification:  ';
                $pdf->SetXY($posX+3.5, 17); 
                $pdf->MultiCell(90, 0, $ParteNo, 0, 'L', 0, 1);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->SetXY($posX+27.5, 17); 
                $pdf->MultiCell(66, 0, $Descripcion, 0, 'L', 0, 1);
                //Codigo de barras
                $CodigoBarras = $Articulo;
                //$pdf->SetXY($posX + 30, 1);
                $pdf->write1DBarcode($CodigoBarras, 'C128',18, 39, 65, 4, 0.4, array(), 'N');
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->setFontSpacing(0);
                $pdf->SetXY($posX+25.5, 33);
                $pdf->MultiCell(65, 0, $CodigoBarras, 0, 'L', 0, 1);
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaBolsaJumper_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
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
                $Descripcion= html_entity_decode($OrdenFabricacion->Descripcion, ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaBolsaJumperCEDIS_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
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
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
            }else{
                if($CodigoCliente == $this->Nokia){
                    return json_encode(["error" => 'Esta etiqueta no corresponde al cliente NOKIA.']);
                }elseif($CodigoCliente == $this->Drai){
                    return json_encode(["error" => 'Esta etiqueta no corresponde al cliente DRAI.']);
                }elseif($CodigoCliente == $this->HuaweiInternacional){
                    return json_encode(["error" => 'Esta etiqueta no corresponde al cliente HUAWEI INTERNACIONAL.']);
                }
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
            $PaginaFin = $PaginaFin;
            $NumSerie = $PaginaInicio;
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
                $i++;
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
                    $Provisional = 2;
                    $x = 54;
                    $y = 18-$Aumento-$AumentoLetra ;
                    $pdf->SetXY($x+$Provisional, $y);
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
                    $x = 58+$Provisional;
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
                            if($CodigoCliente == $this->HuaweiInternacional){
                                $NumeroEspecial =  $DatosSAP[0]['SubCatNum'];
                            }elseif($CodigoCliente == $this->Nokia){
                                $NumeroEspecial =  $DatosSAP[0]['ItemCode'];
                                $LetraEspecial = 1.5;
                                $AumentoLetraEspecial =0.5;
                            }elseif($CodigoCliente == $this->Drai){
                                $NumeroEspecial =  $DatosSAP[0]['SubCatNum'];
                                if($NumeroEspecial == ""){
                                    return json_encode(["error" => 'Número Especial no Encontrado, Si el problema perciste Contacta a TI!.']);
                                }
                                $LetraEspecial = 2;
                                $AumentoLetraEspecial = 0.5;
                            }else{
                                return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es solo para Cliente Especial.']);
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
                        //if($DatosSAP[0]['SubCatNum']==""){
                        //    return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es solo para Cliente Especial']);
                        //}else{
                            if($CodigoCliente == $this->HuaweiInternacional){
                                $NumeroEspecial =  $DatosSAP[0]['SubCatNum'];
                            }elseif($CodigoCliente == $this->Nokia){
                                $NumeroEspecial =  $DatosSAP[0]['ItemCode'];
                                $LetraEspecial = 1.5;
                                $AumentoLetraEspecial = 0.5;
                            }elseif($CodigoCliente == $this->Drai){
                                $LetraEspecial = 2;
                                $AumentoLetraEspecial = 0.5;
                                $NumeroEspecial =  $DatosSAP[0]['SubCatNum'];
                                if($NumeroEspecial == ""){
                                    return json_encode(["error" => 'Número Especial no Encontrado, Si el problema perciste Contacta a TI!.']);
                                }
                            }else{
                                return json_encode(["error" => 'Cliente no encontrado, esta etiqueta es solo para Cliente Especial.']);
                            }
                       // }
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
    public function EtiquetaNumeroPiezas($Sociedad,$CantidadEtiquetas,$CantidadBolsa,$OrdenFabricacion,$CodigoCliente){
        try {
            $Sociedad = $Sociedad; //Sociedad
            $CantidadBolsa = $CantidadBolsa;
            $AumentoX = 0;
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.'],500);
            }
            if($CodigoCliente == "C0004" AND $Sociedad == "OPT"){
                return json_encode(["error" => 'Logotipo optronics no permitido para el Cliente Fibremex.'],500);
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
                        throw new \Exception('Etiqueta permitida solo para Cliente Fibremex (Intercompañias).');
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

                        $ParteNo = str_ireplace("OC ", "\nOC ", $NoPEDIDO);
                        $pdf->SetFont('dejavusans', 'B', 7.5);
                        $pdf->SetXY(5, 32);
                        $pdf->MultiCell(24, 0, $ParteNo, 0, 'L', 0, 1);
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
    //Etiqueta de Trazabilidad MPO
    public function EtiquetaTrazabilidadMPO($PaginaInicio,$PaginaFin,$Insercion,$Retorno,$OrdenFabricacion,$CodigoCliente){
            $BanderaDiferente = (($Insercion == $Retorno) || $Retorno == "")? true : false;
            $Insercion1 = "";
            $Retorno1 = "";
            $Insercion2 = "";
            $Retorno2 = "";
            switch($Insercion){
                case "MUPC":
                    $Insercion1 = 0.20;
                    $Retorno1 = 40.0;
                    break;
                case "MOUPC":
                    $Insercion1 = 0.20;
                    $Retorno1 = 55.0;
                    break;
                case "MOAPC":
                    $Insercion1 = 0.20;
                    $Retorno1 = 65.0;
                    break;
                case "MULMTRJ":
                    $Insercion1 = 0.70;
                    $Retorno1 = 40.0;
                    break;
                case "MONMTRJ":
                    $Insercion1 = 0.40;
                    $Retorno1 = 35.0;
                    break;
                case "MUMPO":
                    $Insercion1 = 0.50;
                    $Retorno1 = 20.0;
                    break;
                case "MOMPO":
                    $Insercion1 = 0.35;
                    $Retorno1 = 60.0;
                    break;
                case "MUMTP":
                    $Insercion1 = 0.50;
                    $Retorno1 = 20.0;
                    break;
                case "MOMTP":
                    $Insercion1 = 0.35;
                    $Retorno1 = 60.0;
                    break;
                case "MUMTP_PRO":
                    $Insercion1 = 0.35;
                    $Retorno1 = 25.0;        
                    break;
                case "MOMTP_PRO":
                    $Insercion1 = 0.35;
                    $Retorno1 = 25.0;
                    break;
            }
            if(!$BanderaDiferente){
                switch($Retorno){
                    case "MUPC":
                        $Insercion2 = 0.20;
                        $Retorno2 = 40.0;
                        break;
                    case "MOUPC":
                        $Insercion2 = 0.20;
                        $Retorno2 = 55.0;
                        break;
                    case "MOAPC":
                        $Insercion2 = 0.20;
                        $Retorno2 = 65.0;
                        break;
                    case "MULMTRJ":
                        $Insercion2 = 0.70;
                        $Retorno2 = 40.0;
                        break;
                    case "MONMTRJ":
                        $Insercion2 = 0.40;
                        $Retorno2 = 35.0;
                        break;
                    case "MUMPO":
                        $Insercion2 = 0.50;
                        $Retorno2 = 20.0;
                        break;
                    case "MOMPO":
                        $Insercion2 = 0.35;
                        $Retorno2 = 60.0;
                        break;
                    case "MUMTP":
                        $Insercion2 = 0.50;
                        $Retorno2 = 20.0;
                        break;
                    case "MOMTP":
                        $Insercion2 = 0.35;
                        $Retorno2 = 60.0;
                        break;
                    case "MUMTP_PRO":
                        $Insercion2 = 0.35;
                        $Retorno2 = 25.0;        
                        break;
                    case "MOMTP_PRO":
                        $Insercion2 = 0.35;
                        $Retorno2 = 25.0;
                        break;
                }
                if ($Insercion2 != floor($Insercion2)) {
                    $Insercion2 = number_format((float)$Insercion2, 2);
                }else{
                    $Insercion2 = $Insercion2 = number_format((float)$Insercion2, 1);
                }
                if ($Retorno2 != floor($Retorno2)) {
                        $Retorno2 = number_format((float)$Retorno2, 2);
                }else{
                    $Retorno2 = $Retorno2 = number_format((float)$Retorno2, 1);
                }
            }
        try {
            if ($Insercion1 != floor($Insercion1)) {
                    $Insercion1 = number_format((float)$Insercion1, 2);
            }else{
                $Insercion1 = $Insercion1 = number_format((float)$Insercion1, 1);
            }
            if ($Retorno1 != floor($Retorno1)) {
                    $Retorno1 = number_format((float)$Retorno1, 2);
            }else{
                $Retorno1 = $Retorno1 = number_format((float)$Retorno1, 1);
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
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($NumOV,$OrdenFabricacion->OrdenFabricacion);
                if($this->HuaweiInternacional == $CodigoCliente){
                    $NumeroParte = $DatosSAP[0]["SubCatNum"];
                }elseif($this->Nokia == $CodigoCliente OR $this->Drai == $CodigoCliente){
                    $NumeroParte = $DatosSAP[0]["ItemCode"];
                }
            }else{
                $NumeroParte = $OrdenFabricacion->Articulo;
            }
            $Descripcion = html_entity_decode($OrdenFabricacion->Descripcion, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            $ValorMedicionA = "PERDIDA DE INSERCION\n≤ ".$Insercion1." dB";
            if(!$BanderaDiferente){
                $ValorMedicionA .= " / ".$Insercion2." dB";
            }
            $ValorMedicionB = "PERDIDA DE RETORNO \n≥ ".$Retorno1." dB";
            if(!$BanderaDiferente){
                $ValorMedicionB .= " / ".$Retorno2." dB";
            }
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
            $pdf->SetFont('dejavusans', '', 4.5);
            //$pdf->SetTextColor(0, 0, 0);
            //$pdf->setFontSpacing(-0.1);
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
                $pdf->SetFont('dejavusans', 'B', 5.5);
                    $Resx = 40;
                    $pdf->SetXY(5,2); 
                    $pdf->SetFontStretching(90);
                    $pdf->setFontSpacing(-0.1);
                    $pdf->MultiCell(23, 0, $NombreFabricante, 0, 'L', 0, 1);

                    $pdf->SetFontStretching(60);
                    $pdf->setFontSpacing(0.1);
                    $pdf->SetXY(5,4); 
                    $pdf->MultiCell(24, 0, $NumeroParte, 0, 'L', 0, 1);
                    
                    $pdf->SetFontStretching(70);
                    $pdf->setFontSpacing(-0.1);
                    $pdf->SetXY(5,7); 
                    $pdf->MultiCell(21, 0, $Descripcion, 0, 'L', 0, 1);

                    $pdf->SetFontStretching(70);
                    $pdf->setFontSpacing(-0.1);
                    $pdf->SetXY(5,19); 
                    $pdf->MultiCell(21, 0, $ValorMedicionA, 0, 'L', 0, 1);
                    $pdf->SetXY(5,24); 
                    $pdf->MultiCell(21, 0, $ValorMedicionB, 0, 'L', 0, 1);
                    $pdf->SetXY(5,29); 
                    $pdf->SetFontStretching(100);
                    $pdf->setFontSpacing(-0.1);
                    $pdf->MultiCell(21, 0, "ORDEN:", 0, 'L', 0, 1);
                    $pdf->SetXY(14,29); 
                    $pdf->MultiCell(21, 0, $nombre.$OrdenFabricacion->OrdenFabricacion, 0, 'L', 0, 1);
                    $pdf->SetXY(20.5,31); 
                    $pdf->MultiCell(20, 0, str_pad(($serial), 2, '0', STR_PAD_LEFT), 0, 'L', 0, 1);
                    $serial++;
                    if($serial<=$CompletoCantidadEtiquetas){
                        $pdf->SetFont('dejavusans', 'B', 5.5);
                        $pdf->SetFontStretching(90);
                        $pdf->setFontSpacing(-0.1);
                        $pdf->SetXY(5,41); 
                        $pdf->MultiCell(23, 0, $NombreFabricante, 0, 'L', 0, 1);

                        $pdf->SetXY(5,43); 
                        $pdf->SetFontStretching(60);
                        $pdf->setFontSpacing(0.1);
                        $pdf->MultiCell(24, 0, $NumeroParte, 0, 'L', 0, 1);
                        
                        $pdf->SetFontStretching(70);
                        $pdf->setFontSpacing(-0.1);
                        $pdf->SetXY(5,46); 
                        $pdf->MultiCell(21, 0, $Descripcion, 0, 'L', 0, 1);
                        
                        $pdf->SetFontStretching(70);
                        $pdf->setFontSpacing(-0.1);
                        $pdf->SetXY(5,58); 
                        $pdf->MultiCell(21, 0, $ValorMedicionA, 0, 'L', 0, 1);
                        $pdf->SetXY(5,63); 
                        $pdf->MultiCell(21, 0, $ValorMedicionB, 0, 'L', 0, 1);
                        
                        $pdf->SetFontStretching(100);
                        $pdf->setFontSpacing(-0.1);
                        $pdf->SetXY(5,68); 
                        $pdf->MultiCell(21, 0, "ORDEN:", 0, 'L', 0, 1);
                        $pdf->SetXY(14,68); 
                        $pdf->MultiCell(21, 0, $nombre.$OrdenFabricacion->OrdenFabricacion, 0, 'L', 0, 1);
                        $pdf->SetXY(20.5,70); 
                        $pdf->MultiCell(21, 0, str_pad(($serial), 2, '0', STR_PAD_LEFT), 0, 'L', 0, 1);
                        $serial++;
                    }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaTrazabilidadMPO_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Etiqueta de Inyeccion
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
            $pdf->SetFontStretching(105);
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
                    $pdf->Image($imagePath, 20.5, 3, 15);
                    $pdf->SetFont('helvetica', 'B', 8);
                    $pdf->setFontSpacing(0);                  // Sin espaciado adicional entre letras
                    $pdf->setCellPadding(0);
                    $pdf->SetXY(3, 8);
                    $pdf->MultiCell(51, 0, "FECHA:                    GRUPO:", 0, 'L', 0, 1);
                    $pdf->SetXY(3, 13);
                    $pdf->MultiCell(51, 0, "HORA DE INGRESO:", 0, 'L', 0, 1);
                    $pdf->SetXY(3, 18);
                    $pdf->SetFont('helvetica', 'B', 7);
                    $pdf->setFontSpacing(-0.3);
                    $pdf->MultiCell(51, 0, "CONECTORES INYECTADOS:", 0, 'L', 0, 1);
                    $pdf->SetXY(3, 22);
                    $pdf->SetFont('helvetica', 'B', 8);
                    $pdf->MultiCell(51, 0, "HORA DE DESECHO:", 0, 'L', 0, 1);
                    if(($i+1 < $CantidadEtiquetas) OR ($ResiduoCantidadEtiquetas == 0)){
                        $pdf->Image($imagePath, 72.5, 3, 15);
                        $pdf->SetFont('helvetica', 'B', 8);
                        $pdf->setFontSpacing(0);                  // Sin espaciado adicional entre letras
                        $pdf->setCellPadding(0);
                        $pdf->SetXY(58, 8);
                        $pdf->MultiCell(51, 0, "FECHA:                    GRUPO:", 0, 'L', 0, 1);
                        $pdf->SetXY(58, 13);
                        $pdf->MultiCell(51, 0, "HORA DE INGRESO:", 0, 'L', 0, 1);
                        $pdf->SetXY(58, 18);
                        $pdf->SetFont('helvetica', 'B', 7);
                        $pdf->setFontSpacing(-0.3);
                        $pdf->MultiCell(51, 0, "CONECTORES INYECTADOS :", 0, 'L', 0, 1);
                        $pdf->SetXY(58, 22);
                        $pdf->SetFont('helvetica', 'B', 8);
                        $pdf->MultiCell(51, 0, "HORA DE DESECHO:", 0, 'L', 0, 1);
                    }
                }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaInyeccion_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Etiqueta de Divisor
    public function EtiquetaDivisor($CantidadEtiquetas,$OrdenFabricacion,$PorcentajeA,$PorcentajeB,$PorcentajeC,$PorcentajeD){
        try {
            if($PorcentajeA <0 OR $PorcentajeB<0){
                return response()->json(['error' => "Medida 1 y Medida 2 tienen que ser mayor a 0"], 500);
            }
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(1, 1, 1); 
            $pdf->SetFont('dejavusans', '', 9);  //dejavusans equivalente a Arial helvetica
            $pdf->SetAutoPageBreak(TRUE, 0.5);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);
            $PorcentajeA = (intval($PorcentajeA) === 0)?number_format($PorcentajeA, 2, '.', ''):number_format($PorcentajeA, 1, '.', '');
            $PorcentajeB = (intval($PorcentajeB) === 0)?number_format($PorcentajeB, 2, '.', ''):number_format($PorcentajeB, 1, '.', '');
            $PorcentajeC = (intval($PorcentajeC) === 0)?number_format($PorcentajeC, 2, '.', ''):number_format($PorcentajeC, 1, '.', '');
            $PorcentajeD = (intval($PorcentajeD) === 0)?number_format($PorcentajeD, 2, '.', ''):number_format($PorcentajeD, 1, '.', '');
            // Contador para saber cuántas etiquetas se han colocado en la página
            $ResiduoCantidadEtiquetas = $CantidadEtiquetas%2;
            $CantidadEtiquetas = intval($CantidadEtiquetas/2);
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetFontStretching(76);
                $pdf->setFontSpacing(-0.1);
                $pdf->AddPage('L', array(114, 25));
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.4);
                //Cuadro 1
                $pdf->Rect(3, 6, 8 , 5 );
                $pdf->Rect(11, 6, 8 , 5 );
                $pdf->Rect(19, 6, 8 , 5 );
                $pdf->Rect(3, 11, 8 , 5 );
                $pdf->Rect(11, 11, 8 , 5 );
                $pdf->Rect(19, 11, 8 , 5 );
                $pdf->SetXY(18.5, 6.5);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(2.5, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetXY(10.5, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeC."%", 0, 'L', 0, 1);
                $pdf->SetXY(18.5, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeD."%", 0, 'L', 0, 1);
                $pdf->SetFont('helvetica', 'B', 9.5);
                $pdf->SetFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetXY(10.4, 6.5);
                $pdf->MultiCell(10, 0, "OUT", 0, 'L', 0, 1);
                $pdf->SetXY(4, 6.5);
                $pdf->MultiCell(10, 0, "IN", 0, 'L', 0, 1);
                // Cuadro 2
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetFontStretching(76);
                $pdf->setFontSpacing(-0.1);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.5);
                $pdf->Rect(41, 6, 8 , 5 );
                $pdf->Rect(49, 6, 8 , 5 );
                $pdf->Rect(57, 6, 8 , 5 );
                $pdf->Rect(41, 11, 8 , 5 );
                $pdf->Rect(49, 11, 8 , 5 );
                $pdf->Rect(57, 11, 8 , 5 );
                $pdf->SetXY(56.5, 6.5);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(40.5, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetXY(48.5, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeC."%", 0, 'L', 0, 1);
                $pdf->SetXY(56.5, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeD."%", 0, 'L', 0, 1);

                $pdf->SetFont('helvetica', 'B', 9.5);
                $pdf->SetFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetXY(48.4, 6.5);
                $pdf->MultiCell(10, 0, "OUT", 0, 'L', 0, 1);
                $pdf->SetXY(42, 6.5);
                $pdf->MultiCell(10, 0, "IN", 0, 'L', 0, 1);
                // Cuadro 3
                    /*$pdf->SetFont('helvetica', 'B', 9.5);
                    $pdf->SetDrawColor(0, 0, 0);
                    $pdf->SetLineWidth(0.4);
                    $pdf->Rect(77, 6, 8 , 5 );
                    $pdf->Rect(85, 6, 8 , 5 );
                    $pdf->Rect(93, 6, 8 , 5 );
                    $pdf->Rect(77, 11, 8 , 5 );
                    $pdf->Rect(85, 11, 8 , 5 );
                    $pdf->Rect(93, 11, 8 , 5 );
                    $pdf->SetXY(93, 6);
                    $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                    $pdf->SetXY(77, 12);
                    $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                    $pdf->SetXY(85, 12);
                    $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                    $pdf->SetXY(93, 12);
                    $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);

                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->SetXY(84.3, 6);
                    $pdf->MultiCell(10, 0, "OUT", 0, 'L', 0, 1);
                    $pdf->SetXY(78, 6);
                    $pdf->MultiCell(10, 0, "IN", 0, 'L', 0, 1);*/
            }
            if($ResiduoCantidadEtiquetas >= 1){
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetFontStretching(76);
                $pdf->setFontSpacing(-0.1);
                $pdf->AddPage('L', array(114, 25));
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.4);
                //Cuadro 1
                $pdf->Rect(3, 6, 8 , 5 );
                $pdf->Rect(11, 6, 8 , 5 );
                $pdf->Rect(19, 6, 8 , 5 );
                $pdf->Rect(3, 11, 8 , 5 );
                $pdf->Rect(11, 11, 8 , 5 );
                $pdf->Rect(19, 11, 8 , 5 );
                $pdf->SetXY(18.5, 6.5);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(2.5, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeA."%", 0, 'L', 0, 1);
                $pdf->SetXY(10.5, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetXY(18.5, 11.5);
                $pdf->MultiCell(10, 0, $PorcentajeB."%", 0, 'L', 0, 1);
                $pdf->SetFont('helvetica', 'B', 9.5);
                $pdf->SetFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetXY(10.3, 6.5);
                $pdf->MultiCell(10, 0, "OUT", 0, 'L', 0, 1);
                $pdf->SetXY(4, 6.5);
                $pdf->MultiCell(10, 0, "IN", 0, 'L', 0, 1);
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaNumeroPiezas_'.$OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Etiqueta de Caja Nokia
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
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
                $NumeroRegistros = count($DatosSAP);
                // Crear PDF
                $pdf = new TCPDF();
                // Ajustar márgenes
                $pdf->SetMargins(1, 1, 1); 
                $pdf->SetFont('dejavusans', '', 10);  //dejavusans equivalente a Arial helvetica
                $pdf->SetAutoPageBreak(TRUE, 0.5);   
                $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
                $pdf->SetPrintHeader(false);

                // Contador para saber cuántas etiquetas se han colocado en la página
                $PaginaInicio = ($PaginaInicio<1)?1:$PaginaInicio;
                $NumOV = $OrdenFabricacion->OrdenVenta->OrdenVenta;
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($NumOV,$OrdenFabricacion->OrdenFabricacion);
                $SumarY = 5;
                $SumarX = 5;
                for ($i=($PaginaInicio-1); $i<$PaginaFin; $i++) {
                    if($i == $CantidadEtiquetas ){
                        break;
                    }
                    $yp = 80 ;
                    $xp = 105;
                    $page_format = array(
                        'MediaBox' => array('llx' => 0, 'lly' => 0, 'urx' => $xp, 'ury' => $yp),
                        'CropBox' => array('llx' => 0, 'lly' => 0, 'urx' => $xp, 'ury' => $yp),
                        'BleedBox' => array('llx' => 2, 'lly' => 2, 'urx' => $xp-2, 'ury' => $yp-2),
                        'TrimBox' => array('llx' => 4, 'lly' => 4, 'urx' => $xp-4, 'ury' => $yp-4),
                        'ArtBox' => array('llx' => 6, 'lly' => 6, 'urx' => $xp-6, 'ury' => $yp-6),
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
                    $pdf->AddPage('P', $page_format,false,false);
                    $pdf->SetFont('times', 'B', 10.5);
                    $pdf->setFontSpacing(-0.2);
                    $pdf->SetXY(3+$SumarX, 3+$SumarY);
                    $pdf->Cell(63, 6, "Descripción:", 0, 1, 'L', 0);
                    $pdf->SetXY(9+$SumarX, 9+$SumarY);
                    $pdf->MultiCell(56, 0, html_entity_decode($OrdenFabricacion->Descripcion, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 0, 'L', 0, 1);
                    $pdf->SetXY(3+$SumarX, 27+$SumarY);
                    $pdf->Cell(63, 6, "No. de departe:", 0, 1, 'L', 0);
                    $x = 25+$SumarX;
                    $y = 34+$SumarY;
                    $w = 35;
                    $h = 5.5;
                    $NoParte = $OrdenFabricacion->Articulo;
                    $pdf->write1DBarcode($NoParte,'C128', $x, $y, $w, $h, 0.4, null, 'L');
                    $textWidth = $pdf->GetStringWidth($NoParte);
                    $textX = $x + ($w - $textWidth) / 2;
                    $textY = $y + $h ;
                    $pdf->SetXY($textX, $textY);
                    $pdf->Cell($textWidth, 4, $NoParte, 0, 1, 'C');
                    $pdf->SetXY(3+$SumarX, 45+$SumarY);
                    $pdf->Cell(63, 6, "Codigo especial:", 0, 1, 'L', 0);
                    $CodigoEspecial = $DatosSAP[0]["ItemCode"];
                    $x = 15+$SumarX;
                    $y = 52+$SumarY;
                    $w = 46;
                    $h = 5.5;
                    $pdf->write1DBarcode($CodigoEspecial,'C128', $x, $y, $w, $h, 0.4, null, 'L');
                    $textWidth = $pdf->GetStringWidth($CodigoEspecial);
                    $textX = $x + ($w - $textWidth) / 2;
                    $textY = $y + $h ;
                    $pdf->SetXY($textX, $textY);
                    $pdf->Cell($textWidth, 4, $CodigoEspecial , 0, 1, 'C');
                    $pdf->SetXY(3+$SumarX, 63+$SumarY);
                    $pdf->Cell(63, 6, "(O.V.)                     ".$NumOV, 0, 1, 'L', 0);
                    $pdf->SetXY(3+$SumarX, 70+$SumarY);
                    $pdf->Cell(63, 6, "Caja:                        ".($i+1)." / ".$CantidadEtiquetas, 0, 1, 'L', 0);
                    $pdf->SetXY(3+$SumarX, 80+$SumarY);
                    $pdf->Cell(20, 6, "Cantidad:", 0, 1, 'L', 0);
                    $pdf->SetXY(50+$SumarX, 85+$SumarY);
                    $pdf->Cell(10, 6, "PCS", 0, 1, 'L', 0);
                    $x = 25+$SumarX;
                    $y = 80+$SumarY;
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
    //Etiqueta de Caja Huawei
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
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
                $NumeroRegistros = count($DatosSAP);
                // Crear PDF
                $pdf = new TCPDF();
                // Ajustar márgenes
                $pdf->SetMargins(0, 0, 0); 
                $pdf->SetAutoPageBreak(TRUE, 0.5);   
                $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
                $pdf->SetPrintHeader(false);

                // Contador para saber cuántas etiquetas se han colocado en la página
                $PaginaFin = ($PaginaFin>$CantidadCajas)?$CantidadCajas:$PaginaFin;
                $PaginaInicio = ($PaginaInicio<1)?1:$PaginaInicio;
                $NumOV = $OrdenFabricacion->OrdenVenta->OrdenVenta;
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($NumOV,$OrdenFabricacion->OrdenFabricacion);
                $SumarX = 10;
                $SumarY = 7;
                for ($i=($PaginaInicio-1); $i<$PaginaFin; $i++) {
                    $page_format = array(
                        'MediaBox' => array('llx' => 0, 'lly' => 0, 'urx' => 106, 'ury' => 80),
                        'CropBox' => array('llx' => 0, 'lly' => 0, 'urx' => 106, 'ury' => 80),
                        'BleedBox' => array('llx' => 2, 'lly' => 2, 'urx' => 104, 'ury' => 78),
                        'TrimBox' => array('llx' => 4, 'lly' => 4, 'urx' => 102, 'ury' => 76),
                        'ArtBox' => array('llx' => 6, 'lly' => 6, 'urx' => 100, 'ury' => 74),
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
                    $pdf->AddPage('P',$page_format,false,false);
                    $pdf->setFontSpacing(-0.1);
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->SetXY(23+$SumarX , 2+$SumarY );
                    $pdf->Cell(10, 6, $DatosSAP[0]["SubCatNum"], 0, 1, 'L', 0);
                    $pdf->SetFont('helvetica', '', 8.5);
                    $pdf->write1DBarcode($DatosSAP[0]["SubCatNum"],'C128', 21+$SumarX , 8+$SumarY, 30, 4, 0.4, null, 'L');

                    $pdf->SetXY(3+$SumarX , 7+$SumarY);
                    $pdf->Cell(10, 6, "(ITEM)", 0, 1, 'L', 0);
                    $pdf->SetXY(3+$SumarX , 14+$SumarY);
                    $pdf->Cell(10, 6, "(DESC)", 0, 1, 'L', 0);
                    $pdf->SetXY(16+$SumarX , 13+$SumarY);
                    $pdf->MultiCell(40, 0, $DatosSAP[0]["U_BPItmDsc"], 0, 'L', 0, 1);
                    
                    $pdf->SetXY(3+$SumarX , 23+$SumarY);
                    $pdf->Cell(10, 6, "(MODEL)", 0, 1, 'L', 0);
                    $pdf->SetXY(16+$SumarX , 24+$SumarY);
                    $pdf->MultiCell(40, 0,html_entity_decode($OrdenFabricacion->Descripcion, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 0, 'L', 0, 1);

                    $pdf->SetXY(3+$SumarX , 34+$SumarY);
                    $pdf->Cell(10, 6, "(C.O.)", 0, 1, 'L', 0);
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->setFontSpacing(0);
                    $pdf->SetXY(16+$SumarX , 34+$SumarY);
                    $pdf->Cell(10, 6, "Queretaro / Mexico", 0, 1, 'L', 0);
                    $pdf->setFontSpacing(-0.2);
                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->SetXY(3+$SumarX , 40+$SumarY);
                    $pdf->Cell(10, 6, "(O.V.)", 0, 1, 'L', 0);
                    $pdf->write1DBarcode($NumOV,'C128', 20+$SumarX , 41+$SumarY, 30, 4, 0.4, null, 'L');

                    $pdf->SetXY(3+$SumarX , 45+$SumarY);
                    $pdf->Cell(10, 6, "(QTY)", 0, 1, 'L', 0);
                    $x = 18+$SumarX ;
                    $y = 47+$SumarY;
                    $w = 18;
                    $h = 3;
                    $pdf->write1DBarcode($CantidadBolsa,'C128', $x, $y, $w, $h, 0.4, null, 'L');
                    $textWidth = $pdf->GetStringWidth($CantidadBolsa);
                    $textX = $x + ($w - $textWidth) / 2;
                    $textY = $y + $h ;
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->SetXY($textX, $textY);
                    $pdf->Cell($textWidth, 4, $CantidadBolsa, 0, 1, 'C');
                    $pdf->SetXY($textX+10, $textY);
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->Cell(10, 4, "PCS", 0, 1, 'C');
                    $pdf->SetXY(3+$SumarX, 45+$SumarY);

                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->SetXY(3+$SumarX , 55+$SumarY);
                    $pdf->Cell(10, 6, "(SN/TN)", 0, 1, 'L', 0);
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->SetXY(24+$SumarX , 56+$SumarY);
                    $pdf->Cell(18, 6, ($i+1)." / ".$CantidadCajas, 0, 1, 'L', 0);

                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->SetXY(3+$SumarX , 61+$SumarY);
                    $pdf->Cell(10, 6, "(CODE)", 0, 1, 'L', 0);
                    $pdf->SetXY(16+$SumarX , 61+$SumarY);
                    $pdf->Cell(10, 6, $OrdenFabricacion->Articulo, 0, 1, 'L', 0);

                    $pdf->SetXY(3+$SumarX , 66+$SumarY);
                    $pdf->Cell(10, 6, "(PO No)", 0, 1, 'L', 0);
                    $pdf->SetXY(16+$SumarX , 66+$SumarY);
                    $pdf->Cell(10, 6, $DatosSAP[0]["NumAtCard"], 0, 1, 'L', 0);

                    $pdf->SetXY(3+$SumarX , 71+$SumarY);
                    $pdf->Cell(10, 6, "(LOT No)", 0, 1, 'L', 0);
                    $pdf->SetXY(16+$SumarX , 71+$SumarY);
                    $pdf->Cell(10, 6, $NumOV, 0, 1, 'L', 0);

                    $pdf->SetXY(3+$SumarX , 76+$SumarY);
                    $pdf->Cell(10, 6, "(DATE)", 0, 1, 'L', 0);

                    $Fecha = $this->SemanaFecha($DatosSAP[0]["DocDate"]);
                    $pdf->SetXY(16+$SumarX , 76+$SumarY);
                    $pdf->Cell(10, 6,  $Fecha['Year'].$Fecha['Week'], 0, 1, 'L', 0);

                    $pdf->SetXY(3+$SumarX , 81+$SumarY);
                    $pdf->Cell(10, 6, "(REMARK)", 0, 1, 'L', 0);
                    $CodigoBarras = "19".$DatosSAP[0]["SubCatNum"]."/4U1003".$Fecha['Year'].$Fecha['Week'];
                    $x = 10+$SumarX ;
                    $y = 87+$SumarY;
                    $w = 50;
                    $h = 5;
                    $pdf->write1DBarcode($CodigoBarras,'C128', $x, $y, $w, $h, 0.4, null, 'L');
                    $textWidth = $pdf->GetStringWidth($CodigoBarras);
                    $textX = $x + ($w - $textWidth) / 2;
                    $textY = $y + $h ;
                    $pdf->SetFont('helvetica', 'B', 11);
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
    //Etiqueta sirve para  caja de cable de servicio y para  Marcadores de Fibra Optica
    public function EtiquetaCajaCableServicio_MarcadoresFO($CantidadEtiquetas,$OrdenFabricacion,$CantidadBolsa,$CodigoCliente,$TipoEtiqueta){
        try {
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']);
            }
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            $Articulo = "";
            $Articulo = $OrdenFabricacion->Articulo;
            if($OrdenVenta == "" || $OrdenVenta == "00000"){
                $OrdenVenta = "N/A";
            }else{
                $OrdenVenta = $OrdenVenta->OrdenVenta;
                if($this->Nokia == $CodigoCliente OR $this->HuaweiInternacional == $CodigoCliente OR $this->Drai == $CodigoCliente){
                    $Articulo = $this->CodigoEspecial($CodigoCliente,$OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
                    if($Articulo == ""){
                        return json_encode(["error" => 'Número Especial no Encontrado, Si el problema perciste Contacta a TI!.']);
                    }
                }
            }
            $NoPEDIDO = "";
            if($TipoEtiqueta != "ETIQ11"){
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAPFMX($OrdenVenta);
                $NumeroRegistros = count($DatosSAP);
                if($NumeroRegistros==0){
                throw new \Exception('Etiqueta permitida solo para Cliente Fibremex (Intercompañias).');
                }else{
                    if($DatosSAP[0]['NumAtCard']==""){
                        throw new \Exception('No.PEDIDO no encontrado.');
                    }else{
                        $NoPEDIDO =  $DatosSAP[0]['NumAtCard'];
                    }
                }
            }
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(2, 2, 2); 
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->SetAutoPageBreak(TRUE, 0);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);
            $Descripcion = html_entity_decode($OrdenFabricacion->Descripcion, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            // Contador para saber cuántas etiquetas se han colocado en la página
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                $pdf->AddPage('L', array(101, 51));
                $pdf->SetFont('dejavusans', '', 9);
                $pdf->setFontSpacing(-0.2);
                // Color de fondo y texto en la parte superior de la etiqueta
                $posX = 0;
                //Agregar la imagen
                if($TipoEtiqueta == "ETIQ11"){
                    $Imagen = 'app/Logos/Optronics.jpg';
                    $TamImg = 0;
                    $TamCodigo = 70;
                    $TamCodigoPosx = 0;
                }else{
                    $Imagen = 'app/Logos/Fibremex.png';
                    $TamImg = 6;
                    $TamCodigo = 40;
                    $TamCodigoPosx = 15;
                }
                if(!file_exists(storage_path($Imagen))){
                    throw new \Exception('No se encontraron el Logo requerido, por favor contactate con TI.');
                }else{
                    $imagePath = storage_path($Imagen);
                    $pdf->Image($imagePath, 3.5, 6, 25+$TamImg);
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
                $pdf->RoundedRect(2,5, 97, 45, 1, '1111', 'D', $border_style, array());
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->Rect(2, 12.5, 97 , 0 );

                $ParteNo = 'Denomination:  '."\n\n".
                            'Specification:  ';
                $pdf->SetXY($posX+3.5, 15); 
                $pdf->MultiCell(90, 0, $ParteNo, 0, 'L', 0, 1);
                $pdf->SetFont('dejavusans', '', 7);
                $pdf->SetXY($posX+27.5, 14); 
                $pdf->MultiCell(68, 0, $Descripcion, 0, 'L', 0, 1);
                //Codigo de barras
                $CodigoBarras = $Articulo;
                //$pdf->SetXY($posX + 30, 1);
                $pdf->write1DBarcode($CodigoBarras, 'C128',15+$TamCodigoPosx, 28, $TamCodigo, 3, 0.4, array(), 'N');
                $pdf->SetFont('dejavusans', '', 9);
                $pdf->setFontSpacing(0);
                $pdf->SetXY($posX+26.5, 23);
                $pdf->MultiCell(65, 0, $CodigoBarras, 0, 'L', 0, 1);
                if($TipoEtiqueta == "ETIQ11"){
                    $pdf->RoundedRect(4.5,33, 92, 15, 1, '1111', 'D', $border_style, array());
                    $pdf->Rect(4.5, 39, 92 , 0 );
                    $pdf->Rect(35.5, 33, 0, 15);
                    $pdf->Rect(65.5, 33, 0, 15);
                    $pdf->Text(15, 34, "O.V");
                    $pdf->Text(46, 34, "O.F.");
                    $pdf->Text(71, 34, "CANTIDAD");
                    $pdf->SetFont('dejavusans', 'B', 12);
                    $RestarEspacio = 0;
                    if($OrdenVenta == "N/A"){
                        $RestarEspacio = 3;
                    }
                    $pdf->Text(11+$RestarEspacio, 41, $OrdenVenta);
                    $pdf->Text(40, 41, $OrdenFabricacion->OrdenFabricacion);
                    $pdf->SetFont('dejavusans', 'B', 14);
                    $NumeroDigitos = strlen($CantidadBolsa);
                    $RestarEspacio = ($NumeroDigitos == 1)?78:76;
                    $RestarEspacio = ($NumeroDigitos > 2)?74:$RestarEspacio;
                    $pdf->Text($RestarEspacio, 41, $CantidadBolsa);
                }else{
                    $pdf->RoundedRect(4.5,33, 92, 15, 1, '1111', 'D', $border_style, array());
                    $pdf->Rect(4.5, 39, 92 , 0 );
                    $pdf->Rect(27.5, 33, 0, 15);
                    $pdf->Rect(50.5, 33, 0, 15);
                    $pdf->Rect(73.5, 33, 0, 15);
                    $pdf->Text(6, 34, "No. PEDIDO");
                    $pdf->Text(35, 34, "O.V");
                    $pdf->Text(57, 34, "O.F.");
                    $pdf->Text(75, 34, "CANTIDAD");
                    $RestarEspacio = 0;
                    if($OrdenVenta == "N/A"){
                        $RestarEspacio = 3;
                    }
                    $pdf->SetFont('dejavusans', 'B', 8);
                    //$pdf->Text(3, 41, $NoPEDIDO);
                    $pdf->SetXY(5, 41); 
                    $pdf->MultiCell(24, 0, str_ireplace("OC ", "\nOC ", $NoPEDIDO)."", 0, 'L', 0, 1);
                    $pdf->SetFont('dejavusans', 'B', 12);
                    $pdf->Text(30+$RestarEspacio, 41, $OrdenVenta);
                    $pdf->Text(52, 41, $OrdenFabricacion->OrdenFabricacion);
                    $pdf->SetFont('dejavusans', 'B', 14);
                    $NumeroDigitos = strlen($CantidadBolsa);
                    $RestarEspacio = ($NumeroDigitos == 1)?82:80;
                    $RestarEspacio = ($NumeroDigitos > 2)?78:$RestarEspacio;
                    $pdf->Text($RestarEspacio, 41, $CantidadBolsa);
                }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('CableServicioMarcadores_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Etiqueta de Certificado de Medicion Optronics
    public function EtiquetaCertificadoMedicion($PaginaInicio,$PaginaFin,$Insercion,$Retorno,$OrdenFabricacion,$CodigoCliente){
        try{
            $BanderaDiferente = (($Insercion == $Retorno) || $Retorno == "" || $Retorno == null)? true : false;
            $Insercion1 = "";
            $Retorno1 = "";
            $Insercion2 = "";
            $Retorno2 = "";
            $LongitudOnda1 = "";
            $LongitudOnda2 = "";
            switch($Insercion){
                    case "MUPC":
                        $Insercion1 = 0.20;
                        $Retorno1 = 40.0;
                        $LongitudOnda1 = "850nm / 1300nm";
                        break;
                    case "MOUPC":
                        $Insercion1 = 0.20;
                        $Retorno1 = 55.0;
                        $LongitudOnda1 = "1310nm / 1550nm";
                        break;
                    case "MOAPC":
                        $Insercion1 = 0.20;
                        $Retorno1 = 65.0;
                        $LongitudOnda1 = "1310nm / 1550nm";
                        break;
                    case "MULMTRJ":
                        $Insercion1 = 0.70;
                        $Retorno1 = 40.0;
                        $LongitudOnda1 = "850nm / 1300nm";
                        break;
                    case "MONMTRJ":
                        $Insercion1 = 0.40;
                        $Retorno1 = 35.0;
                        $LongitudOnda1 = "1310nm / 1550nm";
                        break;
                    case "MUMPO":
                        $Insercion1 = 0.50;
                        $Retorno1 = 20.0;
                        $LongitudOnda1 = "850nm / 1300nm";
                        break;
                    case "MOMPO":
                        $Insercion1 = 0.35;
                        $Retorno1 = 60.0;
                        $LongitudOnda1 = "1310nm / 1550nm";
                        break;
                    case "MUMTP":
                        $Insercion1 = 0.50;
                        $Retorno1 = 20.0;
                        $LongitudOnda1 = "850nm / 1300nm";
                        break;
                    case "MOMTP":
                        $Insercion1 = 0.35;
                        $Retorno1 = 60.0;
                        $LongitudOnda1 = "1310nm / 1550nm";
                        break;
                    case "MUMTP_PRO":
                        $Insercion1 = 0.35;
                        $Retorno1 = 25.0;        
                        $LongitudOnda1 = "850nm / 1300nm";
                        break;
                    case "MOMTP_PRO":
                        $Insercion1 = 0.35;
                        $Retorno1 = 25.0;
                        $LongitudOnda1 = "1310nm / 1550nm";
                        break;
            }
            if(!$BanderaDiferente){
                    switch($Retorno){
                        case "MUPC":
                            $Insercion2 = 0.20;
                            $Retorno2 = 40.0;
                            $LongitudOnda2 = "850nm / 1300nm";
                            break;
                        case "MOUPC":
                            $Insercion2 = 0.20;
                            $Retorno2 = 55.0;
                            $LongitudOnda2 = "1310nm / 1550nm";
                            break;
                        case "MOAPC":
                            $Insercion2 = 0.20;
                            $Retorno2 = 65.0;
                            $LongitudOnda2 = "1310nm / 1550nm";
                            break;
                        case "MULMTRJ":
                            $Insercion2 = 0.70;
                            $Retorno2 = 40.0;
                            $LongitudOnda2 = "850nm / 1300nm";
                            break;
                        case "MONMTRJ":
                            $Insercion2 = 0.40;
                            $Retorno2 = 35.0;
                            $LongitudOnda2 = "1310nm / 1550nm";
                            break;
                        case "MUMPO":
                            $Insercion2 = 0.50;
                            $Retorno2 = 20.0;
                            $LongitudOnda2 = "850nm / 1300nm";
                            break;
                        case "MOMPO":
                            $Insercion2 = 0.35;
                            $Retorno2 = 60.0;
                            $LongitudOnda2 = "1310nm / 1550nm";
                            break;
                        case "MUMTP":
                            $Insercion2 = 0.50;
                            $Retorno2 = 20.0;
                            $LongitudOnda2 = "850nm / 1300nm";
                            break;
                        case "MOMTP":
                            $Insercion2 = 0.35;
                            $Retorno2 = 60.0;
                            $LongitudOnda2 = "1310nm / 1550nm";
                            break;
                        case "MUMTP_PRO":
                            $Insercion2 = 0.35;
                            $Retorno2 = 25.0;        
                            $LongitudOnda2 = "850nm / 1300nm";
                            break;
                        case "MOMTP_PRO":
                            $Insercion2 = 0.35;
                            $Retorno2 = 25.0;
                            $LongitudOnda2 = "1310nm / 1550nm";
                            break;
                    }
                    if ($Insercion2 != floor($Insercion2)) {
                        $Insercion2 = number_format((float)$Insercion2, 2);
                    }else{
                        $Insercion2 = $Insercion2 = number_format((float)$Insercion2, 1);
                    }
                    if ($Retorno2 != floor($Retorno2)) {
                            $Retorno2 = number_format((float)$Retorno2, 2);
                    }else{
                        $Retorno2 = $Retorno2 = number_format((float)$Retorno2, 1);
                    }
            }
            if($LongitudOnda1 != $LongitudOnda2 AND $LongitudOnda2 != ""){
                $LongitudOnda1 = $LongitudOnda1. "\n".$LongitudOnda2;
            }
            if ($Insercion1 != floor($Insercion1)) {
                $Insercion1 = number_format((float)$Insercion1, 2);
            }else{
                $Insercion1 = $Insercion1 = number_format((float)$Insercion1, 1);
            }
            if ($Retorno1 != floor($Retorno1)) {
                $Retorno1 = number_format((float)$Retorno1, 2);
            }else{
                $Retorno1 = $Retorno1 = number_format((float)$Retorno1, 1);
            }
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first(); 
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']); 
            }
            if ($PaginaFin<$PaginaInicio) {
                return json_encode(["error" => 'Página de inicio tiene que ser menor a Página fin.']);
            }
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            $CodigoEspecial = $OrdenFabricacion->Articulo;
            if($OrdenVenta == "" || $OrdenVenta == "00000"){
                $OrdenVenta = "N/A";
            }else{
                $OrdenVenta = $OrdenVenta->OrdenVenta;
                if($this->Nokia == $CodigoCliente OR $this->HuaweiInternacional == $CodigoCliente OR $this->Drai == $CodigoCliente){
                    $CodigoEspecial = $this->CodigoEspecial($CodigoCliente,$OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
                    if($CodigoEspecial == ""){
                        return json_encode(["error" => 'Número Especial no Encontrado, Si el problema perciste Contacta a TI!.']);
                    }
                }
            }
            $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
             $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(2, 2, 2); 
            $pdf->SetFontStretching(105);
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);
            $pdf->SetAutoPageBreak(TRUE, 0);   
            for ($i=($PaginaInicio-1); $i<$PaginaFin; $i++) {
                $page_format = array(
                    'MediaBox' => array('llx' => 0, 'lly' => 0, 'urx' => 106, 'ury' => 80),
                    'CropBox' => array('llx' => 0, 'lly' => 0, 'urx' => 106, 'ury' => 80),
                    'BleedBox' => array('llx' => 2, 'lly' => 2, 'urx' => 104, 'ury' => 78),
                    'TrimBox' => array('llx' => 4, 'lly' => 4, 'urx' => 102, 'ury' => 76),
                    'ArtBox' => array('llx' => 6, 'lly' => 6, 'urx' => 100, 'ury' => 74),
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
                $pdf->AddPage('L', array(80,106));//$page_format,false,false);
                if(!file_exists(storage_path('app/Logos/Optronics.jpg'))){
                    throw new \Exception('No se encontraron el Logo requerido, por favor contactate con TI.');
                }else{
                    $imagePath = storage_path('app/Logos/Optronics.jpg');
                    $pdf->Image($imagePath, 4.5, 6, 20);
                }
                $pdf->SetFont('helvetica', '', 10);
                $pdf->setFontSpacing(-0);
                $pdf->Text(50,6,"Certificado de Medición");
                $pdf->SetXY(3.5, 15); 
                $pdf->MultiCell(25, 0, "Descripción del producto: ", 0, 'L', 0, 1);
                $pdf->SetXY(35, 15); 
                $pdf->setFontSpacing(-0.2);
                $pdf->MultiCell(65, 0, $OrdenFabricacion->Descripcion, 0, 'L', 0, 1);
                $pdf->Text(3.5,35,"Número de parte: ");
                $pdf->Text(50,35,$CodigoEspecial);
                $pdf->Text(3.5,40,"Número de serie: ");
                $Tipo = (stripos($OrdenFabricacion->Descripcion, 'multimodo'))?'M':'S';

                $Fecha = $this->SemanaFecha($DatosSAP[0]["DocDate"]);
                $pdf->Text(50,40,$Tipo.$OrdenFabricacion->OrdenFabricacion."-".($Fecha['Week']).($Fecha['Year']).str_pad($i+1, 4, "0", STR_PAD_LEFT));
                $pdf->Text(3.5,45,"Fecha de prueba: ");
                $FechaPrueba = date('d/m/Y');
                $pdf->Text(50,45,$FechaPrueba);
                $pdf->Text(35,52.5,$LongitudOnda1);
                $pdf->Text(3.5,60,"Perdida permitida:");
                $ValorMedicionA = "≤ ".$Insercion1." dB"; 
                $ValorMedicionB ="≥ ".$Retorno1." dB";
                $pdf->SetFont('dejavusans', '', 9);
                if(!$BanderaDiferente){
                    $ValorMedicionA .= " / ".$Insercion2." dB";
                }
                if(!$BanderaDiferente){
                    $ValorMedicionB .= " / ".$Retorno2." dB";
                }
                $pdf->Text(3.5,65,"Inserción: ".$ValorMedicionA);
                $pdf->Text(3.5,70,"Retorno: ".$ValorMedicionB);
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->Text(60,72,"Resultado: APROBADO");
            }
            ob_end_clean();
            return json_encode(["pdf"=>base64_encode($pdf->Output('CertificadoMedicion_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Distribuidor Etiqueta de Trazabilidad
    public function DistribuidorEtiquetaTrazabilidad($PaginaInicio,$PaginaFin,$OrdenFabricacion,$TipoDistribuidor,$CodigoCliente){
        try{
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first(); 
            if (is_null( $OrdenFabricacion) || is_null( $OrdenFabricacion)) {
                return json_encode(["error" => 'No se encontraron datos para esta orden de Fabricación.']); 
            }
            if ($PaginaFin<$PaginaInicio) {
                return json_encode(["error" => 'Página de inicio tiene que ser menor a Página fin.']);
            }
            $OrdenVenta = $OrdenFabricacion->OrdenVenta;
            $CodigoEspecial = $OrdenFabricacion->Articulo;
            $DatosSAP=[];
            if($OrdenVenta == "" || $OrdenVenta == "00000"){
                return json_encode(["error" => 'No se encontro la Orden de Fabricacion.']);
                $OrdenVenta = "N/A";
            }else{
                $OrdenVenta = $OrdenVenta->OrdenVenta;
                $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
                if($this->Nokia == $CodigoCliente OR $this->HuaweiInternacional == $CodigoCliente OR $this->Drai == $CodigoCliente){
                    $CodigoEspecial = $this->CodigoEspecial($CodigoCliente,$OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
                    if($CodigoEspecial == ""){
                        return json_encode(["error" => 'Número Especial no Encontrado, Si el problema perciste Contacta a TI!.']);
                    }
                }
            }
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(1, 1, 1); 
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);
            $pdf->SetAutoPageBreak(TRUE, 0);   
            $Fecha = $this->SemanaFecha(date('d-m-Y'));
            for ($i=$PaginaInicio; $i<($PaginaFin+1); $i++) {
                $pdf->AddPage('L', array(108,28));
                if(!file_exists(storage_path('app/Logos/Optronics.jpg'))){
                        throw new \Exception('No se encontraron el Logo requerido, por favor contactate con TI.');
                }else{
                    $imagePath = storage_path('app/Logos/Optronics.jpg');
                    $pdf->Image($imagePath, 8, 4, 20);
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->setFontSpacing(-0.2);
                    $pdf->SetXY(0, 10);
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->MultiCell(35, 0, $CodigoEspecial, 0, 'C', 0, 1);
                    $pdf->setFontSpacing(-0.1);
                    $pdf->SetXY(0, 15);
                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->MultiCell(35, 0,  $TipoDistribuidor.$OrdenFabricacion->OrdenFabricacion."-".$Fecha['Week'].$Fecha['Year'].str_pad($i, 4, '0', STR_PAD_LEFT), 0, 'C', 0, 1);

                    $i++;
                    $pdf->Image($imagePath, 46, 4, 20);
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->setFontSpacing(-0.2);
                    $pdf->SetXY(38, 10);
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->MultiCell(35, 0, $CodigoEspecial, 0, 'C', 0, 1);
                    $pdf->setFontSpacing(-0.1);
                    $pdf->SetXY(38, 15);
                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->MultiCell(35, 0,  $TipoDistribuidor.$OrdenFabricacion->OrdenFabricacion."-".$Fecha['Week'].$Fecha['Year'].str_pad($i, 4, '0', STR_PAD_LEFT), 0, 'C', 0, 1);
                }
            }
            ob_end_clean();
            return json_encode(["pdf"=>base64_encode($pdf->Output('DistribuidorTrazabilidad_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador*/
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
    //Distribuidor Etiqueta de Tubo
    public function DistribuidorEtiquetaTubo($PaginaInicio,$PaginaFin,$OrdenFabricacion,$CodigoCliente){
        try {
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(2, 2, 2); 
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetFontStretching(105);
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseO
            $pdf->SetPrintHeader(false);
            $pdf->SetAutoPageBreak(TRUE, 0);   
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.4);
            $pdf->setFontSpacing(-0.2);
            for ($i=$PaginaInicio; $i<($PaginaFin+1); $i++) {
                $pdf->AddPage('L', array(108,28));
                $pdf->SetFont('helvetica', 'B',12);
                $pdf->StartTransform();
                $pdf->Rotate(90, 13, 15);
                $pdf->SetXY(7, 6); // posición X=3 mm, Y=0 mm
                $pdf->Cell(17.5, 4,"TUBO ".($i), 0, 0, 'C'); // ancho=3 mm, alto=4 mm 
                $pdf->StopTransform();
                if($i>=$PaginaFin){
                    break;
                }
                $i++;
                $pdf->SetFont('helvetica', 'B',12);
                //Ejmeplo
                $pdf->StartTransform();
                $pdf->Rotate(90, 56, 14);
                $pdf->SetXY(49, 0);
                $pdf->Cell(17.5, 4,"TUBO ".($i), 0, 0, 'C');
                $pdf->StopTransform();
                if($i>=$PaginaFin){
                    break;
                }
                /*$i++;
                $pdf->StartTransform();
                $pdf->Rotate(90, 73, -3);
                $pdf->SetXY(49, 0);
                $pdf->Cell(17.5, 4,"TUBO ".($i), 0, 0, 'C');
                $pdf->StopTransform();*/
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaDistribuidorTubo_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }
    //Charola Trnastelco
    public function EtiquetaIdentificacionCharolasTranstelco($CantidadEtiquetas,$CodigoCliente){
        try {
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(2, 2, 2); 
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->SetAutoPageBreak(TRUE, 0);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseOutlines'
            $pdf->SetPrintHeader(false);
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                $pdf->AddPage('L', array(101, 51));
                $pdf->SetFont('dejavusans', '', 9);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->Rect(7, 5, 32 , 6 );
                $pdf->Rect(7, 12, 32 , 6 );
                $pdf->Rect(7, 19, 32 , 6 );
                $pdf->Rect(7, 26, 32 , 6 );
                $pdf->Rect(7, 33, 32 , 6 );
                $pdf->Rect(7, 40, 32 , 6 );
                $pdf->Text(7,6,"Tubo 12 (133-144)");
                $pdf->Text(7,13,"Tubo 11 (121-132)");
                $pdf->Text(7,20,"Tubo 10 (109-120)");
                $pdf->Text(7,27,"Tubo 9 (97-108)");
                $pdf->Text(7,34,"Tubo 8 (85-96)");
                $pdf->Text(7,41,"Tubo 7 (73-84)");

                $pdf->SetLineWidth(0.3);
                $pdf->Rect(46, 5, 32 , 6 );
                $pdf->Rect(46, 12, 32 , 6 );
                $pdf->Rect(46, 19, 32 , 6 );
                $pdf->Rect(46, 26, 32 , 6 );
                $pdf->Rect(46, 33, 32 , 6 );
                $pdf->Rect(46, 40, 32 , 6 );
                $pdf->Text(46,6,"Tubo 6 (61-72)");
                $pdf->Text(46,13,"Tubo 5 (49-60)");
                $pdf->Text(46,20,"Tubo 4 (37-48)");
                $pdf->Text(46,27,"Tubo 3 (25-36)");
                $pdf->Text(46,34,"Tubo 2 (13-24)");
                $pdf->Text(46,41,"Tubo 1 (1-12)");
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaIdentificacionCharolasTranstelco_'.date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Etiquetas Broadata
    public function BroadataCH($CantidadEtiquetas,$ColorCable){
        try {
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(2, 2, 2); 
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetFontStretching(105);
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseO
            $pdf->SetPrintHeader(false);
            $pdf->SetAutoPageBreak(TRUE, 0);   
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.4);
            for ($i=0; $i<($CantidadEtiquetas/4); $i++) {
                $pdf->AddPage('L', array(63.5,25));
                $pdf->SetFont('helvetica', 'B',6);
                $AumentoX = 0;
                for($j=0; $j<4; $j++){
                    $pdf->SetXY(3+$AumentoX, 2); // posición X=3 mm, Y=0 mm
                    $pdf->Cell(12.70, 9.53,$ColorCable, 0, 0, 'C'); // ancho=3 mm, alto=4 mm 
                    $AumentoX += 15; 
                    if($CantidadEtiquetas-1 == ($i*4)+$j){
                        break;
                    }
                }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaDistribuidorTubo_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function BroadataCable($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$CodigoCliente){
        try {
            // Crear PDF
            $pdf = new TCPDF();
            // Ajustar márgenes
            $pdf->SetMargins(2, 2, 2); 
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseO
            $pdf->SetPrintHeader(false);
            $pdf->SetAutoPageBreak(TRUE, 0);   
            $pdf->SetDrawColor(0, 0, 0);
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$PDFOrdenFabricacion)->first();
            $Fecha = $this->SemanaFecha(date('Y-m-d'));
            $CantidadEtiquetas = $PaginaFin-$PaginaInicio+1;
            for ($i=0; $i<$CantidadEtiquetas/3; $i++) {
                $pdf->AddPage('P', array(86,103));
                $pdf->SetFont('helvetica', 'B',8);
                $AumentoX = 0; 
                for($j=0; $j<3; $j++){
                    $pdf->SetXY(3+$AumentoX, 2); // posición X=3 mm, Y=0 mm 
                    $pdf->MultiCell(26,9.50,"BRO-".($Fecha['Year']).($Fecha['Week'])."-P".str_pad($PaginaInicio+($i*3)+$j, 4, "0", STR_PAD_LEFT)."\nP455689 Rev.A \n".$OrdenFabricacion->OrdenFabricacion.str_pad($PaginaInicio+($i*3)+$j, 4, "0", STR_PAD_LEFT),0,'C',false,0);
                    $CantidadEtiquetas-1;
                    $AumentoX += 27;
                    if($CantidadEtiquetas-1 == ($i*3)+$j){
                        break;
                    }
                }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaDistribuidorTubo_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function BroadataBolsa($CantidadEtiquetas,$OrdenFabricacion,$CodigoCliente){
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
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseO
            $pdf->SetPrintHeader(false);
            $Descripcion = html_entity_decode($OrdenFabricacion->Descripcion, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $Articulo = "";
            $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
            $NumeroRegistros = count($DatosSAP);
            $NumCatalogo = "";
            if($NumeroRegistros==0){
                throw new \Exception('Número de parte del cliente no encontrado, consulta a tu supervisor!.');
            }else{
                if($DatosSAP[0]['ItemCode']==""){
                    throw new \Exception('Número de parte del cliente no encontrado, consulta a tu supervisor!.');
                }else{
                    $NumCatalogo =  $DatosSAP[0]['ItemCode'];
                }
            }
            // Contador para saber cuántas etiquetas se han colocado en la página
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                $pdf->AddPage('L', array(101, 51));
                $pdf->SetFont('dejavusans', 'B', 16);
                //$pdf->setFontSpacing(-0.2);
                $margen = 1;
                $border_style = array(
                            'width' => 0.5,
                            'cap' => 'butt',
                            'join' => 'miter',
                            'dash' => 0,
                            'phase' => 0,
                            'color' => array(0, 0, 0) // RGB negro
                        );
                $pdf->RoundedRect(2,5, 97, 43, 1, '1111', 'D', $border_style, array());
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->SetXY(2,20);
                //$pdf->MultiCell(97, 0,$NumCatalogo, 0, 'C', 0, 1);
                $pdf->MultiCell(97, 0, $NumCatalogo."\n P45689 Rev.A\n", 0, 'C', 0, 1);
            }
            ob_end_clean();
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaBroadataBolsa_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function BroadataCaja($CantidadEtiquetas,$PDFOrdenFabricacion,$CantidadBolsa,$CodigoCliente){
        try {
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$PDFOrdenFabricacion)->first();
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
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseO
            $pdf->SetPrintHeader(false);
            $Descripcion = html_entity_decode($OrdenFabricacion->Descripcion, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $Articulo = "";
            $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
            $NumeroRegistros = count($DatosSAP);
            $NumCatalogo = "";
            if($NumeroRegistros==0){
                throw new \Exception('Número de parte del cliente no encontrado, consulta a tu supervisor!.');
            }else{
                if($DatosSAP[0]['ItemCode']==""){
                    throw new \Exception('Número de parte del cliente no encontrado, consulta a tu supervisor!.');
                }else{
                    $NumCatalogo =  $DatosSAP[0]['ItemCode'];
                }
            }
            // Contador para saber cuántas etiquetas se han colocado en la página
            for ($i=0; $i<$CantidadEtiquetas; $i++) {
                $pdf->AddPage('L', array(101, 51));
                $pdf->SetFont('dejavusans', 'B', 16);
                //$pdf->setFontSpacing(-0.2);
                $margen = 1;
                $border_style = array(
                            'width' => 0.5,
                            'cap' => 'butt',
                            'join' => 'miter',
                            'dash' => 0,
                            'phase' => 0,
                            'color' => array(0, 0, 0) // RGB negro
                        );
                $pdf->RoundedRect(2,5, 97, 43, 1, '1111', 'D', $border_style, array());
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->SetXY(2,20); 
                //$pdf->MultiCell(97, 0, $NumCatalogo."\nQTY:".$CantidadBolsa, 0, 'C', 0, 1);
                $pdf->MultiCell(97, 0, $NumCatalogo."\n P45689 Rev.A\nQTY:".$CantidadBolsa, 0, 'C', 0, 1);
            }
            ob_end_clean();
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaBroadataCaja_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function BroadataCertificado($PaginaInicio,$PaginaFin,$PDFOrdenFabricacion,$CodigoCliente){
        try {
            $OrdenFabricacion = OrdenFabricacion::where('OrdenFabricacion',$PDFOrdenFabricacion)->first();
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
            $pdf->SetAutoPageBreak(TRUE, 0);   
            $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone'); // NO usar 'UseAnnots' o 'UseO
            $pdf->SetPrintHeader(false);

            $Fecha = $this->SemanaFecha(date('Y-m-d'));
            $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion->OrdenFabricacion);
            $NumeroRegistros = count($DatosSAP);
            $NumCatalogo = "F2-P-OM3-LC-LC-075";
            if($NumeroRegistros==0){
                throw new \Exception('Número de parte del cliente no encontrado, consulta a tu supervisor!.');
            }else{
                if($DatosSAP[0]['ItemCode']==""){
                    throw new \Exception('Número de parte del cliente no encontrado, consulta a tu supervisor!.');
                }else{
                    $NumCatalogo =  $DatosSAP[0]['ItemCode'];
                }
            }
            // Contador para saber cuántas etiquetas se han colocado en la página
            for ($i=$PaginaInicio-1; $i<$PaginaFin; $i++) {
                $pdf->AddPage('P', 'LETTER'); //Tamaño Carta 216 mm × 279 mm 263 
                $pdf->SetFont('dejavusans', 'B', 16);
                //$pdf->setFontSpacing(-0.2);
                $margen = 1;
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                //Horizontal
                $pdf->Rect(16, 16, 184 , 120 );
                $pdf->Rect(16, 32, 184 , 0 );
                $pdf->Rect(16, 45, 184 , 0 );
                $pdf->Rect(16, 58, 184 , 0 );
                $pdf->Rect(16, 71, 184 , 0 );
                $pdf->Rect(16, 84, 184 , 0 );
                $pdf->Rect(16, 98, 184 , 0 );
                $pdf->Rect(16, 111, 184 , 0 );
                $pdf->Rect(16, 124, 184 , 0 );
                $pdf->Rect(16, 177, 184 , 85 );
                //Vertical
                $pdf->Rect(96, 16, 0 , 16);
                $pdf->Rect(70, 32, 0 , 39);
                $pdf->Rect(108, 71, 0 , 13);
                $pdf->Rect(104, 84, 0 , 52);
                $pdf->Rect(60, 84, 0 , 52);
                $pdf->Rect(152, 84, 0 , 52);

                //Información
                if(!file_exists(storage_path('app/Logos/Broadata.png'))){
                    return json_encode(["error" => 'No se encontraron el Logo requerido, por favor contactate con TI.']);
                }else{
                    $imagePath = storage_path('app/Logos/Broadata.png');
                    $pdf->Image($imagePath, 22, 18, 48);
                }
                $pdf->setFontSpacing(-0.4);
                $pdf->SetFont('dejavusans', '', 22);
                $pdf->SetXY(100, 17);
                $pdf->Cell(88, 16,'Fiber Optics Test Report', 0, 0, 'C');
                $pdf->setFontSpacing(-0.2);
                $pdf->SetFont('dejavusans', '', 18);
                $pdf->SetXY(10, 33);
                $pdf->Cell(70, 13,'P/N:', 0, 0, 'C');
                $pdf->SetXY(96, 33);
                $pdf->Cell(70, 13,'BRO-'.$Fecha['Year'].$Fecha['Week']."P".str_pad(($i+1), 4, "0", STR_PAD_LEFT), 0, 0, 'C');
                $pdf->SetXY(10, 46);
                $pdf->Cell(70, 13,'Description:', 0, 0, 'C');
                $pdf->SetXY(96, 46);
                $pdf->Cell(70, 13,$NumCatalogo, 0, 0, 'C');
                $pdf->SetXY(10, 59);
                $pdf->Cell(70, 13,'Serial No:', 0, 0, 'C');
                $pdf->SetXY(96, 59);
                $pdf->Cell(70, 13,$OrdenFabricacion->OrdenFabricacion.str_pad(($i+1), 4, "0", STR_PAD_LEFT), 0, 0, 'C');
                $pdf->SetXY(16, 72);
                $pdf->Cell(92, 13,'End A:', 0, 0, 'C');
                $pdf->SetXY(108, 72);
                $pdf->Cell(92, 13,'End B:', 0, 0, 'C');
                $pdf->SetXY(16, 85);
                $pdf->Cell(184, 13,'  Wavelength        850 nm         Wavelength         850nm', 0, 0, 'L');
                $pdf->SetXY(16, 98);
                $pdf->Cell(184, 13,'                               IL (dB)                                        IL(dB)', 0, 0, 'L');
                $pdf->SetXY(16, 111);
                $pdf->Cell(184, 13,'        CH1                 P/N:                   CH1                   P/N:', 0, 0, 'L');
                $pdf->SetXY(16, 124);
                $pdf->Cell(184, 13,'        CH2                 P/N:                   CH2                   P/N:', 0, 0, 'L');
                $pdf->SetXY(16, 165);
                $pdf->SetFont('dejavusans', 'B', 18);
                $pdf->Cell(184, 13,'Fiber Optic Connector Handling Quick Tips:', 0, 0, 'C');
                $pdf->SetFont('dejavusans', '', 12);
                $pdf->SetXY(16,178); 
                $pdf->MultiCell(184, 0, "Safety:\n1. Disconnect sources before inspecting connector ends.\nCleaning:\n2. ALWAYS Visual inspect the connector inmediately prior to Mate. Re-clean if the connector is contaminated then mate connector.\n3. Do NOT use compressed air directly on the connector's end face.\n4. Use ONLY CLETOP or optical grade alcohol with lint-free wipes for cleaning. Make sure the end face is dried before inserting the connector.\nHandling:\n5. Prevent connector end surface from contacting any other object during insertion.\n6. Do not pull the fiber when extracting connector.\n7. Do not apply excessive force when cleaning.\n8. Keep dust cap on when not using connector", 0, 'L', 0, 1);
                $pdf->SetXY(16,250);
                $pdf->SetFont('dejavusans', 'B', 18);
                $pdf->Cell(184, 13,'www.broadatacom.com', 0, 0, 'C');
                
            }
            ob_end_clean();
            return json_encode(["pdf"=>base64_encode($pdf->Output('EtiquetaBroadataCaja_'.$OrdenFabricacion->OrdenFabricacion.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function EtiquetaDistribuidores($CantidadEtiquetas, $TipoDistribuidor){
         try {
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
                //Agregar la imagen
                if(!file_exists(storage_path('app/Etiquetas/Distribuidores/'.$TipoDistribuidor.'.png'))){
                    throw new \Exception('No se encontro la imagen, La etiqueta '.$TipoDistribuidor.' es requerida, por favor contactate con TI.');
                }else{
                    $imagePath = storage_path('app/Etiquetas/Distribuidores/'.$TipoDistribuidor.'.png');
                    $pdf->Image($imagePath, ($pdf->GetPageWidth()-95)/2, 2, 95);
                }
            }
            ob_end_clean();
            // Generar el archivo PDF y devolverlo al navegador
            return json_encode(["pdf"=>base64_encode($pdf->Output($TipoDistribuidor.'_' .date('dmY'). '.pdf', 'S'))]); // 'I' para devolver el PDF al navegador
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //Menu etiquetas y restricciones clientes etiquetas
    public function Menu($CodigoCliente){
        $Etiquetas = $this->Etiquetas;
        if($CodigoCliente == $this->HuaweiInternacional){
            $Etiquetas = array_filter($Etiquetas, function($etiqueta) {
                return $etiqueta[0] == 2 || $etiqueta[0] == 6 ||
                        $etiqueta[0] == 13;
                /*return $etiqueta[0] !== 1 &&
                        $etiqueta[0] !== 3 && $etiqueta[0] !== 4 &&
                        $etiqueta[0] !== 5 &&
                        $etiqueta[0] !== 7 && $etiqueta[0] !== 8 && 
                        $etiqueta[0] !== 9 && $etiqueta[0] !== 10 &&
                        $etiqueta[0] !== 11 && $etiqueta[0] !== 12 && 
                        $etiqueta[0] !== 14 &&
                        $etiqueta[0] !== 15 && $etiqueta[0] !== 16 &&
                        $etiqueta[0] !== 17 && $etiqueta[0] !== 18 &&
                        $etiqueta[0] !== 19 && $etiqueta[0] !== 20 &&
                        $etiqueta[0] !== 21 && $etiqueta[0] !== 22 &&
                        $etiqueta[0] !== 23 && $etiqueta[0] !== 24 && 
                        $etiqueta[0] !== 25 && $etiqueta[0] !== 26;*/
            });
        
        }elseif($CodigoCliente == $this->Nokia){
            $Etiquetas = array_filter($Etiquetas, function($etiqueta) {
                return $etiqueta[0] == 3 ||  $etiqueta[0] == 6 ||
                        $etiqueta[0] == 8 || $etiqueta[0] == 14;
                /*return $etiqueta[0] !== 1 && $etiqueta[0] !== 2 &&
                        $etiqueta[0] !== 4 &&
                        $etiqueta[0] !== 5 &&
                        $etiqueta[0] !== 7 && $etiqueta[0] !== 8 && 
                        $etiqueta[0] !== 9 && $etiqueta[0] !== 10 &&
                        $etiqueta[0] !== 11 && $etiqueta[0] !== 12 && 
                        $etiqueta[0] !== 13 &&
                        $etiqueta[0] !== 15 && $etiqueta[0] !== 16 &&
                        $etiqueta[0] !== 17 && $etiqueta[0] !== 18 &&
                        $etiqueta[0] !== 19 && $etiqueta[0] !== 20 &&
                        $etiqueta[0] !== 21 && $etiqueta[0] !== 22 &&
                        $etiqueta[0] !== 23 && $etiqueta[0] !== 24 && 
                        $etiqueta[0] !== 25 && $etiqueta[0] !== 26;*/
            });
        }elseif($CodigoCliente == $this->Drai){
            $Etiquetas = array_filter($Etiquetas, function($etiqueta) {
                return $etiqueta[0] == 4 || $etiqueta[0] == 6 ||
                     $etiqueta[0] == 17 ||  $etiqueta[0] == 19;
                    /*return $etiqueta[0] !== 1 && $etiqueta[0] !== 2 &&
                        $etiqueta[0] !== 3 &&
                        $etiqueta[0] !== 5 &&
                        $etiqueta[0] !== 7 && $etiqueta[0] !== 8 && 
                        $etiqueta[0] !== 9 && $etiqueta[0] !== 10 &&
                        $etiqueta[0] !== 11 && $etiqueta[0] !== 12 && 
                        $etiqueta[0] !== 13 && $etiqueta[0] !== 14 &&
                        $etiqueta[0] !== 16 &&
                        $etiqueta[0] !== 18 &&
                        $etiqueta[0] !== 19 && $etiqueta[0] !== 20 &&
                        $etiqueta[0] !== 21 && $etiqueta[0] !== 22 &&
                        $etiqueta[0] !== 23 && $etiqueta[0] !== 24 && 
                        $etiqueta[0] !== 25 && $etiqueta[0] !== 26;*/
            });
        }elseif($CodigoCliente == $this->OptronicsLLC ){
            $Etiquetas = array_filter($Etiquetas, function($etiqueta) {
            return  $etiqueta[0] == 1 || 
                    $etiqueta[0] == 5 || 
                    $etiqueta[0] == 7 || $etiqueta[0] == 8 ||
                    $etiqueta[0] == 9 || $etiqueta[0] == 10 ||
                    $etiqueta[0] == 11 || $etiqueta[0] == 12 ||
                    $etiqueta[0] == 15 || $etiqueta[0] == 16 ||
                    $etiqueta[0] == 18 ||
                    $etiqueta[0] == 20 ||
                    $etiqueta[0] == 21 || $etiqueta[0] == 22 ||
                    $etiqueta[0] == 23 || $etiqueta[0] == 24 ||
                    $etiqueta[0] == 25 || $etiqueta[0] == 26 ||
                    $etiqueta[0] == 27 || $etiqueta[0] == 28 ||
                    $etiqueta[0] == 29 ||  $etiqueta[0] == 30 ||  
                    $etiqueta[0] == 31;
            });
        }else{
            $Etiquetas = array_filter($Etiquetas, function($etiqueta) {
                return $etiqueta[0] == 1 || 
                    $etiqueta[0] == 5 || 
                    $etiqueta[0] == 7 || $etiqueta[0] == 8 ||
                    $etiqueta[0] == 9 || $etiqueta[0] == 10 ||
                    $etiqueta[0] == 11 || $etiqueta[0] == 12 ||
                    $etiqueta[0] == 15 || $etiqueta[0] == 16 ||
                    $etiqueta[0] == 17 || $etiqueta[0] == 18 ||
                    $etiqueta[0] == 20 ||
                    $etiqueta[0] == 21 || $etiqueta[0] == 22 ||
                    $etiqueta[0] == 23 || $etiqueta[0] == 24 ||
                    $etiqueta[0] == 25 || $etiqueta[0] == 26;
            });
        }
         //Ordenar por el nombre de la etiqueta en Orden Alfabetico
        usort($Etiquetas, function($a, $b) {
            return strcmp($a[2], $b[2]);
        });
        return $Etiquetas;
    }
    public function Campos(Request $request){
        $TipoEtiqueta = $request->input('TipoEtiqueta');
        $OrdenFabricacion = $request->input('OrdenFabricacion');
        $CodigoCliente = $request->input('CodigoCliente');
        $OF = OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
        $Logo = "OPT";
        $Opciones = '<option value="FMX">Fibremex</option>
                    <option value="OPT" selected>Optronics</option>';
        if($CodigoCliente == $this->Fibremex){
            $Logo = "FMX";
             $Opciones = '<option value="FMX" selected>Fibremex</option>
                        <option value="OPT">Optronics</option>';
        }
        $Titulo = "";
        foreach($this->Etiquetas as $TituloE){
            if($TituloE[1] == $TipoEtiqueta){
                $Titulo = $TituloE[2];
            }
        }
        switch($TipoEtiqueta){
            case 'ETIQ1':
                $CamposRequeridos = ['PaginaInicio','PaginaFin'];
                break;
            case 'ETIQ2':
                $CamposRequeridos = ['PaginaInicio','PaginaFin',];
                break;
            case 'ETIQ3':
                $CamposRequeridos = ['PaginaInicio','PaginaFin',];
                break;
            case 'ETIQ4':
                $CamposRequeridos = ['CantidadEtiquetas'];
                break;
            case 'ETIQ4CEDIS':
                $CamposRequeridos = ['CantidadEtiquetas'];
                break;
            case 'ETIQ5':
                $CamposRequeridos = ['Sociedad','CantidadEtiquetas','CantidadBolsa'];
                break;
            case 'ETIQ6':
                $CamposRequeridos = ['PaginaInicio','PaginaFin','Insercion','Retorno'];
                break;
            case 'ETIQ7':
                $CamposRequeridos = ['CantidadEtiquetas'];
                break;
            case 'ETIQ8':
                $CamposRequeridos = ['CantidadEtiquetas','PorcentajeA','PorcentajeB','PorcentajeC','PorcentajeD'];
                break;
            case 'ETIQ9':
                $CamposRequeridos = ['PaginaInicio','PaginaFin','CantidadBolsa','CantidadCajas'];
                break;
            case 'ETIQ10':
                $CamposRequeridos = ['PaginaInicio','PaginaFin','CantidadBolsa','CantidadCajas'];
                break;
            case 'ETIQ11':
                $CamposRequeridos = ['CantidadEtiquetas','CantidadBolsa'];
                break;
            case 'ETIQ12':
                $CamposRequeridos = ['CantidadEtiquetas','CantidadBolsa'];
                break;
            case 'ETIQ13':
                $CamposRequeridos = ['PaginaInicio','PaginaFin','Insercion','Retorno'];
                break;
            case 'ETIQ14':
                $CamposRequeridos = $CamposRequeridos = ['PaginaInicio','PaginaFin','TipoDistribuidor'];
                break;
            case 'ETIQ15':
                $CamposRequeridos = $CamposRequeridos = ['PaginaInicio','PaginaFin'];
                break;
            case 'ETIQ15':
                $CamposRequeridos = $CamposRequeridos = ['PaginaInicio','PaginaFin'];
                break;
            case 'ETIQ16':
                $CamposRequeridos = $CamposRequeridos = ['CantidadEtiquetas'];
                break;
            case 'ETIQ17':
                $CamposRequeridos = $CamposRequeridos = ['CantidadEtiquetas','MenuDistribuidor1UR'];
                break;
            case 'ETIQ18':
                $CamposRequeridos = $CamposRequeridos = ['CantidadEtiquetas','MenuDistribuidor2UR'];
                break;
            case 'ETIQ19':
                $CamposRequeridos = $CamposRequeridos = ['CantidadEtiquetas','MenuDistribuidor4UR'];
                break;
            case 'ETIQ20':
                $CamposRequeridos = $CamposRequeridos = ['CantidadEtiquetas','MenuDistribuidorPared'];
                break;
            case 'ETIQ21':
                $CamposRequeridos = $CamposRequeridos = ['CantidadEtiquetas','MenuDistribuidorExterior'];
                break;
            case 'ETIQ22':
                $CamposRequeridos = $CamposRequeridos = ['CantidadEtiquetas','MenuDistribuidorRielDin'];
                break;
            case 'ETIQ23':
                $CamposRequeridos = $CamposRequeridos = ['CantidadEtiquetas','ColorCable'];
                break;
            case 'ETIQ24':
                $CamposRequeridos = $CamposRequeridos = ['PaginaInicio','PaginaFin'];
                break;
            case 'ETIQ25':
                $CamposRequeridos = $CamposRequeridos = ['CantidadEtiquetas'];
                break;
            case 'ETIQ26':
                $CamposRequeridos = $CamposRequeridos = ['CantidadEtiquetas','CantidadBolsa'];
                break;
             case 'ETIQ27':
                $CamposRequeridos = $CamposRequeridos = ['PaginaInicio','PaginaFin'];
                break;
            default:
            $CamposRequeridos = [];
                break;
        }
        array_push($CamposRequeridos, 'Boton');
        $Cantidad = ($OF->CantidadTotal>0)?$OF->CantidadTotal:1;
                    $Campos = [["Sociedad",' <div class="col-3" id="ContenedorSociedad">
                                    <div class="mb-3">
                                        <label class="form-label" for="Sociedad">Logo</label>
                                            <select class="form-select" id="Sociedad" data-choices="data-choices" size="1" required="required" name="organizerSingle" data-options=\'{"removeItemButton":true,"placeholder":true}\'>
                                                <option value="" disabled>Selecciona una Opci&oacute;n</option>
                                                '.$Opciones.'
                                            </select>
                                    </div>
                                </div>'],
                    ["CantidadEtiquetas",'<div class="col-3" id="ContenedorCantidadEtiquetas">
                                        <div class="mb-3">
                                            <label class="form-label" for="CantidadEtiquetas">Cantidad de Etiquetas  </label>
                                            <input class="form-control" oninput="RegexNumeros(this)" id="CantidadEtiquetas" value ="'.$Cantidad.'" type="number" placeholder="0" />
                                            <small id="ErrorCantidadEtiquetas" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["PaginaInicio",'<div class="col-3" id="ContenedorPaginaInicio">
                                        <div class="mb-3">
                                            <label class="form-label" for="PaginaInicio">Etiqueta inicio </label>
                                            <input class="form-control" id="PaginaInicio" oninput="RegexNumeros(this)" type="number" value="1" min="1" max="'.$Cantidad.'" placeholder="0" />
                                            <small id="ErrorPaginaInicio" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["PaginaFin",' <div class="col-3" id="ContenedorPaginaFin">
                                        <div class="mb-3">
                                            <label class="form-label" for="PaginaFin">Etiqueta fin  </label>
                                            <input class="form-control" id="PaginaFin" oninput="RegexNumeros(this)" type="number"min="1" value="'.$Cantidad.'" max="'.$Cantidad.'" placeholder="0" />
                                            <small id="ErrorPaginaFin" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["CantidadCajas",'<div class="col-3" id="ContenedorCantidadCajas">
                                        <div class="mb-3">
                                            <label class="form-label" for="CantidadCajas">Cantidad de Cajas  </label>
                                            <input class="form-control" id="CantidadCajas" oninput="CantidadFinal();RegexNumeros(this);" value="'.$Cantidad.'" type="number" placeholder="0" />
                                            <small id="ErrorCantidadCajas" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["CantidadBolsa",'<div class="col-3" id="ContenedorCantidadBolsa">
                                        <div class="mb-3">
                                            <label class="form-label" for="CantidadBolsa">Cantidad por bolsa o caja  </label>
                                            <input class="form-control" id="CantidadBolsa" oninput="RegexNumeros(this)" type="number" placeholder="0" value="1" />
                                            <small id="ErrorCantidadBolsa" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["PorcentajeA",'<div class="col-3" id="ContenedorPorcentajeA">
                                        <div class="mb-3">
                                            <label class="form-label" for="PorcentajeA">Medida 1  </label>
                                            <input class="form-control" id="PorcentajeA" type="number" oninput="ValorDivisor(this, \'A\')" placeholder="0" min="1" max="100" value="50" />
                                            <small id="ErrorPorcentajeA" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["PorcentajeB",'<div class="col-3" id="ContenedorPorcentajeB">
                                        <div class="mb-3">
                                            <label class="form-label" for="PorcentajeB">Medida 2  </label>
                                            <input class="form-control" id="PorcentajeB" type="number" oninput="ValorDivisor(this, \'B\')" placeholder="0" min="1" max="100" value="50" />
                                            <small id="ErrorPorcentajeB" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["PorcentajeC",'<div class="col-3" id="ContenedorPorcentajeC">
                                        <div class="mb-3">
                                            <label class="form-label" for="PorcentajeC">Medida 3  </label>
                                            <input class="form-control" id="PorcentajeC" type="number" oninput="ValorDivisor(this, \'C\')" placeholder="0" min="1" max="100" value="50" />
                                            <small id="ErrorPorcentajeC" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["PorcentajeD",'<div class="col-3" id="ContenedorPorcentajeD">
                                        <div class="mb-3">
                                            <label class="form-label" for="PorcentajeD">Medida 4  </label>
                                            <input class="form-control" id="PorcentajeD" type="number" oninput="ValorDivisor(this, \'D\')" placeholder="0" min="1" max="100" value="50" />
                                            <small id="ErrorPorcentajeD" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["Insercion",'<div class="col-3" id="ContenedorInsercion">
                                        <div class="mb-3">
                                            <label class="form-label" for="Insercion">CONECTORIZACI&Oacute;N A</label>
                                            <!--<input class="form-control" id="Insercion" type="number" placeholder="0" value="0.50" />-->
                                            <select class="form-select" id="Insercion">
                                                <option value="" selected disabled>Selecciona una opci&oacute;n</option>
                                                <option value="MUPC">MULTIMODO (MM) PC</option>
                                                <option value="MOUPC">MONOMODO (SM) UPC</option>
                                                <option value="MOAPC">MONOMODO (SM) APC</option>
                                                <option value="MULMTRJ">ESPECIALES MULTIMODO MTRJ</option>
                                                <option value="MONMTRJ">ESPECIALES MONOMODO MTRJ</option>
                                                <option value="MUMPO">MULTIMODO MPO (UPC)</option>
                                                <option value="MOMPO">MONOMODO MPO (UPC-APC)</option>
                                                <option value="MUMTP">MULTIMODO MTP Est&aacute;ndar (UPC)</option>
                                                <option value="MOMTP">MONOMODO MTP Est&aacute;ndar (UPC-APC)</option>
                                                <option value="MUMTP_PRO">MULTIMODO MTP-PRO (UPC)</option>
                                                <option value="MOMTP_PRO">MONOMODO MTR-PRO (UPC-APC)</option>
                                            </select>
                                            <small id="ErrorInsercion" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["Retorno",'<div class="col-3" id="ContenedorRetorno">
                                        <div class="mb-3">
                                            <label class="form-label" for="Retorno">CONECTORIZACI&Oacute;N B</label>
                                            <select class="form-select" id="Retorno">
                                                <option value="" selected>Selecciona una opci&oacute;n</option>
                                                <option value="MUPC">MULTIMODO (MM) PC</option>
                                                <option value="MOUPC">MONOMODO (SM) UPC</option>
                                                <option value="MOAPC">MONOMODO (SM) APC</option>
                                                <option value="MULMTRJ">ESPECIALES MULTIMODO MTRJ</option>
                                                <option value="MONMTRJ">ESPECIALES MONOMODO MTRJ</option>
                                                <option value="MUMPO">MULTIMODO MPO (UPC)</option>
                                                <option value="MOMPO">MONOMODO MPO (UPC-APC)</option>
                                                <option value="MUMTP">MULTIMODO MTP Est&aacute;ndar (UPC)</option>
                                                <option value="MOMTP">MONOMODO MTP Est&aacute;ndar (UPC-APC)</option>
                                                <option value="MUMTP_PRO">MULTIMODO MTP-PRO (UPC)</option>
                                                <option value="MOMTP_PRO">MONOMODO MTR-PRO (UPC-APC)</option>
                                            </select>
                                            <small id="ErrorRetorno" class="text-danger"></small>
                                        </div>
                                    </div>'],
                    ["TipoDistribuidor",'<div class="col-3" id="ContenedorTipoDistribuidor"><div class="mb-3">
                                            <label class="form-label" for="TipoDistribuidor">Tipo de Distribuidor</label>
                                            <select class="form-select" id="TipoDistribuidor">
                                                <option value="" selected disabled>Selecciona una opci&oacute;n</option>
                                                <option value="M">MULTIMODO </option>
                                                <option value="S">MONOMODO</option>
                                                <option value="MA">RE EMPAQUE </option>
                                            </select><small id="ErrorTipoDistribuidor" class="text-danger"></small></div>
                                    </div>'],
                    ["Boton",'<div class="col-3 d-flex align-items-end" id="ContenedorBoton"><div class="mb-2">
                                    <button type="button" id="BtnGenerar" class="btn btn-phoenix-primary me-1 mb-1">Generar</button>
                                </div></div>'],

                    ["MenuDistribuidor1UR",'<div class="col-6" id="ContenedorMenuDistribuidor">
                                                <div class="mb-3">
                                                    <label class="form-label" for="MenuDistribuidor">Distribuidor</label>
                                                    <select class="form-select" id="MenuDistribuidor" onchange="mostrarSeleccion(this)">
                                                        <option value="" selected disabled>Selecciona una opci&oacute;n</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>MPO HIGT DENSITY</option>
                                                        <option value="1UR12CassettesMPOTypeA-B1">12 Cassettes MPO Type A-B1</option>
                                                        <option value="1UR12CassettesMPOTypeB">12 Cassettes MPO Type B</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA FC-ST SIMPLEX</option>
                                                        <option value="1URPLACA6DE4ACOPLADORESSXHORIZONTALFC-ST">PLACA 6 DE 4 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="1URPLACA6DE6ACOPLADORESSXHORIZONTALFC-ST">PLACA 6 DE 6 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="1URPLACA6DE12ACOPLADORESSXHORIZONTALFC-ST">PLACA 6 DE 12 ACOPLADORES  SX HORIZONTAL FC-ST</option>
                                                        <option value="1URPLACA88ACOPLADORESSXHORIZONTALFC-ST">PLACA 8 DE 8 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="1URPLACA812ACOPLADORESSXHORIZONTALFC-ST">PLACA 8 DE 12 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="1URPLACA824ACOPLADORESSXHORIZONTALFC-ST">PLACA 8 DE 24 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="1UR12ACOPLADORESSXHORIZONTALFC-STABBDEMEXICO">PLACA 12 DE 12 ACOPLADORES SX HORIZONTAL FC-ST ABB DE MEXICO</option>
                                                        <option value="1UR12ACOPLADORESSXHORIZONTALFC-ST">PLACA 12 DE 12 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="1UR24ACOPLADORESSXHORIZONTALFC-ST">PLACA 12 DE 24 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="1UR36ACOPLADORESSXHORIZONTALFC-ST">PLACA 12 DE 36 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA LC DUPLEX</option>
                                                        <option value="1UR2ACOPLADORESDUPLEXHORIZONTALLCECO">2 ACOPLADORES DUPLEX HORIZONTAL LC ECO</option>
                                                        <option value="1UR2ACOPLADORESDUPLEXHORIZONTALLC">2 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="1UR3ACOPLADORESDUPLEXHORIZONTALLC">3 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="1UR4ACOPLADORESDUPLEXHORIZONTALLCATC">4 ACOPLADORES DUPLEX HORIZONTAL LC (ATC)</option>
                                                        <option value="1UR4ACOPLADORESDUPLEXHORIZONTALLC">4 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="1UR6ACOPLADORESDUPLEXHORIZONTALLC">6 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="1UR9ACOPLADORESDUPLEXHORIZONTALLC">9 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="1UR12ACOPDUPLEXHLC6SC">12 ACOP DUPLEX H LC  6 SC</option>
                                                        <option value="1UR12ACOPLADORESCUADRUPLEXLC">12 ACOPLADORES CUADRUPLEX LC</option>
                                                        <option value="1UR12ACOPLADORESDUPLEXHORIZONTALLC">12 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="1UR18ACOPLADORESCUADRUPLEXLC">18 ACOPLADORES CUADRUPLEX LC</option>
                                                        <option value="1UR18ACOPLADORESDUPLEXHORIZONTALLC">18 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA SC DUPLEX</option>
                                                        <option value="1UR2ACOPLADORESDUPLEXHORIZONTALSCECO">2 ACOPLADORES DUPLEX HORIZONTAL SC ECO</option>
                                                        <option value="1UR2ACOPLADORESDUPLEXHORIZONTALSC">2 ACOPLADORES DUPLEX HORIZONTAL SC</option>
                                                        <option value="1UR3ACOPLADORESDUPLEXHORIZONTALSC">3 ACOPLADORES DUPLEX HORIZONTAL SC</option>
                                                        <option value="1UR6ACOPLADORESDUPLEXHORIZONTALSC">6 ACOPLADORES DUPLEX HORIZONTAL SC</option>
                                                        <option value="1UR9ACOPLADORESDUPLEXHORIZONTALSC">9 ACOPLADORES DUPLEX HORIZONTAL SC</option>
                                                        <option value="1UR12ACOPLADORESDUPLEXHORIZONTALSC">12 ACOPLADORES DUPLEX HORIZONTAL SC</option>
                                                        <option value="1UR18ACOPLADORESDUPLEXHORIZONTALSC6PIGTAIL6COLORES">18 ACOPLADORES DUPLEX HORIZONTAL SC 6 PIGTAIL 6 COLORES</option>
                                                        <option value="1UR18ACOPLADORESDUPLEXHORIZONTALSC">18 ACOPLADORES DUPLEX HORIZONTAL SC</option>
                                                        <option value="1UR24ACOPLADORESDUPLEXHORIZONTALSCDRAI">24 ACOPLADORES DUPLEX HORIZONTAL SC DRAI</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA SC SIMPLEX</option>
                                                        <option value="1UR4ACOPLADORESSIMPLEXHORIZONTALSC">4 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                        <option value="1UR6ACOPLADORESSIMPLEXHORIZONTALSC">6 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                        <option value="1UR12ACOPLADORESSIMPLEXHORIZONTALSC">12 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                        <option value="1UR18ACOPLADORESSIMPLEXHORIZONTALSC">18 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                        <option value="1UR24ACOPLADORESSIMPLEXHORIZONTALSC">24 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                        <option value="1UR96ACOPLADORESSIMPLEXHORIZONTALSC">96 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                    </select>
                                                    <small id="ErrorMenuDistribuidor" class="text-danger"></small>
                                                </div>
                                            </div>'],
                    ["MenuDistribuidor2UR",'<div class="col-6" id="ContenedorMenuDistribuidor">
                                                <div class="mb-3">
                                                    <label class="form-label" for="MenuDistribuidor">Distribuidor</label>
                                                    <select class="form-select" id="MenuDistribuidor" onchange="mostrarSeleccion(this)">
                                                        <option value="" selected disabled>Selecciona una opci&oacute;n</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA FC-ST SIMPLEX</option>

                                                        <option value="2UR6ACOPLADORESSIMPLEXHORIZONTALFC-ST(ABBDEMEXICO)">6 ACOPLADORES SIMPLEX HORIZONTAL FC-ST (ABB DE MEXICO)</option>
                                                        <option value="2UR6ACOPLADORESSIMPLEXHORIZONTALFC-ST">6 ACOPLADORES SIMPLEX HORIZONTAL FC-ST</option>
                                                        <option value="2UR8ACOPLADORESSIMPLEXHORIZONTALFC-STTELEFONICA">8 ACOPLADORES SIMPLEX HORIZONTAL FC-ST TELEFONICA</option>
                                                        <option value="2UR8ACOPLADORESSIMPLEXHORIZONTALFC-ST">8 ACOPLADORES SIMPLEX HORIZONTAL FC-ST</option>
                                                        <option value="2UR24ACOPLADORESSXHORIZONTALFC-ST">24 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="2UR36ACOPLADORESSXHORIZONTALFC-ST">36 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="2UR48ACOPLADORESSXHORIZONTALFC-ST">48 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="2UR64ACOPLADORESSXHORIZONTALFC-ST">64 ACOPLADORES SX HORIZONTAL FC-ST</option>
                                                        <option value="2UR72ACOPLADORESSXHORIZONTALFC-ST">72 ACOPLADORES SX HORIZONTAL FC-ST</option>

                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA LC DUPLEX</option>
                                                        <option value="2UR3ACOPLADORESDUPLEXHORIZONTALLC">3 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="2UR6ACOPLADORESDUPLEXHORIZONTALLC">6 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="2UR12ACOPLADORESDUPLEXHORIZONTALLC-copia">12 ACOPLADORES DUPLEX HORIZONTAL LC - copia</option>
                                                        <option value="2UR12ACOPLADORESDUPLEXHORIZONTALLC">12 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="2UR12ACOPLADORESLCDUPLEX-12ACOPLADORESSCDUPLEX">12 ACOPLADORES LC DUPLEX-12 ACOPLADORES SC DUPLEX</option>
                                                        <option value="2UR18ACOPLADORESDUPLEXHORIZONTALLC">18 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="2UR18ACOPLADORESDUPLEXHORIZONTALLCFR">18 ACOPLADORES DUPLEX HORIZONTAL LC FR</option>
                                                        <option value="2UR24ACOPLADORESDUPLEXHORIZONTALLC">24 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="2UR30ACOPLADORESDUPLEXHORIZONTALLC">30 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="2UR36ACOPLADORESDUPLEXHORIZONTALLC">36 ACOPLADORES DUPLEX HORIZONTAL LC</option>

                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA SC DUPLEX</option>
                                                        <option value="2UR9ACOPLADORESDUPLEXLCHORIZONTAL18PIGTAIL">9 ACOPLADORES DUPLEX LC HORIZONTAL 18 PIGTAIL</option>
                                                        <option value="2UR9ACOPLADORESDUPLEXLCY9ACOPLADORESDUPLEXSC2">9 ACOPLADORES DUPLEX LC Y 9 ACOPLADORES DUPLEX SC.2</option>
                                                        <option value="2UR9ACOPLADORESDUPLEXLCY9ACOPLADORESDUPLEXSC">9 ACOPLADORES DUPLEX LC Y 9 ACOPLADORES DUPLEX SC</option>
                                                        <option value="2UR12ACOPLADORESDUPLEXHORIZONTALSC">12 ACOPLADORES DUPLEX HORIZONTAL SC</option>
                                                        <option value="2UR18ACOPLADORESDUPLEXHORIZONTALSC">18 ACOPLADORES DUPLEX HORIZONTAL SC</option>
                                                        <option value="2UR24ACOPLADORESDUPLEXHORIZONTALSC">24 ACOPLADORES DUPLEX HORIZONTAL SC</option>
                                                        <option value="2UR24ACOPLADORESHORIZONTALCUADRUPLEXLC">24 ACOPLADORES HORIZONTAL CUADRUPLEX LC</option>
                                                        <option value="2UR36ACOPLADORESDUPLEXHORIZONTALSC">36 ACOPLADORES DUPLEX HORIZONTAL SC</option>
                                                        <option value="2UR36ACOPLADORESHORIZONTALCUADRUPLEXLC">36 ACOPLADORES HORIZONTAL CUADRUPLEX LC</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA SC SIMPLEX</option>
                                                        <option value="2UR6ACOPLADORESSIMPLEXHORIZONTALSC">6 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                        <option value="2UR16ACOPLADORESSIMPLEXHORIZONTALSCFBTCOUPLER">16 ACOPLADORES SIMPLEX HORIZONTAL SC FBT COUPLER</option>
                                                        <option value="2UR18ACOPLADORESSIMPLEXHORIZONTALSC">18 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                        <option value="2UR24ACOPLADORESSIMPLEXHORIZONTALSCy12LCDUPLEX">24 ACOPLADORES SIMPLEX HORIZONTAL SC y 12 LC DUPLEX</option>
                                                        <option value="2UR24ACOPLADORESSIMPLEXHORIZONTALSC">24 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                        <option value="2UR36ACOPLADORESSIMPLEXHORIZONTALSC">36 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                        <option value="2UR48ACOPLADORESSIMPLEXHORIZONTALSC">48 ACOPLADORES SIMPLEX HORIZONTAL SC</option>
                                                    </select>
                                                    <small id="ErrorMenuDistribuidor" class="text-danger"></small>
                                                </div>
                                            </div>'],
                    ["MenuDistribuidor4UR",'<div class="col-6" id="ContenedorMenuDistribuidor">
                                                <div class="mb-3">
                                                    <label class="form-label" for="MenuDistribuidor">Distribuidor</label>
                                                    <select class="form-select" id="MenuDistribuidor" onchange="mostrarSeleccion(this)">
                                                        <option value="" selected disabled>Selecciona una opci&oacute;n</option>

                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA FC-ST SIMPLEX</option>
                                                        <option value="4URPLACA6DE6ACOPLADORESSIMPLEXVERTICALFC-ST">PLACA 6 DE 6 ACOPLADORES SIMPLEX VERTICAL FC-ST</option>
                                                        <option value="4URPLACA8ACOPLADORESSIMPLEXVERTICALFC-STTELEFONICA">PLACA 8 DE 8 ACOPLADORES SIMPLEX VERTICAL FC-ST TELEFONICA</option>
                                                        <option value="4URPLACA12DE48ACOPLADORESSIMPLEXVERTICALFC-ST">PLACA 12 DE 48 ACOPLADORES SIMPLEX VERTICAL FC-ST</option>
                                                        <option value="4URPLACA12DE144ACOPLADORESSIMPLEXVERTICALFC-ST">PLACA 12 DE 144 ACOPLADORES SIMPLEX VERTICAL FC-ST</option>

                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA LC DUPLEX</option>
                                                        <option value="4UR6ACOPLADORESDUPLEXVERTICALLC">6 ACOPLADORES DUPLEX VERTICAL LC (Propuesta)</option>
                                                        <option value="4UR18ACOPLADORESDUPLEXVERTICALLC">18 ACOPLADORES DUPLEX VERTICAL LC</option>
                                                        <option value="4UR48ACOPLADORESDUPLEXVERTICALLC">48 ACOPLADORES DUPLEX VERTICAL LC</option>
                                                        <option value="4UR72ACOPLADORESDUPLEXVERTICALLC">72 ACOPLADORES DUPLEX VERTICAL LC</option>

                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA SC DUPLEX</option>
                                                        <option value="4UR18ACOPLADORESDUPLEXVERTICALSC">18 ACOPLADORES DUPLEX VERTICAL SC</option>
                                                        <option value="4UR24ACOPLADORESDUPLEXVERTICALSC">24 ACOPLADORES DUPLEX VERTICAL SC</option>
                                                        <option value="4UR48ACOPLADORESCUADRUPLEXVERTICALLC">48 ACOPLADORES CUADRUPLEX VERTICAL LC</option>
                                                        <option value="4UR48ACOPLADORESDUPLEXVERTICALSC">48 ACOPLADORES DUPLEX VERTICAL SC</option>
                                                        <option value="4UR72ACOPLADORESCUADRUPLEXVERTICALLC">72 ACOPLADORES CUADRUPLEX VERTICAL LC</option>
                                                        <option value="4UR72ACOPLADORESDUPLEXVERTICALSC">72 ACOPLADORES DUPLEX VERTICAL SC</option>

                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA SC SIMPLEX</option>
                                                        <option value="4UR48ACOPLADORESSIMPLEXVERTICALSC">48 ACOPLADORES SIMPLEX VERTICAL SC</option>
                                                        <option value="4UR72ACOPLADORESSIMPLEXVERTICALSC">72 ACOPLADORES SIMPLEX VERTICAL SC</option>
                                                        <option value="4UR96ACOPLADORESSIMPLEXVERTICALSC">96 ACOPLADORES SIMPLEX VERTICAL SC</option>
                                                    </select>
                                                    <small id="ErrorMenuDistribuidor" class="text-danger"></small>
                                                </div>
                                            </div>'],
                    ["MenuDistribuidorPared",'<div class="col-6" id="ContenedorMenuDistribuidor">
                                                <div class="mb-3">
                                                    <label class="form-label" for="MenuDistribuidor">Distribuidor</label>
                                                    <select class="form-select" id="MenuDistribuidor" onchange="mostrarSeleccion(this)">
                                                        <option value="" selected disabled>Selecciona una opci&oacute;n</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>DISTRIBUIDOR DE PARED 12W</option>
                                                        <option value="12W12ACOPLADORESDUPLEXLC">12W DE 12 ACOPLADORES DUPLEX LC</option>
                                                        <option value="12W6ACOPLADORESSIMPLEXFC-ST">12W DE 6 ACOPLADORES SIMPLEX FC-ST</option>
                                                        <option value="12W8ACOPLADORESFC-ST">12W DE 8 ACOPLADORES FC-ST</option>
                                                        <option value="12W12ACOPLADORESSIMPLEXFC-ST">12W DE 12 ACOPLADORES SIMPLEX FC-ST</option>
                                                        <option value="12W2ACOPLADORESDUPLEXLC">12W DE 2 ACOPLADORES DUPLEX LC</option>
                                                        <option value="12W3ACOPLADORESDUPLEXLC">12W DE 3 ACOPLADORES DUPLEX LC</option>
                                                        <option value="12W6ACOPLADORESDUPLEXLC">12W DE 6 ACOPLADORES DUPLEX LC</option>
                                                        <option value="12W2ACOPLADORESDUPLEXSC">12W DE 2 ACOPLADORES DUPLEX SC</option>
                                                        <option value="12W6ACOPLADORESCUADRUPLEXLC">12W DE 6 ACOPLADORES CUADRUPLEX LC</option>
                                                        <option value="12W6ACOPLADORESDUPLEXSC">12W DE 6 ACOPLADORES DUPLEX SC</option>
                                                        <option value="12W6ACOPLADORESSIMPLEXSC">12W DE 6 ACOPLADORES SIMPLEX SC</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>DISTRIBUIDOR DE PARED 24W PLACA FC-ST SIMPLEX</option>
                                                        <option value="24WDE6ACOPLADORESSIMPLEXFC-ST2mm">24W DE 6 ACOPLADORES SIMPLEX FC-ST. 2mm</option>
                                                        <option value="24W6ACOPLADORESSIMPLEXFC-ST">24W DE 6 ACOPLADORES SIMPLEX FC-ST</option>
                                                        <option value="24W8ACOPLADORESSIMPLEXFC-ST">24W DE 8 ACOPLADORES  SIMPLEX FC-ST</option>
                                                        <option value="24W12ACOPLADORESSIMPLEXFC-ST">24W DE 12 ACOPLADORES  SIMPLEX FC-ST</option>
                                                        <option value="24W24ACOPLADORESSIMPLEXFC-ST">24W DE 24 ACOPLADORES  SIMPLEX FC-ST</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>DISTRIBUIDOR DE PARED 24W PLACA LC DUPLEX</option>
                                                        <option value="24W3ACOPLADORESDUPLEXLC">24W DE 3 ACOPLADORES DUPLEX LC</option>
                                                        <option value="24W6ACOPLADORESDUPLEXLC">24W DE 6 ACOPLADORES DUPLEX LC</option>
                                                        <option value="24W12ACOPLADORESDUPLEXLC">24W DE 12 ACOPLADORES DUPLEX LC</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>DISTRIBUIDOR DE PARED 24W PLACA SC DUPLEX</option>
                                                        <option value="24W3ACOPLADORESDUPLEXSC">24W DE 3 ACOPLADORES DUPLEX SC</option>
                                                        <option value="24W6ACOPLADORESCUADRUPLEXLC">24W DE 6 ACOPLADORES CUADRUPLEX LC</option>
                                                        <option value="24W6ACOPLADORESDUPLEXSC">24W DE 6 ACOPLADORES DUPLEX SC</option>
                                                        <option value="24W12ACOPLADORESDUPLEXSC">24W DE 12 ACOPLADORES DUPLEX SC</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>DISTRIBUIDOR DE PARED 24W PLACA SC SIMPLEX</option>
                                                        <option value="24W6ACOPLADORESSIMPLEXSC">24W DE 6 ACOPLADORES SIMPLEX SC</option>
                                                        <option value="24W12ACOPLADORESSIMPLEXSC">24W DE 12 ACOPLADORES SIMPLEX SC</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>DISTRIBUIDOR DE PARED 24W SLIM</option>
                                                        <option value="24WSLIM12ACOPLADORESDUPLEXHORIZONTALLC">24W SLIM DE 12 ACOPLADORES DUPLEX HORIZONTAL LC</option>
                                                        <option value="24WSLIM24ACOPLADORESSIMPLEXHORIZONTALST">24W SLIM DE 24 ACOPLADORES SIMPLEX HORIZONTAL ST</option>
                                                    </select>
                                                    <small id="ErrorMenuDistribuidor" class="text-danger"></small>
                                                </div>
                                            </div>'],
                    ["MenuDistribuidorExterior",'<div class="col-6" id="ContenedorMenuDistribuidor">
                                                <div class="mb-3">
                                                    <label class="form-label" for="MenuDistribuidor">Distribuidor</label>
                                                    <select class="form-select" id="MenuDistribuidor" onchange="mostrarSeleccion(this)">'.
                                                        '<option value="" selected disabled>Selecciona una opci&oacute;n</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA FC-ST SIMPLEX</option>
                                                        <option value="24ACOPLADORESSIMPLEXFC-ST">24W DE 24 ACOPLADORES SIMPLEX FC-ST</option>
                                                        <option value="8ACOPLADORESSIMPLEXFC-ST">24W DE 8 ACOPLADORES SIMPLEX FC-ST</option>
                                                        <option value="12ACOPLADORESSIMPLEXFC-ST">24W DE 12 ACOPLADORES SIMPLEX FC-ST</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA LC DUPLEX</option>
                                                        <option value="2ACOPLADORESDUPLEXLCUNITARIO">24W DE 2 ACOPLADORES DUPLEX LC UNITARIO</option>
                                                        <option value="2ACOPLADORESDUPLEXLC">24W DE 2 ACOPLADORES DUPLEX LC</option>
                                                        <option value="3ACOPLADORESDUPLEXLC">24W DE 3 ACOPLADORES DUPLEX LC</option>
                                                        <option value="4ACOPLADORESDUPLEXLC">24W DE 4 ACOPLADORES DUPLEX LC</option>
                                                        <option value="6ACOPLADORESDUPLEXLC">24W DE 6 ACOPLADORES DUPLEX LC</option>
                                                        <option value="12ACOPLADORESDUPLEXLC">24W DE 12 ACOPLADORES DUPLEX LC</option>
                                                        <option value="24ACOPLADORESDUPLEXLC">24W DE 24 ACOPLADORES DUPLEX LC</option>
                                                        <option value="24ACOPLADORESCUADRUPLEXLC">24W DE 24 ACOPLADORES CUADRUPLEX LC</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA SC DUPLEX</option>
                                                        <option value="2ACOPLADORESSCDUPLEX">24W DE 2 ACOPLADORES DUPLEX SC</option>
                                                        <option value="24ACOPLADORESDUPLEXSC">24W DE 24 ACOPLADORES DUPLEX SC</option>
                                                        <option value="" style="background-color: #54c8faff; font-weight: bold;" disabled>PLACA SC SIMPLEX</option>
                                                        <option value="24ACOPLADORESSIMPLEXSC">24W DE 24 ACOPLADORES SIMPLEX SC</option>
                                                        '.
                                                    '</select>
                                                    <small id="ErrorMenuDistribuidor" class="text-danger"></small>
                                                </div>
                                            </div>'],
                    ["MenuDistribuidorRielDin",'<div class="col-6" id="ContenedorMenuDistribuidor">
                                                <div class="mb-3">
                                                    <label class="form-label" for="MenuDistribuidor">Distribuidor</label>
                                                    <select class="form-select" id="MenuDistribuidor" onchange="mostrarSeleccion(this)">'.
                                                        '<option value="" selected disabled>Selecciona una opci&oacute;n</option>
                                                        <option value="RIELDIN6ACOPLADORESLCDUPLEX">6 ACOPLADORES LC DUPLEX</option>
                                                        '.
                                                    '</select>
                                                    <small id="ErrorMenuDistribuidor" class="text-danger"></small>
                                                </div>
                                            </div>'],
                    ["ColorCable",'<div class="col-6" id="ContenedorColorCable">
                                                <div class="mb-3">
                                                    <label class="form-label" for="ColorCable">Cable</label>
                                                    <select class="form-select" id="ColorCable" onchange="mostrarSeleccion(this)">
                                                        <option value="" selected disabled>Selecciona una opci&oacute;n</option>
                                                        <option value="Ch.1">Blue</option>
                                                        <option value="Ch.2">Orange</option>
                                                    </select>
                                                    <small id="ErrorColorCable" class="text-danger"></small>
                                                </div>
                                            </div>'],
                ];
        $CadenaCampos = '';
        foreach ($CamposRequeridos as $campoBuscado) {
            foreach ($Campos as $campo) {
                if ($campo[0] === $campoBuscado) {
                    $CadenaCampos .= $campo[1]; 
                    break;
                }
            }
        }
        return json_encode(["Menu" => $CadenaCampos,"Titulo"=>$Titulo]);
    }
    public function SemanaFecha($fecha){
        $fecha = Carbon::parse($fecha);
        $Year = $fecha->isoFormat('GG');
        $Year = substr($Year, -2);
        $Week = $fecha->isoWeek();
        return array("Week"=>$Week, "Year"=>$Year);
    }
    public function CodigoEspecial($CodigoCliente,$OrdenVenta,$OrdenFabricacion){
        $CodigoEspecial = "";
        if($this->Nokia == $CodigoCliente OR $this->HuaweiInternacional == $CodigoCliente OR $this->Drai == $CodigoCliente){
            $DatosSAP = $this->funcionesGenerales->EtiquetasDatosSAP($OrdenVenta,$OrdenFabricacion);
            if($this->HuaweiInternacional == $CodigoCliente){
                $CodigoEspecial = $DatosSAP[0]["SubCatNum"];
            }elseif($this->Nokia == $CodigoCliente OR $this->Drai == $CodigoCliente){
                $CodigoEspecial = $DatosSAP[0]["ItemCode"];
            }
        }
        return $CodigoEspecial;
    }
}
