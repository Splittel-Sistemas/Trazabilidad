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
        $linea = Linea::orderBy('Nombre', 'asc')->get();
        return view('Lineas.Lineaindex', compact('linea'));
    }
    // Mostrar el formulario para crear una nueva línea
    public function create()
    {
        return view('lineas.create');
    }
    // Guardar una nueva línea en la base de datos
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'Nombre' => 'required|string|max:255',
                'NumeroLinea' => 'required|integer|unique:linea,NumeroLinea',
                'Descripcion' => 'nullable|string',
            ]);
            $linea = Linea::create($validatedData);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Línea creada correctamente'
                ]);
            }
            return redirect()->route('index.linea')->with('message', 'Línea creada con éxito');
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }
    // Mostrar los detalles de una línea específica
    public function show($id)
    {
        try {
            $linea = Linea::findOrFail($id);
            return response()->json([
                'Nombre' => $linea->Nombre,
                'Descripcion' => $linea->Descripcion,
                'NumeroLinea' => $linea->NumeroLinea,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Línea no encontrada'], 404);
        }
    }
    // Actualizar los detalles de una línea
    public function update(Request $request, $id)
    {
        try {
            $linea = Linea::findOrFail($id);
            $validatedData = $request->validate([
                'Nombre' => 'required|string|max:255',
                'NumeroLinea' => 'required|integer|unique:linea,NumeroLinea,' . $linea->id, 
                'Descripcion' => 'nullable|string',
            ]);
            $linea->update($validatedData);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Línea actualizada correctamente'
                ]);
            }
            return redirect()->route('linea.index')->with('message', 'Línea actualizada con éxito');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->getMessage()
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
    // Eliminar una línea
    public function destroy(linea $linea)
    {
        $linea->delete();

        return response()->json(['message' => 'Línea eliminada con éxito']);
    }
    // Método para activar una línea
public function activar(Request $request)
{
    $linea = Linea::find($request->id); // Buscar por id en lugar de NumeroLinea
    if ($linea) {
        $linea->active = true;
        $linea->save();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false, 'message' => 'Línea no encontrada.'], 404);
}

// Método para desactivar una línea
public function desactivar(Request $request)
{
    $linea = Linea::find($request->id); // Buscar por id en lugar de NumeroLinea
    if ($linea) {
        $linea->active = false;
        $linea->save();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false, 'message' => 'Línea no encontrada.'], 404);
}

}