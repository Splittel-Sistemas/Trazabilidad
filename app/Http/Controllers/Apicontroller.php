<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ApiController extends Controller
{
    public function users(Request $request)
    {
        if ($request->has('active')) {
            $users = User::where('active', true)->get();
        } else {
            $users = User::all();
        }

        return response()->json($users);
    }

    public function login(Request $request)
    {
        $response = ["status" => 0, "msg" => ""];

        // Asegúrate de validar la solicitud antes de procesarla
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $data = $request->only('email', 'password');
        $user = User::where('email', $data['email'])->first();

        if ($user) {
            if (Hash::check($data['password'], $user->password)) {
                // Aquí puedes generar un token o manejar la sesión
                $response["status"] = 1; // Login exitoso
                $response["msg"] = "Login exitoso";
            } else {
                $response["msg"] = "Credenciales incorrectas";
            }
        } else {
            $response["msg"] = "Usuario no encontrado";
        }

        return response()->json($response);
    }
}
