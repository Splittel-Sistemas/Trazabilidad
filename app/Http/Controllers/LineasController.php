<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Linea;


class LineasController extends Controller
{
    //

    public function index()
    {
        return view('Lineas.Lineaindex');
    }

    public function tablalinea()
    {
        $tabla = DB::table('linea')
            ->select('linea.Nombre', 'linea.NumeroLinea', 'linea.Descripcion')
            ->get();
    
        return response()->json($tabla);
    }
    public function create()
    {
        return view('lineas.create');
    }

    // Guardar una nueva línea en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required|string|max:255',
            'NumeroLinea' => 'required|integer|unique:linea',
            'Descripcion' => 'nullable|string',
        ]);

        $linea = linea::create($request->all());

        return response()->json([
            'message' => 'Línea creada con éxito',
            'linea' => $linea
        ]);
    }

    // Mostrar una línea específica
    public function show(linea $linea)
    {
        return response()->json($linea);
    }

    // Mostrar el formulario de edición (si usas vistas Blade)
    public function edit(linea $linea)
    {
        return view('linea.edit', compact('linea'));
    }

    // Actualizar una línea existente
    public function update(Request $request, linea $linea)
    {
        $request->validate([
            'Nombre' => 'required|string|max:255',
            'NumeroLinea' => 'required|integer|unique:linea,NumeroLinea,' . $linea->id,
            'Descripcion' => 'nullable|string',
        ]);

        $linea->update($request->all());

        return response()->json([
            'message' => 'Línea actualizada con éxito',
            'linea' => $linea
        ]);
    }

    // Eliminar una línea
    public function destroy(linea $linea)
    {
        $linea->delete();

        return response()->json(['message' => 'Línea eliminada con éxito']);
    }
}
