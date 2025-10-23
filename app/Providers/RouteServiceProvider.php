<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
         $this->routes(function () {
        // ✅ Health check route
        Route::get('/health', function (Request $request) {
            return response()->json([
                'status' => 'ok',
                'time' => microtime(true) - $request->attributes->get('request_start_time')
            ]);
        });

        // Include routes/api.php
        Route::middleware("api")
            ->prefix("api/v1/")
            ->middleware(["auth:sanctum"])
            ->group(base_path("routes/api.php"));
        });
    }
}
