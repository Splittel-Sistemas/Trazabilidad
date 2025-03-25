<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Linea;
use Illuminate\Support\Facades\Auth;
class LineasController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        if ($user->hasPermission('Vista Lineas')) {
            $linea = Linea::orderBy('NumeroLinea', 'asc')->get();
            return view('Lineas.Lineaindex', compact('linea'));
        } else {
            return redirect()->route('error.');
        }
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
                'ColorLinea' => 'required',
            ]);
    
            // Verificar si el NumeroLinea ya existe
            if (Linea::where('NumeroLinea', $request->NumeroLinea)->exists()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Número de línea ya existe'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Número de línea ya existe');
            }
    
            $linea = Linea::create($validatedData);
    
            // Si es una solicitud AJAX, retornar una respuesta JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Línea creada con éxito',
                    'data' => $linea // Devuelve los datos de la nueva línea
                ]);
            }
    
            // Si no es una solicitud AJAX, redirige con un mensaje de éxito
            return redirect()->route('linea.index')->with('message', 'Línea creada con éxito');
        } catch (ValidationException $e) {
            // Si ocurre un error de validación
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $e->errors() // Devuelve los errores de validación
                ], 422); // 422 es el código para "Unprocessable Entity"
            }
    
            // Si no es AJAX, se redirige con los errores
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
                'ColorLinea' => $linea->ColorLinea,
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
                'ColorLinea' => 'required|string',
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
        $linea = Linea::find($request->id); 
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
        $linea = Linea::find($request->id); 
        if ($linea) {
            $linea->active = false;
            $linea->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Línea no encontrada.'], 404);
    }
}