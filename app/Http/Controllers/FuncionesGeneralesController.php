<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenVenta;
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
        $password   = "SPL.Lectura202xx5.";

        
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
        $sql = "SELECT DISTINCT T1.\"ItemCode\" AS \"Articulo\", 
                    T1.\"Dscription\" AS \"Descripcion\", 
                    ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", 
                    T2.\"DueDate\" AS \"Fecha entrega OF\", 
                    T1.\"PoTrgNum\" AS \"Orden de F.\" ,
                    --T1.\"LineNum\" AS \"LineNum\",
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
                ORDER BY T1.\"PoTrgNum\"
                ORDER BY T1.\"VisOrder\"";
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
    public function DetallesCable($OrdenFabricacion){
        $schema = 'HN_OPTRONICS';
        $sql="SELECT DISTINCT T1.\"ItemCode\" AS \"Articulo\", T1.\"Dscription\" AS\"Descripcion\", ROUND(T2.\"PlannedQty\", 0) AS \"Cantidad OF\", T2.\"DueDate\" AS \"Fecha entrega OF\", 
	            T1.\"PoTrgNum\" AS \"Orden de F.\" , T1.\"LineNum\" AS \"LineNum\", T3.\"ItemCode\" \"Hijo\", T3.\"ItemName\" \"Nombre Hijo\", T3.\"BaseQty\" \"Cantidad Base\",T3.\"IssuedQty\" \"Ctd. requerida\" 
                FROM {$schema}.\"ORDR\" T0
                INNER JOIN {$schema}.\"RDR1\" T1 ON T0.\"DocEntry\" = T1.\"DocEntry\"
                LEFT JOIN {$schema}.\"OWOR\" T2 ON T1.\"PoTrgNum\" = T2.\"DocNum\"
                INNER JOIN {$schema}.\"WOR1\" T3 ON T3.\"DocEntry\" = T2.\"DocEntry\"
                WHERE T1.\"PoTrgNum\" = {$OrdenFabricacion} --AND T3.\"ItemName\" LIKE '%Cable%' 
                LIMIT 1";
                //WHERE T0.\"DocNum\" = 66483 AND T3.\"ItemName\" LIKE '%Cable%'";
        return $Detalles = $this->ejecutarConsulta($sql);
    }
}
