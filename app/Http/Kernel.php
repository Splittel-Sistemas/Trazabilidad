<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // Los middlewares globales aquí, como el middleware de sesión, cookies, etc.
    ];

    protected $middlewareGroups = [
        'web' => [
        
        ],
        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\FuncionesSapMiddleware::class, 
        ],
    ];
    protected $routeMiddleware = [
        'sap.connection' => \App\Http\Middleware\FuncionesSapMiddleware::class, 
    ];
}