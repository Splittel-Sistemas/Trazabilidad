<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\models\OrdenVenta;
use Illuminate\Support\Facades\Crypt;

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

       
        return $datos;
    }
    public function obtenerDatosDeSap()
    {
       
        $sql = 'SELECT * FROM HN_OPTRONICS.OCRD';
        return $this->ejecutarConsulta($sql);
    }
    public function cerrarConexion()
    {
        if ($this->connection) {
            odbc_close($this->connection);  
        }
    }
    public function encrypt($dato){
        $encrypted = Crypt::encrypt($dato);
        return $encrypted;
    }
    public function decrypt($dato){
        $encrypted = Crypt::decrypt($dato);
        return $encrypted;
    }
    public function InfoUsuario(){
        return 1;
    }
    public function Linea(){
        return 1;
    }
    
}
