<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\PorcentajePlaneacion;
use App\Models\OrdenFabricacion;
use App\Models\Areas;
use App\Models\Permission;
use App\Models\Partidasof_Areas;
use App\Models\Linea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function GuzzleHttp\json_decode;

class HomeController extends Controller
{ 
    private $AreaEspecialEmpaque=17;
    private $AreaEspecialCorte=2;
    private $AreaEspecialSuministro = 3;
    
    public function index(){   
        $user = Auth::user();
        if ($user->hasPermission('Vista Dashboard')) {
            $user = Auth::user();
            if (!$user || !$user->active) {
                Auth::logout();
                return redirect()->route('login_view')->withErrors(['email' => 'Tu cuenta ha sido desactivada.']);
            }
            // Si el usuario está autenticado y su cuenta está activa
            //$OFDia = $this->Dia();
            //$OFDia = $OFDia->getData();
            $FechaHoy = date('Y-m-d');
            $FechaAyer = date('Y-m-d', strtotime('-1 day'));
            return view('Home', compact('FechaHoy','FechaAyer')); // O la vista que corresponda
        } else {
            return redirect()->route('index.operador');
        }
    }
    public function Dia(){
        //Día de hoy
        $FechaHoy = date('Y-m-d');
        $FechaAyer = date('Y-m-d', strtotime('-1 day'));
        $OFDia = OrdenFabricacion::where('FechaEntrega',$FechaHoy)->get();
        $OFAbierta = OrdenFabricacion::where('FechaEntrega',$FechaHoy)->where('Cerrada',1)->get();
        $OFCerrada = OrdenFabricacion::where('FechaEntrega',$FechaHoy)->where('Cerrada',0)->get();
        $OFAbiertaCant = $OFAbierta->count();
        $OFCerradaCant = $OFCerrada->count();
        $OFAbiertaAyer = OrdenFabricacion::where('FechaEntrega',$FechaAyer)->where('Cerrada',1)->get();
        $OFCerradaAyer = OrdenFabricacion::where('FechaEntrega',$FechaAyer)->where('Cerrada',0)->get();
        $OFAbiertaCantAyer = $OFAbiertaAyer->count();
        $OFCerradaCantAyer = $OFCerradaAyer->count();
        //Orden de Fabricacion
        if($OFAbiertaCantAyer == 0){
            $PorcentajeAvanceA = $OFAbiertaCant*100;
        }else{
            $PorcentajeAvanceA = (($OFAbiertaCant-$OFAbiertaCantAyer)/$OFAbiertaCantAyer)*100;
        }
        if($OFCerradaCantAyer == 0){
            $PorcentajeAvanceC = $OFCerradaCant*100;
        }else{
            $PorcentajeAvanceC = (($OFCerradaCant-$OFCerradaCantAyer)/$OFCerradaCantAyer)*100;
        }
        if($PorcentajeAvanceA>=0){
            $PorcentajeAvanceA = "+".$PorcentajeAvanceA; 
        }else{
            $PorcentajeAvanceA = $PorcentajeAvanceA; 
        }
        if($PorcentajeAvanceC>=0){
            $PorcentajeAvanceC = "+".$PorcentajeAvanceC; 
        }else{
            $PorcentajeAvanceC = $PorcentajeAvanceC; 
        }
        //Estaciones
        $Estaciones = Areas::where('id','!=',1)->where('id','!=',19)->where('id','!=',20)->where('id','!=',21)->get();
        $Programadas = 0;
        $Pendientes = 0;
        $EnProceso = 0;
        $Terminadas = 0;
        $PromedioPieza = 0;
        $PiezasAsignadasLinea = 0;
        $PiezasFaltanteLinea = 0;
        //Asignadas, Pendientes Línea
        foreach ($OFDia as $key1=>$OFD) {
            $OFDP = $OFD->partidasOF->first();
            //Asignadas, Pendientes Línea
            ($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0)?$PiezasAsignadasLinea += 1:$PiezasFaltanteLinea += 1;
            ($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0)?$OFDia[$key1]['LineasOriginal']=$OFDP->Areas()->where('Areas_id', 18)->first()['pivot']->Linea_id: $OFDia[$key1]['LineasOriginal']=0;
        }
        //Estaciones
        foreach($Estaciones as $key => $Estac){
            //Programadas
            $Programadas = 0;
            $Pendientes = 0;
            $EnProceso = 0;
            $Terminadas = 0;
            $TiempoPromedioPieza = 0;
            $CantidadPiezas = 0;
            $CantidadPiezasHora = 0;
            $TiempoTotal = 0;
            $PiezasTiempoTotal = 0;
            $TiempoProductivo = 0;
            $TiempoMuerto = 0;
            foreach ($OFDia as $key => $OFD) {
                //Programadas
                if($OFD->LineasOriginal != 0){
                    $LineasOriginal = Linea::find($OFD->LineasOriginal);
                    $LineasOriginal = array_map('intval', explode(',', $LineasOriginal->AreasPosibles));
                    if($Estac->id == 2){
                        if($OFD->Corte != 0){
                            if (in_array($Estac->id, $LineasOriginal)) {
                                $Programadas+=1;
                            }
                        }
                    }else{
                        if (in_array($Estac->id, $LineasOriginal)) {
                            $Programadas+=1;
                        }
                    }
                }else{
                        if($Estac->id == 2){
                            if($OFD->Corte != 0){
                                $Programadas+=1;
                            }
                        }elseif($Estac->id == 3){
                            $Programadas+=1;
                        }
                }
                //Pendientes y terminadas
                if($OFD->Cerrada == 0){
                    if($Estac->id == 2){
                        if($OFD->Corte == 1){        
                            $Terminadas++;
                        }
                    }else{
                        $Terminadas++;
                    }
                }else{
                    if($Programadas>0){
                        $OFDP = $OFD->partidasOF->first();
                        if($OFDP->Areas()->where('Areas_id', $Estac->id)->COUNT() == 0){
                            if($Estac->id == $this->AreaEspecialCorte){
                                if($OFD->Corte == 1){
                                    $Pendientes += 1;
                                }
                            }elseif($Estac->id == $this->AreaEspecialSuministro){
                                    $Pendientes += 1;
                            }else{
                                if($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0){
                                    $Pendientes += 1;
                                }
                            }
                        }else{
                            if($this->NumeroCompletadas($OFD->OrdenFabricacion,$Estac->id) >= $OFD->CantidadTotal){
                               if($Estac->id == $this->AreaEspecialCorte){
                                    if($OFD->Corte == 1){
                                        $Terminadas += 1;   
                                    }
                                }else{
                                    $Terminadas += 1;
                                }
                            }else{
                                if($Estac->id == $this->AreaEspecialCorte){
                                    if($OFD->Corte == 1){
                                        $EnProceso += 1;   
                                    }
                                }else{
                                    $EnProceso += 1;
                                }
                            }
                        }
                    }
                }
                //Promedio por pieza
                    $TiempoPromedioPiezaArr = $this->TiempoPiezasSegundos($Estac->id,$OFD);
                    if($TiempoPromedioPiezaArr['Tiempo']>0){
                        $CantidadPiezas += $TiempoPromedioPiezaArr['Cantidad'];
                        $TiempoPromedioPieza += $TiempoPromedioPiezaArr['Tiempo'];
                    }
                //Tiempo total por estacion
                    $TiempoTotalArr = $this->TiempoTotal($Estac->id,$OFD);
                    $TiempoTotal += $TiempoTotalArr['Tiempo'];
                    $PiezasTiempoTotal += $TiempoTotalArr['Cantidad'];
                    /*$PartidaInicio = $OFD->PartidasOF->first();
                    $FechaInicio = $PartidaInicio->Areas()->where('Areas_id',$Estac->id)->whereNotNull('FechaComienzo')->orderByPivot('FechaComienzo','asc')->first();
                    $FechaFin = $PartidaInicio->Areas()->where('Areas_id',$Estac->id)->whereNotNull('FechaTermina')->orderByPivot('FechaTermina','desc')->first();
                    if($FechaInicio!="" AND $FechaFin!=""){
                        $ComprobarTotal = 0;
                        $ComprobarTotal = $TiempoTotal += $this->TiempoSegundos($FechaInicio['pivot']->FechaComienzo, $FechaFin['pivot']->FechaTermina);
                        if($ComprobarTotal>0){
                            $PiezasTiempoTotal += $this->NumeroCompletadas($OFD->OrdenFabricacion,$Estac->id);
                        }
                    }*/
            }
            //TiempoTotal estacion
                $TiempoTotal = (($PiezasTiempoTotal==0)?0:$TiempoTotal/$PiezasTiempoTotal);
                $Estac['TiempoTotal'] = $this->Fechas($TiempoTotal);
            //TiempoProductivo estacion
                $TiempoProductivo = (($CantidadPiezas == 0)?0:$TiempoPromedioPieza/$CantidadPiezas);
                $Estac['TiempoProductivo'] = $this->Fechas($TiempoProductivo);
            //TiempoMuerto estacion
                $TiempoMuerto = $TiempoTotal-$TiempoProductivo;
                $Estac['TiempoMuerto'] = $this->Fechas($TiempoMuerto);
            //Parametros Tabla
                $Estac['Programadas'] = $Programadas;
                $Estac['Pendientes'] = $Pendientes;
                $Estac['EnProceso'] = $EnProceso;
                $Estac['Terminadas'] = $Terminadas;
            //Tiempo Promedio por Hora
                $CantidadPiezasHora = $TiempoProductivo;
                $Estac['CantidadPiezasHora'] = ($CantidadPiezasHora > 0)?floor(3600 / $CantidadPiezasHora):"Sin datos";
                $TiempoPromedioPieza = $this->Fechas(($CantidadPiezas == 0)?0:$TiempoPromedioPieza/$CantidadPiezas);
                $Estac['PorcentajeTerminadas'] = round(($Terminadas== 0)?0:($Terminadas/$Programadas) *100,2);
                $Estac['TiempoPromedioPieza'] = $TiempoPromedioPieza;
                $Estac['PorcentajeTiempoTotal'] = ($TiempoTotal>0)?100:0;//$TiempoTotal;
                $TiempoMuerto = ($TiempoTotal > 0) ? ($TiempoProductivo / $TiempoTotal) * 100 : 0;
                $Estac['PorcentajeTiempoProductivo'] =round(($TiempoTotal > 0) ? ($TiempoProductivo / $TiempoTotal) * 100 : 0,2);
                $Estac['PorcentajeTiempoMuerto'] = round(($TiempoTotal > 0) ?100-$TiempoMuerto:0,2);
        }
        $Lineas = Linea::where('id','!=',1)->get();
        return response()->json([
            "OFAbiertaCant" => $OFAbiertaCant,
            "OFCerradaCant" => $OFCerradaCant,
            "PorcentajeAvanceA" => round($PorcentajeAvanceA,2),
            "PorcentajeAvanceC" => round($PorcentajeAvanceC,2),
            "Estaciones" => $Estaciones,
        ]);
    }
    public function Semana(){
        // 1 Semana hacia Atras
        $FechaHoy = date('Y-m-d');
        $FechaSemana = date('Y-m-d', strtotime('-1 week'));
        $FechaSemanaAtras = date('Y-m-d', strtotime('-2 week'));
        $OFSemana = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaSemana,$FechaHoy])->get();
        $OFAbierta = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaSemana,$FechaHoy])->where('Cerrada',1)->get();
        $OFCerrada = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaSemana,$FechaHoy])->where('Cerrada',0)->get();
        $OFAbiertaCant = $OFAbierta->count();
        $OFCerradaCant = $OFCerrada->count();
        $OFAbiertaAyer = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaSemanaAtras,$FechaSemana])->where('Cerrada',1)->get();
        $OFCerradaAyer = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaSemanaAtras,$FechaSemana])->where('Cerrada',0)->get();
        $OFAbiertaCantAyer = $OFAbiertaAyer->count();
        $OFCerradaCantAyer = $OFCerradaAyer->count();
        if($OFAbiertaCantAyer == 0){
            $PorcentajeAvanceA = $OFAbiertaCant*100;
        }else{
            $PorcentajeAvanceA = (($OFAbiertaCant-$OFAbiertaCantAyer)/$OFAbiertaCantAyer)*100;
        }
        if($OFCerradaCantAyer == 0){
            $PorcentajeAvanceC = $OFCerradaCant*100;
        }else{
            $PorcentajeAvanceC = (($OFCerradaCant-$OFCerradaCantAyer)/$OFCerradaCantAyer)*100;
        }
        if($PorcentajeAvanceA>=0){
            $PorcentajeAvanceA = "+".$PorcentajeAvanceA; 
        }else{
            $PorcentajeAvanceA = $PorcentajeAvanceA; 
        }
        if($PorcentajeAvanceC>=0){
            $PorcentajeAvanceC = "+".$PorcentajeAvanceC; 
        }else{
            $PorcentajeAvanceC = $PorcentajeAvanceC; 
        }
        $Estaciones = Areas::where('id','!=',19)->where('id','!=',20)->where('id','!=',21)->get();
        $Programadas = 0;
        $Pendientes = 0;
        $EnProceso = 0;
        $Terminadas = 0;
        $PromedioPieza = 0;
        $PiezasAsignadasLinea = 0;
        $PiezasFaltanteLinea = 0;
        //Asignadas, Pendientes Línea
        foreach ($OFSemana as $key1=>$OFD) {
            $OFDP = $OFD->partidasOF->first();
            //Asignadas, Pendientes Línea
            ($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0)?$PiezasAsignadasLinea += 1:$PiezasFaltanteLinea += 1;
            ($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0)?$OFDia[$key1]['LineasOriginal']=$OFDP->Areas()->where('Areas_id', 18)->first()['pivot']->Linea_id: $OFDia[$key1]['LineasOriginal']=0;
        }
        //Estaciones
        foreach($Estaciones as $key => $Estac){
            //Programadas
            $Programadas = 0;
            $Pendientes = 0;
            $EnProceso = 0;
            $Terminadas = 0;
            $TiempoPromedioPieza = 0;
            $CantidadPiezas = 0;
            $CantidadPiezasHora = 0;
            $TiempoTotal = 0;
            $PiezasTiempoTotal = 0;
            $TiempoProductivo = 0;
            $TiempoMuerto = 0;
            foreach ($OFSemana as $key => $OFD) {
                //Programadas
                if($OFD->LineasOriginal != 0){
                    $LineasOriginal = Linea::find($OFD->LineasOriginal);
                    $LineasOriginal = array_map('intval', explode(',', $LineasOriginal->AreasPosibles));
                    if($Estac->id == 2){
                        if($OFD->Corte != 0){
                            if (in_array($Estac->id, $LineasOriginal)) {
                                $Programadas+=1;
                            }
                        }
                    }else{
                        if (in_array($Estac->id, $LineasOriginal)) {
                            $Programadas+=1;
                        }
                    }
                }else{
                        if($Estac->id == 2){
                            if($OFD->Corte != 0){
                                $Programadas+=1;
                            }
                        }elseif($Estac->id == 3){
                            $Programadas+=1;
                        }
                }
                //Pendientes y terminadas
                if($OFD->Cerrada == 0){
                    if($Estac->id == 2){
                        if($OFD->Corte == 1){        
                            $Terminadas++;
                        }
                    }else{
                        $Terminadas++;
                    }
                }else{
                    if($Programadas>0){
                        $OFDP = $OFD->partidasOF->first();
                        if($OFDP->Areas()->where('Areas_id', $Estac->id)->COUNT() == 0){
                            if($Estac->id == $this->AreaEspecialCorte){
                                if($OFD->Corte == 1){
                                    $Pendientes += 1;
                                }
                            }elseif($Estac->id == $this->AreaEspecialSuministro){
                                    $Pendientes += 1;
                            }else{
                                if($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0){
                                    $Pendientes += 1;
                                }
                            }
                        }else{
                            if($this->NumeroCompletadas($OFD->OrdenFabricacion,$Estac->id) >= $OFD->CantidadTotal){
                               if($Estac->id == $this->AreaEspecialCorte){
                                    if($OFD->Corte == 1){
                                        $Terminadas += 1;   
                                    }
                                }else{
                                    $Terminadas += 1;
                                }
                            }else{
                                if($Estac->id == $this->AreaEspecialCorte){
                                    if($OFD->Corte == 1){
                                        $EnProceso += 1;   
                                    }
                                }else{
                                    $EnProceso += 1;
                                }
                            }
                        }
                    }
                }
                //Promedio por pieza
                    $TiempoPromedioPiezaArr = $this->TiempoPiezasSegundos($Estac->id,$OFD);
                    $CantidadPiezas += $TiempoPromedioPiezaArr['Cantidad'];
                    $TiempoPromedioPieza += $TiempoPromedioPiezaArr['Tiempo'];
                //Tiempo total por estacion
                    $PartidaInicio = $OFD->PartidasOF->first();
                    $FechaInicio = $PartidaInicio->Areas()->where('Areas_id',$Estac->id)->whereNotNull('FechaComienzo')->orderByPivot('FechaComienzo','asc')->first();
                    $FechaFin = $PartidaInicio->Areas()->where('Areas_id',$Estac->id)->whereNotNull('FechaTermina')->orderByPivot('FechaTermina','desc')->first();
                    if($FechaInicio!="" AND $FechaFin!=""){
                        if($Estac->id == 5){
                            //return $FechaInicio;//.$FechaFin; 
                        }
                        $TiempoTotal += $this->TiempoSegundos($FechaInicio['pivot']->FechaComienzo, $FechaFin['pivot']->FechaTermina);
                        if($this->TiempoSegundos($FechaInicio['pivot']->FechaComienzo, $FechaFin['pivot']->FechaTermina)>0){
                            $PiezasTiempoTotal += $this->NumeroCompletadas($OFD->OrdenFabricacion,$Estac->id);
                            //echo$FechaInicio['pivot']->FechaComienzo."     ".$FechaFin['pivot']->FechaTermina."     ".$Estac->id."     ".$TiempoTotal."     ".$PiezasTiempoTotal."||||||||||||||";
                        }
                    }
            }
            $TiempoTotal = ($PiezasTiempoTotal==0)?0:$TiempoTotal/$PiezasTiempoTotal;
            //TiempoProductivo estacion
            $TiempoProductivo = (($CantidadPiezas == 0)?0:$TiempoPromedioPieza/$CantidadPiezas);
            $CantidadPiezasHora = ($CantidadPiezas == 0)?0:$TiempoPromedioPieza/$CantidadPiezas;
            $TiempoPromedioPieza = $this->Fechas(($CantidadPiezas == 0)?0:$TiempoPromedioPieza/$CantidadPiezas);
            $Estac['PorcentajeTerminadas'] = round(($Terminadas== 0)?0:($Terminadas/$Programadas) *100,2);
            $Estac['Programadas'] = $Programadas;
            $Estac['Pendientes'] = $Pendientes;
            $Estac['EnProceso'] = $EnProceso;
            $Estac['Terminadas'] = $Terminadas;
            $Estac['TiempoPromedioPieza'] = $TiempoPromedioPieza;
            $Estac['CantidadPiezasHora'] = ($CantidadPiezasHora > 0)?floor(3600 / $CantidadPiezasHora):"Sin datos";
            $Estac['PorcentajeTiempoTotal'] = ($TiempoTotal>0)?100:0;//$TiempoTotal;
            $TiempoMuerto = ($TiempoTotal > 0) ? ($TiempoProductivo / $TiempoTotal) * 100 : 0;
            $Estac['PorcentajeTiempoProductivo'] =($TiempoTotal > 0) ? ($TiempoProductivo / $TiempoTotal) * 100 : 0;
            $Estac['PorcentajeTiempoMuerto'] = ($TiempoTotal > 0) ?100-$TiempoMuerto:0;
            $Estac['TiempoTotal'] = $this->Fechas($TiempoTotal);
            $Estac['TiempoProductivo'] = $this->Fechas($TiempoProductivo);
            $Estac['TiempoMuerto'] = $this->Fechas($TiempoTotal-$TiempoProductivo);
        }
        $Lineas = Linea::where('id','!=',1)->get();
        return response()->json([
            "OFAbiertaCant" => $OFAbiertaCant,
            "OFCerradaCant" => $OFCerradaCant,
            "PorcentajeAvanceA" => round($PorcentajeAvanceA,2),
            "PorcentajeAvanceC" => round($PorcentajeAvanceC,2),
            "Estaciones" => $Estaciones,
        ]);
    }
    public function Mes(){
        //Este mes
        $FechaMesInicio = date('Y-m-01');
        $FechaMes = date('Y-m-d', strtotime('-1 month'));
        $FechaMesAtras = date('Y-m-d', strtotime('-2 month'));
        $OFMes = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaMes,$FechaMesInicio])->get();
        $OFAbierta = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaMes,$FechaMesInicio])->where('Cerrada',1)->get();
        $OFCerrada = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaMes,$FechaMesInicio])->where('Cerrada',0)->get();
        $OFAbiertaCant = $OFAbierta->count();
        $OFCerradaCant = $OFCerrada->count();
        $OFAbiertaAyer = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaMesAtras,$FechaMes])->where('Cerrada',1)->get();
        $OFCerradaAyer = OrdenFabricacion::whereBetween('FechaEntrega', [$FechaMesAtras,$FechaMes])->where('Cerrada',0)->get();
        $OFAbiertaCantAyer = $OFAbiertaAyer->count();
        $OFCerradaCantAyer = $OFCerradaAyer->count();
        if($OFAbiertaCantAyer == 0){
            $PorcentajeAvanceA = $OFAbiertaCant*100;
        }else{
            $PorcentajeAvanceA = (($OFAbiertaCant-$OFAbiertaCantAyer)/$OFAbiertaCantAyer)*100;
        }
        if($OFCerradaCantAyer == 0){
            $PorcentajeAvanceC = $OFCerradaCant*100;
        }else{
            $PorcentajeAvanceC = (($OFCerradaCant-$OFCerradaCantAyer)/$OFCerradaCantAyer)*100;
        }
        if($PorcentajeAvanceA>=0){
            $PorcentajeAvanceA = "+".$PorcentajeAvanceA; 
        }else{
            $PorcentajeAvanceA = $PorcentajeAvanceA; 
        }
        if($PorcentajeAvanceC>=0){
            $PorcentajeAvanceC = "+".$PorcentajeAvanceC; 
        }else{
            $PorcentajeAvanceC = $PorcentajeAvanceC; 
        }
        $Estaciones = Areas::where('id','!=',19)->where('id','!=',20)->where('id','!=',21)->get();
        $Programadas = 0;
        $Pendientes = 0;
        $EnProceso = 0;
        $Terminadas = 0;
        $PromedioPieza = 0;
        $PiezasAsignadasLinea = 0;
        $PiezasFaltanteLinea = 0;
        //Asignadas, Pendientes Línea
        foreach ($OFMes as $key1=>$OFD) {
            $OFDP = $OFD->partidasOF->first();
            //Asignadas, Pendientes Línea
            ($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0)?$PiezasAsignadasLinea += 1:$PiezasFaltanteLinea += 1;
            ($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0)?$OFDia[$key1]['LineasOriginal']=$OFDP->Areas()->where('Areas_id', 18)->first()['pivot']->Linea_id: $OFDia[$key1]['LineasOriginal']=0;
        }
        //Estaciones
        foreach($Estaciones as $key => $Estac){
            //Programadas
            $Programadas = 0;
            $Pendientes = 0;
            $EnProceso = 0;
            $Terminadas = 0;
            $TiempoPromedioPieza = 0;
            $CantidadPiezas = 0;
            $CantidadPiezasHora = 0;
            $TiempoTotal = 0;
            $PiezasTiempoTotal = 0;
            $TiempoProductivo = 0;
            $TiempoMuerto = 0;
            foreach ($OFMes as $key => $OFD) {
                //Programadas
                if($OFD->LineasOriginal != 0){
                    $LineasOriginal = Linea::find($OFD->LineasOriginal);
                    $LineasOriginal = array_map('intval', explode(',', $LineasOriginal->AreasPosibles));
                    if($Estac->id == 2){
                        if($OFD->Corte != 0){
                            if (in_array($Estac->id, $LineasOriginal)) {
                                $Programadas+=1;
                            }
                        }
                    }else{
                        if (in_array($Estac->id, $LineasOriginal)) {
                            $Programadas+=1;
                        }
                    }
                }else{
                        if($Estac->id == 2){
                            if($OFD->Corte != 0){
                                $Programadas+=1;
                            }
                        }elseif($Estac->id == 3){
                            $Programadas+=1;
                        }
                }
                //Pendientes y terminadas
                if($OFD->Cerrada == 0){
                    if($Estac->id == 2){
                        if($OFD->Corte == 1){        
                            $Terminadas++;
                        }
                    }else{
                        $Terminadas++;
                    }
                }else{
                    if($Programadas>0){
                        $OFDP = $OFD->partidasOF->first();
                        if($OFDP->Areas()->where('Areas_id', $Estac->id)->COUNT() == 0){
                            if($Estac->id == $this->AreaEspecialCorte){
                                if($OFD->Corte == 1){
                                    $Pendientes += 1;
                                }
                            }elseif($Estac->id == $this->AreaEspecialSuministro){
                                    $Pendientes += 1;
                            }else{
                                if($OFDP->Areas()->where('Areas_id', 18)->COUNT() > 0){
                                    $Pendientes += 1;
                                }
                            }
                        }else{
                            if($this->NumeroCompletadas($OFD->OrdenFabricacion,$Estac->id) >= $OFD->CantidadTotal){
                               if($Estac->id == $this->AreaEspecialCorte){
                                    if($OFD->Corte == 1){
                                        $Terminadas += 1;   
                                    }
                                }else{
                                    $Terminadas += 1;
                                }
                            }else{
                                if($Estac->id == $this->AreaEspecialCorte){
                                    if($OFD->Corte == 1){
                                        $EnProceso += 1;   
                                    }
                                }else{
                                    $EnProceso += 1;
                                }
                            }
                        }
                    }
                }
                //Promedio por pieza
                    $TiempoPromedioPiezaArr = $this->TiempoPiezasSegundos($Estac->id,$OFD);
                    $CantidadPiezas += $TiempoPromedioPiezaArr['Cantidad'];
                    $TiempoPromedioPieza += $TiempoPromedioPiezaArr['Tiempo'];
                //Tiempo total por estacion
                    $PartidaInicio = $OFD->PartidasOF->first();
                    $FechaInicio = $PartidaInicio->Areas()->where('Areas_id',$Estac->id)->whereNotNull('FechaComienzo')->orderByPivot('FechaComienzo','asc')->first();
                    $FechaFin = $PartidaInicio->Areas()->where('Areas_id',$Estac->id)->whereNotNull('FechaTermina')->orderByPivot('FechaTermina','desc')->first();
                    if($FechaInicio!="" AND $FechaFin!=""){
                        if($Estac->id == 5){
                            //return $FechaInicio;//.$FechaFin; 
                        }
                        $TiempoTotal += $this->TiempoSegundos($FechaInicio['pivot']->FechaComienzo, $FechaFin['pivot']->FechaTermina);
                        if($this->TiempoSegundos($FechaInicio['pivot']->FechaComienzo, $FechaFin['pivot']->FechaTermina)>0){
                            $PiezasTiempoTotal += $this->NumeroCompletadas($OFD->OrdenFabricacion,$Estac->id);
                            //echo$FechaInicio['pivot']->FechaComienzo."     ".$FechaFin['pivot']->FechaTermina."     ".$Estac->id."     ".$TiempoTotal."     ".$PiezasTiempoTotal."||||||||||||||";
                        }
                    }
            }
            $TiempoTotal = ($PiezasTiempoTotal==0)?0:$TiempoTotal/$PiezasTiempoTotal;
            //TiempoProductivo estacion
            $TiempoProductivo = (($CantidadPiezas == 0)?0:$TiempoPromedioPieza/$CantidadPiezas);
            $CantidadPiezasHora = ($CantidadPiezas == 0)?0:$TiempoPromedioPieza/$CantidadPiezas;
            $TiempoPromedioPieza = $this->Fechas(($CantidadPiezas == 0)?0:$TiempoPromedioPieza/$CantidadPiezas);
            $Estac['PorcentajeTerminadas'] = round(($Terminadas== 0)?0:($Terminadas/$Programadas) *100,2);
            $Estac['Programadas'] = $Programadas;
            $Estac['Pendientes'] = $Pendientes;
            $Estac['EnProceso'] = $EnProceso;
            $Estac['Terminadas'] = $Terminadas;
            $Estac['TiempoPromedioPieza'] = $TiempoPromedioPieza;
            $Estac['CantidadPiezasHora'] = ($CantidadPiezasHora > 0)?floor(3600 / $CantidadPiezasHora):"Sin datos";
            $Estac['PorcentajeTiempoTotal'] = ($TiempoTotal>0)?100:0;//$TiempoTotal;
            $TiempoMuerto = ($TiempoTotal > 0) ? ($TiempoProductivo / $TiempoTotal) * 100 : 0;
            $Estac['PorcentajeTiempoProductivo'] =($TiempoTotal > 0) ? ($TiempoProductivo / $TiempoTotal) * 100 : 0;
            $Estac['PorcentajeTiempoMuerto'] = ($TiempoTotal > 0) ?100-$TiempoMuerto:0;
            $Estac['TiempoTotal'] = $this->Fechas($TiempoTotal);
            $Estac['TiempoProductivo'] = $this->Fechas($TiempoProductivo);
            $Estac['TiempoMuerto'] = $this->Fechas($TiempoTotal-$TiempoProductivo);
        }
        $Lineas = Linea::where('id','!=',1)->get();
        return response()->json([
            "OFAbiertaCant" => $OFAbiertaCant,
            "OFCerradaCant" => $OFCerradaCant,
            "PorcentajeAvanceA" => round($PorcentajeAvanceA,2),
            "PorcentajeAvanceC" => round($PorcentajeAvanceC,2),
            "Estaciones" => $Estaciones,
        ]);
    }
    public function DashboardPrincipal(Request $request){
        $LapsoTiempo = $request->LapsoTiempo;
        $FechaInicio = $request->FechaInicio;
        $FechaFin = $request->FechaFin;
        if($LapsoTiempo == 'D'){
            return $this-> Dia();
        }else if($LapsoTiempo == 'S'){
            return $this-> Semana();
        }else{
            return $this-> Mes();
        }

    }
    public function TiempoPiezasSegundos($IdArea,$OrdenFabricacion){
        //Dependiendo del tipo de escaner se toma el tiempo
        $TiempoProductivoEstacion = 0;
        $PartidasOF = $OrdenFabricacion->PartidasOF->first();
        $PiezasCantidad = 0;
        if($OrdenFabricacion->Escaner == 1){
            $TiempoProductivoEstacionAreas = $PartidasOF->Areas()->whereNotNull('FechaTermina')->where('Areas_id',$IdArea)->get();
            $TiempoProductivoEstacion = 0;
            foreach($TiempoProductivoEstacionAreas as $TPEA){
                $PiezasCantidad += $TPEA['pivot']->Cantidad;
                $FechaPrimera=Carbon::parse($TPEA['pivot']->FechaComienzo);
                $FechaTermina=Carbon::parse($TPEA['pivot']->FechaTermina);
                $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
            }
        }else{
            $TiempoProductivoEstacion=0;
            $TiempoProductivoEstacions=0;
            if($IdArea == 2 || $IdArea == 3){
                $TiempoProductivoEstacionAreas = $PartidasOF->Areas()->whereNotNull('FechaTermina')->where('Areas_id',$IdArea)->get();
                foreach($TiempoProductivoEstacionAreas as $TPEA){
                    $PiezasCantidad += $TPEA['pivot']->Cantidad;
                    $FechaPrimera=Carbon::parse($TPEA['pivot']->FechaComienzo);
                    $FechaTermina=Carbon::parse($TPEA['pivot']->FechaTermina);
                    $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                }
            }else{
                $TiempoProductivoEstacionAreasA = $PartidasOF->Areas()->where('TipoPartida','!=','F')->where('Areas_id',$IdArea)->orderBy('Linea_id')->get();
                $TiempoProductivoEstacionAreasF= $PartidasOF->Areas()->where('TipoPartida','=','F')->where('Areas_id',$IdArea)->orderBy('Linea_id')->get();
                foreach($TiempoProductivoEstacionAreasA as $keyA => $TPEAA){
                    foreach($TiempoProductivoEstacionAreasF as $keyF => $TPEAF){
                        if($TPEAA['pivot']->Linea_id != $TPEAF['pivot']->Linea_id){
                            unset($TiempoProductivoEstacionAreasA[$keyA]);
                        }else{
                            $PiezasCantidad += $TPEAF['pivot']->Cantidad;
                            $CantidadA=$TPEAA['pivot']->Cantidad;
                            $CantidadF=$TPEAF['pivot']->Cantidad;
                            if($CantidadA == $CantidadF){
                                $FechaPrimera=Carbon::parse($TPEAA['pivot']->FechaComienzo);
                                $FechaTermina=Carbon::parse($TPEAF['pivot']->FechaTermina);
                                $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                unset($TiempoProductivoEstacionAreasF[$keyF]);
                                break;
                            }elseif($CantidadA > $CantidadF){
                                $FechaPrimera=Carbon::parse($TPEAA['pivot']->FechaComienzo);
                                $FechaTermina=Carbon::parse($TPEAF['pivot']->FechaTermina);
                                $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                $TPEAA['pivot']->Cantidad -= $CantidadF;
                                unset($TiempoProductivoEstacionAreasF[$keyF]);
                            }else{
                                $FechaPrimera=Carbon::parse($TPEAA['pivot']->FechaComienzo);
                                $FechaTermina=Carbon::parse($TPEAF['pivot']->FechaTermina);
                                $TiempoProductivoEstacion+=$FechaPrimera->diffInSeconds($FechaTermina);
                                $TPEAF['pivot']->Cantidad -= $CantidadA;
                                unset($TiempoProductivoEstacionAreasA[$keyA]);
                                break;
                            }
                        }
                    }
                }
            }
        }
        return [
        'Tiempo' => $TiempoProductivoEstacion,
        'Cantidad' => $PiezasCantidad,
        ];
    }
    public function Fechas($TiempoTotalEstacion){
        $horas = floor($TiempoTotalEstacion / 3600);
        $minutos = floor(($TiempoTotalEstacion % 3600) / 60);
        $segundos = $TiempoTotalEstacion % 60;
        if($horas!=0){$TiempoTotalEstacion = sprintf("%02d h %02d m %02d s ", $horas, $minutos, $segundos);}
        elseif($minutos!=0){$TiempoTotalEstacion = sprintf("%02d m %02d s ", $minutos, $segundos);}
        elseif($segundos!=0){$TiempoTotalEstacion = sprintf("%02d s",$segundos);}
        else{$TiempoTotalEstacion = "Sin datos";}
        return $TiempoTotalEstacion;
    }
    public function TiempoSegundos($FechaInicio, $FechaFin){
        $inicio = Carbon::parse($FechaInicio);
        $fin = Carbon::parse($FechaFin);
        if ($inicio > $fin) {
            return 0;
        }
        return $inicio->diffInSeconds($fin);
    }
    public function TiempoTotal($IdArea,$OrdenFabricacion){
        $PartidaOF = $OrdenFabricacion->PartidasOF->first();
        $Partidasof_Areas = Partidasof_Areas::where('Areas_id',$IdArea)->whereNotNull('FechaTermina')->where('PartidasOF_id',$PartidaOF->id)->orderBy('Linea_id', 'asc')->get();
        $Cantidad = 0;
        $TiempoSegundos = 0;
        //Si es igual a 2 o 3 no imparta el tipo de escaner
        if($IdArea == $this->AreaEspecialCorte){
            if($OrdenFabricacion->Corte == 1){
                foreach($Partidasof_Areas as $POF_A){
                    $Tiempo = $this->TiempoSegundos($OrdenFabricacion->created_at,$POF_A->FechaTermina);
                    if($Tiempo >0){
                        $TiempoSegundos += $Tiempo;
                        $Cantidad += $POF_A->Cantidad;
                    }
                }
            }else{
                $TiempoSegundos = 0;
            }
        }else if($IdArea == $this->AreaEspecialSuministro){
            foreach($Partidasof_Areas as $POF_A){
                    $Tiempo = $this->TiempoSegundos($OrdenFabricacion->created_at,$POF_A->FechaTermina);
                    if($Tiempo >0){
                        $TiempoSegundos += $Tiempo;
                        $Cantidad += $POF_A->Cantidad;
                    }
                }
        }else{
            if($OrdenFabricacion->Escaner == 1){
                $EstacionAnterior = $this->EstacionAnterior($OrdenFabricacion,$IdArea);
            }else{
                $EstacionAnterior = $this->EstacionAnterior($OrdenFabricacion,$IdArea);
            }
        }
        /*if($OrdenFabricacion->Escaner == 1 || $IdArea == $this->AreaEspecialCorte || $IdArea == $this->AreaEspecialSuministro ){
            $Partidasof_Areas = Partidasof_Areas::where('Areas_id',$IdArea)->whereNotNull('FechaTermina')->where('PartidasOF_id',$PartidaOF->id)->orderBy('Linea_id', 'asc')->get();
            foreach($Partidasof_Areas as $key => $PA){
                if(($key+1) < $Partidasof_Areas->count()){
                    if($PA->Linea_id == $Partidasof_Areas[$key+1]->Linea_id){
                        $FechaInicio = $PA->Fechatermina;
                        $FechaFin = $Partidasof_Areas[$key+1]->Fechacomienzo;
                        return 
                        $TiempoSegundos += $this->TiempoSegundos($FechaInicio, $FechaFin);
                    }
                }
            }
        }else{

        }*/
        return [
        'Tiempo' => $TiempoSegundos,
        'Cantidad' => $Cantidad,
        ];;
    }
    public function NumeroCompletadas($OrdenFabricacion,$Area){
        $OrdenFabricacion=OrdenFabricacion::where('OrdenFabricacion',$OrdenFabricacion)->first();
        $PartidasOF=$OrdenFabricacion->PartidasOF;
        $Suma=0;
        if($OrdenFabricacion->Escaner == 1 OR $Area==2 OR $Area==3){
            foreach($PartidasOF as $Partidas){
                $PartidasOFAreasCompN=$Partidas->Areas()->where('Areas_id',$Area)->whereNotNull('FechaTermina')->where('TipoPartida','N')->SUM('Cantidad');
                $PartidasOFAreasCompRI=$Partidas->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('TipoPartida','R')->SUM('Cantidad');
                $Suma+=$PartidasOFAreasCompN-$PartidasOFAreasCompRI;
            }
        }else{
            if($Area!=$this->AreaEspecialEmpaque ){
                foreach($PartidasOF as $Partidas){
                    $Suma +=$Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida','N')->get()->SUM('pivot.Cantidad')-
                            ($Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida','!=','F')->get()->SUM('pivot.Cantidad')
                           - $Partidas->Areas()->where('Areas_id', $Area)->where('TipoPartida','F')->get()->SUM('pivot.Cantidad'));
                }
            }else{
                foreach($PartidasOF as $Partidas){
                    $PartidasOFAreasCompN=$Partidas->Areas()->where('Areas_id',$Area)->whereNotNull('FechaTermina')->where('TipoPartida','N')->SUM('Cantidad');
                    $PartidasOFAreasCompRI=$Partidas->Areas()->where('Areas_id',$Area)->whereNull('FechaTermina')->where('TipoPartida','R')->SUM('Cantidad');
                    $Suma+=$PartidasOFAreasCompN-$PartidasOFAreasCompRI;
                }
            }
        }
        return $Suma;
    }
    public function EstacionAnterior($OrdenFabricacion,$Area){

    }
    //vista sin permisos
    public function error(Request $request)
    {
        return view('Error');
    }
    public function lineas(Request $request)
    {
        $personal = DB::table('porcentajeplaneacion')
            ->join('linea', 'porcentajeplaneacion.Linea_id', '=', 'linea.id')
            ->whereDate('porcentajeplaneacion.FechaPlaneacion', today())
            ->where('linea.active', 1)
            ->select('linea.id as LineaId', 'linea.Nombre', 'porcentajeplaneacion.NumeroPersonas', 'porcentajeplaneacion.CantidadPlaneada')
            ->get();

        $TotalOfTotal = DB::table('porcentajeplaneacion')
            ->join('linea', 'porcentajeplaneacion.Linea_id', '=', 'linea.id')
            ->whereDate('FechaPlaneacion', today())
            ->where('linea.active', 1)
            ->select(
                'Linea_id',
                DB::raw('COALESCE(NumeroPersonas, 20) as NumeroPersonas'),
                DB::raw('COALESCE(CantidadPlaneada, 100) as CantidadTotal')
            )
            ->get();

        $indicador = DB::table('ordenfabricacion')
            ->join('partidasof', 'ordenfabricacion.id', '=', 'partidasof.OrdenFabricacion_id')
            ->join('partidasof_areas', 'partidasof.id', '=', 'partidasof_areas.PartidasOF_id')
            ->whereDate('ordenfabricacion.FechaEntrega', today())
            ->where('partidasof_areas.Areas_id', 9)
            ->select(
                'ordenfabricacion.Linea_id',
                'OrdenFabricacion',
                'OrdenVenta_id',
                'partidasof_areas.Cantidad',
                'ordenfabricacion.Cerrada',
                'partidasof_areas.Areas_id',
                DB::raw('COALESCE(SUM(partidasof_areas.Cantidad), 0) as SumaCantidad')
            )
            ->groupBy('OrdenFabricacion', 'OrdenVenta_id', 'partidasof_areas.Cantidad', 'ordenfabricacion.Cerrada', 'partidasof_areas.Areas_id', 'ordenfabricacion.Linea_id')
            ->get();

        $lineas = DB::table('linea')
            ->where('active', 1)
            ->get();

        $totalOFcompletadas = 0;
        $totalSumaCantidad = 0;
        $totalFaltante = 0;
        $lineasData = [];

        foreach ($lineas as $linea) {
            $lineaId = $linea->id;
            $personalLinea = $personal->firstWhere('LineaId', $lineaId);
            $cantidadPersonas = $personalLinea->NumeroPersonas ?? 0;
            $cantidadPlaneada = $personalLinea->CantidadPlaneada ?? 0;

            $indicadorLinea = $indicador->where('Linea_id', $lineaId);
            $totalOFcompletadasLinea = $indicadorLinea->where('Cerrada', 1)->sum('SumaCantidad') ?? 0;
            $totalSumaCantidadLinea = $indicadorLinea->sum('SumaCantidad') ?? 0;
            $faltanteTotalLinea = max(($cantidadPlaneada - $totalSumaCantidadLinea), 0);

            $totalOFcompletadas += $totalOFcompletadasLinea;
            $totalSumaCantidad += $totalSumaCantidadLinea;
            $totalFaltante += $faltanteTotalLinea;

            $lineasData[] = [
                'id' => $lineaId,
                'cantidad_personas' => $cantidadPersonas,
                'estimado_piezas' => $cantidadPlaneada,
                'piezas_completadas' => $totalOFcompletadasLinea,
                'piezas_faltantes' => $faltanteTotalLinea,
                'porcentaje_completadas' => $cantidadPlaneada > 0 ? round(($totalOFcompletadasLinea / $cantidadPlaneada) * 100, 2) : 0,
                'porcentaje_faltantes' => $cantidadPlaneada > 0 ? round(($faltanteTotalLinea / $cantidadPlaneada) * 100, 2) : 0
            ];
        }

        $porcentajeCompletadas = $totalSumaCantidad > 0 ? round(($totalOFcompletadas / $totalSumaCantidad) * 100, 2) : 0;
        $porcentajeCerradas = $totalSumaCantidad > 0 ? round(($totalFaltante / $totalSumaCantidad) * 100, 2) : 0;

        return response()->json([
            'lineas' => $lineasData,
            'TotalOFcompletadas' => $totalOFcompletadas,
            'TotalOfTotal' => (int) $totalSumaCantidad,
            'faltanteTotal' => $totalFaltante,
            'PorcentajeCompletadas' => $porcentajeCompletadas,
            'porcentajeCerradas' => $porcentajeCerradas
        ]);
    }
    //dasboard operador 
    public function indexoperador(Request $request){
        //$manana = Carbon::now()->addDay()->format('Y-m-d H:i:s'); 
        $Inicio = Carbon::now()->format('Y-m-d H:i:s');
        $Fin = Carbon::now()->format('Y-m-d H:i:s');
        $Avisos = DB::table('avisos')
                        ->where('FechaInicio','<=',$Inicio)
                        ->where('FechaFin','>=',$Fin)
                        ->get();
        $user = Auth::user();
        if (!$user || !$user->active) {
            Auth::logout();
            return redirect()->route('login_view')->withErrors(['email' => 'Tu cuenta ha sido desactivada.']);
        }
        return view('HomeOperador', compact('user', 'Avisos'));
    }
    //Guardar aviso
    public function guardarAviso(Request $request){
        // Valida la entrada del usuario
        $rules = [
            'Titulo'      => 'max:255',
            'FechaInicio' => 'required',
            'FechaFin'    => 'required|after_or_equal:FechaInicio',
            'Aviso'       => 'required|string|min:1',
        ];

        $messages = [
            //'Titulo.required'      => '*Campo título es obligatoria.',
            'Titulo.max'           => '*Máximo :max caracteres.',
            'FechaInicio.required' => '*Campo Fecha Inicio es obligatoria.',
            'FechaInicio.date'     => '*Campo Fecha Inicio no es válida.',
            'FechaFin.required'    => '*Campo Fecha Fin es obligatoria.',
            'FechaFin.date'        => '*Campo Fecha Fin no es válida.',
            'FechaFin.after_or_equal' => '*Campo Fecha Fin debe ser mayor o igual a Fecha Inicio.',
            'Aviso.required'       => '*Campo aviso no puede estar vacío.',
            'Aviso.min'            => '*Campo aviso requiere minimo :min caracteres.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $Html  = 'no';
        if ($request->has('CodigoHtml')) {
            $Html  = 'si';
        }
        // Inserta el aviso en la base de datos
        DB::table('avisos')->insert([
            'Titulo' => $request->input('Titulo'),
            'Contenido' => $request->input('Aviso'),
            'FechaInicio' => $request->input('FechaInicio'),
            'FechaFin' => $request->input('FechaFin'),
            'Html' => $Html,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirige con mensaje de éxito
        return redirect()->back()->with('success', 'Aviso enviado correctamente.');
    }
    //Revisa la sesión y la cooki de sesion
    public function UpdateSession(){
        if(session()->get('inicio_sesion')){
            Session::put('last_activity', now());
            //Session::regenerate();
        }
        return session()->get('inicio_sesion');
        //return response()->json(['isValid' => Auth::check()]);
    }
    //Manuales de Usuario
    public function ManualesUsuario(){
        $user = Auth::user();
            if (!$user || !$user->active) {
                Auth::logout();
                return redirect()->route('login_view')->withErrors(['email' => 'Tu cuenta ha sido desactivada.']);
            }
            return view('ManualesUsuario.ManualesUsuario'); // O la vista que corresponda
    }
    public function MostrarManual($manual){
        $path = storage_path('app/public/Manuales/Estaciones/'.$manual.'_TRAZABILIDAD.pdf');
        if (!file_exists($path)) {
            abort(404, 'El archivo no fue encontrado');
        }
        return response()->file($path);
    }

}

    
    
 