<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FuncionesGeneralesController extends Controller
{
    private $connection;

    // Método para verificar la conexión a SAP
    public function checkSapConnection()
    {
        try {
            // Intentar conectar a SAP
            $this->connectToSap();
            return true;  // Si la conexión es exitosa, retornamos true
        } catch (\Exception $e) {
            // En caso de error, se captura la excepción y retorna false
            return false;
        }
    }

    // Método privado para realizar la conexión a SAP
    private function connectToSap()
    {
        $datasource = 'DRIVER=HDBODBC;SERVERNODE=192.168.2.19:30015;CHAR_AS_UTF8=1;';
        $username   = "USR_LECTURA";
        $password   = "SPL.Lectura202xx4.";

        // Establecer la conexión ODBC
        $conn = odbc_connect($datasource, $username, $password);

        if (!$conn) {
            // Si la conexión falla, lanzamos una excepción con el mensaje de error
            throw new \Exception("Error de conexión a SAP: " . odbc_errormsg());
        }

        // Guardamos la conexión para usarla en otros métodos
        $this->connection = $conn;
    }

    // Método para ejecutar una consulta SQL en SAP
    public function ejecutarConsulta($sql)
    {
        // Asegurarnos de que hay una conexión establecida
        $this->connectToSap();

        if (!$this->connection) {
            throw new \Exception("No se ha establecido conexión a SAP.");
        }

        // Ejecutamos la consulta
        $result = odbc_exec($this->connection, $sql);

        if (!$result) {
            // Si hay un error en la ejecución, lanzamos una excepción con el mensaje de error
            throw new \Exception("Error al ejecutar la consulta: " . odbc_errormsg());
        }

        // Recolectamos los datos obtenidos de la consulta
        $datos = [];
        while ($row = odbc_fetch_array($result)) {
            $datos[] = $row;
        }

        // Retornamos los datos obtenidos
        return $datos;
    }

    // Método público para obtener datos de SAP
    public function obtenerDatosDeSap()
    {
        // Definimos la consulta SQL
        $sql = 'SELECT * FROM HN_OPTRONICS.OCRD';

        // Ejecutamos la consulta y obtenemos los resultados
        return $this->ejecutarConsulta($sql);
    }

    // Método para cerrar la conexión con SAP
    public function cerrarConexion()
    {
        if ($this->connection) {
            odbc_close($this->connection);  
        }
    }
}
