<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
class PerfilController extends Controller
{
    public function index(){
        $user = Auth::user();
        $roles = $user->roles;  
        return view('Usuarios.UsuariosIndex', compact('user', 'roles'));
    }
    public function update(Request $request){
        if ($request->has('Password')) {
            $request->validate([
                'Password' => 'required|min:8|same:ConfirmarPassword',
                'ConfirmarPassword' => 'required|min:8',
            ], [
                'Password.required' => 'La contraseña es obligatoria.',
                'Password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'Password.same' => 'Las contraseñas no coinciden.',
                'ConfirmarPassword.required' => 'Debes confirmar la contraseña.',
            ]);
            try {
                $user = Auth::user();
                $updateData['password'] = bcrypt($request->Password);
                $user->update($updateData);
                return response()->json(['message' => 'Contraseña actualizado correctamente.']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error al actualizar los datos.'], 500);
            }
        }
        Log::info($request->all());
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.max' => 'El apellido no puede tener más de 255 caracteres.',
        ]);
        try {
            $user = Auth::user();
            $user->update($request->only(['name', 'apellido']));

            return response()->json(['message' => 'Perfil actualizado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar los datos.'], 500);
        }
    }

}    
