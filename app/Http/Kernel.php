<?php
// app/Http/Kernel.php (Add to the existing file)
namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        'same-company' => \App\Http\Middleware\EnsureSameCompany::class,
    ];

    protected $middlewareGroups = [
        'api' => [
            \App\Http\Middleware\ForceJsonResponse::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
}