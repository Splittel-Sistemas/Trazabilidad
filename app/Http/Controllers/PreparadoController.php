<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;

class PreparadoController extends Controller
{
    // Método para mostrar la vista con los materiales registrados
    public function index()
    {
        //$materiales = Material::all();  // Obtiene todos los materiales registrados
        return view('Areas.Preparado'); //compact('materiales'));
    }

    // Método para almacenar un nuevo material
   /* public function store(Request $request)
    {
        // Validar los datos
        $validated = $request->validate([
            'material' => 'required|string',
            'cantidad' => 'required|integer|min:1',
            'proveedor' => 'required|string',
            'fecha' => 'required|date',
        ]);

        // Crear un nuevo material
        Material::create([
            'tipo' => $validated['material'],
            'cantidad' => $validated['cantidad'],
            'proveedor' => $validated['proveedor'],
            'fecha' => $validated['fecha'],
        ]);

        // Redirigir al listado de materiales con un mensaje de éxito
        return redirect()->route('preparado.index')->with('success', 'Material registrado exitosamente');
    }*/
}
