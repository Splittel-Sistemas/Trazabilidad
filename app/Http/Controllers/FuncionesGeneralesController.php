<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenVenta;
use App\Models\Logs;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class FuncionesGeneralesController extends Controller
{
    private $connection;

    public function checkSapConnection()
    {
        try {
            
            $this->connectToSap();
            return true;  
        } catch (\Exception $e) {
            
            return false;
        }
    }
    private function connectToSap()
    {
        $datasource = 'DRIVER=HDBODBC;SERVERNODE=192.168.2.19:30015;CHAR_AS_UTF8=1;';
        $username   = "USR_LECTURA";
        $password   = "SPL.Lectura202xx7.";

        
        $conn = odbc_connect($datasource, $username, $password);

        if (!$conn) {
            
            throw new \Exception("Error de conexión a SAP: " . odbc_errormsg());
        }

        $this->connection = $conn;
    }
    public function ejecutarConsulta($sql)
    {
        $this->connectToSap();
        if (!$this->connection) {
            throw new \Exception("No se ha establecido conexión a SAP.");
        }
        $result = odbc_exec($this->connection, $sql);
        if (!$result) {
            throw new \Exception("Error al ejecutar la consulta: " . odbc_errormsg());
        }
        $datos = [];
        while ($row = odbc_fetch_array($result)) {
            $datos[] = $row;
        }  
        odbc_close($this->connection);
        return $datos;
    }
    public function obtenerDatosDeSap(){
       
        $sql = 'SELECT * FROM HN_OPTRONICS.OCRD';
        return $this->ejecutarConsulta($sql);
    }
    public function cerrarConexion(){
        if ($this->connection) {
            odbc_close($this->connection);  
        }
    }
    //Funcion para encriptar
    public function encrypt($dato){
        $encrypted = Crypt::encrypt($dato);
        return $encrypted;
    }
    //funcion para desencriptar
    public function decrypt($dato){
        $encrypted = Crypt::decrypt($dato);
        return $encrypted;
    }
    //retorna el id del usuario
    public function InfoUsuario(){
        if (auth()->check()) {
        $usuario = auth()->user();
        return$usuario->id;
        }else{
            return redirect()->route('login');
        }
    }
    public function Linea(){
        return 1;
    }
    public function Emisiones($OrdenFabricacion){
        $OrdenFabricacion;
        $schema = 'HN_OPTRONICS';
        /*$query_emisiones="SELECT T00.\"DocNum\" \"NoEmision\", T00.\"DocDate\" \"FechaEmision\", T111.\"ItemCode\" \"Componente\", T111.\"Dscription\" \"Descripcion\",
                            T111.\"Quantity\" \"Cantidad\", T111.\"WhsCode\" \"Almacen\"*/
        $query_emisiones="SELECT DISTINCT T00.\"DocNum\" \"NoEmision\", TO_DATE(T00.\"DocDate\") \"FechaEmision\", T00.\"Ref2\" \"Cantidad\"                       
                        FROM {$schema}.\"OIGE\" T00
                        LEFT JOIN {$schema}.\"IGE1\" T111 ON T00.\"DocEntry\" = T111.\"DocEntry\"
                        LEFT JOIN {$schema}.\"OWOR\" T222 ON T111.\"BaseEntry\" = T222.\"DocEntry\" AND T111.\"BaseType\" = T222.\"ObjType\"
                        LEFT JOIN {$schema}.\"WOR1\" T333 ON T222.\"DocEntry\" = T333.\"DocEntry\" AND T111.\"BaseLine\" = T333.\"LineNum\"
                        WHERE T222.\"DocNum\" = ".$OrdenFabricacion."
                        ORDER BY 1";
        return$emisiones=$this->ejecutarConsulta($query_emisiones);
    }
    public function EmisioneFiltro($OrdenFabricacion,$NumeroEmision){
        $OrdenFabricacion;
        $schema = 'HN_OPTRONICS';
        /*$query_emisiones="SELECT T00.\"DocNum\" \"NoEmision\", T00.\"DocDate\" \"FechaEmision\", T111.\"ItemCode\" \"Componente\", T111.\"Dscription\" \"Descripcion\",
                            T111.\"Quantity\" \"Cantidad\", T111.\"WhsCode\" \"Almacen\"*/
        $query_emisiones="SELECT DISTINCT T00.\"DocNum\" \"NoEmision\", TO_DATE(T00.\"DocDate\") \"FechaEmision\", T00.\"Ref2\" \"Cantidad\"                       
                        FROM {$schema}.\"OIGE\" T00
                        LEFT JOIN {$schema}.\"IGE1\" T111 ON T00.\"DocEntry\" = T111.\"DocEntry\"
                        LEFT JOIN {$schema}.\"OWOR\" T222 ON T111.\"BaseEntry\" = T222.\"DocEntry\" AND T111.\"BaseType\" = T222.\"ObjType\"
                        LEFT JOIN {$schema}.\"WOR1\" T333 ON T222.\"DocEntry\" = T333.\"DocEntry\" AND T111.\"BaseLine\" = T333.\"LineNum\"
                        WHERE T222.\"DocNum\" = ".$OrdenFabricacion."
                        AND T00.\"DocNum\" = ".$NumeroEmision."
                        ORDER BY 1";
        return$emisiones=$this->ejecutarConsulta($query_emisiones);
    }
    public function OrdenFabricacion($ordenventa){
        $schema = 'HN_OPTRONICS';
        //Este Where Es para traer solo lo de Optronics
        
        //End
        $sql = "SELECT DISTINCT T1.\"ItemCode\" AS \"Articulo\", 
                    T1.\"Dscription\" AS \"Descripcion\",
                    ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", 
                    T2.\"DueDate\" AS \"Fecha entrega OF\", 
                    --T1.\"LineNum\" AS \"LineNum\",
                    T2.\"DocNum\" AS \"Orden de F.\",
                    T2.\"CardCode\" AS \"Cliente\",
                    CASE T2.\"Status\"
                    	WHEN 'P' THEN 'Planeado'
                    	WHEN 'R' THEN 'Liberado'
                    	WHEN 'L' THEN 'Cerrado'
                    	WHEN 'C' THEN 'Cancelado'
                    END \"Estatus\"
                FROM {$schema}.\"ORDR\" T0
                INNER JOIN {$schema}.\"RDR1\" T1 ON T0.\"DocEntry\" = T1.\"DocEntry\"
                LEFT JOIN {$schema}.\"OWOR\" T2 ON T1.\"DocEntry\" = T2.\"OriginAbs\" AND T2.\"Status\" NOT IN ('C') AND T2.\"ItemCode\" = T1.\"ItemCode\"
                WHERE T0.\"DocNum\" = '{$ordenventa}'
                AND  (T2.\"Status\" = 'R' OR T2.\"Status\" = 'P')
                ORDER BY T2.\"DocNum\"";
        /*$sql = "SELECT DISTINCT T1.\"ItemCode\" AS \"Articulo\", 
                    T1.\"Dscription\" AS \"Descripcion\", 
                    ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", 
                    T2.\"DueDate\" AS \"Fecha entrega OF\", 
                    T1.\"PoTrgNum\",
                    --T1.\"LineNum\" AS \"LineNum\",
                    T2.\"DocNum\"AS \"Orden de F.\",
                    CASE T2.\"Status\"
                    	WHEN 'P' THEN 'Planeado'
                    	WHEN 'R' THEN 'Liberado'
                    	WHEN 'L' THEN 'Cerrado'
                    	WHEN 'C' THEN 'Cancelado'
                    END \"Estatus\"
                FROM {$schema}.\"ORDR\" T0
                INNER JOIN {$schema}.\"RDR1\" T1 ON T0.\"DocEntry\" = T1.\"DocEntry\"
                LEFT JOIN {$schema}.\"OWOR\" T2 ON T1.\"DocEntry\" = T2.\"OriginAbs\" AND T2.\"Status\" NOT IN ('C') AND T2.\"ItemCode\" = T1.\"ItemCode\"
                WHERE T0.\"DocNum\" = '{$ordenventa}'
                AND  T2.\"Status\" = 'R'
                ORDER BY T1.\"PoTrgNum\",T2.\"DocNum\"
                ORDER BY T1.\"VisOrder\"";*/
        //Consulta a SAP para traer las partidas de una OV
        /*$sql = "SELECT T1.\"ItemCode\" AS \"Articulo\", 
                    T1.\"Dscription\" AS \"Descripcion\", 
                    ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", 
                    T2.\"DueDate\" AS \"Fecha entrega OF\", 
                    T1.\"PoTrgNum\" AS \"Orden de F.\" ,
                    T1.\"LineNum\" AS \"LineNum\"
                FROM {$schema}.\"ORDR\" T0
                INNER JOIN {$schema}.\"RDR1\" T1 ON T0.\"DocEntry\" = T1.\"DocEntry\"
                LEFT JOIN {$schema}.\"OWOR\" T2 ON T1.\"PoTrgNum\" = T2.\"DocNum\"
                WHERE T0.\"DocNum\" = '{$ordenventa}'
                ORDER BY T1.\"PoTrgNum\"";  
                //ORDER BY T1.\"VisOrder\"";*/
        //Ejecucion de la consulta
        $partidas = $this->ejecutarConsulta($sql);
        $partidasOF=OrdenVenta::where('OrdenVenta','=',$ordenventa)->first();
        if($partidasOF==null || $partidasOF==""){
            $countpartidas=0;
        }else{
            $countpartidas=$partidasOF->ordenesFabricacions()->get()->count();
        }
        return count($partidas)-$countpartidas;
    }
    public function OrdeneVenta($NumOV){
        $schema = 'HN_OPTRONICS';
        $where="";
        $datos="";
        $where = 'T0."DocNum" LIKE \'%' . $NumOV . '%\'';
        $sql = 'SELECT T0."DocNum" AS "OV", T0."CardName" AS "Cliente",T0."CardCode" AS "CardCode", T0."DocDate" AS "Fecha", 
                T0."DocStatus" AS "Estado", T0."DocTotal" AS "Total" 
                FROM ' . $schema . '.ORDR T0
                WHERE '.$where.'ORDER BY T0."DocNum"';
        //Queretaro
        /*$sql = 'SELECT T0."DocNum" AS "OV", T0."CardName" AS "Cliente",T0."CardCode" AS "CardCode", T0."DocDate" AS "Fecha", 
                T0."DocStatus" AS "Estado", T0."DocTotal" AS "Total" 
                FROM ' . $schema . '.ORDR T0
                INNER JOIN '.$schema.'.RDR1 T1 ON T0."DocEntry" = T1."DocEntry" 
                WHERE '.$where.'ORDER BY T0."DocNum"
                AND T1.WhsCode NOT IN (\'BOE-MP\',\'BOE-PT\')';
         //Tijuana
        $sql = 'SELECT T0."DocNum" AS "OV", T0."CardName" AS "Cliente",T0."CardCode" AS "CardCode", T0."DocDate" AS "Fecha", 
                T0."DocStatus" AS "Estado", T0."DocTotal" AS "Total" 
                FROM ' . $schema . '.ORDR T0
                INNER JOIN '.$schema.'.RDR1 T1 ON T0."DocEntry" = T1."DocEntry" 
                WHERE '.$where.'ORDER BY T0."DocNum"
                AND T1.WhsCode IN (\'BOE-MP\',\'BOE-PT\')';*/

        /**AND T1.WhsCode NOT IN ('BOE-MP','BOE-PT') */
        try {
            $datos = $this->ejecutarConsulta($sql);
        } catch (\Exception $e) {
            return $datos=0;
        }
        return $datos;
    }
    //Retorna detalles del cable para validarlos 
    public function DetallesCable($OrdenFabricacion){
        $schema = 'HN_OPTRONICS';
        $sql = 'SELECT DISTINCT T3."ItemCode" AS "Articulo", T4."ItemName" AS"Descripcion", ROUND(T2."PlannedQty", 0) AS "Cantidad OF", T2."DueDate" AS "Fecha entrega OF",
                    T2."DocNum" AS "Orden de F." , T3."ItemCode" "Hijo", T3."ItemName" "Nombre Hijo", T3."BaseQty" "Cantidad Base",T3."IssuedQty" "Ctd. requerida" , T4."InvntryUom" "Medida"
                    FROM  HN_OPTRONICS."OWOR" T2 
                    INNER JOIN HN_OPTRONICS."WOR1" T3 ON T3."DocEntry" = T2."DocEntry" 
                    INNER JOIN HN_OPTRONICS."OITM" T4 ON T4."ItemCode" = T3."ItemCode"
                    WHERE T2."DocNum" = '.$OrdenFabricacion.' AND T4."InvntryUom" = \'MTR\' AND  (T3."ItemName" LIKE \'%Cable%\' OR T3."ItemCode" = \'SP012930190B\' OR T3."ItemName" LIKE \'%fiber/print%\')';
        return $Detalles = $this->ejecutarConsulta($sql);
    }
    public function EtiquetasDatosSAP($NumOV,$NumOF){
        $schema = 'HN_OPTRONICS';
        $where="";
        $datos="";
        try {
            $sql = ' SELECT  T0."DocNum" AS "OV",T2."SubCatNum"  AS "SubCatNum", T2."ItemCode" as "ItemCode", 
                            T1."U_BPItmDsc" as "U_BPItmDsc", T0."DocDate", T0."NumAtCard"
                FROM  ' . $schema . '.ORDR T0
				INNER JOIN  ' . $schema . '.RDR1 T2 ON T2."DocEntry" = T0."DocEntry" 
                LEFT JOIN  ' . $schema . '.OSCN T1 ON T0."CardCode" = T1."CardCode" AND T1."Substitute" = T2."SubCatNum" AND T2."ItemCode" = T1."ItemCode"
                WHERE T0."DocNum" = '. $NumOV.'
                AND T2."PoTrgNum" = '.$NumOF.';';
            $datos = $this->ejecutarConsulta($sql);
        } catch (\Exception $e) {
            return $datos=[];
        }
        return $datos;
    }
    public function EtiquetasDatosSAPFMX($NumOV){
        $schema = 'HN_OPTRONICS';
        $where="";
        $datos="";
        try {
            $sql = ' SELECT T0."NumAtCard",T0."DocNum" AS "OV"
                FROM  ' . $schema . '.ORDR T0
                WHERE T0."DocNum" = '. $NumOV.' AND T0."CardCode" = \'C0004\';';
            $datos = $this->ejecutarConsulta($sql);
        } catch (\Exception $e) {
            return $datos=[];
        }
        return $datos;
    }
    public function Logs($Vista,$Accion,$log){
        $Log = new Logs();
        $Usuario = $this->InfoUsuario();
        $Log->id_user = $Usuario;
        $Log->log = "Usuario: ".$Usuario."  Vista: ".$Vista."  Accion: ".$Accion."[Log]:".$log;
        $Log->save();
    }
}
