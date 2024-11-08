<?php

namespace App\Http\Controllers;

use App\Http\Controllers\FuncionesGeneralesController; // Importar el controlador base

class SapController extends FuncionesGeneralesController
{
    // El controlador `SapController` hereda ahora las funciones de `FuncionesGeneralesController`

    public function conexionSap()
    {
        try {
            $conexionExitosa = $this->checkSapConnection(); // Usamos el método heredado
            return response()->json(['message' => 'Conexión SAP exitosa!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'No se pudo establecer conexión con SAP: ' . $e->getMessage()], 500);
        }
    }

    public function obtenerDatosSap()
    {
        try {
            $datos = $this->obtenerDatosDeSap(); // Usamos el método heredado
            return response()->json($datos);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }
}
