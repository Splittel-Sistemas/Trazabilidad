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
    //tabla principal
    public function tablalinea()
    {
        $items  = DB::table('linea')
            ->select('linea.Nombre', 'linea.NumeroLinea', 'linea.Descripcion', 'linea.active')
            ->get();
    
        return response()->json($items );
    }
    // direccionar a la ruta crear
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
    public function update(Request $request, $numero)
    {
        try {
            $linea = Linea::where('NumeroLinea', $numero)->firstOrFail();
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
    // Eliminar una línea
    public function destroy(linea $linea)
    {
        $linea->delete();

        return response()->json(['message' => 'Línea eliminada con éxito']);
    }
    public function activar(Request $request)
    {
        $linea = linea::where('NumeroLinea', $request->NumeroLinea)->first(); 
        if ($linea) {
            $linea->active = true;
            $linea->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Linea no encontrada.'], 404);
    }
    // Método para desactivar un usuario
    public function desactivar(Request $request)
    {
        $linea = linea::where('NumeroLinea', $request->NumeroLinea)->first(); 
        if ($linea) {
            $linea->active = false;
            $linea->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Linea no encontrada.'], 404);
    } 
}