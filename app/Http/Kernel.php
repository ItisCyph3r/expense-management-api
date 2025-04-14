<?php
// app/Http/Kernel.php (Add to the existing file)
namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... existing code ...

    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // ... existing middleware ...
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        'same-company' => \App\Http\Middleware\EnsureSameCompany::class,
    ];
}