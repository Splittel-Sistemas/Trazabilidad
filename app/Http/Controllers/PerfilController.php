<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
class PerfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roles = $user->roles;  
        return view('Usuarios.UsuariosIndex', compact('user', 'roles'));
    }
    public function update(Request $request)
{
    Log::info($request->all());

    $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'apellido' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|nullable|string|email|max:255|unique:users,email,' . Auth::id(),
    ]);

    try {
        $user = Auth::user();
        $user->update($request->only(['name', 'apellido', 'email']));

        return response()->json(['message' => 'Perfil actualizado correctamente.']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al actualizar los datos.'], 500);
    }
}

}    
