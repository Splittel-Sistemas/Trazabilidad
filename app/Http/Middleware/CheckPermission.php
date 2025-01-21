<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
{
    Log::debug("Verificando permiso: " . $permission);
    $user = Auth::user();

    $hasPermission = $user && $user->roles->flatMap(function ($role) {
        return $role->permissions->pluck('name');
    })->contains($permission);

    if ($hasPermission) {
        return $next($request);
    }

    abort(403, 'No tienes permiso para acceder a esta sección. Permiso requerido: ' . $permission);
}
}